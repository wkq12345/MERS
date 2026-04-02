@extends('layouts.super-admin')

@section('title', 'Add Location')

@section('content')
<div class="container py-4">
    <h1 class="mb-4"><i class="bi bi-geo-alt-fill me-2"></i>Add Location</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('super-admin.locations.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Location Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
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
