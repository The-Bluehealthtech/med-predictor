<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Référentiel des postes détaillés (Livrable 1, §9). Clé primaire non
 * auto-incrémentée (le code, ex. "LCB"). Modèle volontairement mince :
 * aucune relation Eloquent stricte vers role_config_weights ni
 * player_role_evaluations, qui référencent `family` en clé logique (une
 * famille regroupe plusieurs codes, ce n'est pas la clé primaire d'ici).
 */
class PositionCatalog extends Model
{
    protected $table = 'position_catalog';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code', 'label_fr', 'label_en', 'family', 'broad_group', 'display_order',
    ];
}
