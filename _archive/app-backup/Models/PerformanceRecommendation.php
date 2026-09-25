<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerformanceRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'performance_id',
        'recommendation_type', // 'training', 'recovery', 'tactical', 'mental', 'nutrition', 'injury_prevention'
        'category', // 'physical', 'technical', 'tactical', 'mental', 'social', 'medical'
        'priority', // 'low', 'medium', 'high', 'critical'
        'title',
        'description',
        'detailed_analysis',
        'recommended_actions',
        'expected_outcomes',
        'timeframe', // 'immediate', 'short_term', 'medium_term', 'long_term'
        'difficulty_level', // 'easy', 'moderate', 'challenging', 'expert'
        'resources_required',
        'cost_estimate',
        'success_metrics',
        'risk_assessment',
        
        // IA et analyse
        'ai_model_version',
        'confidence_score',
        'data_sources',
        'analysis_factors',
        'prediction_accuracy',
        
        // Suivi et évaluation
        'status', // 'pending', 'in_progress', 'completed', 'cancelled', 'overdue'
        'assigned_to',
        'start_date',
        'target_completion_date',
        'actual_completion_date',
        'progress_percentage',
        'implementation_notes',
        'results_achieved',
        'feedback_score',
        
        // Métadonnées
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'tags',
        'notes'
    ];

    protected $casts = [
        'recommended_actions' => 'array',
        'expected_outcomes' => 'array',
        'resources_required' => 'array',
        'success_metrics' => 'array',
        'risk_assessment' => 'array',
        'data_sources' => 'array',
        'analysis_factors' => 'array',
        'implementation_notes' => 'array',
        'results_achieved' => 'array',
        'tags' => 'array',
        'start_date' => 'date',
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'approved_at' => 'datetime',
        'confidence_score' => 'decimal:2',
        'prediction_accuracy' => 'decimal:2',
        'progress_percentage' => 'decimal:2',
        'feedback_score' => 'decimal:2',
        'cost_estimate' => 'decimal:2',
        'notes' => 'array'
    ];

    // Relationships
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function performance(): BelongsTo
    {
        return $this->belongsTo(PlayerPerformance::class, 'performance_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeByPlayer($query, $playerId)
    {
        return $query->where('player_id', $playerId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('recommendation_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'in_progress')
                    ->where('target_completion_date', '<', now());
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    public function scopeByTimeframe($query, $timeframe)
    {
        return $query->where('timeframe', $timeframe);
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->isInProgress() && $this->target_completion_date < now();
    }

    public function isHighPriority(): bool
    {
        return in_array($this->priority, ['high', 'critical']);
    }

    public function getDaysUntilDeadline(): int
    {
        return now()->diffInDays($this->target_completion_date, false);
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'overdue' => 'orange',
            default => 'gray'
        };
    }

    public function getStatusText(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'overdue' => 'Overdue',
            default => 'Unknown'
        };
    }

    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray'
        };
    }

    public function getPriorityText(): string
    {
        return match($this->priority) {
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
            default => 'Unknown'
        };
    }

    public function getTimeframeText(): string
    {
        return match($this->timeframe) {
            'immediate' => 'Immediate',
            'short_term' => 'Short Term (1-2 weeks)',
            'medium_term' => 'Medium Term (1-3 months)',
            'long_term' => 'Long Term (3+ months)',
            default => 'Unknown'
        };
    }

    public function getDifficultyText(): string
    {
        return match($this->difficulty_level) {
            'easy' => 'Easy',
            'moderate' => 'Moderate',
            'challenging' => 'Challenging',
            'expert' => 'Expert',
            default => 'Unknown'
        };
    }

    public function getConfidenceLevel(): string
    {
        $score = $this->confidence_score;
        
        if ($score >= 0.9) return 'Very High';
        if ($score >= 0.8) return 'High';
        if ($score >= 0.7) return 'Good';
        if ($score >= 0.6) return 'Moderate';
        if ($score >= 0.5) return 'Low';
        return 'Very Low';
    }

    public function getConfidenceColor(): string
    {
        $score = $this->confidence_score;
        
        if ($score >= 0.8) return 'green';
        if ($score >= 0.6) return 'yellow';
        return 'red';
    }

    public function calculateROI(): float
    {
        if (!$this->cost_estimate || !$this->feedback_score) {
            return 0;
        }

        // Simple ROI calculation based on feedback score and cost
        $benefit = $this->feedback_score * 100; // Convert to percentage
        $cost = $this->cost_estimate;
        
        if ($cost <= 0) return 0;
        
        return round((($benefit - $cost) / $cost) * 100, 2);
    }

    public function getROIColor(): string
    {
        $roi = $this->calculateROI();
        
        if ($roi >= 200) return 'green';
        if ($roi >= 100) return 'blue';
        if ($roi >= 50) return 'yellow';
        if ($roi >= 0) return 'orange';
        return 'red';
    }

    public function updateProgress(int $percentage): void
    {
        $this->update([
            'progress_percentage' => min(100, max(0, $percentage)),
            'status' => $percentage >= 100 ? 'completed' : 'in_progress',
            'actual_completion_date' => $percentage >= 100 ? now() : null
        ]);
    }

    public function markAsCompleted(array $results = []): void
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'actual_completion_date' => now(),
            'results_achieved' => $results
        ]);
    }

    public function assignTo(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
            'status' => 'in_progress',
            'start_date' => now()
        ]);
    }

    public function getEstimatedDuration(): string
    {
        return match($this->timeframe) {
            'immediate' => '1-3 days',
            'short_term' => '1-2 weeks',
            'medium_term' => '1-3 months',
            'long_term' => '3+ months',
            default => 'Unknown'
        };
    }

    public function getResourceSummary(): string
    {
        if (empty($this->resources_required)) {
            return 'No specific resources required';
        }

        $resources = $this->resources_required;
        $summary = [];

        if (isset($resources['equipment'])) {
            $summary[] = count($resources['equipment']) . ' equipment items';
        }

        if (isset($resources['personnel'])) {
            $summary[] = count($resources['personnel']) . ' personnel';
        }

        if (isset($resources['facilities'])) {
            $summary[] = count($resources['facilities']) . ' facilities';
        }

        return implode(', ', $summary);
    }

    public function getRiskLevel(): string
    {
        if (empty($this->risk_assessment)) {
            return 'Low';
        }

        $risks = $this->risk_assessment;
        $maxRisk = 0;

        foreach ($risks as $risk) {
            $level = $risk['level'] ?? 0;
            $maxRisk = max($maxRisk, $level);
        }

        return match(true) {
            $maxRisk >= 8 => 'Very High',
            $maxRisk >= 6 => 'High',
            $maxRisk >= 4 => 'Medium',
            $maxRisk >= 2 => 'Low',
            default => 'Very Low'
        };
    }

    public function getRiskColor(): string
    {
        return match($this->getRiskLevel()) {
            'Very High' => 'red',
            'High' => 'orange',
            'Medium' => 'yellow',
            'Low' => 'blue',
            'Very Low' => 'green',
            default => 'gray'
        };
    }
} 