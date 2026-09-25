<?php

namespace Tests\Feature;

use App\Http\Controllers\PCMAController;
use App\Models\FifaConnect\Person;
use App\Models\FifaConnect\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use ReflectionMethod;
use Tests\TestCase;

class PcmaTeamDoctorIdentityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_only_active_matching_team_doctor_registration_establishes_identity(): void
    {
        $doctor = User::factory()->create(['role' => 'team_doctor', 'fifa_connect_id' => 'ABC123A']);
        $method = new ReflectionMethod(PCMAController::class, 'activeTeamDoctorRegistration');
        $controller = new PCMAController();
        $this->assertNull($method->invoke($controller, $doctor));
        $person = Person::create([
            'person_fifa_id' => 'ABC123A', 'gender' => 'male',
            'nationality' => 'FRA', 'date_of_birth' => '1980-01-01',
            'country_of_birth' => 'FRA', 'place_of_birth' => 'Paris',
        ]);
        $registration = Registration::create([
            'person_id' => $person->id, 'person_fifa_id' => 'ABC123A',
            'organisation_fifa_id' => 'CLB123A',
            'registration_type' => Registration::TYPE_TEAM_OFFICIAL,
            'team_official_role' => 'TeamDoctor', 'discipline' => 'Football',
            'status' => 'active', 'registration_valid_from' => '2020-01-01',
        ]);
        $this->assertSame($registration->id, $method->invoke($controller, $doctor)?->id);
        $registration->update(['team_official_role' => 'Coach']);
        $this->assertNull($method->invoke($controller, $doctor));
        $registration->update(['team_official_role' => 'TeamDoctor', 'registration_valid_to' => '2020-12-31']);
        $this->assertNull($method->invoke($controller, $doctor));
        $registration->update(['registration_valid_to' => null, 'person_fifa_id' => 'WRONG1A']);
        $this->assertNull($method->invoke($controller, $doctor));
    }

}
