<?php

namespace App\Imports;

use App\Models\Criteria;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class CriteriaImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new Criteria([
            'name' => $row['name'] ?? null,
            'description' => $row['description'] ?? null,
            'criteria_type_id' => $row['criteria_type_id'] ?? null,
        ]);
    }

    public function uniqueBy(): string
    {
        return 'name';
    }
}
