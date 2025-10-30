<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    public function index()
    {
        return view('auth.admin-login');
    }
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.signin');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        if ($user && $user->role === 'admin') {
            $fallback = route('any', 'index');
        } elseif ($user && $user->role === 'donor') {
            $fallback = route('third', ['user', 'donations', 'index']);
        } else {
            $fallback = route('root');
        }
        return redirect()->intended($fallback)->with('success', 'You have successfully logged in.');
    }
    public function admin_store(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // Get admin user (assuming only 1 admin, or fetch the first one)
        $admin = \App\Models\User::where('role', 'admin')->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            // Log in this admin directly
            Auth::login($admin);

            $request->session()->regenerate();

            return redirect('/admin/dashboard')
                ->with('success', 'Welcome back, Admin!');
        }

        return back()->withErrors([
            'password' => 'Invalid admin credentials.',
        ]);
    }
    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/auth/admin-signin')->with('success', 'You have been logged out.');
    }
}
