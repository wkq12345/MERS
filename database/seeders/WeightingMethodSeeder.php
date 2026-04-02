<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeightingMethod;

class WeightingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $weightingMethods = [
            [
                'code' => 'drm',
                'name' => 'Direct Rating Method',
                'description' => 'Rate based on scale 1-9.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'hdm',
                'name' => 'Hundred Dollar Method',
                'description' => 'Rate based on monetary value.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'kano',
                'name' => 'Kano Model',
                'description' => 'Rate based on functional and dysfunctional questions.',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($weightingMethods as $method) {
            WeightingMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
}
