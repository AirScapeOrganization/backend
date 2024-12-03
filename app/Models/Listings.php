<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listings extends Model
{
    use HasFactory;


    protected $primaryKey = 'listing_id';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'address',
        'latitude',
        'longitude',
        'price_per_night',
        'num_bedrooms',
        'num_bathrooms',
        'max_guests',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];


    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'price_per_night' => 'float',
        'num_bedrooms' => 'integer',
        'num_bathrooms' => 'integer',
        'max_guests' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function photos()
    {
    return $this->hasMany(Photos::class, 'listing_id'); 
    }

}
