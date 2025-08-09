@extends('layouts.app')

@section('title', 'Create Video Package - ' . $restaurant->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Video Package</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Request professional video content for your restaurant</p>
                </div>
                <a href="{{ route('restaurant.video-packages.index', $restaurant->slug) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Video Packages
                </a>
            </div>
        </div>

        <!-- Package Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Package Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600 mb-2">Basic</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">₦50,000</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">1 video, 60 seconds</div>
                        <div class="text-xs text-gray-500">
                            <div>• Professional video production</div>
                            <div>• Social media optimization</div>
                            <div>• Basic editing & effects</div>
                        </div>
                    </div>
                    
                    <div class="text-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800">
                        <div class="text-2xl font-bold text-purple-600 mb-2">Premium</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">₦100,000</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">2 videos, 90 seconds each</div>
                        <div class="text-xs text-gray-500">
                            <div>• Advanced video production</div>
                            <div>• Multiple platform formats</div>
                            <div>• Professional editing & effects</div>
                        </div>
                    </div>
                    
                    <div class="text-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 mb-2">Custom</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">₦150,000+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">Multiple videos, custom duration</div>
                        <div class="text-xs text-gray-500">
                            <div>• Custom video production</div>
                            <div>• Advanced effects & animation</div>
                            <div>• Full project management</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Package Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Video Package Request Form</h3>
            </div>

            <form action="{{ route('restaurant.video-packages.store', $restaurant->slug) }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <!-- Package Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="package_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Package Name *
                        </label>
                        <input type="text" 
                               name="package_name" 
                               id="package_name" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="e.g., Restaurant Introduction Video">
                        @error('package_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="package_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Package Type *
                        </label>
                        <select name="package_type" 
                                id="package_type" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select Package Type</option>
                            <option value="basic">Basic Package</option>
                            <option value="premium">Premium Package</option>
                            <option value="custom">Custom Package</option>
                        </select>
                        @error('package_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Project Description *
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                              placeholder="Describe your video project, goals, and what you want to showcase..."></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Video Specifications -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="video_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Video Duration (seconds) *
                        </label>
                        <input type="number" 
                               name="video_duration" 
                               id="video_duration" 
                               required
                               min="15" 
                               max="300"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="60">
                        <p class="mt-1 text-xs text-gray-500">15 seconds to 5 minutes</p>
                        @error('video_duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="number_of_videos" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Number of Videos
                        </label>
                        <input type="number" 
                               name="number_of_videos" 
                               id="number_of_videos" 
                               min="1" 
                               max="10"
                               value="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('number_of_videos')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Budget (₦) *
                        </label>
                        <input type="number" 
                               name="price" 
                               id="price" 
                               required
                               min="10000" 
                               step="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="50000">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Shoot Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="shoot_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Preferred Shoot Date
                        </label>
                        <input type="date" 
                               name="shoot_date" 
                               id="shoot_date" 
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('shoot_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="shoot_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Preferred Shoot Time
                        </label>
                        <input type="time" 
                               name="shoot_time" 
                               id="shoot_time" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('shoot_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location and Contact -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="location_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Shoot Location Address
                        </label>
                        <input type="text" 
                               name="location_address" 
                               id="location_address" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Restaurant address or preferred location">
                        @error('location_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Contact Person
                        </label>
                        <input type="text" 
                               name="contact_person" 
                               id="contact_person" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Name of person to contact">
                        @error('contact_person')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contact Phone
                    </label>
                    <input type="tel" 
                           name="contact_phone" 
                           id="contact_phone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="Phone number for coordination">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Special Instructions -->
                <div>
                    <label for="special_instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Special Instructions
                    </label>
                    <textarea name="special_instructions" 
                              id="special_instructions" 
                              rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                              placeholder="Any specific requirements, style preferences, or special requests..."></textarea>
                    @error('special_instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>



                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <i class="fas fa-video mr-2"></i>
                        Submit Video Package Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
