<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\ClubOfficial;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Dirigeants et staff des clubs au format FIFA Connect : saisie validée,
 * entraîneur principal unique et visible dans la fiche club, PDF, périmètre.
 */
class ClubOfficialsTest extends TestCase
{
    use DatabaseTransactions;

    private int $clubId;
    private int $otherClubId;
    private int $associationId;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Schema::hasTable('club_officials')) {
            (require base_path('database/migrations/2026_10_02_150000_create_club_officials_table.php'))->up();
        }
        if (!Schema::hasTable('document_signature_requests')) {
            (require base_path('database/migrations/2026_10_03_180000_create_document_signature_requests.php'))->up();
        }
        $this->associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération test', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $this->clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club test', 'association_id' => $this->associationId, 'fifa_connect_id' => 'ORG123', 'created_at' => now(), 'updated_at' => now()]);
        $this->otherClubId = (int) DB::table('clubs')->insertGetId(['name' => 'Autre club', 'created_at' => now(), 'updated_at' => now()]);
        // La page des officiels renvoie vers la fiche club, absente des routes de test.
        if (!\Illuminate\Support\Facades\Route::has('modules.clubs.show')) {
            \Illuminate\Support\Facades\Route::get('/modules/clubs/{club}', fn () => null)->name('modules.clubs.show');
        }
        if (!\Illuminate\Support\Facades\Route::has('club-officials.sync-connect')) {
            \Illuminate\Support\Facades\Route::post('/clubs/{club}/officials/sync-connect', [\App\Http\Controllers\ClubOfficials\ClubOfficialController::class, 'syncConnect'])->name('club-officials.sync-connect');
        }
        \Illuminate\Support\Facades\Route::getRoutes()->refreshNameLookups();
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => $role, 'tenant_id' => 1, 'club_id' => null, 'association_id' => null, 'status' => 'active'], $attributes));
    }

    private function coach(array $overrides = []): array
    {
        return array_merge([
            'registration_type' => 'TeamOfficial', 'team_official_role' => 'Coach', 'is_head_coach' => '1',
            'person_fifa_id' => 'P99887', 'international_first_name' => 'Nabil', 'international_last_name' => 'Maaloul',
            'local_first_name' => 'نبيل', 'gender' => 'male', 'date_of_birth' => '1962-07-25', 'nationality' => 'tn',
            'status' => 'active', 'discipline' => 'Football', 'registration_valid_from' => '2026-07-01',
            'certification_name' => 'Licence CAF Pro', 'certification_number' => 'CAF-PRO-12', 'email' => 'coach@example.org',
        ], $overrides);
    }

    public function test_club_admin_records_the_head_coach_in_fifa_connect_format(): void
    {
        $admin = $this->user('club_admin', ['club_id' => $this->clubId]);
        $this->actingAs($admin)->post(route('club-officials.store', $this->clubId), $this->coach())->assertRedirect();
        $coach = ClubOfficial::query()->firstOrFail();
        $this->assertSame('TN', $coach->nationality, 'code pays normalisé en ISO majuscules');
        $this->assertSame('TeamOfficial', $coach->certification_type);
        $this->assertTrue($coach->is_head_coach);

        $this->actingAs($admin)->get(route('club-officials.show', [$this->clubId, $coach->id]))->assertOk()
            ->assertSee('PersonFIFAId')->assertSee('P99887')->assertSee('TeamOfficialRole')->assertSee('Coach · Entraîneur principal')
            ->assertSee('OrganisationFIFAId ORG123')->assertSee('Licence CAF Pro');
        $this->assertStringStartsWith('%PDF-', $this->actingAs($admin)->get(route('club-officials.pdf', [$this->clubId, $coach->id]))->assertOk()->getContent());

        // Encart « Entraîneur principal » de la fiche club
        $this->actingAs($admin);
        $html = view('club-officials.partials.head-coach', ['club' => Club::findOrFail($this->clubId)])->render();
        $this->assertStringContainsString('Nabil Maaloul', $html);
        $this->assertStringContainsString('format FIFA Connect', $html);
    }

    public function test_only_one_head_coach_and_roles_are_validated(): void
    {
        $admin = $this->user('club_admin', ['club_id' => $this->clubId]);
        $this->actingAs($admin)->post(route('club-officials.store', $this->clubId), $this->coach())->assertRedirect();
        $this->actingAs($admin)->post(route('club-officials.store', $this->clubId), $this->coach(['person_fifa_id' => null, 'international_first_name' => 'Sami', 'international_last_name' => 'Trabelsi']))->assertRedirect();
        $this->assertSame(1, ClubOfficial::query()->where('club_id', $this->clubId)->where('is_head_coach', true)->count());
        $this->assertSame('Trabelsi', ClubOfficial::query()->where('is_head_coach', true)->value('international_last_name'));

        $this->actingAs($admin)->post(route('club-officials.store', $this->clubId), $this->coach(['team_official_role' => 'Magicien']))->assertSessionHasErrors('team_official_role');
        $this->actingAs($admin)->post(route('club-officials.store', $this->clubId), $this->coach(['nationality' => 'TUN']))->assertSessionHasErrors('nationality');
        $this->actingAs($admin)->post(route('club-officials.store', $this->clubId), [
            'registration_type' => 'OrganisationOfficial', 'organisation_official_role' => 'President', 'international_first_name' => 'Ali', 'international_last_name' => 'Ben Salah',
            'gender' => 'male', 'date_of_birth' => '1970-01-01', 'nationality' => 'TN', 'status' => 'active', 'discipline' => 'Football', 'registration_valid_from' => '2025-01-01',
        ])->assertRedirect();
        $president = ClubOfficial::query()->where('registration_type', 'OrganisationOfficial')->firstOrFail();
        $this->assertSame('President', $president->organisation_official_role);
        $this->assertNull($president->team_official_role);
        $this->assertFalse($president->is_head_coach);
    }

    public function test_signature_workflow_is_scoped_to_club_and_federation_admins(): void
    {
        $admin = $this->user('club_admin', ['club_id' => $this->clubId]);
        $this->actingAs($admin)->post(route('club-officials.store', $this->clubId), $this->coach())->assertRedirect();
        $coach = ClubOfficial::query()->firstOrFail();

        $this->actingAs($admin)->get(route('club-officials.show', [$this->clubId, $coach]))->assertOk()
            ->assertSee('Certification numérique de la fiche')->assertSee('prêt pour le Go Live');
        $this->actingAs($this->user('club_manager', ['club_id' => $this->clubId]))
            ->post(route('club-officials.digital-signature', [$this->clubId, $coach]), ['provider' => 'adobe_sign'])->assertForbidden();
        $this->actingAs($this->user('club_admin', ['club_id' => $this->otherClubId]))
            ->post(route('club-officials.digital-signature', [$this->clubId, $coach]), ['provider' => 'adobe_sign'])->assertForbidden();
    }

    public function test_club_admin_syncs_connect_team_doctor_into_club_officials(): void
    {
        if (!Schema::hasTable('fifa_connect_persons') || !Schema::hasTable('fifa_connect_registrations')) {
            $this->markTestSkipped('FIFA Connect canonical tables unavailable.');
        }

        $person = \App\Models\FifaConnect\Person::query()->create([
            'person_fifa_id' => 'MEDCONNECT84',
            'international_first_name' => 'Leila',
            'international_last_name' => 'Medecin',
            'gender' => 'Female',
            'nationality' => 'FR',
            'date_of_birth' => '1985-03-14',
            'country_of_birth' => 'FR',
            'place_of_birth' => 'Paris',
        ]);
        \App\Models\FifaConnect\Registration::query()->create([
            'person_id' => $person->id,
            'person_fifa_id' => $person->person_fifa_id,
            'organisation_fifa_id' => 'ORG123',
            'registration_type' => 'TeamOfficial',
            'status' => 'active',
            'registration_valid_from' => '2026-07-01',
            'discipline' => 'Football',
            'team_official_role' => 'TeamDoctor',
        ]);

        $admin = $this->user('club_admin', ['club_id' => $this->clubId]);
        $result = app(\App\Services\ClubOfficials\SyncFifaConnectOfficials::class)
            ->sync(Club::findOrFail($this->clubId), $admin);
        $this->assertSame(1, $result['created']);

        $official = ClubOfficial::query()->where('club_id', $this->clubId)->where('person_fifa_id', 'MEDCONNECT84')->firstOrFail();
        $this->assertSame('TeamOfficial', $official->registration_type);
        $this->assertSame('TeamDoctor', $official->team_official_role);
        $this->assertSame('FIFAConnect', $official->source);
        $this->assertSame('Leila Medecin', $official->fullName());

        $this->actingAs($admin)
            ->get(route('club-officials.club', $this->clubId))
            ->assertOk()
            ->assertSee('Leila Medecin')
            ->assertSee('TeamDoctor')
            ->assertSee('MEDCONNECT84')
            ->assertSee('Connect');
    }

    public function test_access_follows_club_and_federation(): void
    {
        $this->actingAs($this->user('club_admin', ['club_id' => $this->clubId]))->post(route('club-officials.store', $this->clubId), $this->coach())->assertRedirect();
        $coach = ClubOfficial::query()->firstOrFail();

        $this->actingAs($this->user('club_admin', ['club_id' => $this->otherClubId]))->get(route('club-officials.club', $this->clubId))->assertForbidden();
        $this->actingAs($this->user('club_admin', ['club_id' => $this->otherClubId]))->post(route('club-officials.store', $this->clubId), $this->coach())->assertForbidden();
        $manager = $this->user('club_manager', ['club_id' => $this->clubId]);
        $this->actingAs($manager)->get(route('club-officials.club', $this->clubId))->assertOk()->assertSee('Nabil Maaloul')->assertDontSee('+ Membre du staff');
        $this->actingAs($manager)->get(route('club-officials.edit', [$this->clubId, $coach->id]))->assertForbidden();
        $this->actingAs($this->user('association_admin', ['association_id' => $this->associationId]))->get(route('club-officials.edit', [$this->clubId, $coach->id]))->assertOk();
        $this->actingAs($this->user('player'))->get(route('club-officials.index'))->assertForbidden();
        $this->actingAs($manager)->get(route('club-officials.index'))->assertRedirect(route('club-officials.club', $this->clubId));
        $this->actingAs($this->user('club_admin', ['club_id' => $this->otherClubId]))->get(route('club-officials.show', [$this->otherClubId, $coach->id]))->assertNotFound();
    }
}
