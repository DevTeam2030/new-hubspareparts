<?php

namespace App\Repositories;

use App\Models\Governorate;

class GovernorateRepository
{
    /**
     * Retrieve all governorates.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Governorate::all();
    }

    /**
     * Create a new governorate.
     *
     * @param array $data
     * @return \App\Models\Governorate
     */
    public function create(array $data)
    {
        return Governorate::create($data);
    }

    /**
     * Find a governorate by its ID.
     *
     * @param $id
     * @return \App\Models\Governorate
     */
    public function findById($id)
    {
        return Governorate::findOrFail($id);
    }

    /**
     * Update a governorate with new data.
     *
     * @param $id
     * @param array $data
     * @return \App\Models\Governorate
     */
    public function update($id, array $data)
    {
        $governorate = Governorate::findOrFail($id);
        $governorate->update($data);
        return $governorate;
    }

    /**
     * Delete the specified governorate.
     *
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $governorate = Governorate::findOrFail($id);
        return $governorate->delete();
    }
}
