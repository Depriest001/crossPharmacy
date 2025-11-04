<?php

namespace App\Http\Controllers\AuthController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $staff = Staff::where('email', $request->email)->first();

        if (!$staff) {
            return back()->withErrors(['email' => 'No staff found with that email.']);
        }

        if (!Hash::check($request->password, $staff->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        if ($staff->status !== 'active') {
            return back()->withErrors(['email' => 'Account inactive.']);
        }

        // 🔥 The correct way to log in manually
        Auth::guard('staff')->login($staff);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }
}
