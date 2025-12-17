<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryTime;
use App\Services\DeliveryTimeService;
use App\Models\Governorate;
use Illuminate\Http\Request;

class DeliveryTimeController extends Controller
{
    protected $deliveryTimeService;

    public function __construct(DeliveryTimeService $deliveryTimeService)
    {
        $this->deliveryTimeService = $deliveryTimeService;
    }

    // Display a listing of delivery times
    public function index(Request $request)
    {
        $deliveryTimes = $this->deliveryTimeService->getAllDeliveryTimes()->paginate(5);
        return view('admin-views.delivery-time.view', compact('deliveryTimes'));
    }

    // Show the form for creating a new delivery time
    public function create()
    {
        $governorates = Governorate::all();
        return view('admin-views.delivery-time.create', compact('governorates'));
    }

    // Store a newly created delivery time
    public function store(Request $request)
    {
        $this->deliveryTimeService->storeDeliveryTime($request);
        return redirect()->route('admin.location.delivery-times.view')->with('success', 'Delivery Time Added Successfully!');
    }

    // Show the form for editing the specified delivery time
    public function edit($id)
    {
        $deliveryTime = $this->deliveryTimeService->getDeliveryTimeById($id);
        $governorates = Governorate::all();
        return view('admin-views.delivery-time.edit', compact('deliveryTime', 'governorates'));
    }

    // Update the specified delivery time
    public function update(Request $request, $id)
    {
        $this->deliveryTimeService->updateDeliveryTime($request, $id);
        return redirect()->route('admin.location.delivery-times.view')->with('success', 'Delivery Time Updated Successfully!');
    }

    // Remove the specified delivery time
    public function destroy($id)
    {
        $this->deliveryTimeService->deleteDeliveryTime($id);
        return redirect()->route('admin.location.delivery-times.view')->with('success', 'Delivery Time Deleted Successfully!');
    }

    /**
     * Return note and delivery times for a given governorate.
     */
    public function getTimesByGovernorate(Request $request)
    {
        $governorateId = $request->get('governorate_id');
        $governorate   = Governorate::find($governorateId);

        $note = $governorate?->note;

        // Return times in 24-hour format
        $times = DeliveryTime::where('governorate_id', $governorateId)
            ->get(['id','start_time','end_time']);

        return response()->json([
            'note'  => $note,
            'times' => $times
        ]);
    }

}
