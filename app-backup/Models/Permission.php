<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
        'action',
        'resource'
    ];

    /**
     * Relation many-to-many avec les rôles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Relation many-to-many avec les utilisateurs
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions');
    }

    /**
     * Vérifier si la permission existe
     */
    public static function exists($slug): bool
    {
        return static::where('slug', $slug)->exists();
    }

    /**
     * Créer une permission
     */
    public static function createPermission($name, $slug, $description = null, $module = null, $action = null, $resource = null)
    {
        return static::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'module' => $module,
            'action' => $action,
            'resource' => $resource
        ]);
    }

    /**
     * Obtenir les permissions par module
     */
    public static function getByModule($module)
    {
        return static::where('module', $module)->get();
    }

    /**
     * Obtenir les permissions par action
     */
    public static function getByAction($action)
    {
        return static::where('action', $action)->get();
    }
}


