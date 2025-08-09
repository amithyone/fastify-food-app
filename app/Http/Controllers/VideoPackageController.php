<?php

namespace App\Http\Controllers;

use App\Models\VideoPackage;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoPackageController extends Controller
{
    /**
     * Display a listing of video packages
     */
    public function index($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $packages = $restaurant->videoPackages()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('restaurant.video-packages.index', compact('restaurant', 'packages'));
    }

    /**
     * Show the form for creating a new video package
     */
    public function create($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        // Get active video package templates
        $templates = \App\Models\VideoPackageTemplate::active()->ordered()->get();

        return view('restaurant.video-packages.create', compact('restaurant', 'templates'));
    }

    /**
     * Store a newly created video package
     */
    public function store(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'package_name' => 'required|string|max:255',
            'description' => 'required|string',
            'package_type' => 'required|in:basic,premium,custom',
            'price' => 'required|numeric|min:0',
            'video_duration' => 'required|integer|min:30|max:3600',
            'number_of_videos' => 'required|integer|min:1|max:10',
            'video_requirements' => 'nullable|array',
            'deliverables' => 'nullable|array',
            'shoot_date' => 'nullable|date|after_or_equal:today',
            'shoot_time' => 'nullable|date_format:H:i',
            'location_address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'special_instructions' => 'nullable|string'
        ]);

        $validated['restaurant_id'] = $restaurant->id;
        $validated['status'] = 'pending';

        $package = VideoPackage::create($validated);

        Log::info('Video package created', [
            'restaurant_id' => $restaurant->id,
            'package_id' => $package->id,
            'package_name' => $package->package_name,
            'package_type' => $package->package_type
        ]);

        return redirect()->route('restaurant.video-packages.index', $restaurant->slug)
            ->with('success', 'Video package created successfully!');
    }

    /**
     * Display the specified video package
     */
    public function show($slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $package = $restaurant->videoPackages()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return view('restaurant.video-packages.show', compact('restaurant', 'package'));
    }

    /**
     * Show the form for editing the specified video package
     */
    public function edit($slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $package = $restaurant->videoPackages()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return view('restaurant.video-packages.edit', compact('restaurant', 'package'));
    }

    /**
     * Update the specified video package
     */
    public function update(Request $request, $slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $package = $restaurant->videoPackages()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'package_name' => 'required|string|max:255',
            'description' => 'required|string',
            'package_type' => 'required|in:basic,premium,custom',
            'price' => 'required|numeric|min:0',
            'video_duration' => 'required|integer|min:30|max:3600',
            'number_of_videos' => 'required|integer|min:1|max:10',
            'video_requirements' => 'nullable|array',
            'deliverables' => 'nullable|array',
            'shoot_date' => 'nullable|date',
            'shoot_time' => 'nullable|date_format:H:i',
            'location_address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'special_instructions' => 'nullable|string',
            'status' => 'required|in:pending,in_production,completed,delivered,cancelled'
        ]);

        $package->update($validated);

        Log::info('Video package updated', [
            'restaurant_id' => $restaurant->id,
            'package_id' => $package->id,
            'status' => $package->status
        ]);

        return redirect()->route('restaurant.video-packages.index', $restaurant->slug)
            ->with('success', 'Video package updated successfully!');
    }

    /**
     * Remove the specified video package
     */
    public function destroy($slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $package = $restaurant->videoPackages()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        // Delete associated files
        if ($package->video_file_path) {
            Storage::delete($package->video_file_path);
        }
        if ($package->thumbnail_path) {
            Storage::delete($package->thumbnail_path);
        }

        $package->delete();

        Log::info('Video package deleted', [
            'restaurant_id' => $restaurant->id,
            'package_id' => $package->id
        ]);

        return redirect()->route('restaurant.video-packages.index', $restaurant->slug)
            ->with('success', 'Video package deleted successfully!');
    }

    /**
     * Upload video file
     */
    public function uploadVideo(Request $request, $slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $package = $restaurant->videoPackages()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'video_file' => 'required|file|mimes:mp4,mov,avi|max:102400' // 100MB max
        ]);

        $file = $request->file('video_file');
        $path = $file->store('video-packages', 'public');

        $package->update([
            'video_file_path' => $path,
            'status' => 'completed'
        ]);

        Log::info('Video file uploaded', [
            'restaurant_id' => $restaurant->id,
            'package_id' => $package->id,
            'file_path' => $path
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Video uploaded successfully'
        ]);
    }

    /**
     * Update package analytics
     */
    public function updateAnalytics(Request $request, $slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $package = $restaurant->videoPackages()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'views' => 'required|integer|min:0',
            'shares' => 'required|integer|min:0',
            'engagements' => 'required|integer|min:0'
        ]);

        $package->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Analytics updated successfully'
        ]);
    }
}
