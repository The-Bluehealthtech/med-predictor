<?php

namespace App\Services\RoleEvaluationImport;

use Exception;

/**
 * Exception technique utilisée uniquement pour faire annuler (ROLLBACK) la
 * transaction DB d'un import lancé en --dry-run, y compris la ligne
 * import_batches créée pour porter le rapport. N'est jamais une vraie
 * erreur : la commande l'attrape et affiche le rapport normalement.
 */
class DryRunRollback extends Exception
{
    public function __construct(public readonly int $attemptedBatchId)
    {
        parent::__construct('dry-run rollback');
    }
}
