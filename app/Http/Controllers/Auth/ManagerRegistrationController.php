<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class ManagerRegistrationController extends Controller
{
    /**
     * Show the manager registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.manager-register');
    }

    /**
     * Handle manager registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['required', 'string', 'max:20'],
            'business_name' => ['required', 'string', 'max:255'],
            'business_registration_number' => ['required', 'string', 'max:100'],
            'cac_number' => ['required', 'string', 'max:100'],
            'business_address' => ['required', 'string', 'max:500'],
            'business_phone' => ['required', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'role' => 'manager',
            'business_name' => $request->business_name,
            'business_registration_number' => $request->business_registration_number,
            'cac_number' => $request->cac_number,
            'business_address' => $request->business_address,
            'business_phone' => $request->business_phone,
            'manager_verification_status' => 'pending',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('manager.dashboard')->with('success', 
            'Manager account created successfully! Your business verification is pending approval.'
        );
    }
} 