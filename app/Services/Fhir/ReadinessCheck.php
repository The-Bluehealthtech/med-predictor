<?php

namespace App\Services\Fhir;

use App\Models\Association;
use App\Models\FhirOrder;
use App\Models\PrivacyPolicy;
use App\Services\Documents\DocumentSignatureService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

/**
 * Vérification de mise en service de la chaîne FHIR de FIT : configuration, serveur HAPI
 * (conformité IHE), abonnement des comptes rendus, autorisation IUA, traçabilité BALP,
 * consentement PCF, décodeur d'imagerie, examens à transmettre. Lecture seule ; chaque point
 * indique l'action à mener (commande fhir:readiness et page d'administration).
 */
final class ReadinessCheck
{
    /** @var list<array{0:string,1:string,2:string}> */
    private array $rows = [];

    public function __construct(private readonly FhirClient $client, private readonly ConformanceCheck $conformance,
        private readonly IuaToken $token, private readonly DocumentSignatureService $signatures)
    {
    }

    /** @return list<array{label:string, state:string, detail:string}> state : ok | warn | fail */
    public function run(): array
    {
        $this->rows = [];
        $client = $this->client;
        $conformance = $this->conformance;
        $token = $this->token;
        $signatures = $this->signatures;

        $base = (string) config('fhir.base_url');
        $app = (string) config('app.url');

        $this->check('FIT_FHIR_BASE_URL (adresse interne du serveur)', $base !== '' ? 'ok' : 'fail', $base ?: 'à renseigner avec l’adresse interne Render du service fit-fhir, ex. http://fit-fhir:8080/fhir');
        $this->check('APP_URL en HTTPS (point de notification)', str_starts_with($app, 'https://') ? 'ok' : 'fail', $app ?: 'APP_URL vide');
        $this->check('FIT_FHIR_WEBHOOK_SECRET', strlen((string) config('fhir.webhook_secret')) >= 32 ? 'ok' : 'fail', 'secret partagé d’au moins 32 caractères, envoyé par le serveur à chaque notification');
        $this->check('FIT_FHIR_SOURCE_OID (publication des IPS)', preg_match('/^[0-2](\.(0|[1-9][0-9]*))+$/', (string) config('fhir.document_sharing.source_oid')) ? 'ok' : 'warn', 'OID de FIT comme source documentaire ; sans lui, la publication des IPS reste désactivée');
        $viewer = (string) config('fhir.imaging.iid_viewer_url');
        $this->check('FIT_IID_VIEWER_URL (visionneuse du PACS)', str_starts_with($viewer, 'https://') ? 'ok' : 'warn', $viewer ?: 'sans elle, les examens des établissements s’affichent sans lien vers les images');
        $this->check('Dictée du PCMA (GOOGLE_SPEECH_API_KEY)', filled(config('services.google_speech.key')) ? 'ok' : 'warn', 'clé Google Cloud Speech-to-Text gardée sur le serveur ; sans elle, la dictée du PCMA est indisponible');
        $pacs = (string) config('medical_imaging.pacs_url');
        $this->check('PACS DICOMweb (MEDICAL_PACS_DICOMWEB_URL)', str_starts_with($pacs, 'https://') ? 'ok' : 'warn', $pacs !== '' ? $pacs : 'sans lui, les images des établissements ne s’ouvrent pas dans FIT (IHE RAD WIA)');
        $this->check('Traçabilité BALP (FIT_FHIR_AUDIT)', config('fhir.audit.enabled') ? 'ok' : 'warn', config('fhir.audit.enabled') ? 'AuditEvent déposé pour chaque échange de données de patient' : 'désactivée');

        if ($token->enabled()) {
            try {
                $token->forget();
                $token->get();
                $this->check('Autorisation IUA (ITI-71)', 'ok', 'jeton obtenu auprès de ' . config('fhir.auth.token_url'));
            } catch (FhirException $e) {
                $this->check('Autorisation IUA (ITI-71)', 'fail', $e->getMessage());
            }
        } else {
            $this->check('Autorisation IUA (ITI-71)', 'warn', 'non configurée : protection par le seul réseau privé Render (FIT_FHIR_TOKEN_URL, CLIENT_ID, CLIENT_SECRET, SCOPE)');
        }

        if ($base !== '') {
            try {
                $result = $conformance->run();
                $missing = collect($result['actors'])->flatMap(fn ($a) => $a['findings'])->where('ok', false)->where('level', 'SHALL')->count();
                $guides = collect($result['guides'])->where('ok', false)->pluck('package')->implode(', ');
                $this->check('Serveur FHIR joignable, version ' . config('fhir.fhir_version'), $result['version']['ok'] ? 'ok' : 'fail', 'version annoncée : ' . ($result['version']['actual'] ?? '—'));
                $this->check('Guides IHE / HL7 installés', $guides === '' ? 'ok' : 'fail', $guides === '' ? 'IPS, sIPS, MHD, PDQm, PIXm, BALP' : 'absents : ' . $guides);
                $this->check('Exigences SHALL des acteurs IHE', $missing === 0 ? 'ok' : 'fail', $missing === 0 ? 'toutes présentes' : "{$missing} manquante(s) : php artisan fhir:conformance pour le détail");
            } catch (FhirException $e) {
                $this->check('Serveur FHIR joignable', 'fail', $e->getMessage());
            }
            try {
                $endpoint = rtrim($app, '/') . '/api/fhir/notify';
                $subscriptions = $client->search('Subscription', ['url' => $endpoint]);
                $statuses = collect($subscriptions['entry'] ?? [])->pluck('resource.status')->all();
                $this->check('Abonnement des comptes rendus', in_array('active', $statuses, true) ? 'ok' : 'fail',
                    $statuses === [] ? 'absent : php artisan fhir:subscriptions:install' : 'statut ' . implode(', ', $statuses) . ' (attendu : active)');
            } catch (FhirException $e) {
                $this->check('Abonnement des comptes rendus', 'fail', $e->getMessage());
            }
        }

        $python = (string) config('medical_imaging.python');
        $decoder = new Process([$python, '-c', 'import pydicom, numpy, PIL, gdcm']);
        $this->check('Décodeur d’imagerie (pydicom, GDCM)', is_executable($python) && $decoder->run() === 0 ? 'ok' : 'fail', $python);

        $withPlayers = DB::table('players')->whereNotNull('association_id')->distinct()->pluck('association_id');
        $withoutPolicy = Association::query()->whereIn('id', $withPlayers)->whereNotIn('id', PrivacyPolicy::query()->select('association_id'))->pluck('name');
        $this->check('Politiques de confidentialité publiées', $withoutPolicy->isEmpty() ? 'ok' : 'warn',
            $withoutPolicy->isEmpty() ? 'toutes les fédérations ayant des joueurs' : 'à publier (/privacy-policies) : ' . $withoutPolicy->implode(', '));
        $adobe = collect($signatures->allStatuses())->firstWhere('slug', 'adobe_sign');
        $this->check('Signature des consentements (Adobe Sign)', ($adobe['status'] ?? null) === 'ready' ? 'ok' : 'warn', $adobe['label'] ?? 'inconnu');

        $toSend = FhirOrder::query()->whereIn('status', ['pending', 'error'])->count();
        $this->check('Examens prescrits à transmettre', $toSend === 0 ? 'ok' : 'warn', $toSend === 0 ? 'aucun' : "{$toSend} en attente : php artisan fhir:orders:resend");


        return array_map(fn ($r) => ['label' => $r[0], 'state' => $r[1], 'detail' => $r[2]], $this->rows);
    }

    private function check(string $label, string $state, string $detail): void
    {
        $this->rows[] = [$label, $state, $detail];
    }
}
