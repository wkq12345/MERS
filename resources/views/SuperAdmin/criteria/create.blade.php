@extends('layouts.super-admin')

@section('title', 'Add Criteria')

@section('content')
<div class="container py-4">
    <h1 class="mb-4"><i class="bi bi-sliders me-2"></i>Add Criteria</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('super-admin.criteria.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Criteria Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description (optional)</label>
                    <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}">
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="criteria_type_id" class="form-label">Criteria Type</label>
                    <select id="criteria_type_id" name="criteria_type_id" class="form-select @error('criteria_type_id') is-invalid @enderror" required>
                        <option value="">Select Criteria Type</option>
                        @foreach($criteriaTypes as $criteriaType)
                            <option value="{{ $criteriaType->id }}" {{ old('criteria_type_id') == $criteriaType->id ? 'selected' : '' }}>
                                {{ $criteriaType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('criteria_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Save</button>
                    <a href="{{ route('super-admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
