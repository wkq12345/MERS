@extends('layouts.super-admin')
@section('title', 'Manage Locations')
@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Locations</h1>
            <div>
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-file-earmark-arrow-up"></i> Import file
                </button>
                <a href="{{ route('super-admin.locations.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Location
                </a>
            </div>
        </div>
        @if ($locations->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($locations as $location)
                            <tr>
                                <td>{{ $location->name }}</td>
                                <td>
                                    @if ($location->image)
                                        <img src="{{ filter_var($location->image, FILTER_VALIDATE_URL) ? $location->image : \Illuminate\Support\Facades\Storage::url($location->image) }}" alt="{{ $location->name }}"
                                            class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('super-admin.locations.edit', $location) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('super-admin.locations.destroy', $location) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this location?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $locations->links() }}
        @else
            <div class="alert alert-info">No locations added yet.</div>
        @endif
    </div>

    <!-- Import File Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('import-data') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Locations File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="importFile" name="file"
                                accept=".csv,.xlsx,.xls" required>
                            <div class="form-text">Accepted formats: CSV, Excel (.xlsx, .xls)</div>
                                <select name="sheet">
                                    <option value="import location">Location</option>
                                    <option value="import criteria type">Criteria Type</option>
                                    <option value="import criteria">Criteria</option>
                                    <option value="import tourist spot">Tourist Spot</option>
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
