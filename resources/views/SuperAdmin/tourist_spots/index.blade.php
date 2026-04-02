@extends('layouts.super-admin')
@section('title','Manage Tourist Spots')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Tourist Spots</h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-arrow-up"></i> Import file
            </button>
            <a href="{{ route('super-admin.tourist_spots.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Tourist Spot
            </a>
        </div>
    </div>
    @if($spots->count())
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($spots as $spot)
                <tr>
                    <td>{{ $spot->name }}</td>
                    <td>{{ $spot->location?->name ?? '-' }}</td>
                    <td>
                        @if($spot->image)
                            @php
                                $src = preg_match('/^https?:\/\//', $spot->image) ? $spot->image : asset($spot->image);
                            @endphp
                            <img src="{{ $src }}" alt="{{ $spot->name }}" width="64" class="rounded shadow-sm">
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $spot->status ? 'bg-success' : 'bg-secondary' }}">
                            {{ $spot->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('super-admin.tourist_spots.edit', $spot) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('super-admin.tourist_spots.destroy', $spot) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this tourist spot?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $spots->links() }}
    @else
        <div class="alert alert-info">No tourist spots added yet.</div>
    @endif
</div>

<!-- Import File Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('import-data') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Tourist Spots File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="importFile" name="file"
                                accept=".csv,.xlsx,.xls" required>
                            <div class="form-text">Accepted formats: CSV, Excel (.xlsx, .xls)</div>
                                <select name="sheet" default="import tourist spot">
                                    <option value="import location">Location</option>
                                    <option value="import criteria type">Criteria Type</option>
                                    <option value="import criteria">Criteria</option>
                                    <option value="import tourist spot" selected>Tourist Spot</option>
                                    <option value="import rating">Rating</option>
                                </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
