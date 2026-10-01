<?php

namespace App\Services;

use App\Models\HealthRecord;
use App\Models\PosturalAssessment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosturalAssessmentService
{
    public function create(HealthRecord $healthRecord, array $data, int $userId): PosturalAssessment
    {
        return DB::transaction(function () use ($healthRecord, $data, $userId) {
            $assessment = PosturalAssessment::create([
                'player_id' => $healthRecord->player_id,
                'user_id' => $userId,
                'health_record_id' => $healthRecord->id,
                'assessment_type' => $data['assessment_type'],
                'assessment_date' => $data['assessment_date'],
                'context' => $data['context'] ?? null,
                'overall_impression' => $data['overall_impression'] ?? null,
                'clinical_notes' => $data['clinical_notes'] ?? null,
                'recommendations' => $data['recommendations'] ?? null,
                'status' => 'draft',
                'view' => 'anterior',
            ]);

            $this->syncChildren($assessment, $data);

            return $assessment->load(['findings', 'measurements', 'clinician']);
        });
    }

    public function update(PosturalAssessment $assessment, array $data): PosturalAssessment
    {
        if ($assessment->status === 'validated') {
            throw ValidationException::withMessages([
                'assessment' => 'Une évaluation validée est en lecture seule.',
            ]);
        }

        return DB::transaction(function () use ($assessment, $data) {
            $assessment->update(Arr::only($data, [
                'assessment_type',
                'assessment_date',
                'context',
                'overall_impression',
                'clinical_notes',
                'recommendations',
            ]));

            $this->syncChildren($assessment, $data);

            return $assessment->load(['findings', 'measurements', 'clinician']);
        });
    }

    public function complete(PosturalAssessment $assessment): PosturalAssessment
    {
        if ($assessment->status === 'validated') {
            throw ValidationException::withMessages(['assessment' => 'Cette évaluation est déjà validée.']);
        }

        $context = (array) ($assessment->context ?? []);
        $coverage = (array) ($context['coverage'] ?? []);

        if (empty($coverage) && $assessment->findings()->count() === 0 && $assessment->measurements()->count() === 0) {
            throw ValidationException::withMessages([
                'assessment' => 'L’évaluation ne contient encore aucune donnée posturale.',
            ]);
        }

        $assessment->update(['status' => 'completed']);

        return $assessment->fresh(['findings', 'measurements', 'clinician']);
    }

    public function validate(PosturalAssessment $assessment, int $userId): PosturalAssessment
    {
        if ($assessment->status !== 'completed') {
            throw ValidationException::withMessages([
                'assessment' => 'Une évaluation doit être terminée avant validation.',
            ]);
        }

        $assessment->update([
            'status' => 'validated',
            'validated_at' => now(),
            'validated_by' => $userId,
        ]);

        return $assessment->fresh(['findings', 'measurements', 'clinician', 'validatedBy']);
    }

    private function syncChildren(PosturalAssessment $assessment, array $data): void
    {
        $assessment->findings()->delete();
        $assessment->measurements()->delete();

        foreach ($data['findings'] ?? [] as $finding) {
            $finding['source'] = $finding['source'] ?? 'clinician';
            $assessment->findings()->create($finding);
        }

        foreach ($data['measurements'] ?? [] as $measurement) {
            $assessment->measurements()->create($measurement);
        }
    }
}
