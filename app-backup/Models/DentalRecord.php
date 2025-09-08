<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DentalRecord extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'user_id',
        'dental_data',
        'notes',
        'status',
        'examined_at',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'dental_data' => 'array',
        'examined_at' => 'datetime',
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Relation avec le patient.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation avec l'utilisateur (médecin).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour filtrer par statut.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour filtrer par patient.
     */
    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Méthode pour obtenir les données dentaires formatées.
     */
    public function getFormattedDentalData(): array
    {
        $dentalData = $this->dental_data ?? [];
        
        // Structure par défaut pour les 32 dents (notation FDI)
        $defaultTeeth = [];
        for ($quadrant = 1; $quadrant <= 4; $quadrant++) {
            for ($tooth = 1; $tooth <= 8; $tooth++) {
                $toothNumber = $quadrant . $tooth;
                $defaultTeeth[$toothNumber] = [
                    'status' => 'healthy', // sain par défaut
                    'notes' => '',
                    'color' => '#4ade80', // vert pour sain
                    'last_updated' => null,
                ];
            }
        }

        return array_merge($defaultTeeth, $dentalData);
    }

    /**
     * Méthode pour mettre à jour l'état d'une dent.
     */
    public function updateToothStatus(string $toothNumber, string $status, string $notes = ''): void
    {
        $dentalData = $this->dental_data ?? [];
        
        $colors = [
            'healthy' => '#4ade80', // vert
            'cavity' => '#ef4444', // rouge
            'crown' => '#fbbf24', // jaune
            'extracted' => '#6b7280', // gris
            'treatment' => '#3b82f6', // bleu
        ];

        $dentalData[$toothNumber] = [
            'status' => $status,
            'notes' => $notes,
            'color' => $colors[$status] ?? '#4ade80',
            'last_updated' => now()->toISOString(),
        ];

        $this->update(['dental_data' => $dentalData]);
    }

    /**
     * Méthode pour obtenir le nombre de dents par statut.
     */
    public function getTeethCountByStatus(): array
    {
        $dentalData = $this->getFormattedDentalData();
        $counts = [
            'healthy' => 0,
            'cavity' => 0,
            'crown' => 0,
            'extracted' => 0,
            'treatment' => 0,
        ];

        foreach ($dentalData as $tooth) {
            $status = $tooth['status'] ?? 'healthy';
            $counts[$status]++;
        }

        return $counts;
    }
}
