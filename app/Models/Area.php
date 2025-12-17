<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name', 'governorate_id', 'latitude', 'longitude', 'price_per_kg', 'max_distance_km'];


    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
