<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'requirements',
        'validation_rules',
        'approval_process',
        'validity_period_months',
        'fee_amount',
        'fee_currency',
        'requires_medical_clearance',
        'requires_fitness_certificate',
        'requires_contract',
        'requires_work_permit',
        'requires_international_clearance',
        'age_restrictions',
        'position_restrictions',
        'experience_requirements',
        'is_active',
        'requires_association_approval',
        'requires_club_approval',
        'max_players_per_club',
        'max_players_total',
        'document_templates',
        'notification_settings',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'requirements' => 'array',
        'validation_rules' => 'array',
        'approval_process' => 'array',
        'fee_amount' => 'decimal:2',
        'requires_medical_clearance' => 'boolean',
        'requires_fitness_certificate' => 'boolean',
        'requires_contract' => 'boolean',
        'requires_work_permit' => 'boolean',
        'requires_international_clearance' => 'boolean',
        'age_restrictions' => 'array',
        'position_restrictions' => 'array',
        'experience_requirements' => 'array',
        'is_active' => 'boolean',
        'requires_association_approval' => 'boolean',
        'requires_club_approval' => 'boolean',
        'document_templates' => 'array',
        'notification_settings' => 'array',
    ];

    // Relationships
    public function playerLicenses(): HasMany
    {
        return $this->hasMany(PlayerLicense::class, 'license_type', 'code');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // Methods
    public function validateRequirements(PlayerLicense $license): array
    {
        $errors = [];
        $requirements = $this->requirements ?? [];

        foreach ($requirements as $requirement => $config) {
            $required = $config['required'] ?? false;
            $field = $config['field'] ?? $requirement;

            if ($required && empty($license->$field)) {
                $errors[] = $config['message'] ?? "{$requirement} is required";
            }
        }

        return $errors;
    }

    public function validateAgeRestrictions(Player $player): array
    {
        $errors = [];
        $ageRestrictions = $this->age_restrictions ?? [];
        
        if (!empty($ageRestrictions)) {
            $playerAge = $player->age;
            $minAge = $ageRestrictions['min_age'] ?? null;
            $maxAge = $ageRestrictions['max_age'] ?? null;

            if ($minAge && $playerAge < $minAge) {
                $errors[] = "Player must be at least {$minAge} years old for this license type";
            }

            if ($maxAge && $playerAge > $maxAge) {
                $errors[] = "Player must be no more than {$maxAge} years old for this license type";
            }
        }

        return $errors;
    }

    public function validatePositionRestrictions(Player $player): array
    {
        $errors = [];
        $positionRestrictions = $this->position_restrictions ?? [];
        
        if (!empty($positionRestrictions)) {
            $allowedPositions = $positionRestrictions['allowed_positions'] ?? [];
            $excludedPositions = $positionRestrictions['excluded_positions'] ?? [];

            if (!empty($allowedPositions) && !in_array($player->position, $allowedPositions)) {
                $errors[] = "Position {$player->position} is not allowed for this license type";
            }

            if (!empty($excludedPositions) && in_array($player->position, $excludedPositions)) {
                $errors[] = "Position {$player->position} is excluded for this license type";
            }
        }

        return $errors;
    }

    public function getApprovalSteps(): array
    {
        return $this->approval_process ?? [];
    }

    public function requiresDocument(string $documentType): bool
    {
        $documentMap = [
            'medical_clearance' => 'requires_medical_clearance',
            'fitness_certificate' => 'requires_fitness_certificate',
            'contract' => 'requires_contract',
            'work_permit' => 'requires_work_permit',
            'international_clearance' => 'requires_international_clearance',
        ];

        $field = $documentMap[$documentType] ?? null;
        return $field ? $this->$field : false;
    }

    public function getFeeFormatted(): string
    {
        return number_format($this->fee_amount, 2) . ' ' . $this->fee_currency;
    }

    public function getValidityPeriodFormatted(): string
    {
        $months = $this->validity_period_months;
        if ($months == 1) {
            return '1 month';
        } elseif ($months < 12) {
            return "{$months} months";
        } else {
            $years = floor($months / 12);
            $remainingMonths = $months % 12;
            if ($remainingMonths == 0) {
                return $years == 1 ? '1 year' : "{$years} years";
            } else {
                return "{$years} year" . ($years > 1 ? 's' : '') . " {$remainingMonths} month" . ($remainingMonths > 1 ? 's' : '');
            }
        }
    }

    public function canBeIssuedToPlayer(Player $player): array
    {
        $errors = [];
        
        // Check age restrictions
        $errors = array_merge($errors, $this->validateAgeRestrictions($player));
        
        // Check position restrictions
        $errors = array_merge($errors, $this->validatePositionRestrictions($player));
        
        // Check experience requirements
        $experienceErrors = $this->validateExperienceRequirements($player);
        $errors = array_merge($errors, $experienceErrors);

        return $errors;
    }

    private function validateExperienceRequirements(Player $player): array
    {
        $errors = [];
        $experienceRequirements = $this->experience_requirements ?? [];
        
        if (!empty($experienceRequirements)) {
            $minYears = $experienceRequirements['min_years'] ?? null;
            $maxYears = $experienceRequirements['max_years'] ?? null;
            
            // Calculate player experience (this would need to be implemented based on your data model)
            $playerExperience = $this->calculatePlayerExperience($player);
            
            if ($minYears && $playerExperience < $minYears) {
                $errors[] = "Player must have at least {$minYears} years of experience for this license type";
            }
            
            if ($maxYears && $playerExperience > $maxYears) {
                $errors[] = "Player must have no more than {$maxYears} years of experience for this license type";
            }
        }

        return $errors;
    }

    private function calculatePlayerExperience(Player $player): int
    {
        // This is a placeholder - implement based on your actual data model
        // Could be based on contract start dates, registration dates, etc.
        return 0;
    }

    public function getStatusColor(): string
    {
        return $this->is_active ? 'green' : 'gray';
    }

    public function getStatusText(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
} 