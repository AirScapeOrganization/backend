<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'review_id';

    protected $fillable = [
        'user_id',
        'listing_id',
        'rating',
        'comment',
        'created_at',
    ];

    protected $hidden = [
        'created_at',
    ];

    protected $casts = [
        'rating' => 'integer',
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
