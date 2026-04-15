<?php

namespace App\Imports;

use App\Models\Criteria;
use App\Models\TouristSpot;
use App\Models\TouristSpotCriteriaRating;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class RatingImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        $touristSpotId = $this->sanitizeId($row['tourist_spot_id'] ?? null);
        $criteriaId = $this->sanitizeId($row['criteria_id'] ?? null);

        if (!$touristSpotId || !$criteriaId) {
            return null;
        }

        if (!TouristSpot::query()->whereKey($touristSpotId)->exists()) {
            return null;
        }

        if (!Criteria::query()->whereKey($criteriaId)->exists()) {
            return null;
        }

        return new TouristSpotCriteriaRating([
            'raw_value' => $row['raw_value'] ?? null,
            'tourist_spot_id' => $touristSpotId,
            'criteria_id' => $criteriaId,
        ]);
    }

    private function sanitizeId($value): ?int
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }

        $id = (int) $value;

        return $id > 0 ? $id : null;
    }

    public function uniqueBy(): array
    {
        return ['tourist_spot_id', 'criteria_id'];
    }
}
