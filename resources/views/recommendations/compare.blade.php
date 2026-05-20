@extends('layouts.user')

@section('title', 'Compare Recommendation Methods')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            prefix: 'tw-',
            corePlugins: {
                preflight: false,
            }
        }
    </script>
@endpush

@section('content')
    @php
        $methodRouteMap = [
            'drm' => route('recommendations.drm'),
            'hdm' => route('recommendations.hdm'),
            'kano' => route('recommendations.kano'),
        ];
    @endphp
    <div class="tw-min-h-screen tw-bg-gray-50">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-6 tw-py-8">
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            <div class="tw-mb-6 tw-flex tw-flex-col md:tw-flex-row md:tw-items-center md:tw-justify-between tw-gap-3">
                @if (session('success'))
                    <div class="tw-w-full tw-mb-2 tw-px-4 tw-py-3 tw-bg-green-50 tw-border tw-border-green-200 tw-text-green-800 tw-rounded-lg tw-text-sm tw-flex tw-items-center tw-gap-2">
                        <i class="bi bi-check-circle-fill tw-text-green-600"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="tw-w-full tw-mb-2 tw-px-4 tw-py-3 tw-bg-red-50 tw-border tw-border-red-200 tw-text-red-800 tw-rounded-lg tw-text-sm tw-flex tw-items-center tw-gap-2">
                        <i class="bi bi-exclamation-circle-fill tw-text-red-600"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <div>
                    <h2 class="tw-text-3xl tw-text-gray-900 tw-font-semibold tw-mb-1">Method Comparison</h2>
                    <p class="tw-text-gray-600">Side-by-side ranking comparison across DRM, HDM, and Kano for the same criteria set.</p>
                </div>
            </div>
                            <div>
                    <a href="{{ route('recommendations.compare.download') }}"
                        class="tw-inline-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-rounded-lg tw-bg-emerald-600 tw-text-white hover:tw-bg-emerald-700 tw-no-underline hover:tw-no-underline">
                        <i class="bi bi-download"></i>
                        Download Result (CSV)
                    </a>
                </div>

            <div class="tw-mb-6 tw-bg-white tw-border tw-border-gray-200 tw-rounded-lg tw-p-4">
                <div class="tw-flex tw-items-center tw-gap-2 tw-mb-3">
                    <i class="bi bi-ui-checks-grid tw-text-indigo-600"></i>
                    <h3 class="tw-text-sm tw-font-semibold tw-text-gray-900 tw-uppercase tw-tracking-wide">Choose Method</h3>
                </div>

                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-3">
                    @foreach ($methods as $method)
                        @php
                            $run = $methodRuns[$method->code] ?? null;
                        @endphp
                        <div class="tw-border tw-rounded-lg tw-p-3 {{ $run ? 'tw-border-gray-300' : 'tw-border-indigo-200 tw-bg-indigo-50/40' }}">
                            <p class="tw-text-sm tw-font-semibold tw-text-gray-900 tw-mb-2">{{ $method->name }}</p>

                            @if (!$run)
                                <a href="{{ $methodRouteMap[$method->code] ?? '#' }}"
                                    class="tw-block tw-w-full tw-text-center tw-px-3 tw-py-2 tw-rounded-lg tw-bg-indigo-600 tw-text-white hover:tw-bg-indigo-700 tw-no-underline hover:tw-no-underline">
                                    Start Method
                                </a>
                            @else
                                <a href="{{ route('recommendations.showPrevious', ['method_code' => $method->code]) }}"
                                    class="tw-block tw-w-full tw-text-center tw-px-3 tw-py-2 tw-rounded-lg tw-bg-blue-600 tw-text-white hover:tw-bg-blue-700 tw-no-underline hover:tw-no-underline">
                                    View Result
                                </a>

                                <form id="clear-form-{{ $method->code }}" class="tw-mt-2" method="POST" action="{{ route('recommendations.clear_method') }}">
                                    @csrf
                                    <input type="hidden" name="method_code" value="{{ $method->code }}">
                                    <input type="hidden" name="criteria_signature" value="{{ $criteriaSignature }}">
                                    <button type="button"
                                        data-clear-form="clear-form-{{ $method->code }}"
                                        data-method-name="{{ $method->name }}"
                                        class="clear-method-btn tw-w-full tw-px-3 tw-py-2 tw-rounded-lg tw-border tw-border-indigo-300 tw-text-indigo-700 hover:tw-bg-indigo-50 tw-transition-colors"
                                    >
                                        Clear Result and Redo
                                    </button>
                                </form>
                            @endif

                            @if ($run)
                                @if (!empty($methodSusSubmitted[$method->code]))
                                    <a href="{{ route('recommendations.sus.index', ['method_code' => $method->code]) }}"
                                        class="tw-mt-2 tw-block tw-w-full tw-text-center tw-px-3 tw-py-2 tw-rounded-lg tw-bg-emerald-50 tw-text-emerald-700 tw-border tw-border-emerald-200 hover:tw-bg-emerald-100 tw-no-underline hover:tw-no-underline">
                                        SUS Submitted (View)
                                    </a>
                                @else
                                    <a href="{{ route('recommendations.sus.index', ['method_code' => $method->code]) }}"
                                        class="tw-mt-2 tw-block tw-w-full tw-text-center tw-px-3 tw-py-2 tw-rounded-lg tw-bg-amber-50 tw-text-amber-700 tw-border tw-border-amber-200 hover:tw-bg-amber-100 tw-no-underline hover:tw-no-underline">
                                        Your feedback matters! (Click here)
                                    </a>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="tw-mb-6 tw-bg-white tw-border tw-border-gray-200 tw-rounded-lg tw-p-4">
                <p class="tw-text-xs tw-text-gray-500 tw-uppercase tw-tracking-wide tw-font-semibold tw-mb-3">Selected Criteria By Method</p>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
                    @foreach ($methods as $method)
                        @php
                            $criteriaList = $methodSelectedCriteria[$method->code] ?? [];
                        @endphp
                        <div class="tw-rounded-lg tw-border tw-border-gray-200 tw-p-3">
                            <p class="tw-text-sm tw-font-semibold tw-text-gray-900 tw-mb-2">{{ $method->name }}</p>
                            @if (!empty($criteriaList))
                                <div class="tw-flex tw-flex-wrap tw-gap-2">
                                    @foreach ($criteriaList as $criterionName)
                                        <span class="tw-inline-flex tw-items-center tw-px-3 tw-py-1 tw-text-xs tw-font-medium tw-rounded-full tw-bg-indigo-50 tw-text-indigo-700 tw-border tw-border-indigo-200">
                                            {{ $criterionName }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="tw-text-xs tw-text-gray-500">No saved criteria yet.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @if (!empty($hasMixedCriteriaSignatures))
                <div class="tw-mb-6 tw-bg-amber-50 tw-border tw-border-amber-200 tw-rounded-lg tw-p-4">
                    <div class="tw-flex tw-items-start tw-gap-2">
                        <i class="bi bi-exclamation-triangle tw-text-amber-600"></i>
                        <p class="tw-text-sm tw-text-amber-800">
                            Compared methods were saved using different criteria selections. Ranking differences may not be directly comparable.
                        </p>
                    </div>
                </div>
            @endif

            <div id="clear-method-modal" class="tw-fixed tw-inset-0 tw-bg-black/40 tw-z-50 tw-hidden tw-items-center tw-justify-center tw-p-4">
                <div class="tw-w-full tw-max-w-md tw-bg-white tw-rounded-xl tw-shadow-xl tw-border tw-border-gray-200">
                    <div class="tw-p-5 tw-border-b tw-border-gray-100">
                        <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900">Clear Saved Result?</h4>
                        <p class="tw-text-sm tw-text-gray-600 tw-mt-1" id="clear-method-modal-message">
                            This will clear the selected method result and allow you to redo it.
                        </p>
                    </div>
                    <div class="tw-p-5 tw-flex tw-items-center tw-justify-end tw-gap-3">
                        <button type="button" id="clear-method-cancel"
                            class="tw-px-4 tw-py-2 tw-rounded-lg tw-border tw-border-gray-300 tw-text-gray-700 hover:tw-bg-gray-50">
                            Cancel
                        </button>
                        <button type="button" id="clear-method-confirm"
                            class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-red-600 tw-text-white hover:tw-bg-red-700">
                            Yes, Clear Result
                        </button>
                    </div>
                </div>
            </div>

            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4 tw-mb-6">
                @foreach ($methods as $method)
                    @php
                        $run = $methodRuns[$method->code] ?? null;
                    @endphp
                    <div class="tw-bg-white tw-border tw-rounded-lg tw-p-4 {{ $run ? 'tw-border-green-300' : 'tw-border-gray-200' }}">
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <p class="tw-text-sm tw-text-gray-500">{{ $method->name }}</p>
                            @if ($run)
                                <span class="tw-px-2 tw-py-1 tw-text-xs tw-font-semibold tw-rounded-full tw-bg-green-100 tw-text-green-700">Available</span>
                            @else
                                <span class="tw-px-2 tw-py-1 tw-text-xs tw-font-semibold tw-rounded-full tw-bg-gray-100 tw-text-gray-600">No Run</span>
                            @endif
                        </div>
                        <p class="tw-text-xs tw-text-gray-500 tw-mt-2">
                            @if ($run)
                                Last run: {{ $run->created_at->diffForHumans() }}
                            @else
                                Run this method to include it in comparison.
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>

            @if ($compareRows->isEmpty())
                <div class="tw-bg-white tw-border tw-border-gray-200 tw-rounded-lg tw-p-12 tw-text-center">
                    <i class="bi bi-bar-chart-line tw-text-4xl tw-text-gray-300"></i>
                    <p class="tw-text-gray-600 tw-mt-3">No comparable runs found yet.</p>
                    <p class="tw-text-sm tw-text-gray-500">Run at least one method first, then return to this page.</p>
                </div>
            @else
                <div class="tw-bg-white tw-border tw-border-gray-200 tw-rounded-lg tw-overflow-hidden">
                    <div class="tw-overflow-x-auto">
                        <table class="tw-min-w-full tw-text-sm">
                            <thead class="tw-bg-gray-50 tw-border-b tw-border-gray-200">
                                <tr>
                                    <th class="tw-text-left tw-px-4 tw-py-3 tw-font-semibold tw-text-gray-700">Tourist Spot</th>
                                    @foreach ($methods as $method)
                                        <th class="tw-text-center tw-px-4 tw-py-3 tw-font-semibold tw-text-gray-700">{{ $method->name }} Rank</th>
                                    @endforeach
                                    <th class="tw-text-center tw-px-4 tw-py-3 tw-font-semibold tw-text-gray-700">Average Rank</th>
                                </tr>
                            </thead>
                            <tbody class="tw-divide-y tw-divide-gray-100">
                                @foreach ($compareRows as $row)
                                    <tr class="hover:tw-bg-gray-50">
                                        <td class="tw-px-4 tw-py-3 tw-font-medium tw-text-gray-900">{{ $row['tourist_spot'] }}</td>
                                        @foreach ($methods as $method)
                                            @php
                                                $rank = $row['ranks'][$method->code] ?? null;
                                            @endphp
                                            <td class="tw-px-4 tw-py-3 tw-text-center">
                                                @if ($rank)
                                                    <span class="tw-inline-flex tw-items-center tw-px-2 tw-py-1 tw-rounded tw-bg-indigo-50 tw-text-indigo-700 tw-font-semibold">#{{ $rank }}</span>
                                                @else
                                                    <span class="tw-text-gray-400">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-font-semibold tw-text-blue-700">
                                            {{ $row['avg_rank'] ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('clear-method-modal');
                const messageEl = document.getElementById('clear-method-modal-message');
                const cancelBtn = document.getElementById('clear-method-cancel');
                const confirmBtn = document.getElementById('clear-method-confirm');
                let pendingFormId = null;

                function openModal(methodName, formId) {
                    pendingFormId = formId;
                    messageEl.textContent = 'This will clear the saved result for ' + methodName + ' and allow you to redo it.';
                    modal.classList.remove('tw-hidden');
                    modal.classList.add('tw-flex');
                }

                function closeModal() {
                    pendingFormId = null;
                    modal.classList.add('tw-hidden');
                    modal.classList.remove('tw-flex');
                }

                document.querySelectorAll('.clear-method-btn').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const formId = btn.getAttribute('data-clear-form');
                        const methodName = btn.getAttribute('data-method-name') || 'this method';
                        openModal(methodName, formId);
                    });
                });

                cancelBtn.addEventListener('click', closeModal);

                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                confirmBtn.addEventListener('click', function () {
                    if (!pendingFormId) {
                        return;
                    }

                    const form = document.getElementById(pendingFormId);
                    if (form) {
                        form.submit();
                    }
                });
            });
        </script>
    @endpush
@endsection
