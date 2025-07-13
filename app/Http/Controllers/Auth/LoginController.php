<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm(Request $request)
    {
        // Check if user was redirected due to session expiration
        if ($request->has('expired')) {
            session()->flash('message', 'Your session has expired. Please log in again.');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($e->getMessage() === 'The given data was invalid.') {
                return back()->withErrors([
                    'email' => 'Session expired. Please try again.',
                ])->onlyInput('email');
            }
            throw $e;
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            

            $user = Auth::user();

            // Redirect based on user role
            switch ($user->role) {
                case User::ROLE_ADMIN:
                    return redirect()->intended('/admin/dashboard');
                case User::ROLE_EMPLOYEE:
                    return redirect()->intended('/employee/dashboard');
                case User::ROLE_CLIENT:
                    return redirect()->intended('/client/dashboard');
                default:
                    return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('message', 'You have been logged out successfully.');
    }
}
