<?php

namespace App\Http\Controllers\Api\V1\Selections;

use App\Http\Controllers\Controller;
use App\Services\Dtn\ApiAbilities;
use App\Services\Dtn\DtnAccess;
use App\Services\Dtn\SelectionWorkflow;
use Illuminate\Http\Request;

/**
 * Socle des API des sélections nationales : chaque appel exige le droit du
 * jeton ET la permission RBAC de l'espace pour son propriétaire. La partie
 * médicale exige en plus le droit selections:medical et un rôle médical.
 */
abstract class SelectionApiController extends Controller
{
    public function __construct(protected readonly DtnAccess $access, protected readonly SelectionWorkflow $workflow)
    {
    }

    protected function requireAbility(Request $request, string $ability): void
    {
        abort_unless($request->user()?->tokenCan($ability), 403, "Ce jeton n'a pas le droit « {$ability} ».");
    }

    protected function requirePermission(Request $request, string $permission): void
    {
        $allowed = $permission === DtnAccess::FEDERATION_PERMISSION
            ? $this->access->isDtnSide($request->user())
            : $this->access->isClubSide($request->user());
        abort_unless($allowed, 403, "Le compte de ce jeton n'a pas la permission « {$permission} ».");
    }

    /** Partie médicale demandée explicitement (?include_medical=1) et autorisée par le jeton. */
    protected function wantsMedical(Request $request): bool
    {
        return $request->boolean('include_medical') && $request->user()->tokenCan(ApiAbilities::MEDICAL);
    }

    protected function medicalWriteAllowed(Request $request): bool
    {
        return $request->user()->tokenCan(ApiAbilities::MEDICAL);
    }
}
