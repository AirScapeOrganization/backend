<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory; 

    protected $primary_key = 'message_id';

    protected $fillable = [
        'sender_user_id',
        'receiver_user_id',
        'listing_id',
        'message',
        'created_at'
    ];

    protected $hidden = [
        'listing_id',
    ];

    protected $casts = [
        
    ];
}
