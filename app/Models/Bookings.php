<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'start_date',
        'end_date',
        'total_price',
        'listing_id',
        'user_id',
        'created_at'
    ];

    public $timestamps = false; 

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'float',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listings::class, 'listing_id');
    }
}
