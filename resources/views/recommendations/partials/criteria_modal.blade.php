<div>
    <div class="row g-3">
        @foreach($criteriaTypes as $type)
            @foreach($type->criteria as $criterion)
                <div class="col-md-6">
                    <label class="d-flex align-items-start p-3 bg-white border rounded shadow-sm cursor-pointer h-100"
                        style="cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.borderColor='#8b5cf6'"
                        onmouseout="this.style.borderColor='#dee2e6'">
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

    <div class="modal-footer border-top-0 px-4 py-3 d-flex justify-content-between align-items-center bg-white" style="border-radius: 0 0 1rem 1rem;">
        <span class="text-muted fw-medium small">
            Selected: <span id="modalSelectedCount" class="fw-bold fs-5" style="color: #8b5cf6;">0</span> / 4
        </span>
        <button class="btn px-4 py-2 text-white fw-bold shadow-sm" id="modalConfirmBtn" disabled style="background-color: #8b5cf6; border: none; border-radius: 0.5rem; transition: background-color 0.2s;">
            Confirm Selection
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function updateModalBtnState() {
                const count = document.querySelectorAll('.criteria-checkbox:checked').length;
                const display = document.getElementById('modalSelectedCount');
                const btn = document.getElementById('modalConfirmBtn');
                if (display) display.innerText = count;
                if (btn) btn.disabled = (count !== 4);
            }

            // Expose for pages that want to call it before showing modal
            window.updateModalBtnState = updateModalBtnState;

            document.querySelectorAll('.criteria-checkbox').forEach(cb => {
                cb.addEventListener('change', () => {
                    const checked = document.querySelectorAll('.criteria-checkbox:checked').length;
                    if (checked > 4) {
                        cb.checked = false;
                        alert('You can only select up to 4 criteria.');
                        return;
                    }
                    updateModalBtnState();
                });
            });

            const confirmBtn = document.getElementById('modalConfirmBtn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', async () => {
                    const checkedEls = document.querySelectorAll('.criteria-checkbox:checked');
                    const selected = Array.from(checkedEls).map(e => e.value);
                    // POST selection to server to persist in session
                    try {
                        confirmBtn.disabled = true;
                        const tokenEl = document.querySelector('meta[name="csrf-token"]');
                        const token = tokenEl ? tokenEl.getAttribute('content') : '';
                        const res = await fetch("{{ route('recommendations.save_criteria') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ criteria: selected })
                        });

                        if (!res.ok) {
                            throw new Error('Failed to save selection');
                        }

                        const data = await res.json();
                        const evt = new CustomEvent('criteria:confirmed', { detail: { selected: data.selected || selected } });
                        document.dispatchEvent(evt);
                    } catch (err) {
                        alert('Could not save selection. Please try again.');
                    } finally {
                        confirmBtn.disabled = false;
                    }
                });
            }
        });
    </script>
</div>
