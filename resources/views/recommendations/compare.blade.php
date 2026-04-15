@extends('layouts.user')

@section('title', 'Compare Recommendation Methods')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            important: true,
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
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                @if (session('success'))
                    <div class="w-full mb-2 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-green-600"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="w-full mb-2 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm flex items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill text-red-600"></i>
                        {{ session('error') }}
                    </div>
                @endif
                <div>
                    <h2 class="text-3xl text-gray-900 font-semibold mb-1">Method Comparison</h2>
                    <p class="text-gray-600">Side-by-side ranking comparison across DRM, HDM, and Kano for the same criteria set.</p>
                </div>
            </div>

            <div class="mb-6 bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <i class="bi bi-ui-checks-grid text-indigo-600"></i>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Choose Method</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach ($methods as $method)
                        @php
                            $run = $methodRuns[$method->code] ?? null;
                        @endphp
                        <div class="border rounded-lg p-3 {{ $run ? 'border-gray-300' : 'border-indigo-200 bg-indigo-50/40' }}">
                            <p class="text-sm font-semibold text-gray-900 mb-2">{{ $method->name }}</p>

                            @if (!$run)
                                <a href="{{ $methodRouteMap[$method->code] ?? '#' }}"
                                    class="block w-full text-center px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 no-underline hover:no-underline">
                                    Start Method
                                </a>
                            @else
                                <a href="{{ route('recommendations.showPrevious', ['method_code' => $method->code]) }}"
                                    class="block w-full text-center px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 no-underline hover:no-underline">
                                    View Result
                                </a>

                                <form id="clear-form-{{ $method->code }}" class="mt-2" method="POST" action="{{ route('recommendations.clear_method') }}">
                                    @csrf
                                    <input type="hidden" name="method_code" value="{{ $method->code }}">
                                    <input type="hidden" name="criteria_signature" value="{{ $criteriaSignature }}">
                                    <button type="button"
                                        data-clear-form="clear-form-{{ $method->code }}"
                                        data-method-name="{{ $method->name }}"
                                        class="clear-method-btn w-full px-3 py-2 rounded-lg border border-indigo-300 text-indigo-700 hover:bg-indigo-50 transition-colors"
                                    >
                                        Clear Result and Redo
                                    </button>
                                </form>
                            @endif

                            @if ($run)
                                @if (!is_null($run->sus_submitted_at))
                                    <a href="{{ route('recommendations.sus.index', ['method_code' => $method->code]) }}"
                                        class="mt-2 block w-full text-center px-3 py-2 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 no-underline hover:no-underline">
                                        SUS Submitted (View)
                                    </a>
                                @else
                                    <a href="{{ route('recommendations.sus.index', ['method_code' => $method->code]) }}"
                                        class="mt-2 block w-full text-center px-3 py-2 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 no-underline hover:no-underline">
                                        Your feedback matters! (Click here)
                                    </a>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-6 bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-3">Selected Criteria By Method</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($methods as $method)
                        @php
                            $criteriaList = $methodSelectedCriteria[$method->code] ?? [];
                        @endphp
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-sm font-semibold text-gray-900 mb-2">{{ $method->name }}</p>
                            @if (!empty($criteriaList))
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($criteriaList as $criterionName)
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            {{ $criterionName }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-500">No saved criteria yet.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @if (!empty($hasMixedCriteriaSignatures))
                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-start gap-2">
                        <i class="bi bi-exclamation-triangle text-amber-600"></i>
                        <p class="text-sm text-amber-800">
                            Compared methods were saved using different criteria selections. Ranking differences may not be directly comparable.
                        </p>
                    </div>
                </div>
            @endif

            <div id="clear-method-modal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
                <div class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200">
                    <div class="p-5 border-b border-gray-100">
                        <h4 class="text-lg font-semibold text-gray-900">Clear Saved Result?</h4>
                        <p class="text-sm text-gray-600 mt-1" id="clear-method-modal-message">
                            This will clear the selected method result and allow you to redo it.
                        </p>
                    </div>
                    <div class="p-5 flex items-center justify-end gap-3">
                        <button type="button" id="clear-method-cancel"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="button" id="clear-method-confirm"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                            Yes, Clear Result
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                @foreach ($methods as $method)
                    @php
                        $run = $methodRuns[$method->code] ?? null;
                    @endphp
                    <div class="bg-white border rounded-lg p-4 {{ $run ? 'border-green-300' : 'border-gray-200' }}">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500">{{ $method->name }}</p>
                            @if ($run)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Available</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">No Run</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
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
                <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
                    <i class="bi bi-bar-chart-line text-4xl text-gray-300"></i>
                    <p class="text-gray-600 mt-3">No comparable runs found yet.</p>
                    <p class="text-sm text-gray-500">Run at least one method first, then return to this page.</p>
                </div>
            @else
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left px-4 py-3 font-semibold text-gray-700">Tourist Spot</th>
                                    @foreach ($methods as $method)
                                        <th class="text-center px-4 py-3 font-semibold text-gray-700">{{ $method->name }} Rank</th>
                                    @endforeach
                                    <th class="text-center px-4 py-3 font-semibold text-gray-700">Average Rank</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($compareRows as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row['tourist_spot'] }}</td>
                                        @foreach ($methods as $method)
                                            @php
                                                $rank = $row['ranks'][$method->code] ?? null;
                                            @endphp
                                            <td class="px-4 py-3 text-center">
                                                @if ($rank)
                                                    <span class="inline-flex items-center px-2 py-1 rounded bg-indigo-50 text-indigo-700 font-semibold">#{{ $rank }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-3 text-center font-semibold text-blue-700">
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
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeModal() {
                    pendingFormId = null;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
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
