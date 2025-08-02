@extends('layouts.app')

@section('title', 'Recently Visited - Fastify')

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
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Recently Visited</h1>
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
            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search recently visited..." class="w-full px-4 py-3 pl-10 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-300 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-100">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-300"></i>
                </div>
            </div>

            <!-- Restaurants Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="restaurantsGrid">
                @forelse($recentRestaurants as $restaurant)
                    @include('components.restaurant-card', ['restaurant' => $restaurant])
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-clock text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No recently visited restaurants</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Start ordering from restaurants to see them here</p>
                        <a href="{{ route('restaurants.all') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                            Browse All Restaurants
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden text-center py-8">
                <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No restaurants found</h3>
                <p class="text-gray-600 dark:text-gray-400">Try adjusting your search</p>
            </div>
        </div>
    </div>
</div>

<script>
// Theme toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const searchInput = document.getElementById('searchInput');
    const restaurantsGrid = document.getElementById('restaurantsGrid');
    const noResults = document.getElementById('noResults');
    
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
    
    // Search functionality
    function filterRestaurants() {
        const searchTerm = searchInput.value.toLowerCase();
        const restaurantCards = document.querySelectorAll('.restaurant-card');
        let visibleCount = 0;
        
        restaurantCards.forEach(card => {
            const name = card.dataset.name;
            const cuisine = card.dataset.cuisine;
            
            const matchesSearch = name.includes(searchTerm) || cuisine.includes(searchTerm);
            
            if (matchesSearch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterRestaurants);
});
</script>
@endsection 