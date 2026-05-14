<?php

namespace App\Exports;

use App\Models\RecommendationRun;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubmissionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function query()
    {
        return RecommendationRun::with(['weightingMethod', 'user', 'favoriteTouristSpot'])
            ->where('submitted_to_admin', true)
            ->latest('submitted_at');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Submitted By',
            'Email',
            'Method',
            'Time Taken',
            'Submitted At',
            'Favorite Spot',
            'Guest Key',
            'IP Address',
        ];
    }

    public function map($run): array
    {
        $timeTaken = '—';
        if (!is_null($run->time_taken_seconds)) {
            $hours = intdiv($run->time_taken_seconds, 3600);
            $minutes = intdiv($run->time_taken_seconds % 3600, 60);
            $seconds = $run->time_taken_seconds % 60;
            $parts = [];

            if ($hours > 0) {
                $parts[] = $hours . 'h';
            }
            if ($minutes > 0) {
                $parts[] = $minutes . 'm';
            }
            $parts[] = $seconds . 's';
            $timeTaken = implode(' ', $parts);
        }

        return [
            $run->id,
            $run->submitter_name ?? ($run->user?->name ?? 'Guest'),
            $run->user?->email ?? '—',
            optional($run->weightingMethod)->name ?? '—',
            $timeTaken,
            $run->submitted_at?->format('d M Y, H:i') ?? '—',
            $run->favoriteTouristSpot?->name ?? '—',
            $run->guest_key ?? '—',
            $run->ip_address ?? '—',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
            ],
        ];
    }
}
