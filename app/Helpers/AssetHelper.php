<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class AssetHelper
{
    /**
     * Get the asset URL for a given file
     * Works with both Vite (development) and built assets (production)
     */
    public static function getAssetUrl($file)
    {
        // Check configuration and built assets
        $assetMode = config('app.asset_mode', 'built');
        $hasBuiltAssets = File::exists(public_path('build/manifest.json'));
        
        if ($assetMode === 'built' && $hasBuiltAssets) {
            $manifestPath = public_path('build/manifest.json');
            
            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);
                
                if (isset($manifest[$file])) {
                    return asset('build/' . $manifest[$file]['file']);
                }
            }
        }
        
        // Fallback to Vite (development)
        return $file;
    }
    
    /**
     * Get CSS asset URL
     */
    public static function getCssUrl()
    {
        return self::getAssetUrl('resources/css/app.css');
    }
    
    /**
     * Get JS asset URL
     */
    public static function getJsUrl()
    {
        return self::getAssetUrl('resources/js/app.js');
    }
    
    /**
     * Check if built assets exist
     */
    public static function hasBuiltAssets()
    {
        return File::exists(public_path('build/manifest.json'));
    }
    
    /**
     * Get asset tags for CSS and JS
     */
    public static function getAssetTags()
    {
        $tags = '';
        
        if (self::hasBuiltAssets()) {
            // Use built assets
            $cssUrl = self::getCssUrl();
            $jsUrl = self::getJsUrl();
            
            if ($cssUrl && $cssUrl !== 'resources/css/app.css') {
                $tags .= '<link rel="stylesheet" href="' . $cssUrl . '">' . "\n";
            }
            
            if ($jsUrl && $jsUrl !== 'resources/js/app.js') {
                $tags .= '<script type="module" src="' . $jsUrl . '"></script>' . "\n";
            }
        } else {
            // Use Vite (development)
            $tags .= '@vite(["resources/css/app.css", "resources/js/app.js"])' . "\n";
        }
        
        return $tags;
    }
}
