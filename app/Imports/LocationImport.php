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
            'name' => $row['name'],
        ]);
    }

    public function uniqueBy(): string
    {
        return 'name';
    }
}
