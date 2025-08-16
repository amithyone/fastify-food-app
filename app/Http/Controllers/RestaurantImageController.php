<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
// Intervention Image imports are now optional and checked at runtime

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
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to restaurant images.',
                'error' => 'unauthorized'
            ], 403);
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
            'user_id' => Auth::id(),
            'request_url' => request()->url(),
            'request_method' => request()->method()
        ]);
        
        try {
            $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
            
            \Log::info('Restaurant found', [
                'restaurant_id' => $restaurant->id,
                'restaurant_name' => $restaurant->name
            ]);
            
            // Check authorization
            $canAccess = \App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager');
            $isAdmin = Auth::user()->isAdmin();
            
            \Log::info('Authorization check', [
                'can_access' => $canAccess,
                'is_admin' => $isAdmin,
                'user_id' => Auth::id()
            ]);
            
            if (!$canAccess && !$isAdmin) {
                \Log::warning('Unauthorized access attempt to restaurant images', [
                    'user_id' => Auth::id(),
                    'restaurant_id' => $restaurant->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to restaurant images.',
                    'error' => 'unauthorized'
                ], 403);
            }
            
            $images = $restaurant->images()->unused()->orderBy('created_at', 'desc')->get();
            
            \Log::info('Images retrieved successfully', [
                'restaurant_id' => $restaurant->id,
                'images_count' => $images->count(),
                'images' => $images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->url,
                        'thumbnail_url' => $image->thumbnail_url,
                        'original_name' => $image->original_name
                    ];
                })
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
        } catch (\Exception $e) {
            \Log::error('Error in getImages', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving images: ' . $e->getMessage()
            ], 500);
        }
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
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to restaurant images.',
                'error' => 'unauthorized'
            ], 403);
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
     * Set a restaurant image as default menu image (premium/trial only)
     */
    public function setDefault($slug, $imageId)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        // Auth check
        if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to restaurant images.',
                'error' => 'unauthorized'
            ], 403);
        }
        
        // Subscription check: allow premium or trial
        $subscription = $restaurant->activeSubscription;
        if (!$subscription || !($subscription->isTrial() || ($subscription->plan_type === 'premium' && ($subscription->isActive() || $subscription->isTrial())))) {
            return response()->json(['success' => false, 'message' => 'Feature available for Premium or Trial only'], 403);
        }
        
        $image = RestaurantImage::where('restaurant_id', $restaurant->id)->findOrFail($imageId);
        
        // Set as default using original file path
        $restaurant->default_menu_image = $image->file_path;
        $restaurant->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Default menu image set successfully',
            'default_menu_image_url' => $restaurant->default_menu_image_url
        ]);
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
            
            // Try to use Intervention Image if available
            if (class_exists('Intervention\Image\ImageManager')) {
                try {
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $image = $manager->read($fullPath);
                    $image->cover(300, 300);
                    $image->save($thumbnailFullPath, 80);
                    
                    Log::info('Thumbnail created successfully with Intervention Image', [
                        'original_path' => $filePath,
                        'thumbnail_path' => $thumbnailPath
                    ]);
                    return;
                } catch (\Exception $e) {
                    Log::warning('Intervention Image failed, falling back to GD', [
                        'file_path' => $filePath,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Fallback to native GD
            $this->createThumbnailWithGD($fullPath, $thumbnailFullPath);
            
        } catch (\Exception $e) {
            Log::warning('Failed to create thumbnail', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Create thumbnail using native GD functions
     */
    private function createThumbnailWithGD($sourcePath, $thumbnailPath)
    {
        try {
            $imageInfo = getimagesize($sourcePath);
            if (!$imageInfo) {
                throw new \Exception('Unable to get image info');
            }
            
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $type = $imageInfo[2];
            
            // Calculate new dimensions (maintain aspect ratio)
            $maxWidth = 300;
            $maxHeight = 300;
            
            if ($width > $height) {
                $newWidth = $maxWidth;
                $newHeight = floor($height * $maxWidth / $width);
            } else {
                $newHeight = $maxHeight;
                $newWidth = floor($width * $maxHeight / $height);
            }
            
            // Create source image resource
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $source = imagecreatefromjpeg($sourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $source = imagecreatefrompng($sourcePath);
                    break;
                case IMAGETYPE_GIF:
                    $source = imagecreatefromgif($sourcePath);
                    break;
                default:
                    throw new \Exception('Unsupported image type');
            }
            
            if (!$source) {
                throw new \Exception('Failed to create source image resource');
            }
            
            // Create thumbnail resource
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG and GIF
            if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefill($thumbnail, 0, 0, $transparent);
            }
            
            // Resize the image
            imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // Save the thumbnail
            switch ($type) {
                case IMAGETYPE_JPEG:
                    imagejpeg($thumbnail, $thumbnailPath, 80);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($thumbnail, $thumbnailPath, 8);
                    break;
                case IMAGETYPE_GIF:
                    imagegif($thumbnail, $thumbnailPath);
                    break;
            }
            
            // Clean up
            imagedestroy($source);
            imagedestroy($thumbnail);
            
            Log::info('Thumbnail created successfully with GD', [
                'original_path' => $sourcePath,
                'thumbnail_path' => $thumbnailPath,
                'original_size' => "{$width}x{$height}",
                'thumbnail_size' => "{$newWidth}x{$newHeight}"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create thumbnail with GD', [
                'source_path' => $sourcePath,
                'thumbnail_path' => $thumbnailPath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
