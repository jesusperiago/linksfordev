<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class UserLogoutTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_user_can_log_out_and_session_is_invalidated(): void
    {
        $user = User::factory()->create();

        $response = $this->withSession([
            'logout_probe' => 'temporary',
        ])
            ->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect(route('index'));
        $response->assertSessionMissing('logout_probe');
        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login_when_trying_to_log_out(): void
    {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
