<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerDashboardController extends Controller
{
    /**
     * Show the manager dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Manager privileges required.');
        }

        $data = [
            'user' => $user,
            'restaurants' => $user->restaurants,
            'pendingVerification' => $user->isManagerPending(),
            'approvedManager' => $user->isManagerApproved(),
            'rejectedManager' => $user->isManagerRejected(),
        ];

        return view('manager.dashboard', $data);
    }

    /**
     * Show manager verification status.
     */
    public function verificationStatus()
    {
        $user = Auth::user();

        if (!$user->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Manager privileges required.');
        }

        $data = [
            'user' => $user,
            'status' => $user->manager_verification_status,
            'notes' => $user->manager_verification_notes,
        ];

        return view('manager.verification-status', $data);
    }

    /**
     * Show create restaurant form.
     */
    public function createRestaurant()
    {
        $user = Auth::user();

        if (!$user->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Manager privileges required.');
        }

        if (!$user->isManagerApproved()) {
            return redirect()->route('manager.dashboard')->with('error', 
                'Your manager account is pending verification. You cannot create restaurants yet.'
            );
        }

        return view('manager.create-restaurant');
    }

    /**
     * Store new restaurant.
     */
    public function storeRestaurant(Request $request)
    {
        $user = Auth::user();

        if (!$user->isManager()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Manager privileges required.');
        }

        if (!$user->isManagerApproved()) {
            return redirect()->route('manager.dashboard')->with('error', 
                'Your manager account is pending verification. You cannot create restaurants yet.'
            );
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'cuisine_type' => 'required|string|max:100',
            'delivery_radius' => 'required|numeric|min:1|max:50',
            'minimum_order' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
        ]);

        $restaurant = Restaurant::create([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'cuisine_type' => $request->cuisine_type,
            'delivery_radius' => $request->delivery_radius,
            'minimum_order' => $request->minimum_order,
            'delivery_fee' => $request->delivery_fee,
            'owner_id' => $user->id,
            'is_open' => true,
            'open_time' => '08:00:00',
            'close_time' => '22:00:00',
        ]);

        return redirect()->route('manager.dashboard')->with('success', 
            "Restaurant '{$restaurant->name}' created successfully!"
        );
    }
} 