<?php

namespace App\Services\Privacy;

use App\Models\PlayerConsent;
use App\Models\Player;
use App\Models\PrivacyPolicy;
use App\Models\User;
use App\Services\Audit\Auditor;
use App\Services\Documents\DocumentSignatureService;
use App\Services\Fhir\FhirClient;
use App\Services\Fhir\FhirException;
use App\Services\Fhir\PatientIdentity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

/**
 * Consentement du joueur au partage de ses données de santé hors du club, selon IHE PCF 1.1.0
 * (Consent Recorder, profil Basic Consent) :
 *  - le formulaire (politique de la fédération + décision) est signé électroniquement par le
 *    joueur ou son représentant légal via le circuit de signature de FIT ;
 *  - à la signature, le consentement devient actif (le précédent est remplacé) et il est
 *    enregistré sur le serveur FHIR (Consent, LOINC 59284-0, scope patient-privacy) ;
 *  - sans consentement actif « permit », FIT refuse le partage hors du club : publication de
 *    l'IPS et consultation des dossiers des établissements.
 */
final class PlayerConsents
{
    public const WORKFLOW = 'privacy.consent';

    public const PCF_BASIC = 'https://profiles.ihe.net/ITI/PCF/StructureDefinition/IHE.PCF.consentBasic';

    public const RELATIONSHIPS = ['MTH' => 'Mère', 'FTH' => 'Père', 'GUARD' => 'Tuteur légal'];

    public function __construct(private readonly DocumentSignatureService $signatures, private readonly FhirClient $client,
        private readonly PatientIdentity $identity, private readonly Auditor $auditor)
    {
    }

    public function associationId(Player $player): ?int
    {
        $player->loadMissing('club');

        return $player->association_id ?: $player->club?->association_id;
    }

    /** Dernière version publiée de la politique de la fédération du joueur. */
    public function currentPolicy(Player $player): ?PrivacyPolicy
    {
        $association = $this->associationId($player);

        return $association ? PrivacyPolicy::query()->where('association_id', $association)->orderByDesc('version')->first() : null;
    }

    public function active(Player $player): ?PlayerConsent
    {
        return PlayerConsent::query()->where('player_id', $player->id)->where('status', 'active')->latest('signed_at')->first();
    }

    /** Partage hors du club autorisé : consentement actif « permit », non échu. */
    public function allowsExternalSharing(Player $player): bool
    {
        $consent = $this->active($player);

        return $consent && $consent->decision === 'permit' && (!$consent->period_end || $consent->period_end->endOfDay()->isFuture());
    }

    public function assertExternalSharing(Player $player): void
    {
        abort_unless($this->allowsExternalSharing($player), 403,
            'Partage hors du club refusé : aucun consentement actif du joueur (IHE PCF). Le secrétariat médical peut le recueillir sur la fiche « Identité clinique ».');
    }

    /** Formulaire PDF, puis demande de signature électronique au joueur ou à son représentant. */
    public function prepare(Player $player, array $data, User $recorder): PlayerConsent
    {
        $policy = $this->currentPolicy($player);
        abort_unless($policy, 422, 'La fédération n’a pas encore publié de politique de confidentialité.');

        return DB::transaction(function () use ($player, $data, $recorder, $policy) {
            $consent = PlayerConsent::query()->create([
                'player_id' => $player->id, 'privacy_policy_id' => $policy->id, 'decision' => $data['decision'],
                'period_end' => $data['period_end'] ?? null, 'performer_type' => $data['performer_type'],
                'performer_name' => $data['performer_name'], 'performer_relationship' => $data['performer_type'] === 'guardian' ? $data['performer_relationship'] : null,
                'performer_email' => $data['performer_email'], 'recorded_by' => $recorder->id, 'status' => 'pending_signature',
            ]);
            $bytes = Pdf::loadView('privacy.consent-pdf', ['consent' => $consent, 'player' => $player, 'policy' => $policy,
                'relationships' => self::RELATIONSHIPS])->setPaper('a4')->setOption('isRemoteEnabled', false)->output();
            $sha = hash('sha256', $bytes);
            $signature = $this->signatures->createRequest($data['provider'], [
                'type' => 'privacy_consent_pdf', 'reference' => 'CONSENT-' . $consent->id . '-' . substr($sha, 0, 16), 'sha256' => $sha,
                'player_id' => $player->id, 'consent_id' => $consent->id, 'policy_id' => $policy->id, 'policy_version' => $policy->version,
                'filename' => 'consentement-partage-' . $consent->id . '.pdf', 'name' => 'Consentement au partage des données de santé', 'bytes' => $bytes,
            ], ['type' => $consent->performer_type, 'name' => $consent->performer_name, 'email' => $consent->performer_email], self::WORKFLOW);
            $consent->update(['signature_request_id' => $signature->id, 'document_sha256' => $sha,
                'status' => in_array($signature->status, ['error', 'cancelled'], true) ? 'cancelled' : 'pending_signature']);
            $this->auditor->record(['event_type' => 'data_modification', 'module' => 'privacy', 'action' => 'consent_prepare',
                'description' => 'Consentement au partage envoyé à la signature (IHE PCF)', 'model' => $player, 'sensitive' => true,
                'metadata' => ['consent_id' => $consent->id, 'decision' => $consent->decision, 'policy_version' => $policy->version]]);

            return $consent->fresh();
        });
    }

    /** Statut de la signature ; un consentement signé devient actif et remplace le précédent. */
    public function refresh(PlayerConsent $consent): PlayerConsent
    {
        if ($consent->status !== 'pending_signature' || !$consent->signatureRequest) {
            return $consent;
        }
        $signature = $this->signatures->sync($consent->signatureRequest);
        if ($signature->status === 'cancelled' || $signature->status === 'error') {
            $consent->update(['status' => 'cancelled']);
        } elseif ($signature->status === 'signed') {
            DB::transaction(function () use ($consent, $signature) {
                PlayerConsent::query()->where('player_id', $consent->player_id)->where('status', 'active')->update(['status' => 'superseded']);
                $consent->update(['status' => 'active', 'signed_at' => $signature->signed_at ?? now()]);
            });
            $this->auditor->record(['event_type' => 'data_modification', 'module' => 'privacy', 'action' => 'consent_activate',
                'description' => 'Consentement au partage signé et actif (IHE PCF)', 'model' => $consent->player, 'sensitive' => true,
                'metadata' => ['consent_id' => $consent->id, 'decision' => $consent->decision]]);
            $this->push($consent->fresh());
        }

        return $consent->fresh();
    }

    public function revoke(PlayerConsent $consent, User $user): void
    {
        abort_unless(in_array($consent->status, ['active', 'pending_signature'], true), 422);
        $consent->update(['status' => 'revoked', 'revoked_at' => now(), 'revoked_by' => $user->id]);
        $this->auditor->record(['event_type' => 'data_modification', 'module' => 'privacy', 'action' => 'consent_revoke',
            'description' => 'Consentement au partage révoqué (IHE PCF)', 'model' => $consent->player, 'sensitive' => true, 'metadata' => ['consent_id' => $consent->id]]);
        if ($consent->fhir_consent_id) {
            $this->push($consent->fresh());
        }
    }

    /** Consent FHIR R4 au profil IHE PCF Basic Consent. */
    public function resource(PlayerConsent $consent, string $patientId): array
    {
        $consent->loadMissing(['policy', 'player.club', 'signatureRequest']);
        $guardian = $consent->performer_type === 'guardian';
        $performer = $guardian ? ['reference' => '#representant'] : ['reference' => 'Patient/' . $patientId];
        $signedPath = data_get($consent->signatureRequest?->metadata, 'signed_path');

        return array_filter([
            'resourceType' => 'Consent',
            'meta' => ['profile' => [self::PCF_BASIC]],
            'contained' => $guardian ? [[
                'resourceType' => 'RelatedPerson', 'id' => 'representant', 'patient' => ['reference' => 'Patient/' . $patientId],
                'relationship' => [['coding' => [['system' => 'http://terminology.hl7.org/CodeSystem/v3-RoleCode', 'code' => $consent->performer_relationship,
                    'display' => self::RELATIONSHIPS[$consent->performer_relationship] ?? $consent->performer_relationship]]]],
                'name' => [['text' => $consent->performer_name]],
            ]] : null,
            'identifier' => [['system' => 'https://fit.tbhc.uk/fhir/sid/consent', 'value' => (string) $consent->id]],
            'status' => match ($consent->status) { 'active' => 'active', 'revoked', 'superseded' => 'inactive', default => 'proposed' },
            'scope' => ['coding' => [['system' => 'http://terminology.hl7.org/CodeSystem/consentscope', 'code' => 'patient-privacy']]],
            'category' => [['coding' => [['system' => 'http://loinc.org', 'code' => '59284-0', 'display' => 'Consent']]]],
            'patient' => ['reference' => 'Patient/' . $patientId],
            'dateTime' => ($consent->signed_at ?? $consent->created_at)->toIso8601String(),
            'performer' => [$performer],
            'organization' => [['display' => $consent->player->club?->name ?? 'Club']],
            'sourceAttachment' => array_filter(['contentType' => 'application/pdf', 'title' => 'Consentement signé #' . $consent->id,
                'hash' => $consent->document_sha256 ? base64_encode(hex2bin($consent->document_sha256)) : null, 'creation' => $consent->created_at->toIso8601String(),
                'url' => $signedPath ? route('privacy.consents.document', $consent) : null]),
            'policy' => [['uri' => $consent->policy->uri()]],
            'provision' => array_filter([
                'type' => $consent->decision,
                'period' => $consent->period_end ? ['start' => ($consent->signed_at ?? $consent->created_at)->toDateString(), 'end' => $consent->period_end->toDateString()] : null,
                'purpose' => [['system' => 'http://terminology.hl7.org/CodeSystem/v3-ActReason', 'code' => 'TREAT', 'display' => 'treatment']],
            ]),
        ], fn ($v) => $v !== null);
    }

    /** Enregistrement sur le serveur FHIR (Consent Recorder) ; un échec reste visible et n'annule pas le consentement. */
    public function push(PlayerConsent $consent): void
    {
        if (!$this->client->configured()) {
            return;
        }
        try {
            $player = Player::withoutGlobalScopes()->findOrFail($consent->player_id);
            $patientId = $this->identity->fitPatientId($player) ?? $this->identity->feed($player);
            $resource = $this->client->conditionalUpdate($this->resource($consent, $patientId), ['identifier' => 'https://fit.tbhc.uk/fhir/sid/consent|' . $consent->id]);
            $consent->update(['fhir_consent_id' => (string) ($resource['id'] ?? $consent->fhir_consent_id), 'sync_error' => null]);
        } catch (FhirException $e) {
            $consent->update(['sync_error' => mb_substr($e->getMessage() . ' ' . implode(' ; ', $e->issues()), 0, 500)]);
        }
    }
}
