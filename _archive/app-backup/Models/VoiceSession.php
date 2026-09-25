<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class VoiceSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'player_name',
        'current_field',
        'session_data',
        'language',
        'status',
        'error_count',
        'fallback_requested',
        'completed_at',
        'dialogflow_session'
    ];

    protected $casts = [
        'session_data' => 'array',
        'completed_at' => 'datetime',
        'fallback_requested' => 'boolean'
    ];

    protected $attributes = [
        'status' => 'active',
        'error_count' => 0,
        'fallback_requested' => false
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifie si la session est active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Vérifie si la session est terminée
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifie si la session a expiré (plus de 30 minutes)
     */
    public function isExpired(): bool
    {
        return $this->created_at->diffInMinutes(now()) > 30;
    }

    /**
     * Marque la session comme terminée
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    /**
     * Marque la session comme ayant demandé un fallback
     */
    public function markFallbackRequested(): void
    {
        $this->update(['fallback_requested' => true]);
    }

    /**
     * Incrémente le compteur d'erreurs
     */
    public function incrementErrorCount(): void
    {
        $this->increment('error_count');
    }

    /**
     * Obtient le champ actuel de la session
     */
    public function getCurrentField(): string
    {
        return $this->current_field;
    }

    /**
     * Obtient les données de session
     */
    public function getSessionData(): array
    {
        return $this->session_data ?? [];
    }

    /**
     * Met à jour les données de session
     */
    public function updateSessionData(array $data): void
    {
        $this->update(['session_data' => $data]);
    }

    /**
     * Obtient le nom du joueur
     */
    public function getPlayerName(): string
    {
        return $this->player_name;
    }

    /**
     * Obtient la langue de la session
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Obtient le nombre d'erreurs
     */
    public function getErrorCount(): int
    {
        return $this->error_count;
    }

    /**
     * Vérifie si trop d'erreurs ont été commises
     */
    public function hasTooManyErrors(): bool
    {
        return $this->error_count >= 5;
    }

    /**
     * Obtient le temps écoulé depuis le début de la session
     */
    public function getElapsedTime(): int
    {
        return $this->created_at->diffInSeconds(now());
    }

    /**
     * Obtient le temps restant avant expiration
     */
    public function getTimeUntilExpiration(): int
    {
        $expirationTime = 30 * 60; // 30 minutes en secondes
        $elapsed = $this->getElapsedTime();
        return max(0, $expirationTime - $elapsed);
    }

    /**
     * Obtient le pourcentage de progression
     */
    public function getProgressPercentage(): int
    {
        $data = $this->getSessionData();
        $currentStep = $data['current_step'] ?? 0;
        $totalSteps = $data['total_steps'] ?? 5;
        
        if ($totalSteps === 0) return 0;
        
        return min(100, round(($currentStep / $totalSteps) * 100));
    }

    /**
     * Obtient le statut de la session en français
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'active' => 'En cours',
            'completed' => 'Terminée',
            'expired' => 'Expirée',
            'error' => 'Erreur',
            default => 'Inconnu'
        };
    }

    /**
     * Obtient la langue en format lisible
     */
    public function getLanguageLabel(): string
    {
        return match($this->language) {
            'fr' => 'Français',
            'en' => 'Anglais',
            'ar' => 'Arabe tunisien',
            default => 'Inconnue'
        };
    }

    /**
     * Scope pour les sessions actives
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les sessions terminées
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les sessions expirées
     */
    public function scopeExpired($query)
    {
        return $query->where('created_at', '<', now()->subMinutes(30));
    }

    /**
     * Scope pour les sessions avec erreurs
     */
    public function scopeWithErrors($query)
    {
        return $query->where('error_count', '>', 0);
    }

    /**
     * Scope pour les sessions par langue
     */
    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope pour les sessions d'un utilisateur
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Obtient les statistiques de la session
     */
    public function getStatistics(): array
    {
        return [
            'progress_percentage' => $this->getProgressPercentage(),
            'elapsed_time' => $this->getElapsedTime(),
            'time_until_expiration' => $this->getTimeUntilExpiration(),
            'error_count' => $this->error_count,
            'fallback_requested' => $this->fallback_requested,
            'is_expired' => $this->isExpired(),
            'current_field' => $this->current_field,
            'total_fields' => $this->session_data['total_steps'] ?? 5
        ];
    }

    /**
     * Nettoie les sessions expirées
     */
    public static function cleanupExpiredSessions(): int
    {
        return static::expired()->update(['status' => 'expired']);
    }

    /**
     * Obtient le résumé des données PCMA
     */
    public function getPcmaSummary(): array
    {
        $data = $this->getSessionData();
        $fields = $data['fields'] ?? [];
        
        return [
            'player_name' => $this->player_name,
            'poste' => $fields['poste'] ?? 'Non spécifié',
            'age' => $fields['age'] ?? 'Non spécifié',
            'antecedents' => $fields['antecedents'] ?? 'Non spécifié',
            'derniere_blessure' => $fields['derniere_blessure'] ?? 'Non spécifié',
            'statut' => $fields['statut'] ?? 'Non spécifié',
            'completion_date' => $this->completed_at?->format('d/m/Y H:i') ?? 'Non terminé'
        ];
    }
}
