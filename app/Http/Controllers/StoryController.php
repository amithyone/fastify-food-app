<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::ordered()->get();
        return view('admin.stories.index', compact('stories'));
    }

    public function create()
    {
        return view('admin.stories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'emoji' => 'nullable|string|max:10',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'show_button' => 'boolean',
            'button_text' => 'nullable|string|max:100',
            'button_action' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        Story::create($request->all());

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story created successfully!');
    }

    public function edit(Story $story)
    {
        return view('admin.stories.edit', compact('story'));
    }

    public function update(Request $request, Story $story)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'emoji' => 'nullable|string|max:10',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'show_button' => 'boolean',
            'button_text' => 'nullable|string|max:100',
            'button_action' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $story->update($request->all());

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story updated successfully!');
    }

    public function destroy(Story $story)
    {
        $story->delete();

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story deleted successfully!');
    }

    public function toggleStatus(Story $story)
    {
        $story->update(['is_active' => !$story->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $story->is_active,
            'message' => $story->is_active ? 'Story activated!' : 'Story deactivated!'
        ]);
    }

    // Restaurant Stories Management Methods
    public function restaurantIndex($slug)
    {
        $restaurant = \App\Models\Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check if user is a manager of this restaurant
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant stories. You need manager privileges.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant stories.');
        }
        
        $stories = $restaurant->stories()->ordered()->get();
        
        return view('restaurant.stories.index', compact('stories', 'restaurant'));
    }

    public function restaurantStore(Request $request, $slug)
    {
        \Log::info('Story creation attempt', [
            'user_id' => Auth::id(),
            'restaurant_slug' => $slug,
            'request_data' => $request->all()
        ]);

        $restaurant = \App\Models\Restaurant::where('slug', $slug)->firstOrFail();
        
        // Check authorization
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                \Log::error('Unauthorized story creation attempt', [
                    'user_id' => Auth::id(),
                    'restaurant_id' => $restaurant->id
                ]);
                abort(403, 'Unauthorized access to restaurant stories.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant stories.');
        }
        
        try {
            // Log all request data for debugging
            \Log::info('Story creation request data', [
                'all_data' => $request->all(),
                'has_is_active' => $request->has('is_active'),
                'is_active_value' => $request->input('is_active'),
                'is_active_boolean' => $request->boolean('is_active')
            ]);

            $request->validate([
                'type' => 'required|string|max:50',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'emoji' => 'nullable|string|max:10',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'original_price' => 'nullable|numeric|min:0',
                'show_button' => 'nullable|boolean',
                'button_text' => 'nullable|string|max:100',
                'button_action' => 'nullable|string|max:100',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
                'color_gradient' => 'nullable|string|max:50'
            ]);

            \Log::info('Validation passed for story creation', [
                'validated_data' => $request->all()
            ]);

            $data = $request->all();
            $data['restaurant_id'] = $restaurant->id;
            
            // Handle boolean fields properly
            $data['is_active'] = $request->boolean('is_active');
            $data['show_button'] = $request->boolean('show_button');
            
            \Log::info('Data before story creation', [
                'processed_data' => $data
            ]);
            
            $story = Story::create($data);

            \Log::info('Story created successfully', [
                'story_id' => $story->id,
                'story_title' => $story->title,
                'story_is_active' => $story->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Story created successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating story', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating story: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restaurantUpdate(Request $request, $slug, $story)
    {
        \Log::info('Story update attempt', [
            'user_id' => Auth::id(),
            'restaurant_slug' => $slug,
            'story_id' => $story,
            'request_data' => $request->all()
        ]);

        $restaurant = \App\Models\Restaurant::where('slug', $slug)->firstOrFail();
        $story = Story::where('id', $story)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        // Check authorization
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                \Log::error('Unauthorized story update attempt', [
                    'user_id' => Auth::id(),
                    'restaurant_id' => $restaurant->id,
                    'story_id' => $story
                ]);
                abort(403, 'Unauthorized access to restaurant stories.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant stories.');
        }
        
        try {
            \Log::info('Story update request data', [
                'all_data' => $request->all(),
                'has_is_active' => $request->has('is_active'),
                'is_active_value' => $request->input('is_active'),
                'is_active_boolean' => $request->boolean('is_active')
            ]);

            $request->validate([
                'type' => 'required|string|max:50',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'emoji' => 'nullable|string|max:10',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'original_price' => 'nullable|numeric|min:0',
                'show_button' => 'nullable|boolean',
                'button_text' => 'nullable|string|max:100',
                'button_action' => 'nullable|string|max:100',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
                'color_gradient' => 'nullable|string|max:50'
            ]);

            \Log::info('Validation passed for story update', [
                'validated_data' => $request->all()
            ]);

            $data = $request->all();
            
            // Handle boolean fields properly
            $data['is_active'] = $request->boolean('is_active');
            $data['show_button'] = $request->boolean('show_button');
            
            \Log::info('Data before story update', [
                'processed_data' => $data
            ]);

            $story->update($data);

            \Log::info('Story updated successfully', [
                'story_id' => $story->id,
                'story_title' => $story->title,
                'story_is_active' => $story->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Story updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating story', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating story: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restaurantDestroy($slug, $story)
    {
        $restaurant = \App\Models\Restaurant::where('slug', $slug)->firstOrFail();
        $story = Story::where('id', $story)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        // Check authorization
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant stories.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant stories.');
        }
        
        $story->delete();

        return response()->json([
            'success' => true,
            'message' => 'Story deleted successfully!'
        ]);
    }

    public function restaurantEdit($slug, $story)
    {
        $restaurant = \App\Models\Restaurant::where('slug', $slug)->firstOrFail();
        $story = Story::where('id', $story)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        // Check authorization
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant stories.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant stories.');
        }
        
        return response()->json([
            'success' => true,
            'story' => $story
        ]);
    }

    public function restaurantToggleStatus($slug, $story)
    {
        $restaurant = \App\Models\Restaurant::where('slug', $slug)->firstOrFail();
        $story = Story::where('id', $story)->where('restaurant_id', $restaurant->id)->firstOrFail();
        
        // Check authorization
        if (Auth::check()) {
            if (!\App\Models\Manager::canAccessRestaurant(Auth::id(), $restaurant->id, 'manager') && !Auth::user()->isAdmin()) {
                abort(403, 'Unauthorized access to restaurant stories.');
            }
        } else {
            return redirect()->route('login')->with('error', 'Please login to access the restaurant stories.');
        }
        
        $story->update(['is_active' => !$story->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $story->is_active,
            'message' => $story->is_active ? 'Story activated!' : 'Story deactivated!'
        ]);
    }
}
