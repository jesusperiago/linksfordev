<?php

use App\Livewire\Links\Index;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', Index::class)->name('index');
