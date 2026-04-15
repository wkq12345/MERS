@extends('layouts.user')

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
    <style>
        /* Essential preflight for buttons and inputs used in the design */
        button {
            background-color: transparent;
            background-image: none;
            cursor: pointer;
        }
        .bg-white { background-color: #fff !important; }
    </style>
@endpush

@section('content')
<!-- Hidden safely list dynamic classes for Tailwind CDN -->
<div class="hidden bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 bg-gray-200 text-gray-700 hover:bg-gray-300 shadow-sm text-gray-500 cursor-not-allowed border-2 border-gray-200 text-green-600"></div>

@php
   $gradients = [
       'from-purple-500 to-pink-500',
       'from-blue-500 to-cyan-500',
       'from-green-500 to-emerald-500',
       'from-orange-500 to-red-500',
       'from-indigo-500 to-purple-500'
   ];
   $bgColors = ['bg-purple-50', 'bg-blue-50', 'bg-green-50', 'bg-orange-50', 'bg-indigo-50'];
   $accentHoverBorder = ['hover:border-purple-400', 'hover:border-blue-400', 'hover:border-green-400', 'hover:border-orange-400', 'hover:border-indigo-400'];
   $accentHoverBg = ['hover:bg-purple-50', 'hover:bg-blue-50', 'hover:bg-green-50', 'hover:bg-orange-50', 'hover:bg-indigo-50'];
   $accentBorder = ['border-purple-500', 'border-blue-500', 'border-green-500', 'border-orange-500', 'border-indigo-500'];
   $accentTextInfo = ['text-purple-600', 'text-blue-600', 'text-green-600', 'text-orange-600', 'text-indigo-600'];
   $accentTextExpl = ['text-purple-900', 'text-blue-900', 'text-green-900', 'text-orange-900', 'text-indigo-900'];

   $icons = ['bi-geo-alt', 'bi-building', 'bi-box', 'bi-star', 'bi-tag'];

   $index = 0;
   $allCriteriaIds = [];
   foreach($criteriaTypes as $type) {
       foreach($type->criteria as $criterion) {
           $allCriteriaIds[] = $criterion->id;
       }
   }
@endphp

<div class="min-h-screen bg-white">
  <div class="max-w-4xl mx-auto px-6 py-8">
    <div class="mb-8 flex justify-between items-center">
      <div>
        <h2 class="text-2xl mb-2 font-bold text-gray-900">Direct Rating Method</h2>
        <p class="text-gray-500 text-sm">
          Rate exactly four criteria from 1 to 9 based on their importance to you. (1 = Not Important, 9 = Very Important)
        </p>
      </div>
      <button type="button" onclick="openCriteriaModal()" class="px-4 py-2 bg-purple-600 text-white font-medium rounded-lg shadow hover:bg-purple-700 transition">
        Select Criteria
      </button>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form action="{{ route('recommendations.calculate') }}" method="POST" id="ratingForm">
      @csrf
    <input type="hidden" name="weighting_method" value="drm">
      <div class="space-y-6">
        @foreach($criteriaTypes as $type)
          @foreach($type->criteria as $criterion)
            @php
              $colorIdx = $index % count($gradients);
              $gradient = $gradients[$colorIdx];
              $bgColor = $bgColors[$colorIdx];

              $hBorder = $accentHoverBorder[$colorIdx];
              $hBg = $accentHoverBg[$colorIdx];
              $borderColor = $accentBorder[$colorIdx];
              $txtInfo = $accentTextInfo[$colorIdx];
              $txtExpl = $accentTextExpl[$colorIdx];
              $icon = $icons[$colorIdx];

              $currentRating = $userWeights[$criterion->id] ?? 0;
              $index++;

              $selectedClass = "bg-white text-gray-700 border-2 rounded-lg transition-all scale-100 shadow-md font-bold text-lg";
              $unselectedClass = "bg-white text-gray-700 border border-gray-300 rounded-lg hover:border-$hBorder hover:bg-$hBg hover:scale-105 transition-all font-bold text-lg";
            @endphp

            <div class="relative bg-white rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.1)] transition-shadow mt-6 criteria-card hidden" data-criteria-id="{{ $criterion->id }}" id="card_{{ $criterion->id }}">
              <!-- Gradient Border Wrapper -->
              <div class="absolute inset-0 rounded-xl bg-gradient-to-br {{ $gradient }} pointer-events-none" style="padding: 3px;">
                <div class="w-full h-full bg-white rounded-[9px]"></div>
              </div>

              <div class="relative p-6">
                <!-- Hidden Input -->
                <input type="hidden" name="score_{{ $criterion->id }}" id="input_{{ $criterion->id }}" value="{{ $currentRating }}">

                <div class="mb-4">
                  <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-4">
                      <div class="w-12 h-12 bg-gradient-to-br {{ $gradient }} rounded-xl flex items-center justify-center shadow-sm">
                        <i class="bi {{ $icon }} text-white text-2xl"></i>
                      </div>
                      <div>
                        <h3 class="text-xl font-bold bg-gradient-to-r {{ $gradient }} bg-clip-text text-transparent">
                          {{ $index }}. {{ $criterion->name }}
                        </h3>
                      </div>
                    </div>
                    <span id="badge_{{ $criterion->id }}" class="px-5 py-2.5 bg-gradient-to-r {{ $gradient }} text-white rounded-full text-sm font-bold shadow-md {{ $currentRating > 0 ? '' : 'hidden' }}">
                      Rating: <span class="rating-display">{{ $currentRating }}</span>
                    </span>
                  </div>

                  <div class="flex gap-3 p-4 rounded-[14px] mb-5 border-l-[3.5px] {{ $borderColor }} {{ $bgColor }}" style="--tw-bg-opacity: 0.4;">
                    <i class="bi bi-info-circle {{ $txtInfo }} mt-0.5 flex-shrink-0 text-lg"></i>
                    <div>
                      <p class="text-sm text-gray-800 mb-1">
                        <span class="font-bold text-gray-900">Explanation:</span> {{ $criterion->description ?? 'Description for this criteria.' }}
                      </p>
                    </div>
                  </div>

                  <p class="text-gray-700 text-base mb-4 font-medium">Do you consider {{ strtolower($criterion->name) }} important to you?</p>
                </div>

                <div class="space-y-3">
                  <div class="flex items-center justify-between text-xs font-semibold text-gray-500 mb-3 px-1">
                    <span class="flex items-center gap-1">
                      <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                      Not Important
                    </span>
                    <span class="flex items-center gap-1">
                      Very Important
                      <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    </span>
                  </div>

                  <div class="grid grid-cols-9 gap-3">
                    @for($i = 1; $i <= 9; $i++)
                      @php
                        $isSelected = ($currentRating == $i);
                      @endphp
                      <button
                        type="button"
                        onclick="setRating('{{ $criterion->id }}', {{ $i }})"
                        id="btn_{{ $criterion->id }}_{{ $i }}"
                        class="relative rating-btn-{{ $criterion->id }} aspect-square flex items-center justify-center rounded-[14px] text-gray-700 font-bold transition-all z-10 hover:shadow-md hover:-translate-y-0.5"
                        data-color-idx="{{ $colorIdx }}"
                        data-gradient="{{ $gradient }}"
                      >
                         <div class="btn-bg-wrapper absolute inset-0 rounded-[14px] transition-all {{ $isSelected ? 'shadow-[0_4px_12px_rgba(0,0,0,0.15)] scale-110 z-20' : 'border-[2px] border-gray-300 z-10' }}">
                            <div class="inner-bg w-full h-full rounded-[14px] transition-all {{ $isSelected ? 'bg-gradient-to-br ' . $gradient : 'bg-white' }}"></div>
                         </div>
                         <span class="btn-text relative z-20 transition-all font-black text-lg {{ $isSelected ? 'text-white text-xl drop-shadow-md' : 'text-gray-600' }}">{{ $i }}</span>
                      </button>
                    @endfor
                  </div>
                </div>
              </div>
            </div>

          @endforeach
        @endforeach
      </div>

      <div class="mt-8 bg-white border-2 border-purple-500 rounded-xl shadow-sm transition-shadow">
        <div class="p-6 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-xl">📝</span>
            <p id="completionMessage" class="text-gray-700 font-medium text-sm">
              @if(count($userWeights) >= 4)
                <span class="font-semibold text-green-600">4 criteria selected</span>
              @else
                <span class="font-semibold"><span id="answeredCount">{{ count($userWeights) }}</span> of 4</span> criteria selected
              @endif
            </p>
          </div>
          <button
            type="submit"
            id="proceedBtn"
            class="px-5 py-2 rounded-lg flex items-center gap-2 transition-all font-medium text-sm {{ count($userWeights) >= 4 ? 'bg-gray-200 text-gray-700 hover:bg-gray-300 shadow-sm' : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}"
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
<div id="criteriaModal" class="fixed inset-0 min-h-screen bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col" onclick="event.stopPropagation()">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
      <div>
        <h3 class="text-xl font-bold text-gray-900">Select 4 Criteria</h3>
        <p class="text-sm text-gray-500 mt-1">Choose exactly four criteria you want to rate.</p>
      </div>
      <button onclick="closeCriteriaModal()" class="text-gray-400 hover:text-gray-600 transition">
        <i class="bi bi-x-lg text-xl"></i>
      </button>
    </div>

    <div class="p-6 overflow-y-auto flex-1 bg-gray-50">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($criteriaTypes as $type)
          @foreach($type->criteria as $criterion)
            <label class="flex items-start p-4 bg-white rounded-lg border border-gray-200 cursor-pointer hover:border-purple-400 transition shadow-sm">
              <input type="checkbox" name="modal_criteria" value="{{ $criterion->id }}" class="mt-1 mr-3 criteria-checkbox w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
              <div>
                <span class="font-bold text-gray-800 block">{{ $criterion->name }}</span>
                <span class="text-xs text-gray-500 line-clamp-2 md:line-clamp-1 mt-0.5">{{ $criterion->description }}</span>
              </div>
            </label>
          @endforeach
        @endforeach
      </div>
    </div>

    <div class="p-5 border-t border-gray-100 bg-white rounded-b-xl flex justify-between items-center">
      <span class="text-sm font-medium text-gray-600">
        Selected: <span id="modalSelectedCount" class="font-bold text-purple-600">0</span> / 4
      </span>
      <button id="modalConfirmBtn" onclick="confirmCriteriaSelection()" class="px-6 py-2 bg-purple-600 text-white font-medium rounded-lg shadow hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
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
          const gradientClass = btn.getAttribute('data-gradient');
          const bgWrapper = btn.querySelector('.btn-bg-wrapper');
          const innerBg = btn.querySelector('.inner-bg');
          const btnText = btn.querySelector('.btn-text');

          if (!isDeselecting && btnValue === value) {
              // Active state
              bgWrapper.className = `btn-bg-wrapper absolute inset-0 rounded-[14px] transition-all shadow-[0_4px_12px_rgba(0,0,0,0.15)] scale-110 z-20`;

              // Apply thick padding for the selected state and force gradient onto content too
              bgWrapper.style.padding = '0';
              innerBg.className = `inner-bg w-full h-full rounded-[14px] transition-all bg-gradient-to-br ${gradientClass}`;

              btnText.className = `btn-text relative z-20 transition-all font-black text-xl text-white drop-shadow-md`;
          } else {
              // Inactive state - just normal line border
              bgWrapper.className = `btn-bg-wrapper absolute inset-0 rounded-[14px] transition-all border-[2px] border-gray-300 z-10`;
              bgWrapper.style.padding = '0';
              innerBg.className = `inner-bg w-full h-full rounded-[12px] transition-all bg-white`;

              btnText.className = `btn-text relative z-20 transition-all font-black text-lg text-gray-600`;
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
          proceedBtn.className = "px-5 py-2 rounded-lg flex items-center gap-2 transition-all font-medium text-sm bg-gray-200 text-gray-700 hover:bg-gray-300 shadow-sm";

          completionMessage.innerHTML = "<span class='font-semibold text-green-600'>4 criteria rated</span>";
      } else {
          proceedBtn.disabled = true;
          proceedBtn.className = "px-5 py-2 rounded-lg flex items-center gap-2 transition-all font-medium text-sm bg-gray-200 text-gray-500 cursor-not-allowed";

          completionMessage.innerHTML = `<span class="font-semibold"><span id="answeredCount">${Math.min(answeredActiveCount, 4)}</span> of 4</span> criteria rated`;
      }
  }
</script>

@endsection
