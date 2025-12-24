<?php

namespace App\Models;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Validator;

class FirebaseLocation
{
    protected $firebaseService;
    protected $collection = 'officeLocations';

    public function __construct()
    {
        $this->firebaseService = app(FirebaseService::class);
    }

    /**
     * Get all locations from Firebase
     */
    public function all()
    {
        return $this->firebaseService->getCollection($this->collection);
    }

    /**
     * Find a location by ID
     */
    public function find($id)
    {
        return $this->firebaseService->getDocument($this->collection, $id);
    }

    /**
     * Create a new location
     */
    public function create(array $data)
    {
        // Validation
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'isActive' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        // Prepare data with timestamps
        $locationData = [
            'name' => $data['name'],
            'latitude' => (float) $data['latitude'],
            'longitude' => (float) $data['longitude'],
            'isActive' => $data['isActive'] ?? true,
            'createdAt' => now(),
        ];

        return $this->firebaseService->createDocument($this->collection, $locationData);
    }

    /**
     * Update a location
     */
    public function update($id, array $data)
    {
        // Validation
        $validator = Validator::make($data, [
            'name' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'isActive' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        // Convert numeric strings to float for coordinates
        if (isset($data['latitude'])) {
            $data['latitude'] = (float) $data['latitude'];
        }
        if (isset($data['longitude'])) {
            $data['longitude'] = (float) $data['longitude'];
        }

        return $this->firebaseService->updateDocument($this->collection, $id, $data);
    }

    /**
     * Delete a location
     */
    public function delete($id)
    {
        return $this->firebaseService->deleteDocument($this->collection, $id);
    }

    /**
     * Query locations by field
     */
    public function where($field, $operator, $value, $limit = null)
    {
        return $this->firebaseService->queryCollection($this->collection, $field, $operator, $value, $limit);
    }

    /**
     * Find location by name
     */
    public function findByName($name)
    {
        $locations = $this->where('name', '=', $name, 1);
        return !empty($locations) ? $locations[0] : null;
    }

    /**
     * Get only active locations
     */
    public function getActive()
    {
        return $this->where('isActive', '=', true);
    }
}
