<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Services\AreaService;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    protected $areaService;

    // Constructor to inject the AreaService
    public function __construct(AreaService $areaService)
    {
        $this->areaService = $areaService;
    }

    // Show the list of all areas
    public function index()
    {
        $governorates = $this->areaService->getAllGovernorates();
        $areas = $this->areaService->getAllAreas()->paginate(5);
        return view('admin-views.area.view', compact('areas','governorates'));
    }

    // Show the form for creating a new area
    public function create()
    {
        $governorates = $this->areaService->getAllGovernorates();
        return view('admin-views.area.create', compact('governorates'));
    }

    // Store a newly created area
    public function store(Request $request)
    {
        $this->areaService->storeArea($request);
        return redirect()->route('admin.location.areas.view')->with('success', 'Area Added Successfully!');
    }

    // Show the form for editing the specified area
    public function edit($id)
    {
        $area = $this->areaService->getAreaById($id);
        $governorates = $this->areaService->getAllGovernorates();
        return view('admin-views.area.edit', compact('area', 'governorates'));
    }

    // Update the specified area
    public function update(Request $request, $id)
    {
        $this->areaService->updateArea($request, $id);
        return redirect()->route('admin.location.areas.view')->with('success', 'Area Updated Successfully!');
    }

    // Remove the specified area
    public function destroy($id)
    {
        $this->areaService->deleteArea($id);
        return redirect()->route('admin.location.areas.view')->with('success', 'Area Deleted Successfully!');
    }

    /**
     * Calculate shipping cost based on user location and governorate.
     */
    public function calculateShipping(Request $request)
    {
        $governorateId = (int)$request->get('governorate_id', 0);
        $lat = (float)$request->get('lat', 0);
        $lng = (float)$request->get('lng', 0);

        $result = $this->areaService->calculateShippingCost($governorateId, $lat, $lng);
        if(!$result['success']) {
            // Return error
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 200);
        }

        // Return success
        return response()->json([
            'success' => true,
            'shipping_cost' => $result['shipping_cost'],
            'distance' => $result['distance'],
            'area_id' => $result['area_id'],
            'area_name' => $result['area_name']
        ], 200);
    }
}
