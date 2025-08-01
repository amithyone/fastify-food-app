@extends('layouts.guest')

@section('title', 'Abuja Eat - Digital Menu Solutions for Restaurants')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 dark:from-gray-900 dark:to-gray-800">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-qrcode text-4xl text-white"></i>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                    Transform Your Restaurant with
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-orange-600">
                        Digital Menus
                    </span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-8 max-w-3xl mx-auto">
                    Create beautiful QR code menus for your restaurant. Let customers browse, order, and pay seamlessly from their phones.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('restaurant.onboarding') }}" 
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-rocket mr-3"></i>
                        Get Started Free
                    </a>
                    <a href="#features" 
                        class="inline-flex items-center px-8 py-4 border-2 border-orange-500 text-orange-500 dark:text-orange-400 font-bold rounded-lg hover:bg-orange-500 hover:text-white transition-all duration-200">
                        <i class="fas fa-play mr-3"></i>
                        See How It Works
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Everything You Need to Go Digital
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    From QR codes to order management, we've got you covered
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- QR Code Menus -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-qrcode text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">QR Code Menus</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Generate unique QR codes for each table. Customers scan and instantly access your digital menu.
                    </p>
                </div>

                <!-- Digital Menus -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-mobile-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Beautiful Digital Menus</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Create stunning, mobile-friendly menus with your branding, photos, and descriptions.
                    </p>
                </div>

                <!-- Order Management -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-cart text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Order Management</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Track orders in real-time, manage inventory, and streamline your kitchen operations.
                    </p>
                </div>

                <!-- Analytics -->
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-chart-line text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Analytics & Insights</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Get detailed insights into customer preferences, popular items, and sales performance.
                    </p>
                </div>

                <!-- Multi-location -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-store text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Multi-location Support</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Manage multiple restaurant locations from a single dashboard with centralized control.
                    </p>
                </div>

                <!-- 24/7 Support -->
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-indigo-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-headset text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">24/7 Support</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Get help whenever you need it with our dedicated customer support team.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="py-24 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    How It Works
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    Get your restaurant online in just 3 simple steps
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-orange-500 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">1</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Sign Up & Setup</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Create your restaurant profile, upload your menu, and customize your branding in minutes.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-orange-500 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">2</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Generate QR Codes</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Generate unique QR codes for each table and print them for your customers to scan.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-orange-500 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-6">3</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Start Taking Orders</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Customers scan QR codes, browse your menu, and place orders directly from their phones.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Section -->
    <div class="py-24 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Simple, Transparent Pricing
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">
                    Start free, scale as you grow
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Free Plan -->
                <div class="bg-white dark:bg-gray-700 rounded-2xl p-8 border-2 border-gray-200 dark:border-gray-600">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Free</h3>
                        <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2">₦0</div>
                        <p class="text-gray-600 dark:text-gray-400">Forever</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Up to 50 menu items</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Basic QR codes</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Order notifications</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Email support</span>
                        </li>
                    </ul>
                    <a href="{{ route('restaurant.onboarding') }}" 
                        class="w-full py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors text-center block">
                        Get Started Free
                    </a>
                </div>

                <!-- Pro Plan -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-8 text-white relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-semibold">Most Popular</span>
                    </div>
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold mb-2">Pro</h3>
                        <div class="text-4xl font-bold mb-2">₦5,000</div>
                        <p class="text-orange-100">Per month</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-white mr-3"></i>
                            <span>Unlimited menu items</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-white mr-3"></i>
                            <span>Advanced QR codes</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-white mr-3"></i>
                            <span>Analytics & insights</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-white mr-3"></i>
                            <span>Priority support</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-white mr-3"></i>
                            <span>Custom branding</span>
                        </li>
                    </ul>
                    <a href="{{ route('restaurant.onboarding') }}" 
                        class="w-full py-3 bg-white text-orange-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors text-center block">
                        Start Pro Trial
                    </a>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-white dark:bg-gray-700 rounded-2xl p-8 border-2 border-gray-200 dark:border-gray-600">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Enterprise</h3>
                        <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Custom</div>
                        <p class="text-gray-600 dark:text-gray-400">Contact us</p>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Multi-location support</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">API access</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Dedicated support</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700 dark:text-gray-300">Custom integrations</span>
                        </li>
                    </ul>
                    <a href="mailto:enterprise@abujaeat.com" 
                        class="w-full py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors text-center block">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-24 bg-gradient-to-r from-orange-500 to-orange-600">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-white mb-6">
                Ready to Transform Your Restaurant?
            </h2>
            <p class="text-xl text-orange-100 mb-8">
                Join thousands of restaurants already using Abuja Eat to digitize their menus and boost sales.
            </p>
            <a href="{{ route('restaurant.onboarding') }}" 
                class="inline-flex items-center px-8 py-4 bg-white text-orange-600 font-bold rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 shadow-lg">
                <i class="fas fa-rocket mr-3"></i>
                Start Your Free Trial
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Abuja Eat</h3>
                    <p class="text-gray-400">
                        Transforming restaurants with digital menu solutions.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Status</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Abuja Eat. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>
@endsection
