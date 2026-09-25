<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'birth_date',
        'gender',
        'phone',
        'email',
        'address',
        'medical_history',
        'allergies',
        'medications',
    ];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Relation avec les enregistrements dentaires.
     */
    public function dentalRecords(): HasMany
    {
        return $this->hasMany(DentalRecord::class);
    }

    /**
     * Obtenir l'âge du patient.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }

        return $this->birth_date->diffInYears(now());
    }

    /**
     * Obtenir le nom complet avec l'âge.
     */
    public function getFullNameWithAgeAttribute(): string
    {
        $name = $this->name;
        if ($this->age) {
            $name .= " ({$this->age} ans)";
        }
        return $name;
    }

    /**
     * Scope pour filtrer par nom.
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    /**
     * Scope pour filtrer par genre.
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }
}
