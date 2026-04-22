
@php
	$authUser = auth()->user();
	$guestKey = (string) session('recommendation_guest_key', '');
	$hasDemographic = false;

	if ($authUser) {
		$hasDemographic = $authUser->demographic()->exists();
	} elseif ($guestKey !== '') {
		$hasDemographic = \App\Models\UserDemographic::where('guest_key', $guestKey)->exists();
	}

	$needsDemographic = !$hasDemographic;
@endphp

@if ($needsDemographic)
	<style>
		.demographic-modal .modal-content {
			border: 0;
			border-radius: 1rem;
			overflow: hidden;
			box-shadow: 0 1.5rem 3rem rgba(15, 23, 42, 0.18);
		}

		.demographic-modal .modal-header {
			background: linear-gradient(135deg, #0d6efd, #14b8a6);
			color: #fff;
			border-bottom: 0;
		}

		.demographic-modal .form-label {
			font-weight: 600;
			color: #1f2937;
		}
	</style>

	<div class="modal fade demographic-modal" id="userDemographicModal" tabindex="-1" aria-labelledby="userDemographicModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<div>
						<h5 class="modal-title mb-1" id="userDemographicModalLabel">Complete your profile</h5>
						<small>Please fill in these details once before continuing.</small>
					</div>
				</div>
				<form action="{{ route('user.demographic.store') }}" method="POST">
					@csrf
					<div class="modal-body">
						<div class="mb-3">
							<label for="demographicAge" class="form-label">Age</label>
							<input type="number" name="age" id="demographicAge" class="form-control @error('age') is-invalid @enderror" min="1" max="120" value="{{ old('age') }}" required>
							@error('age')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="mb-3">
							<label for="demographicGender" class="form-label">Gender</label>
							<select name="gender" id="demographicGender" class="form-select @error('gender') is-invalid @enderror" required>
								<option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
								<option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
								<option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
							</select>
							@error('gender')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="mb-3">
							<label for="demographicIncome" class="form-label">Spending per trip (Approximate in RM)</label>
							<input type="number" name="income" id="demographicIncome" class="form-control @error('income') is-invalid @enderror" min="0" step="0.01" value="{{ old('income') }}" placeholder="0.00" required>
							@error('income')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary w-100">Save and continue</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				const modalElement = document.getElementById('userDemographicModal');

				if (modalElement && window.bootstrap) {
					const modal = new bootstrap.Modal(modalElement);
					modal.show();
				}
			});
		</script>
	@endpush
@endif
