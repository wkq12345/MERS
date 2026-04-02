<?php

namespace App\Imports;

use App\Models\TouristSpotCriteriaRating;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class RatingImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new TouristSpotCriteriaRating([
            'raw_value' => $row['raw_value'] ?? null,
            'tourist_spot_id' => $row['tourist_spot_id'] ?? null,
            'criteria_id' => $row['criteria_id'] ?? null,
        ]);
    }

    public function uniqueBy(): array
    {
        return ['tourist_spot_id', 'criteria_id'];
    }
}
