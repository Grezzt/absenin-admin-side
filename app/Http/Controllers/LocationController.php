<?php

namespace App\Http\Controllers;

use App\Models\FirebaseLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    protected $locationModel;

    public function __construct()
    {
        $this->locationModel = new FirebaseLocation();
    }

    /**
     * Display a listing of locations.
     * GET /api/locations
     */
    public function index(Request $request)
    {
        try {
            $locations = $this->locationModel->all();

            // If request is API (wants JSON)
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Locations retrieved successfully',
                    'data' => $locations,
                    'count' => count($locations)
                ], 200);
            }

            // If request is web (wants HTML)
            return view('locations.index', ['locations' => $locations]);
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve locations',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to retrieve locations: ' . $e->getMessage());
        }
    }

    /**
     * Show web view for managing locations (dashboard)
     */
    public function dashboard()
    {
        try {
            $locations = $this->locationModel->all();
            return view('locations.dashboard', ['locations' => $locations]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to retrieve locations: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created location.
     * POST /api/locations
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'isActive' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if location with same name already exists
            $existingLocation = $this->locationModel->findByName($request->name);
            if ($existingLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location with this name already exists'
                ], 409);
            }

            $location = $this->locationModel->create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Location created successfully',
                'data' => $location
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified location.
     * GET /api/locations/{id}
     */
    public function show($id)
    {
        try {
            $location = $this->locationModel->find($id);

            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Location retrieved successfully',
                'data' => $location
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified location.
     * PUT/PATCH /api/locations/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'latitude' => 'sometimes|numeric|between:-90,90',
                'longitude' => 'sometimes|numeric|between:-180,180',
                'isActive' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if location exists
            $existingLocation = $this->locationModel->find($id);
            if (!$existingLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }

            $location = $this->locationModel->update($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully',
                'data' => $location
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified location.
     * DELETE /api/locations/{id}
     */
    public function destroy($id)
    {
        try {
            // Check if location exists
            $existingLocation = $this->locationModel->find($id);
            if (!$existingLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found'
                ], 404);
            }

            $this->locationModel->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Location deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete location',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
