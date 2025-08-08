@extends('layouts.app')

@section('title', $restaurant->name . ' - Create Social Media Campaign')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Social Media Campaign</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Design and launch your social media marketing campaign</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('restaurant.social-media.index', $restaurant->slug) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Campaigns
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Campaign Details</h3>
            </div>

            <form action="{{ route('restaurant.social-media.store', $restaurant->slug) }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <!-- Campaign Name -->
                <div>
                    <label for="campaign_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Campaign Name *
                    </label>
                    <input type="text" id="campaign_name" name="campaign_name" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="e.g., Summer Food Festival 2024"
                           value="{{ old('campaign_name') }}">
                    @error('campaign_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Campaign Description *
                    </label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                              placeholder="Describe your campaign goals, target audience, and key messaging...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Platform Selection -->
                <div>
                    <label for="platform" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Social Media Platform *
                    </label>
                    <select id="platform" name="platform" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="">Select Platform</option>
                        <option value="instagram" {{ old('platform') == 'instagram' ? 'selected' : '' }}>
                            <i class="fab fa-instagram"></i> Instagram
                        </option>
                        <option value="facebook" {{ old('platform') == 'facebook' ? 'selected' : '' }}>
                            <i class="fab fa-facebook"></i> Facebook
                        </option>
                        <option value="twitter" {{ old('platform') == 'twitter' ? 'selected' : '' }}>
                            <i class="fab fa-twitter"></i> Twitter
                        </option>
                        <option value="tiktok" {{ old('platform') == 'tiktok' ? 'selected' : '' }}>
                            <i class="fab fa-tiktok"></i> TikTok
                        </option>
                        <option value="youtube" {{ old('platform') == 'youtube' ? 'selected' : '' }}>
                            <i class="fab fa-youtube"></i> YouTube
                        </option>
                        <option value="all" {{ old('platform') == 'all' ? 'selected' : '' }}>
                            <i class="fas fa-share-alt"></i> All Platforms
                        </option>
                    </select>
                    @error('platform')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Budget and Dates -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="budget" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Budget (₦)
                        </label>
                        <input type="number" id="budget" name="budget" min="0" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="0.00"
                               value="{{ old('budget') }}">
                        @error('budget')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Date *
                        </label>
                        <input type="date" id="start_date" name="start_date" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               value="{{ old('start_date', date('Y-m-d')) }}">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            End Date *
                        </label>
                        <input type="date" id="end_date" name="end_date" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Target Audience -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Target Audience
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="target_audience[]" value="young_adults" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   {{ in_array('young_adults', old('target_audience', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Young Adults (18-25)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="target_audience[]" value="professionals" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   {{ in_array('professionals', old('target_audience', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Professionals (25-40)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="target_audience[]" value="families" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   {{ in_array('families', old('target_audience', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Families</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="target_audience[]" value="food_enthusiasts" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   {{ in_array('food_enthusiasts', old('target_audience', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Food Enthusiasts</span>
                        </label>
                    </div>
                </div>

                <!-- Content Plan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Content Plan
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="content_plan[]" value="food_photos" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   {{ in_array('food_photos', old('content_plan', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Food Photos</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="content_plan[]" value="behind_scenes" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   {{ in_array('behind_scenes', old('content_plan', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Behind the Scenes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="content_plan[]" value="customer_reviews" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   {{ in_array('customer_reviews', old('content_plan', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Customer Reviews</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="content_plan[]" value="promotions" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                   {{ in_array('promotions', old('content_plan', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Promotions & Offers</span>
                        </label>
                    </div>
                </div>

                <!-- Hashtags and CTA -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="hashtags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Hashtags
                        </label>
                        <input type="text" id="hashtags" name="hashtags"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="e.g., #foodie #restaurant #delicious"
                               value="{{ old('hashtags') }}">
                        @error('hashtags')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="call_to_action" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Call to Action
                        </label>
                        <input type="text" id="call_to_action" name="call_to_action"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="e.g., Order Now, Visit Us, Book a Table"
                               value="{{ old('call_to_action') }}">
                        @error('call_to_action')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Landing Page URL -->
                <div>
                    <label for="landing_page_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Landing Page URL
                    </label>
                    <input type="url" id="landing_page_url" name="landing_page_url"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="https://your-restaurant.com/special-offer"
                           value="{{ old('landing_page_url') }}">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Optional: Custom landing page for your campaign. Leave empty to use your main menu.
                    </p>
                    @error('landing_page_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('restaurant.social-media.index', $restaurant->slug) }}" 
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum end date to start date
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });
    
    // Set minimum start date to today
    const today = new Date().toISOString().split('T')[0];
    startDate.min = today;
});
</script>
@endsection
