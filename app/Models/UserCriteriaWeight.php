<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCriteriaWeight extends Model
{
    protected $table = 'user_criteria_weights';

    protected $fillable = [
        'user_id',
        'criteria_id',
        'weight',
    ];
}
