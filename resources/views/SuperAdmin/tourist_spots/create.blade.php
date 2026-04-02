@extends('layouts.super-admin')

@section('title', 'Add Tourist Spot')

@section('content')
<div class="container py-4">
    <h1 class="mb-4"><i class="bi bi-camera-fill me-2"></i>Add Tourist Spot</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('super-admin.tourist_spots.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Spot Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                           name="name" value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                              name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="location_id" class="form-label">Location</label>
                    <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id">
                        <option value="">-- Select Location --</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1"
                           {{ old('status', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active</label>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="image" class="form-label">Upload Image (jpg, png, webp)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                               name="image" accept="image/*">
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="image_url" class="form-label">Or Image URL</label>
                        <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url"
                               name="image_url" value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg">
                        @error('image_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <small class="text-muted">If both are provided, the uploaded file is used.</small>

                <div class="mt-4">
                    <h5 class="mb-3"><i class="bi bi-list-check me-2"></i>Criteria Ratings</h5>
                    <p class="text-muted">Rate each criterion (1 = lowest, 9 = highest).</p>

                    @foreach ($criteriaTypes as $type)
                        @php $isCost = $type->ideal_preference === 'min'; @endphp
                        <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $type->name }}</strong>
                                    @if ($type->description)
                                        <small class="text-muted d-block">{{ $type->description }}</small>
                                    @endif
                                </div>
                                <span class="badge {{ $isCost ? 'bg-danger' : 'bg-success' }}">
                                    {{ $isCost ? 'Lower is better' : 'Higher is better' }}
                                </span>
                            </div>
                            <div class="card-body">
                                @if ($type->criteria->count())
                                    <div class="row g-3">
                                        @foreach ($type->criteria as $criterion)
                                            <div class="col-md-6">
                                                <label class="form-label" for="rating_{{ $criterion->id }}">
                                                    {{ $criterion->name }}
                                                    @if ($criterion->description)
                                                        <br><small class="text-muted">{{ $criterion->description }}</small>
                                                    @endif
                                                </label>
                                                <select class="form-select @error('ratings.' . $criterion->id) is-invalid @enderror"
                                                        id="rating_{{ $criterion->id }}"
                                                        name="ratings[{{ $criterion->id }}]">
                                                    <option value="">-- Select Rating (1-9) --</option>
                                                    @for ($i = 1; $i <= 9; $i++)
                                                        <option value="{{ $i }}" {{ old('ratings.' . $criterion->id) == $i ? 'selected' : '' }}>
                                                            {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                @error('ratings.' . $criterion->id)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No criteria available for this type.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                    <a href="{{ route('super-admin.tourist_spots.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
