<?php

namespace App\Services\Modules;

use App\Models\User;
use App\Services\Dtn\DtnAccess;
use App\Services\RBACService;
use Illuminate\Support\Collection;

/**
 * Catalogue des modules (config/fit_modules.php) et règles de visibilité par
 * compte, partagés par /modules et par le tableau de bord général.
 */
final class ModuleCatalog
{
    public const MEDICAL_ROLES = ['system_admin', 'association_medical', 'club_medical', 'doctor', 'team_doctor', 'medical_staff'];

    /** Les quatre sections métier de /modules, dans leur ordre d'affichage. */
    public const SECTIONS = [
        'clinique' => 'La clinique',
        'performance' => 'Le centre de performance',
        'selections' => 'Les sélections nationales',
        'administration' => 'L\'administration',
    ];

    public function __construct(private readonly DtnAccess $dtn, private readonly RBACService $rbac)
    {
    }

    public function all(): array
    {
        return config('fit_modules.modules', []);
    }

    /** Modules visibles par ce compte. */
    public function visibleFor(?User $user): Collection
    {
        return collect($this->all())->filter(fn (array $module) => $this->isVisible($module, $user))->values();
    }

    /**
     * La clinique pour les rôles médicaux (plus le secrétariat), chaque espace des
     * sélections pour les comptes qui ont sa permission, la saisie FIT selon sa permission RBAC.
     * Mêmes règles que $moduleVisible dans modules/index.blade.php : à garder alignées.
     */
    public function isVisible(array $module, ?User $user): bool
    {
        if (($module['category'] ?? null) === 'clinique') {
            return $this->isMedical($user) || (($module['route'] ?? null) === 'secretary.dashboard' && $user?->role === 'secretary');
        }
        if (isset($module['audience'])) {
            $licensing = app(\App\Services\Licensing\LicenseWorkflow::class);

            return $user !== null && ($module['audience'] === 'federation' ? $licensing->canApprove($user) : $licensing->canRequest($user));
        }
        if (($module['route'] ?? null) === 'performances.fit-metrics') {
            return $user !== null && $this->rbac->userHasPermission($user, 'record-performance-metrics');
        }

        return match ($module['group'] ?? null) {
            'dtn' => $user !== null && $this->dtn->isDtnSide($user),
            'club' => $user !== null && $this->dtn->isClubSide($user),
            default => true,
        };
    }

    public function isMedical(?User $user): bool
    {
        return $user !== null && $user->hasAnyRole(self::MEDICAL_ROLES);
    }
}
