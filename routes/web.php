<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Links\Create;
use App\Livewire\Links\Edit;
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

Route::post('/logout', LogoutController::class)
    ->middleware('auth')
    ->name('logout');

Route::livewire('/links/create', Create::class)
    ->middleware('auth')
    ->name('links.create');

Route::livewire('/links/{link}/edit', Edit::class)
    ->middleware('auth')
    ->name('links.edit');
