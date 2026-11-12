<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_can_view_login_form(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Iniciar sesión');
    }

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'ada@example.test',
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_show_a_generic_error_without_authenticating(): void
    {
        $user = User::factory()->create([
            'email' => 'ada@example.test',
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email')
            ->assertSee('Las credenciales no son correctas.');

        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'ada@example.test',
        ]);

        $login = Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $login->call('login')
                ->assertSee('Las credenciales no son correctas.');
        }

        $login->call('login')
            ->assertSee('Demasiados intentos. Prueba de nuevo en');

        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('index'));
    }

    public function test_login_requires_email_and_password(): void
    {
        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors([
                'email' => ['required'],
                'password' => ['required'],
            ]);

        $this->assertGuest();
    }

    public function test_login_rejects_an_invalid_email_format(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'not-an-email')
            ->set('password', 'some-password')
            ->call('login')
            ->assertHasErrors([
                'email' => ['email'],
            ]);

        $this->assertGuest();
    }
}
