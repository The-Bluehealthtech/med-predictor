<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FifaSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'request_payload',
        'response_payload',
        'status_code',
        'error_message',
        'operation_type',
        'fifa_endpoint',
        'response_time_ms',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'status_code' => 'integer',
        'response_time_ms' => 'integer',
    ];

    /**
     * Get the class name for polymorphic relations.
     */
    public function getMorphClass()
    {
        $map = [
            'player' => Player::class,
            'club' => Club::class,
            'association' => Association::class,
        ];

        return $map[$this->entity_type] ?? $this->entity_type;
    }

    /**
     * Relation polymorphique vers l'entité synchronisée
     */
    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * Scope pour filtrer par type d'entité
     */
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)
                    ->where('entity_id', $entityId);
    }

    /**
     * Scope pour filtrer par statut de réponse
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status_code', '>=', 200)
                    ->where('status_code', '<', 300);
    }

    /**
     * Scope pour filtrer les erreurs
     */
    public function scopeErrors($query)
    {
        return $query->where('status_code', '>=', 400);
    }

    /**
     * Scope pour filtrer par type d'opération
     */
    public function scopeOperationType($query, $type)
    {
        return $query->where('operation_type', $type);
    }

    /**
     * Obtenir les logs récents pour une entité
     */
    public static function getRecentLogs($entityType, $entityId, $limit = 10)
    {
        return static::forEntity($entityType, $entityId)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtenir le dernier log réussi pour une entité
     */
    public static function getLastSuccessfulLog($entityType, $entityId)
    {
        return static::forEntity($entityType, $entityId)
                    ->successful()
                    ->orderBy('created_at', 'desc')
                    ->first();
    }

    /**
     * Obtenir le dernier log d'erreur pour une entité
     */
    public static function getLastErrorLog($entityType, $entityId)
    {
        return static::forEntity($entityType, $entityId)
                    ->errors()
                    ->orderBy('created_at', 'desc')
                    ->first();
    }
}
