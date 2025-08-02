@extends('layouts.app')

@section('title', 'Restaurant Onboarding - Fastify')

@section('content')
<!-- Fixed/Sticky Top Bar: always at the very top -->
<div class="fixed top-0 left-0 right-0 z-50 bg-[#f1ecdc] dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-2 shadow-lg max-w-md mx-auto w-full mt-15">
    <div class="flex items-center gap-2 px-4">
        <!-- Back Button -->
        <button onclick="history.back()" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-orange-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i class="fas fa-arrow-left"></i>
        </button>
        <!-- Title -->
        <div class="flex-1 text-center">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Restaurant Onboarding</h1>
        </div>
        <!-- Theme Toggle Button -->
        <button id="themeToggle" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-yellow-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i id="themeIcon" class="fas fa-moon"></i>
        </button>
    </div>
</div>

<div class="w-full min-h-screen bg-[#f1ecdc] dark:bg-gray-900">
    <div class="max-w-md mx-auto px-4 py-4">
        <!-- Content starts after fixed header -->
        <div style="margin-top: 60px;">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg relative">
                    <div class="text-white font-bold text-2xl tracking-wider">y</div>
                    <div class="absolute -top-2 -left-2 w-4 h-4 bg-red-500 rounded-full"></div>
                    <div class="absolute -bottom-2 -right-2 w-3 h-3 bg-green-600 rounded-full"></div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Welcome to Fastify</h1>
                <p class="text-gray-600 dark:text-white text-lg">Set up your digital menu in minutes</p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        <span class="ml-2 text-sm font-medium text-orange-500 dark:text-orange-400">Restaurant Info</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-200 dark:bg-gray-600 rounded"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">Menu Setup</span>
                    </div>
                    <div class="w-16 h-1 bg-gray-200 dark:bg-gray-600 rounded"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400">QR Codes</span>
                    </div>
                </div>
            </div>

            <!-- Onboarding Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('restaurant.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Restaurant Basic Info -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Restaurant Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    Restaurant Name *
                                </label>
                                <input type="text" id="name" name="name" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                                    placeholder="Enter your restaurant name">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="currency" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    Currency *
                                </label>
                                <select id="currency" name="currency" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                                    <option value="₦">Nigerian Naira (₦)</option>
                                    <option value="$">US Dollar ($)</option>
                                    <option value="€">Euro (€)</option>
                                    <option value="£">British Pound (£)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                            Description
                        </label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            placeholder="Tell customers about your restaurant..."></textarea>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="whatsapp_number" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    WhatsApp Number *
                                </label>
                                <input type="text" id="whatsapp_number" name="whatsapp_number" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                                    placeholder="+234 801 234 5678">
                                @error('whatsapp_number')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone_number" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    Phone Number
                                </label>
                                <input type="text" id="phone_number" name="phone_number"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                                    placeholder="+234 801 234 5678">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    Email Address
                                </label>
                                <input type="email" id="email" name="email"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                                    placeholder="restaurant@example.com">
                            </div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Location Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    Address *
                                </label>
                                <textarea id="address" name="address" rows="3" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                                    placeholder="Enter your restaurant address"></textarea>
                                @error('address')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="city" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                        City *
                                    </label>
                                    <input type="text" id="city" name="city" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                                        placeholder="City">
                                </div>

                                <div>
                                    <label for="state" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                        State *
                                    </label>
                                    <input type="text" id="state" name="state" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                                        placeholder="State">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Restaurant Type -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Restaurant Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="cuisine_type" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    Cuisine Type
                                </label>
                                <select id="cuisine_type" name="cuisine_type"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                                    <option value="">Select cuisine type</option>
                                    <option value="Nigerian">Nigerian</option>
                                    <option value="African">African</option>
                                    <option value="International">International</option>
                                    <option value="Fast Food">Fast Food</option>
                                    <option value="Fine Dining">Fine Dining</option>
                                    <option value="Cafe">Cafe</option>
                                    <option value="Pizza">Pizza</option>
                                    <option value="Chinese">Chinese</option>
                                    <option value="Indian">Indian</option>
                                    <option value="Italian">Italian</option>
                                    <option value="Mexican">Mexican</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="opening_hours" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    Opening Hours
                                </label>
                                <input type="text" id="opening_hours" name="opening_hours"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                                    placeholder="e.g., Mon-Sun: 8AM-10PM">
                            </div>
                        </div>
                    </div>

                    <!-- Media Upload -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Restaurant Media</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="logo" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    Restaurant Logo
                                </label>
                                <input type="file" id="logo" name="logo" accept="image/*"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Recommended: Square image, max 2MB</p>
                            </div>

                            <div>
                                <label for="banner" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">
                                    Restaurant Banner
                                </label>
                                <input type="file" id="banner" name="banner" accept="image/*"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Recommended: 16:9 ratio, max 5MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" 
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-rocket mr-2"></i>
                            Create My Restaurant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Theme toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.classList.toggle('dark', currentTheme === 'dark');
    updateThemeIcon(currentTheme);
    
    themeToggle.addEventListener('click', function() {
        const isDark = document.documentElement.classList.toggle('dark');
        const theme = isDark ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        updateThemeIcon(theme);
    });
    
    function updateThemeIcon(theme) {
        themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
});
</script>
@endsection 