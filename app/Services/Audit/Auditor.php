<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Point d'entrée unique de l'audit trail (table audit_logs, en ajout seul) :
 * qui, quoi, quand, d'où. N'interrompt jamais l'action auditée : une erreur
 * d'écriture est seulement signalée dans les journaux applicatifs.
 */
final class Auditor
{
    private static ?bool $ready = null;

    /**
     * @param  array{event_type:string, action:string, description?:string, module?:string, severity?:string,
     *               model?:?Model, model_type?:?string, model_id?:?int, old_values?:?array, new_values?:?array,
     *               changes?:?array, metadata?:?array}  $entry
     */
    public function record(array $entry): ?AuditLog
    {
        if (!$this->ready()) {
            return null;
        }
        try {
            $user = Auth::user();
            $model = $entry['model'] ?? null;
            $request = app()->runningInConsole() && !app()->runningUnitTests() ? null : request();

            return AuditLog::query()->create([
                'event_type' => $entry['event_type'],
                'action' => mb_substr($entry['action'], 0, 255),
                'description' => isset($entry['description']) ? mb_substr($entry['description'], 0, 255) : null,
                'module' => $entry['module'] ?? null,
                'severity' => $entry['severity'] ?? 'info',
                // model_type est obligatoire en base : à défaut d'objet, le module ou le type d'événement.
                'model_type' => $model ? $model::class : ($entry['model_type'] ?? $entry['module'] ?? $entry['event_type']),
                'model_id' => $model?->getKey() ?? ($entry['model_id'] ?? null),
                'model_name' => $model && empty($entry['sensitive']) ? $this->modelName($model) : null,
                'old_values' => $entry['old_values'] ?? null,
                'new_values' => $entry['new_values'] ?? null,
                'changes' => $entry['changes'] ?? null,
                'metadata' => $entry['metadata'] ?? null,
                'user_id' => $user?->getAuthIdentifier(),
                'user_name' => $user?->name,
                'user_email' => $user?->email,
                'tenant_id' => $user?->tenant_id,
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? mb_substr((string) $request->userAgent(), 0, 255) : 'console',
                'url' => $request ? mb_substr($request->fullUrl(), 0, 255) : null,
                'method' => $request?->method() ?? 'CLI',
            ]);
        } catch (\Throwable $e) {
            Log::warning('audit trail : événement non enregistré', ['action' => $entry['action'] ?? null, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /** Création, modification ou suppression d'un enregistrement suivi (config/audit.php). */
    public function modelEvent(string $action, Model $model, array $spec): void
    {
        if (!Auth::check()) {
            return; // imports en ligne de commande et jeux de démonstration : hors audit utilisateur
        }
        $sensitive = (bool) ($spec['sensitive'] ?? false);
        $redacted = array_merge(config('audit.redacted_fields', []), $model->getHidden());
        $ignored = array_merge($redacted, [$model->getCreatedAtColumn(), $model->getUpdatedAtColumn()]);

        $fields = match ($action) {
            'updated' => array_keys(array_diff_key($model->getChanges(), array_flip([$model->getUpdatedAtColumn()]))),
            default => array_keys($model->getAttributes()),
        };
        if ($action === 'updated' && array_diff($fields, config('audit.noise_fields', [])) === []) {
            return; // jeton de session, date de dernière connexion : bruit technique
        }
        $kept = array_values(array_diff($fields, $ignored));
        $old = $new = null;
        if (!$sensitive) {
            $original = $model->getOriginal();
            $values = fn (array $source) => collect($kept)->mapWithKeys(fn ($f) => [$f => $this->scalar($source[$f] ?? null)])->all();
            $old = $action === 'created' ? null : $values($action === 'updated' ? array_intersect_key($original, array_flip($kept)) : $model->getAttributes());
            $new = $action === 'deleted' ? null : $values($model->getAttributes());
        }

        $label = ['created' => 'création', 'updated' => 'modification', 'deleted' => 'suppression'][$action];
        $this->record([
            'event_type' => 'model_change',
            'action' => $action,
            'description' => ucfirst($label) . ' : ' . class_basename($model) . ' #' . $model->getKey(),
            'module' => $spec['module'] ?? null,
            'model' => $model,
            'sensitive' => $sensitive,
            'old_values' => $old,
            'new_values' => $new,
            // Noms des champs concernés (y compris mot de passe ou champs médicaux, sans leur valeur).
            'changes' => ['fields' => array_values(array_diff($fields, [$model->getCreatedAtColumn(), $model->getUpdatedAtColumn()])), 'sensitive' => $sensitive],
        ]);
    }

    /** Branche les modèles de config/audit.php. */
    public static function observeConfiguredModels(): void
    {
        foreach (config('audit.models', []) as $class => $spec) {
            if (!class_exists($class)) {
                continue;
            }
            foreach (['created', 'updated', 'deleted'] as $event) {
                $class::$event(fn (Model $model) => app(self::class)->modelEvent($event, $model, $spec));
            }
        }
    }

    private function modelName(Model $model): ?string
    {
        foreach (['name', 'title', 'label', 'license_number'] as $attribute) {
            $value = $model->getAttribute($attribute);
            if (is_string($value) && $value !== '') {
                return mb_substr($value, 0, 255);
            }
        }

        return null;
    }

    private function scalar(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        return is_string($value) && mb_strlen($value) > 500 ? mb_substr($value, 0, 500) . '…' : $value;
    }

    private function ready(): bool
    {
        return self::$ready ??= Schema::hasTable('audit_logs');
    }
}
