<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserUpgradeController extends Controller
{
    /**
     * Show the upgrade to manager form.
     */
    public function showUpgradeForm()
    {
        $user = Auth::user();
        
        if ($user->isManager()) {
            return redirect()->route('manager.dashboard')->with('info', 'You are already a manager!');
        }

        return view('auth.upgrade-to-manager');
    }

    /**
     * Handle user upgrade to manager.
     */
    public function upgrade(Request $request)
    {
        $user = Auth::user();

        if ($user->isManager()) {
            return redirect()->route('manager.dashboard')->with('info', 'You are already a manager!');
        }

        $validator = Validator::make($request->all(), [
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

        $user->upgradeToManager([
            'business_name' => $request->business_name,
            'business_registration_number' => $request->business_registration_number,
            'cac_number' => $request->cac_number,
            'business_address' => $request->business_address,
            'business_phone' => $request->business_phone,
        ]);

        return redirect()->route('manager.dashboard')->with('success', 
            'Your account has been upgraded to manager! Your business verification is pending approval.'
        );
    }
} 