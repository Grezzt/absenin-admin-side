<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\FirebaseService;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\AttendanceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User CRUD Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);                    // Get all users
    Route::post('/', [UserController::class, 'store']);                   // Create user
    Route::get('/nip/{nip}', [UserController::class, 'findByNip']);      // Find by NIP
    Route::get('/email/{email}', [UserController::class, 'findByEmail']); // Find by email
    Route::get('/{id}', [UserController::class, 'show']);                // Get user by ID
    Route::put('/{id}', [UserController::class, 'update']);              // Update user
    Route::patch('/{id}', [UserController::class, 'update']);            // Update user (partial)
    Route::delete('/{id}', [UserController::class, 'destroy']);          // Delete user
});

// Location CRUD Routes
Route::prefix('locations')->group(function () {
    Route::get('/', [LocationController::class, 'index']);           // Get all locations
    Route::post('/', [LocationController::class, 'store']);          // Create location
    Route::get('/{id}', [LocationController::class, 'show']);        // Get location by ID
    Route::put('/{id}', [LocationController::class, 'update']);      // Update location
    Route::patch('/{id}', [LocationController::class, 'update']);    // Update location (partial)
    Route::delete('/{id}', [LocationController::class, 'destroy']);  // Delete location
});

// Attendance Routes
Route::prefix('attendance')->group(function () {
    Route::get('/user/{userId}', [AttendanceController::class, 'getUserAttendance']); // Get user attendance history from subcollection
});

// Test Firebase Connection
Route::get('/test-firebase', function () {
    try {
        $credentialsPath = storage_path('app/firebase/absensi-pegawai-app-firebase-adminsdk-fbsvc-9f7f35dcee.json');

        // Check if credentials file exists
        if (!file_exists($credentialsPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credentials file not found',
                'path' => $credentialsPath
            ], 500);
        }

        // Test FirebaseService
        $firebase = new FirebaseService();

        return response()->json([
            'status' => 'success',
            'message' => 'Firebase connected successfully!',
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'credentials_path' => $credentialsPath
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// Get all employees from Firebase
Route::get('/firebase/employees', function (FirebaseService $firebase) {
    try {
        $employees = $firebase->getCollection('employees');

        return response()->json([
            'status' => 'success',
            'collection' => 'employees',
            'count' => count($employees),
            'data' => $employees
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Get all attendance from Firebase
Route::get('/firebase/attendance', function (FirebaseService $firebase) {
    try {
        $attendance = $firebase->getCollection('attendance');

        return response()->json([
            'status' => 'success',
            'collection' => 'attendance',
            'count' => count($attendance),
            'data' => $attendance
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Get employee by ID
Route::get('/firebase/employees/{id}', function ($id, FirebaseService $firebase) {
    try {
        $employee = $firebase->getDocument('employees', $id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Get attendance by employee ID
Route::get('/firebase/attendance/employee/{employeeId}', function ($employeeId, FirebaseService $firebase) {
    try {
        $attendance = $firebase->queryCollection('attendance', 'employee_id', '=', $employeeId, 10);

        return response()->json([
            'status' => 'success',
            'employee_id' => $employeeId,
            'count' => count($attendance),
            'data' => $attendance
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Get attendance by date
Route::get('/firebase/attendance/date/{date}', function ($date, FirebaseService $firebase) {
    try {
        $attendance = $firebase->queryCollection('attendance', 'date', '=', $date);

        return response()->json([
            'status' => 'success',
            'date' => $date,
            'count' => count($attendance),
            'data' => $attendance
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
