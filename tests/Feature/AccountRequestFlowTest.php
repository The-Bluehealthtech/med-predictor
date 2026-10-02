<?php

namespace Tests\Feature;

use App\Models\AccountRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountRequestFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_account_request_is_persisted_and_recent_duplicate_is_reused(): void
    {
        $payload = [
            'first_name' => 'Test',
            'last_name' => 'Requester',
            'email' => 'requester@example.test',
            'phone' => '+687000000',
            'organization_name' => 'FIT Test Club',
            'organization_type' => 'club',
            'football_type' => '11-a-side',
            'fifa_connect_type' => 'club_admin',
            'city' => 'Noumea',
            'description' => 'Test request',
        ];

        $first = $this->postJson('/account-request', $payload);

        $first->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['message', 'request_id']);

        $this->assertDatabaseHas('account_requests', [
            'email' => 'requester@example.test',
            'first_name' => 'Test',
            'last_name' => 'Requester',
            'status' => AccountRequest::STATUS_PENDING,
        ]);

        $requestId = $first->json('request_id');

        $second = $this->postJson('/account-request', $payload);
        $second->assertCreated()
            ->assertJsonPath('request_id', $requestId);

        $this->assertDatabaseCount('account_requests', 1);
    }
}
