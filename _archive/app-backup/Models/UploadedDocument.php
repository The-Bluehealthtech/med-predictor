<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'fifa_connect_id',
        'uploaded_by',
        'filename',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'document_type',
        'title',
        'description',
        'ai_analysis',
        'metadata',
        'status'
    ];

    protected $casts = [
        'ai_analysis' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Relation avec l'athlète
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Relation avec l'utilisateur qui a uploadé
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Rechercher par FIFA Connect ID
     */
    public static function findByFifaConnectId(string $fifaConnectId)
    {
        return static::where('fifa_connect_id', $fifaConnectId);
    }

    /**
     * Rechercher un athlète par FIFA Connect ID et créer un document
     */
    public static function createForAthlete(string $fifaConnectId, array $data)
    {
        $athlete = Athlete::where('fifa_connect_id', $fifaConnectId)->first();
        
        if (!$athlete) {
            throw new \Exception("Athlète avec FIFA Connect ID {$fifaConnectId} non trouvé");
        }

        $data['athlete_id'] = $athlete->id;
        $data['fifa_connect_id'] = $fifaConnectId;

        return static::create($data);
    }

    /**
     * Scope pour les documents par type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope pour les documents par statut
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour les documents récents
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Obtenir les documents pour un athlète
     */
    public static function getForAthlete(string $fifaConnectId)
    {
        return static::where('fifa_connect_id', $fifaConnectId)
                    ->with(['athlete', 'uploadedBy'])
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Obtenir l'URL du fichier
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Obtenir la taille formatée
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Obtenir le type de document formaté
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'medical_record' => 'Dossier Médical',
            'imaging' => 'Imagerie',
            'lab_result' => 'Résultats Labo',
            'prescription' => 'Ordonnance',
            'certificate' => 'Certificat',
            'other' => 'Autre',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir le statut formaté
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'processed' => 'Traité',
            'analyzed' => 'Analysé',
            'archived' => 'Archivé',
            default => 'Inconnu'
        };
    }

    /**
     * Vérifier si le document a été analysé par l'IA
     */
    public function hasAiAnalysis(): bool
    {
        return !empty($this->ai_analysis);
    }

    /**
     * Obtenir les résultats d'analyse IA
     */
    public function getAiAnalysisResults(): array
    {
        return $this->ai_analysis ?? [];
    }
} 