<?php
namespace App\Services;

final class PlayerPcmaData
{
    // Projection de la ligne opérationnelle ; aucune décision ni échéance déduite.
    public function fromRecord(object $record): object
    {
        $results = $this->json($record->result_json ?? null);
        $final = $this->json($record->final_statement ?? null);
        $decision = $final['overall_decision'] ?? null;
        if (!in_array($decision, ['FIT', 'NOT_FIT', 'CONDITIONAL'], true)) {
            $decisions = [];
            foreach (['cleared_for_competition'=>'FIT', 'not_cleared'=>'NOT_FIT',
                'cleared_with_restrictions'=>'CONDITIONAL'] as $key=>$value) {
                if (in_array($final[$key] ?? null, [true, 1, '1'], true)) $decisions[] = $value;
            }
            $decision = count($decisions) === 1 ? $decisions[0] : null;
        }
        $signed = (bool) ($record->is_signed ?? false);
        $status = $signed && $decision !== null
            ? ['FIT'=>'cleared', 'NOT_FIT'=>'not_cleared', 'CONDITIONAL'=>'conditional'][$decision]
            : (in_array($record->status ?? null, ['pending', 'completed', 'failed'], true)
                ? $record->status : null);
        return (object) [
            'pcma_id'=>$record->id, 'player_id'=>$record->player_id,
            'pcma_status'=>$status, 'medical_decision'=>$decision, 'is_signed'=>$signed,
            'synthetic_test'=>(bool) data_get($results, 'synthetic_demo'),
            'pcma_score'=>data_get($results, 'pcma_score') ?? data_get($results, 'overall_score'),
            'cardiovascular_fitness'=>data_get($results, 'cardiovascular_fitness'),
            'respiratory_fitness'=>data_get($results, 'respiratory_fitness'),
            'musculoskeletal_fitness'=>data_get($results, 'musculoskeletal_fitness'),
            'neurological_fitness'=>data_get($results, 'neurological_fitness'),
            'next_assessment_date'=>data_get($results, 'next_assessment_date'),
        ];
    }
    private function json(mixed $value): array
    {
        if (is_array($value)) return $value;
        if (!is_string($value)) return [];
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }
}
