<?php

namespace App\Repositories;

use App\Models\DeliveryTime;

class DeliveryTimeRepository
{
    /**
     * Retrieve all delivery times (with pagination if needed).
     */
    public function getAll($perPage = null)
    {
        return $perPage ? DeliveryTime::with('governorate')->paginate($perPage) : DeliveryTime::with('governorate')->get();
    }

    /**
     * Create a new delivery time.
     */
    public function create(array $data)
    {
        return DeliveryTime::create($data);
    }

    /**
     * Find a delivery time by ID.
     */
    public function findById($id)
    {
        return DeliveryTime::findOrFail($id);
    }

    /**
     * Update a delivery time.
     */
    public function update($id, array $data)
    {
        $deliveryTime = DeliveryTime::findOrFail($id);
        $deliveryTime->update($data);
        return $deliveryTime;
    }

    /**
     * Delete a delivery time.
     */
    public function delete($id)
    {
        $deliveryTime = DeliveryTime::findOrFail($id);
        return $deliveryTime->delete();
    }
}
