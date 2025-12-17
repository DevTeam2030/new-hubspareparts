<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'latitude'        => $this->latitude,
            'longitude'       => $this->longitude,
            'price_per_kg'    => $this->price_per_kg,
            'max_distance_km' => $this->max_distance_km,
        ];
    }
}
