<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SuperAdminProfileController extends Controller
{
    /** Display super admin profile */
    public function show()
    {
        $superAdmin = Auth::user();
        if (!$superAdmin || !$superAdmin->isSuperAdmin()) {
            return redirect()->route('super-admin.login')->with('error', 'Please log in as super admin.');
        }
        return view('SuperAdmin.profile.show', compact('superAdmin'));
    }

    /** Show edit form */
    public function edit()
    {
        $superAdmin = Auth::user();
        if (!$superAdmin || !$superAdmin->isSuperAdmin()) {
            return redirect()->route('super-admin.login')->with('error', 'Please log in as super admin.');
        }
        return view('SuperAdmin.profile.edit', compact('superAdmin'));
    }

    /** Update super admin basic details */
    public function update(\Illuminate\Http\Request $request)
    {
        $superAdmin = Auth::user();
        if (!$superAdmin || !$superAdmin->isSuperAdmin()) {
            return redirect()->route('super-admin.login')->with('error', 'Please log in as super admin.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $superAdmin->id],
        ]);

        $superAdmin->update($validated);

        return redirect()->route('super-admin.profile.show')->with('success', 'Super Admin profile updated successfully.');
    }

    /** Update super admin password */
    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $superAdmin = Auth::user();
        if (!$superAdmin || !$superAdmin->isSuperAdmin()) {
            return redirect()->route('super-admin.login')->with('error', 'Please log in as super admin.');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $superAdmin->update([
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('super-admin.profile.show')->with('success', 'Password updated successfully.');
    }
}
