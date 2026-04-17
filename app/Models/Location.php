<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'location';

    protected $fillable = [
        'id',
        'name',
        'image',
    ];

    public function touristSpots()
    {
        return $this->hasMany(TouristSpot::class);
    }
}
