<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listings extends Model
{
    use HasFactory;

    /**
     * La clave primaria de la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'listing_id';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
      
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

    /**
     * Los atributos que deben estar ocultos para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Los atributos que deben ser casteados a tipos nativos.
     *
     * @var array<string, string>
     */
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

    /**
     * Relación con el modelo User (un listing pertenece a un usuario).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
