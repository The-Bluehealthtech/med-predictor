<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\UsesEnhancedTenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseRequest extends Model
{
    use UsesEnhancedTenantScope;
    use HasFactory;

    protected $table = 'license_requests';

    protected $fillable = [
        // Identifiants FIFA
        'fifa_connect_id',
        'fifa_license_request_number',
        'fifa_license_number',
        
        // Informations de la demande
        'request_type',
        'license_type',
        'license_category',
        'license_level',
        'request_status',
        'request_reason',
        'validity_period',
        
        // Identité du joueur
        'first_name',
        'last_name',
        'middle_name',
        'full_name',
        'date_of_birth',
        'place_of_birth',
        'nationality',
        'second_nationality',
        'passport_number',
        'national_id_number',
        'birth_certificate_number',
        
        // Informations physiques
        'height',
        'weight',
        'eye_color',
        'hair_color',
        'distinguishing_marks',
        
        // Informations de contact
        'email',
        'phone',
        'mobile',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        
        // Adresse
        'current_address',
        'current_city',
        'current_country',
        'current_postal_code',
        'permanent_address',
        'permanent_city',
        'permanent_country',
        'permanent_postal_code',
        
        // Informations institutionnelles
        'current_club_id',
        'current_club_name',
        'current_club_city',
        'current_club_country',
        'club_contract_start',
        'club_contract_end',
        'club_role',
        'national_association_id',
        'national_association_name',
        'national_association_code',
        'national_association_country',
        'confederation_id',
        'confederation_name',
        'confederation_code',
        
        // Historique des licences (VU PAR L'ASSOCIATION)
        'local_license_number',
        'previous_licenses',
        'previous_license_numbers',
        'last_license_issue_date',
        'last_license_expiry_date',
        'last_license_status',
        'last_license_category',
        'last_license_level',
        'last_license_issuer',
        'last_license_country',
        'disciplinary_history',
        'has_active_suspension',
        'suspension_start_date',
        'suspension_end_date',
        'suspension_reason',
        'suspension_issuer',
        'suspension_type',
        'transfer_history',
        'previous_clubs',
        'last_transfer_date',
        
        // Informations médicales (VU PAR L'ASSOCIATION)
        'medical_certificate_number',
        'medical_certificate_issue_date',
        'medical_certificate_expiry_date',
        'medical_certificate_issuer',
        'medical_restrictions',
        'medical_conditions',
        'medical_clearance_status',
        'medical_tests',
        'last_medical_checkup',
        'medical_doctor_name',
        'medical_clinic_name',
        'medical_notes',
        'medical_history',
        'has_chronic_conditions',
        'chronic_conditions_details',
        'takes_medication',
        'medication_details',
        
        // Documents
        'passport_copy_path',
        'national_id_copy_path',
        'birth_certificate_copy_path',
        'photo_path',
        'previous_license_copy_path',
        'club_contract_copy_path',
        'medical_certificate_copy_path',
        'additional_documents',
        'document_notes',
        
        // Processus de validation
        'requested_by',
        'requested_at',
        'request_notes',
        'club_approved_by',
        'club_approved_at',
        'club_approval_notes',
        'association_reviewed_by',
        'association_reviewed_at',
        'association_review_notes',
        'association_decision',
        'fifa_approved_by',
        'fifa_approved_at',
        'fifa_approval_notes',
        'fifa_decision',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'correction_instructions',
        
        // Métadonnées
        'license_issue_date',
        'license_expiry_date',
        'license_renewal_date',
        'license_status',
        'license_restrictions',
        'license_notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'club_contract_start' => 'date',
        'club_contract_end' => 'date',
        'last_license_issue_date' => 'date',
        'last_license_expiry_date' => 'date',
        'suspension_start_date' => 'date',
        'suspension_end_date' => 'date',
        'medical_certificate_issue_date' => 'date',
        'medical_certificate_expiry_date' => 'date',
        'last_medical_checkup' => 'date',
        'last_transfer_date' => 'date',
        'requested_at' => 'datetime',
        'club_approved_at' => 'datetime',
        'association_reviewed_at' => 'datetime',
        'fifa_approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'license_issue_date' => 'date',
        'license_expiry_date' => 'date',
        'license_renewal_date' => 'date',
        'has_active_suspension' => 'boolean',
        'has_chronic_conditions' => 'boolean',
        'takes_medication' => 'boolean',
        'previous_licenses' => 'array',
        'disciplinary_history' => 'array',
        'transfer_history' => 'array',
        'medical_tests' => 'array',
        'medical_history' => 'array',
        'additional_documents' => 'array',
        'license_restrictions' => 'array',
        'height' => 'integer',
        'weight' => 'integer'
    ];

    // ========================================
    // 🔗 RELATIONS
    // ========================================

    public function currentClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'current_club_id');
    }

    public function nationalAssociation(): BelongsTo
    {
        return $this->belongsTo(Association::class, 'national_association_id');
    }

    public function confederation(): BelongsTo
    {
        return $this->belongsTo(Confederation::class, 'confederation_id');
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function clubApprovedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'club_approved_by');
    }

    public function associationReviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'association_reviewed_by');
    }

    public function fifaApprovedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fifa_approved_by');
    }

    // ========================================
    // 🎯 SCOPES FIFA
    // ========================================

    public function scopeByFifaConnectId($query, $fifaConnectId)
    {
        return $query->where('fifa_connect_id', $fifaConnectId);
    }

    public function scopeByLocalLicenseNumber($query, $localLicenseNumber)
    {
        return $query->where('local_license_number', $localLicenseNumber);
    }

    public function scopeByRequestStatus($query, $status)
    {
        return $query->where('request_status', $status);
    }

    public function scopeByLicenseType($query, $type)
    {
        return $query->where('license_type', $type);
    }

    public function scopeByLicenseCategory($query, $category)
    {
        return $query->where('license_category', $category);
    }

    public function scopeByLicenseLevel($query, $level)
    {
        return $query->where('license_level', $level);
    }

    public function scopeByClub($query, $clubId)
    {
        return $query->where('current_club_id', $clubId);
    }

    public function scopeByAssociation($query, $associationId)
    {
        return $query->where('national_association_id', $associationId);
    }

    public function scopePending($query)
    {
        return $query->where('request_status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('request_status', 'submitted');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('request_status', 'under_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('request_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('request_status', 'rejected');
    }

    // ========================================
    // 🏷️ ACCESSORS
    // ========================================

    public function getFullNameAttribute()
    {
        if ($this->full_name) {
            return $this->full_name;
        }
        
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return implode(' ', $parts);
    }

    public function getRequestTypeLabelAttribute()
    {
        return match($this->request_type) {
            'new_license' => 'Nouvelle Licence',
            'renewal' => 'Renouvellement',
            'upgrade' => 'Mise à Niveau',
            'transfer' => 'Transfert',
            'replacement' => 'Remplacement',
            default => 'Type Inconnu'
        };
    }

    public function getLicenseTypeLabelAttribute()
    {
        return match($this->license_type) {
            'player' => 'Licence Joueur',
            'staff' => 'Licence Staff',
            'medical' => 'Licence Médicale',
            'coach' => 'Licence Entraîneur',
            'referee' => 'Licence Arbitre',
            'administrative' => 'Licence Administrative',
            default => 'Type Inconnu'
        };
    }

    public function getLicenseCategoryLabelAttribute()
    {
        return match($this->license_category) {
            'amateur' => 'Amateur',
            'semi_pro' => 'Semi-Professionnel',
            'professional' => 'Professionnel',
            'international' => 'International',
            default => 'Catégorie Inconnue'
        };
    }

    public function getLicenseLevelLabelAttribute()
    {
        return match($this->license_level) {
            'basic' => 'Basique',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
            'expert' => 'Expert',
            default => 'Niveau Inconnu'
        };
    }

    public function getRequestStatusLabelAttribute()
    {
        return match($this->request_status) {
            'draft' => 'Brouillon',
            'submitted' => 'Soumise',
            'under_review' => 'En Révision',
            'additional_info_required' => 'Informations Supplémentaires Requises',
            'approved' => 'Approuvée',
            'rejected' => 'Rejetée',
            'expired' => 'Expirée',
            default => 'Statut Inconnu'
        };
    }

    public function getRequestStatusBadgeAttribute()
    {
        return match($this->request_status) {
            'draft' => '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Brouillon</span>',
            'submitted' => '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Soumise</span>',
            'under_review' => '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">En Révision</span>',
            'additional_info_required' => '<span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">Info Requise</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Approuvée</span>',
            'rejected' => '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Rejetée</span>',
            'expired' => '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Expirée</span>',
            default => '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inconnu</span>'
        };
    }

    public function getValidityPeriodLabelAttribute()
    {
        return match($this->validity_period) {
            '1_year' => '1 an',
            '2_years' => '2 ans',
            '3_years' => '3 ans',
            '5_years' => '5 ans',
            default => 'Période Inconnue'
        };
    }

    // ========================================
    // 🔍 MÉTHODES DE VALIDATION
    // ========================================

    public function canBeSubmitted(): bool
    {
        return $this->request_status === 'draft' && $this->hasRequiredFields();
    }

    public function canBeReviewed(): bool
    {
        return in_array($this->request_status, ['submitted', 'under_review']);
    }

    public function canBeApproved(): bool
    {
        return in_array($this->request_status, ['submitted', 'under_review', 'additional_info_required']);
    }

    public function canBeRejected(): bool
    {
        return in_array($this->request_status, ['submitted', 'under_review', 'additional_info_required']);
    }

    public function isPending(): bool
    {
        return in_array($this->request_status, ['draft', 'submitted', 'under_review', 'additional_info_required']);
    }

    public function isApproved(): bool
    {
        return $this->request_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->request_status === 'rejected';
    }

    public function isExpired(): bool
    {
        return $this->request_status === 'expired';
    }

    // ========================================
    // 📋 MÉTHODES UTILITAIRES
    // ========================================

    public function hasRequiredFields(): bool
    {
        $requiredFields = [
            'first_name', 'last_name', 'date_of_birth', 'nationality',
            'current_club_id', 'national_association_id',
            'request_reason', 'photo_path'
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }

    public function getMissingFields(): array
    {
        $requiredFields = [
            'first_name' => 'Prénom',
            'last_name' => 'Nom',
            'date_of_birth' => 'Date de naissance',
            'nationality' => 'Nationalité',
            'current_club_id' => 'Club actuel',
            'national_association_id' => 'Association nationale',
            'request_reason' => 'Raison de la demande',
            'photo_path' => 'Photo d\'identité'
        ];

        $missing = [];
        foreach ($requiredFields as $field => $label) {
            if (empty($this->$field)) {
                $missing[] = $label;
            }
        }

        return $missing;
    }

    public function getProgressPercentage(): int
    {
        $requiredFields = [
            'first_name', 'last_name', 'date_of_birth', 'nationality',
            'current_club_id', 'national_association_id',
            'request_reason', 'photo_path', 'passport_copy_path',
            'birth_certificate_copy_path', 'medical_certificate_copy_path'
        ];

        $completed = 0;
        foreach ($requiredFields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($requiredFields)) * 100);
    }

    // ========================================
    // 🆔 GÉNÉRATION AUTOMATIQUE
    // ========================================

    public static function generateFifaConnectId(): string
    {
        $prefix = 'FIFA';
        $year = date('Y');
        $random = strtoupper(substr(md5(uniqid()), 0, 8));
        return "{$prefix}{$year}{$random}";
    }

    public static function generateFifaLicenseRequestNumber(): string
    {
        $prefix = 'FIFA-REQ';
        $year = date('Y');
        $month = date('m');
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        return "{$prefix}{$year}{$month}{$random}";
    }

    public static function generateFifaLicenseNumber(): string
    {
        $prefix = 'FIFA-LIC';
        $year = date('Y');
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        return "{$prefix}{$year}{$random}";
    }

    // ========================================
    // 📊 MÉTHODES DE SUIVI
    // ========================================

    public function getDaysSinceSubmission(): ?int
    {
        if (!$this->requested_at) {
            return null;
        }
        
        return now()->diffInDays($this->requested_at);
    }

    public function getEstimatedProcessingTime(): int
    {
        // Estimation basée sur le type de licence
        return match($this->license_type) {
            'player' => 15, // 15 jours pour les joueurs
            'staff' => 20,  // 20 jours pour le staff
            'medical' => 25, // 25 jours pour le médical
            'coach' => 30,  // 30 jours pour les entraîneurs
            'referee' => 35, // 35 jours pour les arbitres
            default => 20
        };
    }

    public function isOverdue(): bool
    {
        if (!$this->requested_at) {
            return false;
        }
        
        $estimatedDays = $this->getEstimatedProcessingTime();
        $daysSinceSubmission = $this->getDaysSinceSubmission();
        
        return $daysSinceSubmission > $estimatedDays && $this->isPending();
    }

    // ========================================
    // 🏥 MÉTHODES MÉDICALES
    // ========================================

    public function isMedicallyCleared(): bool
    {
        return $this->medical_clearance_status === 'cleared' || 
               $this->medical_clearance_status === 'approved';
    }

    public function hasMedicalRestrictions(): bool
    {
        return !empty($this->medical_restrictions);
    }

    public function getMedicalCertificateDaysUntilExpiry(): ?int
    {
        if (!$this->medical_certificate_expiry_date) {
            return null;
        }
        
        return now()->diffInDays($this->medical_certificate_expiry_date, false);
    }

    public function isMedicalCertificateExpired(): bool
    {
        if (!$this->medical_certificate_expiry_date) {
            return true;
        }
        
        return $this->medical_certificate_expiry_date->isPast();
    }

    // ========================================
    // 📚 MÉTHODES LICENCES
    // ========================================

    public function hasPreviousLicenses(): bool
    {
        return !empty($this->previous_licenses) || !empty($this->previous_license_numbers);
    }

    public function getLastLicenseDaysUntilExpiry(): ?int
    {
        if (!$this->last_license_expiry_date) {
            return null;
        }
        
        return now()->diffInDays($this->last_license_expiry_date, false);
    }

    public function isLastLicenseExpired(): bool
    {
        if (!$this->last_license_expiry_date) {
            return true;
        }
        
        return $this->last_license_expiry_date->isPast();
    }

    public function hasActiveSuspension(): bool
    {
        if (!$this->has_active_suspension) {
            return false;
        }
        
        if (!$this->suspension_end_date) {
            return true;
        }
        
        return $this->suspension_end_date->isFuture();
    }

    public function getSuspensionDaysRemaining(): ?int
    {
        if (!$this->hasActiveSuspension()) {
            return null;
        }
        
        if (!$this->suspension_end_date) {
            return null;
        }
        
        return now()->diffInDays($this->suspension_end_date, false);
    }
}
