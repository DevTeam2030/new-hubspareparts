<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GovernorateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'note'               => $this->note,
            'min_shipping_cost'  => $this->min_shipping_cost,
            'delivery_centers'              => AreaResource::collection($this->whenLoaded('areas')),
            'delivery_times'     => DeliveryTimeResource::collection($this->whenLoaded('deliveryTimes')),
        ];
    }
}
