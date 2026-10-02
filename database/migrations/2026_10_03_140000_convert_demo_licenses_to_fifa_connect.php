<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Conversion des licences de démonstration existantes au format de
 * l'enregistrement FIFA Connect (données à effacer au lancement) :
 * Player · Football · Registration ; niveau pro (ligues professionnelles de
 * démonstration) ; genre male (championnats masculins), aussi renseigné sur le
 * joueur s'il manquait ; saison de la date de début, validité ramenée à cette
 * saison (1er juillet → 30 juin) ; catégorie d'âge du barème par défaut, âge
 * au 1er janvier de la saison. Rejouable : seules les licences sans type
 * d'enregistrement sont traitées.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('player_licenses', 'registration_type')) {
            return;
        }
        $thresholds = collect(config('licensing.default_scale.categories'))->sortBy(fn ($c) => $c['max_age'] ?? PHP_INT_MAX)->values();

        DB::table('player_licenses as l')->leftJoin('players as p', 'p.id', '=', 'l.player_id')
            ->whereNull('l.registration_type')->whereNotNull('l.player_id')
            ->select('l.id', 'l.player_id', 'l.contract_start_date', 'l.expiry_date', 'l.created_at', 'p.date_of_birth', 'p.gender')
            // chunkById : la conversion retire les lignes du filtre, une pagination par décalage en sauterait.
            ->chunkById(200, function ($licenses) use ($thresholds) {
                foreach ($licenses as $license) {
                    $start = Carbon::parse($license->contract_start_date ?? $license->created_at ?? now());
                    $seasonStart = Carbon::create($start->month >= 7 ? $start->year : $start->year - 1, 7, 1)->startOfDay();
                    // Une licence dont le début précède la saison en cours est rattachée à la saison de son échéance.
                    if ($license->expiry_date && Carbon::parse($license->expiry_date)->gt($seasonStart->copy()->addYear()->subDay())) {
                        $end = Carbon::parse($license->expiry_date);
                        $seasonStart = Carbon::create($end->month >= 7 ? $end->year : $end->year - 1, 7, 1)->startOfDay();
                    }
                    $seasonEnd = $seasonStart->copy()->addYear()->subDay();
                    $reference = Carbon::create($seasonStart->year + 1, 1, 1);
                    $age = $license->date_of_birth ? (int) Carbon::parse($license->date_of_birth)->diffInYears($reference) : null;
                    $category = $age === null ? 'SENIOR' : ($thresholds->first(fn ($c) => $c['max_age'] === null || $age < $c['max_age'])['code'] ?? 'SENIOR');
                    $gender = in_array($license->gender, ['male', 'female'], true) ? $license->gender : 'male';

                    DB::table('player_licenses')->where('id', $license->id)->update([
                        'registration_type' => 'Player',
                        'discipline' => 'Football',
                        'level' => 'pro',
                        'registration_nature' => 'Registration',
                        'gender' => $gender,
                        'age_category' => $category,
                        'season' => $seasonStart->year . '-' . ($seasonStart->year + 1),
                        'contract_start_date' => ($start->gt($seasonStart) ? $start : $seasonStart)->toDateString(),
                        'expiry_date' => (($expiry = Carbon::parse($license->expiry_date ?? $seasonEnd))->lt($seasonEnd) ? $expiry : $seasonEnd)->toDateString(),
                        'contract_end_date' => $seasonEnd->toDateString(),
                    ]);
                    if (!$license->gender) {
                        DB::table('players')->where('id', $license->player_id)->whereNull('gender')->update(['gender' => $gender]);
                    }
                }
            }, 'l.id', 'id');
    }

    public function down(): void
    {
        // Conversion de données de démonstration : pas de retour arrière.
    }
};
