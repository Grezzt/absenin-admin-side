<?php

namespace App\Http\Controllers;

use App\Models\FirebaseUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  protected $userModel;

  public function __construct()
  {
    $this->userModel = new FirebaseUser();
  }

  /**
   * Display a listing of users.
   * GET /api/users
   */
  public function index(Request $request)
  {
    try {
      $users = $this->userModel->all();

      // If request is API (wants JSON)
      if ($request->wantsJson() || $request->is('api/*')) {
        return response()->json([
          'success' => true,
          'message' => 'Users retrieved successfully',
          'data' => $users,
          'count' => count($users)
        ], 200);
      }

      // If request is web (wants HTML)
      return view('users.index', ['users' => $users]);
    } catch (\Exception $e) {
      if ($request->wantsJson() || $request->is('api/*')) {
        return response()->json([
          'success' => false,
          'message' => 'Failed to retrieve users',
          'error' => $e->getMessage()
        ], 500);
      }

      return back()->with('error', 'Failed to retrieve users: ' . $e->getMessage());
    }
  }

  /**
   * Show web view for managing users (dashboard)
   */
  public function dashboard()
  {
    try {
      $users = $this->userModel->all();

      // Get locations for unified admin dashboard
      $locationModel = new \App\Models\FirebaseLocation();
      $locations = $locationModel->all();

      return view('users.dashboard', [
        'users' => $users,
        'locations' => $locations
      ]);
    } catch (\Exception $e) {
      return back()->with('error', 'Failed to retrieve data: ' . $e->getMessage());
    }
  }

  /**
   * Store a newly created user.
   * POST /api/users
   */
  public function store(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'nip' => 'required|string',
        'email' => 'required|email',
        'fullName' => 'required|string',
        'password' => 'required|string|min:6',
        'faceDataBase64' => 'nullable|string',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'Validation error',
          'errors' => $validator->errors()
        ], 422);
      }

      // Check if user with same NIP already exists
      $existingUser = $this->userModel->findByNip($request->nip);
      if ($existingUser) {
        return response()->json([
          'success' => false,
          'message' => 'User with this NIP already exists'
        ], 409);
      }

      // 1. Create User in Firebase Authentication
      try {
        $auth = app('firebase.auth');
        $userProperties = [
          'email' => $request->email,
          'emailVerified' => false,
          'password' => $request->password,
          'displayName' => $request->fullName,
          'disabled' => false,
        ];
        $createdAuthUser = $auth->createUser($userProperties);
        $uid = $createdAuthUser->uid;
      } catch (\Exception $e) {
        return response()->json([
          'success' => false,
          'message' => 'Failed to create Firebase Auth user: ' . $e->getMessage()
        ], 500);
      }

      // 2. Create User Document in Firestore using the UID
      $user = $this->userModel->create($request->all(), $uid);

      return response()->json([
        'success' => true,
        'message' => 'User created successfully',
        'data' => $user
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to create user',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Display the specified user.
   * GET /api/users/{id}
   */
  public function show($id)
  {
    try {
      $user = $this->userModel->find($id);

      if (!$user) {
        return response()->json([
          'success' => false,
          'message' => 'User not found'
        ], 404);
      }

      return response()->json([
        'success' => true,
        'message' => 'User retrieved successfully',
        'data' => $user
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to retrieve user',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Update the specified user.
   * PUT/PATCH /api/users/{id}
   */
  public function update(Request $request, $id)
  {
    try {
      $validator = Validator::make($request->all(), [
        'nip' => 'sometimes|string',
        'email' => 'sometimes|email',
        'fullName' => 'sometimes|string',
        'faceDataBase64' => 'nullable|string',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'Validation error',
          'errors' => $validator->errors()
        ], 422);
      }

      // Check if user exists
      $existingUser = $this->userModel->find($id);
      if (!$existingUser) {
        return response()->json([
          'success' => false,
          'message' => 'User not found'
        ], 404);
      }

      // Check for duplicate NIP (exclude current user)
      if ($request->has('nip')) {
        $allUsers = $this->userModel->all();
        foreach ($allUsers as $user) {
          if ($user['id'] !== $id && strtolower(trim($user['data']['nip'] ?? '')) === strtolower(trim($request->nip))) {
            return response()->json([
              'success' => false,
              'message' => 'NIP sudah digunakan oleh user lain'
            ], 422);
          }
        }
      }

      // Check for duplicate email (exclude current user)
      if ($request->has('email')) {
        $allUsers = $this->userModel->all();
        foreach ($allUsers as $user) {
          if ($user['id'] !== $id && strtolower(trim($user['data']['email'] ?? '')) === strtolower(trim($request->email))) {
            return response()->json([
              'success' => false,
              'message' => 'Email sudah digunakan oleh user lain'
            ], 422);
          }
        }
      }

      // Get existing user data to preserve fields not being updated
      $existingData = $existingUser['data'] ?? [];

      // Prepare update data - only include fields that are actually being changed
      $updateData = [];

      if ($request->has('nip')) {
        $updateData['nip'] = $request->nip;
      }
      if ($request->has('fullName')) {
        $updateData['fullName'] = $request->fullName;
      }
      if ($request->has('email')) {
        $updateData['email'] = $request->email;
      }
      if ($request->has('faceDataBase64')) {
        $updateData['faceDataBase64'] = $request->faceDataBase64;
      }

      // Handle password update if provided
      if ($request->has('password') && !empty($request->password)) {
        $updateData['passwordHash'] = password_hash($request->password, PASSWORD_BCRYPT);
      }

      // Merge with existing data to preserve other fields (createdAt, role, isActive, etc.)
      $finalData = array_merge($existingData, $updateData);

      $user = $this->userModel->update($id, $finalData);

      return response()->json([
        'success' => true,
        'message' => 'User updated successfully',
        'data' => $user
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to update user',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Remove the specified user.
   * DELETE /api/users/{id}
   */
  public function destroy($id)
  {
    try {
      // Check if user exists
      $existingUser = $this->userModel->find($id);
      if (!$existingUser) {
        return response()->json([
          'success' => false,
          'message' => 'User not found'
        ], 404);
      }

      $this->userModel->delete($id);

      return response()->json([
        'success' => true,
        'message' => 'User deleted successfully'
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to delete user',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Search users by NIP.
   * GET /api/users/nip/{nip}
   */
  public function findByNip($nip)
  {
    try {
      $user = $this->userModel->findByNip($nip);

      if (!$user) {
        return response()->json([
          'success' => false,
          'message' => 'User not found'
        ], 404);
      }

      return response()->json([
        'success' => true,
        'message' => 'User retrieved successfully',
        'data' => $user
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to retrieve user',
        'error' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Search users by email.
   * GET /api/users/email/{email}
   */
  public function findByEmail($email)
  {
    try {
      $user = $this->userModel->findByEmail($email);

      if (!$user) {
        return response()->json([
          'success' => false,
          'message' => 'User not found'
        ], 404);
      }

      return response()->json([
        'success' => true,
        'message' => 'User retrieved successfully',
        'data' => $user
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to retrieve user',
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
