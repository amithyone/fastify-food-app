<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class PWAHelper
{
    /**
     * Get restaurant configuration
     */
    public static function getRestaurantConfig()
    {
        return Config::get('restaurant');
    }

    /**
     * Generate dynamic PWA manifest
     */
    public static function generateManifest()
    {
        $config = self::getRestaurantConfig();
        
        return [
            'name' => $config['pwa']['name'],
            'short_name' => $config['pwa']['short_name'],
            'description' => $config['pwa']['description'],
            'start_url' => $config['pwa']['start_url'],
            'display' => $config['pwa']['display'],
            'background_color' => $config['pwa']['background_color'],
            'theme_color' => $config['pwa']['theme_color'],
            'orientation' => $config['pwa']['orientation'],
            'scope' => $config['pwa']['scope'],
            'lang' => 'en',
            'categories' => ['food', 'lifestyle', 'shopping'],
            'icons' => self::generateIconsArray($config['pwa']['icons']),
            'shortcuts' => $config['pwa']['shortcuts'],
            'screenshots' => self::generateScreenshotsArray(),
            'related_applications' => [],
            'prefer_related_applications' => false,
            'edge_side_panel' => [
                'preferred_width' => 400
            ]
        ];
    }

    /**
     * Generate icons array for manifest
     */
    private static function generateIconsArray($icons)
    {
        $manifestIcons = [];
        
        foreach ($icons as $size => $path) {
            $dimensions = explode('x', $size);
            $manifestIcons[] = [
                'src' => $path,
                'sizes' => $size,
                'type' => 'image/png',
                'purpose' => 'maskable any'
            ];
        }
        
        return $manifestIcons;
    }

    /**
     * Generate screenshots array for manifest
     */
    private static function generateScreenshotsArray()
    {
        return [
            [
                'src' => '/screenshots/mobile-menu.png',
                'sizes' => '390x844',
                'type' => 'image/png',
                'form_factor' => 'narrow',
                'label' => 'Menu page showing food categories and dishes'
            ],
            [
                'src' => '/screenshots/mobile-cart.png',
                'sizes' => '390x844',
                'type' => 'image/png',
                'form_factor' => 'narrow',
                'label' => 'Shopping cart with selected items'
            ],
            [
                'src' => '/screenshots/mobile-orders.png',
                'sizes' => '390x844',
                'type' => 'image/png',
                'form_factor' => 'narrow',
                'label' => 'Order tracking with real-time status updates'
            ]
        ];
    }

    /**
     * Get PWA meta tags
     */
    public static function getMetaTags()
    {
        $config = self::getRestaurantConfig();
        
        return [
            'theme-color' => $config['pwa']['theme_color'],
            'apple-mobile-web-app-capable' => 'yes',
            'apple-mobile-web-app-status-bar-style' => 'default',
            'apple-mobile-web-app-title' => $config['pwa']['short_name'],
            'mobile-web-app-capable' => 'yes',
            'application-name' => $config['pwa']['short_name'],
            'msapplication-TileColor' => $config['pwa']['theme_color'],
            'msapplication-tap-highlight' => 'no',
        ];
    }

    /**
     * Get PWA icon links
     */
    public static function getIconLinks()
    {
        $config = self::getRestaurantConfig();
        
        return [
            'icon-32x32' => $config['pwa']['icons']['32x32'],
            'icon-16x16' => $config['pwa']['icons']['16x16'],
            'apple-touch-icon' => $config['pwa']['icons']['192x192'],
            'mask-icon' => $config['pwa']['icons']['192x192'],
        ];
    }

    /**
     * Get app title
     */
    public static function getAppTitle()
    {
        $config = self::getRestaurantConfig();
        return config('app.name', $config['name']);
    }

    /**
     * Get restaurant name
     */
    public static function getRestaurantName()
    {
        $config = self::getRestaurantConfig();
        return $config['name'];
    }

    /**
     * Get restaurant display name
     */
    public static function getRestaurantDisplayName()
    {
        $config = self::getRestaurantConfig();
        return $config['display_name'];
    }

    /**
     * Get restaurant short name
     */
    public static function getRestaurantShortName()
    {
        $config = self::getRestaurantConfig();
        return $config['short_name'];
    }

    /**
     * Get theme color
     */
    public static function getThemeColor()
    {
        $config = self::getRestaurantConfig();
        return $config['pwa']['theme_color'];
    }

    /**
     * Check if feature is enabled
     */
    public static function isFeatureEnabled($feature)
    {
        $config = self::getRestaurantConfig();
        return $config['features'][$feature] ?? false;
    }

    /**
     * Get business settings
     */
    public static function getBusinessSettings()
    {
        $config = self::getRestaurantConfig();
        return $config['business'];
    }

    /**
     * Get contact information
     */
    public static function getContactInfo()
    {
        $config = self::getRestaurantConfig();
        return [
            'phone' => $config['phone'],
            'email' => $config['email'],
            'address' => $config['address'],
        ];
    }

    /**
     * Get social media links
     */
    public static function getSocialLinks()
    {
        $config = self::getRestaurantConfig();
        return $config['social'];
    }

    /**
     * Get SEO settings
     */
    public static function getSEOSettings()
    {
        $config = self::getRestaurantConfig();
        return $config['seo'];
    }

    /**
     * Get placeholder image for menu items
     */
    public static function getPlaceholderImage($type = 'square')
    {
        switch ($type) {
            case 'square':
                return '/images/placeholder-square.svg';
            case 'rectangle':
                return '/images/placeholder.svg';
            default:
                return '/images/placeholder-square.svg';
        }
    }

    /**
     * Get menu item image with fallback to placeholder
     */
    public static function getMenuItemImage($imagePath = null, $type = 'square')
    {
        if ($imagePath && file_exists(public_path($imagePath))) {
            return $imagePath;
        }
        return self::getPlaceholderImage($type);
    }
} 