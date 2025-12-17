<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistCollection extends Model
{
    use HasFactory;

    protected $fillable = ['name','name_ar','due_date',
        'priority' , 'notes' , 'eng_approve' , 'eng_proc' , 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_eng_approve()
    {
        return $this->belongsTo(User::class , 'eng_approve_user_id');
    }

    public function user_eng_proc()
    {
        return $this->belongsTo(User::class , 'eng_proc_user_id');
    }
    public function wishlist_items(){
        return $this->hasMany(Wishlist::Class , 'wishlist_collection_id');
    }
}
