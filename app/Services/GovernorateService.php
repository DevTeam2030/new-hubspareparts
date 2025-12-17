<?php

namespace App\Services;

use App\Repositories\GovernorateRepository;
use Illuminate\Http\Request;

class GovernorateService
{
    protected $governorateRepository;

    /**
     * Constructor to inject the GovernorateRepository
     *
     * @param GovernorateRepository $governorateRepository
     */
    public function __construct(GovernorateRepository $governorateRepository)
    {
        $this->governorateRepository = $governorateRepository;
    }

    /**
     * Get all governorates
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllGovernorates()
    {
        return $this->governorateRepository->getAll();
    }

    /**
     * Store a newly created governorate.
     *
     * @param Request $request
     */
    public function storeGovernorate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'note' => 'nullable|string',
            'min_shipping_cost' => 'nullable|numeric|min:0',
        ]);

        $this->governorateRepository->create($validated);
    }

    /**
     * Get a governorate by its ID.
     *
     * @param $id
     * @return \App\Models\Governorate
     */
    public function getGovernorateById($id)
    {
        return $this->governorateRepository->findById($id);
    }

    /**
     * Update the governorate with new data.
     *
     * @param Request $request
     * @param $id
     */
    public function updateGovernorate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $this->governorateRepository->update($id,$validated);
    }

    /**
     * Delete the specified governorate.
     *
     * @param $id
     */
    public function deleteGovernorate($id)
    {
        $this->governorateRepository->delete($id);
    }
}
