<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

    use HasFactory, Notifiable;

    protected $primaryKey = "user_id";

    protected $fillable = [
        'username',
        'email',
        'password',
        'profile_picture',
        'bio',
        'is_owner'
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at'
    ];
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function listings()
    {
        return $this->hasMany(Listings::class);
    }
}
