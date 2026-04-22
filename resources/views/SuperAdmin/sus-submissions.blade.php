@extends('layouts.super-admin')

@section('title', 'SUS Submissions')

@section('content')
    <style>
        .page-hero {
            background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
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
    </style>

    <div class="container py-4">
        <div class="page-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="mb-1"><i class="bi bi-clipboard2-pulse me-2"></i>SUS Submissions</h1>
                <p class="mb-0" style="opacity:.85;">System Usability Scale feedback submitted by users.</p>
            </div>
            <a href="{{ route('super-admin.submissions.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-inbox-fill me-1"></i>All Submissions
            </a>
        </div>

        @if ($submissions->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clipboard-x display-4 d-block mb-3"></i>
                <p class="fs-5">No SUS feedback yet.</p>
                <p class="small">SUS responses will appear here after users complete the survey.</p>
            </div>
        @else
            <div class="sub-card mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="py-3">Submitted By</th>
                                <th class="py-3">Guest Key</th>
                                <th class="py-3">Recommendation Method</th>
                                <th class="py-3">SUS Score</th>
                                <th class="py-3">Submitted At</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submissions as $submission)
                                @php
                                    $submitterDisplay = $submission->user?->name ?? 'Guest';
                                    $susScore = $submission->sus_score;
                                @endphp
                                <tr>
                                    <td class="px-4 text-muted small">{{ $submission->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $submitterDisplay }}</div>
                                        @if ($submission->user)
                                            <div class="text-muted small">{{ $submission->user->email }}</div>
                                        @else
                                            <div class="text-muted small fst-italic">Guest session</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $submission->guest_key ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            {{ $submission->recommendationRun?->weightingMethod?->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill" style="background:#dcfce7;color:#166534;">
                                            {{ number_format((float) $susScore, 2) }}/100
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $submission->submitted_at?->format('d M Y') }}<br>
                                        <span class="text-secondary">{{ $submission->submitted_at?->format('H:i') }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#sus-modal-{{ $submission->id }}">
                                            <i class="bi bi-eye me-1"></i>View SUS
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{ $submissions->links() }}
            </div>

            @foreach ($submissions as $submission)
                @php
                    $submitterDisplay = $submission->user?->name ?? 'Guest';
                    $susResponses = is_array($submission->sus_responses) ? $submission->sus_responses : [];
                    $susStatements = [
                        1 => 'I think that I would like to use this system frequently.',
                        2 => 'I found the system unnecessarily complex.',
                        3 => 'I thought the system was easy to use.',
                        4 => 'I think that I would need support from a technical person to use this system.',
                        5 => 'I found the various functions in this system were well integrated.',
                        6 => 'I thought there was too much inconsistency in this system.',
                        7 => 'I would imagine that most people would learn to use this system very quickly.',
                        8 => 'I found the system very cumbersome to use.',
                        9 => 'I felt very confident using the system.',
                        10 => 'I needed to learn a lot of things before I could get going with this system.',
                    ];
                @endphp
                <div class="modal fade" id="sus-modal-{{ $submission->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#f97316);color:white;">
                                <h5 class="modal-title">
                                    <i class="bi bi-clipboard2-pulse me-2"></i>
                                    {{ $submitterDisplay }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div>
                                        <div class="text-muted small">Submitted By</div>
                                        <div class="fw-semibold">
                                            {{ $submission->user?->name ?? 'Guest' }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Guest Key</div>
                                        <div class="fw-semibold">
                                            {{ $submission->guest_key ?? '—' }}
                                        </div>
                                    </div>
                                    <div>
                                            <div class="text-muted small">Recommendation Method</div>
                                            <div class="fw-semibold">
                                                {{ $submission->recommendationRun?->weightingMethod?->name ?? '—' }}
                                            </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="text-muted small">SUS score</div>
                                        <div class="fw-semibold">{{ number_format((float) $submission->sus_score, 2) }}/100</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Submitted at</div>
                                        <div class="fw-semibold">{{ $submission->submitted_at?->format('d M Y, H:i') ?? '—' }}</div>
                                    </div>
                                </div>

                                <div class="list-group list-group-flush border rounded">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <div class="list-group-item d-flex justify-content-between align-items-start">
                                            <div class="small pe-3">Q{{ $i }}. {{ $susStatements[$i] }}</div>
                                            <span class="badge text-bg-secondary">{{ $susResponses['q' . $i] ?? '—' }}</span>
                                        </div>
                                    @endfor
                                </div>
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
