<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Injury;
use App\Models\PCMA;
use App\Models\Player;
use App\Models\PlayerFitnessLog;
use App\Models\PlayerPassport;
use App\Models\PlayerSeasonStat;
use Illuminate\Support\Facades\Schema;

final class MedicalVigilanceModelRegistry
{
    public function forPlayer(Player $player): array
    {
        $definitions = config('medical_vigilance_models.models', []);

        return [
            'identity' => $this->build($definitions['identity'] ?? [], $this->identityData($player)),
            'injury' => $this->build($definitions['injury'] ?? [], $this->injuryData($player)),
            'cardiac' => $this->build($definitions['cardiac'] ?? [], $this->cardiacData($player)),
        ];
    }

    private function identityData(Player $player): array
    {
        $passport = Schema::hasTable('player_passports')
            ? PlayerPassport::where('player_id', $player->id)->latest('id')->first()
            : null;

        return [
            'date_of_birth' => (bool) $player->date_of_birth,
            'fifa_connect_id' => filled($player->fifa_connect_id),
            'passport' => (bool) $passport,
        ];
    }

    private function injuryData(Player $player): array
    {
        $injuries = collect();
        if (Schema::hasTable('athletes') && Schema::hasTable('injuries')) {
            $athleteIds = Athlete::where('player_id', $player->id)->pluck('id');
            if ($athleteIds->isNotEmpty()) {
                $injuries = Injury::whereIn('athlete_id', $athleteIds)->get();
            }
        }

        $trainingExposure = false;
        $matchExposure = false;
        if (Schema::hasTable('player_fitness_logs')) {
            $logs = PlayerFitnessLog::where('player_id', $player->id)
                ->where('log_date', '>=', now()->subDays(28))
                ->where('is_completed', true)
                ->get();
            $trainingExposure = (int) $logs->where('session_type', 'training')->sum('duration_minutes') > 0;
            $matchExposure = (int) $logs->where('session_type', 'match')->sum('duration_minutes') > 0;
        }
        if (!$matchExposure && Schema::hasTable('player_season_stats')) {
            $matchExposure = (int) PlayerSeasonStat::where('player_id', $player->id)->sum('minutes_played') > 0;
        }

        $timeLoss = $injuries->contains(fn (Injury $injury) =>
            filled($injury->estimated_recovery_days)
            || filled($injury->expected_return_date)
            || filled($injury->actual_return_date)
        );

        return [
            'injury_history' => $injuries->isNotEmpty(),
            'training_exposure' => $trainingExposure,
            'match_exposure' => $matchExposure,
            'time_loss' => $timeLoss,
        ];
    }

    private function cardiacData(Player $player): array
    {
        $pcma = Schema::hasTable('pcmas')
            ? PCMA::where('player_id', $player->id)->orderByDesc('assessment_date')->orderByDesc('id')->first()
            : null;

        return [
            'pcma' => (bool) $pcma,
            'ecg' => (bool) ($pcma && ($pcma->ecg_date || filled($pcma->ecg_interpretation))),
            'medical_history' => (bool) ($pcma && !empty($pcma->medical_history)),
            'physical_examination' => (bool) ($pcma && !empty($pcma->physical_examination)),
        ];
    }

    private function build(array $definition, array $availability): array
    {
        $required = $definition['required_data'] ?? [];
        $present = [];
        $missing = [];

        foreach ($required as $key => $label) {
            (!empty($availability[$key]) ? $present : $missing)[] = $label;
        }

        $total = max(1, count($required));
        $coverage = (int) round((count($present) / $total) * 100);
        $state = $coverage === 100 ? 'ready' : ($coverage >= 50 ? 'partial' : 'insufficient');

        return [
            'id' => $definition['id'] ?? null,
            'name' => $definition['name'] ?? 'Référentiel externe',
            'mode' => $definition['mode'] ?? 'observation',
            'validation_status' => $definition['validation_status'] ?? 'reference_only',
            'population' => $definition['population'] ?? null,
            'source' => $definition['source'] ?? null,
            'url' => $definition['url'] ?? null,
            'state' => $state,
            'coverage' => $coverage,
            'present' => $present,
            'missing' => $missing,
        ];
    }
}
