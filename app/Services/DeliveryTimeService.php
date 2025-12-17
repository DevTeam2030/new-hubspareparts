<?php

namespace App\Services;

use App\Repositories\DeliveryTimeRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeliveryTimeService
{
    protected $deliveryTimeRepository;

    public function __construct(DeliveryTimeRepository $deliveryTimeRepository)
    {
        $this->deliveryTimeRepository = $deliveryTimeRepository;
    }

    // Get all delivery times
    public function getAllDeliveryTimes($perPage = null)
    {
        return $this->deliveryTimeRepository->getAll($perPage);
    }

    // Store a new delivery time
    public function storeDeliveryTime(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $validated['start_time'] = Carbon::parse($request->start_time)->format('H:i');
        $validated['end_time']   = Carbon::parse($request->end_time)->format('H:i');

        return $this->deliveryTimeRepository->create($validated);
    }


    // Get a delivery time by ID
    public function getDeliveryTimeById($id)
    {
        return $this->deliveryTimeRepository->findById($id);
    }

    // Update a delivery time
    public function updateDeliveryTime(Request $request, $id)
    {
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);


        $validated['start_time'] = Carbon::parse($request->start_time)->format('H:i');
        $validated['end_time']   = Carbon::parse($request->end_time)->format('H:i');

        return $this->deliveryTimeRepository->update($id, $validated);
    }

    // Delete a delivery time
    public function deleteDeliveryTime($id)
    {
        return $this->deliveryTimeRepository->delete($id);
    }
}
