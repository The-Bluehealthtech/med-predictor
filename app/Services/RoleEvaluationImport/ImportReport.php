<?php

namespace App\Services\RoleEvaluationImport;

/**
 * Rapport de qualité d'un lot d'import (exigé par le mandat, Livrable 2) :
 * lignes lues, importées, rejetées avec la raison. Persisté sur
 * import_batches (colonnes rows_read/rows_imported/rows_rejected + report JSON).
 */
class ImportReport
{
    public int $rowsRead = 0;
    public int $rowsImported = 0;
    /** @var array<int,array{row_number:int,reasons:string[]}> */
    public array $rejections = [];

    public function recordRead(): void
    {
        $this->rowsRead++;
    }

    public function recordImported(): void
    {
        $this->rowsImported++;
    }

    /**
     * @param string[] $reasons
     */
    public function recordRejected(int $rowNumber, array $reasons): void
    {
        $this->rejections[] = ['row_number' => $rowNumber, 'reasons' => $reasons];
    }

    public function rowsRejected(): int
    {
        return count($this->rejections);
    }

    public function toArray(): array
    {
        return [
            'rows_read' => $this->rowsRead,
            'rows_imported' => $this->rowsImported,
            'rows_rejected' => $this->rowsRejected(),
            'rejections' => $this->rejections,
        ];
    }

    public function summaryLine(): string
    {
        return sprintf(
            '%d ligne(s) lue(s), %d importée(s), %d rejetée(s)',
            $this->rowsRead,
            $this->rowsImported,
            $this->rowsRejected()
        );
    }
}
