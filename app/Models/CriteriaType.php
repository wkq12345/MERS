<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriteriaType extends Model
{
    use HasFactory;

    protected $table = 'criteria_type';

    protected $fillable = [
        'name',
        'description',
        'ideal_preference',
    ];

    /**
     * Get all criteria of this type
     */
    public function criteria()
    {
        return $this->hasMany(Criteria::class, 'criteria_type_id');
    }
}
