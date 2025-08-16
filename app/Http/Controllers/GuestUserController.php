<?php

namespace App\Http\Controllers;

use App\Models\GuestUser;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GuestOrderConfirmation;

class GuestUserController extends Controller
{
    /**
     * Show email collection modal after order success
     */
    public function showEmailCollection(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Check if order already has a guest user
        if ($order->guest_user_id) {
            return redirect()->route('guest.orders.show', $orderId);
        }
        
        return view('guest.email-collection', compact('order'));
    }

    /**
     * Collect email and create guest account
     */
    public function collectEmail(Request $request, $orderId)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $order = Order::findOrFail($orderId);
        
        try {
            // Create or find guest user
            $guestUser = GuestUser::findOrCreateByEmail(
                $request->email,
                $request->name,
                $request->phone
            );

            // Generate session token for QR code
            $sessionToken = $guestUser->generateSessionToken();

            // Link order to guest user
            $order->update(['guest_user_id' => $guestUser->id]);

            // Send email confirmation
            $this->sendOrderConfirmation($guestUser, $order);

            // Store guest user in session for immediate access
            session(['guest_user_id' => $guestUser->id]);

            Log::info('Guest account created successfully', [
                'guest_user_id' => $guestUser->id,
                'order_id' => $order->id,
                'email' => $request->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully! Check your email for order details.',
                'redirect_url' => route('guest.orders.show', $orderId),
                'qr_code_data' => $guestUser->getQrCodeData(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create guest account', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create account. Please try again.',
            ], 500);
        }
    }

    /**
     * Show guest order details
     */
    public function showOrder($orderId)
    {
        $order = Order::with(['items.menuItem', 'restaurant'])->findOrFail($orderId);
        
        // Check if user has access to this order
        $guestUserId = session('guest_user_id');
        
        if (!$guestUserId || $order->guest_user_id != $guestUserId) {
            return redirect()->route('guest.login', ['order_id' => $orderId]);
        }

        return view('guest.order-details', compact('order'));
    }

    /**
     * Show guest login page
     */
    public function showLogin(Request $request)
    {
        $orderId = $request->get('order_id');
        return view('guest.login', compact('orderId'));
    }

    /**
     * Handle guest login via email
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $guestUser = GuestUser::where('email', $request->email)->first();

        if (!$guestUser) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        // Generate and send magic link
        $magicLink = $guestUser->generateMagicLink();
        
        // Send magic link email
        Mail::to($guestUser->email)->send(new \App\Mail\GuestMagicLink($guestUser, $magicLink));

        return back()->with('success', 'Login link sent to your email! Check your inbox.');
    }

    /**
     * Handle magic link login
     */
    public function magicLogin(Request $request, $token)
    {
        $guestUser = GuestUser::verifyMagicLink($token);

        if (!$guestUser) {
            return redirect()->route('home')->with('error', 'Invalid or expired login link.');
        }

        // Log in guest user
        session(['guest_user_id' => $guestUser->id]);

        // Generate new session token for QR code
        $guestUser->generateSessionToken();

        return redirect()->route('guest.dashboard')->with('success', 'Welcome back!');
    }

    /**
     * Show guest dashboard with order history
     */
    public function dashboard()
    {
        $guestUserId = session('guest_user_id');
        
        if (!$guestUserId) {
            return redirect()->route('guest.login');
        }

        $guestUser = GuestUser::with(['orders' => function($query) {
            $query->latest();
        }])->findOrFail($guestUserId);

        return view('guest.dashboard', compact('guestUser'));
    }

    /**
     * Handle QR code session authentication
     */
    public function qrLogin(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $guestUser = GuestUser::where('session_token', $request->token)
                             ->where('session_expires_at', '>', now())
                             ->first();

        if (!$guestUser) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired QR code.',
            ], 401);
        }

        // Log in guest user
        session(['guest_user_id' => $guestUser->id]);

        return response()->json([
            'success' => true,
            'message' => 'Login successful!',
            'redirect_url' => route('guest.dashboard'),
        ]);
    }

    /**
     * Logout guest user
     */
    public function logout()
    {
        $guestUserId = session('guest_user_id');
        
        if ($guestUserId) {
            $guestUser = GuestUser::find($guestUserId);
            if ($guestUser) {
                $guestUser->invalidateSession();
            }
        }

        session()->forget('guest_user_id');

        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }

    /**
     * Send order confirmation email
     */
    private function sendOrderConfirmation($guestUser, $order)
    {
        try {
            Mail::to($guestUser->email)->send(new GuestOrderConfirmation($guestUser, $order));
            
            Log::info('Order confirmation email sent', [
                'guest_user_id' => $guestUser->id,
                'order_id' => $order->id,
                'email' => $guestUser->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email', [
                'error' => $e->getMessage(),
                'guest_user_id' => $guestUser->id,
                'order_id' => $order->id
            ]);
        }
    }
}
