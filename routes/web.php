<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;

// Redirect root to users dashboard
Route::get('/', function () {
    return redirect()->route('users.dashboard');
});

// Users Management Dashboard (No Auth Required)
Route::get('/users', [UserController::class, 'dashboard'])->name('users.dashboard');

// DEBUG ROUTE - Remove after fixing
Route::get('/debug-users', function () {
    $firebaseService = app(\App\Services\FirebaseService::class);
    $users = $firebaseService->getCollection('users');

    return response()->json([
        'total_count' => count($users),
        'users' => $users,
        'message' => 'If total_count is 2, Firestore API only returning 2 docs. If 7, problem is elsewhere.'
    ]);
});
