@extends('layouts.super-admin')
@section('title','Edit Criteria')
@section('content')
<div class="container py-4" style="max-width: 880px;">
    <div class="card shadow-sm">
        <div class="card-header fw-bold">Edit Criteria</div>
        <div class="card-body">
            <form action="{{ route('super-admin.criteria.update',$criteria) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name',$criteria->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description',$criteria->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="criteria_type_id" class="form-label">Criteria Type</label>
                    <select id="criteria_type_id" name="criteria_type_id" class="form-select @error('criteria_type_id') is-invalid @enderror" required>
                        <option value="">Select Criteria Type</option>
                        @foreach($criteriaTypes as $criteriaType)
                            <option value="{{ $criteriaType->id }}" {{ (int)old('criteria_type_id',$criteria->criteria_type_id) === $criteriaType->id ? 'selected' : '' }}>
                                {{ $criteriaType->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('criteria_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('super-admin.criteria.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
