@extends('layouts.super-admin')

@section('title', 'Add Location')

@section('content')
<div class="container py-4">
    <h1 class="mb-4"><i class="bi bi-geo-alt-fill me-2"></i>Add Location</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('super-admin.locations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Location Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="image_file" class="form-label">Upload Image</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror @error('image_file') is-invalid @enderror" id="image_file" name="image_file" accept="image/*">
                    <div class="form-text">Upload a JPG, PNG, GIF, or WebP file, or paste an image URL below.</div>
                    @error('image_file')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="image_url" class="form-label">Image URL</label>
                    <input type="url" class="form-control @error('image_url') is-invalid @enderror" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg">
                    @error('image_url')
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
