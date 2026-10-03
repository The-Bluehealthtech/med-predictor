<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use App\Services\MedicalFileStore;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Avis spécialiste et kinésithérapie prescrits en consultation : courrier d'adressage et
 * prescription en PDF établis au nom du médecin, joints à la visite, ouvrables depuis le dossier.
 */
class ClinicalOrderDocumentsTest extends TestCase
{
    use DatabaseTransactions;

    private int $player;

    private int $visit;

    private User $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        Route::middleware('web')->group(base_path('routes/web.php'));
        app('router')->getRoutes()->refreshNameLookups();
        app('router')->getRoutes()->refreshActionLookups();
        $now = now();
        $assoc = DB::table('associations')->insertGetId(['name' => 'Fédération Ordonnances', 'country' => 'Tunisie', 'created_at' => $now, 'updated_at' => $now]);
        $club = DB::table('clubs')->insertGetId(['name' => 'Club Ordonnances', 'association_id' => $assoc, 'created_at' => $now, 'updated_at' => $now]);
        $team = DB::table('teams')->insertGetId(['name' => 'Équipe Ordonnances', 'club_id' => $club, 'created_at' => $now, 'updated_at' => $now]);
        $this->player = DB::table('players')->insertGetId(['name' => 'Samir Ordonnance', 'first_name' => 'Samir', 'last_name' => 'Ordonnance', 'date_of_birth' => '2000-05-14',
            'club_id' => $club, 'association_id' => $assoc, 'created_at' => $now, 'updated_at' => $now]);
        $athlete = DB::table('athletes')->insertGetId(['name' => 'Samir Ordonnance', 'dob' => '2000-05-14', 'nationality' => 'TN', 'team_id' => $team, 'player_id' => $this->player, 'created_at' => $now, 'updated_at' => $now]);
        $this->doctor = User::factory()->create(['role' => 'club_medical', 'club_id' => $club, 'association_id' => $assoc, 'status' => 'active', 'tenant_id' => 1, 'name' => 'Amine Médecin']);
        $appointment = DB::table('appointments')->insertGetId(['athlete_id' => $athlete, 'created_by' => $this->doctor->id, 'doctor_id' => $this->doctor->id, 'appointment_date' => $now,
            'appointment_type' => 'consultation', 'status' => 'En cours', 'created_at' => $now, 'updated_at' => $now]);
        $this->visit = DB::table('visits')->insertGetId(['athlete_id' => $athlete, 'appointment_id' => $appointment, 'doctor_id' => $this->doctor->id, 'visit_date' => $now,
            'visit_type' => 'consultation', 'status' => 'En cours', 'created_at' => $now, 'updated_at' => $now]);
    }

    private function consultation(array $extra): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->doctor)->post(route('health-records.store'), $extra + [
            'player_id' => $this->player, 'visit_id' => $this->visit, 'visit_date' => now()->toDateString(), 'record_date' => now()->toDateString(), 'doctor_name' => 'Amine Médecin',
            'visit_type' => 'consultation', 'status' => 'active',
        ]);
    }

    public function test_referral_letter_and_physiotherapy_prescription_are_issued_and_attached(): void
    {
        $this->consultation([
            'prescribed_modules' => ['specialist', 'physiotherapy', 'scat'],
            'referral_specialty' => 'orthopedie', 'referral_reason' => 'Instabilité chronique de la cheville droite après trois entorses. Avis chirurgical ?', 'referral_urgency' => 'urgent',
            'physio_indication' => 'Rééducation après entorse latérale de la cheville droite', 'physio_sessions' => 12, 'physio_frequency' => '3 séances par semaine',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $documents = Document::query()->where('visit_id', $this->visit)->orderBy('id')->get();
        $this->assertSame([['referral', 'Courrier d’adressage — Chirurgie orthopédique'], ['prescription', 'Prescription de kinésithérapie']],
            $documents->map(fn ($d) => [$d->document_type, $d->description])->all());
        foreach ($documents as $document) {
            $file = app(MedicalFileStore::class)->read($document->file_path);
            $this->assertStringStartsWith('%PDF-', $file['bytes']);
            $this->assertSame(['fit_prescription', $this->doctor->id], [$document->metadata['source'], $document->metadata['issued_by']]);
        }
        $this->assertSame(2, DB::table('audit_logs')->where('action', 'clinical_order_document')->count());

        $this->actingAs($this->doctor)->get(route('medical-files.document', $documents[0]))->assertOk()->assertSee('<iframe', false);
    }

    public function test_reason_and_indication_are_required_when_the_act_is_prescribed(): void
    {
        $this->consultation(['prescribed_modules' => ['specialist', 'physiotherapy']])
            ->assertSessionHasErrors(['referral_specialty', 'referral_reason', 'physio_indication']);
        $this->assertSame(0, Document::query()->where('visit_id', $this->visit)->count());

        $this->consultation(['prescribed_modules' => ['scat']])->assertSessionHasNoErrors();
        $this->assertSame(0, Document::query()->where('visit_id', $this->visit)->count(), 'aucun document sans acte prescrit');
    }
}
