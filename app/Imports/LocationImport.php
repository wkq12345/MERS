<?php

namespace App\Imports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class LocationImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new Location([
            'id' => $row['id'] ?? null,
            'name' => $row['name'],
            'image' => $row['image'] ?? null,
        ]);
    }

    public function uniqueBy(): string
    {
        return 'name';
    }
}
