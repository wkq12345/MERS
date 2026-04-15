@extends('layouts.super-admin')

@section('title', 'User Submissions')

@section('content')
    <style>
        .page-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: white;
            border-radius: 20px;
            padding: 32px 40px;
            margin-bottom: 28px;
        }

        .sub-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 700;
        }
    </style>

    <div class="container py-4">

        <div class="page-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="mb-1"><i class="bi bi-inbox-fill me-2"></i>User Submissions</h1>
                <p class="mb-0" style="opacity:.85;">Recommendation results submitted by users for admin review.</p>
            </div>
            <a href="{{ route('super-admin.sus_submissions.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-clipboard2-pulse me-1"></i>View SUS Submissions
            </a>
        </div>

        @if ($submissions->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                <p class="fs-5">No submissions yet.</p>
                <p class="small">Users will appear here once they submit their recommendation results.</p>
            </div>
        @else
            <div class="sub-card mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="py-3">Submitted By</th>
                                <th class="py-3">Method</th>
                                <th class="py-3">Time Taken</th>
                                <th class="py-3">Submitted At</th>
                                <th class="py-3">Top 3 Spots</th>
                                <th class="py-3">Favorite Spot</th>
                                <th class="py-3">Criteria Used</th>
                                <th class="py-3">IP Address</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submissions as $run)
                                @php
                                    $rankedResults = is_array($run->ranked_results) ? $run->ranked_results : [];
                                    $top3 = array_slice($rankedResults, 0, 3);
                                    $criteriaIds = is_array($run->criteria_id) ? $run->criteria_id : [];
                                    $criteriaNames = \App\Models\Criteria::whereIn('id', $criteriaIds)->pluck('name');
                                    $methodName = optional($run->weightingMethod)->name ?? '—';
                                    $submitterDisplay = $run->submitter_name ?? ($run->user?->name ?? 'Guest');
                                    $timeTakenSeconds = $run->time_taken_seconds;
                                    $timeTakenLabel = '—';
                                    $ipAddress = $run->ip_address ?? '—';

                                    if (!is_null($timeTakenSeconds)) {
                                        $hours = intdiv($timeTakenSeconds, 3600);
                                        $minutes = intdiv($timeTakenSeconds % 3600, 60);
                                        $seconds = $timeTakenSeconds % 60;
                                        $parts = [];

                                        if ($hours > 0) {
                                            $parts[] = $hours . 'h';
                                        }
                                        if ($minutes > 0) {
                                            $parts[] = $minutes . 'm';
                                        }
                                        $parts[] = $seconds . 's';
                                        $timeTakenLabel = implode(' ', $parts);
                                    }
                                @endphp
                                <tr>
                                    <td class="px-4 text-muted small">{{ $run->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $submitterDisplay }}</div>
                                        @if ($run->user)
                                            <div class="text-muted small">{{ $run->user->email }}</div>
                                        @else
                                            <div class="text-muted small fst-italic">Guest session</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill"
                                            style="background:#ede9fe;color:#5b21b6;font-size:12px;">
                                            {{ $methodName }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $timeTakenLabel }}</td>
                                    <td class="text-muted small">
                                        {{ $run->submitted_at?->format('d M Y') }}<br>
                                        <span class="text-secondary">{{ $run->submitted_at?->format('H:i') }}</span>
                                    </td>
                                    <td>
                                        @foreach ($top3 as $r)
                                            @php
                                                $medal = $r['rank'] === 1 ? '🥇' : ($r['rank'] === 2 ? '🥈' : '🥉');
                                            @endphp
                                            <div class="small">{{ $medal }} {{ $r['tourist_spot'] ?? '—' }}
                                                <span class="text-muted">({{ number_format($r['score'] ?? 0, 4) }})</span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($run->favoriteTouristSpot)
                                            <span class="badge bg-success small">
                                                <i class="bi bi-heart-fill me-1"></i>{{ $run->favoriteTouristSpot->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small fst-italic">None selected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($criteriaNames as $cName)
                                                <span
                                                    class="badge bg-light text-dark border small">{{ $cName }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $ipAddress }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#submission-modal-{{ $run->id }}">
                                            <i class="bi bi-eye me-1"></i>Full Results
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center">
                {{ $submissions->links() }}
            </div>

            {{-- Detail Modals --}}
            @foreach ($submissions as $run)
                @php
                    $rankedResults = is_array($run->ranked_results) ? $run->ranked_results : [];
                    $criteriaIds = is_array($run->criteria_id) ? $run->criteria_id : [];
                    $criteriaNames = \App\Models\Criteria::whereIn('id', $criteriaIds)->pluck('name');
                    $methodName = optional($run->weightingMethod)->name ?? '—';
                    $submitterDisplay = $run->submitter_name ?? ($run->user?->name ?? 'Guest');
                    $timeTakenSeconds = $run->time_taken_seconds;
                    $timeTakenLabel = '—';
                    $ipAddress = $run->ip_address ?? '—';

                    if (!is_null($timeTakenSeconds)) {
                        $hours = intdiv($timeTakenSeconds, 3600);
                        $minutes = intdiv($timeTakenSeconds % 3600, 60);
                        $seconds = $timeTakenSeconds % 60;
                        $parts = [];

                        if ($hours > 0) {
                            $parts[] = $hours . 'h';
                        }
                        if ($minutes > 0) {
                            $parts[] = $minutes . 'm';
                        }
                        $parts[] = $seconds . 's';
                        $timeTakenLabel = implode(' ', $parts);
                    }
                @endphp
                <div class="modal fade" id="submission-modal-{{ $run->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header"
                                style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;">
                                <h5 class="modal-title">
                                    <i class="bi bi-bar-chart-line me-2"></i>
                                    {{ $submitterDisplay }} — {{ $methodName }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-sm-4">
                                        <div class="text-muted small">Submitted by</div>
                                        <div class="fw-semibold">{{ $submitterDisplay }}</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-muted small">Method</div>
                                        <div class="fw-semibold">{{ $methodName }}</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-muted small">Time taken</div>
                                        <div class="fw-semibold">{{ $timeTakenLabel }}</div>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-sm-4">
                                        <div class="text-muted small">Submitted at</div>
                                        <div class="fw-semibold">{{ $run->submitted_at?->format('d M Y, H:i') ?? '—' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="text-muted small">Favorite Spot Selected</div>
                                        <div class="fw-semibold">
                                            @if ($run->favoriteTouristSpot)
                                                <span class="text-success"><i class="bi bi-heart-fill me-1"></i>{{ $run->favoriteTouristSpot->name }}</span>
                                            @else
                                                <span class="text-muted fst-italic">None selected</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="text-muted small">IP Address</div>
                                    <div class="fw-semibold">{{ $run->ip_address ?? '—' }}</div>
                                </div>

                                @if ($criteriaNames->isNotEmpty())
                                    <div class="mb-3">
                                        <div class="text-muted small mb-1">Selected Criteria</div>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($criteriaNames as $cName)
                                                <span class="badge bg-light text-dark border">{{ $cName }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if (!empty($rankedResults))
                                    <table class="table table-sm table-bordered rounded">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Rank</th>
                                                <th>Tourist Spot</th>
                                                <th class="text-end">Score (Ci)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($rankedResults as $r)
                                                <tr @if (($r['rank'] ?? 0) === 1) class="table-warning" @endif>
                                                    <td class="fw-bold">{{ $r['rank'] ?? '—' }}</td>
                                                    <td>{{ $r['tourist_spot'] ?? '—' }}</td>
                                                    <td class="text-end fw-semibold" style="color:#4f46e5;">
                                                        {{ number_format((float) ($r['score'] ?? 0), 4) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-muted small">No ranked results available.</p>
                                @endif

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
