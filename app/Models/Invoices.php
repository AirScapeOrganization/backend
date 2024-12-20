<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasFactory;

    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'booking_id',
        'date',
        'time',
        'tax_price',
        'price_gross',
        'price_net',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'booking_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tax_price' => 'float',
        'price_gross' => 'float',
        'price_net' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function booking_id()
    {
        return $this->belongsTo(Bookings::class, 'booking_id');
    }
}
