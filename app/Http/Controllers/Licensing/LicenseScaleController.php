<?php

namespace App\Http\Controllers\Licensing;

use App\Http\Controllers\Controller;
use App\Models\LicenseAgeCategory;
use App\Models\User;
use App\Services\Licensing\LicenseScale;
use App\Services\Licensing\LicenseWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Barème des licences, paramétré par la fédération : par genre (male, female)
 * puis discipline (Football, Futsal, BeachSoccer), les catégories d'âge ; et les
 * tarifs des licences d'officiels (TeamOfficial, OrganisationOfficial).
 */
class LicenseScaleController extends Controller
{
    public function __construct(private readonly LicenseScale $scale, private readonly LicenseWorkflow $workflow)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $associationId = $this->associationFor($user, $request->integer('association_id') ?: null);
        $gender = array_key_exists($request->query('gender'), config('licensing.genders')) ? $request->query('gender') : 'male';
        $discipline = array_key_exists($request->query('discipline'), config('licensing.disciplines')) ? $request->query('discipline') : 'Football';
        $settings = $this->scale->settings($associationId);

        return view('licenses.scale', [
            'associationId' => $associationId,
            'associations' => $user->isSystemAdmin() ? DB::table('associations')->orderBy('name')->get(['id', 'name']) : collect(),
            'associationName' => $associationId ? DB::table('associations')->where('id', $associationId)->value('name') : null,
            'saved' => $this->scale->isSaved($associationId),
            'settings' => $settings,
            'season' => $this->scale->season($settings),
            'gender' => $gender,
            'discipline' => $discipline,
            'categories' => $this->scale->categories($associationId, $gender, $discipline),
            'pcmaRules' => LicenseAgeCategory::PCMA_RULES,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $associationId = $this->associationFor($user, $request->integer('association_id') ?: null);
        abort_unless($associationId, 422, 'Choisissez une fédération.');

        $data = $request->validate([
            'gender' => 'required|in:' . implode(',', array_keys(config('licensing.genders'))),
            'discipline' => 'required|in:' . implode(',', array_keys(config('licensing.disciplines'))),
            'currency' => 'required|string|size:3|alpha',
            // ISO639-2Type (FIFA Connect PersonLocal/LocalLanguage) : langue des noms locaux des personnes.
            'local_language' => ['nullable', 'string', 'size:3', $this->languageRule()],
            'season_start_month' => 'required|integer|between:1,12',
            'season_start_day' => 'required|integer|between:1,28',
            'reference_month' => 'required|integer|between:1,12',
            'reference_day' => 'required|integer|between:1,28',
            'official_fees' => 'nullable|array',
            'official_fees.*' => 'nullable|numeric|min:0|max:100000',
            'categories' => 'required|array|min:1',
            'categories.*.code' => 'nullable|string|max:20|regex:/^[A-Za-z0-9_-]+$/',
            'categories.*.label' => 'nullable|string|max:60',
            'categories.*.max_age' => 'nullable|integer|between:5,99',
            'categories.*.allowed_levels' => 'nullable|array',
            'categories.*.allowed_levels.*' => 'in:' . implode(',', array_keys(config('licensing.levels'))),
            'categories.*.fees' => 'nullable|array',
            'categories.*.fees.*' => 'nullable|numeric|min:0|max:100000',
            'categories.*.pcma_rule' => 'nullable|in:' . implode(',', array_keys(LicenseAgeCategory::PCMA_RULES)),
            'categories.*.required_documents' => 'nullable|array',
            'categories.*.required_documents.*' => 'in:' . implode(',', array_keys(config('licensing.documents'))),
            'categories.*.delete' => 'nullable|boolean',
        ]);

        // Lignes vides (nouvelle catégorie non remplie) ou marquées à supprimer : ignorées.
        $categories = collect($data['categories'])->filter(fn ($c) => !empty($c['code']) && empty($c['delete']))->map(fn ($c) => [
            'code' => mb_strtoupper($c['code']),
            'label' => $c['label'] ?: mb_strtoupper($c['code']),
            'max_age' => $c['max_age'] ?? null,
            'allowed_levels' => array_values($c['allowed_levels'] ?? []),
            'fees' => collect($c['fees'] ?? [])->only($c['allowed_levels'] ?? [])->filter(fn ($v) => $v !== null && $v !== '')->map(fn ($v) => round((float) $v, 2))->all(),
            'pcma_rule' => $c['pcma_rule'] ?? 'pro',
            'required_documents' => array_values($c['required_documents'] ?? []),
        ])->sortBy(fn ($c) => $c['max_age'] ?? PHP_INT_MAX)->values();

        $error = match (true) {
            $categories->isEmpty() => 'Au moins une catégorie est nécessaire.',
            $categories->pluck('code')->duplicates()->isNotEmpty() => 'Chaque catégorie doit avoir un code unique.',
            $categories->whereNull('max_age')->count() !== 1 => 'Il faut exactement une catégorie sans âge maximum (senior).',
            $categories->whereNotNull('max_age')->pluck('max_age')->duplicates()->isNotEmpty() => 'Deux catégories ne peuvent pas avoir le même âge maximum.',
            default => null,
        };
        if ($error) {
            throw ValidationException::withMessages(['categories' => $error]);
        }

        $officialFees = collect($data['official_fees'] ?? [])->only(['TeamOfficial', 'OrganisationOfficial'])
            ->filter(fn ($v) => $v !== null && $v !== '')->map(fn ($v) => round((float) $v, 2))->all();
        $this->scale->save($associationId, [
            'currency' => mb_strtoupper($data['currency']),
            'season_start_month' => $data['season_start_month'], 'season_start_day' => $data['season_start_day'],
            'reference_month' => $data['reference_month'], 'reference_day' => $data['reference_day'],
            'official_fees' => $officialFees,
            'local_language' => ($data['local_language'] ?? null) ?: null,
        ], ["{$data['gender']}|{$data['discipline']}" => $categories->all()]);

        $query = ['gender' => $data['gender'], 'discipline' => $data['discipline']] + ($user->isSystemAdmin() ? ['association_id' => $associationId] : []);

        return redirect()->route('licenses.scale', $query)
            ->with('success', 'Barème enregistré pour ' . mb_strtolower(config("licensing.genders.{$data['gender']}")) . ' — ' . config("licensing.disciplines.{$data['discipline']}") . '. Il s\'applique aux nouvelles demandes.');
    }

    private function associationFor(User $user, ?int $requested): ?int
    {
        abort_unless($this->workflow->canApprove($user), 403);
        if ($user->isSystemAdmin()) {
            return $requested ?? ((int) DB::table('associations')->orderBy('name')->value('id') ?: null);
        }

        return (int) $user->association_id;
    }

    /** Énumération ISO639-2Type du paquet XSD FIFA Connect ; à défaut (paquet absent), trois lettres minuscules. */
    private function languageRule(): mixed
    {
        try {
            return Rule::in(app(\App\Services\FifaConnect\SchemaCatalog::class)->enumValues('ISO639-2Type'));
        } catch (\RuntimeException) {
            return 'regex:/^[a-z]{3}$/';
        }
    }
}
