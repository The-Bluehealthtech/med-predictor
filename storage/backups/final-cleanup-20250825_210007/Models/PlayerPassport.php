<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class PlayerPassport extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'fifa_connect_id',
        'passport_number',
        'passport_type', // 'electronic', 'physical', 'temporary'
        'status', // 'active', 'suspended', 'expired', 'revoked', 'pending_validation'
        'issue_date',
        'expiry_date',
        'renewal_date',
        'issuing_authority',
        'issuing_country',
        'registration_number',
        
        // Informations personnelles FIFA
        'fifa_name',
        'fifa_first_name',
        'fifa_last_name',
        'fifa_date_of_birth',
        'fifa_nationality',
        'fifa_second_nationality',
        'fifa_place_of_birth',
        'fifa_country_of_birth',
        
        // Informations de licence
        'license_type', // 'professional', 'amateur', 'youth', 'international'
        'license_category', // 'A', 'B', 'C', 'D', 'E'
        'license_status', // 'valid', 'pending', 'expired', 'suspended', 'revoked'
        'license_issue_date',
        'license_expiry_date',
        
        // Informations de transfert
        'transfer_status', // 'registered', 'pending_transfer', 'transferred', 'free_agent'
        'current_club_id',
        'previous_club_id',
        'transfer_history',
        'itc_status', // 'not_required', 'not_requested', 'requested', 'approved', 'rejected'
        'itc_request_date',
        'itc_response_date',
        'itc_response_code',
        'itc_response_message',
        
        // Informations médicales
        'medical_clearance',
        'medical_clearance_date',
        'medical_clearance_expiry',
        'fitness_certificate',
        'fitness_certificate_date',
        'fitness_certificate_expiry',
        'doping_test_status',
        'doping_test_date',
        'doping_test_result',
        
        // Informations disciplinaires
        'disciplinary_record',
        'suspensions',
        'warnings',
        'fines',
        'disciplinary_points',
        
        // Informations de performance
        'performance_history',
        'achievements',
        'awards',
        'international_caps',
        'international_goals',
        
        // Documents et signatures
        'photo_url',
        'signature_url',
        'document_hash',
        'digital_signature',
        'certification_chain',
        
        // Conformité et audit
        'compliance_status',
        'last_audit_date',
        'audit_results',
        'gdpr_consent',
        'data_retention_policy',
        
        // Métadonnées
        'version',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
        'fifa_date_of_birth' => 'date',
        'license_issue_date' => 'date',
        'license_expiry_date' => 'date',
        'medical_clearance_date' => 'date',
        'medical_clearance_expiry' => 'date',
        'fitness_certificate_date' => 'date',
        'fitness_certificate_expiry' => 'date',
        'doping_test_date' => 'date',
        'itc_request_date' => 'datetime',
        'itc_response_date' => 'datetime',
        'transfer_history' => 'array',
        'disciplinary_record' => 'array',
        'suspensions' => 'array',
        'warnings' => 'array',
        'fines' => 'array',
        'performance_history' => 'array',
        'achievements' => 'array',
        'awards' => 'array',
        'audit_results' => 'array',
        'approved_at' => 'datetime',
        'notes' => 'array'
    ];

    // Relationships
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function currentClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'current_club_id');
    }

    public function previousClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'previous_club_id');
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

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'player_id', 'player_id');
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class, 'player_id', 'player_id');
    }

    public function performances(): HasMany
    {
        return $this->hasMany(PlayerPerformance::class, 'player_id', 'player_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    public function scopeByLicenseType($query, $type)
    {
        return $query->where('license_type', $type);
    }

    public function scopeByClub($query, $clubId)
    {
        return $query->where('current_club_id', $clubId);
    }

    public function scopeByAssociation($query, $associationId)
    {
        return $query->whereHas('currentClub', function ($q) use ($associationId) {
            $q->where('association_id', $associationId);
        });
    }

    public function scopePendingValidation($query)
    {
        return $query->where('status', 'pending_validation');
    }

    public function scopeCompliant($query)
    {
        return $query->where('compliance_status', 'compliant');
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expiry_date > now();
    }

    public function isExpired(): bool
    {
        return $this->expiry_date < now();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date->between(now(), now()->addDays($days));
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function isPendingValidation(): bool
    {
        return $this->status === 'pending_validation';
    }

    public function isCompliant(): bool
    {
        return $this->compliance_status === 'compliant';
    }

    public function getDaysUntilExpiry(): int
    {
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'active' => 'green',
            'suspended' => 'red',
            'expired' => 'orange',
            'revoked' => 'gray',
            'pending_validation' => 'yellow',
            default => 'gray'
        };
    }

    public function getStatusText(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'suspended' => 'Suspended',
            'expired' => 'Expired',
            'revoked' => 'Revoked',
            'pending_validation' => 'Pending Validation',
            default => 'Unknown'
        };
    }

    public function getLicenseStatusColor(): string
    {
        return match($this->license_status) {
            'valid' => 'green',
            'pending' => 'yellow',
            'expired' => 'orange',
            'suspended' => 'red',
            'revoked' => 'gray',
            default => 'gray'
        };
    }

    public function getItcStatusColor(): string
    {
        return match($this->itc_status) {
            'not_required' => 'gray',
            'not_requested' => 'yellow',
            'requested' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    public function generatePassportNumber(): string
    {
        $prefix = 'FIFA';
        $year = now()->format('Y');
        $random = strtoupper(substr(md5(uniqid()), 0, 8));
        
        return "{$prefix}-{$year}-{$random}";
    }

    public function calculateDocumentHash(): string
    {
        $data = [
            'player_id' => $this->player_id,
            'fifa_connect_id' => $this->fifa_connect_id,
            'passport_number' => $this->passport_number,
            'issue_date' => $this->issue_date->toISOString(),
            'expiry_date' => $this->expiry_date->toISOString(),
            'fifa_name' => $this->fifa_name,
            'fifa_date_of_birth' => $this->fifa_date_of_birth->toISOString(),
            'fifa_nationality' => $this->fifa_nationality,
            'license_type' => $this->license_type,
            'current_club_id' => $this->current_club_id,
            'version' => $this->version
        ];

        return hash('sha256', json_encode($data));
    }

    public function generateDigitalSignature(): string
    {
        $data = $this->calculateDocumentHash();
        $privateKey = config('fifa.private_key');
        
        // In a real implementation, this would use proper cryptographic signing
        return hash_hmac('sha256', $data, $privateKey);
    }

    public function validateDigitalSignature(): bool
    {
        $expectedSignature = $this->generateDigitalSignature();
        return hash_equals($expectedSignature, $this->digital_signature);
    }

    public function exportToPdf(): string
    {
        // This would generate a PDF with all passport information
        // For now, return a placeholder
        return "PDF content for passport {$this->passport_number}";
    }

    public function getTransferEligibility(): array
    {
        $eligibility = [
            'eligible' => true,
            'reasons' => []
        ];

        if (!$this->isActive()) {
            $eligibility['eligible'] = false;
            $eligibility['reasons'][] = 'Passport not active';
        }

        if ($this->isSuspended()) {
            $eligibility['eligible'] = false;
            $eligibility['reasons'][] = 'Player suspended';
        }

        if (!$this->medical_clearance) {
            $eligibility['eligible'] = false;
            $eligibility['reasons'][] = 'No medical clearance';
        }

        if ($this->medical_clearance_expiry && $this->medical_clearance_expiry < now()) {
            $eligibility['eligible'] = false;
            $eligibility['reasons'][] = 'Medical clearance expired';
        }

        return $eligibility;
    }

    public function updateComplianceStatus(): void
    {
        $compliance = 'compliant';
        $issues = [];

        // Check passport expiry
        if ($this->isExpired()) {
            $compliance = 'non_compliant';
            $issues[] = 'Passport expired';
        }

        // Check medical clearance
        if (!$this->medical_clearance) {
            $compliance = 'non_compliant';
            $issues[] = 'Missing medical clearance';
        }

        // Check fitness certificate
        if (!$this->fitness_certificate) {
            $compliance = 'non_compliant';
            $issues[] = 'Missing fitness certificate';
        }

        // Check disciplinary record
        if (!empty($this->suspensions)) {
            $activeSuspensions = array_filter($this->suspensions, function ($suspension) {
                return isset($suspension['end_date']) && 
                       \Carbon\Carbon::parse($suspension['end_date'])->isFuture();
            });

            if (!empty($activeSuspensions)) {
                $compliance = 'non_compliant';
                $issues[] = 'Active suspension';
            }
        }

        $this->update([
            'compliance_status' => $compliance,
            'audit_results' => [
                'last_check' => now()->toISOString(),
                'status' => $compliance,
                'issues' => $issues
            ]
        ]);
    }

    public function addTransferRecord(array $transferData): void
    {
        $history = $this->transfer_history ?? [];
        $history[] = array_merge($transferData, [
            'recorded_at' => now()->toISOString(),
            'passport_version' => $this->version
        ]);

        $this->update(['transfer_history' => $history]);
    }

    public function addDisciplinaryRecord(array $record): void
    {
        $records = $this->disciplinary_record ?? [];
        $records[] = array_merge($record, [
            'recorded_at' => now()->toISOString(),
            'passport_version' => $this->version
        ]);

        $this->update(['disciplinary_record' => $records]);
    }
} 