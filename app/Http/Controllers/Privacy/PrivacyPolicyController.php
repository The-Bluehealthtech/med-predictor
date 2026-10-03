<?php

namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use App\Models\Association;
use App\Models\PrivacyPolicy;
use App\Services\Audit\Auditor;
use Illuminate\Http\Request;

/**
 * Politique de confidentialité de la fédération (IHE PCF : politique référencée par
 * Consent.policy.uri). Chaque publication crée une nouvelle version immuable ; un
 * consentement reste lié à la version présentée au joueur.
 */
class PrivacyPolicyController extends Controller
{
    public function index(Request $request)
    {
        $associationId = $this->associationFor($request);
        $policies = PrivacyPolicy::query()->where('association_id', $associationId)->orderByDesc('version')->get();

        return view('privacy.policies', ['policies' => $policies, 'current' => $policies->first(), 'associationId' => $associationId,
            'association' => Association::query()->find($associationId), 'associations' => $request->user()->isSystemAdmin() ? Association::query()->orderBy('name')->get(['id', 'name']) : collect()]);
    }

    public function store(Request $request, Auditor $auditor)
    {
        $associationId = $this->associationFor($request);
        $data = $request->validate(['title' => 'required|string|max:255', 'body' => 'required|string|min:50|max:100000']);
        $policy = PrivacyPolicy::query()->create([
            'association_id' => $associationId,
            'version' => (int) PrivacyPolicy::query()->where('association_id', $associationId)->max('version') + 1,
            'title' => $data['title'], 'body' => $data['body'], 'body_sha256' => hash('sha256', $data['body']),
            'published_by' => $request->user()->id, 'published_at' => now(),
        ]);
        $auditor->record(['event_type' => 'data_modification', 'module' => 'privacy', 'action' => 'privacy_policy_publish',
            'description' => 'Politique de confidentialité publiée (version ' . $policy->version . ')', 'model' => $policy]);

        return redirect()->route('privacy-policies.index', $request->user()->isSystemAdmin() ? ['association_id' => $associationId] : [])
            ->with('success', 'Version ' . $policy->version . ' publiée : elle sera présentée aux prochains consentements.');
    }

    /** Texte d'une version, adresse référencée par les consentements FHIR (accès public, aucune donnée personnelle). */
    public function show(PrivacyPolicy $policy)
    {
        return response()->view('privacy.policy-show', ['policy' => $policy, 'association' => Association::query()->find($policy->association_id)]);
    }

    private function associationFor(Request $request): int
    {
        $user = $request->user();
        if ($user->isSystemAdmin()) {
            $id = $request->integer('association_id') ?: (int) Association::query()->orderBy('id')->value('id');
            abort_unless($id, 422, 'Aucune fédération.');

            return $id;
        }
        abort_unless(in_array($user->role, ['association_admin'], true) && $user->association_id, 403);

        return (int) $user->association_id;
    }
}
