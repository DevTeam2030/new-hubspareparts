<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryTime extends Model
{
    protected $fillable = [
        'governorate_id',
        'start_time',
        'end_time',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    protected $casts = [

        'start_time' => 'datetime:H:i:s',
        'end_time'   => 'datetime:H:i:s',
    ];
}
