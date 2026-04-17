@extends('layouts.super-admin')
@section('title', 'Edit Location')
@section('content')
    <div class="container py-4" style="max-width: 720px;">
        <div class="card shadow-sm">
            <div class="card-header fw-bold">Edit Location</div>
            <div class="card-body">
                <form action="{{ route('super-admin.locations.update', $location) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $location->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="image_file" class="form-label">Upload New Image</label>
                        <input type="file" id="image_file" name="image_file"
                            class="form-control @error('image_file') is-invalid @enderror @error('image') is-invalid @enderror"
                            accept="image/*">
                        <div class="form-text">Leave this blank to keep the current image.</div>
                        @error('image_file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL</label>
                        <input type="url" id="image_url" name="image_url"
                            class="form-control @error('image_url') is-invalid @enderror"
                            value="{{ old('image_url', filter_var($location->image, FILTER_VALIDATE_URL) ? $location->image : '') }}"
                            placeholder="https://example.com/image.jpg">
                        <div class="form-text">Paste a URL here to replace the current image without uploading a file.</div>
                        @error('image_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('super-admin.locations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
