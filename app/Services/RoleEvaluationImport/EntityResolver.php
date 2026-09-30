<?php

namespace App\Services\RoleEvaluationImport;

use Illuminate\Support\Facades\DB;

/**
 * Résout joueur / équipe / match à partir des colonnes source déclarées dans
 * le mapping (section "resolve"), pour satisfaire l'exigence du mandat
 * "joueur et match connus" avant tout import.
 *
 * Deux modes supportés par entité, déclarés dans le mapping :
 *  - "id"               : la colonne source contient directement l'identifiant interne (players.id / teams.id / matches.id)
 *  - "fifa_connect_id"   : (joueur uniquement) résolution via players.fifa_connect_id
 *  - "name"              : (équipe uniquement) résolution via teams.name (comparaison insensible à la casse)
 *  - "teams_and_date"    : (match uniquement) résolution via home_team + away_team (par nom) + date (jour seul, sans l'heure)
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

        return [null, "mode de résolution match non configuré ou inconnu : '{$mode}'"];
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
