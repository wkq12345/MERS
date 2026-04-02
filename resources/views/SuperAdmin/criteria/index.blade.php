@extends('layouts.super-admin')
@section('title','Manage Criteria')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Criteria</h1>
        <div>
           <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
              <i class="bi bi-file-earmark-arrow-up"></i> Import file
           </button>
            <a href="{{ route('super-admin.criteria.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Criteria
            </a>
            </div>
    </div>
    @if($criteria->count())
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Criteria Type</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($criteria as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->criteriaType->name }}</td>
                    <td class="text-end">
                        <a href="{{ route('super-admin.criteria.edit',$item) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('super-admin.criteria.destroy',$item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this criteria?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $criteria->links() }}
    @else
        <div class="alert alert-info">No criteria added yet.</div>
    @endif
</div>

    <!-- Import File Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('import-data') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Criteria File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="importFile" name="file"
                                accept=".csv,.xlsx,.xls" required>
                            <div class="form-text">Accepted formats: CSV, Excel (.xlsx, .xls)</div>
                                <select name="sheet" default="import criteria">
                                    <option value="import location">Location</option>
                                    <option value="import criteria type">Criteria Type</option>
                                    <option value="import criteria" selected>Criteria</option>
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
