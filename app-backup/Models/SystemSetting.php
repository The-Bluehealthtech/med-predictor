<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'value',
        'type',
        'group',
        'is_public',
        'is_editable',
        'is_required',
        'options',
        'validation_rules',
        'default_value',
        'updated_by'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_editable' => 'boolean',
        'is_required' => 'boolean',
        'options' => 'array'
    ];

    // Relationships
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    // Static methods for getting/setting values
    public static function get($key, $default = null)
    {
        $cacheKey = "system_setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return static::castValue($setting->value, $setting->type);
        });
    }

    public static function set($key, $value, $userId = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            throw new \Exception("Setting '{$key}' not found");
        }
        
        if (!$setting->is_editable) {
            throw new \Exception("Setting '{$key}' is not editable");
        }
        
        $setting->update([
            'value' => $value,
            'updated_by' => $userId
        ]);
        
        // Clear cache
        Cache::forget("system_setting_{$key}");
        
        return $setting;
    }

    public static function getGroup($group)
    {
        $cacheKey = "system_settings_group_{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            return static::where('group', $group)
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key => static::castValue($setting->value, $setting->type)];
                });
        });
    }

    public static function getAllPublic()
    {
        $cacheKey = "system_settings_public";
        
        return Cache::remember($cacheKey, 3600, function () {
            return static::public()
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->key => static::castValue($setting->value, $setting->type)];
                });
        });
    }

    // Helper methods
    public static function castValue($value, $type)
    {
        if ($value === null) {
            return null;
        }
        
        return match($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            'array' => json_decode($value, true),
            default => $value
        };
    }

    public function getFormattedValueAttribute()
    {
        return static::castValue($this->value, $this->type);
    }

    public function getFormattedOptionsAttribute()
    {
        if (!$this->options) {
            return [];
        }
        
        return is_array($this->options) ? $this->options : json_decode($this->options, true);
    }

    // Default settings - Dynamically loaded from database or configuration
    public static function getDefaultSettings()
    {
        return [
            // General Settings
            [
                'key' => 'app_name',
                'name' => 'Nom de l\'application',
                'description' => 'Nom affiché de l\'application',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'is_required' => true,
                'default_value' => 'FIT Platform'
            ],
            [
                'key' => 'app_version',
                'name' => 'Version de l\'application',
                'description' => 'Version actuelle de l\'application',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'is_public' => true,
                'is_required' => true,
                'default_value' => '1.0.0'
            ],
            [
                'key' => 'maintenance_mode',
                'name' => 'Mode maintenance',
                'description' => 'Activer le mode maintenance',
                'value' => '',
                'type' => 'boolean',
                'group' => 'general',
                'is_public' => false,
                'is_required' => false,
                'default_value' => '0'
            ],
            
            // Security Settings
            [
                'key' => 'max_login_attempts',
                'name' => 'Tentatives de connexion max',
                'description' => 'Nombre maximum de tentatives de connexion',
                'value' => '',
                'type' => 'integer',
                'group' => 'security',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '5'
            ],
            [
                'key' => 'session_timeout',
                'name' => 'Timeout de session (minutes)',
                'description' => 'Durée avant expiration de la session',
                'value' => '',
                'type' => 'integer',
                'group' => 'security',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '120'
            ],
            [
                'key' => 'password_min_length',
                'name' => 'Longueur minimale du mot de passe',
                'description' => 'Nombre minimum de caractères pour le mot de passe',
                'value' => '',
                'type' => 'integer',
                'group' => 'security',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '8'
            ],
            
            // Email Settings
            [
                'key' => 'mail_from_address',
                'name' => 'Adresse email expéditeur',
                'description' => 'Adresse email utilisée pour l\'envoi d\'emails',
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'is_public' => false,
                'is_required' => true,
                'default_value' => 'noreply@fitplatform.com'
            ],
            [
                'key' => 'mail_from_name',
                'name' => 'Nom expéditeur',
                'description' => 'Nom affiché pour l\'expéditeur d\'emails',
                'value' => '',
                'type' => 'string',
                'group' => 'email',
                'is_public' => false,
                'is_required' => true,
                'default_value' => 'FIT Platform'
            ],
            
            // File Upload Settings
            [
                'key' => 'max_file_size',
                'name' => 'Taille max des fichiers (MB)',
                'description' => 'Taille maximale autorisée pour les fichiers',
                'value' => '',
                'type' => 'integer',
                'group' => 'files',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '10'
            ],
            [
                'key' => 'allowed_file_types',
                'name' => 'Types de fichiers autorisés',
                'description' => 'Types de fichiers autorisés pour l\'upload',
                'value' => '',
                'type' => 'string',
                'group' => 'files',
                'is_public' => false,
                'is_required' => true,
                'default_value' => 'jpg,jpeg,png,pdf,doc,docx'
            ],
            
            // FIFA Connect Settings
            [
                'key' => 'fifa_api_url',
                'name' => 'URL API FIFA',
                'description' => 'URL de base pour l\'API FIFA Connect',
                'value' => '',
                'type' => 'string',
                'group' => 'fifa',
                'is_public' => false,
                'is_required' => false,
                'default_value' => 'https://api.fifa.com/v1'
            ],
            [
                'key' => 'fifa_sync_interval',
                'name' => 'Intervalle de synchronisation FIFA (minutes)',
                'description' => 'Fréquence de synchronisation avec FIFA',
                'value' => '',
                'type' => 'integer',
                'group' => 'fifa',
                'is_public' => false,
                'is_required' => false,
                'default_value' => '60'
            ],

            // Medical Settings
            [
                'key' => 'medical_certificate_validity_days',
                'name' => 'Validité certificat médical (jours)',
                'description' => 'Durée de validité d\'un certificat médical en jours',
                'value' => '',
                'type' => 'integer',
                'group' => 'medical',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '365'
            ],
            [
                'key' => 'medical_exam_required_age',
                'name' => 'Âge requis pour examen médical',
                'description' => 'Âge minimum pour passer un examen médical',
                'value' => '',
                'type' => 'integer',
                'group' => 'medical',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '16'
            ],
            [
                'key' => 'medical_clearance_types',
                'name' => 'Types d\'aptitude médicale',
                'description' => 'Types d\'aptitude médicale autorisés (séparés par virgule)',
                'value' => '',
                'type' => 'string',
                'group' => 'medical',
                'is_public' => false,
                'is_required' => true,
                'default_value' => 'apte,inapte,apte_avec_reserves'
            ],
            [
                'key' => 'medical_document_retention_years',
                'name' => 'Conservation documents médicaux (années)',
                'description' => 'Durée de conservation des documents médicaux',
                'value' => '',
                'type' => 'integer',
                'group' => 'medical',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '5'
            ],
            [
                'key' => 'medical_emergency_contact_required',
                'name' => 'Contact d\'urgence obligatoire',
                'description' => 'Exiger un contact d\'urgence pour tous les joueurs',
                'value' => '',
                'type' => 'boolean',
                'group' => 'medical',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '1'
            ],
            [
                'key' => 'medical_insurance_required',
                'name' => 'Assurance médicale obligatoire',
                'description' => 'Exiger une assurance médicale pour tous les joueurs',
                'value' => '',
                'type' => 'boolean',
                'group' => 'medical',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '1'
            ],

            // Competition Settings
            [
                'key' => 'competition_registration_deadline_days',
                'name' => 'Délai d\'inscription (jours avant)',
                'description' => 'Nombre de jours avant le début pour fermer les inscriptions',
                'value' => '',
                'type' => 'integer',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '7'
            ],
            [
                'key' => 'competition_min_players_per_team',
                'name' => 'Joueurs minimum par équipe',
                'description' => 'Nombre minimum de joueurs requis par équipe',
                'value' => '',
                'type' => 'integer',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '11'
            ],
            [
                'key' => 'competition_max_players_per_team',
                'name' => 'Joueurs maximum par équipe',
                'description' => 'Nombre maximum de joueurs autorisés par équipe',
                'value' => '',
                'type' => 'integer',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '25'
            ],
            [
                'key' => 'competition_match_duration_minutes',
                'name' => 'Durée d\'un match (minutes)',
                'description' => 'Durée standard d\'un match en minutes',
                'value' => '',
                'type' => 'integer',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '90'
            ],
            [
                'key' => 'competition_halftime_duration_minutes',
                'name' => 'Durée de la mi-temps (minutes)',
                'description' => 'Durée de la pause entre les deux mi-temps',
                'value' => '',
                'type' => 'integer',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '15'
            ],
            [
                'key' => 'competition_referee_assignments_auto',
                'name' => 'Assignation automatique des arbitres',
                'description' => 'Activer l\'assignation automatique des arbitres',
                'value' => '',
                'type' => 'boolean',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '0'
            ],
            [
                'key' => 'competition_disciplinary_sanctions_enabled',
                'name' => 'Sanctions disciplinaires activées',
                'description' => 'Activer le système de sanctions disciplinaires',
                'value' => '',
                'type' => 'boolean',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '1'
            ],
            [
                'key' => 'competition_points_win',
                'name' => 'Points pour victoire',
                'description' => 'Nombre de points attribués pour une victoire',
                'value' => '',
                'type' => 'integer',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '3'
            ],
            [
                'key' => 'competition_points_draw',
                'name' => 'Points pour match nul',
                'description' => 'Nombre de points attribués pour un match nul',
                'value' => '',
                'type' => 'integer',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '1'
            ],
            [
                'key' => 'competition_points_loss',
                'name' => 'Points pour défaite',
                'description' => 'Nombre de points attribués pour une défaite',
                'value' => '',
                'type' => 'integer',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => '0'
            ],
            [
                'key' => 'competition_ranking_criteria',
                'name' => 'Critères de classement',
                'description' => 'Critères de classement (séparés par virgule)',
                'value' => '',
                'type' => 'string',
                'group' => 'competition',
                'is_public' => false,
                'is_required' => true,
                'default_value' => 'points,goal_difference,goals_scored,head_to_head'
            ]
        ];
    }

    public static function createDefaultSettings()
    {
        $defaultSettings = static::getDefaultSettings();
        
        foreach ($defaultSettings as $settingData) {
            $existing = static::where('key', $settingData['key'])->first();
            
            if (!$existing) {
                // Si le paramètre n'existe pas, utiliser la valeur par défaut
                $settingData['value'] = $settingData['default_value'];
                static::create($settingData);
            } else {
                // Si le paramètre existe mais n'a pas de valeur, utiliser la valeur par défaut
                if (empty($existing->value) && !empty($settingData['default_value'])) {
                    $existing->update(['value' => $settingData['default_value']]);
                }
            }
        }
    }

    /**
     * Créer un nouveau paramètre dynamiquement
     */
    public static function createSetting($key, $name, $description, $value, $type = 'string', $group = 'general', $options = [])
    {
        $defaultOptions = [
            'is_public' => false,
            'is_editable' => true,
            'is_required' => false,
            'default_value' => $value,
            'validation_rules' => null
        ];

        $options = array_merge($defaultOptions, $options);

        return static::create([
            'key' => $key,
            'name' => $name,
            'description' => $description,
            'value' => $value,
            'type' => $type,
            'group' => $group,
            'is_public' => $options['is_public'],
            'is_editable' => $options['is_editable'],
            'is_required' => $options['is_required'],
            'default_value' => $options['default_value'],
            'validation_rules' => $options['validation_rules']
        ]);
    }

    /**
     * Obtenir les groupes disponibles avec leurs icônes
     */
    public static function getGroupIcons()
    {
        return [
            'general' => '⚙️',
            'security' => '🔒',
            'email' => '📧',
            'files' => '📁',
            'fifa' => '⚽',
            'medical' => '🏥',
            'competition' => '🏆'
        ];
    }

    /**
     * Obtenir les groupes disponibles avec leurs descriptions
     */
    public static function getGroupDescriptions()
    {
        return [
            'general' => 'Paramètres généraux de l\'application',
            'security' => 'Paramètres de sécurité et authentification',
            'email' => 'Configuration des emails et notifications',
            'files' => 'Gestion des fichiers et uploads',
            'fifa' => 'Intégration FIFA Connect',
            'medical' => 'Paramètres médicaux et certificats',
            'competition' => 'Gestion des compétitions et matchs'
        ];
    }
}
