<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public function login(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(
            Str::lower($credentials['email']).'|'.request()->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            $this->addError(
                'email',
                "Demasiados intentos. Prueba de nuevo en {$seconds} segundos."
            );

            return;
        }

        if (! Auth::attempt($credentials)) {
            RateLimiter::increment($throttleKey, 60);
            $this->addError('email', 'Las credenciales no son correctas.');

            return;
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        $this->redirect(route('index'));
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
