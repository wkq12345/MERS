<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDemographic extends Model
{
    protected $fillable = [
        'user_id',
        'guest_key',
        'age',
        'gender',
        'income',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
