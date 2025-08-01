@extends('layouts.guest')

@section('title', 'Restaurant Onboarding - Abuja Eat')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-utensils text-3xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Welcome to Abuja Eat</h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Set up your digital menu in minutes</p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        <span class="ml-2 text-sm font-medium text-orange-600">Restaurant Info</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-200 rounded"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Menu Setup</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-200 rounded"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        <span class="ml-2 text-sm font-medium text-gray-500">QR Codes</span>
                    </div>
                </div>
            </div>

            <!-- Onboarding Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <form action="{{ route('restaurant.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Restaurant Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Restaurant Name *
                            </label>
                            <input type="text" id="name" name="name" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                placeholder="Enter your restaurant name">
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
                                <option value="₦">Nigerian Naira (₦)</option>
                                <option value="$">US Dollar ($)</option>
                                <option value="€">Euro (€)</option>
                                <option value="£">British Pound (£)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="Tell customers about your restaurant..."></textarea>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                WhatsApp Number *
                            </label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" required
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
                            <input type="text" id="phone_number" name="phone_number"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                placeholder="+234 801 234 5678">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="restaurant@example.com">
                    </div>

                    <!-- Address Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Street Address *
                            </label>
                            <input type="text" id="address" name="address" required
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
                            <input type="text" id="city" name="city" required
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
                            <input type="text" id="state" name="state" required
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
                            <input type="text" id="postal_code" name="postal_code"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                placeholder="900001">
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Country *
                            </label>
                            <input type="text" id="country" name="country" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                value="Nigeria" placeholder="Nigeria">
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
                                <input type="color" id="theme_color" name="theme_color" value="#ff6b35" required
                                    class="w-16 h-12 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                                <input type="text" value="#ff6b35" 
                                    class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    placeholder="#ff6b35" readonly>
                            </div>
                        </div>

                        <div>
                            <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Secondary Color *
                            </label>
                            <div class="flex items-center space-x-3">
                                <input type="color" id="secondary_color" name="secondary_color" value="#f7931e" required
                                    class="w-16 h-12 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                                <input type="text" value="#f7931e"
                                    class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    placeholder="#f7931e" readonly>
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
                                        <i class="fas fa-camera text-2xl text-gray-400"></i>
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
                                        <i class="fas fa-image text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload banner</p>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" 
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold py-4 px-6 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-rocket mr-2"></i>
                            Create My Restaurant
                        </button>
                    </div>
                </form>
            </div>

            <!-- Features Preview -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 text-center shadow-lg">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-qrcode text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">QR Code Menus</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Generate unique QR codes for each table</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 text-center shadow-lg">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-green-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Digital Menus</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Beautiful, mobile-friendly menu display</p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 text-center shadow-lg">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Order Management</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Track orders and manage inventory</p>
                </div>
            </div>
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