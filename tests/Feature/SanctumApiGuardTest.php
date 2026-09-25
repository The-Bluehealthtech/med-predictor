<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SanctumApiGuardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_api_rejects_guest_and_accepts_authorized_token(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();

        $user = User::factory()->create(['role' => 'system_admin']);
        $token = $user->createToken('api-test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $user->id);

        $athlete = \App\Models\Athlete::create([
            'fifa_id' => 'test-api-athlete-'.$user->id,
            'name' => 'API Test Athlete',
            'dob' => '2000-01-01',
            'nationality' => 'FRA',
            'team_id' => 999999,
        ]);

        $this->getJson("/api/v1/athletes/{$athlete->id}/pcmas")
            ->assertOk()->assertJsonPath('data', []);
        $this->getJson("/api/v1/athletes/{$athlete->id}/pcmas/statistics")
            ->assertOk()->assertJsonPath('data.total_pcmas', 0);

        $nonMedical = User::factory()->create(['role' => 'club_admin']);
        auth()->forgetGuards();
        $this->withToken($nonMedical->createToken('unauthorized-api-test')->plainTextToken)
            ->getJson("/api/v1/athletes/{$athlete->id}/pcmas")
            ->assertForbidden();
    }
}
