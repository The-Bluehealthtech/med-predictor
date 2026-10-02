<?php

namespace App\Http\Controllers;

use App\Models\RoleConfigVersion;
use App\Models\RoleConfigWeight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class RoleEvaluationSettingsController extends Controller
{
    public function index()
    {
        $versions = RoleConfigVersion::with('weights')
            ->orderByDesc('id')
            ->get();

        return view('modules.coach-cockpit.role-evaluation-settings', [
            'versions' => $versions,
            'families' => $this->families(),
            'dimensions' => config('role_evaluation_engine.dimensions'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
        ]);

        $version = DB::transaction(function () use ($validated, $request) {
            $version = RoleConfigVersion::create([
                'label' => $validated['label'],
                'description' => $validated['description'] ?? null,
                'status' => 'draft',
                'created_by' => $request->user()?->id,
            ]);

            foreach ($this->families() as $family) {
                $dimensionKeys = $this->dimensionKeysForFamily($family);
                $weight = round(1 / count($dimensionKeys), 4);
                foreach ($dimensionKeys as $dimensionKey) {
                    RoleConfigWeight::create([
                        'role_config_version_id' => $version->id,
                        'position_family' => $family,
                        'dimension_key' => $dimensionKey,
                        'weight' => $weight,
                    ]);
                }
            }

            return $version;
        });

        return redirect()
            ->route('modules.coach-cockpit.role-evaluation.settings')
            ->with('success', "Configuration #{$version->id} créée avec des poids égaux modifiables.");
    }

    public function update(Request $request, RoleConfigVersion $version)
    {
        $this->assertDraft($version);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'weights' => ['required', 'array'],
            'weights.*' => ['array'],
            'weights.*.*' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        $this->validateWeightMatrix($validated['weights']);

        DB::transaction(function () use ($version, $validated) {
            $version->update([
                'label' => $validated['label'],
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($validated['weights'] as $family => $weights) {
                foreach ($weights as $dimensionKey => $weight) {
                    RoleConfigWeight::updateOrCreate(
                        [
                            'role_config_version_id' => $version->id,
                            'position_family' => $family,
                            'dimension_key' => $dimensionKey,
                        ],
                        ['weight' => round((float) $weight, 4)]
                    );
                }
            }
        });

        return back()->with('success', "Configuration #{$version->id} enregistrée.");
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'config_file' => ['required', 'file', 'max:2048'],
        ]);

        $decoded = json_decode(
            file_get_contents($request->file('config_file')->getRealPath()),
            true
        );

        if (! is_array($decoded)) {
            throw ValidationException::withMessages([
                'config_file' => 'JSON de configuration invalide.',
            ]);
        }

        $weights = $decoded['weights'] ?? null;
        if (! is_array($weights)) {
            throw ValidationException::withMessages([
                'config_file' => 'Le JSON doit contenir un objet "weights".',
            ]);
        }

        $this->validateWeightMatrix($weights);

        $version = DB::transaction(function () use ($decoded, $weights, $request) {
            $version = RoleConfigVersion::create([
                'label' => (string) ($decoded['label'] ?? 'Configuration importée'),
                'description' => $decoded['description'] ?? null,
                'status' => 'draft',
                'created_by' => $request->user()?->id,
            ]);

            foreach ($weights as $family => $familyWeights) {
                foreach ($familyWeights as $dimensionKey => $weight) {
                    RoleConfigWeight::create([
                        'role_config_version_id' => $version->id,
                        'position_family' => $family,
                        'dimension_key' => $dimensionKey,
                        'weight' => round((float) $weight, 4),
                    ]);
                }
            }

            return $version;
        });

        return back()->with('success', "Configuration externe importée dans la version #{$version->id}.");
    }

    public function export(RoleConfigVersion $version)
    {
        $version->load('weights');

        $weights = [];
        foreach ($version->weights as $weight) {
            $weights[$weight->position_family][$weight->dimension_key] = (float) $weight->weight;
        }

        $payload = [
            'schema' => 'fit.role-evaluation-config.v1',
            'id' => $version->id,
            'label' => $version->label,
            'description' => $version->description,
            'status' => $version->status,
            'weights' => $weights,
        ];

        return Response::make(
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            200,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="role-evaluation-config-'.$version->id.'.json"',
            ]
        );
    }

    public function publish(RoleConfigVersion $version)
    {
        $this->assertDraft($version);
        $weights = $this->weightMatrix($version);
        $this->validateWeightMatrix($weights);

        $version->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', "Configuration #{$version->id} publiée et disponible pour le calcul.");
    }

    public function archive(RoleConfigVersion $version)
    {
        $version->update(['status' => 'archived']);

        return back()->with('success', "Configuration #{$version->id} archivée.");
    }

    private function assertDraft(RoleConfigVersion $version): void
    {
        if ($version->status !== 'draft') {
            abort(409, 'Une configuration publiée ou archivée est immuable.');
        }
    }

    private function families(): array
    {
        return DB::table('position_catalog')
            ->distinct()
            ->orderBy('family')
            ->pluck('family')
            ->all();
    }

    private function dimensionKeysForFamily(string $family): array
    {
        $type = mb_strtolower($family) === 'gardien' ? 'goalkeeper' : 'field';

        return array_keys(config("role_evaluation_engine.dimensions.{$type}", []));
    }

    private function weightMatrix(RoleConfigVersion $version): array
    {
        $matrix = [];
        foreach ($version->weights()->get() as $weight) {
            $matrix[$weight->position_family][$weight->dimension_key] = (float) $weight->weight;
        }

        return $matrix;
    }

    private function validateWeightMatrix(array $matrix): void
    {
        $errors = [];

        foreach ($this->families() as $family) {
            $expected = $this->dimensionKeysForFamily($family);
            $actual = $matrix[$family] ?? [];

            foreach ($expected as $dimensionKey) {
                if (! array_key_exists($dimensionKey, $actual)) {
                    $errors["weights.{$family}.{$dimensionKey}"] = "Poids manquant pour {$family} / {$dimensionKey}.";
                }
            }

            $sum = array_sum(array_map('floatval', array_intersect_key($actual, array_flip($expected))));
            if (abs($sum - 1.0) > 0.002) {
                $errors["weights.{$family}"] = sprintf(
                    'La somme des poids de %s doit être 1,0000 (actuellement %.4f).',
                    $family,
                    $sum
                );
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }
}
