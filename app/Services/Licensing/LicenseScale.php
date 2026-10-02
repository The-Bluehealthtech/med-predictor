<?php

namespace App\Services\Licensing;

use App\Models\ClubOfficial;
use App\Models\LicenseAgeCategory;
use App\Models\LicenseScaleSetting;
use App\Models\Player;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Barème des licences d'une fédération, organisé comme l'enregistrement FIFA
 * Connect : genre de la personne (male, female), puis discipline (Football,
 * Futsal, BeachSoccer), puis catégories d'âge (paramètre de la fédération).
 * Chaque catégorie fixe les niveaux autorisés (amateur, pro), les tarifs, la
 * règle PCMA et les pièces exigées. Les licences d'officiels (TeamOfficial,
 * OrganisationOfficial) ont leur propre tarif, sans catégorie d'âge.
 *
 * Une licence vaut au plus une saison (début de saison réglable, 1er juillet par
 * défaut). L'âge est calculé à la date de référence comprise dans la saison
 * (1er janvier par défaut) ; U-x = moins de x ans à cette date.
 */
final class LicenseScale
{
    public function settings(?int $associationId): LicenseScaleSetting
    {
        $defaults = config('licensing.default_scale');

        return ($associationId ? LicenseScaleSetting::query()->where('association_id', $associationId)->first() : null)
            ?? new LicenseScaleSetting(['association_id' => $associationId, 'currency' => $defaults['currency'],
                'season_start_month' => $defaults['season_start_month'], 'season_start_day' => $defaults['season_start_day'],
                'reference_month' => $defaults['reference_month'], 'reference_day' => $defaults['reference_day'], 'official_fees' => []]);
    }

    /** Catégories d'un genre et d'une discipline ; barème par défaut (non enregistré) s'il n'y en a pas. */
    public function categories(?int $associationId, string $gender, string $discipline): Collection
    {
        $saved = $associationId ? LicenseAgeCategory::query()->where(compact('gender', 'discipline') + ['association_id' => $associationId])->orderBy('position')->get() : collect();

        return $saved->isNotEmpty() ? $saved : collect(config('licensing.default_scale.categories'))->values()
            ->map(fn ($c, $i) => new LicenseAgeCategory($c + ['association_id' => $associationId, 'gender' => $gender, 'discipline' => $discipline, 'fees' => [], 'position' => $i]));
    }

    public function isSaved(?int $associationId): bool
    {
        return $associationId && LicenseAgeCategory::query()->where('association_id', $associationId)->exists();
    }

    public function associationOfClub(?int $clubId): ?int
    {
        $id = $clubId ? DB::table('clubs')->where('id', $clubId)->value('association_id') : null;

        return $id ? (int) $id : null;
    }

    /** Saison contenant une date : libellé « 2026-2027 », début et fin. */
    public function season(LicenseScaleSetting $settings, $date = null): array
    {
        $date = $date ? Carbon::parse($date) : now();
        $start = Carbon::create($date->year, (int) $settings->season_start_month, min((int) $settings->season_start_day, 28))->startOfDay();
        if ($date->lt($start)) {
            $start->subYear();
        }
        $end = $start->copy()->addYear()->subDay();

        return ['label' => $start->year . '-' . ($start->year + 1), 'start' => $start, 'end' => $end];
    }

    /** Saison en cours et saison suivante, proposées au club. */
    public function selectableSeasons(LicenseScaleSetting $settings): array
    {
        $current = $this->season($settings);
        $next = $this->season($settings, $current['end']->copy()->addDay());

        return [$current['label'] => $current, $next['label'] => $next];
    }

    public function seasonByLabel(LicenseScaleSetting $settings, string $label): ?array
    {
        return $this->selectableSeasons($settings)[$label] ?? null;
    }

    /** Date de référence de l'âge, comprise dans la saison. */
    public function referenceDate(LicenseScaleSetting $settings, array $season): Carbon
    {
        $reference = Carbon::create($season['start']->year, (int) $settings->reference_month, min((int) $settings->reference_day, 28))->startOfDay();

        return $reference->lt($season['start']) ? $reference->addYear() : $reference;
    }

    /**
     * Règles d'une licence de joueur.
     *
     * @return array{season:array, gender:?string, gender_missing:bool, category:?string, category_label:string, age:?int, reference_date:Carbon,
     *               allowed:bool, fee:?float, currency:string, pcma_required:bool, pcma_reason:string, documents:array, age_unknown:bool}
     */
    public function playerRules(Player $player, string $discipline, string $level, string $nature = 'Registration', ?array $season = null): array
    {
        $associationId = $this->associationOfClub($player->club_id) ?? ($player->association_id ? (int) $player->association_id : null);
        $settings = $this->settings($associationId);
        $season ??= $this->season($settings);
        $reference = $this->referenceDate($settings, $season);
        $gender = in_array($player->gender, array_keys(config('licensing.genders')), true) ? $player->gender : null;
        $age = $player->date_of_birth ? (int) Carbon::parse($player->date_of_birth)->diffInYears($reference) : null;

        // Barème du genre (masculin par défaut tant que le genre n'est pas renseigné : il est alors exigé au dépôt).
        $ordered = $this->categories($associationId, $gender ?? 'male', $discipline)->sortBy(fn ($c) => $c->max_age ?? PHP_INT_MAX)->values();
        $category = ($age === null ? null : $ordered->first(fn ($c) => $c->max_age === null || $age < $c->max_age)) ?? $ordered->last();

        $rule = $category?->pcma_rule ?? 'all';
        $pcmaRequired = $age === null || $rule === 'all' || ($rule === 'pro' && $level === 'pro');
        $label = $category?->label ?? 'Senior';
        $levelLabel = mb_strtolower(config("licensing.levels.{$level}", $level));
        $where = "Catégorie {$label}" . ($age !== null ? " ({$age} ans au {$reference->format('d/m/Y')})" : '');
        $fee = $category?->fees[$level] ?? null;

        return [
            'season' => $season,
            'gender' => $gender,
            'gender_missing' => $gender === null,
            'category' => $category?->code,
            'category_label' => $label,
            'age' => $age,
            'reference_date' => $reference,
            'allowed' => in_array($level, $category?->allowed_levels ?? [], true),
            'fee' => $fee === null || $fee === '' ? null : (float) $fee,
            'currency' => (string) $settings->currency,
            'pcma_required' => $pcmaRequired,
            'pcma_reason' => $age === null
                ? 'Date de naissance inconnue : catégorie senior et PCMA exigés par précaution.'
                : $where . ($pcmaRequired ? " : PCMA exigé pour un joueur {$levelLabel}." : " : PCMA non exigé pour un joueur {$levelLabel}."),
            'documents' => array_values(array_unique(array_merge($category?->required_documents ?? [],
                config("licensing.level_documents.{$level}", []), config("licensing.nature_documents.{$nature}", [])))),
            'age_unknown' => $age === null,
        ];
    }

    /** Règles d'une licence d'officiel d'équipe ou de dirigeant (sans catégorie d'âge ni PCMA). */
    public function officialRules(ClubOfficial $official, ?array $season = null): array
    {
        $settings = $this->settings($this->associationOfClub($official->club_id));
        $type = (string) $official->registration_type;
        $fee = $settings->official_fees[$type] ?? null;

        return [
            'season' => $season ?? $this->season($settings),
            'fee' => $fee === null || $fee === '' ? null : (float) $fee,
            'currency' => (string) $settings->currency,
            'documents' => config('licensing.official_documents', []),
            'pcma_required' => false,
        ];
    }

    /**
     * Enregistre le barème d'une fédération : réglages et, pour chaque genre et
     * discipline, l'ensemble de ses catégories (remplacées).
     *
     * @param  array<string, array<int, array>>  $scopes  clé « genre|discipline »
     */
    public function save(int $associationId, array $settings, array $scopes): void
    {
        DB::transaction(function () use ($associationId, $settings, $scopes) {
            LicenseScaleSetting::query()->updateOrCreate(['association_id' => $associationId], $settings);
            foreach ($scopes as $scope => $categories) {
                [$gender, $discipline] = explode('|', $scope);
                $keep = [];
                foreach (array_values($categories) as $i => $data) {
                    $keep[] = LicenseAgeCategory::query()->updateOrCreate(
                        ['association_id' => $associationId, 'gender' => $gender, 'discipline' => $discipline, 'code' => $data['code']],
                        $data + ['position' => $i]
                    )->id;
                }
                LicenseAgeCategory::query()->where(['association_id' => $associationId, 'gender' => $gender, 'discipline' => $discipline])
                    ->whereNotIn('id', $keep)->get()->each->delete();
            }
        });
    }
}
