<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationRun extends Model
{
    protected $fillable = [
        'user_id',
        'guest_key',
        'user_demographic_id',
        'weighting_method_id',
        'criteria_id',
        'criteria_weight',
        'criteria_signature',
        'ranked_results',
        'submitted_to_admin',
        'submitted_at',
        'submitter_name',
        'started_at',
        'completed_at',
        'time_taken_seconds',
        'sus_responses',
        'sus_score',
        'sus_submitted_at',
        'favorite_tourist_spot_id',
        'ip_address',
    ];

    public function favoriteTouristSpot()
    {
        return $this->belongsTo(TouristSpot::class, 'favorite_tourist_spot_id');
    }

    protected $casts = [
        'criteria_id' => 'array',
        'criteria_weight' => 'array',
        'ranked_results' => 'array',
        'ip_address' => 'string',
        'submitted_to_admin' => 'boolean',
        'submitted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'sus_responses' => 'array',
        'sus_score' => 'decimal:2',
        'sus_submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function weightingMethod()
    {
        return $this->belongsTo(WeightingMethod::class);
    }

    public function userDemographic()
    {
        return $this->belongsTo(UserDemographic::class, 'user_demographic_id');
    }
}
