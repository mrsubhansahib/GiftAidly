<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\HasValidMx;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.signup');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email', new HasValidMx],
           'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()->symbols()],
        ], [
            'email.email'  => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password' => 'The password must be at least 8 characters long and contain letters, numbers, and symbols.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // dd($user);
        event(new Registered($user));

        return redirect()->back()->with('success', 'Please verify your email to continue!');
    }
}
