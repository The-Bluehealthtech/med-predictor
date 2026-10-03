<?php

namespace Tests\Feature;

use App\Http\Controllers\MedicalSecretaryController;
use App\Models\Appointment;
use App\Models\PCMA;
use App\Models\Player;
use App\Models\PlayerLicense;
use App\Models\User;
use App\Models\Visit;
use App\Services\Licensing\PcmaRequirement;
use App\Services\Medical\PcmaVisit;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Motif « PCMA » du secrétariat médical : la visite ouvre le PCMA du médecin,
 * la signature clôt la visite et le PCMA signé lève la condition des licences.
 */
class PcmaVisitTest extends TestCase
{
    use DatabaseTransactions;

    private int $clubId;

    private int $playerId;

    private int $athleteId;

    private User $secretary;

    private User $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        Route::get('/_t/pcma/create', fn () => 'ok')->name('pcma.create');
        Route::get('/_t/pcma/{pcma}/edit', fn () => 'ok')->name('pcma.edit');
        Route::get('/_t/health-records/create', fn () => 'ok')->name('health-records.create');
        app('router')->getRoutes()->refreshNameLookups();

        $associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération PCMA', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $this->clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club PCMA', 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->playerId = (int) DB::table('players')->insertGetId(['name' => 'Samir Visite', 'first_name' => 'Samir', 'last_name' => 'Visite', 'gender' => 'male',
            'date_of_birth' => now()->subYears(25)->toDateString(), 'club_id' => $this->clubId, 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $teamId = DB::table('teams')->value('id') ?? DB::table('teams')->insertGetId(['name' => 'Équipe PCMA', 'club_id' => $this->clubId, 'created_at' => now(), 'updated_at' => now()]);
        $this->athleteId = (int) DB::table('athletes')->insertGetId(['name' => 'Samir Visite', 'dob' => '2001-04-12', 'nationality' => 'TN', 'team_id' => $teamId,
            'player_id' => $this->playerId, 'created_at' => now(), 'updated_at' => now()]);
        $this->secretary = User::factory()->create(['role' => 'secretary', 'club_id' => $this->clubId, 'status' => 'active']);
        $this->doctor = User::factory()->create(['role' => 'club_medical', 'club_id' => $this->clubId, 'status' => 'active']);
    }

    private function visit(string $type = 'pcma', string $status = 'Enregistré'): Visit
    {
        $appointment = Appointment::create(['athlete_id' => $this->athleteId, 'created_by' => $this->secretary->id, 'doctor_id' => $this->doctor->id,
            'appointment_date' => now(), 'appointment_type' => $type, 'status' => $status, 'reason' => 'PCMA licence']);

        return Visit::create(['athlete_id' => $this->athleteId, 'appointment_id' => $appointment->id, 'doctor_id' => $this->doctor->id,
            'visit_date' => now(), 'visit_type' => $type, 'status' => $status, 'administrative_data' => ['player_id' => $this->playerId]]);
    }

    private function pcma(Visit $visit, bool $signed): PCMA
    {
        return PCMA::create(['athlete_id' => $this->athleteId, 'player_id' => $this->playerId, 'assessor_id' => $this->doctor->id, 'visit_id' => $visit->id,
            'type' => 'pcma', 'status' => $signed ? 'completed' : 'pending', 'result_json' => ['overall_score' => 90], 'assessment_date' => now()->toDateString(),
            'final_statement' => $signed ? ['overall_decision' => 'FIT'] : null, 'is_signed' => $signed, 'signed_at' => $signed ? now() : null]);
    }

    public function test_pcma_visit_is_linkable_only_for_the_player_while_open(): void
    {
        $service = app(PcmaVisit::class);
        $visit = $this->visit();
        $this->assertSame($visit->id, $service->linkable($visit->id, $this->playerId)->id);

        foreach ([[$this->visit('consultation')->id, $this->playerId], [$visit->id, $this->playerId + 1000], [$this->visit('pcma', 'Terminé')->id, $this->playerId]] as [$id, $player]) {
            try {
                $service->linkable($id, $player);
                $this->fail('visite non rattachable acceptée');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('visit_id', $e->errors());
            }
        }
    }

    public function test_doctor_signature_closes_the_visit_and_lifts_the_licence_condition(): void
    {
        $visit = $this->visit();
        $license = PlayerLicense::query()->create(['player_id' => $this->playerId, 'club_id' => $this->clubId, 'registration_type' => 'Player', 'discipline' => 'Football',
            'level' => 'pro', 'registration_nature' => 'Registration', 'request_reason' => 'first', 'status' => 'pending', 'season' => '2026-2027']);
        $this->assertTrue(app(PcmaRequirement::class)->check($license)['blocking'], 'senior pro : PCMA exigé');

        $draft = $this->pcma($visit, false);
        $this->assertSame('Enregistré', $visit->fresh()->status, 'un brouillon ne clôt pas la visite');
        $draft->delete();

        $pcma = $this->pcma($visit, true);
        $visit->refresh();
        $this->assertSame('Terminé', $visit->status);
        $this->assertSame($pcma->id, $visit->administrative_data['pcma_id']);
        $this->assertSame('Terminé', $visit->appointment->fresh()->status);

        $check = app(PcmaRequirement::class)->check($license->fresh());
        $this->assertFalse($check['blocking']);
        $this->assertSame('valid', $check['status']['state']);
        $this->assertSame($visit->visit_date->toDateString(), $check['status']['visit_date']->toDateString());

        $this->expectException(ValidationException::class);
        app(PcmaVisit::class)->linkable($visit->id, $this->playerId); // visite clôturée
    }

    public function test_receiving_a_pcma_visit_opens_the_pcma_form_linked_to_the_visit(): void
    {
        $this->actingAs($this->secretary);
        $visit = $this->visit();
        $response = app(MedicalSecretaryController::class)->receive($visit->appointment);
        $this->assertStringContainsString('pcma/create?visit_id=' . $visit->id, $response->getTargetUrl());
        $this->assertSame('En cours', $visit->fresh()->status);

        $draft = $this->pcma($visit, false);
        $response = app(MedicalSecretaryController::class)->receive($visit->appointment->fresh());
        $this->assertStringContainsString("pcma/{$draft->id}/edit", $response->getTargetUrl(), 'reprise du brouillon de la visite');
    }

    public function test_secretary_sees_licence_requests_waiting_for_a_pcma_until_a_visit_is_planned(): void
    {
        $this->actingAs($this->secretary);
        PlayerLicense::query()->create(['player_id' => $this->playerId, 'club_id' => $this->clubId, 'registration_type' => 'Player', 'discipline' => 'Football',
            'level' => 'pro', 'registration_nature' => 'Registration', 'request_reason' => 'first', 'status' => 'pending', 'season' => '2026-2027']);
        $needed = new ReflectionMethod(MedicalSecretaryController::class, 'pcmaNeeded');

        $rows = $needed->invoke(app(MedicalSecretaryController::class));
        $this->assertSame([$this->playerId], $rows->pluck('player.id')->all());
        $this->assertSame($this->athleteId, $rows->first()['athlete_id']);
        $this->assertSame('missing', $rows->first()['status']['state']);

        $this->visit('pcma', 'Planifié');
        $this->assertCount(0, $needed->invoke(app(MedicalSecretaryController::class)), 'visite PCMA déjà planifiée');
    }
}
