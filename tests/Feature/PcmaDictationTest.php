<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Medical\PcmaDictationParser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Dictée du PCMA : transcription par le serveur FIT (Google Speech-to-Text, clé côté serveur),
 * analyse en propositions de champs, aucune valeur déduite, aucun appel direct du navigateur.
 */
class PcmaDictationTest extends TestCase
{
    use DatabaseTransactions;

    private function proposals(string $text): array
    {
        return collect(app(PcmaDictationParser::class)->parse($text))->pluck('value', 'field')->all();
    }

    private function doctor(): User
    {
        return User::factory()->create(['role' => 'club_medical', 'status' => 'active', 'tenant_id' => 1]);
    }

    public function test_identity_vitals_and_fifa_id_are_parsed_from_french_dictation(): void
    {
        $this->assertSame(['player_name' => 'Karim Mansour', 'position' => 'defender'],
            $this->proposals("Le joueur s'appelle Karim Mansour, il a 24 ans, il est défenseur et joue au Club Africain"));
        $this->assertSame(['blood_pressure' => '120/80', 'heart_rate' => '62', 'oxygen_saturation' => '98', 'weight' => '74'],
            $this->proposals('Tension artérielle 12 sur 8, fréquence cardiaque 62, saturation 98 pour cent, poids 74 kilos'));
        $this->assertSame(['blood_pressure' => '145/95'], $this->proposals('TA 145 sur 95'), 'valeurs déjà en mmHg conservées');
        $this->assertSame(['respiratory_rate' => '16', 'temperature' => '37.2'], $this->proposals('Température 37 virgule 2, fréquence respiratoire 16'));
        $this->assertSame(['fifa_connect_id' => 'KAR123A'], $this->proposals('ID FIFA CONNECT KAR123A'));
        $this->assertSame(['fifa_connect_id' => 'KAR123A'], $this->proposals('FIFA connect K A R 1 2 3 A'), 'FIFA ID épelé');
    }

    public function test_negations_histories_and_conclusions_are_explicit_and_never_inferred(): void
    {
        $this->assertSame(['cardiovascular_history' => 'Aucun antécédent cardiovasculaire déclaré', 'final_statement[overall_decision]' => 'FIT'],
            $this->proposals("Pas d'antécédents cardiaques, apte à la compétition"));
        $this->assertSame(['surgical_history' => 'ligamentoplastie du genou droit en 2022', 'final_statement[overall_decision]' => 'CONDITIONAL'],
            $this->proposals('Antécédents chirurgicaux : ligamentoplastie du genou droit en 2022. Apte avec restrictions'));
        $this->assertSame('NOT_FIT', $this->proposals('Joueur inapte temporairement')['final_statement[overall_decision]']);
        $this->assertSame([], $this->proposals('Le joueur se sent bien aujourd’hui, 62 et 98'), 'un nombre sans mesure nommée n’est pas proposé');
        $this->assertSame([], $this->proposals('Tension 300 sur 20'), 'valeur hors des bornes physiologiques');
    }

    public function test_transcription_goes_through_the_server_with_the_server_key(): void
    {
        config(['services.google_speech.key' => 'cle-serveur']);
        Http::fake(['speech.googleapis.com/*' => Http::response(['results' => [['alternatives' => [['transcript' => 'Tension 12 sur 8, fréquence cardiaque 62', 'confidence' => 0.91]]]]])]);
        $audio = UploadedFile::fake()->createWithContent('dictee.webm', 'webm-audio');

        $response = $this->actingAs($this->doctor())->post(route('pcma.dictation.transcribe'), ['audio' => $audio], ['Accept' => 'application/json'])->assertOk();

        $this->assertSame('Tension 12 sur 8, fréquence cardiaque 62', $response->json('text'));
        $this->assertSame(['blood_pressure' => '120/80', 'heart_rate' => '62'], collect($response->json('proposals'))->pluck('value', 'field')->all());
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'speech:recognize?key=cle-serveur') && $r['config']['languageCode'] === 'fr-FR'
            && $r['config']['encoding'] === 'WEBM_OPUS' && $r['audio']['content'] === base64_encode('webm-audio'));
        $this->assertFalse(DB::table('audit_logs')->where('action', 'pcma_dictation_transcribe')->whereRaw("CAST(metadata AS TEXT) LIKE '%Tension%'")->exists(), 'texte dicté jamais journalisé');
        $this->assertTrue(DB::table('audit_logs')->where('action', 'pcma_dictation_transcribe')->exists());
    }

    public function test_transcription_errors_and_access_rules(): void
    {
        $audio = fn () => UploadedFile::fake()->createWithContent('dictee.webm', 'webm-audio');
        config(['services.google_speech.key' => null]);
        $this->actingAs($this->doctor())->post(route('pcma.dictation.transcribe'), ['audio' => $audio()], ['Accept' => 'application/json'])
            ->assertStatus(503)->assertJsonFragment(['success' => false]);

        config(['services.google_speech.key' => 'cle-serveur']);
        Http::fake(['speech.googleapis.com/*' => Http::sequence()->push(['error' => ['code' => 403]], 403)->push(['results' => []])]);
        $this->actingAs($this->doctor())->post(route('pcma.dictation.transcribe'), ['audio' => $audio()], ['Accept' => 'application/json'])->assertStatus(502);
        $this->actingAs($this->doctor())->post(route('pcma.dictation.transcribe'), ['audio' => $audio()], ['Accept' => 'application/json'])->assertStatus(422);

        $this->actingAs(User::factory()->create(['role' => 'club_admin', 'status' => 'active', 'tenant_id' => 1]))
            ->postJson(route('pcma.dictation.parse'), ['text' => 'Tension 12 sur 8'])->assertForbidden();
        $this->actingAs($this->doctor())->postJson(route('pcma.dictation.parse'), ['text' => 'Tension 12 sur 8'])
            ->assertOk()->assertJsonPath('proposals.0.value', '120/80');
    }

    public function test_browser_never_calls_google_directly_and_dictation_is_reviewed(): void
    {
        $service = file_get_contents(public_path('js/SpeechRecognitionService-laravel.js'));
        $this->assertStringNotContainsString('speech.googleapis.com', $service);
        $this->assertStringContainsString('window.pcmaDictationReview(result)', $service);

        $view = file_get_contents(resource_path('views/pcma/create.blade.php'));
        $this->assertStringContainsString('window.PCMA_DICTATION', $view);
        $this->assertStringContainsString('window.pcmaDictationReview = function', $view);
        $this->assertStringNotContainsString("            integrateWithSpeechService();\n", $view, 'reconnaissance du navigateur non initialisée');
    }

    public function test_legacy_key_endpoint_never_reveals_the_provider_key(): void
    {
        \Illuminate\Support\Facades\Route::middleware('web')->group(base_path('routes/web.php'));
        app('router')->getRoutes()->refreshNameLookups();
        config(['services.google_speech.key' => 'cle-serveur']);
        $response = $this->actingAs($this->doctor())->getJson('/api/google-speech-key')->assertStatus(503);
        $this->assertArrayNotHasKey('api_key', $response->json());
        $this->assertStringNotContainsString('cle-serveur', $response->getContent());
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/google-speech-key')->assertUnauthorized();
    }
}
