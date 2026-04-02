@extends('layouts.super-admin')

@section('title', 'Super Admin Dashboard')

@section('content')
<style>
    .admin-hero {
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        color: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        text-align: center;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .quick-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        height: 100%;
    }
    .quick-card .btn {
        width: 100%;
        border-radius: 50px;
        padding: 12px 18px;
    }
    .top-actions .btn { border-radius: 50px; }
    .alert-success { border-radius: 12px; }
    .admin-meta { color: rgba(255,255,255,0.9); }
}</style>

<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="admin-hero d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1 class="mb-1"><i class="bi bi-speedometer2 me-2"></i>Super Admin Dashboard</h1>
            <p class="mb-0 super-admin-meta">Welcome back, {{ Auth::guard('super-admin')->user()->name ?? 'super administrator' }}</p>
        </div>
        <div class="top-actions d-flex gap-2">
            <a href="{{ route('super-admin.locations.create') }}" class="btn btn-light"><i class="bi bi-geo-alt-fill me-2"></i>Add Location</a>
            <a href="{{ route('super-admin.tourist_spots.create') }}" class="btn btn-light"><i class="bi bi-camera-fill me-2"></i>Add Tourist Spot</a>
            <a href="{{ route('super-admin.criteria.create') }}" class="btn btn-light"><i class="bi bi-sliders me-2"></i>Add Criteria</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-muted">Locations</div>
                <p class="stat-value">{{ $stats['locations'] ?? 0 }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-muted">Tourist Spots</div>
                <p class="stat-value">{{ $stats['spots'] ?? 0 }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-muted">Criteria</div>
                <p class="stat-value">{{ $stats['criteria'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="text-muted">Submissions</div>
                <p class="stat-value">{{ $stats['submissions'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="quick-card">
                <h5 class="mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Add Location</h5>
                <p class="text-muted">Create a new location (e.g., state or region).</p>
                <a href="{{ route('super-admin.locations.index') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Manage Location
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="quick-card">
                <h5 class="mb-3"><i class="bi bi-camera-fill me-2"></i>Add Tourist Spot</h5>
                <p class="text-muted">Add a tourist spot and link it to a location.</p>
                <a href="{{ route('super-admin.tourist_spots.index') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Manage Tourist Spot
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="quick-card">
                <h5 class="mb-3"><i class="bi bi-sliders me-2"></i>Add Criteria Type</h5>
                <p class="text-muted">Define a new evaluation criterion (with weight).</p>
                <a href="{{ route('super-admin.criteria_types.index') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Manage Criteria Types
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="quick-card">
                <h5 class="mb-3"><i class="bi bi-sliders me-2"></i>Add Criteria</h5>
                <p class="text-muted">Define a new evaluation criterion (with weight).</p>
                <a href="{{ route('super-admin.criteria.index') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Manage Criteria
                </a>
            </div>
        </div>
    </div>
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <div class="quick-card">
                <h5 class="mb-3"><i class="bi bi-inbox-fill me-2"></i>User Submissions</h5>
                <p class="text-muted">Review recommendation results submitted by users.</p>
                <a href="{{ route('super-admin.submissions.index') }}" class="btn btn-primary">
                    <i class="bi bi-eye me-1"></i> View Submissions
                    @if(($stats['submissions'] ?? 0) > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $stats['submissions'] }}</span>
                    @endif
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="quick-card">
                <h5 class="mb-3"><i class="bi bi-clipboard2-pulse me-2"></i>SUS Submissions</h5>
                <p class="text-muted">Review usability feedback and SUS scores from users.</p>
                <a href="{{ route('super-admin.sus_submissions.index') }}" class="btn btn-primary">
                    <i class="bi bi-eye me-1"></i> View SUS Feedback
                    @if(($stats['sus_submissions'] ?? 0) > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $stats['sus_submissions'] }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
