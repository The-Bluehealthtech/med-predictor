<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePosturalAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'assessment_type' => ['required', Rule::in(['baseline','routine','injury','follow_up','return_to_play'])],
            'assessment_date' => ['required', 'date'],
            'context' => ['nullable', 'array'],
            'overall_impression' => ['nullable', 'string', 'max:10000'],
            'clinical_notes' => ['nullable', 'string', 'max:10000'],
            'recommendations' => ['nullable', 'string', 'max:10000'],

            'findings' => ['nullable', 'array'],
            'findings.*.view' => ['required', 'string'],
            'findings.*.region' => ['required', 'string', 'max:40'],
            'findings.*.finding_key' => ['required', 'string', 'max:80'],
            'findings.*.side' => ['required', 'string'],
            'findings.*.severity' => ['nullable', 'string'],
            'findings.*.value' => ['nullable', 'numeric'],
            'findings.*.unit' => ['nullable', 'string', 'max:16'],
            'findings.*.source' => ['nullable', 'string'],
            'findings.*.confidence' => ['nullable', 'numeric', 'between:0,1'],
            'findings.*.source_metadata' => ['nullable', 'array'],
            'findings.*.notes' => ['nullable', 'string', 'max:2000'],

            'measurements' => ['nullable', 'array'],
            'measurements.*.view' => ['required', 'string'],
            'measurements.*.measurement_type' => ['required', 'string', 'max:32'],
            'measurements.*.measurement_key' => ['nullable', 'string', 'max:80'],
            'measurements.*.anatomical_region' => ['nullable', 'string', 'max:40'],
            'measurements.*.side' => ['nullable', 'string', 'max:24'],
            'measurements.*.value' => ['nullable', 'numeric'],
            'measurements.*.unit' => ['nullable', 'string', 'max:16'],
            'measurements.*.points' => ['required', 'array', 'min:1'],
            'measurements.*.points.*.x' => ['required', 'numeric', 'between:0,1'],
            'measurements.*.points.*.y' => ['required', 'numeric', 'between:0,1'],
            'measurements.*.metadata' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $catalog = config('postural_assessment');
            $views = $catalog['views'] ?? [];
            $sides = $catalog['sides'] ?? [];
            $severities = $catalog['severities'] ?? [];
            $sources = $catalog['sources'] ?? [];
            $findingsCatalog = $catalog['findings'] ?? [];
            $measurementsCatalog = $catalog['measurements'] ?? [];

            foreach ((array) $this->input('findings', []) as $index => $finding) {
                $key = $finding['finding_key'] ?? null;
                $definition = $key ? ($findingsCatalog[$key] ?? null) : null;

                if (!$definition) {
                    $validator->errors()->add("findings.$index.finding_key", 'Finding postural inconnu.');
                    continue;
                }

                if (!in_array($finding['view'] ?? null, $views, true) || !in_array($finding['view'] ?? null, $definition['views'], true)) {
                    $validator->errors()->add("findings.$index.view", 'Vue incompatible avec ce finding.');
                }

                if (($finding['region'] ?? null) !== $definition['region']) {
                    $validator->errors()->add("findings.$index.region", 'Région incompatible avec ce finding.');
                }

                if (!in_array($finding['side'] ?? null, $sides, true) || !in_array($finding['side'] ?? null, $definition['sides'], true)) {
                    $validator->errors()->add("findings.$index.side", 'Latéralisation incompatible avec ce finding.');
                }

                if (isset($finding['severity']) && $finding['severity'] !== null && !in_array($finding['severity'], $severities, true)) {
                    $validator->errors()->add("findings.$index.severity", 'Sévérité invalide.');
                }

                if (isset($finding['source']) && $finding['source'] !== null && !in_array($finding['source'], $sources, true)) {
                    $validator->errors()->add("findings.$index.source", 'Source invalide.');
                }
            }

            foreach ((array) $this->input('measurements', []) as $index => $measurement) {
                $key = $measurement['measurement_key'] ?? null;

                if (!in_array($measurement['view'] ?? null, $views, true)) {
                    $validator->errors()->add("measurements.$index.view", 'Vue posturale invalide.');
                }

                if (!$key) {
                    continue;
                }

                $definition = $measurementsCatalog[$key] ?? null;
                if (!$definition) {
                    $validator->errors()->add("measurements.$index.measurement_key", 'Mesure posturale inconnue.');
                    continue;
                }

                if (($measurement['measurement_type'] ?? null) !== $definition['type']) {
                    $validator->errors()->add("measurements.$index.measurement_type", 'Type de mesure incompatible.');
                }

                if (($measurement['unit'] ?? null) !== $definition['unit']) {
                    $validator->errors()->add("measurements.$index.unit", 'Unité incompatible avec la mesure.');
                }

                $pointCount = count((array) ($measurement['points'] ?? []));
                if ($pointCount !== (int) $definition['points']) {
                    $validator->errors()->add("measurements.$index.points", 'Nombre de points incompatible avec le protocole de mesure.');
                }
            }
        });
    }
}
