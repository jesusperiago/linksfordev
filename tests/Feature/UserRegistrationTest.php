<?php

namespace Tests\Feature;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guest_can_view_registration_form(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Crear cuenta');
    }

    public function test_registration_creates_user_authenticates_and_redirects(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Ada Lovelace')
            ->set('email', 'ada@example.test')
            ->set('password', 'a-secure-password')
            ->set('password_confirmation', 'a-secure-password')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('index'));

        $user = User::query()
            ->where('email', 'ada@example.test')
            ->firstOrFail();

        $this->assertDatabaseHas('users', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.test',
        ]);
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('a-secure-password', $user->password));
    }

    public function test_registration_rejects_an_email_already_in_use(): void
    {
        User::factory()->create([
            'email' => 'taken@example.test',
        ]);

        Livewire::test(Register::class)
            ->set('name', 'Ada Lovelace')
            ->set('email', 'taken@example.test')
            ->set('password', 'a-secure-password')
            ->set('password_confirmation', 'a-secure-password')
            ->call('register')
            ->assertHasErrors('email');

        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    public function test_registration_rejects_mismatched_password_confirmation(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Ada Lovelace')
            ->set('email', 'ada@example.test')
            ->set('password', 'a-secure-password')
            ->set('password_confirmation', 'a-different-password')
            ->call('register')
            ->assertHasErrors('password');

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registration_rejects_passwords_shorter_than_eight_characters(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Ada Lovelace')
            ->set('email', 'ada@example.test')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('register')
            ->assertHasErrors('password');

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registration_requires_name_and_email(): void
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('email', '')
            ->set('password', 'a-secure-password')
            ->set('password_confirmation', 'a-secure-password')
            ->call('register')
            ->assertHasErrors([
                'name' => ['required'],
                'email' => ['required'],
            ]);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registration_requires_a_valid_email(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Ada Lovelace')
            ->set('email', 'not-an-email')
            ->set('password', 'a-secure-password')
            ->set('password_confirmation', 'a-secure-password')
            ->call('register')
            ->assertHasErrors([
                'email' => ['email'],
            ]);

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_from_registration_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('register'))
            ->assertRedirect(route('index'));
    }
}
