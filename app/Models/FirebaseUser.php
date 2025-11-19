<?php

namespace App\Models;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Validator;

class FirebaseUser
{
  protected $firebaseService;
  protected $collection = 'users';

  public function __construct()
  {
    $this->firebaseService = app(FirebaseService::class);
  }

  /**
   * Get all users from Firebase
   */
  public function all()
  {
    return $this->firebaseService->getCollection($this->collection);
  }

  /**
   * Find a user by ID
   */
  public function find($id)
  {
    return $this->firebaseService->getDocument($this->collection, $id);
  }

  /**
   * Create a new user
   */
  public function create(array $data)
  {
    // Validation
    $validator = Validator::make($data, [
      'nip' => 'required|string',
      'email' => 'required|email',
      'fullName' => 'required|string',
      'faceDataBase64' => 'nullable|string',
    ]);

    if ($validator->fails()) {
      throw new \InvalidArgumentException($validator->errors()->first());
    }

    // Prepare data with timestamps
    $userData = [
      'nip' => $data['nip'],
      'email' => $data['email'],
      'fullName' => $data['fullName'],
      'faceDataBase64' => $data['faceDataBase64'] ?? '',
      'createdAt' => now()->toIso8601String(),
      'faceRegistrationTimestamp' => isset($data['faceDataBase64']) && !empty($data['faceDataBase64'])
        ? now()->toIso8601String()
        : null,
    ];

    // Use NIP as document ID (you can change this to auto-generate ID)
    $documentId = $data['nip'];

    return $this->firebaseService->createDocument($this->collection, $userData, $documentId);
  }

  /**
   * Update a user
   */
  public function update($id, array $data)
  {
    // Validation
    $validator = Validator::make($data, [
      'nip' => 'sometimes|string',
      'email' => 'sometimes|email',
      'fullName' => 'sometimes|string',
      'faceDataBase64' => 'nullable|string',
    ]);

    if ($validator->fails()) {
      throw new \InvalidArgumentException($validator->errors()->first());
    }

    // Update faceRegistrationTimestamp if faceDataBase64 is being updated
    if (isset($data['faceDataBase64']) && !empty($data['faceDataBase64'])) {
      $data['faceRegistrationTimestamp'] = now()->toIso8601String();
    }

    return $this->firebaseService->updateDocument($this->collection, $id, $data);
  }

  /**
   * Delete a user
   */
  public function delete($id)
  {
    return $this->firebaseService->deleteDocument($this->collection, $id);
  }

  /**
   * Query users by field
   */
  public function where($field, $operator, $value, $limit = null)
  {
    return $this->firebaseService->queryCollection($this->collection, $field, $operator, $value, $limit);
  }

  /**
   * Find user by NIP
   */
  public function findByNip($nip)
  {
    $users = $this->where('nip', '=', $nip, 1);
    return !empty($users) ? $users[0] : null;
  }

  /**
   * Find user by email
   */
  public function findByEmail($email)
  {
    $users = $this->where('email', '=', $email, 1);
    return !empty($users) ? $users[0] : null;
  }
}
