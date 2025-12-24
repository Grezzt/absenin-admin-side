<?php

namespace App\Http\Controllers;

use App\Models\FirebaseAttendance;

class AttendanceController extends Controller
{
    protected $attendanceModel;

    public function __construct()
    {
        $this->attendanceModel = new FirebaseAttendance();
    }

    /**
     * Get attendance history for a specific user from subcollection
     * GET /api/attendance/user/{userId}
     */
    public function getUserAttendance($userId)
    {
        try {
            $attendanceRecords = $this->attendanceModel->getByUserId($userId);

            // Sort by createdAt descending (newest first)
            usort($attendanceRecords, function ($a, $b) {
                $timeA = $a['data']['createdAt'] ?? null;
                $timeB = $b['data']['createdAt'] ?? null;

                // Handle different timestamp formats
                $timestampA = $this->parseTimestamp($timeA);
                $timestampB = $this->parseTimestamp($timeB);

                return $timestampB <=> $timestampA;
            });

            // Format for frontend
            $formattedResult = array_map(function ($record) {
                $data = $record['data'] ?? [];

                // Try to get timestamp from multiple possible fields
                $timestamp = $data['timestamp'] ?? $data['createdAt'] ?? $data['checkInTimestamp'] ?? null;

                // Convert Firestore timestamp to ISO string
                $checkInTimeISO = null;
                if ($timestamp && is_array($timestamp) && isset($timestamp['_seconds'])) {
                    $checkInTimeISO = date('c', $timestamp['_seconds']); // ISO 8601
                } elseif ($timestamp) {
                    $checkInTimeISO = $timestamp;
                }

                // If still no timestamp, try to use the ID (if it's a date)
                if (!$checkInTimeISO && preg_match('/^\d{4}-\d{2}-\d{2}$/', $record['id'])) {
                    $checkInTimeISO = $record['id'] . 'T00:00:00Z';
                }

                return [
                    'id' => $record['id'],
                    'data' => [
                        'checkInTime' => $checkInTimeISO,
                        'checkOutTime' => null, // Will be populated if exists
                        'officeName' => $data['officeName'] ?? 'N/A',
                        'location' => $data['location'] ?? null,
                        'type' => $data['type'] ?? 'Unknown'
                    ]
                ];
            }, $attendanceRecords);

            \Log::info('Controller returning data', [
                'userId' => $userId,
                'count' => count($formattedResult),
                'firstRecordId' => $formattedResult[0]['id'] ?? 'none',
                'allIds' => array_map(fn($r) => $r['id'], $formattedResult)
            ]);

            return response()->json([
                'success' => true,
                'data' => $formattedResult
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch attendance records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse different timestamp formats to Unix timestamp
     */
    private function parseTimestamp($timestamp)
    {
        if (!$timestamp) {
            return 0;
        }

        if ($timestamp instanceof \DateTime) {
            return $timestamp->getTimestamp();
        } elseif (is_array($timestamp) && isset($timestamp['_seconds'])) {
            return $timestamp['_seconds'];
        } elseif (is_string($timestamp)) {
            return strtotime($timestamp);
        }

        return 0;
    }
}
