@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<style>
    .edit-profile-page {
        background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
        min-height: 100vh;
        padding: 80px 0 60px;
    }

    .edit-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 10px;
    }

    .page-header p {
        color: #64748b;
        font-size: 1.1rem;
    }

    .edit-section {
        background: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        padding-bottom: 15px;
        border-bottom: 3px solid #f1f5f9;
    }

    .section-title i {
        font-size: 1.8rem;
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-label {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label i {
        color: #4f46e5;
    }

    .form-control,
    .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    .form-text {
        color: #64748b;
        font-size: 0.9rem;
        margin-top: 8px;
    }

    .btn-save {
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
        color: white;
        border: none;
        padding: 15px 50px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
    }

    .btn-cancel {
        background: white;
        color: #64748b;
        border: 2px solid #e2e8f0;
        padding: 15px 50px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        border-color: #4f46e5;
        color: #4f46e5;
    }

    .btn-danger-gradient {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        padding: 15px 50px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        transition: all 0.3s ease;
    }

    .btn-danger-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }

    .alert-custom {
        border-radius: 15px;
        padding: 20px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .invalid-feedback {
        display: block;
        color: #ef4444;
        font-weight: 600;
        margin-top: 8px;
    }

    .is-invalid {
        border-color: #ef4444 !important;
    }

    .danger-zone {
        border: 2px solid #fecaca;
        background: #fef2f2;
        padding: 30px;
        border-radius: 15px;
    }

    .danger-zone h3 {
        color: #dc2626;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .danger-zone p {
        color: #991b1b;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2rem;
        }

        .edit-section {
            padding: 25px;
        }

        .btn-save,
        .btn-cancel,
        .btn-danger-gradient {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>

<div class="edit-profile-page">
    <div class="container edit-container">
        <div class="page-header">
            <h1><i class="bi bi-pencil-square me-3"></i>Edit Profile</h1>
            <p>Update your personal information and preferences</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-custom">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Oops! There were some errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Personal Information Form -->
        <div class="edit-section">
            <h2 class="section-title">
                <i class="bi bi-person-fill"></i>
                Personal Information
            </h2>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label">
                            <i class="bi bi-person-badge"></i>
                            Full Name
                        </label>
                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope"></i>
                            Email Address
                        </label>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="phone" class="form-label">
                            <i class="bi bi-telephone"></i>
                            Phone Number
                        </label>
                        <input
                            type="tel"
                            class="form-control @error('phone') is-invalid @enderror"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone ?? '') }}"
                            placeholder="+60 12-345 6789"
                        >
                        <small class="form-text">Optional - Your contact number</small>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="location" class="form-label">
                            <i class="bi bi-geo-alt"></i>
                            Location
                        </label>
                        <input
                            type="text"
                            class="form-control @error('location') is-invalid @enderror"
                            id="location"
                            name="location"
                            value="{{ old('location', $user->location ?? '') }}"
                            placeholder="e.g., Kuala Lumpur, Malaysia"
                        >
                        <small class="form-text">Optional - Your city or state</small>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="bio" class="form-label">
                        <i class="bi bi-card-text"></i>
                        Bio / About Me
                    </label>
                    <textarea
                        class="form-control @error('bio') is-invalid @enderror"
                        id="bio"
                        name="bio"
                        rows="4"
                        maxlength="500"
                        placeholder="Tell us about yourself, your travel interests, favorite destinations..."
                    >{{ old('bio', $user->bio ?? '') }}</textarea>
                    <small class="form-text">Optional - Maximum 500 characters</small>
                    @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-3 flex-wrap">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Save Changes
                    </button>
                    <a href="{{ route('profile.show') }}" class="btn-cancel">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Password Update Form -->
        <div class="edit-section">
            <h2 class="section-title">
                <i class="bi bi-shield-lock-fill"></i>
                Change Password
            </h2>

            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="current_password" class="form-label">
                        <i class="bi bi-key"></i>
                        Current Password
                    </label>
                    <input
                        type="password"
                        class="form-control @error('current_password') is-invalid @enderror"
                        id="current_password"
                        name="current_password"
                        required
                    >
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-key-fill"></i>
                        New Password
                    </label>
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        required
                    >
                    <small class="form-text">Minimum 8 characters</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        <i class="bi bi-check2-square"></i>
                        Confirm New Password
                    </label>
                    <input
                        type="password"
                        class="form-control"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                    >
                </div>

                <button type="submit" class="btn-save">
                    <i class="bi bi-shield-check me-2"></i>
                    Update Password
                </button>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="edit-section">
            <h2 class="section-title">
                <i class="bi bi-exclamation-triangle-fill" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Danger Zone
            </h2>

            <div class="danger-zone">
                <h3><i class="bi bi-trash-fill me-2"></i>Delete Account</h3>
                <p>Once you delete your account, there is no going back. All your data will be permanently deleted. This action cannot be undone.</p>

                <button type="button" class="btn-danger-gradient" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    Delete My Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Confirm Account Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <p style="color: #1e293b; font-size: 1.1rem; margin-bottom: 20px;">
                    Are you absolutely sure you want to delete your account? This action is <strong>permanent</strong> and cannot be undone.
                </p>

                <form action="{{ route('profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label" style="color: #1e293b; font-weight: 700;">
                            Enter your password to confirm:
                        </label>
                        <input
                            type="password"
                            class="form-control"
                            id="confirm_password"
                            name="password"
                            required
                            style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 12px;"
                        >
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-danger" style="flex: 1; border-radius: 50px; padding: 12px; font-weight: 700;">
                            <i class="bi bi-trash-fill me-2"></i>
                            Yes, Delete My Account
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="flex: 1; border-radius: 50px; padding: 12px; font-weight: 700;">
                            <i class="bi bi-x-circle me-2"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
@endsection
