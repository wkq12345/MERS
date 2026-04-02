@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<style>
    .profile-page {
        background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
        min-height: 100vh;
        padding: 80px 0 60px;
    }

    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .profile-header {
        background: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 150px;
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        z-index: 0;
    }

    .profile-content {
        position: relative;
        z-index: 1;
    }

    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: white;
        font-weight: 800;
        margin: 0 auto 20px;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .profile-name {
        font-size: 2.5rem;
        font-weight: 800;
        color: white;
        text-align: center;
        margin-bottom: 10px;
    }

    .profile-email {
        text-align: center;
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    .profile-stats {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .stat-item {
        text-align: center;
        background: rgba(255, 255, 255, 0.95);
        padding: 20px 30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 600;
        margin-top: 5px;
    }

    .info-section {
        background: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .section-title i {
        font-size: 2rem;
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .info-row {
        display: flex;
        padding: 20px 0;
        border-bottom: 2px solid #f1f5f9;
        align-items: center;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        flex: 0 0 200px;
        font-weight: 700;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-label i {
        color: #4f46e5;
        font-size: 1.2rem;
    }

    .info-value {
        flex: 1;
        color: #1e293b;
        font-size: 1.1rem;
    }

    .info-value.empty {
        color: #cbd5e1;
        font-style: italic;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .btn-primary-gradient {
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-primary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        color: white;
    }

    .btn-outline-gradient {
        background: white;
        color: #4f46e5;
        border: 2px solid #4f46e5;
        padding: 15px 40px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-outline-gradient:hover {
        background: #4f46e5;
        color: white;
        transform: translateY(-2px);
    }

    .alert-custom {
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .activity-card {
        background: #f8fafc;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid #4f46e5;
        transition: all 0.3s ease;
    }

    .activity-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .activity-date {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .activity-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .profile-name {
            font-size: 1.8rem;
        }

        .profile-stats {
            gap: 20px;
        }

        .info-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .info-label {
            margin-bottom: 10px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-primary-gradient,
        .btn-outline-gradient {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="profile-page">
    <div class="container profile-container">
        @if(session('success'))
        <div class="alert alert-success alert-custom">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
        @endif

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-content">
                @php
                    // Gracefully handle cases where $user may be null (should be prevented by controller)
                    $displayName = $user->name ?? 'Guest';
                    $initial = strtoupper(substr($displayName, 0, 1));
                @endphp
                <div class="profile-avatar">
                    {{ $initial }}
                </div>
                <h1 class="profile-name">{{ $displayName }}</h1>
                <p class="profile-email">
                    <i class="bi bi-envelope-fill me-2"></i>{{ $user->email }}
                </p>

                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Spots Visited</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Reviews</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Favorites</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Personal Information -->
            <div class="col-lg-8 mb-4">
                <div class="info-section">
                    <h2 class="section-title">
                        <i class="bi bi-person-fill"></i>
                        Personal Information
                    </h2>

                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-person-badge"></i>
                            Full Name
                        </div>
                        <div class="info-value">{{ $displayName }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-envelope"></i>
                            Email Address
                        </div>
                        <div class="info-value">{{ $user->email ?? 'Not provided' }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-telephone"></i>
                            Phone Number
                        </div>
                        <div class="info-value {{ !isset($user->phone) ? 'empty' : '' }}">
                            {{ $user->phone ?? 'Not provided' }}
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-geo-alt"></i>
                            Location
                        </div>
                        <div class="info-value {{ !isset($user->location) ? 'empty' : '' }}">
                            {{ $user->location ?? 'Not provided' }}
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-calendar"></i>
                            Member Since
                        </div>
                        <div class="info-value">{{ $user->created_at->format('F d, Y') }}</div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('profile.edit') }}" class="btn-primary-gradient">
                            <i class="bi bi-pencil-fill"></i>
                            Edit Profile
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn-outline-gradient">
                            <i class="bi bi-arrow-left"></i>
                            Back to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Bio Section -->
                @if(isset($user->bio))
                <div class="info-section">
                    <h2 class="section-title">
                        <i class="bi bi-card-text"></i>
                        About Me
                    </h2>
                    <p style="color: #64748b; line-height: 1.8; font-size: 1.1rem;">
                        {{ $user->bio }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Recent Activity -->
                <div class="info-section">
                    <h2 class="section-title">
                        <i class="bi bi-clock-history"></i>
                        Recent Activity
                    </h2>

                    <div class="activity-card">
                        <div class="activity-date">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $user->created_at->format('M d, Y') }}
                        </div>
                        <div class="activity-title">
                            <i class="bi bi-person-plus-fill me-2" style="color: #4f46e5;"></i>
                            Joined MERS
                        </div>
                    </div>

                    @if($user->updated_at != $user->created_at)
                    <div class="activity-card">
                        <div class="activity-date">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $user->updated_at->format('M d, Y') }}
                        </div>
                        <div class="activity-title">
                            <i class="bi bi-pencil-square me-2" style="color: #10b981;"></i>
                            Updated Profile
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="info-section">
                    <h2 class="section-title">
                        <i class="bi bi-lightning-fill"></i>
                        Quick Actions
                    </h2>

                    <div class="d-grid gap-3">
                        <a href="{{ route('recommendations.preferences') }}" class="btn btn-outline-primary" style="border-radius: 50px; padding: 12px;">
                            <i class="bi bi-stars me-2"></i>
                            Get Recommendations
                        </a>
                        <a href="#" class="btn btn-outline-primary" style="border-radius: 50px; padding: 12px;">
                            <i class="bi bi-bookmark-fill me-2"></i>
                            My Favorites
                        </a>
                        <a href="#" class="btn btn-outline-primary" style="border-radius: 50px; padding: 12px;">
                            <i class="bi bi-geo-alt-fill me-2"></i>
                            Browse Spots
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
