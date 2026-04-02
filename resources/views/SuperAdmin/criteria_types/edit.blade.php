@extends('layouts.super-admin')
@section('title','Edit Criteria Type')
@section('content')
<div class="container py-4" style="max-width: 880px;">
    <div class="card shadow-sm">
        <div class="card-header fw-bold">Edit Criteria Type</div>
        <div class="card-body">
            <form action="{{ route('super-admin.criteria_types.update',$type) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$type->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description',$type->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="ideal_preference" class="form-label">Ideal Preference</label>
                    <select id="ideal_preference" name="ideal_preference" class="form-select @error('ideal_preference') is-invalid @enderror" required>
                        <option value="max" {{ old('ideal_preference',$type->ideal_preference) == 'max' ? 'selected' : '' }}>Maximum (higher is better)</option>
                        <option value="min" {{ old('ideal_preference',$type->ideal_preference) == 'min' ? 'selected' : '' }}>Minimum (lower is better)</option>
                    </select>
                    @error('ideal_preference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.criteria_types.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
