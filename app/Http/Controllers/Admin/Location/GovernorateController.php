<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Services\GovernorateService;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    protected $governorateService;

    /**
     * Constructor to inject GovernorateService
     *
     * @param GovernorateService $governorateService
     */
    public function __construct(GovernorateService $governorateService)
    {
        $this->governorateService = $governorateService;
    }

    /**
     * Display a listing of governorates.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $governorates = $this->governorateService->getAllGovernorates()->paginate(5);
        return view('admin-views.governorate.view', compact('governorates'));
    }

    /**
     * Show the form for creating a new governorate.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin-views.governorate.create');
    }

    /**
     * Store a newly created governorate.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->governorateService->storeGovernorate($request);
        return redirect()->route('admin.location.governorates.view')->with('success', 'Governorate Added Successfully!');
    }

    /**
     * Show the form for editing the specified governorate.
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $governorate = $this->governorateService->getGovernorateById($id);
        return view('admin-views.governorate.edit', compact('governorate'));
    }

    /**
     * Update the specified governorate.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->governorateService->updateGovernorate($request, $id);
        return redirect()->route('admin.location.governorates.view')->with('success', 'Governorate Updated Successfully!');
    }

    /**
     * Remove the specified governorate.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->governorateService->deleteGovernorate($id);
        return redirect()->route('admin.location.governorates.view')->with('success', 'Governorate Deleted Successfully!');
    }
}
