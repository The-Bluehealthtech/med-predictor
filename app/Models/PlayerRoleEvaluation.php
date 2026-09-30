<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Résultat calculé (Livrable 1, §6) : score, fiabilité, intervalle,
 * adéquation au rôle, pour un joueur, une famille de poste évaluée, une
 * version de configuration et une version de modèle données. Une ligne par
 * famille comparée (décision validée du 30/09, question 6) : pour un
 * joueur/période donnés, une ligne où position_family_evaluated = la
 * famille réellement jouée (score = performance réelle), et une ligne par
 * famille voisine comparée (role_fit_score seul, ou score+role_fit_score
 * selon ce que RoleFitEvaluator calcule — voir ce service).
 *
 * Écrit uniquement par du code dérivé (RoleFitEvaluator / une commande de
 * calcul), jamais par un import direct : pas de import_batch_id propre,
 * seulement source_import_batch_id/source_demo_batch_id qui tracent la
 * provenance des données d'ENTRÉE.
 */
class PlayerRoleEvaluation extends Model
{
    protected $table = 'player_role_evaluations';

    protected $fillable = [
        'player_id', 'match_id', 'period_start', 'period_end',
        'position_family_evaluated', 'role_config_version_id', 'model_version',
        'score', 'reliability', 'interval_low', 'interval_high', 'role_fit_score',
        'is_demo', 'source_import_batch_id', 'source_demo_batch_id', 'computed_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'score' => 'decimal:2',
        'reliability' => 'decimal:4',
        'interval_low' => 'decimal:2',
        'interval_high' => 'decimal:2',
        'role_fit_score' => 'decimal:2',
        'is_demo' => 'boolean',
        'computed_at' => 'datetime',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
    }

    public function configVersion(): BelongsTo
    {
        return $this->belongsTo(RoleConfigVersion::class, 'role_config_version_id');
    }
}
