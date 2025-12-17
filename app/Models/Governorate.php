<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = ['name', 'note','min_shipping_cost'];


    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sellers()
    {
        return $this->belongsToMany(Seller::class, 'seller_governorates');
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_governorates');
    }
    public function deliveryTimes()
    {
        return $this->hasMany(DeliveryTime::class);
    }
}
