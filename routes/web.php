<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Redirect root to users dashboard
Route::get('/', function () {
    return redirect()->route('users.dashboard');
});

// Users Management Dashboard (No Auth Required)
Route::get('/users', [UserController::class, 'dashboard'])->name('users.dashboard');
