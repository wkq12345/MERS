<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SusSubmission extends Model
{
    protected $fillable = [
        'recommendation_run_id',
        'user_id',
        'guest_key',
        'sus_responses',
        'sus_score',
        'submitted_at',
    ];

    protected $casts = [
        'sus_responses' => 'array',
        'sus_score' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recommendationRun()
    {
        return $this->belongsTo(RecommendationRun::class);
    }
}
