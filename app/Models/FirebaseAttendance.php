<?php

namespace App\Models;

use App\Services\FirebaseService;

class FirebaseAttendance
{
    protected $firebaseService;

    public function __construct()
    {
        $this->firebaseService = app(FirebaseService::class);
    }

    /**
     * Get attendance records for a specific user from subcollection
     * Path: users/{userId}/attendance
     */
    public function getByUserId($userId)
    {
        $path = "users/{$userId}/attendance";
        return $this->firebaseService->getCollection($path);
    }

    /**
     * Get specific attendance record
     */
    public function find($userId, $attendanceId)
    {
        $path = "users/{$userId}/attendance";
        return $this->firebaseService->getDocument($path, $attendanceId);
    }

    /**
     * Create new attendance record in user's subcollection
     */
    public function create($userId, array $data, $documentId = null)
    {
        $path = "users/{$userId}/attendance";
        return $this->firebaseService->createDocument($path, $data, $documentId);
    }

    /**
     * Delete attendance record
     */
    public function delete($userId, $attendanceId)
    {
        $path = "users/{$userId}/attendance";
        return $this->firebaseService->deleteDocument($path, $attendanceId);
    }
}
