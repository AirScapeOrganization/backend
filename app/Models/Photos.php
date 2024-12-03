<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photos extends Model
{
    use HasFactory;
    public $timestamps = false; 

    protected $primaryKey = 'photo_id';

    protected $fillable = [
        'listing_id',
        'photo_url',
        'created_at'
    ];

    protected $hidden = [
        'created_at'
      
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function listing()
    {
        return $this->belongsTo(Listings::class, 'listing_id');
    }
}
