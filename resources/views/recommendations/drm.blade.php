@extends('layouts.user')

@push('styles')
    <style>
    :root {
      --drm-page-bg: #ffffff;
      --drm-text-primary: #111827;
      --drm-text-secondary: #6b7280;
      --drm-border: #e5e7eb;
    }

    .hidden {
      display: none !important;
    }

    .drm-page {
      min-height: 100vh;
      background: var(--drm-page-bg);
    }

    .drm-shell {
      max-width: 56rem;
      margin: 0 auto;
      padding: 2rem 1.5rem;
    }

    .drm-header {
      margin-bottom: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
    }

    .drm-title {
      margin: 0 0 0.5rem;
      color: var(--drm-text-primary);
      font-size: 1.5rem;
      line-height: 1.25;
      font-weight: 700;
    }

    .drm-subtitle {
      margin: 0;
      color: var(--drm-text-secondary);
      font-size: 0.875rem;
      line-height: 1.5;
    }

    .drm-btn,
    .rating-btn,
    .modal-close-btn {
      cursor: pointer;
      border: 0;
      background: transparent;
    }

    .drm-btn-primary {
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
      font-weight: 500;
      color: #fff;
      background: #7c3aed;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
      transition: background-color 0.2s ease;
    }

    .drm-btn-primary:hover {
      background: #6d28d9;
    }

    .error-alert {
      margin-bottom: 1.5rem;
      padding: 1rem;
      color: #b91c1c;
      border-left: 4px solid #ef4444;
      background: #fee2e2;
    }

    .criteria-list {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .drm-card {
      --grad-start: #a855f7;
      --grad-end: #ec4899;
      --tint-bg: rgba(245, 243, 255, 0.95);
      --accent: #a855f7;
      --accent-soft: #c084fc;
      --info-color: #9333ea;
      position: relative;
      margin-top: 1.5rem;
      border-radius: 0.75rem;
      background: #fff;
      box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.1);
    }

    .drm-card.palette-0 {
      --grad-start: #a855f7;
      --grad-end: #ec4899;
      --tint-bg: rgba(245, 243, 255, 0.95);
      --accent: #a855f7;
      --accent-soft: #c084fc;
      --info-color: #9333ea;
    }

    .drm-card.palette-1 {
      --grad-start: #3b82f6;
      --grad-end: #06b6d4;
      --tint-bg: rgba(239, 246, 255, 0.95);
      --accent: #3b82f6;
      --accent-soft: #60a5fa;
      --info-color: #2563eb;
    }

    .drm-card.palette-2 {
      --grad-start: #22c55e;
      --grad-end: #10b981;
      --tint-bg: rgba(240, 253, 244, 0.95);
      --accent: #22c55e;
      --accent-soft: #4ade80;
      --info-color: #16a34a;
    }

    .drm-card.palette-3 {
      --grad-start: #f97316;
      --grad-end: #ef4444;
      --tint-bg: rgba(255, 247, 237, 0.95);
      --accent: #f97316;
      --accent-soft: #fb923c;
      --info-color: #ea580c;
    }

    .drm-card.palette-4 {
      --grad-start: #6366f1;
      --grad-end: #a855f7;
      --tint-bg: rgba(238, 242, 255, 0.95);
      --accent: #6366f1;
      --accent-soft: #818cf8;
      --info-color: #4f46e5;
    }

    .card-border {
      position: absolute;
      inset: 0;
      border-radius: 0.75rem;
      padding: 3px;
      background: linear-gradient(135deg, var(--grad-start), var(--grad-end));
      pointer-events: none;
    }

    .card-border-inner {
      width: 100%;
      height: 100%;
      border-radius: 9px;
      background: #fff;
    }

    .card-body {
      position: relative;
      padding: 1.5rem;
    }

    .criterion-head {
      margin-bottom: 1rem;
    }

    .criterion-top {
      margin-bottom: 0.75rem;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 0.75rem;
    }

    .criterion-meta {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .criterion-icon {
      width: 3rem;
      height: 3rem;
      border-radius: 0.75rem;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 1.5rem;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
      background: linear-gradient(135deg, var(--grad-start), var(--grad-end));
    }

    .criterion-title {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 700;
      line-height: 1.3;
      color: transparent;
      background: linear-gradient(90deg, var(--grad-start), var(--grad-end));
      background-clip: text;
      -webkit-background-clip: text;
    }

    .rating-badge {
      border-radius: 9999px;
      padding: 0.625rem 1.25rem;
      color: #fff;
      font-size: 0.875rem;
      font-weight: 700;
      white-space: nowrap;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
      background: linear-gradient(90deg, var(--grad-start), var(--grad-end));
    }

    .criteria-info-box {
      margin-bottom: 1.25rem;
      border-left: 3.5px solid var(--accent);
      border-radius: 14px;
      padding: 1rem;
      display: flex;
      gap: 0.75rem;
      background: var(--tint-bg);
    }

    .criteria-info-icon {
      margin-top: 0.125rem;
      flex-shrink: 0;
      font-size: 1.125rem;
      color: var(--info-color);
    }

    .criteria-info-text {
      margin: 0 0 0.25rem;
      color: #1f2937;
      font-size: 0.875rem;
      line-height: 1.5;
    }

    .criteria-info-text strong {
      color: #111827;
    }

    .criteria-question {
      margin: 0 0 1rem;
      color: #374151;
      font-size: 1rem;
      font-weight: 500;
    }

    .rating-wrap {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .rating-scale {
      margin-bottom: 0.75rem;
      padding: 0 0.25rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #6b7280;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .rating-scale-item {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }

    .dot {
      width: 0.375rem;
      height: 0.375rem;
      border-radius: 9999px;
    }

    .dot-red {
      background: #ef4444;
    }

    .dot-green {
      background: #22c55e;
    }

    .rating-grid {
      display: grid;
      grid-template-columns: repeat(9, minmax(0, 1fr));
      gap: 0.75rem;
    }

    .rating-btn {
      position: relative;
      z-index: 10;
      width: 100%;
      aspect-ratio: 1 / 1;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .rating-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .btn-bg-wrapper {
      position: absolute;
      inset: 0;
      z-index: 10;
      border-radius: 14px;
      border: 2px solid #d1d5db;
      transition: all 0.2s ease;
    }

    .inner-bg {
      width: 100%;
      height: 100%;
      border-radius: 12px;
      background: #fff;
      transition: all 0.2s ease;
    }

    .btn-text {
      position: relative;
      z-index: 20;
      color: #4b5563;
      font-size: 1.125rem;
      font-weight: 900;
      transition: all 0.2s ease;
    }

    .rating-btn.is-selected .btn-bg-wrapper {
      z-index: 20;
      border-color: transparent;
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .rating-btn.is-selected .inner-bg {
      border-radius: 14px;
      background: linear-gradient(135deg, var(--grad-start), var(--grad-end));
    }

    .rating-btn.is-selected .btn-text {
      color: #fff;
      font-size: 1.25rem;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.28);
    }

    .progress-box {
      margin-top: 2rem;
      border: 2px solid #a855f7;
      border-radius: 0.75rem;
      background: #fff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .progress-inner {
      padding: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
    }

    .progress-label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .progress-message {
      margin: 0;
      color: #374151;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .text-success {
      color: #16a34a;
      font-weight: 600;
    }

    .text-strong {
      font-weight: 600;
    }

    .proceed-btn {
      border-radius: 0.5rem;
      padding: 0.5rem 1.25rem;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      font-weight: 500;
      transition: all 0.2s ease;
      border: 0;
    }

    .proceed-btn.is-enabled {
      color: #374151;
      background: #e5e7eb;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
      cursor: pointer;
    }

    .proceed-btn.is-enabled:hover {
      background: #d1d5db;
    }

    .proceed-btn.is-disabled {
      color: #6b7280;
      background: #e5e7eb;
      cursor: not-allowed;
    }

    .criteria-modal {
      position: fixed;
      inset: 0;
      z-index: 50;
      min-height: 100vh;
      padding: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(0, 0, 0, 0.5);
    }

    .criteria-modal-panel {
      width: 100%;
      max-width: 42rem;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
      border-radius: 0.75rem;
      background: #fff;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      padding: 1.5rem;
      border-bottom: 1px solid #f3f4f6;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-title {
      margin: 0;
      color: #111827;
      font-size: 1.25rem;
      font-weight: 700;
    }

    .modal-subtitle {
      margin: 0.25rem 0 0;
      color: #6b7280;
      font-size: 0.875rem;
    }

    .modal-close-btn {
      color: #9ca3af;
      transition: color 0.2s ease;
    }

    .modal-close-btn:hover {
      color: #4b5563;
    }

    .modal-body {
      flex: 1;
      overflow-y: auto;
      padding: 1.5rem;
      background: #f9fafb;
    }

    .criteria-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1rem;
    }

    .criteria-option {
      padding: 1rem;
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      background: #fff;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
      cursor: pointer;
      transition: border-color 0.2s ease;
    }

    .criteria-option:hover {
      border-color: #c084fc;
    }

    .criteria-checkbox {
      width: 1rem;
      height: 1rem;
      margin-top: 0.2rem;
      accent-color: #9333ea;
      cursor: pointer;
    }

    .criteria-option-name {
      display: block;
      color: #1f2937;
      font-weight: 700;
    }

    .criteria-option-desc {
      margin-top: 0.2rem;
      display: -webkit-box;
      overflow: hidden;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      color: #6b7280;
      font-size: 0.75rem;
      line-height: 1.4;
    }

    .modal-footer {
      padding: 1.25rem;
      border-top: 1px solid #f3f4f6;
      border-radius: 0 0 0.75rem 0.75rem;
      background: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-count {
      margin: 0;
      color: #4b5563;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .modal-count-value {
      color: #9333ea;
      font-weight: 700;
    }

    .modal-confirm-btn {
      border: 0;
      border-radius: 0.5rem;
      padding: 0.5rem 1.5rem;
      color: #fff;
      font-weight: 500;
      background: #7c3aed;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .modal-confirm-btn:hover {
      background: #6d28d9;
    }

    .modal-confirm-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    @media (max-width: 900px) {
      .rating-grid {
        gap: 0.5rem;
      }
    }

    @media (max-width: 768px) {
      .drm-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .criterion-top {
        flex-direction: column;
      }

      .progress-inner {
        flex-direction: column;
        align-items: flex-start;
      }

      .criteria-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 640px) {
      .drm-shell {
        padding: 1.25rem 0.75rem;
      }

      .criteria-grid {
        grid-template-columns: 1fr;
      }
    }
    </style>
@endpush

@section('content')
@php
   $icons = ['bi-geo-alt', 'bi-building', 'bi-box', 'bi-star', 'bi-tag'];

   $index = 0;
@endphp

<div class="drm-page">
  <div class="drm-shell">
  <div class="drm-header">
      <div>
    <h2 class="drm-title">Direct Rating Method</h2>
    <p class="drm-subtitle">
          Rate exactly four criteria from 1 to 9 based on their importance to you. (1 = Not Important, 9 = Very Important)
        </p>
      </div>
      <button type="button" onclick="openCriteriaModal()" class="drm-btn drm-btn-primary">
        Select Criteria
      </button>
    </div>

    @if(session('error'))
    <div class="error-alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form action="{{ route('recommendations.calculate') }}" method="POST" id="ratingForm">
      @csrf
    <input type="hidden" name="weighting_method" value="drm">
      <div class="criteria-list">
        @foreach($criteriaTypes as $type)
          @foreach($type->criteria as $criterion)
            @php
              $colorIdx = $index % 5;
              $icon = $icons[$colorIdx];

              $currentRating = $userWeights[$criterion->id] ?? 0;
              $index++;
            @endphp

            <div class="criteria-card drm-card palette-{{ $colorIdx }} hidden" data-criteria-id="{{ $criterion->id }}" id="card_{{ $criterion->id }}">
              <!-- Gradient Border Wrapper -->
              <div class="card-border">
                <div class="card-border-inner"></div>
              </div>

              <div class="card-body">
                <!-- Hidden Input -->
                <input type="hidden" name="score_{{ $criterion->id }}" id="input_{{ $criterion->id }}" value="{{ $currentRating }}">

                <div class="criterion-head">
                  <div class="criterion-top">
                    <div class="criterion-meta">
                      <div class="criterion-icon">
                        <i class="bi {{ $icon }}"></i>
                      </div>
                      <div>
                        <h3 class="criterion-title">
                          {{ $index }}. {{ $criterion->name }}
                        </h3>
                      </div>
                    </div>
                    <span id="badge_{{ $criterion->id }}" class="rating-badge {{ $currentRating > 0 ? '' : 'hidden' }}">
                      Rating: <span class="rating-display">{{ $currentRating }}</span>
                    </span>
                  </div>

                  <div class="criteria-info-box">
                    <i class="bi bi-info-circle criteria-info-icon"></i>
                    <div>
                      <p class="criteria-info-text">
                        <strong>Explanation:</strong> {{ $criterion->description ?? 'Description for this criteria.' }}
                      </p>
                    </div>
                  </div>

                  <p class="criteria-question">Do you consider {{ strtolower($criterion->name) }} important to you?</p>
                </div>

                <div class="rating-wrap">
                  <div class="rating-scale">
                    <span class="rating-scale-item">
                      <span class="dot dot-red"></span>
                      Not Important
                    </span>
                    <span class="rating-scale-item">
                      Very Important
                      <span class="dot dot-green"></span>
                    </span>
                  </div>

                  <div class="rating-grid">
                    @for($i = 1; $i <= 9; $i++)
                      @php
                        $isSelected = ($currentRating == $i);
                      @endphp
                      <button
                        type="button"
                        onclick="setRating('{{ $criterion->id }}', {{ $i }})"
                        id="btn_{{ $criterion->id }}_{{ $i }}"
                        class="rating-btn rating-btn-{{ $criterion->id }} {{ $isSelected ? 'is-selected' : '' }}"
                        data-palette="{{ $colorIdx }}"
                      >
                         <div class="btn-bg-wrapper">
                           <div class="inner-bg"></div>
                         </div>
                         <span class="btn-text">{{ $i }}</span>
                      </button>
                    @endfor
                  </div>
                </div>
              </div>
            </div>

          @endforeach
        @endforeach
      </div>

      <div class="progress-box">
        <div class="progress-inner">
          <div class="progress-label">
            <span>📝</span>
            <p id="completionMessage" class="progress-message">
              @if(count($userWeights) >= 4)
                <span class="text-success">4 criteria selected</span>
              @else
                <span class="text-strong"><span id="answeredCount">{{ count($userWeights) }}</span> of 4</span> criteria selected
              @endif
            </p>
          </div>
          <button
            type="submit"
            id="proceedBtn"
            class="proceed-btn {{ count($userWeights) >= 4 ? 'is-enabled' : 'is-disabled' }}"
            {{ count($userWeights) >= 4 ? '' : 'disabled' }}
          >
            <span>Proceed to Rankings</span>
            <i class="bi bi-arrow-right"></i>
          </button>
        </div>
      </div>

    </form>
  </div>
</div>

<!-- Criteria Selection Modal -->
<div id="criteriaModal" class="criteria-modal hidden">
  <div class="criteria-modal-panel" onclick="event.stopPropagation()">
    <div class="modal-header">
      <div>
        <h3 class="modal-title">Select 4 Criteria</h3>
        <p class="modal-subtitle">Choose exactly four criteria you want to rate.</p>
      </div>
      <button onclick="closeCriteriaModal()" class="modal-close-btn">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <div class="modal-body">
      <div class="criteria-grid">
        @foreach($criteriaTypes as $type)
          @foreach($type->criteria as $criterion)
            <label class="criteria-option">
              <input type="checkbox" name="modal_criteria" value="{{ $criterion->id }}" class="criteria-checkbox">
              <div>
                <span class="criteria-option-name">{{ $criterion->name }}</span>
                <span class="criteria-option-desc">{{ $criterion->description }}</span>
              </div>
            </label>
          @endforeach
        @endforeach
      </div>
    </div>

    <div class="modal-footer">
      <p class="modal-count">
        Selected: <span id="modalSelectedCount" class="modal-count-value">0</span> / 4
      </p>
      <button id="modalConfirmBtn" onclick="confirmCriteriaSelection()" class="modal-confirm-btn" disabled>
        Confirm Selection
      </button>
    </div>
  </div>
</div>

<script>
  const requiredQuestions = 4;
  let activeCriteria = new Set([
      @foreach($userWeights as $id => $weight)
          @if($weight > 0)
          '{{ $id }}',
          @endif
      @endforeach
  ]);
  const answeredSet = new Set([...activeCriteria]);

  document.addEventListener('DOMContentLoaded', () => {
      // Check checkboxes dynamically
      document.querySelectorAll('.criteria-checkbox').forEach(cb => {
          cb.addEventListener('change',() => {
              const checked = document.querySelectorAll('.criteria-checkbox:checked').length;
              if (checked > 4) {
                 cb.checked = false;
                 alert("You can only select up to 4 criteria.");
                 return;
              }
              updateModalBtnState();
          });
      });

      // Show modal initially if no active criteria
      if(activeCriteria.size < 4) {
          openCriteriaModal();
      } else {
          showActiveCriteriaCards();
          updateProgress();
      }

      // Allow closing by clicking outside the modal
      document.getElementById('criteriaModal').addEventListener('click', (e) => {
          if (e.target === document.getElementById('criteriaModal') && activeCriteria.size === 4) {
              closeCriteriaModal();
          }
      });
  });

  function openCriteriaModal() {
      // populate checkboxes initially matching current selection
      document.querySelectorAll('.criteria-checkbox').forEach(cb => {
          cb.checked = activeCriteria.has(cb.value);
      });
      updateModalBtnState();
      document.getElementById('criteriaModal').classList.remove('hidden');
  }

  function closeCriteriaModal() {
      document.getElementById('criteriaModal').classList.add('hidden');
  }

  function updateModalBtnState() {
      const checked = document.querySelectorAll('.criteria-checkbox:checked').length;
      document.getElementById('modalSelectedCount').innerText = checked;
      document.getElementById('modalConfirmBtn').disabled = (checked !== 4);
  }

  function confirmCriteriaSelection() {
      const checked = document.querySelectorAll('.criteria-checkbox:checked');
      if (checked.length !== 4) return;

      activeCriteria.clear();
      checked.forEach(cb => activeCriteria.add(cb.value));

      showActiveCriteriaCards();
      document.getElementById('criteriaModal').classList.add('hidden');
      updateProgress();
  }

  function showActiveCriteriaCards() {
      document.querySelectorAll('.criteria-card').forEach(card => {
          const id = card.getAttribute('data-criteria-id');
          if (activeCriteria.has(id)) {
              card.classList.remove('hidden');
          } else {
              card.classList.add('hidden');
              // Clear score memory internally
              const input = document.getElementById('input_' + id);
              if(input) {
                  input.value = 0;
                  // Clear UI stars
                  const stars = card.querySelectorAll('svg');
                  stars.forEach(s => {
                      s.classList.remove('text-yellow-400');
                      s.classList.add('text-gray-300');
                  });
              }
              answeredSet.delete(id);
          }
      });
  }

  function setRating(criteriaId, value) {
      const input = document.getElementById('input_' + criteriaId);
      const isDeselecting = parseInt(input.value, 10) === value;

      // Update hidden input
      input.value = isDeselecting ? 0 : value;

      // Update badge
      const badge = document.getElementById('badge_' + criteriaId);
      if(badge) {
          if (isDeselecting) {
              badge.classList.add('hidden');
          } else {
              badge.classList.remove('hidden');
              badge.querySelector('.rating-display').innerText = value;
          }
      }

      // Update buttons style
      const btnGroup = document.querySelectorAll('.rating-btn-' + criteriaId);
      btnGroup.forEach(btn => {
          const parts = btn.id.split('_');
          const btnValue = parseInt(parts[parts.length - 1], 10);

          if (!isDeselecting && btnValue === value) {
            btn.classList.add('is-selected');
          } else {
            btn.classList.remove('is-selected');
          }
      });

      // Manage answered set
      if(activeCriteria.has(criteriaId.toString())) {
          if (isDeselecting) {
              answeredSet.delete(criteriaId.toString());
          } else {
              answeredSet.add(criteriaId.toString());
          }
      }
      updateProgress();
  }

  function updateProgress() {
      // Calculate how many active criteria have been answered
      let answeredActiveCount = 0;
      activeCriteria.forEach(id => {
          if (answeredSet.has(id)) {
              answeredActiveCount++;
          }
      });

      const isComplete = (answeredActiveCount === requiredQuestions);
      const proceedBtn = document.getElementById('proceedBtn');
      const completionMessage = document.getElementById('completionMessage');

      if (isComplete) {
          proceedBtn.disabled = false;
          proceedBtn.className = "proceed-btn is-enabled";

          completionMessage.innerHTML = "<span class='text-success'>4 criteria rated</span>";
      } else {
          proceedBtn.disabled = true;
          proceedBtn.className = "proceed-btn is-disabled";

          completionMessage.innerHTML = `<span class="text-strong"><span id="answeredCount">${Math.min(answeredActiveCount, 4)}</span> of 4</span> criteria rated`;
      }
  }
</script>

@endsection
aa
