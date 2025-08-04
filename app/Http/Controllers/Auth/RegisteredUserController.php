<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'default_address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'default_address' => $request->default_address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
        ]);

        // If address information is provided, create a default address
        if ($request->filled(['default_address', 'city', 'state'])) {
            $user->addresses()->create([
                'label' => 'Home',
                'address_line_1' => $request->default_address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => 'Nigeria',
                'phone_number' => $request->phone_number,
                'is_default' => true,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect to dashboard after successful registration
        return redirect()->route('dashboard')->with('success', 
            'Account created successfully! Welcome to Fastify.'
        );
    }
}
