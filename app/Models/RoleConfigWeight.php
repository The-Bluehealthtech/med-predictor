<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Poids d'une dimension pour une famille de poste, au sein d'une version de
 * configuration (Livrable 1, §5). `position_family` référence
 * position_catalog.family en clé logique (string), jamais une clé
 * étrangère stricte : plusieurs codes de position_catalog partagent la
 * même famille.
 */
class RoleConfigWeight extends Model
{
    protected $table = 'role_config_weights';

    protected $fillable = [
        'role_config_version_id', 'position_family', 'dimension_key', 'weight',
    ];

    protected $casts = [
        'weight' => 'decimal:4',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(RoleConfigVersion::class, 'role_config_version_id');
    }
}
