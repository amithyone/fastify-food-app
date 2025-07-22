<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\UserReward;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getWalletOrCreate();
        
        $transactions = $wallet->transactions()
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $rewards = $user->rewards()
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('wallet.index', compact('wallet', 'transactions', 'rewards'));
    }

    public function transactions()
    {
        $user = Auth::user();
        $wallet = $user->getWalletOrCreate();
        
        $transactions = $wallet->transactions()
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('wallet.transactions', compact('wallet', 'transactions'));
    }

    public function rewards()
    {
        $user = Auth::user();
        $wallet = $user->getWalletOrCreate();
        
        $rewards = $user->rewards()
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('wallet.rewards', compact('wallet', 'rewards'));
    }

    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_method' => 'required|in:transfer,card'
        ]);

        $user = Auth::user();
        $wallet = $user->getWalletOrCreate();
        
        // In a real app, you'd integrate with a payment gateway here
        // For now, we'll simulate a successful payment
        
        try {
            $wallet->credit(
                $request->amount,
                0,
                "Wallet top-up via {$request->payment_method}",
                null,
                ['payment_method' => $request->payment_method]
            );

            return response()->json([
                'success' => true,
                'message' => 'Funds added successfully!',
                'new_balance' => $wallet->fresh()->formatted_balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add funds. Please try again.'
            ], 500);
        }
    }

    public function getWalletInfo()
    {
        if (!Auth::check()) {
            return response()->json([
                'balance' => 0,
                'formatted_balance' => '₦0',
                'points' => 0,
                'points_display' => '0 points',
                'authenticated' => false
            ]);
        }

        $user = Auth::user();
        $wallet = $user->getWalletOrCreate();

        return response()->json([
            'balance' => $wallet->balance,
            'formatted_balance' => $wallet->formatted_balance,
            'points' => $wallet->points,
            'points_display' => $wallet->points . ' points',
            'authenticated' => true
        ]);
    }

    public function info()
    {
        return $this->getWalletInfo();
    }
}
