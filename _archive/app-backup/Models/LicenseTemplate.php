<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'required_fields',
        'optional_fields',
        'validation_rules',
        'validity_period_months',
        'fee',
        'requires_medical_check',
        'requires_documents',
        'document_requirements',
        'is_active'
    ];

    protected $casts = [
        'required_fields' => 'array',
        'optional_fields' => 'array',
        'validation_rules' => 'array',
        'document_requirements' => 'array',
        'requires_medical_check' => 'boolean',
        'requires_documents' => 'boolean',
        'is_active' => 'boolean',
        'fee' => 'decimal:2'
    ];

    public function licenses()
    {
        return $this->hasMany(PlayerLicense::class, 'template_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function getRequiredFieldsListAttribute()
    {
        return $this->required_fields ?? [];
    }

    public function getOptionalFieldsListAttribute()
    {
        return $this->optional_fields ?? [];
    }

    public function getDocumentRequirementsListAttribute()
    {
        return $this->document_requirements ?? [];
    }

    public function isFieldRequired($fieldName)
    {
        return in_array($fieldName, $this->required_fields ?? []);
    }

    public function isFieldOptional($fieldName)
    {
        return in_array($fieldName, $this->optional_fields ?? []);
    }

    public function getValidationRulesForField($fieldName)
    {
        return $this->validation_rules[$fieldName] ?? [];
    }

    public function requiresDocument($documentType)
    {
        return in_array($documentType, $this->document_requirements ?? []);
    }
}
