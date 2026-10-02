<?php

namespace App\Services\Activity;

use App\Models\PlatformActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Enregistre les actions des utilisateurs dans platform_activities. Ne bloque
 * jamais l'action elle-même : une erreur d'écriture est seulement journalisée.
 */
final class ActivityRecorder
{
    private static ?bool $ready = null;

    /** Branche les modèles de config/platform_activity.php (création et changement de statut). */
    public static function observeConfiguredModels(): void
    {
        foreach (config('platform_activity.models', []) as $class => $spec) {
            if (!class_exists($class)) {
                continue;
            }
            $class::created(fn (Model $model) => app(self::class)->record($spec['domain'], $spec['created'], $model));
            if (isset($spec['status_field'], $spec['status'])) {
                $class::updated(function (Model $model) use ($spec) {
                    if ($model->wasChanged($spec['status_field'])) {
                        app(self::class)->record($spec['domain'], $spec['status'], $model);
                    }
                });
            }
        }
    }

    /** Une action de l'utilisateur connecté ; sans utilisateur connecté, rien n'est écrit. */
    public function record(string $domain, string $action, ?Model $subject = null, ?int $clubId = null): void
    {
        $user = Auth::user();
        if (!$user || !$this->ready()) {
            return;
        }
        try {
            $attr = fn (string $key) => $subject?->getAttribute($key);
            PlatformActivity::query()->create([
                'user_id' => $user->id,
                'domain' => $domain,
                'action' => $action,
                'subject_type' => $subject ? class_basename($subject) : null,
                'subject_id' => $subject?->getKey(),
                'club_id' => $clubId ?? $attr('club_id') ?? $attr('club_origin_id') ?? $user->club_id,
                'association_id' => $attr('association_id') ?? $user->association_id,
            ]);
        } catch (\Throwable $e) {
            Log::warning('platform_activities : action non journalisée', ['action' => $action, 'error' => $e->getMessage()]);
        }
    }

    private function ready(): bool
    {
        return self::$ready ??= Schema::hasTable('platform_activities');
    }
}
