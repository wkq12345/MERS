<?php

namespace App\Imports;

use App\Models\Location;
use App\Models\TouristSpot;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class TouristSpotImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new TouristSpot([
            'id' => $row['id'] ?? null,
            'name' => $row['name'] ?? null,
            'image' => $row['image'] ?? null,
            'review_link' => $this->sanitizeReviewLink($row['review_link'] ?? null),
            'status' => $row['status'] ?? null,
            'location_id' => $this->sanitizeLocationId($row['location_id'] ?? null),
        ]);
    }

    private function sanitizeReviewLink($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $cleaned = trim((string) $value);

        if ($cleaned === '') {
            return null;
        }

        // Keep all provider URLs while normalizing separator spacing.
        $parts = array_filter(array_map('trim', explode('|||||', $cleaned)), function ($part) {
            return $part !== '';
        });

        if (empty($parts)) {
            return null;
        }

        $cleaned = implode(' ||||| ', $parts);

        return $cleaned === '' ? null : $cleaned;
    }

    private function sanitizeLocationId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        $locationId = (int) $value;

        if ($locationId <= 0) {
            return null;
        }

        $exists = Location::query()->whereKey($locationId)->exists();

        return $exists ? $locationId : null;
    }

    public function uniqueBy(): string
    {
        return 'name';
    }
}
