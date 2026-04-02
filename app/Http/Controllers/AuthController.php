<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /* =========================
       SHOW FORMS
    ========================== */

    public function showLoginForm()
    {
        return view('Auth.login');
    }

    public function showRegisterForm()
    {
        return view('Auth.register');
    }

    public function showSuperAdminLoginForm()
    {
        return view('Auth.login'); // reuse same login page
    }

    public function showAdminLoginForm()
    {
        return view('Auth.login'); // reuse same login page
    }

    /* =========================
       REGISTRATION
    ========================== */

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $userRole = Role::where('role_name', 'user')->firstOrFail();

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role()->associate($userRole); // sets role_id internally
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Registration successful!');
    }

    /* =========================
       LOGIN (SINGLE SOURCE)
    ========================== */

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // 🔐 ROLE-BASED REDIRECT
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    public function adminLogin(Request $request)
    {
        return $this->login($request);
    }

    public function superAdminLogin(Request $request)
    {
        return $this->login($request);
    }

    /* =========================
       LOGOUT
    ========================== */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully!');
    }

    public function adminLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Admin logged out successfully!');
    }

    public function superAdminLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super-admin.login')
            ->with('success', 'Super admin logged out successfully!');
    }
}
