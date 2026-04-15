<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    protected $table = 'criteria';

    protected $fillable = [
        'id',
        'name',
        'description',
        'criteria_type_id',
    ];

    public function criteriaType()
    {
        return $this->belongsTo(CriteriaType::class, 'criteria_type_id');
    }
}
