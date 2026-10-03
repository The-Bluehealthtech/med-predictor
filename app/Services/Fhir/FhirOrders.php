<?php

namespace App\Services\Fhir;

use App\Models\FhirOrder;
use App\Models\Player;
use App\Models\User;
use App\Models\Visit;
use App\Notifications\MedicalResultNotification;
use App\Services\Audit\Auditor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

/**
 * Prescriptions d'examens de la consultation transmises aux laboratoires et services
 * d'imagerie par le serveur FHIR de FIT (HL7 FHIR R4) :
 *  - chaque acte prescrit (laboratoire, imagerie, IRM) devient une ServiceRequest
 *    (status active, intent order), créée par mise à jour conditionnelle sur son identifiant ;
 *    le moteur d'intégration la transmet au LIS / RIS (IHE LAB / RAD en amont de FIT) ;
 *  - les comptes rendus qui reviennent portent DiagnosticReport.basedOn = la ServiceRequest :
 *    FIT les rattache, passe la prescription à « résultats reçus » et prévient le médecin et le
 *    secrétariat. Le serveur appelle FIT par un abonnement FHIR (Subscription rest-hook, sans
 *    contenu) ; la synchronisation peut aussi être lancée à la main.
 */
final class FhirOrders
{
    private const V2_0074 = 'http://terminology.hl7.org/CodeSystem/v2-0074';

    public const IDENTIFIER_SYSTEM = 'https://fit.tbhc.uk/fhir/sid/service-request';

    public function __construct(private readonly FhirClient $client, private readonly PatientIdentity $identity, private readonly Auditor $auditor)
    {
    }

    /** Actes prescrits qui partent vers un laboratoire ou un service d'imagerie. */
    public function external(array $modules): array
    {
        return array_values(array_intersect($modules, array_keys(config('fhir.orders', []))));
    }

    /** ServiceRequest d'une prescription (ressource FHIR R4 de base). */
    public function resource(FhirOrder $order, string $patientId, ?User $doctor): array
    {
        $definition = config("fhir.orders.{$order->module}");

        return array_filter([
            'resourceType' => 'ServiceRequest',
            'identifier' => [['system' => self::IDENTIFIER_SYSTEM, 'value' => (string) $order->id]],
            'status' => 'active',
            'intent' => 'order',
            'category' => [['coding' => [['system' => self::V2_0074, 'code' => $definition['category'], 'display' => $definition['category_display']]], 'text' => $definition['label']]],
            'code' => ['text' => $definition['label'] . ($order->details ? ' — ' . $order->details : '')],
            'subject' => ['reference' => 'Patient/' . $patientId],
            'authoredOn' => ($order->created_at ?? now())->toIso8601String(),
            'requester' => $doctor ? ['display' => $doctor->name] : null,
            'note' => $order->details ? [['text' => $order->details]] : null,
        ], fn ($v) => $v !== null);
    }

    /**
     * Transmission des actes prescrits lors d'une consultation. Un échec reste visible sur la
     * prescription (status error) et n'interrompt jamais la consultation.
     *
     * @return list<FhirOrder>
     */
    public function dispatch(Visit $visit, Player $player, array $modules, ?string $details, ?User $doctor): array
    {
        $orders = [];
        foreach ($this->external($modules) as $module) {
            $orders[] = FhirOrder::query()->firstOrCreate(['visit_id' => $visit->id, 'module' => $module],
                ['player_id' => $player->id, 'details' => $details ?: null, 'requested_by' => $doctor?->id]);
        }
        if ($orders === [] || !$this->client->configured()) {
            return $orders;
        }
        try {
            $patientId = $this->identity->fitPatientId($player) ?? $this->identity->feed($player);
        } catch (FhirException $e) {
            foreach ($orders as $order) {
                $order->update(['status' => 'error', 'error' => mb_substr('Identité non transmise : ' . $e->getMessage(), 0, 500)]);
            }

            return $orders;
        }
        foreach ($orders as $order) {
            if ($order->service_request_id) {
                continue;
            }
            try {
                $request = $this->client->conditionalUpdate($this->resource($order, $patientId, $doctor),
                    ['identifier' => self::IDENTIFIER_SYSTEM . '|' . $order->id]);
                $order->update(['service_request_id' => (string) ($request['id'] ?? ''), 'status' => 'active', 'sent_at' => now(), 'error' => null]);
                $this->auditor->record(['event_type' => 'data_modification', 'module' => 'fhir', 'action' => 'service_request_send',
                    'description' => 'Prescription d\'examen transmise au serveur FHIR (ServiceRequest)', 'model' => $player, 'sensitive' => true,
                    'metadata' => ['order_id' => $order->id, 'module' => $order->module, 'service_request' => $order->service_request_id]]);
            } catch (FhirException $e) {
                $order->update(['status' => 'error', 'error' => mb_substr($e->getMessage() . ' ' . implode(' ; ', $e->issues()), 0, 500)]);
            }
        }

        return $orders;
    }

    /** Nouvelle tentative pour les prescriptions en erreur ou non transmises (serveur installé depuis). */
    public function retry(FhirOrder $order): void
    {
        $visit = $order->visit;
        $this->dispatch($visit, $order->player, [$order->module], $order->details, $order->requested_by ? User::query()->find($order->requested_by) : null);
    }

    /**
     * Rattachement des comptes rendus aux prescriptions actives (DiagnosticReport?based-on=…).
     * Renvoie le nombre de prescriptions passées à « résultats reçus ».
     */
    public function sync(?Player $player = null): int
    {
        if (!$this->client->configured()) {
            return 0;
        }
        $completed = 0;
        FhirOrder::query()->whereIn('status', ['active', 'results_received'])->whereNotNull('service_request_id')
            ->when($player, fn ($q) => $q->where('player_id', $player->id))
            ->with(['player', 'visit'])->orderBy('id')->chunkById(50, function ($orders) use (&$completed) {
                $byRequest = $orders->keyBy('service_request_id');
                $bundle = $this->client->search('DiagnosticReport', [
                    'based-on' => $byRequest->keys()->map(fn ($id) => 'ServiceRequest/' . $id)->implode(','),
                    '_count' => 200,
                ]);
                foreach ($bundle['entry'] ?? [] as $entry) {
                    $report = $entry['resource'] ?? [];
                    if (($report['resourceType'] ?? null) !== 'DiagnosticReport' || empty($report['id'])) {
                        continue;
                    }
                    foreach ($report['basedOn'] ?? [] as $reference) {
                        $order = $byRequest->get(preg_replace('~^.*ServiceRequest/~', '', (string) ($reference['reference'] ?? '')));
                        if (!$order || in_array((string) $report['id'], $order->report_ids ?? [], true)) {
                            continue;
                        }
                        $final = in_array($report['status'] ?? null, ['final', 'amended', 'corrected', 'appended'], true);
                        $first = $order->status !== 'results_received' && $final;
                        $order->update([
                            'report_ids' => array_values(array_merge($order->report_ids ?? [], [(string) $report['id']])),
                            'status' => $final ? 'results_received' : $order->status,
                            'results_at' => $final ? ($order->results_at ?? Carbon::parse($report['issued'] ?? now())) : $order->results_at,
                        ]);
                        if ($first) {
                            $completed++;
                            $this->notify($order->fresh(['player', 'visit']));
                        }
                    }
                }
            });

        return $completed;
    }

    /** Médecin de la visite (écran des données des établissements) et secrétariat du club (sans contenu médical). */
    private function notify(FhirOrder $order): void
    {
        $player = Player::withoutGlobalScopes()->with('club')->find($order->player_id);
        $name = $player ? trim($player->first_name . ' ' . $player->last_name) ?: $player->name : 'joueur';
        $message = "Compte rendu reçu : {$order->label()} — {$name}";
        if ($order->visit?->doctor_id && ($doctor = User::query()->find($order->visit->doctor_id))) {
            $doctor->notify(new MedicalResultNotification($order, $message, route('clinical.external-data', ['player' => $order->player_id, 'tab' => 'reports'])));
        }
        $secretaries = User::query()->where('role', 'secretary')->where('club_id', $player?->club_id)->whereNotNull('club_id')->get();
        if ($secretaries->isNotEmpty()) {
            Notification::send($secretaries, new MedicalResultNotification($order, $message, route('secretary.dashboard')));
        }
    }

    /** Abonnement FHIR R4 (rest-hook, sans contenu) : le serveur appelle FIT à chaque compte rendu. */
    public function subscription(string $endpoint): array
    {
        return [
            'resourceType' => 'Subscription',
            'status' => 'requested',
            'reason' => 'FIT : comptes rendus des examens prescrits',
            'criteria' => 'DiagnosticReport?based-on:missing=false',
            'channel' => ['type' => 'rest-hook', 'endpoint' => $endpoint, 'header' => ['Authorization: Bearer ' . config('fhir.webhook_secret')]],
        ];
    }

    public function installSubscription(string $endpoint): array
    {
        return $this->client->conditionalUpdate($this->subscription($endpoint), ['url' => $endpoint]);
    }
}
