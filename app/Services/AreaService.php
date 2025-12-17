<?php

namespace App\Services;

use App\Repositories\AreaRepository;
use App\Models\Area;
use App\Models\Governorate;
use GuzzleHttp\Client;

class AreaService
{
    protected $areaRepository;

    public function __construct(AreaRepository $areaRepository)
    {
        $this->areaRepository = $areaRepository;
    }

    // Get all areas
    public function getAllAreas($perPage = null)
    {
        return $this->areaRepository->getAll($perPage);
    }

    // Get all governorates
    public function getAllGovernorates()
    {
        return Governorate::all();
    }

    // Create new area
    public function storeArea($data)
    {
        // Get coordinates using Google API
        $coordinates = $this->getCoordinatesFromGoogle($data->governorate_id, $data->name);

        return $this->areaRepository->create([
            'name' => $data->name,
            'governorate_id' => $data->governorate_id,
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'price_per_kg' => $data->price_per_kg,
            'max_distance_km' => $data->max_distance_km,
        ]);
    }

    // Get area by ID
    public function getAreaById($id)
    {
        return $this->areaRepository->findById($id);
    }

    // Update area data
    public function updateArea($data, $id)
    {
        $coordinates = $this->getCoordinatesFromGoogle($data->governorate_id, $data->name);

        return $this->areaRepository->update($id, [
            'name' => $data->name,
            'governorate_id' => $data->governorate_id,
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'price_per_kg' => $data->price_per_kg,
            'max_distance_km' => $data->max_distance_km,
        ]);
    }

    // Delete area
    public function deleteArea($id)
    {
        return $this->areaRepository->delete($id);
    }

    // Get coordinates using Google API
    private function getCoordinatesFromGoogle($governorateId, $areaName)
    {
        // Use Google API to get latitude and longitude based on governorate and area name
        $client = new Client();
        $apiKey = env('MAP_KEY');
        $address = $areaName . ', ' . Governorate::find($governorateId)->name;

        $response = $client->get("https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey);

        $body = json_decode($response->getBody());

        $latitude = $body->results[0]->geometry->location->lat ?? 0;
        $longitude = $body->results[0]->geometry->location->lng ?? 0;

        return ['latitude' => $latitude, 'longitude' => $longitude];
    }

    /**
     * Calculate shipping cost based on user location and find the nearest area.
     */
    public function calculateShippingCost(int $governorateId, float $lat, float $lng): array
    {
        // Get all areas for this governorate
        $areas = Area::where('governorate_id', $governorateId)->get();
        if ($areas->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No areas found for this governorate.'
            ];
        }

        // Find the closest area within max_distance_km
        $closestArea = null;
        $minDistance = null;

        foreach ($areas as $area) {
            $distance = $this->distance($lat, $lng, $area->latitude, $area->longitude);
            if ($distance <= $area->max_distance_km) {
                if (is_null($minDistance) || $distance < $minDistance) {
                    $minDistance = $distance;
                    $closestArea = $area;
                }
            }
        }

        if (!$closestArea) {
            return [
                'success' => false,
                'message' => 'المنطقة المراد التوصيل لها خارج نطاق التوصيل او احدى المنتجات خارج النطاق'
            ];
        }

        // Calculate shipping cost = distance * price_per_kg
        $shippingCost = $minDistance * $closestArea->price_per_kg;

        // Get the governorate to check the minimum shipping cost
        $governorate = Governorate::find($governorateId);
        if ($governorate && $governorate->min_shipping_cost > $shippingCost) {
            $shippingCost = $governorate->min_shipping_cost;
        }

        return [
            'success'      => true,
            'shipping_cost'=> $shippingCost,
            'distance'     => $minDistance,
            'area_id'      => $closestArea->id,
            'area_name'    => $closestArea->name
        ];
    }


    /**
     * Haversine formula to get distance in KM
     */
    private function distance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0; // in KM
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

}
