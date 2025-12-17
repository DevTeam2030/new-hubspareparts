<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryTimeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'start_time' => $this->start_time->format('H:i:s'),
            'end_time'   => $this->end_time->format('H:i:s'),
        ];
    }
}
