<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Links\Index;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', Index::class)->name('index');

Route::livewire('/register', Register::class)
    ->middleware('guest')
    ->name('register');

Route::livewire('/login', Login::class)
    ->middleware('guest')
    ->name('login');
