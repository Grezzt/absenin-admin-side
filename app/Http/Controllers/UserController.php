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
      return view('users.dashboard', ['users' => $users]);
    } catch (\Exception $e) {
      return back()->with('error', 'Failed to retrieve users: ' . $e->getMessage());
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

      $user = $this->userModel->create($request->all());

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

      $user = $this->userModel->update($id, $request->all());

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
