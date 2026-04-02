@extends('layouts.super-admin')

@section('title', 'Super Admin Profile')

@section('content')
<style>
    .admin-profile-header {background: linear-gradient(135deg,#f59e0b 0%, #ef4444 100%); border-radius:20px; padding:40px; color:#fff; position:relative; overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,.15);}
    .admin-profile-header::before {content:''; position:absolute; inset:0; background:radial-gradient(circle at 30% 20%, rgba(255,255,255,.25), transparent 60%);}
    .admin-badge {display:inline-block; background:#fff; color:#ef4444; padding:6px 14px; border-radius:50px; font-weight:700; font-size:.75rem; letter-spacing:.5px; text-transform:uppercase; margin-left:10px;}
    .admin-avatar {width:140px; height:140px; border-radius:50%; background:linear-gradient(135deg,#ef4444,#f59e0b); display:flex; align-items:center; justify-content:center; font-size:3.5rem; font-weight:800; color:#fff; box-shadow:0 8px 30px rgba(0,0,0,.25); border:4px solid rgba(255,255,255,.6); margin:0 auto 25px;}
    .admin-profile-name {font-size:2.3rem; font-weight:800; text-align:center; margin-bottom:8px;}
    .admin-profile-email {text-align:center; font-size:1.05rem; opacity:.9; margin-bottom:25px;}
    .admin-section {background:#fff; border-radius:18px; padding:32px; margin-bottom:28px; box-shadow:0 6px 28px rgba(0,0,0,.08);}
    .admin-section-title {font-weight:800; font-size:1.5rem; display:flex; align-items:center; gap:12px; margin-bottom:24px;}
    .admin-section-title i {color:#f59e0b; font-size:1.6rem;}
    .info-grid {display:grid; grid-template-columns:200px 1fr; row-gap:18px; column-gap:28px;}
    .info-label {font-weight:600; color:#64748b; display:flex; align-items:center; gap:10px;}
    .info-label i {color:#ef4444;}
    .info-value {font-weight:500; color:#1e293b;}
    .restricted-note {background:#fef3c7; color:#92400e; padding:14px 18px; border-radius:12px; font-size:.9rem; display:flex; align-items:center; gap:10px; margin-top:10px;}
    .stats-cards {display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:18px; margin-top:10px;}
    .stat-card {background:linear-gradient(135deg,#f59e0b,#ef4444); color:#fff; padding:20px 22px; border-radius:16px; position:relative; overflow:hidden; box-shadow:0 6px 24px rgba(0,0,0,.12);}
    .stat-card::before {content:''; position:absolute; inset:0; background:radial-gradient(circle at 70% 30%, rgba(255,255,255,.3), transparent 60%);}
    .stat-value {font-size:1.9rem; font-weight:800; line-height:1; margin-bottom:6px;}
    .stat-label {font-size:.75rem; font-weight:600; letter-spacing:.5px; text-transform:uppercase; opacity:.85;}
    @media (max-width:768px){.info-grid{grid-template-columns:1fr;} .admin-profile-name{font-size:1.9rem;} }
</style>

<div class="container py-5">
    <div class="super-admin-profile-header mb-4">
        @php($initial = strtoupper(substr($superAdmin->name ?? 'A',0,1)))
        <div class="super-admin-avatar">{{ $initial }}</div>
        <h1 class="super-admin-profile-name">{{ $superAdmin->name }} <span class="super-admin-badge"></span></h1>
        <div class="super-admin-profile-email"><i class="bi bi-envelope-fill me-2"></i>{{ $superAdmin->email }}</div>
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-value">0</div>
                <div class="stat-label">Managed Spots</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">0</div>
                <div class="stat-label">Criteria Defined</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">0</div>
                <div class="stat-label">Locations Added</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="super-admin-section">
                <h2 class="super-admin-section-title"><i class="bi bi-person-badge-fill"></i>Profile Details</h2>
                <div class="info-grid">
                    <div class="info-label"><i class="bi bi-person-fill"></i>Name</div>
                    <div class="info-value">{{ $superAdmin->name }}</div>
                    <div class="info-label"><i class="bi bi-envelope"></i>Email</div>
                    <div class="info-value">{{ $superAdmin->email }}</div>
                    <div class="info-label"><i class="bi bi-shield-lock"></i>Role</div>
                    <div class="info-value">Administrator</div>
                    <div class="info-label"><i class="bi bi-calendar"></i>Member Since</div>
                    <div class="info-value">{{ $superAdmin->created_at?->format('F d, Y') }}</div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('super-admin.profile.edit') }}" class="btn btn-warning text-dark" style="border-radius:50px; padding:10px 18px;">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profile
                    </a>
                    <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-secondary" style="border-radius:50px; padding:10px 18px;">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="admin-section">
                <h2 class="super-admin-section-title"><i class="bi bi-tools"></i>Quick Actions</h2>
                <div class="d-grid gap-3">
                    <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-warning" style="border-radius:50px; padding:12px;">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('super-admin.locations.create') }}" class="btn btn-outline-warning" style="border-radius:50px; padding:12px;">
                        <i class="bi bi-geo-alt-fill me-2"></i>Add Location
                    </a>
                    <a href="{{ route('super-admin.tourist_spots.create') }}" class="btn btn-outline-warning" style="border-radius:50px; padding:12px;">
                        <i class="bi bi-map-fill me-2"></i>Add Tourist Spot
                    </a>
                    <a href="{{ route('super-admin.criteria.create') }}" class="btn btn-outline-warning" style="border-radius:50px; padding:12px;">
                        <i class="bi bi-funnel-fill me-2"></i>Add Criteria
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
