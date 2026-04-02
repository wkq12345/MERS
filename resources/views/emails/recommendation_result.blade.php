<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MERS Recommendation Result</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .wrapper {
            max-width: 640px;
            margin: 32px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 32px 40px;
            color: #ffffff;
        }
        .header h1 {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 700;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.85;
        }
        .body {
            padding: 32px 40px;
        }
        .meta-row {
            display: flex;
            gap: 8px;
            align-items: baseline;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .meta-label {
            font-weight: 600;
            color: #555;
            min-width: 100px;
        }
        .meta-value {
            color: #111;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #ede9fe;
            color: #5b21b6;
        }
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #374151;
            margin: 28px 0 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e5e7eb;
        }
        .criteria-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 8px;
        }
        .criterion-chip {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 13px;
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        thead tr {
            background: #f9fafb;
        }
        thead th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #111827;
        }
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-size: 13px;
            font-weight: 700;
        }
        .rank-1 { background: #fef3c7; color: #92400e; }
        .rank-2 { background: #f3f4f6; color: #374151; }
        .rank-3 { background: #fde8d8; color: #92400e; }
        .rank-other { background: #f3f4f6; color: #6b7280; }
        .score-bar-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .score-text {
            font-size: 13px;
            color: #374151;
            min-width: 44px;
        }
        .footer {
            background: #f9fafb;
            padding: 20px 40px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>&#128205; Recommendation Result</h1>
            <p>Malaysia Ecotourism Recommendation System (MERS)</p>
        </div>

        <div class="body">
            <div class="meta-row">
                <span class="meta-label">Submitted by</span>
                <span class="meta-value">{{ $senderName }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Method</span>
                <span class="badge">{{ optional($run->weightingMethod)->name ?? 'Unknown' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Date</span>
                <span class="meta-value">{{ $run->created_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}</span>
            </div>

            @if (!empty($criteriaNameMap))
                <div class="section-title">&#9989; Selected Criteria</div>
                <div class="criteria-list">
                    @foreach ($criteriaNameMap as $name)
                        <span class="criterion-chip">{{ $name }}</span>
                    @endforeach
                </div>
            @endif

            @php
                $rankedResults = is_array($run->ranked_results) ? $run->ranked_results : [];
            @endphp

            @if (!empty($rankedResults))
                <div class="section-title">&#127942; Ranked Tourist Spots</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width:52px;">Rank</th>
                            <th>Tourist Spot</th>
                            <th style="width:90px; text-align:right;">Score (Ci)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rankedResults as $result)
                            @php
                                $rank = $result['rank'] ?? 0;
                                $rankClass = $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other'));
                            @endphp
                            <tr>
                                <td>
                                    <span class="rank-badge {{ $rankClass }}">{{ $rank }}</span>
                                </td>
                                <td>{{ $result['tourist_spot'] ?? '—' }}</td>
                                <td style="text-align:right; font-weight:600; color:#4f46e5;">
                                    {{ number_format((float)($result['score'] ?? 0), 4) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color:#9ca3af; font-size:14px;">No ranked results available.</p>
            @endif
        </div>

        <div class="footer">
            This email was sent automatically from MERS. Do not reply to this email.
        </div>
    </div>
</body>
</html>
