@extends('layouts.super-admin')
@section('title','Edit Location')
@section('content')
<div class="container py-4" style="max-width: 720px;">
    <div class="card shadow-sm">
        <div class="card-header fw-bold">Edit Location</div>
        <div class="card-body">
            <form action="{{ route('super-admin.locations.update',$location) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name',$location->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
