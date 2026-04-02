<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TouristSpotCriteriaRating extends Model
{
    protected $table = 'tourist_spot_criteria_ratings';

    protected $fillable = [
        'tourist_spot_id',
        'criteria_id',
        'raw_value',
    ];

    public function touristSpot()
    {
        return $this->belongsTo(TouristSpot::class, 'tourist_spot_id');
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }
}
