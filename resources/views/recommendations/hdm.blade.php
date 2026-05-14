@extends('layouts.user')

@section('content')
    @php
        $criteriaCards = [];
        $colors = ['#a855f7', '#0d6efd', '#198754', '#f97316', '#0dcaf0'];
        $bgSubtles = ['#f3e8ff', '#cfe2ff', '#d1e7dd', '#ffedd5', '#cff4fc'];

        $icons = [
            '<svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>',
            '<svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>',
            '<svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M4 12L12 4M4 12L12 20"></path></svg>',
            '<svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>',
            '<svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
        ];

        $idx = 0;
        if (isset($criteriaTypes)) {
            foreach ($criteriaTypes as $type) {
                foreach ($type->criteria as $criterion) {
                    $criteriaCards[] = [
                        'id' => $criterion->id,
                        'name' => $criterion->name,
                        'description' => $criterion->description ?? 'Description for this criteria.',
                        'color' => $colors[$idx % count($colors)],
                        'bg_subtle' => $bgSubtles[$idx % count($bgSubtles)],
                        'icon' => $icons[$idx % count($icons)],
                    ];
                    $idx++;
                }
            }
        }
    @endphp
    <style>
        /* Custom Slider Styles */
        input[type=range].custom-slider {
            -webkit-appearance: none;
            width: 100%;
            background: #e9ecef;
            /* bs-gray-200 */
            height: 8px;
            border-radius: 4px;
            outline: none;
        }

        input[type=range].custom-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ffffff;
            cursor: pointer;
            border: 4px solid var(--thumb-color, #0d6efd);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            transition: transform 0.1s ease;
        }

        input[type=range].custom-slider::-moz-range-thumb {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ffffff;
            cursor: pointer;
            border: 4px solid var(--thumb-color, #0d6efd);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            transition: transform 0.1s ease;
        }

        input[type=range].custom-slider::-webkit-slider-thumb:hover,
        input[type=range].custom-slider::-webkit-slider-thumb:active {
            transform: scale(1.2);
        }

        input[type=range].custom-slider::-moz-range-thumb:hover,
        input[type=range].custom-slider::-moz-range-thumb:active {
            transform: scale(1.2);
        }

        /* Remove default number input arrows */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Utility colors mapping for custom colors without writing external css */
        .bg-purple-100 {
            background-color: #f3e8ff;
        }

        .text-purple-600 {
            color: #9333ea;
        }

        .text-purple-800 {
            color: #6b21a8;
        }

        .bg-purple-50 {
            background-color: #faf5ff;
        }

        .border-purple-100 {
            border-color: #f3e8ff;
        }

        .border-purple-400 {
            border-color: #c084fc;
        }

        .bg-purple-500 {
            background-color: #a855f7;
            color: white;
        }

        .hover-bg-purple-600:hover {
            background-color: #9333ea;
            color: white;
        }

        .focus-ring-purple:focus {
            box-shadow: 0 0 0 0.25rem rgba(168, 85, 247, 0.25);
            border-color: #a855f7;
        }

        .bg-orange-100 {
            background-color: #ffedd5;
        }

        .text-orange-600 {
            color: #ea580c;
        }

        .text-orange-800 {
            color: #9a3412;
        }

        .bg-orange-50 {
            background-color: #fff7ed;
        }

        .border-orange-100 {
            border-color: #ffedd5;
        }

        .border-orange-400 {
            border-color: #fb923c;
        }

        .bg-orange-500 {
            background-color: #f97316;
            color: white;
        }

        .hover-bg-orange-600:hover {
            background-color: #ea580c;
            color: white;
        }

        .focus-ring-orange:focus {
            box-shadow: 0 0 0 0.25rem rgba(249, 115, 22, 0.25);
            border-color: #f97316;
        }

        /* Autofill Button Hover Effects */
        .autofill-btn {
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }

        .autofill-btn:not(:disabled):hover,
        .autofill-btn:not(:disabled):active {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }
    </style>

    <div class="py-5">
        <div class="container" style="max-width: 900px;">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.history.back()">
                <i class="bi bi-arrow-left me-1"></i>Back
            </button>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-5 fw-bold text-dark mb-2">Hundred Dollar Method</h1>
                    <p class="text-secondary mb-0">Imagine you have $100 to allocate across these criteria. Distribute your
                        budget based on what matters most to you.</p>
                </div>
                <button type="button" onclick="openCriteriaModal()" class="btn btn-primary shadow-sm px-4 py-2"
                    style="font-weight: 500;">
                    Select Criteria
                </button>
            </div>

            <!-- Budget Overview -->
            <div id="overview-card" class="bg-white rounded-3 shadow-sm border border-2 border-primary p-4 mb-4"
                style="transition: background-color 0.3s, border-color 0.3s;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div id="overview-icon"
                            class="d-flex border rounded-3 p-2 bg-primary-subtle text-primary border-primary-subtle"
                            style="transition: all 0.3s;">
                            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-0">Budget Overview</h2>
                            <p class="small text-secondary mb-0">Total budget to allocate: $100</p>
                        </div>
                    </div>
                    <div class="text-end">
                        <div id="remaining-text" class="display-4 fw-bolder text-primary" style="transition: color 0.3s;">
                            $100</div>
                        <div class="small fw-medium text-secondary text-uppercase" style="letter-spacing: 0.1em;">Remaining
                        </div>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="w-100 bg-secondary-subtle rounded-pill mb-2 overflow-hidden border border-secondary"
                    style="height: 0.75rem;">
                    <div id="progress-bar" class="bg-primary h-100 rounded-pill"
                        style="width: 0%; transition: width 0.3s ease-out, background-color 0.3s;"></div>
                </div>
                <div class="text-center small text-secondary fw-medium"><span id="allocated-text">0</span> / $100 allocated
                </div>
            </div>

            <div class="d-flex flex-column gap-4 mb-5 pb-5" id="dynamic-cards-container">
                @foreach ($criteriaCards as $index => $card)
                    <style>
                        input[type=range]#slider-{{ $card['id'] }}::-webkit-slider-thumb {
                            border-color: {{ $card['color'] }};
                            background: {{ $card['color'] }};
                        }

                        input[type=range]#slider-{{ $card['id'] }}::-moz-range-thumb {
                            border-color: {{ $card['color'] }};
                            background: {{ $card['color'] }};
                        }

                        #num-{{ $card['id'] }}:focus {
                            box-shadow: 0 0 0 0.25rem {{ $card['color'] }}40;
                            border-color: {{ $card['color'] }};
                        }
                    </style>

                    <div class="criterion-card bg-white rounded-3 shadow-sm border border-2 p-4 d-none"
                        data-criterion="{{ $card['id'] }}" id="card-{{ $card['id'] }}"
                        style="border-color: {{ $card['color'] }} !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-3" style="color: {{ $card['color'] }};">
                                <div class="p-2 rounded-3" style="background-color: {{ $card['bg_subtle'] }};">
                                    {!! $card['icon'] !!}
                                </div>
                                <h3 class="h5 fw-bold mb-0">{{ $index + 1 }}. {{ $card['name'] }}</h3>
                            </div>
                            <div class="alloc-badge text-white px-3 py-1 rounded-pill fw-bold shadow-sm"
                                style="background-color: {{ $card['color'] }};">$0</div>
                        </div>

                        <div class="p-3 rounded-3 small mb-3 d-flex gap-2 border"
                            style="background-color: {{ $card['bg_subtle'] }}; border-color: {{ $card['color'] }}40; color: {{ $card['color'] }};">
                            <i class="bi bi-info-circle flex-shrink-0 mt-1"></i>
                            <p class="mb-0 text-dark"><span class="fw-bold">Explanation:</span> {{ $card['description'] }}
                            </p>
                        </div>

                        <p class="fw-medium text-dark mb-3">How much budget would you allocate for
                            {{ strtolower($card['name']) }}?</p>

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="small fw-medium text-secondary">Quick adjust:</span>
                                <div class="d-flex bg-light rounded-3 p-1 border">
                                    <button type="button" class="btn btn-sm btn-light border-0 quick-btn"
                                        data-val="-10">-$10</button>
                                    <button type="button" class="btn btn-sm btn-light border-0 quick-btn"
                                        data-val="-5">-$5</button>
                                    <button type="button" class="btn btn-sm btn-light border-0 quick-btn"
                                        data-val="5">+$5</button>
                                    <button type="button" class="btn btn-sm btn-light border-0 quick-btn"
                                        data-val="10">+$10</button>
                                </div>
                            </div>
                            <button type="button"
                                class="btn autofill-btn text-white fw-bold d-flex align-items-center gap-2 shadow-sm"
                                style="background-color: {{ $card['color'] }}; transition: opacity 0.2s;"
                                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path
                                        d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641l2.5-8.5z" />
                                </svg>
                                <span class="autofill-text">Autofill $100</span>
                            </button>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="fw-medium text-dark">Adjust with slider:</span>
                                <span class="fw-medium text-secondary">Remaining: <span
                                        class="section-remaining text-success fw-bold">$100</span></span>
                            </div>
                            <input type="range" min="0" max="100" value="0" class="custom-slider w-100"
                                id="slider-{{ $card['id'] }}" data-color="{{ $card['color'] }}">
                            <div class="d-flex justify-content-between small text-muted fw-bold mt-2 px-1">
                                <span>$0</span>
                                <span>$25</span>
                                <span>$50</span>
                                <span>$75</span>
                                <span>$100</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <span class="small fw-medium text-secondary">Or enter amount:</span>
                            <div class="position-relative">
                                <span
                                    class="position-absolute top-50 start-0 translate-middle-y ms-2 text-secondary fw-medium">$</span>
                                <input type="number" min="0" max="1000" value="0"
                                    id="num-{{ $card['id'] }}"
                                    class="form-control ps-4 text-center fw-medium shadow-sm" style="width: 100px;">
                            </div>
                        </div>
                    </div>
                @endforeach


            </div>

            <!-- Bottom Sticky Action Bar -->
            <div class="fixed-bottom bg-white border-top py-3 px-3 shadow-lg z-3">
                <div class="container d-flex align-items-center justify-content-between" style="max-width: 900px;">
                    <div id="bottom-remaining"
                        class="text-dark bg-light border px-3 py-2 rounded-pill d-flex align-items-center gap-2">
                        <span class="fs-5">💰</span> <span class="fw-bold">$100</span> remaining to allocate
                    </div>
                    <!-- Assuming action submits form, but right now it's just a button or link -->
                    <form method="POST" action="{{ route('recommendations.calculate') }}" id="hdm-form"
                        class="m-0 d-flex gap-3">
                        @csrf
                        <input type="hidden" name="weighting_method" value="hdm">
                        @foreach ($criteriaCards as $card)
                            <input type="hidden" name="score_{{ $card['id'] }}" id="input-{{ $card['id'] }}"
                                value="0">
                        @endforeach
                        <button type="submit" id="proceed-btn" disabled
                            class="btn btn-secondary text-white fw-bold py-2 px-4 shadow-sm d-flex align-items-center gap-2">
                            Proceed to Rankings
                            <svg width="24" height="24" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Criteria Selection Modal -->
    <div class="modal fade" id="criteriaModal" tabindex="-1" aria-labelledby="criteriaModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <div>
                        <h4 class="modal-title fw-bold text-dark" id="criteriaModalLabel">Select 4 Criteria</h4>
                        <p class="text-muted small mb-0 mt-1">Choose exactly four criteria you want to allocate budget to.
                        </p>
                    </div>
                    <button type="button" class="btn-close" onclick="closeCriteriaModal()"></button>
                </div>
                <div class="modal-body p-4 bg-light mt-3"
                    style="border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
                    @include('recommendations.partials.criteria_modal')
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalBudget = 100;
            const overviewCard = document.getElementById('overview-card');
            const remainingTextTop = document.getElementById('remaining-text');
            const progressBar = document.getElementById('progress-bar');
            const allocatedText = document.getElementById('allocated-text');
            const bottomRemaining = document.getElementById('bottom-remaining');
            const proceedBtn = document.getElementById('proceed-btn');
            const overviewIcon = document.getElementById('overview-icon');

            const criteriaCardsData = [
                @foreach ($criteriaCards as $card)
                    {
                        id: '{{ $card['id'] }}',
                        color: '{{ $card['color'] }}'
                    },
                @endforeach
            ];

            let allocations = {};
            const formInputs = {};
            const colors = {};

            criteriaCardsData.forEach(c => {
                allocations[c.id] = 0;
                formInputs[c.id] = document.getElementById('input-' + c.id);
                colors[c.id] = c.color;
            });

            const __initialSelected = @json(session('selected_criteria', []));
            let activeCriteria = new Set((__initialSelected || []).map(String));
            let criteriaModalInstance = new bootstrap.Modal(document.getElementById('criteriaModal'));

            window.openCriteriaModal = function() {
                document.querySelectorAll('.criteria-checkbox').forEach(cb => {
                    cb.checked = activeCriteria.has(cb.value);
                });
                if (window.updateModalBtnState) window.updateModalBtnState();
                criteriaModalInstance.show();
            }

            window.closeCriteriaModal = function() {
                criteriaModalInstance.hide();
            }

            // Handle confirmed selection from shared modal partial
            document.addEventListener('criteria:confirmed', function(e) {
                const selected = e.detail.selected || [];
                if (selected.length !== 4) return;

                activeCriteria.clear();
                selected.forEach(id => activeCriteria.add(id));

                // Hide all cards, show active ones
                document.querySelectorAll('.criterion-card').forEach(card => {
                    if (activeCriteria.has(card.dataset.criterion)) {
                        card.classList.remove('d-none');
                    } else {
                        card.classList.add('d-none');
                        // Reset hidden ones to 0
                        allocations[card.dataset.criterion] = 0;
                    }
                });

                updateUI();
                criteriaModalInstance.hide();
            });

            function updateUI() {
                let totalAllocated = 0;
                activeCriteria.forEach(id => {
                    totalAllocated += allocations[id] || 0;
                });

                let remaining = totalBudget - totalAllocated;

                // Sync hidden inputs for form submission
                for (const key in allocations) {
                    if (formInputs[key]) formInputs[key].value = allocations[key];
                }

                // Update top overview
                allocatedText.textContent = totalAllocated;
                remainingTextTop.textContent = '$' + remaining;

                // Limit progress bar to 100% physically
                progressBar.style.width = Math.min(totalAllocated, 100) + '%';

                if (totalAllocated === 100) {
                    overviewCard.className = 'bg-white rounded-3 shadow-sm border border-2 border-success p-4 mb-4';
                    remainingTextTop.className = 'display-4 fw-bolder text-success';
                    progressBar.className = 'bg-success h-100 rounded-pill';
                    overviewIcon.className =
                        'd-flex border rounded-3 p-2 bg-success-subtle text-success border-success-subtle';

                    bottomRemaining.innerHTML =
                        '🎉 <span class="fw-bold">Excellent!</span> Budget perfectly allocated.';
                    bottomRemaining.className =
                        'text-success bg-success-subtle border border-success px-3 py-2 rounded-pill d-flex align-items-center gap-2';
                    proceedBtn.disabled = false;
                    proceedBtn.className =
                        'btn btn-primary fw-bold py-2 px-4 shadow-sm d-flex align-items-center gap-2';
                } else if (totalAllocated > 100) {
                    overviewCard.className = 'bg-white rounded-3 shadow-sm border border-2 border-danger p-4 mb-4';
                    remainingTextTop.className = 'display-4 fw-bolder text-danger';
                    progressBar.className = 'bg-danger h-100 rounded-pill';
                    overviewIcon.className =
                        'd-flex border rounded-3 p-2 bg-danger-subtle text-danger border-danger-subtle';

                    bottomRemaining.innerHTML = '⚠️ <span class="fw-bold">Over budget</span> by $' + Math.abs(
                        remaining);
                    bottomRemaining.className =
                        'text-danger bg-danger-subtle border border-danger px-3 py-2 rounded-pill d-flex align-items-center gap-2';
                    proceedBtn.disabled = true;
                    proceedBtn.className =
                        'btn btn-secondary text-white fw-bold py-2 px-4 shadow-sm d-flex align-items-center gap-2';
                } else {
                    overviewCard.className = 'bg-white rounded-3 shadow-sm border border-2 border-primary p-4 mb-4';
                    remainingTextTop.className = 'display-4 fw-bolder text-primary';
                    progressBar.className = 'bg-primary h-100 rounded-pill';
                    overviewIcon.className =
                        'd-flex border rounded-3 p-2 bg-primary-subtle text-primary border-primary-subtle';

                    bottomRemaining.innerHTML = '<span class="fs-5">💰</span> <span class="fw-bold">$' + remaining +
                        '</span> remaining to allocate';
                    bottomRemaining.className =
                        'text-dark bg-light border px-3 py-2 rounded-pill d-flex align-items-center gap-2';
                    proceedBtn.disabled = true;
                    proceedBtn.className =
                        'btn btn-secondary text-white fw-bold py-2 px-4 shadow-sm d-flex align-items-center gap-2';
                }

                // Update active cards
                document.querySelectorAll('.criterion-card').forEach(card => {
                    const criteria = card.dataset.criterion;
                    if (!activeCriteria.has(criteria)) return;

                    const val = allocations[criteria];

                    // update slider
                    const slider = card.querySelector('.custom-slider');
                    slider.value = val > 100 ? 100 : val;

                    const color = colors[criteria];
                    const gradientVal = val > 100 ? 100 : val;
                    slider.style.background =
                        `linear-gradient(to right, ${color} ${gradientVal}%, #e9ecef ${gradientVal}%)`;

                    // update number input
                    const numInput = card.querySelector('input[type="number"]');
                    numInput.value = val;

                    // update badge
                    const badge = card.querySelector('.alloc-badge');
                    badge.textContent = '$' + val;

                    // update autofill button
                    const autofillBtn = card.querySelector('.autofill-btn');
                    if (autofillBtn) {
                        const autofillText = autofillBtn.querySelector('.autofill-text');
                        let addedAmount = remaining > 0 ? Math.min(remaining, 100 - val) : 0;
                        if (addedAmount < 0) addedAmount = 0;
                        autofillText.textContent = `Autofill $${addedAmount}`;
                        if (addedAmount === 0 || remaining <= 0) {
                            autofillBtn.disabled = true;
                            autofillBtn.style.opacity = '0.5';
                            autofillBtn.style.cursor = 'not-allowed';
                        } else {
                            autofillBtn.disabled = false;
                            autofillBtn.style.opacity = '1';
                            autofillBtn.style.cursor = 'pointer';
                        }
                    }

                    // update remaining text for this section
                    const sectionRemainingText = card.querySelector('.section-remaining');
                    if (sectionRemainingText) {
                        sectionRemainingText.textContent = remaining >= 0 ? '$' + remaining : '-$' + Math
                            .abs(remaining);
                        if (remaining >= 0) {
                            sectionRemainingText.className = 'section-remaining small fw-bold text-success';
                        } else {
                            sectionRemainingText.className = 'section-remaining small fw-bold text-danger';
                        }
                    }
                });
            }

            document.querySelectorAll('.criterion-card').forEach(card => {
                const criteria = card.dataset.criterion;
                const slider = card.querySelector('.custom-slider');
                const numInput = card.querySelector('input[type="number"]');
                const quickBtns = card.querySelectorAll('.quick-btn');
                const autofillBtn = card.querySelector('.autofill-btn');

                slider.addEventListener('input', (e) => {
                    allocations[criteria] = parseInt(e.target.value) || 0;
                    updateUI();
                });

                numInput.addEventListener('input', (e) => {
                    let val = parseInt(e.target.value) || 0;
                    if (val < 0) val = 0;
                    allocations[criteria] = val;
                    updateUI();
                });

                numInput.addEventListener('blur', (e) => {
                    if (e.target.value === '') {
                        e.target.value = 0;
                        allocations[criteria] = 0;
                        updateUI();
                    }
                });

                quickBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const addVal = parseInt(btn.dataset.val);
                        let current = allocations[criteria] || 0;
                        let newVal = current + addVal;
                        if (newVal < 0) newVal = 0;
                        allocations[criteria] = newVal;
                        updateUI();
                    });
                });

                autofillBtn.addEventListener('click', () => {
                    let otherAllocated = 0;
                    activeCriteria.forEach(id => {
                        if (id !== criteria) {
                            otherAllocated += allocations[id] || 0;
                        }
                    });
                    let maxAvailable = totalBudget - otherAllocated;

                    if (maxAvailable < 0) maxAvailable = 0;
                    if (maxAvailable > 100) maxAvailable = 100;

                    allocations[criteria] = maxAvailable;
                    updateUI();
                });
            });

            // Initialize UI
            if (activeCriteria.size === 0) {
                criteriaModalInstance.show();
            } else {
                // Show cards for selected criteria restored from session
                document.querySelectorAll('.criterion-card').forEach(card => {
                    if (activeCriteria.has(card.dataset.criterion)) {
                        card.classList.remove('d-none');
                    } else {
                        card.classList.add('d-none');
                    }
                });
            }
            updateUI();
        });
    </script>
@endsection
