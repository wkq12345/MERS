<?php

namespace App\Imports;

use App\Models\CriteriaType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class CriteriaTypeImport implements ToModel, WithHeadingRow, WithUpserts
{
    public function model(array $row)
    {
        return new CriteriaType([
            'name' => $row['name'] ?? null,
            'description' => $row['description'] ?? null,
            'ideal_preference' => $row['ideal_preference'] ?? null,
        ]);
    }

    public function uniqueBy(): string
    {
        return 'name';
    }
}
