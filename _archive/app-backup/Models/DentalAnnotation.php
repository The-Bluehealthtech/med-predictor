<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalAnnotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'health_record_id',
        'tooth_id',
        'position_x',
        'position_y',
        'status',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'position_x' => 'integer',
        'position_y' => 'integer'
    ];

    /**
     * Relation avec le dossier de santé
     */
    public function healthRecord()
    {
        return $this->belongsTo(HealthRecord::class);
    }

    /**
     * Obtenir le type de dent basé sur l'ID FDI
     */
    public function getToothTypeAttribute()
    {
        $position = (int) substr($this->tooth_id, 1);
        
        if ($position <= 2) return 'Incisive';
        if ($position === 3) return 'Canine';
        if ($position <= 5) return 'Prémolaire';
        return 'Molaire';
    }

    /**
     * Obtenir le quadrant basé sur l'ID FDI
     */
    public function getQuadrantAttribute()
    {
        $quadrant = substr($this->tooth_id, 0, 1);
        $names = [
            '1' => 'Supérieur Droit',
            '2' => 'Supérieur Gauche', 
            '3' => 'Inférieur Gauche',
            '4' => 'Inférieur Droit'
        ];
        return $names[$quadrant] ?? 'Inconnu';
    }

    /**
     * Obtenir la position dans le quadrant
     */
    public function getPositionAttribute()
    {
        return substr($this->tooth_id, 1);
    }

    /**
     * Vérifier si la dent est fixée
     */
    public function isFixed()
    {
        return $this->status === 'fixed';
    }

    /**
     * Vérifier si la dent est sélectionnée
     */
    public function isSelected()
    {
        return $this->status === 'selected';
    }

    /**
     * Obtenir la couleur CSS basée sur le statut
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'normal' => '#3b82f6',
            'selected' => '#1d4ed8',
            'fixed' => '#10b981',
            'problem' => '#ef4444',
            'warning' => '#f59e0b'
        ];
        
        return $colors[$this->status] ?? '#6b7280';
    }
}
