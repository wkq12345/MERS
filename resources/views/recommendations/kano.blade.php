@extends('layouts.user')

@section('content')
    @php
        $colors = ['#d946ef', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'];
        $icons = ['bi-bus-front-fill', 'bi-building-fill', 'bi-box-fill', 'bi-star-fill', 'bi-tag-fill'];
        $options = [
            ['val' => 0, 'text' => 'I like it that way', 'icon' => 'bi bi-hand-thumbs-up', 'theme' => 'success'],
            ['val' => 1, 'text' => 'It must be that way', 'icon' => 'bi bi-check-circle', 'theme' => 'primary'],
            ['val' => 2, 'text' => 'I am neutral', 'icon' => 'bi bi-dash-lg', 'theme' => 'secondary'],
            ['val' => 3, 'text' => 'I can live with that', 'icon' => 'bi bi-x-circle', 'theme' => 'warning'],
            ['val' => 4, 'text' => 'I dislike that way', 'icon' => 'bi bi-hand-thumbs-down', 'theme' => 'danger'],
        ];

        $cards = [];
        $idx = 0;
        if (isset($criteriaTypes)) {
            foreach ($criteriaTypes as $type) {
                foreach ($type->criteria as $criterion) {
                    $cards[] = [
                        'id' => $criterion->id,
                        'title' => ($idx + 1) . '. ' . $criterion->name,
                        'explanation' => $criterion->description ?? 'Description for this criteria.',
                        'icon' => $icons[$idx % count($icons)],
                        'color' => $colors[$idx % count($colors)],
                        'criteriaName' => strtolower($criterion->name),
                    ];
                    $idx++;
                }
            }
        }
    @endphp

    @push('styles')
        <style>
            .kano-container {
                padding-bottom: 20px;
            }

            .kano-btn {
                transition: all 0.2s ease;
                border-width: 1.5px;
                min-height: 90px;
                cursor: pointer;
                background-color: white;
            }

            .kano-btn:hover {
                transform: translateY(-2px);
            }

            .btn-check:checked+.kano-btn {
                border-width: 2px;
                transform: scale(1.02);
                box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            }
        </style>
    @endpush

    <div class="row justify-content-center kano-container mt-4">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-2 text-start fw-bold text-dark">Kano Model Analysis</h2>
                    <p class="text-start text-muted mb-0">For each criteria, answer two questions to help us understand what features delight you and what features are essential.</p>
                </div>
                <button type="button" onclick="openCriteriaModal()" class="btn btn-primary shadow-sm px-4 py-2" style="background-color: #8b5cf6; border: none; font-weight: 500;">
                    Select Criteria
                </button>
            </div>

            <!-- Progress Card -->
            <div class="card mb-4 border-2 shadow-sm" style="border-radius: 0.75rem; border-color: #8b5cf6;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Your Progress</h5>
                            <small id="progress-text" class="text-muted fw-semibold">0 of 8 questions answered</small>
                        </div>
                        <div class="text-end">
                            <h4 id="progress-percent" class="fw-bold mb-1" style="color: #4b5563;">0%</h4>
                            <small class="text-muted">In Progress</small>
                        </div>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 10px; background-color: #e5e7eb;">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: 0%; background-color: #8b5cf6; transition: width 0.4s ease;">
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('recommendations.calculate') }}" id="kanoForm">
                @csrf
                <input type="hidden" name="weighting_method" value="kano">
                @foreach ($cards as $card)
                    <!-- Main Card Border match theme color -->
                    <div class="card mb-5 bg-white shadow-sm kano-question-card d-none" data-criteria-id="{{ $card['id'] }}"
                        style="border-radius: 0.75rem; border: 2px solid {{ $card['color'] }};">
                        <div class="card-header bg-white p-4 pb-0 border-0 d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center rounded"
                                style="width: 40px; height: 40px; background-color: {{ $card['color'] }};">
                                <i class="{{ $card['icon'] }} text-white fs-5"></i>
                            </div>
                            <h5 class="mb-0 ms-3 fw-bold" style="color: {{ $card['color'] }};">{{ $card['title'] }}</h5>
                        </div>

                        <div class="card-body p-0">
                            <!-- Explanation -->
                            <div class="mx-4 mt-3 mb-4 p-3 rounded d-flex gap-2"
                                style="background-color: #fbf5ff; border: 1px solid #f3e8ff; border-left: 4px solid #a855f7;">
                                <i class="bi bi-info-circle flex-shrink-0" style="color: #a855f7; margin-top: 2px;"></i>
                                <p class="mb-0 small" style="color: #4b5563;"><span
                                        class="fw-bold text-dark">Explanation:</span> {{ $card['explanation'] }}</p>
                            </div>

                            <!-- Functional -->
                            <div class="mx-4 mb-3 p-4 rounded-3"
                                style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                                <p class="fw-semibold mb-3 text-success" style="font-size: 1.05rem;">
                                    <i class="bi bi-hand-thumbs-up me-2"></i> How do you feel if <span
                                        class="text-decoration-underline">{{ $card['criteriaName'] }}</span> is good?
                                </p>
                                <div class="d-flex w-100 gap-2">
                                    @foreach ($options as $opt)
                                        <div class="flex-fill" style="width: 19%;">
                                            <input type="radio" class="btn-check kano-radio F-radio"
                                                name="F_{{ $card['id'] }}" id="F_{{ $card['id'] }}_{{ $opt['val'] }}"
                                                data-criteria="{{ $card['id'] }}" data-type="F"
                                                value="{{ $opt['val'] }}" autocomplete="off">
                                            <label
                                                class="btn btn-outline-{{ $opt['theme'] }} w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 kano-btn text-center"
                                                for="F_{{ $card['id'] }}_{{ $opt['val'] }}">
                                                <i class="{{ $opt['icon'] }} fs-4 mb-2"></i>
                                                <small class="fw-medium"
                                                    style="line-height:1.2; word-wrap: break-word; white-space: normal;">{{ $opt['text'] }}</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Dysfunctional -->
                            <div class="mx-4 mb-4 p-4 rounded-3"
                                style="background-color: #fef2f2; border: 1px solid #fecaca;">
                                <p class="fw-semibold mb-3 text-danger" style="font-size: 1.05rem;">
                                    <i class="bi bi-hand-thumbs-down me-2"></i> How do you feel if <span
                                        class="text-decoration-underline">{{ $card['criteriaName'] }}</span> is poor?
                                </p>
                                <div class="d-flex w-100 gap-2">
                                    @foreach ($options as $opt)
                                        <div class="flex-fill" style="width: 19%;">
                                            <input type="radio" class="btn-check kano-radio D-radio"
                                                name="D_{{ $card['id'] }}"
                                                id="D_{{ $card['id'] }}_{{ $opt['val'] }}"
                                                data-criteria="{{ $card['id'] }}" data-type="D"
                                                value="{{ $opt['val'] }}" autocomplete="off">
                                            <label
                                                class="btn btn-outline-{{ $opt['theme'] }} w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 kano-btn text-center"
                                                for="D_{{ $card['id'] }}_{{ $opt['val'] }}">
                                                <i class="{{ $opt['icon'] }} fs-4 mb-2"></i>
                                                <small class="fw-medium"
                                                    style="line-height:1.2; word-wrap: break-word; white-space: normal;">{{ $opt['text'] }}</small>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="text-center pb-4 text-muted small">
                                <span id="status-F-{{ $card['id'] }}">Functional question pending</span>
                                <span class="mx-2" style="color: #cbd5e1;">—</span>
                                <span id="status-D-{{ $card['id'] }}">Dysfunctional question pending</span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="score_{{ $card['id'] }}" id="input-{{ $card['id'] }}"
                        value="0">
                @endforeach

                <div class="card my-5 bg-white shadow-sm" style="border-radius: 0.75rem; border: 2px solid #8b5cf6;">
                    <div
                        class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clipboard2-check text-muted fs-4"></i>
                            <span id="remaining-text" class="fw-semibold text-secondary ms-3"
                                style="font-size: 1.1rem;">Please answer all 8 questions (8 remaining)</span>
                        </div>
                        <button type="submit" id="proceed-btn"
                            class="btn btn-secondary btn-lg px-4 fw-bold rounded shadow-sm" disabled
                            style="background-color: #cbd5e1; color: #64748b; border: none; min-width: 240px; transition: all 0.3s ease;">
                            Proceed to Rankings <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Criteria Selection Modal -->
    <div class="modal fade" id="criteriaModal" tabindex="-1" aria-labelledby="criteriaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
          <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
            <div>
                <h4 class="modal-title fw-bold text-dark" id="criteriaModalLabel">Select 4 Criteria</h4>
                <p class="text-muted small mb-0 mt-1">Choose exactly four criteria you want to rate.</p>
            </div>
            <button type="button" class="btn-close" onclick="closeCriteriaModal()"></button>
          </div>
          <div class="modal-body p-4 bg-light mt-3" style="border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
            <div class="row g-3">
              @foreach($criteriaTypes as $type)
                @foreach($type->criteria as $criterion)
                  <div class="col-md-6">
                    <label class="d-flex align-items-start p-3 bg-white border rounded shadow-sm cursor-pointer h-100" style="cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#8b5cf6'" onmouseout="this.style.borderColor='#dee2e6'">
                      <input type="checkbox" name="modal_criteria" value="{{ $criterion->id }}" class="form-check-input mt-1 me-3 criteria-checkbox">
                      <div>
                        <span class="fw-bold text-dark d-block">{{ $criterion->name }}</span>
                        <span class="text-muted" style="font-size: 0.8rem;">{{ $criterion->description ?? 'Description for this criteria.' }}</span>
                      </div>
                    </label>
                  </div>
                @endforeach
              @endforeach
            </div>
          </div>
          <div class="modal-footer border-top-0 px-4 py-3 d-flex justify-content-between align-items-center bg-white" style="border-radius: 0 0 1rem 1rem;">
            <span class="text-muted fw-medium small">
              Selected: <span id="modalSelectedCount" class="fw-bold fs-5" style="color: #8b5cf6;">0</span> / 4
            </span>
            <button class="btn px-4 py-2 text-white fw-bold shadow-sm" id="modalConfirmBtn" onclick="confirmCriteriaSelection()" disabled style="background-color: #8b5cf6; border: none; border-radius: 0.5rem; transition: background-color 0.2s;">
              Confirm Selection
            </button>
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Kano Mapping Table based on (Functional, Dysfunctional)
                // Functional F = 0(Like), 1(Must), 2(Neutral), 3(Live With), 4(Dislike)
                // Dysfunctional D = 0(Like), 1(Must), 2(Neutral), 3(Live With), 4(Dislike)
                const kanoTable = [
                    ['Q', 'A', 'A', 'A', 'O'],
                    ['R', 'I', 'I', 'I', 'M'],
                    ['R', 'I', 'I', 'I', 'M'],
                    ['R', 'I', 'I', 'I', 'M'],
                    ['R', 'R', 'R', 'R', 'Q']
                ];

                // Weight constants based on Kano category
                const kanoWeights = {
                    'M': 5,
                    'O': 4,
                    'A': 3,
                    'I': 2,
                    'R': 1,
                    'Q': 0
                };

                // Array of all IDs
                const criteriaIds = [
                    @foreach($cards as $card)
                        '{{ $card['id'] }}',
                    @endforeach
                ];

                const answers = {};
                criteriaIds.forEach(id => {
                    answers[id] = { F: null, D: null };
                });

                let activeCriteria = new Set([]);
                let criteriaModalInstance = new bootstrap.Modal(document.getElementById('criteriaModal'));

                const radios = document.querySelectorAll('.kano-radio');
                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('progress-text');
                const progressPercent = document.getElementById('progress-percent');
                const proceedBtn = document.getElementById('proceed-btn');

                // Criteria Modal Logic
                document.querySelectorAll('.criteria-checkbox').forEach(cb => {
                    cb.addEventListener('change', () => {
                        const checked = document.querySelectorAll('.criteria-checkbox:checked').length;
                        if (checked > 4) {
                            cb.checked = false;
                            alert("You can only select up to 4 criteria.");
                            return;
                        }
                        updateModalBtnState();
                    });
                });

                window.openCriteriaModal = function() {
                    document.querySelectorAll('.criteria-checkbox').forEach(cb => {
                        cb.checked = activeCriteria.has(cb.value);
                    });
                    updateModalBtnState();
                    criteriaModalInstance.show();
                }

                window.closeCriteriaModal = function() {
                    criteriaModalInstance.hide();
                }

                window.confirmCriteriaSelection = function() {
                    const checked = document.querySelectorAll('.criteria-checkbox:checked');
                    if (checked.length !== 4) return;

                    activeCriteria.clear();
                    checked.forEach(cb => activeCriteria.add(cb.value));

                    showActiveCriteriaCards();
                    criteriaModalInstance.hide();
                    updateProgress();
                    calculateScores();
                }

                window.updateModalBtnState = function() {
                    const checked = document.querySelectorAll('.criteria-checkbox:checked').length;
                    document.getElementById('modalSelectedCount').innerText = checked;
                    document.getElementById('modalConfirmBtn').disabled = (checked !== 4);
                }

                function showActiveCriteriaCards() {
                    document.querySelectorAll('.kano-question-card').forEach(card => {
                        const id = card.getAttribute('data-criteria-id');
                        if (activeCriteria.has(id)) {
                            card.classList.remove('d-none');
                        } else {
                            card.classList.add('d-none');
                            // Reset answers if hidden
                            answers[id].F = null;
                            answers[id].D = null;

                            // Uncheck radios visually
                            card.querySelectorAll('.kano-radio').forEach(radio => radio.checked = false);
                            const statusF = document.getElementById('status-F-' + id);
                            const statusD = document.getElementById('status-D-' + id);
                            if (statusF) statusF.innerHTML = 'Functional question pending';
                            if (statusD) statusD.innerHTML = 'Dysfunctional question pending';
                        }
                    });
                }

                // Initial show modal if no criteria
                if (activeCriteria.size < 4) {
                    criteriaModalInstance.show();
                }

                radios.forEach(radio => {
                    radio.addEventListener('click', function(e) {
                        const criteria = this.getAttribute('data-criteria');
                        const type = this.getAttribute('data-type');
                        const val = parseInt(this.value);

                        if (this.dataset.wasChecked === 'true') {
                            // Uncheck it
                            this.checked = false;
                            this.dataset.wasChecked = 'false';
                            answers[criteria][type] = null;

                            const statusEl = document.getElementById('status-' + type + '-' + criteria);
                            if (statusEl) {
                                const questionType = type === 'F' ? 'Functional' : 'Dysfunctional';
                                statusEl.innerHTML = `${questionType} question pending`;
                                statusEl.classList.add('text-muted');
                            }
                        } else {
                            // Deselect siblings in same group
                            const siblings = document.querySelectorAll(`input[name="${this.name}"]`);
                            siblings.forEach(sib => sib.dataset.wasChecked = 'false');

                            this.dataset.wasChecked = 'true';
                            answers[criteria][type] = val;

                            const statusEl = document.getElementById('status-' + type + '-' + criteria);
                            if (statusEl) {
                                const questionType = type === 'F' ? 'Functional' : 'Dysfunctional';
                                statusEl.innerHTML = `<i class="bi bi-check-circle text-success fw-bold me-1"></i><span class="text-success">${questionType} question answered</span>`;
                                statusEl.classList.remove('text-muted');
                            }
                        }

                        updateProgress();
                        calculateScores();
                    });
                });

                function updateProgress() {
                    let answered = 0;
                    activeCriteria.forEach(id => {
                        if (answers[id].F !== null) answered++;
                        if (answers[id].D !== null) answered++;
                    });

                    const total = 8; // 4 criteria * 2 questions
                    const percentage = Math.round((answered / total) * 100);
                    const remaining = total - answered;

                    progressBar.style.width = percentage + '%';
                    progressText.innerHTML = `${answered} of ${total} questions answered`;
                    if (progressPercent) {
                        progressPercent.innerHTML = `${percentage}%`;
                    }

                    const remainingText = document.getElementById('remaining-text');
                    if (remainingText) {
                        if (remaining > 0) {
                            remainingText.innerHTML = `Please answer all ${total} questions (${remaining} remaining)`;
                        } else {
                            remainingText.innerHTML = `All ${total} questions answered. You may proceed!`;
                        }
                    }

                    if (answered === total) {
                        proceedBtn.removeAttribute('disabled');
                        proceedBtn.style.backgroundColor = '#8b5cf6';
                        proceedBtn.style.color = '#ffffff';
                        progressBar.style.backgroundColor = '#10b981'; // Green on finish
                    } else {
                        proceedBtn.setAttribute('disabled', 'disabled');
                        proceedBtn.style.backgroundColor = '#cbd5e1';
                        proceedBtn.style.color = '#64748b';
                        progressBar.style.backgroundColor = '#8b5cf6'; // Purple while working
                    }
                }

                function calculateScores() {
                    criteriaIds.forEach(id => {
                        const input = document.getElementById('input-' + id);
                        if (!input) return;

                        // only set if active
                        if (activeCriteria.has(id)) {
                            const fVal = answers[id].F;
                            const dVal = answers[id].D;

                            if (fVal !== null && dVal !== null) {
                                const category = kanoTable[fVal][dVal];
                                const weight = kanoWeights[category];
                                input.value = weight;
                            } else {
                                input.value = 0;
                            }
                        } else {
                            input.value = 0;
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
