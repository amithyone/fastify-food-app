<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class RestaurantDefaultImageController extends Controller
{
    /**
     * Show the default image management page
     */
    public function index($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to restaurant default image.');
        }
        
        // Check if restaurant can set custom default image
        if (!$restaurant->canSetCustomDefaultImage()) {
            return redirect()->route('restaurant.subscription.index', $restaurant->slug)
                ->with('error', 'Custom default images are a Premium feature. Please upgrade your subscription.');
        }
        
        return view('restaurant.default-image.index', compact('restaurant'));
    }

    /**
     * Store the default image
     */
    public function store(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to restaurant default image.');
        }
        
        // Check if restaurant can set custom default image
        if (!$restaurant->canSetCustomDefaultImage()) {
            return response()->json([
                'success' => false,
                'message' => 'Custom default images are a Premium feature. Please upgrade your subscription.'
            ], 403);
        }
        
        $request->validate([
            'default_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);
        
        try {
            // Delete old default image if exists
            if ($restaurant->default_image && Storage::disk('public')->exists($restaurant->default_image)) {
                Storage::disk('public')->delete($restaurant->default_image);
            }
            if ($restaurant->default_image_thumbnail && Storage::disk('public')->exists($restaurant->default_image_thumbnail)) {
                Storage::disk('public')->delete($restaurant->default_image_thumbnail);
            }
            
            // Store the new default image
            $image = $request->file('default_image');
            $originalName = $image->getClientOriginalName();
            $fileName = 'default_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $filePath = 'restaurants/' . $restaurant->id . '/defaults/' . $fileName;
            
            // Store the original image
            $storedPath = $image->storeAs('restaurants/' . $restaurant->id . '/defaults', $fileName, 'public');
            
            // Create thumbnail
            $this->createThumbnail($storedPath, $restaurant->id);
            
            // Update restaurant record
            $restaurant->update([
                'default_image' => $storedPath,
                'default_image_thumbnail' => 'restaurants/' . $restaurant->id . '/defaults/thumbnails/' . $fileName
            ]);
            
            Log::info('Restaurant default image uploaded successfully', [
                'restaurant_id' => $restaurant->id,
                'file_name' => $fileName,
                'file_size' => $image->getSize()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Default image uploaded successfully!',
                'image_url' => $restaurant->fresh()->default_image_url,
                'thumbnail_url' => $restaurant->fresh()->default_image_thumbnail_url
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to upload restaurant default image', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload default image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the default image
     */
    public function destroy($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to restaurant default image.');
        }
        
        try {
            // Delete the files
            if ($restaurant->default_image && Storage::disk('public')->exists($restaurant->default_image)) {
                Storage::disk('public')->delete($restaurant->default_image);
            }
            if ($restaurant->default_image_thumbnail && Storage::disk('public')->exists($restaurant->default_image_thumbnail)) {
                Storage::disk('public')->delete($restaurant->default_image_thumbnail);
            }
            
            // Update restaurant record
            $restaurant->update([
                'default_image' => null,
                'default_image_thumbnail' => null
            ]);
            
            Log::info('Restaurant default image removed', [
                'restaurant_id' => $restaurant->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Default image removed successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to remove restaurant default image', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove default image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create thumbnail for uploaded image
     */
    private function createThumbnail($filePath, $restaurantId)
    {
        try {
            $sourcePath = Storage::disk('public')->path($filePath);
            $pathInfo = pathinfo($filePath);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);
            
            // Create thumbnails directory if it doesn't exist
            $thumbnailDir = dirname($thumbnailFullPath);
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }
            
            // Create thumbnail using GD
            $this->createThumbnailWithGD($sourcePath, $thumbnailFullPath);
            
            Log::info('Default image thumbnail created successfully', [
                'restaurant_id' => $restaurantId,
                'original_path' => $filePath,
                'thumbnail_path' => $thumbnailPath
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create default image thumbnail', [
                'restaurant_id' => $restaurantId,
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create thumbnail using GD library
     */
    private function createThumbnailWithGD($sourcePath, $thumbnailPath)
    {
        $sourceImage = imagecreatefromstring(file_get_contents($sourcePath));
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        
        // Calculate thumbnail dimensions (maintain aspect ratio)
        $maxWidth = 300;
        $maxHeight = 300;
        
        $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
        $thumbnailWidth = round($sourceWidth * $ratio);
        $thumbnailHeight = round($sourceHeight * $ratio);
        
        // Create thumbnail image
        $thumbnailImage = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
        
        // Preserve transparency for PNG images
        imagealphablending($thumbnailImage, false);
        imagesavealpha($thumbnailImage, true);
        $transparent = imagecolorallocatealpha($thumbnailImage, 255, 255, 255, 127);
        imagefilledrectangle($thumbnailImage, 0, 0, $thumbnailWidth, $thumbnailHeight, $transparent);
        
        // Resize and copy
        imagecopyresampled($thumbnailImage, $sourceImage, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $sourceWidth, $sourceHeight);
        
        // Save thumbnail
        $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($thumbnailImage, $thumbnailPath, 85);
                break;
            case 'png':
                imagepng($thumbnailImage, $thumbnailPath, 8);
                break;
            case 'gif':
                imagegif($thumbnailImage, $thumbnailPath);
                break;
            case 'webp':
                imagewebp($thumbnailImage, $thumbnailPath, 85);
                break;
        }
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($thumbnailImage);
    }
}
