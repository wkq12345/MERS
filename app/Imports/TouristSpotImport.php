<?php

namespace App\Imports;

use App\Models\TouristSpot;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class TouristSpotImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new TouristSpot([
            'name' => $row['name'] ?? null,
            'image' => $row['image'] ?? null,
            'review_link' => $row['review_link'] ?? null,
            'status' => $row['status'] ?? null,
            'location_id' => $row['location_id'] ?? null,
        ]);
    }

    public function uniqueBy(): string
    {
        return 'name';
    }
}
