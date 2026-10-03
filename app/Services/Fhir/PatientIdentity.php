<?php

namespace App\Services\Fhir;

use App\Models\FhirPatientLink;
use App\Models\Player;
use App\Models\User;
use App\Services\Audit\Auditor;
use Illuminate\Support\Carbon;

/**
 * Identité clinique du joueur sur le serveur FHIR de FIT.
 *  - Flux d'identité (IHE PIXm ITI-104, rôle Patient Identifier Cross-reference Source) :
 *    FIT alimente son propre Patient par mise à jour conditionnelle sur l'identifiant FIT.
 *  - Recherche de candidats (IHE PDQm ITI-78, rôle Patient Demographics Consumer) parmi les
 *    Patients alimentés par les sources externes : FIFA ID, puis nom + date de naissance.
 *  - Rattachement uniquement après confirmation humaine : Patient.link de type « seealso »
 *    sur le Patient de FIT. Le Patient d'une source n'est jamais modifié ni fusionné.
 */
final class PatientIdentity
{
    public const PIXM_PATIENT = 'https://profiles.ihe.net/ITI/PIXm/StructureDefinition/IHE.PIXm.Patient';

    public const PIXM_PATIENT_BIRTHDATE = 'https://profiles.ihe.net/ITI/PIXm/StructureDefinition/IHE.PIXm.Patient.BirthDateRequired';

    public function __construct(private readonly FhirClient $client, private readonly Auditor $auditor)
    {
    }

    public function configured(): bool
    {
        return $this->client->configured();
    }

    /** Patient FHIR alimenté par FIT pour ce joueur (profil IHE PIXm Patient). */
    public function resource(Player $player): array
    {
        $systems = config('fhir.identifier_systems');
        $identifiers = [['use' => 'usual', 'system' => $systems['player'], 'value' => (string) $player->id]];
        if ($fifa = strtoupper(trim((string) $player->fifa_connect_id))) {
            $identifiers[] = ['use' => 'official', 'system' => $systems['fifa_id'], 'value' => $fifa];
        }
        $name = array_filter(['use' => 'official', 'family' => $player->last_name ?: null, 'given' => array_values(array_filter([$player->first_name]))]);
        if (empty($name['family']) && empty($name['given'])) {
            $name['text'] = (string) ($player->name ?: ('Joueur ' . $player->id)); // iti-pdqm-patname
        }
        $birthDate = $player->date_of_birth ? Carbon::parse($player->date_of_birth)->toDateString() : null;
        $links = FhirPatientLink::query()->where(['player_id' => $player->id, 'role' => 'external', 'status' => 'linked'])
            ->whereNotNull('patient_id')->orderBy('id')->pluck('patient_id')
            ->map(fn ($id) => ['other' => ['reference' => 'Patient/' . $id], 'type' => 'seealso'])->all();

        return array_filter([
            'resourceType' => 'Patient',
            'meta' => ['profile' => [$birthDate ? self::PIXM_PATIENT_BIRTHDATE : self::PIXM_PATIENT]],
            'identifier' => $identifiers,
            'active' => true, // iti-pdqm-linkstatus : obligatoire dès qu'un lien existe
            'name' => [$name],
            'gender' => match (strtolower((string) $player->gender)) {
                'male', 'm', 'homme' => 'male',
                'female', 'f', 'femme' => 'female',
                default => 'unknown',
            },
            'birthDate' => $birthDate,
            'link' => $links ?: null,
        ], fn ($v) => $v !== null);
    }

    /** ITI-104 : création ou mise à jour du Patient de FIT ; renvoie son id logique. */
    public function feed(Player $player): string
    {
        $link = FhirPatientLink::query()->firstOrNew(['player_id' => $player->id, 'role' => 'fit']);
        try {
            $patient = $this->client->conditionalUpdate($this->resource($player),
                ['identifier' => config('fhir.identifier_systems.player') . '|' . $player->id]);
        } catch (FhirException $e) {
            $link->fill(['sync_error' => mb_substr($e->getMessage() . ' ' . implode(' ; ', $e->issues()), 0, 500)])->save();
            throw $e;
        }
        $link->fill(['patient_id' => (string) ($patient['id'] ?? $link->patient_id), 'status' => 'linked', 'synced_at' => now(), 'sync_error' => null])->save();
        $this->auditor->record(['event_type' => 'data_modification', 'module' => 'fhir', 'action' => 'patient_identity_feed',
            'description' => 'Identité du joueur transmise au serveur FHIR (ITI-104)', 'model' => $player, 'sensitive' => true,
            'metadata' => ['patient_id' => $link->patient_id]]);

        return $link->patient_id;
    }

    /** Alimentation sans interrompre le parcours (pré-accueil) : l'échec reste visible sur la fiche d'identité. */
    public function feedQuietly(Player $player): void
    {
        if (!$this->configured()) {
            return;
        }
        try {
            $this->feed($player);
        } catch (FhirException) {
            // sync_error enregistré par feed()
        }
    }

    public function fitPatientId(Player $player): ?string
    {
        return FhirPatientLink::query()->where(['player_id' => $player->id, 'role' => 'fit'])->value('patient_id');
    }

    /**
     * ITI-78 : Patients des sources pouvant correspondre au joueur, hors Patient de FIT et
     * Patients déjà rattachés ou écartés. Aucune décision automatique.
     *
     * @return list<array{id:string, matched_on:string, name:string, birthDate:?string, gender:?string, identifiers:list<string>, source:?string}>
     */
    public function candidates(Player $player): array
    {
        app(\App\Services\Privacy\PlayerConsents::class)->assertExternalSharing($player); // dossiers d'établissements
        $own = $this->fitPatientId($player) ?? $this->feed($player);
        $decided = FhirPatientLink::query()->where(['player_id' => $player->id, 'role' => 'external'])->pluck('patient_id')->all();
        $queries = [];
        if ($fifa = strtoupper(trim((string) $player->fifa_connect_id))) {
            // Sans système : les sources codent le FIFA ID dans leur propre système d'identifiants.
            $queries['fifa_id'] = ['identifier' => $fifa];
        }
        if ($player->last_name && $player->date_of_birth) {
            $queries['demographics'] = array_filter(['family' => $player->last_name, 'given' => $player->first_name ?: null,
                'birthdate' => Carbon::parse($player->date_of_birth)->toDateString()]);
        }

        $found = [];
        foreach ($queries as $matchedOn => $query) {
            $bundle = $this->client->search('Patient', $query + ['_count' => 20]);
            foreach ($bundle['entry'] ?? [] as $entry) {
                $patient = $entry['resource'] ?? [];
                $id = (string) ($patient['id'] ?? '');
                if ($id === '' || ($patient['resourceType'] ?? null) !== 'Patient' || $id === $own || in_array($id, $decided, true) || isset($found[$id])) {
                    continue;
                }
                $found[$id] = $this->summary($patient) + ['matched_on' => $matchedOn];
            }
        }

        return array_values($found);
    }

    /** Rattachement confirmé : lien « seealso » ajouté au Patient de FIT. */
    public function confirm(Player $player, string $patientId, User $user, ?string $matchedOn = null): void
    {
        app(\App\Services\Privacy\PlayerConsents::class)->assertExternalSharing($player);
        $this->decide($player, $patientId, $user, 'linked', $matchedOn);
        $this->feed($player);
    }

    /** Candidat écarté (ou lien retiré) : il n'est plus proposé. */
    public function reject(Player $player, string $patientId, User $user): void
    {
        $wasLinked = FhirPatientLink::query()->where(['player_id' => $player->id, 'role' => 'external', 'patient_id' => $patientId, 'status' => 'linked'])->exists();
        $this->decide($player, $patientId, $user, 'rejected');
        if ($wasLinked) {
            $this->feed($player);
        }
    }

    private function decide(Player $player, string $patientId, User $user, string $status, ?string $matchedOn = null): void
    {
        abort_if($patientId === $this->fitPatientId($player), 422, 'Le Patient de FIT ne peut pas être rattaché à lui-même.');
        $patient = $this->client->read('Patient', $patientId);
        abort_unless(($patient['resourceType'] ?? null) === 'Patient', 422, 'Patient introuvable sur le serveur FHIR.');
        $snapshot = $this->summary($patient);
        $link = FhirPatientLink::query()->firstOrNew(['player_id' => $player->id, 'role' => 'external', 'patient_id' => $patientId]);
        $link->fill(['status' => $status, 'snapshot' => $snapshot, 'decided_by' => $user->id, 'decided_at' => now(),
            'matched_on' => $link->matched_on ?? $matchedOn])->save();
        $this->auditor->record(['event_type' => 'data_modification', 'module' => 'fhir',
            'action' => $status === 'linked' ? 'patient_identity_link' : 'patient_identity_reject',
            'description' => $status === 'linked' ? 'Patient d\'une source rattaché au joueur (PDQm)' : 'Patient d\'une source écarté pour le joueur',
            'model' => $player, 'sensitive' => true, 'metadata' => ['patient_id' => $patientId]]);
    }

    /** Identité affichable d'un Patient (sans données cliniques). */
    private function summary(array $patient): array
    {
        $name = $patient['name'][0] ?? [];
        $display = trim(($name['text'] ?? '') ?: implode(' ', array_merge($name['given'] ?? [], [$name['family'] ?? ''])));

        return [
            'id' => (string) ($patient['id'] ?? ''),
            'name' => $display !== '' ? $display : '—',
            'birthDate' => $patient['birthDate'] ?? null,
            'gender' => $patient['gender'] ?? null,
            'identifiers' => collect($patient['identifier'] ?? [])->map(fn ($i) => ($i['system'] ?? '?') . ' | ' . ($i['value'] ?? '?'))->values()->all(),
            'source' => $patient['meta']['source'] ?? ($patient['managingOrganization']['display'] ?? null),
        ];
    }
}
