@extends('layouts.super-admin')

@section('title', 'Super Admin Dashboard')

@section('content')
    <style>
        .admin-hero {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .quick-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .quick-card .btn {
            width: 100%;
            border-radius: 50px;
            padding: 12px 18px;
        }

        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 22px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        .top-actions .btn {
            border-radius: 50px;
        }

        .alert-success {
            border-radius: 12px;
        }

        .admin-meta {
            color: rgba(255, 255, 255, 0.9);
        }
    </style>

    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif


        <div class="row g-3">
            <div class="col-12 col-lg-4">
                <div class="chart-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2"></i>People Visit</h5>
                        <small class="text-muted">30 days</small>
                    </div>
                    <canvas id="peopleVisitChart" height="160"></canvas>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="chart-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-inbox-fill me-2"></i>Submissions</h5>
                        <small class="text-muted">Total</small>
                    </div>
                    <canvas id="submissionChart" height="160"></canvas>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="chart-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard2-pulse me-2"></i>SUS Submissions</h5>
                        <small class="text-muted">Total</small>
                    </div>
                    <canvas id="susSubmissionChart" height="160"></canvas>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') {
                return;
            }

            const chartData = @json($chartData ?? ['labels' => [], 'values' => []]);
            const values = chartData.values || [];

            function renderSingleMetricChart(canvasId, label, value, color) {
                const chartElement = document.getElementById(canvasId);
                if (!chartElement) {
                    return;
                }

                new Chart(chartElement, {
                    type: 'bar',
                    data: {
                        labels: [label],
                        datasets: [{
                            data: [value],
                            backgroundColor: [color],
                            borderRadius: 12,
                            borderSkipped: false,
                            barThickness: 44,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false,
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                },
                            },
                        },
                    },
                });
            }

            renderSingleMetricChart('peopleVisitChart', 'People Visit', Number(values[0] || 0), '#4f46e5');
            renderSingleMetricChart('submissionChart', 'Submissions', Number(values[1] || 0), '#0ea5e9');
            renderSingleMetricChart('susSubmissionChart', 'SUS Submissions', Number(values[2] || 0), '#14b8a6');
        });
    </script>
@endpush
