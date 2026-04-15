<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TouristSpot extends Model
{
    use HasFactory;

    protected $table = 'tourist_spots';

    protected $fillable = [
        'id',
        'name',
        'description',
        'review_link',
        'image',
        'location_id',
        'status',

    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function ratings()
    {
        return $this->hasMany(TouristSpotCriteriaRating::class);
    }
}
