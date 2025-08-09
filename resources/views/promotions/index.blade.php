@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                <i class="fas fa-star text-yellow-500 mr-2"></i>
                                Restaurant Promotions
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                Boost your restaurant's visibility and attract more customers with our premium promotion packages.
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Navigation -->
                <div class="mb-8">
                    <div class="flex flex-wrap gap-3 justify-center">
                        <a href="#promotion-plans" 
                           class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-star mr-2"></i>
                            Featured Promotions
                        </a>
                        <a href="#video-packages" 
                           class="inline-flex items-center px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-video mr-2"></i>
                            Video Packages
                        </a>
                        <a href="#social-media" 
                           class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fab fa-instagram mr-2"></i>
                            Social Media
                        </a>
                        <a href="#fastify" 
                           class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-rocket mr-2"></i>
                            Fastify Boost
                        </a>
                    </div>
                </div>

                @if($currentPromotion)
                <!-- Current Active Promotion -->
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6 mb-8 border border-green-200 dark:border-green-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <h3 class="text-lg font-medium text-green-800 dark:text-green-200">Active Promotion</h3>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $currentPromotion->badge_text }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $currentPromotion->display_title }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $currentPromotion->display_description }}</p>
                            
                            <div class="flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                                <span><i class="fas fa-eye mr-1"></i> {{ $currentPromotion->impression_count }} impressions</span>
                                <span><i class="fas fa-mouse-pointer mr-1"></i> {{ $currentPromotion->click_count }} clicks</span>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                <div><strong>Started:</strong> {{ $currentPromotion->featured_from ? $currentPromotion->featured_from->format('M j, Y') : 'Now' }}</div>
                                <div><strong>Ends:</strong> {{ $currentPromotion->featured_until ? $currentPromotion->featured_until->format('M j, Y') : 'Ongoing' }}</div>
                            </div>
                            
                            <a href="{{ route('restaurant.promotions.analytics', $restaurant->slug) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                                <i class="fas fa-chart-line mr-2"></i>
                                View Analytics
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Available Plans -->
                <div class="mb-8" id="promotion-plans">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Available Promotion Plans</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($plans as $plan)
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden hover:shadow-xl transition-shadow">
                            @if($plan->sort_order == 0)
                            <div class="bg-yellow-500 text-white text-center py-2 text-sm font-medium">
                                <i class="fas fa-fire mr-1"></i> Most Popular
                            </div>
                            @endif
                            
                            <div class="p-6">
                                <div class="text-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $plan->name }}</h3>
                                    <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-1">
                                        {{ $plan->formatted_price }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        for {{ $plan->duration_text }}
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ $plan->description }}</p>
                                    
                                    <ul class="space-y-2">
                                        @foreach($plan->features_list as $feature)
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            {{ $feature }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                
                                <a href="{{ route('restaurant.promotions.show', ['slug' => $restaurant->slug, 'planId' => $plan->id]) }}" 
                                   class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center block">
                                    <i class="fas fa-star mr-2"></i>
                                    Choose {{ $plan->name }}
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Additional Promotion Services -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Additional Promotion Services</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Video Packages -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden hover:shadow-xl transition-shadow" id="video-packages">
                            <div class="bg-purple-500 text-white text-center py-2 text-sm font-medium">
                                <i class="fas fa-video mr-1"></i> Video Marketing
                            </div>
                            
                            <div class="p-6">
                                <div class="text-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Video Packages</h3>
                                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">
                                        Starting from ₦50,000
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Professional video content
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Create engaging video content to showcase your restaurant, menu, and atmosphere.</p>
                                    
                                    <ul class="space-y-2 mb-4">
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            Professional video production
                                        </li>
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            Social media optimization
                                        </li>
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            Multiple platform distribution
                                        </li>
                                    </ul>
                                </div>
                                
                                <a href="{{ route('restaurant.video-packages.index', $restaurant->slug) }}" 
                                   class="w-full bg-purple-500 hover:bg-purple-600 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center block">
                                    <i class="fas fa-video mr-2"></i>
                                    View Video Packages
                                </a>
                            </div>
                        </div>

                        <!-- Social Media Promotion -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden hover:shadow-xl transition-shadow" id="social-media">
                            <div class="bg-blue-500 text-white text-center py-2 text-sm font-medium">
                                <i class="fab fa-instagram mr-1"></i> Social Media
                            </div>
                            
                            <div class="p-6">
                                <div class="text-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Social Media Campaigns</h3>
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                                        Starting from ₦25,000
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Monthly campaigns
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Boost your online presence with targeted social media marketing campaigns.</p>
                                    
                                    <ul class="space-y-2 mb-4">
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            Instagram & Facebook ads
                                        </li>
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            Content creation & scheduling
                                        </li>
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            Audience targeting
                                        </li>
                                    </ul>
                                </div>
                                
                                <a href="{{ route('restaurant.social-media.index', $restaurant->slug) }}" 
                                   class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center block">
                                    <i class="fab fa-instagram mr-2"></i>
                                    View Social Media
                                </a>
                            </div>
                        </div>

                        <!-- Fastify Promotion -->
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 overflow-hidden hover:shadow-xl transition-shadow" id="fastify">
                            <div class="bg-green-500 text-white text-center py-2 text-sm font-medium">
                                <i class="fas fa-rocket mr-1"></i> Fastify Boost
                            </div>
                            
                            <div class="p-6">
                                <div class="text-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Fastify Promotion</h3>
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                                        Starting from ₦75,000
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Premium visibility boost
                                    </div>
                                </div>
                                
                                <div class="mb-6">
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Get maximum exposure with our premium Fastify promotion package.</p>
                                    
                                    <ul class="space-y-2 mb-4">
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            Top search results
                                        </li>
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            Featured homepage placement
                                        </li>
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            Priority customer support
                                        </li>
                                    </ul>
                                </div>
                                
                                <a href="{{ route('restaurant.promotions', $restaurant->slug) }}?type=fastify" 
                                   class="w-full bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center block">
                                    <i class="fas fa-rocket mr-2"></i>
                                    Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- How It Works -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">How It Works</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-credit-card text-blue-600"></i>
                            </div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">1. Choose a Plan</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Select the promotion package that best fits your needs and budget.</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-money-bill-wave text-green-600"></i>
                            </div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">2. Make Payment</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Pay securely using bank transfer or other available payment methods.</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-rocket text-purple-600"></i>
                            </div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">3. Get Featured</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Your restaurant will be featured on our homepage and attract more customers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

<style>
html {
    scroll-behavior: smooth;
}

.promotion-section {
    scroll-margin-top: 2rem;
}
</style>

<script>
// Smooth scroll to sections with offset for fixed header
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const offsetTop = target.offsetTop - 100; // Adjust offset as needed
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// Highlight active section in navigation
window.addEventListener('scroll', () => {
    const sections = document.querySelectorAll('[id]');
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 150;
        const sectionHeight = section.clientHeight;
        if (window.pageYOffset >= sectionTop && window.pageYOffset < sectionTop + sectionHeight) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('ring-2', 'ring-white', 'ring-offset-2');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('ring-2', 'ring-white', 'ring-offset-2');
        }
    });
});
</script> 