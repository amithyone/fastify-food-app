<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaCampaign;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SocialMediaCampaignController extends Controller
{
    /**
     * Display a listing of campaigns
     */
    public function index($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $campaigns = $restaurant->socialMediaCampaigns()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('restaurant.social-media.index', compact('restaurant', 'campaigns'));
    }

    /**
     * Show the form for creating a new campaign
     */
    public function create($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return view('restaurant.social-media.create', compact('restaurant'));
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'description' => 'required|string',
            'platform' => 'required|in:instagram,facebook,twitter,tiktok,youtube,all',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'target_audience' => 'nullable|array',
            'content_plan' => 'nullable|array',
            'hashtags' => 'nullable|string|max:500',
            'call_to_action' => 'nullable|string|max:255',
            'landing_page_url' => 'nullable|url|max:500'
        ]);

        $validated['restaurant_id'] = $restaurant->id;
        $validated['status'] = 'draft';

        $campaign = SocialMediaCampaign::create($validated);

        Log::info('Social media campaign created', [
            'restaurant_id' => $restaurant->id,
            'campaign_id' => $campaign->id,
            'campaign_name' => $campaign->campaign_name,
            'platform' => $campaign->platform
        ]);

        return redirect()->route('restaurant.social-media.index', $restaurant->slug)
            ->with('success', 'Campaign created successfully!');
    }

    /**
     * Display the specified campaign
     */
    public function show($slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $campaign = $restaurant->socialMediaCampaigns()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return view('restaurant.social-media.show', compact('restaurant', 'campaign'));
    }

    /**
     * Show the form for editing the specified campaign
     */
    public function edit($slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $campaign = $restaurant->socialMediaCampaigns()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        return view('restaurant.social-media.edit', compact('restaurant', 'campaign'));
    }

    /**
     * Update the specified campaign
     */
    public function update(Request $request, $slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $campaign = $restaurant->socialMediaCampaigns()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'campaign_name' => 'required|string|max:255',
            'description' => 'required|string',
            'platform' => 'required|in:instagram,facebook,twitter,tiktok,youtube,all',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'target_audience' => 'nullable|array',
            'content_plan' => 'nullable|array',
            'hashtags' => 'nullable|string|max:500',
            'call_to_action' => 'nullable|string|max:255',
            'landing_page_url' => 'nullable|url|max:500',
            'status' => 'required|in:draft,pending,active,completed,cancelled'
        ]);

        $campaign->update($validated);

        Log::info('Social media campaign updated', [
            'restaurant_id' => $restaurant->id,
            'campaign_id' => $campaign->id,
            'status' => $campaign->status
        ]);

        return redirect()->route('restaurant.social-media.index', $restaurant->slug)
            ->with('success', 'Campaign updated successfully!');
    }

    /**
     * Remove the specified campaign
     */
    public function destroy($slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $campaign = $restaurant->socialMediaCampaigns()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $campaign->delete();

        Log::info('Social media campaign deleted', [
            'restaurant_id' => $restaurant->id,
            'campaign_id' => $campaign->id
        ]);

        return redirect()->route('restaurant.social-media.index', $restaurant->slug)
            ->with('success', 'Campaign deleted successfully!');
    }

    /**
     * Update campaign analytics
     */
    public function updateAnalytics(Request $request, $slug, $id)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $campaign = $restaurant->socialMediaCampaigns()->findOrFail($id);
        
        if (!Auth::user()->canManageRestaurant($restaurant)) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'impressions' => 'required|integer|min:0',
            'clicks' => 'required|integer|min:0',
            'engagements' => 'required|integer|min:0',
            'roi' => 'nullable|numeric|min:0'
        ]);

        $campaign->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Analytics updated successfully'
        ]);
    }
}
