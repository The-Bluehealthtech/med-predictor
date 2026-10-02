<?php
namespace Tests\Feature;

use App\Models\{HealthRecord, TUERequest, User};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Route, Schema};
use Tests\TestCase;

/**
 * AUT : formulaire FIFA rempli en PDF (demande enregistrée ou aperçu) et lien
 * de dépôt vers ADAMS, dans le périmètre médical du club.
 */
final class MedicalAutPdfTest extends TestCase
{
    private string $previous;

    protected function setUp(): void
    {
        parent::setUp();
        $root = dirname(__DIR__, 2);
        config(['medical_aut' => require $root . '/config/medical_aut.php',
            'medical_sections' => require $root . '/config/medical_sections.php']);
        $this->app->instance('translation.loader', new \Illuminate\Translation\FileLoader($this->app['files'], $root . '/resources/lang'));
        $this->app->forgetInstance('translator');
        app('view')->getFinder()->setPaths([$root . '/resources/views']);
        Route::middleware('web')->group($root . '/routes/web.php');
        Route::getRoutes()->refreshNameLookups();

        $this->previous = DB::getDefaultConnection();
        config()->set('database.connections.aut_pdf', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        DB::setDefaultConnection('aut_pdf');
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('role'); $t->unsignedBigInteger('club_id')->nullable();
            $t->unsignedBigInteger('association_id')->nullable(); $t->string('fifa_connect_id')->nullable();
        });
        Schema::create('notifications', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->string('type'); $t->morphs('notifiable');
            $t->text('data'); $t->timestamp('read_at')->nullable(); $t->timestamps();
        });
        Schema::create('clubs', fn (Blueprint $t) => [$t->id(), $t->string('name'), $t->unsignedBigInteger('association_id')->nullable()]);
        Schema::create('players', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('first_name'); $t->string('last_name');
            $t->unsignedBigInteger('club_id')->nullable(); $t->string('fifa_connect_id')->nullable();
        });
        Schema::create('health_records', function (Blueprint $t) {
            $t->id();
            foreach ((new HealthRecord)->getFillable() as $field) {
                if ($field !== 'icd11_diagnoses') $t->text($field)->nullable();
            }
            $t->timestamps();
        });
        (require $root . '/database/migrations/2024_01_15_000006_create_tue_requests_table.php')->up();
        (require $root . '/database/migrations/2026_10_01_000001_add_icd11_and_aut_to_health_records.php')->up();
        \Illuminate\Support\Facades\Event::fake([\App\Events\HealthRecordCreated::class]);

        DB::table('clubs')->insert([['id' => 1, 'name' => 'Club A', 'association_id' => 1], ['id' => 2, 'name' => 'Club B', 'association_id' => 2]]);
        DB::table('players')->insert([
            ['id' => 10, 'name' => 'Sami Ben Ali', 'first_name' => 'Sami', 'last_name' => 'Ben Ali', 'club_id' => 1],
            ['id' => 20, 'name' => 'Autre Joueur', 'first_name' => 'Autre', 'last_name' => 'Joueur', 'club_id' => 2],
        ]);
        DB::table('users')->insert(['id' => 1, 'name' => 'Médecin du club', 'role' => 'club_medical', 'club_id' => 1]);
        $this->actingAs(User::findOrFail(1)->forceFill(['tenant_id' => 1]));
    }

    protected function tearDown(): void
    {
        DB::purge('aut_pdf');
        DB::setDefaultConnection($this->previous);
        parent::tearDown();
    }

    private function record(int $player = 10): HealthRecord
    {
        return HealthRecord::create(['player_id' => $player, 'user_id' => 1, 'status' => 'active',
            'record_date' => '2026-09-30', 'diagnosis' => 'Asthme d’effort']);
    }

    private function form(): array
    {
        return ['surname' => 'Ben Ali', 'given_names' => 'Sami', 'sex' => 'male', 'birth_date' => '2001-04-12',
            'nationality' => 'Tunisienne', 'testing_group' => 'fifa_competition', 'diagnosis' => 'Asthme d’effort documenté par spirométrie',
            'substance_1' => 'Terbutaline', 'dose_1' => '0,5 mg', 'route_1' => 'Inhalation', 'frequency_1' => 'Si besoin', 'duration_1' => '12 mois',
            'physician_name' => 'Dr Trabelsi', 'player_declaration_name' => 'Sami Ben Ali'];
    }

    public function test_create_page_offers_pdf_preview_and_adams_link(): void
    {
        $record = $this->record();
        $this->get(route('medical-aut.create', $record->id))->assertOk()
            ->assertSee(route('medical-aut.preview', $record->id), false)
            ->assertSee('name="then" value="pdf"', false)
            ->assertSee('href="https://adams.wada-ama.org/" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee(config('medical_aut.adams_help_url'), false);
    }

    public function test_saving_with_pdf_then_downloading_a_real_filled_pdf(): void
    {
        $record = $this->record();
        $this->post(route('medical-aut.store', $record->id), ['form' => $this->form(), 'then' => 'pdf'])
            ->assertRedirect(route('medical-aut.index', $record->id))->assertSessionHas('aut_pdf');
        $aut = TUERequest::firstOrFail();

        $this->get(route('medical-aut.index', $record->id))->assertOk()
            ->assertSee(route('medical-aut.pdf', [$record->id, $aut->id]), false)
            ->assertSee('https://adams.wada-ama.org/', false);

        $pdf = $this->get(route('medical-aut.pdf', [$record->id, $aut->id]))->assertOk();
        $this->assertSame('application/pdf', $pdf->headers->get('Content-Type'));
        $this->assertStringContainsString('AUT-FIFA-' . $aut->id . '.pdf', $pdf->headers->get('Content-Disposition'));
        $this->assertStringContainsString('no-store', $pdf->headers->get('Cache-Control'));
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        $this->assertGreaterThan(5000, strlen($pdf->getContent()));

        // Contenu du gabarit : valeurs saisies, cases cochées, déclarations, cadres de signature vides.
        app()->setLocale('fr');
        $html = view('health-records.aut-pdf', ['healthRecord' => $record, 'item' => $aut, 'fields' => $aut->aut_form_data['fields'],
            'sections' => config('medical_aut.sections'), 'preview' => false, 'generatedAt' => now(),
            'source' => json_decode(file_get_contents(config('medical_aut.source_directory') . '/fifa-aut-fr-2024-text.json'), true)])->render();
        foreach (['Terbutaline', '0,5 mg', 'Inhalation', '12/04/2001', 'Dr Trabelsi', '☒ Homme', '☐ Femme', 'Je soussigné(e), Sami Ben Ali,', 'Déclaration de confidentialité', 'adams.wada-ama.org'] as $expected) {
            $this->assertStringContainsString($expected, $html);
        }
        $this->assertStringNotContainsString('APERÇU', $html);
    }

    public function test_preview_renders_unsaved_form_from_create_and_edit_without_persisting(): void
    {
        $record = $this->record();
        $preview = $this->post(route('medical-aut.preview', $record->id), ['form' => $this->form()])->assertOk();
        $this->assertStringStartsWith('%PDF-', $preview->getContent());
        $this->assertStringContainsString('AUT-FIFA-apercu.pdf', $preview->headers->get('Content-Disposition'));
        $this->put(route('medical-aut.preview', $record->id), ['form' => $this->form()])->assertOk();
        $this->assertSame(0, TUERequest::count(), 'un aperçu n’enregistre rien');
        $this->post(route('medical-aut.preview', $record->id), ['form' => ['birth_date' => 'pas une date']])->assertSessionHasErrors('form.birth_date');
    }

    public function test_pdf_stays_inside_the_club_medical_scope(): void
    {
        $other = $this->record(20);
        $aut = TUERequest::create(['player_id' => 20, 'health_record_id' => $other->id, 'status' => 'pending', 'physician_id' => 1, 'request_date' => '2026-09-30',
            'medication' => 'X', 'aut_form_data' => ['fields' => $this->form()]]);
        $this->get(route('medical-aut.pdf', [$other->id, $aut->id]))->assertNotFound();
        $this->post(route('medical-aut.preview', $other->id), ['form' => $this->form()])->assertNotFound();

        $own = $this->record();
        $this->get(route('medical-aut.pdf', [$own->id, $aut->id]))->assertNotFound();
    }
}
