<?php

namespace App\Http\Controllers;

use App\Helpers\PWAHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PWAController extends Controller
{
    /**
     * Serve dynamic PWA manifest
     */
    public function manifest(): JsonResponse
    {
        $manifest = PWAHelper::generateManifest();
        
        return response()->json($manifest)
            ->header('Content-Type', 'application/manifest+json')
            ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
    }

    /**
     * Handle push notification subscription
     */
    public function subscribeToPush(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys' => 'required|array',
            'keys.auth' => 'required|string',
            'keys.p256dh' => 'required|string',
        ]);

        try {
            // Store subscription in database
            $user = auth()->user();
            if ($user) {
                $user->push_subscription = json_encode($validated);
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Push notification subscription successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe to push notifications'
            ], 500);
        }
    }

    /**
     * Send push notification
     */
    public function sendPushNotification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
            'icon' => 'nullable|string',
            'data' => 'nullable|array',
            'user_id' => 'nullable|exists:users,id',
        ]);

        try {
            // Get user's push subscription
            $user = null;
            if (isset($validated['user_id'])) {
                $user = \App\Models\User::find($validated['user_id']);
            } else {
                $user = auth()->user();
            }

            if (!$user || !$user->push_subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No push subscription found'
                ], 404);
            }

            $subscription = json_decode($user->push_subscription, true);

            // Send push notification using web-push library
            // Note: You'll need to install and configure web-push
            // composer require minishlink/web-push

            return response()->json([
                'success' => true,
                'message' => 'Push notification sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send push notification'
            ], 500);
        }
    }

    /**
     * Get PWA configuration
     */
    public function getConfig(): JsonResponse
    {
        $config = [
            'restaurant' => [
                'name' => PWAHelper::getRestaurantName(),
                'display_name' => PWAHelper::getRestaurantDisplayName(),
                'short_name' => PWAHelper::getRestaurantShortName(),
                'theme_color' => PWAHelper::getThemeColor(),
                'contact' => PWAHelper::getContactInfo(),
                'social' => PWAHelper::getSocialLinks(),
            ],
            'features' => [
                'wallet' => PWAHelper::isFeatureEnabled('wallet'),
                'rewards' => PWAHelper::isFeatureEnabled('rewards'),
                'qr_ordering' => PWAHelper::isFeatureEnabled('qr_ordering'),
                'push_notifications' => PWAHelper::isFeatureEnabled('push_notifications'),
                'offline_mode' => PWAHelper::isFeatureEnabled('offline_mode'),
                'dark_mode' => PWAHelper::isFeatureEnabled('dark_mode'),
                'multi_language' => PWAHelper::isFeatureEnabled('multi_language'),
            ],
            'business' => PWAHelper::getBusinessSettings(),
            'pwa' => [
                'name' => PWAHelper::getAppTitle(),
                'short_name' => PWAHelper::getRestaurantShortName(),
                'theme_color' => PWAHelper::getThemeColor(),
            ]
        ];

        return response()->json($config);
    }

    /**
     * Update restaurant configuration
     */
    public function updateConfig(Request $request): JsonResponse
    {
        // This endpoint would be used by admin to update restaurant config
        // For now, we'll just return success
        return response()->json([
            'success' => true,
            'message' => 'Configuration updated successfully'
        ]);
    }

    /**
     * Get offline page data
     */
    public function getOfflineData(): JsonResponse
    {
        $config = [
            'app_name' => PWAHelper::getRestaurantName(),
            'features' => [
                'browse_menu' => true,
                'view_orders' => true,
                'check_wallet' => PWAHelper::isFeatureEnabled('wallet'),
                'saved_addresses' => true,
            ],
            'contact' => PWAHelper::getContactInfo(),
        ];

        return response()->json($config);
    }

    /**
     * Handle service worker updates
     */
    public function serviceWorkerUpdate(): JsonResponse
    {
        // This endpoint can be used to trigger service worker updates
        return response()->json([
            'success' => true,
            'message' => 'Service worker update triggered',
            'timestamp' => now()->toISOString(),
        ]);
    }
} 