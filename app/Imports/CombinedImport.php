<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\RatingImport;

class CombinedImport implements WithMultipleSheets
{
    protected $selectedSheet;

    public function __construct($selectedSheet)
    {
        $this->selectedSheet = $selectedSheet;
    }

    public function sheets(): array
    {
        $sheets = [
            'import location' => new LocationImport(),
            'import criteria type' => new CriteriaTypeImport(),
            'import criteria' => new CriteriaImport(),
            'import tourist spot' => new TouristSpotImport(),
            'import rating' => new RatingImport(),
        ];

        return [
            $this->selectedSheet => $sheets[$this->selectedSheet]
        ];
    }
}
