@extends('layouts.app')

@section('title', 'Edit ' . $restaurant->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Restaurant</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Update your restaurant information</p>
                </div>
                <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow py-12" style="padding-left: 20px; padding-right: 20px;">
            <form action="{{ route('restaurant.update', $restaurant->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')
                
                <!-- Restaurant Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Restaurant Name *
                        </label>
                        <input type="text" id="name" name="name" value="{{ $restaurant->name }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Currency *
                        </label>
                        <select id="currency" name="currency" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="₦" {{ $restaurant->currency === '₦' ? 'selected' : '' }}>Nigerian Naira (₦)</option>
                            <option value="$" {{ $restaurant->currency === '$' ? 'selected' : '' }}>US Dollar ($)</option>
                            <option value="€" {{ $restaurant->currency === '€' ? 'selected' : '' }}>Euro (€)</option>
                            <option value="£" {{ $restaurant->currency === '£' ? 'selected' : '' }}>British Pound (£)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        placeholder="Tell customers about your restaurant...">{{ $restaurant->description }}</textarea>
                </div>

                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            WhatsApp Number *
                        </label>
                        <input type="text" id="whatsapp_number" name="whatsapp_number" value="{{ $restaurant->whatsapp_number }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="+234 801 234 5678">
                        @error('whatsapp_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Phone Number
                        </label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ $restaurant->phone_number }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="+234 801 234 5678">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" value="{{ $restaurant->email }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        placeholder="restaurant@example.com">
                </div>

                <!-- Address Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Street Address *
                        </label>
                        <input type="text" id="address" name="address" value="{{ $restaurant->address }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="123 Main Street">
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            City *
                        </label>
                        <input type="text" id="city" name="city" value="{{ $restaurant->city }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="Abuja">
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            State *
                        </label>
                        <input type="text" id="state" name="state" value="{{ $restaurant->state }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="FCT">
                        @error('state')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Postal Code
                        </label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ $restaurant->postal_code }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="900001">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Country *
                        </label>
                        <input type="text" id="country" name="country" value="{{ $restaurant->country }}" required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="Nigeria">
                        @error('country')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Branding -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="theme_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Primary Color *
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="theme_color" name="theme_color" value="{{ $restaurant->theme_color }}" required
                                class="w-16 h-12 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                            <input type="text" value="{{ $restaurant->theme_color }}" 
                                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                placeholder="#ff6b35" readonly>
                        </div>
                    </div>

                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Secondary Color *
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="secondary_color" name="secondary_color" value="{{ $restaurant->secondary_color }}" required
                                class="w-16 h-12 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                            <input type="text" value="{{ $restaurant->secondary_color }}"
                                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                placeholder="#f7931e" readonly>
                        </div>
                    </div>
                </div>

                <!-- Custom Domain -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Custom Domain</h3>
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-globe text-blue-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Custom Domain Benefits</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                    Use your own domain (e.g., taste-of-abuja.com) for a professional branded experience.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="custom_domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Custom Domain
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                    https://
                                </span>
                                <input type="text" id="custom_domain" name="custom_domain" 
                                    value="{{ $restaurant->custom_domain }}"
                                    class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-r-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    placeholder="your-restaurant.com">
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Enter your domain without https:// (e.g., your-restaurant.com)
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Domain Status
                            </label>
                            <div class="flex items-center space-x-2">
                                @if($restaurant->custom_domain)
                                    <span class="inline-flex px-3 py-2 text-sm font-semibold rounded-lg 
                                        @if($restaurant->custom_domain_status === 'verified') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($restaurant->custom_domain_status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                        {{ ucfirst($restaurant->custom_domain_status ?? 'Not Set') }}
                                    </span>
                                    @if($restaurant->custom_domain_verified_at)
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            Verified {{ $restaurant->custom_domain_verified_at->diffForHumans() }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">No custom domain set</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($restaurant->custom_domain && !$restaurant->custom_domain_verified)
                        <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">DNS Configuration Required</h4>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                        Configure your DNS settings to point your domain to our servers. 
                                        <a href="{{ route('restaurant.custom-domain', $restaurant->slug) }}" 
                                            class="text-blue-600 dark:text-blue-400 hover:underline">
                                            View DNS instructions
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Domain Setup Instructions -->
                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-3">How to Set Up Your Custom Domain</h4>
                        <div class="space-y-3 text-sm text-blue-700 dark:text-blue-300">
                            <div class="flex items-start space-x-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-blue-200 dark:bg-blue-700 rounded-full flex items-center justify-center text-xs font-bold text-blue-800 dark:text-blue-200">1</span>
                                <div>
                                    <p class="font-medium">Enter your domain above (e.g., taste-of-abuja.com)</p>
                                    <p class="text-xs opacity-75">Don't include https:// or www.</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-blue-200 dark:bg-blue-700 rounded-full flex items-center justify-center text-xs font-bold text-blue-800 dark:text-blue-200">2</span>
                                <div>
                                    <p class="font-medium">Configure DNS records with your domain provider</p>
                                    <div class="mt-1 p-2 bg-white dark:bg-gray-800 rounded border text-xs font-mono">
                                        <div><strong>Type:</strong> CNAME</div>
                                        <div><strong>Name:</strong> @ (or your domain)</div>
                                        <div><strong>Value:</strong> {{ config('app.domain', 'fastify.com') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-blue-200 dark:bg-blue-700 rounded-full flex items-center justify-center text-xs font-bold text-blue-800 dark:text-blue-200">3</span>
                                <div>
                                    <p class="font-medium">Wait for DNS propagation (up to 24 hours)</p>
                                    <p class="text-xs opacity-75">You can check propagation using online DNS lookup tools</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="flex-shrink-0 w-5 h-5 bg-blue-200 dark:bg-blue-700 rounded-full flex items-center justify-center text-xs font-bold text-blue-800 dark:text-blue-200">4</span>
                                <div>
                                    <p class="font-medium">Click "Verify Domain" in the custom domain management page</p>
                                    <p class="text-xs opacity-75">This will activate your custom domain for customers</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-blue-200 dark:border-blue-700">
                            <a href="{{ route('restaurant.custom-domain', $restaurant->slug) }}" 
                                class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                <i class="fas fa-cog mr-2"></i>
                                Advanced Domain Management
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Restaurant Logo
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                            <input type="file" id="logo" name="logo" accept="image/*"
                                class="hidden" onchange="previewImage(this, 'logo-preview')">
                            <label for="logo" class="cursor-pointer">
                                <div id="logo-preview" class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    @if($restaurant->logo)
                                        <img src="{{ Storage::url($restaurant->logo) }}" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-camera text-2xl text-gray-400"></i>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload logo</p>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="banner_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Banner Image
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                            <input type="file" id="banner_image" name="banner_image" accept="image/*"
                                class="hidden" onchange="previewImage(this, 'banner-preview')">
                            <label for="banner_image" class="cursor-pointer">
                                <div id="banner-preview" class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    @if($restaurant->banner_image)
                                        <img src="{{ Storage::url($restaurant->banner_image) }}" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-image text-2xl text-gray-400"></i>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload banner</p>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6">
                    <button type="submit" 
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-6 rounded-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>
                        Update Restaurant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Update color input text when color picker changes
document.getElementById('theme_color').addEventListener('input', function(e) {
    e.target.nextElementSibling.value = e.target.value;
});

document.getElementById('secondary_color').addEventListener('input', function(e) {
    e.target.nextElementSibling.value = e.target.value;
});
</script>
@endsection 