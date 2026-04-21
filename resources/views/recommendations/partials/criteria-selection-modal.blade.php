@php
    $modalId = $modalId ?? 'criteriaModal';
    $modalLabelId = $modalLabelId ?? 'criteriaModalLabel';
    $title = $title ?? 'Select 4 Criteria';
    $subtitle = $subtitle ?? 'Choose exactly four criteria.';
    $confirmText = $confirmText ?? 'Confirm Selection';
    $confirmColor = $confirmColor ?? '#8b5cf6';
    $hoverBorderColor = $hoverBorderColor ?? '#8b5cf6';
    $selectedCountColor = $selectedCountColor ?? '#8b5cf6';
    $maxSelection = $maxSelection ?? 4;
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalLabelId }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
      <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
        <div>
            <h4 class="modal-title fw-bold text-dark" id="{{ $modalLabelId }}">{{ $title }}</h4>
            <p class="text-muted small mb-0 mt-1">{{ $subtitle }}</p>
        </div>
        <button type="button" class="btn-close" onclick="closeCriteriaModal()"></button>
      </div>
      <div class="modal-body p-4 bg-light mt-3" style="border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
        <div class="row g-3">
          @foreach($criteriaTypes as $type)
            @foreach($type->criteria as $criterion)
              <div class="col-md-6">
                <label class="d-flex align-items-start p-3 bg-white border rounded shadow-sm cursor-pointer h-100" style="cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='{{ $hoverBorderColor }}'" onmouseout="this.style.borderColor='#dee2e6'">
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
          Selected: <span id="modalSelectedCount" class="fw-bold fs-5" style="color: {{ $selectedCountColor }};">0</span> / {{ $maxSelection }}
        </span>
        <button class="btn px-4 py-2 text-white fw-bold shadow-sm" id="modalConfirmBtn" onclick="confirmCriteriaSelection()" disabled style="background-color: {{ $confirmColor }}; border: none; border-radius: 0.5rem; transition: background-color 0.2s;">
          {{ $confirmText }}
        </button>
      </div>
    </div>
  </div>
</div>
