<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password; // Import Password rule

class CustomRegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.custom-register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'], // Use imported Password rule
        ]);

        if ($validator->fails()) {
            return redirect()->route('custom.register')
                        ->withErrors($validator)
                        ->withInput();
        }

        // Find the 'Client' role
        // Assuming Role model uses Spatie's permission package or similar name field
        $clientRole = \App\Models\Role::where('name', 'Client')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $clientRole ? $clientRole->id : null, // Assign role_id if Client role found
            'created_by' => null, // For self-registration, created_by is null
            // 'email_verified_at' => now(), // Optionally, verify email straight away
        ]);

        Auth::login($user);

        return redirect()->route('home'); // Redirect to home page after successful registration
    }
}
