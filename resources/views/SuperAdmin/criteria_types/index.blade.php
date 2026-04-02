@extends('layouts.super-admin')
@section('title','Manage Criteria Types')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Criteria Types</h1>
            <div>
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-file-earmark-arrow-up"></i> Import file
                </button>
                <a href="{{ route('super-admin.criteria_types.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Criteria Type
                </a>
            </div>
    </div>
    @if($types->count())
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Criteria Count</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($types as $type)
                <tr>
                    <td>{{ $type->name }}</td>
                    <td>{{ $type->description ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $type->criteria_count }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('super-admin.criteria_types.edit',$type) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('super-admin.criteria_types.destroy',$type) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this criteria type?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $types->links() }}
    @else
        <div class="alert alert-info">No criteria types added yet.</div>
    @endif
</div>

<!-- Import File Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('import-data') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Criteria Types File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="importFile" name="file"
                                accept=".csv,.xlsx,.xls" required>
                            <div class="form-text">Accepted formats: CSV, Excel (.xlsx, .xls)</div>
                                <select name="sheet" default="import criteria type">
                                    <option value="import location">Location</option>
                                    <option value="import criteria type" selected>Criteria Type</option>
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
