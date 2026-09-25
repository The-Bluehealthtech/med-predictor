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
    }
}
