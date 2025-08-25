<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'uploaded_by',
        'document_type',
        'document_name',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'validation_status',
        'validation_notes',
        'validated_by',
        'validated_at',
        'fifa_document_id',
        'fifa_metadata',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'fifa_metadata' => 'array',
    ];

    /**
     * Relations
     */
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Scopes
     */
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeApproved($query)
    {
        return $query->where('validation_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('validation_status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('validation_status', 'rejected');
    }

    /**
     * Accessors & Mutators
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'passport' => 'Passeport',
            'contract' => 'Contrat',
            'medical_certificate' => 'Certificat médical',
            'parental_consent' => 'Consentement parental',
            'work_permit' => 'Permis de travail',
            'identity_card' => 'Carte d\'identité',
            'birth_certificate' => 'Acte de naissance',
            'transfer_form' => 'Formulaire de transfert',
            default => ucfirst(str_replace('_', ' ', $this->document_type)),
        };
    }

    public function getValidationStatusLabelAttribute(): string
    {
        return match($this->validation_status) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'expired' => 'Expiré',
            default => ucfirst($this->validation_status),
        };
    }

    /**
     * Méthodes FIFA
     */
    public function isFifaDocument(): bool
    {
        return !empty($this->fifa_document_id);
    }

    public function getFifaMetadata(): array
    {
        return [
            'document_id' => $this->id,
            'transfer_id' => $this->transfer_id,
            'type' => $this->document_type,
            'name' => $this->document_name,
            'file_name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'validation_status' => $this->validation_status,
            'uploaded_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Méthodes métier
     */
    public function approve(User $user, ?string $notes = null): void
    {
        $this->update([
            'validation_status' => 'approved',
            'validation_notes' => $notes,
            'validated_by' => $user->id,
            'validated_at' => now(),
        ]);
    }

    public function reject(User $user, string $notes): void
    {
        $this->update([
            'validation_status' => 'rejected',
            'validation_notes' => $notes,
            'validated_by' => $user->id,
            'validated_at' => now(),
        ]);
    }

    public function isExpired(): bool
    {
        // Logique pour déterminer si un document est expiré
        // Par exemple, les passeports expirent après 10 ans
        if ($this->document_type === 'passport') {
            // Logique spécifique pour les passeports
            return false; // À implémenter selon les règles métier
        }
        
        return false;
    }

    public function isRequiredForTransfer(): bool
    {
        $requiredTypes = ['passport', 'contract'];
        
        if ($this->transfer->is_minor_transfer) {
            $requiredTypes[] = 'parental_consent';
        }
        
        return in_array($this->document_type, $requiredTypes);
    }
}
