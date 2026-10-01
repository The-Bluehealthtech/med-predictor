<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePosturalAssessmentRequest;
use App\Models\HealthRecord;
use App\Models\PosturalAssessment;
use App\Services\PosturalAssessmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PosturalAssessmentController extends Controller
{
    public function __construct(private PosturalAssessmentService $service)
    {
    }

    public function index(Request $request, HealthRecord $healthRecord): JsonResponse
    {
        $this->authorizeMedicalUser($request);

        $assessments = PosturalAssessment::query()
            ->where('health_record_id', $healthRecord->id)
            ->with(['findings', 'measurements', 'clinician', 'validatedBy'])
            ->orderByDesc('assessment_date')
            ->get();

        return response()->json(['data' => $assessments]);
    }

    public function store(StorePosturalAssessmentRequest $request, HealthRecord $healthRecord): JsonResponse
    {
        $this->authorizeMedicalUser($request);
        abort_unless((int) $healthRecord->player_id > 0, 422, 'Le dossier médical doit être associé à un joueur.');

        $assessment = $this->service->create($healthRecord, $request->validated(), (int) $request->user()->id);

        return response()->json(['data' => $assessment], 201);
    }

    public function show(Request $request, PosturalAssessment $assessment): JsonResponse
    {
        $this->authorizeMedicalUser($request);
        $this->assertAssessmentVisible($assessment);

        return response()->json([
            'data' => $assessment->load(['findings', 'measurements', 'clinician', 'validatedBy']),
        ]);
    }

    public function update(StorePosturalAssessmentRequest $request, PosturalAssessment $assessment): JsonResponse
    {
        $this->authorizeMedicalUser($request);
        $this->assertAssessmentVisible($assessment);

        return response()->json([
            'data' => $this->service->update($assessment, $request->validated()),
        ]);
    }

    public function complete(Request $request, PosturalAssessment $assessment): JsonResponse
    {
        $this->authorizeMedicalUser($request);
        $this->assertAssessmentVisible($assessment);

        return response()->json(['data' => $this->service->complete($assessment)]);
    }

    public function validateAssessment(Request $request, PosturalAssessment $assessment): JsonResponse
    {
        $this->authorizeMedicalUser($request);
        $this->assertAssessmentVisible($assessment);

        return response()->json([
            'data' => $this->service->validate($assessment, (int) $request->user()->id),
        ]);
    }

    public function compare(Request $request, PosturalAssessment $assessment, PosturalAssessment $other): JsonResponse
    {
        $this->authorizeMedicalUser($request);
        $this->assertAssessmentVisible($assessment);
        $this->assertAssessmentVisible($other);
        abort_unless($assessment->player_id === $other->player_id, 422, 'Les évaluations doivent appartenir au même joueur.');

        $assessment->load(['findings', 'measurements']);
        $other->load(['findings', 'measurements']);

        $findingKey = fn ($finding) => $finding->finding_key.'|'.$finding->side.'|'.$finding->view;
        $measurementKey = fn ($measurement) => ($measurement->measurement_key ?? 'legacy').'|'.($measurement->side ?? 'none').'|'.$measurement->view;

        return response()->json([
            'data' => [
                'before' => $assessment,
                'after' => $other,
                'finding_changes' => $this->pairByKey($assessment->findings, $other->findings, $findingKey),
                'measurement_changes' => $this->pairByKey($assessment->measurements, $other->measurements, $measurementKey),
            ],
        ]);
    }

    private function pairByKey($before, $after, callable $key): array
    {
        $beforeByKey = $before->keyBy($key);
        $afterByKey = $after->keyBy($key);

        return $beforeByKey->keys()
            ->merge($afterByKey->keys())
            ->unique()
            ->sort()
            ->values()
            ->map(fn ($itemKey) => [
                'key' => $itemKey,
                'before' => $beforeByKey->get($itemKey),
                'after' => $afterByKey->get($itemKey),
            ])
            ->all();
    }

    private function assertAssessmentVisible(PosturalAssessment $assessment): void
    {
        abort_unless($assessment->health_record_id, 403);
        HealthRecord::query()->findOrFail($assessment->health_record_id);
    }

    private function authorizeMedicalUser(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && $user->hasAnyRole([
            'system_admin',
            'super_admin',
            'association_medical',
            'club_medical',
            'doctor',
            'medical_staff',
        ]), 403);
    }
}
