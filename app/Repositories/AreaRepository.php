<?php

namespace App\Repositories;

use App\Models\Area;

class AreaRepository
{
    // Retrieve all areas
    public function getAll($perPage)
    {
        return $perPage ? Area::paginate($perPage) : Area::all();
    }

    // Create a new area
    public function create(array $data)
    {
        return Area::create($data);
    }

    // Find area by ID
    public function findById($id)
    {
        return Area::find($id);
    }

    // Update area data
    public function update($id, array $data)
    {
        $area = Area::find($id);
        if ($area) {
            $area->update($data);
            return $area;
        }
        return null;
    }

    // Delete an area
    public function delete($id)
    {
        $area = Area::find($id);
        return $area ? $area->delete() : false;
    }
}
