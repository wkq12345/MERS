<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightingMethod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    public function recommendationRuns()
    {
        return $this->hasMany(RecommendationRun::class);
    }
}
