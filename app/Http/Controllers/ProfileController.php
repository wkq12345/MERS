<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the user profile page
     */
    public function show(Request $request)
    {
        // If admin logged in, redirect to admin profile
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.profile.show');
        }
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to view your profile.');
        }
        return view('profile.show', compact('user'));
    }

    /**
     * Show the edit profile form
     */
    public function edit(Request $request)
    {
        // Only allow normal users to edit; admins have a separate management interface.
        if (Auth::guard('admin')->check()) {
            return redirect()->route('profile.show')->with('warning', 'Admin profile is read-only.');
        }
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to edit your profile.');
        }
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile information
     */
    public function update(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('profile.show')->with('warning', 'Admin profile cannot be edited here.');
        }
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('profile.show')->with('warning', 'Admin password cannot be changed here.');
        }
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password updated successfully!');
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('profile.show')->with('warning', 'Admin accounts cannot be deleted here.');
        }
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
