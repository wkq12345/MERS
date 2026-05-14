<?php

namespace App\Exports;

use App\Models\SusSubmission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SusSubmissionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function query()
    {
        return SusSubmission::with(['user', 'recommendationRun.weightingMethod'])
            ->latest('submitted_at');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Submitted By',
            'Email',
            'Guest Key',
            'Recommendation Method',
            'SUS Score',
            'Q1',
            'Q2',
            'Q3',
            'Q4',
            'Q5',
            'Q6',
            'Q7',
            'Q8',
            'Q9',
            'Q10',
            'Submitted At',
        ];
    }

    public function map($submission): array
    {
        $susResponses = is_array($submission->sus_responses) ? $submission->sus_responses : [];

        return [
            $submission->id,
            $submission->user?->name ?? 'Guest',
            $submission->user?->email ?? '—',
            $submission->guest_key ?? '—',
            $submission->recommendationRun?->weightingMethod?->name ?? '—',
            number_format((float) $submission->sus_score, 2),
            $susResponses['q1'] ?? '—',
            $susResponses['q2'] ?? '—',
            $susResponses['q3'] ?? '—',
            $susResponses['q4'] ?? '—',
            $susResponses['q5'] ?? '—',
            $susResponses['q6'] ?? '—',
            $susResponses['q7'] ?? '—',
            $susResponses['q8'] ?? '—',
            $susResponses['q9'] ?? '—',
            $susResponses['q10'] ?? '—',
            $submission->submitted_at?->format('d M Y, H:i') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EF4444']],
            ],
        ];
    }
}
