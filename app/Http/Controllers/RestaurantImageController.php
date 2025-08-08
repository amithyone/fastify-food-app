<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class RestaurantImageController extends Controller
{
    /**
     * Show the image management page
     */
    public function index($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to restaurant images.');
        }
        
        $images = $restaurant->images()->orderBy('created_at', 'desc')->get();
        $unusedImages = $restaurant->images()->unused()->orderBy('created_at', 'desc')->get();
        $usedImages = $restaurant->images()->used()->orderBy('created_at', 'desc')->get();
        
        return view('restaurant.images.index', compact('restaurant', 'images', 'unusedImages', 'usedImages'));
    }

    /**
     * Handle bulk image upload
     */
    public function bulkUpload(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to restaurant images.');
        }
        
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'alt_text' => 'nullable|string|max:255'
        ]);
        
        $uploadedImages = [];
        $errors = [];
        
        try {
            foreach ($request->file('images') as $image) {
                try {
                    $originalName = $image->getClientOriginalName();
                    $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $filePath = 'restaurant-images/' . $restaurant->id . '/' . $fileName;
                    
                    // Store the original image
                    $storedPath = $image->storeAs('restaurant-images/' . $restaurant->id, $fileName, 'public');
                    
                    // Create thumbnail
                    $this->createThumbnail($storedPath, $restaurant->id);
                    
                    // Save to database
                    $restaurantImage = RestaurantImage::create([
                        'restaurant_id' => $restaurant->id,
                        'original_name' => $originalName,
                        'file_path' => $storedPath,
                        'file_name' => $fileName,
                        'mime_type' => $image->getMimeType(),
                        'file_size' => $image->getSize(),
                        'alt_text' => $request->input('alt_text'),
                        'is_used' => false
                    ]);
                    
                    $uploadedImages[] = $restaurantImage;
                    
                    Log::info('Restaurant image uploaded successfully', [
                        'restaurant_id' => $restaurant->id,
                        'image_id' => $restaurantImage->id,
                        'file_name' => $fileName,
                        'file_size' => $restaurantImage->formatted_file_size
                    ]);
                    
                } catch (\Exception $e) {
                    $errors[] = "Failed to upload {$image->getClientOriginalName()}: " . $e->getMessage();
                    Log::error('Restaurant image upload failed', [
                        'restaurant_id' => $restaurant->id,
                        'file_name' => $image->getClientOriginalName(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $message = count($uploadedImages) . ' image(s) uploaded successfully.';
            if (!empty($errors)) {
                $message .= ' ' . count($errors) . ' image(s) failed to upload.';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'uploaded_images' => $uploadedImages,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bulk image upload failed', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images: ' . $e->getMessage(),
                'debug_info' => [
                    'gd_extension' => extension_loaded('gd'),
                    'storage_writable' => is_writable(storage_path('app/public')),
                    'restaurant_images_dir' => is_writable(storage_path('app/public/restaurant-images'))
                ]
            ], 500);
        }
    }

    /**
     * Get images for menu creation (AJAX)
     */
    public function getImages($slug)
    {
        \Log::info('Getting images for restaurant', [
            'slug' => $slug,
            'user_id' => Auth::id()
        ]);
        
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
            \Log::warning('Unauthorized access attempt to restaurant images', [
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id
            ]);
            abort(403, 'Unauthorized access to restaurant images.');
        }
        
        $images = $restaurant->images()->unused()->orderBy('created_at', 'desc')->get();
        
        \Log::info('Images retrieved successfully', [
            'restaurant_id' => $restaurant->id,
            'images_count' => $images->count()
        ]);
        
        return response()->json([
            'success' => true,
            'images' => $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'thumbnail_url' => $image->thumbnail_url,
                    'original_name' => $image->original_name,
                    'alt_text' => $image->alt_text,
                    'file_size' => $image->formatted_file_size
                ];
            })
        ]);
    }

    /**
     * Delete an image
     */
    public function destroy($slug, $imageId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $image = RestaurantImage::where('restaurant_id', $restaurant->id)->findOrFail($imageId);
        
        // Check authorization
        if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access to restaurant images.');
        }
        
        try {
            // Delete the file from storage
            if (Storage::disk('public')->exists($image->file_path)) {
                Storage::disk('public')->delete($image->file_path);
            }
            
            // Delete thumbnail if exists
            $pathInfo = pathinfo($image->file_path);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            
            // Delete from database
            $image->delete();
            
            Log::info('Restaurant image deleted', [
                'restaurant_id' => $restaurant->id,
                'image_id' => $imageId,
                'file_name' => $image->file_name
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to delete restaurant image', [
                'restaurant_id' => $restaurant->id,
                'image_id' => $imageId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create thumbnail for uploaded image
     */
    private function createThumbnail($filePath, $restaurantId)
    {
        try {
            $fullPath = Storage::disk('public')->path($filePath);
            $pathInfo = pathinfo($filePath);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);
            
            // Create thumbnails directory if it doesn't exist
            $thumbnailDir = dirname($thumbnailFullPath);
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }
            
            // Check if GD extension is available
            if (!extension_loaded('gd')) {
                Log::warning('GD extension not available, skipping thumbnail creation', [
                    'file_path' => $filePath
                ]);
                return;
            }
            
            // Create thumbnail using Intervention Image
            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullPath);
            $image->cover(300, 300);
            $image->save($thumbnailFullPath, 80);
            
            Log::info('Thumbnail created successfully', [
                'original_path' => $filePath,
                'thumbnail_path' => $thumbnailPath
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Failed to create thumbnail', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
