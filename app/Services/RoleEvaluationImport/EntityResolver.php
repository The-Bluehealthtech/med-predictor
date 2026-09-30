<?php

namespace App\Services\RoleEvaluationImport;

use Illuminate\Support\Facades\DB;

/**
 * Résout joueur / club / équipe / match à partir des colonnes source
 * déclarées dans le mapping (section "resolve"), pour satisfaire l'exigence
 * du mandat "joueur et match connus" avant tout import.
 *
 * APPROCHE RECOMMANDÉE (décision du 30/09) : le FIFA Connect ID est imposé
 * aux fournisseurs comme identifiant standard, "en théorie tout vrai club en
 * a un". Concrètement :
 *  - joueur   : players.fifa_connect_id (mode "fifa_connect_id" de resolvePlayer())
 *  - match    : clubs.fifa_connect_id du domicile + de l'extérieur + date
 *               (mode "clubs_and_date" de resolveMatch()) — PAS le nom de
 *               l'équipe : constat fait en base, "teams.name" vaut
 *               "First Team"/"Reserve Team"/"Youth Academy" pour TOUS les
 *               clubs, ce qui rend un nom d'équipe seul ambigu.
 *  - équipe   : jamais résolue directement. Une fois le match connu, l'équipe
 *               (teams.id) d'un joueur/d'une ligne est déduite via
 *               resolveTeamForMatch() : le club (par FIFA Connect ID) est
 *               comparé à matches.home_club_id/away_club_id (remplis à 100%
 *               en base, vérifié), qui donne directement home_team_id ou
 *               away_team_id — et le camp (domicile/extérieur) au passage.
 *
 * Modes hérités, conservés pour compatibilité si un fournisseur ne peut
 * vraiment pas donner de FIFA Connect ID (voir docs/role-evaluation/
 * 03-implementation-livrable-2.md) :
 *  - "id"    : la colonne source contient directement l'identifiant interne (players.id / teams.id / matches.id)
 *  - "name"  : (équipe uniquement) résolution via teams.name — DÉCONSEILLÉ, ambigu (voir ci-dessus)
 *  - "teams_and_date" : (match uniquement) résolution par nom d'équipe + date — DÉCONSEILLÉ, même raison
 *
 * Non vérifié : cette classe n'a pas pu être exécutée contre une vraie base
 * (pas de PHP disponible dans cet environnement) — voir le rapport
 * d'implémentation du Livrable 2 pour le détail.
 */
class EntityResolver
{
    /** @var array<string,int|null> cache clé -> id résolu (ou null si introuvable) */
    private array $playerCache = [];
    private array $teamCache = [];
    private array $matchCache = [];
    private array $clubCache = [];

    public function resolvePlayer(array $resolveConfig, array $row): array
    {
        $mode = $resolveConfig['player']['mode'] ?? null;

        if ($mode === 'id') {
            $column = $resolveConfig['player']['column'];
            $id = $row[$column] ?? null;
            if ($id === null || $id === '') {
                return [null, 'colonne id joueur absente ou vide'];
            }
            $id = (int) $id;
            $key = "id:{$id}";
            if (! array_key_exists($key, $this->playerCache)) {
                $this->playerCache[$key] = DB::table('players')->where('id', $id)->exists() ? $id : null;
            }

            return $this->playerCache[$key] === null
                ? [null, "joueur introuvable (players.id={$id})"]
                : [$this->playerCache[$key], null];
        }

        if ($mode === 'fifa_connect_id') {
            $column = $resolveConfig['player']['column'];
            $fifaId = trim((string) ($row[$column] ?? ''));
            if ($fifaId === '') {
                return [null, 'colonne fifa_connect_id joueur absente ou vide'];
            }
            $key = "fifa:{$fifaId}";
            if (! array_key_exists($key, $this->playerCache)) {
                $found = DB::table('players')->where('fifa_connect_id', $fifaId)->value('id');
                $this->playerCache[$key] = $found;
            }

            return $this->playerCache[$key] === null
                ? [null, "joueur introuvable (fifa_connect_id={$fifaId})"]
                : [$this->playerCache[$key], null];
        }

        return [null, "mode de résolution joueur non configuré ou inconnu : '{$mode}'"];
    }

    public function resolveTeam(array $resolveConfig, array $row, string $configKey = 'team'): array
    {
        $mode = $resolveConfig[$configKey]['mode'] ?? null;

        if ($mode === 'id') {
            $column = $resolveConfig[$configKey]['column'];
            $id = $row[$column] ?? null;
            if ($id === null || $id === '') {
                return [null, "colonne id équipe ({$configKey}) absente ou vide"];
            }
            $id = (int) $id;
            $key = "id:{$id}";
            if (! array_key_exists($key, $this->teamCache)) {
                $this->teamCache[$key] = DB::table('teams')->where('id', $id)->exists() ? $id : null;
            }

            return $this->teamCache[$key] === null
                ? [null, "équipe introuvable (teams.id={$id})"]
                : [$this->teamCache[$key], null];
        }

        if ($mode === 'name') {
            $column = $resolveConfig[$configKey]['column'];
            $name = trim((string) ($row[$column] ?? ''));
            if ($name === '') {
                return [null, "colonne nom équipe ({$configKey}) absente ou vide"];
            }
            $key = 'name:' . mb_strtolower($name);
            if (! array_key_exists($key, $this->teamCache)) {
                $found = DB::table('teams')->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->value('id');
                $this->teamCache[$key] = $found;
            }

            return $this->teamCache[$key] === null
                ? [null, "équipe introuvable (name='{$name}')"]
                : [$this->teamCache[$key], null];
        }

        return [null, "mode de résolution équipe non configuré ou inconnu : '{$mode}'"];
    }

    public function resolveMatch(array $resolveConfig, array $row): array
    {
        $mode = $resolveConfig['match']['mode'] ?? null;

        if ($mode === 'id') {
            $column = $resolveConfig['match']['column'];
            $id = $row[$column] ?? null;
            if ($id === null || $id === '') {
                return [null, 'colonne id match absente ou vide'];
            }
            $id = (int) $id;
            $key = "id:{$id}";
            if (! array_key_exists($key, $this->matchCache)) {
                $this->matchCache[$key] = DB::table('matches')->where('id', $id)->exists() ? $id : null;
            }

            return $this->matchCache[$key] === null
                ? [null, "match introuvable (matches.id={$id})"]
                : [$this->matchCache[$key], null];
        }

        if ($mode === 'teams_and_date') {
            $homeCol = $resolveConfig['match']['home_team_column'] ?? null;
            $awayCol = $resolveConfig['match']['away_team_column'] ?? null;
            $dateCol = $resolveConfig['match']['date_column'] ?? null;
            if (! $homeCol || ! $awayCol || ! $dateCol) {
                return [null, "configuration 'teams_and_date' incomplète dans le mapping"];
            }

            [$homeTeamId, $homeErr] = $this->resolveTeamByName($row[$homeCol] ?? null);
            if ($homeErr) {
                return [null, "équipe domicile introuvable : {$homeErr}"];
            }
            [$awayTeamId, $awayErr] = $this->resolveTeamByName($row[$awayCol] ?? null);
            if ($awayErr) {
                return [null, "équipe extérieure introuvable : {$awayErr}"];
            }

            $rawDate = trim((string) ($row[$dateCol] ?? ''));
            if ($rawDate === '') {
                return [null, 'colonne date de match absente ou vide'];
            }
            $day = substr($rawDate, 0, 10); // "YYYY-MM-DD" attendu en tête de chaîne

            $key = "teams_date:{$homeTeamId}:{$awayTeamId}:{$day}";
            if (! array_key_exists($key, $this->matchCache)) {
                $found = DB::table('matches')
                    ->where('home_team_id', $homeTeamId)
                    ->where('away_team_id', $awayTeamId)
                    ->whereDate('match_date', $day)
                    ->value('id');
                $this->matchCache[$key] = $found;
            }

            return $this->matchCache[$key] === null
                ? [null, "match introuvable (equipes id={$homeTeamId}/{$awayTeamId}, date={$day})"]
                : [$this->matchCache[$key], null];
        }

        if ($mode === 'clubs_and_date') {
            $homeCol = $resolveConfig['match']['home_club_column'] ?? null;
            $awayCol = $resolveConfig['match']['away_club_column'] ?? null;
            $dateCol = $resolveConfig['match']['date_column'] ?? null;
            if (! $homeCol || ! $awayCol || ! $dateCol) {
                return [null, "configuration 'clubs_and_date' incomplète dans le mapping"];
            }

            [$homeClubId, $homeErr] = $this->resolveClub($homeCol, $row);
            if ($homeErr) {
                return [null, "club domicile introuvable : {$homeErr}"];
            }
            [$awayClubId, $awayErr] = $this->resolveClub($awayCol, $row);
            if ($awayErr) {
                return [null, "club extérieur introuvable : {$awayErr}"];
            }

            $rawDate = trim((string) ($row[$dateCol] ?? ''));
            if ($rawDate === '') {
                return [null, 'colonne date de match absente ou vide'];
            }
            $day = substr($rawDate, 0, 10);

            $key = "clubs_date:{$homeClubId}:{$awayClubId}:{$day}";
            if (! array_key_exists($key, $this->matchCache)) {
                $found = DB::table('matches')
                    ->where('home_club_id', $homeClubId)
                    ->where('away_club_id', $awayClubId)
                    ->whereDate('match_date', $day)
                    ->value('id');
                $this->matchCache[$key] = $found;
            }

            return $this->matchCache[$key] === null
                ? [null, "match introuvable (clubs fifa_connect_id={$homeClubId}/{$awayClubId} en id interne, date={$day})"]
                : [$this->matchCache[$key], null];
        }

        return [null, "mode de résolution match non configuré ou inconnu : '{$mode}'"];
    }

    /**
     * Résout un club par son FIFA Connect ID (colonne déclarée dans le mapping).
     */
    public function resolveClub(string $column, array $row): array
    {
        $fifaId = trim((string) ($row[$column] ?? ''));
        if ($fifaId === '') {
            return [null, "colonne fifa_connect_id club ('{$column}') absente ou vide"];
        }

        $key = "club_fifa:{$fifaId}";
        if (! array_key_exists($key, $this->clubCache)) {
            $found = DB::table('clubs')->where('fifa_connect_id', $fifaId)->value('id');
            $this->clubCache[$key] = $found;
        }

        return $this->clubCache[$key] === null
            ? [null, "club introuvable (fifa_connect_id={$fifaId})"]
            : [$this->clubCache[$key], null];
    }

    /**
     * Déduit l'équipe (teams.id) ET le camp (domicile/extérieur) d'une ligne
     * à partir d'un match déjà résolu et du FIFA Connect ID du club de cette
     * ligne, en comparant à matches.home_club_id / away_club_id (remplis à
     * 100% en base, vérifié sur l'inventaire du 30/09 — 34/34 matchs).
     * C'est la méthode recommandée : elle évite complètement le nom
     * d'équipe, ambigu ("First Team" existe pour tous les clubs).
     *
     * @return array{0:?int,1:?bool,2:?string} [team_id, is_home, raison_erreur]
     */
    public function resolveTeamForMatch(int $matchId, string $clubColumn, array $row): array
    {
        [$clubId, $err] = $this->resolveClub($clubColumn, $row);
        if ($err) {
            return [null, null, $err];
        }

        $match = DB::table('matches')->where('id', $matchId)
            ->first(['home_team_id', 'away_team_id', 'home_club_id', 'away_club_id']);

        if (! $match) {
            return [null, null, "match #{$matchId} introuvable (résolution club -> équipe)"];
        }

        if ((int) $match->home_club_id === (int) $clubId) {
            return [$match->home_team_id, true, null];
        }
        if ((int) $match->away_club_id === (int) $clubId) {
            return [$match->away_team_id, false, null];
        }

        return [null, null, "le club (fifa_connect_id résolu en id {$clubId}) n'est ni le club domicile ni le club extérieur de ce match"];
    }

    private function resolveTeamByName(?string $name): array
    {
        $name = trim((string) $name);
        if ($name === '') {
            return [null, 'nom vide'];
        }
        $key = 'name:' . mb_strtolower($name);
        if (! array_key_exists($key, $this->teamCache)) {
            $found = DB::table('teams')->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->value('id');
            $this->teamCache[$key] = $found;
        }

        return $this->teamCache[$key] === null
            ? [null, "'{$name}'"]
            : [$this->teamCache[$key], null];
    }
}
