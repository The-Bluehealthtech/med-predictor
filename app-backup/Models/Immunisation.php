<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Immunisation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'athlete_id',
        'vaccine_code',
        'vaccine_name',
        'date_administered',
        'fhir_id',
        'lot_number',
        'manufacturer',
        'expiration_date',
        'dose_number',
        'total_doses',
        'route',
        'site',
        'status',
        'notes',
        'administered_by',
        'verified_by',
        'verification_date',
        'source', // 'manual', 'fhir_sync', 'import'
        'last_synced_at',
        'sync_status', // 'pending', 'synced', 'failed'
        'sync_error'
    ];

    protected $casts = [
        'date_administered' => 'datetime',
        'expiration_date' => 'datetime',
        'verification_date' => 'datetime',
        'last_synced_at' => 'datetime',
        'dose_number' => 'integer',
        'total_doses' => 'integer'
    ];

    /**
     * Get the athlete that owns the immunisation record
     */
    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the user who administered the vaccine
     */
    public function administeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    /**
     * Get the user who verified the record
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope for active immunisations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for expired immunisations
     */
    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now());
    }

    /**
     * Scope for expiring soon immunisations (within 30 days)
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('expiration_date', '>', now())
                    ->where('expiration_date', '<', now()->addDays(30));
    }

    /**
     * Scope for pending sync
     */
    public function scopePendingSync($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Scope for failed sync
     */
    public function scopeFailedSync($query)
    {
        return $query->where('sync_status', 'failed');
    }

    /**
     * Check if the immunisation is expired
     */
    public function isExpired(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    /**
     * Check if the immunisation is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->expiration_date && 
               $this->expiration_date->isFuture() && 
               $this->expiration_date->diffInDays(now()) <= 30;
    }

    /**
     * Check if the immunisation is complete (all doses administered)
     */
    public function isComplete(): bool
    {
        return $this->dose_number >= $this->total_doses;
    }

    /**
     * Get the next dose number
     */
    public function getNextDoseNumber(): int
    {
        return $this->dose_number + 1;
    }

    /**
     * Get the progress percentage for multi-dose vaccines
     */
    public function getProgressPercentage(): float
    {
        if ($this->total_doses <= 1) {
            return 100.0;
        }
        
        return round(($this->dose_number / $this->total_doses) * 100, 1);
    }

    /**
     * Get the status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'active' => 'green',
            'expired' => 'red',
            'pending' => 'yellow',
            'incomplete' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get the sync status badge color
     */
    public function getSyncStatusColor(): string
    {
        return match($this->sync_status) {
            'synced' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            default => 'gray'
        };
    }

    /**
     * Convert to FHIR Immunization resource
     */
    public function toFhirResource(): array
    {
        return [
            'resourceType' => 'Immunization',
            'id' => $this->fhir_id,
            'status' => $this->status === 'active' ? 'completed' : $this->status,
            'vaccineCode' => [
                'coding' => [
                    [
                        'system' => 'http://hl7.org/fhir/sid/cvx',
                        'code' => $this->vaccine_code,
                        'display' => $this->vaccine_name
                    ]
                ],
                'text' => $this->vaccine_name
            ],
            'patient' => [
                'reference' => "Patient/{$this->athlete->fhir_id}",
                'display' => $this->athlete->full_name
            ],
            'occurrenceDateTime' => $this->date_administered->toISOString(),
            'recorded' => $this->created_at->toISOString(),
            'lotNumber' => $this->lot_number,
            'expirationDate' => $this->expiration_date?->toDateString(),
            'site' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActSite',
                        'code' => $this->site ?? 'LA',
                        'display' => $this->getSiteDisplay()
                    ]
                ]
            ],
            'route' => [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/v3-RouteOfAdministration',
                        'code' => $this->route ?? 'IM',
                        'display' => $this->getRouteDisplay()
                    ]
                ]
            ],
            'doseQuantity' => [
                'value' => 1,
                'unit' => 'dose',
                'system' => 'http://unitsofmeasure.org',
                'code' => 'dose'
            ],
            'performer' => [
                [
                    'actor' => [
                        'reference' => "Practitioner/{$this->administered_by}",
                        'display' => $this->administeredBy?->name ?? 'Unknown'
                    ]
                ]
            ],
            'note' => $this->notes ? [
                [
                    'text' => $this->notes
                ]
            ] : null
        ];
    }

    /**
     * Get site display name
     */
    private function getSiteDisplay(): string
    {
        return match($this->site) {
            'LA' => 'Left arm',
            'RA' => 'Right arm',
            'LD' => 'Left deltoid',
            'RD' => 'Right deltoid',
            'LG' => 'Left gluteus medius',
            'RG' => 'Right gluteus medius',
            'LVL' => 'Left vastus lateralis',
            'RVL' => 'Right vastus lateralis',
            default => 'Left arm'
        };
    }

    /**
     * Get route display name
     */
    private function getRouteDisplay(): string
    {
        return match($this->route) {
            'IM' => 'Intramuscular',
            'SC' => 'Subcutaneous',
            'ID' => 'Intradermal',
            'IN' => 'Intranasal',
            'PO' => 'Oral',
            default => 'Intramuscular'
        };
    }
} 