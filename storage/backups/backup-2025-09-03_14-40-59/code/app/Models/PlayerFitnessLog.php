<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerFitnessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'log_date',
        'session_type',
        'duration_minutes',
        'distance_km',
        'calories_burned',
        'max_heart_rate',
        'avg_heart_rate',
        'max_speed_kmh',
        'avg_speed_kmh',
        'sprints_count',
        'fatigue_level',
        'energy_level',
        'notes',
        'metrics',
        'is_completed'
    ];

    protected $casts = [
        'log_date' => 'date',
        'duration_minutes' => 'integer',
        'distance_km' => 'decimal:2',
        'calories_burned' => 'integer',
        'max_heart_rate' => 'decimal:1',
        'avg_heart_rate' => 'decimal:1',
        'max_speed_kmh' => 'decimal:1',
        'avg_speed_kmh' => 'decimal:1',
        'sprints_count' => 'integer',
        'fatigue_level' => 'decimal:1',
        'energy_level' => 'decimal:1',
        'metrics' => 'array',
        'is_completed' => 'boolean'
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function getSessionTypeIcon(): string
    {
        return match($this->session_type) {
            'training' => '🏃',
            'match' => '⚽',
            'recovery' => '🧘',
            'strength' => '💪',
            'cardio' => '❤️',
            'flexibility' => '🤸',
            default => '📊'
        };
    }

    public function getFatigueLevelColor(): string
    {
        return match(true) {
            $this->fatigue_level <= 3 => 'text-green-600',
            $this->fatigue_level <= 6 => 'text-yellow-600',
            default => 'text-red-600'
        };
    }

    public function getEnergyLevelColor(): string
    {
        return match(true) {
            $this->energy_level >= 7 => 'text-green-600',
            $this->energy_level >= 4 => 'text-yellow-600',
            default => 'text-red-600'
        };
    }

    public function getIntensityLevel(): string
    {
        if (!$this->avg_heart_rate) return 'Unknown';
        
        $maxHR = 220 - $this->player->age; // Estimated max heart rate
        $percentage = ($this->avg_heart_rate / $maxHR) * 100;
        
        return match(true) {
            $percentage >= 90 => 'Maximum',
            $percentage >= 80 => 'High',
            $percentage >= 70 => 'Moderate',
            $percentage >= 60 => 'Low',
            default => 'Very Low'
        };
    }

    public function getCaloriesPerMinute(): float
    {
        if (!$this->duration_minutes || $this->duration_minutes <= 0) {
            return 0;
        }
        
        return round($this->calories_burned / $this->duration_minutes, 2);
    }
} 