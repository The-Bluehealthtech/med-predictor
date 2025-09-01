<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseComplete extends Model
{
    use HasFactory;

    protected $table = 'licenses_complete';

    protected $fillable = [
        'fifa_connect_id',
        'license_type',
        'applicant_name',
        'date_of_birth',
        'nationality',
        'position',
        'email',
        'phone',
        'player_id',
        'club_id',
        'association_id',
        'license_reason',
        'validity_period',
        'documents',
        'status',
        'requested_by',
        'requested_at',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'fifa_license_number',
        'fifa_license_category',
        'fifa_license_level',
        'fifa_license_issued_date',
        'fifa_license_expiry_date',
        'fifa_license_status',
        'fifa_license_restrictions',
        'fifa_license_notes'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'documents' => 'array',
        'fifa_license_issued_date' => 'date',
        'fifa_license_expiry_date' => 'date',
        'fifa_license_restrictions' => 'array'
    ];

    // Relationships FIFA CONNECT ID
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes FIFA
    public function scopeByFifaConnectId($query, $fifaConnectId)
    {
        return $query->where('fifa_connect_id', $fifaConnectId);
    }

    public function scopeByFifaLicenseStatus($query, $status)
    {
        return $query->where('fifa_license_status', $status);
    }

    public function scopeByFifaLicenseCategory($query, $category)
    {
        return $query->where('fifa_license_category', $category);
    }

    public function scopeByFifaLicenseLevel($query, $level)
    {
        return $query->where('fifa_license_level', $level);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('license_type', $type);
    }

    // Accessors pour compatibilité avec la vue existante
    public function getStartDateAttribute()
    {
        return $this->fifa_license_issued_date;
    }

    public function getEndDateAttribute()
    {
        return $this->fifa_license_expiry_date;
    }

    public function getIssuedAtAttribute()
    {
        return $this->fifa_license_issued_date;
    }

    public function getLicenseTypeTextAttribute()
    {
        return $this->getFifaLicenseCategoryLabelAttribute();
    }

    public function getStatusTextAttribute()
    {
        return match($this->fifa_license_status) {
            'active' => 'Active',
            'suspended' => 'Suspendue',
            'expired' => 'Expirée',
            'pending' => 'En attente',
            default => 'Inconnu'
        };
    }

    public function getIsExpiredAttribute()
    {
        return $this->isFifaLicenseExpired();
    }

    public function getDaysRemainingAttribute()
    {
        return $this->getFifaLicenseDaysRemaining();
    }

    // Accessors FIFA
    public function getFifaLicenseStatusBadgeAttribute()
    {
        return match($this->fifa_license_status) {
            'active' => '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active FIFA</span>',
            'suspended' => '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Suspendue FIFA</span>',
            'expired' => '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Expirée FIFA</span>',
            'pending' => '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">En attente FIFA</span>',
            default => '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inconnu FIFA</span>'
        };
    }

    public function getFifaLicenseCategoryLabelAttribute()
    {
        return match($this->fifa_license_category) {
            'amateur' => 'Amateur FIFA',
            'semi_pro' => 'Semi-Professionnel FIFA',
            'professional' => 'Professionnel FIFA',
            'international' => 'International FIFA',
            default => 'Catégorie Inconnue FIFA'
        };
    }

    public function getFifaLicenseLevelLabelAttribute()
    {
        return match($this->fifa_license_level) {
            'basic' => 'Niveau Basique FIFA',
            'intermediate' => 'Niveau Intermédiaire FIFA',
            'advanced' => 'Niveau Avancé FIFA',
            'expert' => 'Niveau Expert FIFA',
            default => 'Niveau Inconnu FIFA'
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">En attente</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Approuvée</span>',
            'rejected' => '<span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Rejetée</span>',
            default => '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inconnu</span>'
        };
    }

    public function getLicenseTypeLabelAttribute()
    {
        return match($this->license_type) {
            'player' => 'Licence Joueur FIFA',
            'staff' => 'Licence Staff FIFA',
            'medical' => 'Licence Médicale FIFA',
            'coach' => 'Licence Entraîneur FIFA',
            'referee' => 'Licence Arbitre FIFA',
            default => 'Licence Inconnue FIFA'
        };
    }

    public function getValidityPeriodLabelAttribute()
    {
        return match($this->validity_period) {
            '1_year' => '1 an FIFA',
            '2_years' => '2 ans FIFA',
            '3_years' => '3 ans FIFA',
            '5_years' => '5 ans FIFA',
            default => 'Période Inconnue FIFA'
        };
    }

    // Methods FIFA
    public function isFifaLicenseActive(): bool
    {
        return $this->fifa_license_status === 'active' && 
               $this->fifa_license_expiry_date && 
               $this->fifa_license_expiry_date->isFuture();
    }

    public function isFifaLicenseExpired(): bool
    {
        return $this->fifa_license_expiry_date && 
               $this->fifa_license_expiry_date->isPast();
    }

    public function getFifaLicenseDaysRemaining(): ?int
    {
        if (!$this->fifa_license_expiry_date) {
            return null;
        }
        
        return now()->diffInDays($this->fifa_license_expiry_date, false);
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // Génération automatique FIFA CONNECT ID
    public static function generateFifaConnectId(): string
    {
        $prefix = 'FIFA';
        $year = date('Y');
        $random = strtoupper(substr(md5(uniqid()), 0, 8));
        return "{$prefix}{$year}{$random}";
    }

    // Génération automatique numéro de licence FIFA
    public static function generateFifaLicenseNumber(): string
    {
        $prefix = 'FIFA-LIC';
        $year = date('Y');
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        return "{$prefix}{$year}{$random}";
    }
}





