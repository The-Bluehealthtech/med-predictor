<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Version de configuration des poids par famille de poste et dimension
 * (Livrable 1, §5). Une version publiée (`status = 'published'`) est
 * immuable en usage normal ; ce modèle n'impose pas cette règle en base
 * (aucune contrainte SQL), elle reste une discipline applicative à
 * respecter dans le code qui écrit des role_config_weights.
 */
class RoleConfigVersion extends Model
{
    protected $table = 'role_config_versions';

    protected $fillable = [
        'label', 'description', 'status', 'published_at', 'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function weights(): HasMany
    {
        return $this->hasMany(RoleConfigWeight::class, 'role_config_version_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(PlayerRoleEvaluation::class, 'role_config_version_id');
    }
}
