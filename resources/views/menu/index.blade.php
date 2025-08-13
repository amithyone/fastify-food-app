@extends('layouts.app')
@section('title', 'Menu - Fastify')
@section('content')
<!-- Fixed/Sticky Top Bar: always at the very top -->
<div class="fixed top-0 left-0 right-0 z-50 bg-[#f1ecdc] dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-2 shadow-lg max-w-md mx-auto w-full mt-15">
    <div class="flex items-center gap-2 px-4">
        <!-- Menu Toggle Button -->
        <button id="menuToggle" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-orange-300 transition hover:bg-orange-200 dark:hover:bg-gray-600" onclick="console.log('Button clicked via inline handler')">
            <i class="fas fa-bars"></i>
        </button>
        <!-- Search Bar -->
        <div class="flex-1 relative">
            <input type="text" id="searchInput" placeholder="Search for dishes..." class="w-full px-4 py-2 pl-10 border border-gray-200 dark:border-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-300 bg-gray-50 dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-100">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-300"></i>
        </div>
        <!-- Theme Toggle Button -->
        <button id="themeToggle" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-yellow-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i id="themeIcon" class="fas fa-moon"></i>
        </button>
    </div>
</div>
<div class="w-full min-h-screen bg-[#f1ecdc] dark:bg-gray-900">
    <div class="max-w-md mx-auto px-4 py-4">

    <!-- Stories Section -->
    <div class="mb-4" style="margin-top: 60px;">
        @if($stories && $stories->count() > 0)
        <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar w-full whitespace-nowrap" >
            @foreach($stories as $story)
            @php
                $gradient = $story->color_gradient ?? 'orange';
                $gradientClasses = [
                    'orange' => 'bg-gradient-to-tr from-orange-200 to-orange-400 dark:from-orange-700 dark:to-orange-900',
                    'pink' => 'bg-gradient-to-tr from-pink-200 to-pink-400 dark:from-pink-700 dark:to-pink-900',
                    'green' => 'bg-gradient-to-tr from-green-200 to-green-400 dark:from-green-700 dark:to-green-900',
                    'blue' => 'bg-gradient-to-tr from-blue-200 to-blue-400 dark:from-blue-700 dark:to-blue-900',
                    'purple' => 'bg-gradient-to-tr from-purple-200 to-purple-400 dark:from-purple-700 dark:to-purple-900',
                    'emerald' => 'bg-gradient-to-tr from-emerald-200 to-emerald-400 dark:from-emerald-700 dark:to-emerald-900',
                    'red' => 'bg-gradient-to-tr from-red-200 to-red-400 dark:from-red-700 dark:to-red-900',
                ];
                $gradientClass = $gradientClasses[$gradient] ?? $gradientClasses['orange'];
            @endphp
            <div class="flex-shrink-0 w-20 h-28 {{ $gradientClass }} rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('{{ $story->id }}')" >
                <div class="text-lg mb-1">{{ $story->emoji ?: '📖' }}</div>
                <div class="text-center">
                    <div class="font-bold">{{ Str::limit($story->title, 8) }}</div>
                    <div class="text-xs">{{ Str::limit($story->subtitle ?: $story->type, 8) }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Fallback stories if none exist -->
        <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar w-full whitespace-nowrap" >
            <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-orange-200 to-orange-400 dark:from-orange-700 dark:to-orange-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('special')" >
                <div class="text-lg mb-1">🍕</div>
                <div class="text-center">
                    <div class="font-bold">Today's</div>
                    <div>Special</div>
                </div>
            </div>
            
            <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-pink-200 to-pink-400 dark:from-pink-700 dark:to-pink-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('new')">
                <div class="text-lg mb-1">🆕</div>
                <div class="text-center">
                    <div class="font-bold">New</div>
                    <div>Arrivals</div>
                </div>
            </div>
            
            <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-green-200 to-green-400 dark:from-green-700 dark:to-green-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('chef')">
                <div class="text-lg mb-1">👨‍🍳</div>
                <div class="text-center">
                    <div class="font-bold">Chef's</div>
                    <div>Pick</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Kitchen Live Section -->
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700 shadow-xl">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">🍳 Kitchen Live</h3>
            <div class="flex items-center gap-1">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-xs text-gray-600 dark:text-gray-400">Live</span>
            </div>
        </div>
        <!-- Kitchen Live Slider -->
        <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar w-full whitespace-nowrap kitchen-live-slider">
            <!-- HTML Banner Example 1 (clickable) -->
            <a href="/kitchen-live" class="flex-shrink-0 w-64 rounded-lg bg-gradient-to-br from-orange-100 to-yellow-100 dark:from-orange-900/20 dark:to-yellow-900/20 p-4 text-center border border-orange-200 dark:border-orange-700 hover:opacity-90 transition flex flex-col justify-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-utensils text-white text-sm"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Watch Kitchen Live</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400">See our chefs in action!</p>
                </div>
            </a>
            <!-- Reward System Banner (clickable) -->
            <div onclick="showStory('rewards')" class="flex-shrink-0 w-64 rounded-lg bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/20 p-4 text-center border border-green-200 dark:border-green-700 hover:opacity-90 transition flex flex-col justify-center cursor-pointer">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-gift text-white text-sm"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">🎁 Earn Rewards!</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Pay with Bank Transfer & Get Points</p>
                </div>
            </div>
            <!-- HTML Banner Example 3 (clickable) -->
            <a href="/kitchen-live" class="flex-shrink-0 w-64 rounded-lg bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/20 dark:to-pink-900/20 p-4 text-center border border-purple-200 dark:border-purple-700 hover:opacity-90 transition flex flex-col justify-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mb-2">
                        <i class="fas fa-star text-white text-sm"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Chef's Pick</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Recommended dishes!</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="mb-4">
        <div class="flex gap-2 overflow-x-auto hide-scrollbar" id="categoryFilter">
            <button class="category-btn active px-4 py-2 bg-orange-500 text-white dark:text-white rounded-full font-semibold text-xs whitespace-nowrap" data-category="all">All</button>
            @foreach($categories as $category)
                <button class="category-btn px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full font-semibold text-xs text-gray-700 dark:text-gray-200 whitespace-nowrap" data-category="{{ $category->id }}">{{ $category->name }}</button>
            @endforeach
        </div>
    </div>

    <!-- Menu Items Grid -->
    <div class="mb-24" id="menuGrid">
        @php
            $menuItemsByParent = $menuItems->groupBy(function($item) {
                return $item->category && $item->category->parent ? $item->category->parent->name : ($item->category ? 'Main Categories' : 'Uncategorized');
            });
        @endphp
        
        @foreach($menuItemsByParent as $parentName => $items)
            <div class="mb-8 category-section" data-parent="{{ $parentName }}">
                <div class="flex items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        @if($parentName === 'Main Categories')
                            <i class="fas fa-folder-open text-green-500 mr-2"></i>
                        @elseif($parentName === 'Uncategorized')
                            <i class="fas fa-folder-open text-gray-500 mr-2"></i>
                        @else
                            <i class="fas fa-folder-open text-orange-500 mr-2"></i>
                        @endif
                        {{ $parentName }}
                        <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $items->count() }} items)</span>
                    </h2>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    @foreach($items as $item)
            <div class="food-card bg-white dark:bg-gray-800 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-200 overflow-hidden flex flex-col cursor-pointer transform hover:scale-105 border-2 border-transparent relative" 
                 data-category="{{ $item->category_id }}" 
                 data-name="{{ strtolower($item->name) }}"
                 data-item-id="{{ $item->id }}"
                 onclick="handleCardClick(event, {{ $item->id }}, '{{ $item->name }}', {{ $item->price }})">
                
                <div class="h-24 bg-gradient-to-br from-orange-200 to-orange-400 dark:from-gray-700 dark:to-gray-900 flex items-center justify-center">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                    @else
                        <img src="{{ \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white leading-tight">{{ $item->name }}</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $item->category->name }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-bold text-orange-500 dark:text-orange-300">{{ $item->formatted_price }}</span>
                        <div class="w-8 h-8 bg-orange-500 dark:bg-orange-600 rounded-full flex items-center justify-center text-white text-xs font-semibold transition-all duration-200 cursor-pointer hover:bg-orange-600 dark:hover:bg-orange-700"
                             onclick="showDishDetails(event, {{ $item->id }}, '{{ $item->name }}', '{{ $item->description ?? 'No description available.' }}', '{{ $item->formatted_price }}', '{{ $item->category->name }}', '{{ $item->ingredients ?? 'Ingredients not specified.' }}', '{{ $item->allergens ?? 'No allergens specified.' }}')">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Bottom Navigation Bar -->
<x-bottom-nav />

<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

/* Selected card styles */
.food-card.selected {
    border-color: #f97316 !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2) !important;
    transform: scale(1.02) !important;
}

.food-card.selected .bg-orange-500 {
    background-color: #ea580c !important;
}

.food-card.selected .bg-orange-600 {
    background-color: #dc2626 !important;
}

/* Notification styles */
.cart-notification {
    animation: slideInDown 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(10px);
}

@keyframes slideInDown {
    from {
        transform: translate(-50%, -120%);
        opacity: 0;
        scale: 0.8;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
        scale: 1;
    }
}

.cart-notification.hide {
    animation: slideOutUp 0.3s ease-in forwards;
}

@keyframes slideOutUp {
    from {
        transform: translate(-50%, 0);
        opacity: 1;
        scale: 1;
    }
    to {
        transform: translate(-50%, -120%);
        opacity: 0;
        scale: 0.8;
    }
}

/* Dark mode support for notification */
.dark .cart-notification {
    background-color: #059669;
    border-color: #10b981;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}
</style>
@push('scripts')
<script>
// Light/Dark mode toggle
const themeToggle = document.getElementById('themeToggle');
const themeIcon = document.getElementById('themeIcon');

function setTheme(dark) {
    if (dark) {
        document.documentElement.classList.add('dark');
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
    } else {
        document.documentElement.classList.remove('dark');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
    }
}

// Check local storage or system preference
const userPref = localStorage.getItem('theme');
const systemPref = window.matchMedia('(prefers-color-scheme: dark)').matches;
setTheme(userPref === 'dark' || (!userPref && systemPref));

themeToggle.addEventListener('click', () => {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    setTheme(isDark);
});

// Menu Toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up menu toggle...');
    
    const menuToggle = document.getElementById('menuToggle');
    const sideMenu = document.getElementById('sideMenu');
    const closeMenu = document.getElementById('closeMenu');
    
    console.log('Menu elements found:', {
        menuToggle: !!menuToggle,
        sideMenu: !!sideMenu,
        closeMenu: !!closeMenu
    });
    
    let isMenuOpen = false;

    function toggleMenu() {
        console.log('toggleMenu called, current state:', isMenuOpen);
        if (isMenuOpen) {
            // Close menu
            sideMenu.style.transform = 'translateX(-100%)';
            document.body.style.overflow = '';
            isMenuOpen = false;
            console.log('Menu closed');
        } else {
            // Open menu
            sideMenu.style.transform = 'translateX(0)';
            document.body.style.overflow = 'hidden';
            isMenuOpen = true;
            console.log('Menu opened');
        }
    }

    function closeMenuFunc() {
        if (isMenuOpen) {
            toggleMenu();
        }
    }

    // Menu toggle button
    if (menuToggle) {
        console.log('Setting up menu toggle button click handler');
        menuToggle.addEventListener('click', function(e) {
            console.log('Menu toggle button clicked!');
            e.preventDefault();
            e.stopPropagation();
            toggleMenu();
        });
    } else {
        console.error('Menu toggle button not found!');
    }

    // Close button
    if (closeMenu) {
        console.log('Setting up close button click handler');
        closeMenu.addEventListener('click', function(e) {
            console.log('Close button clicked!');
            e.preventDefault();
            e.stopPropagation();
            closeMenuFunc();
        });
    } else {
        console.error('Close button not found!');
    }

    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMenuOpen) {
            closeMenuFunc();
        }
    });
    
    // Make toggleMenu available globally for testing
    window.testMenuToggle = function() {
        console.log('Testing menu toggle...');
        toggleMenu();
    };
});

// Initialize cart array
let cartTotal = 0;
let currentMenuItems = [];
let selectedItems = new Set(); // Track selected items

// Initialize cart display on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing cart display');
    updateCartDisplay();
});

// Story functionality
function showStory(storyId) {
    // Get story from database via AJAX or from stories data
    let story = null;
    
    // Try to find story in window.stories array first
    if (window.stories && window.stories.length > 0) {
        story = window.stories.find(s => s.id == storyId);
    }
    
    // If not found in window.stories, try to fetch from server
    if (!story) {
        // For now, show a generic story modal
        showGenericStory(storyId);
        return;
    }

    // Create story modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
    modal.id = 'storyModal';
    
    // Check if dark mode is active
    const isDarkMode = document.documentElement.classList.contains('dark');
    const modalBgColor = isDarkMode ? '#0b1e35' : '#ffb661';
    
    // Build content based on story data
    let contentHtml = `
        <div class="text-center">
            <div class="text-4xl mb-4">${story.emoji || '📢'}</div>
            <h3 class="text-xl font-bold mb-2">${story.subtitle || story.title}</h3>
            <p class="text-sm mb-3">${story.description || story.content}</p>
    `;
    
    // Add price if available
    if (story.price) {
        contentHtml += `
            <div class="bg-orange-100 dark:bg-orange-900/20 rounded-lg p-3 mb-4">
                <div class="text-lg font-bold text-orange-600 dark:text-orange-400">₦${story.price.toLocaleString()}</div>
                ${story.original_price ? `<div class="text-xs text-orange-600 dark:text-orange-400">Was ₦${story.original_price.toLocaleString()}</div>` : ''}
            </div>
        `;
    }
    
    // Add button if configured
    if (story.show_button && story.button_text) {
        contentHtml += `
            <button onclick="${story.button_action || 'closeStory'}()" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                ${story.button_text}
            </button>
        `;
    }
    
    contentHtml += '</div>';
    
    modal.innerHTML = `
        <div class="bg-[#ffb661] dark:bg-[#0b1e35] rounded-2xl p-6 w-[400px] transform transition-all shadow-2xl" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important; background-color: ${modalBgColor} !important; width: 400px !important; max-width: 400px !important; min-width: 400px !important;">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">${story.title}</h2>
                <button onclick="closeStory()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="text-gray-700 dark:text-gray-300 text-base">
                ${contentHtml}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add click outside to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeStory();
        }
    });
}

function closeStory() {
    const modal = document.getElementById('storyModal');
    if (modal) {
        modal.remove();
    }
}

function showGenericStory(storyId) {
    // Show a generic story modal when story data is not available
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
    modal.id = 'storyModal';
    
    // Check if dark mode is active
    const isDarkMode = document.documentElement.classList.contains('dark');
    const modalBgColor = isDarkMode ? '#0b1e35' : '#ffb661';
    
    modal.innerHTML = `
        <div class="bg-[#ffb661] dark:bg-[#0b1e35] rounded-2xl p-6 w-[400px] transform transition-all shadow-2xl" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important; background-color: ${modalBgColor} !important; width: 400px !important; max-width: 400px !important; min-width: 400px !important;">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Story #${storyId}</h2>
                <button onclick="closeStory()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="text-gray-700 dark:text-gray-300 text-base text-center">
                <div class="text-4xl mb-4">📖</div>
                <h3 class="text-xl font-bold mb-2">Story Content</h3>
                <p class="text-sm mb-3">This story content is being loaded...</p>
                <p class="text-xs text-gray-500">Story ID: ${storyId}</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add click outside to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeStory();
        }
    });
}

function addSpecialToCart() {
    // Add today's special to cart
    addToCart(999, 'Pepperoni Pizza (Special)', 2500);
    closeStory();
}

function addChefPickToCart() {
    // Add chef's pick to cart
    addToCart(998, 'Grilled Salmon (Chef\'s Pick)', 3500);
    closeStory();
}

function openKitchenLive() {
    // Scroll to kitchen live video section
    const videoSection = document.querySelector('#menuYoutubeVideo');
    if (videoSection) {
        videoSection.scrollIntoView({ behavior: 'smooth' });
    }
    closeStory();
}

// Search and filter functions
function performSearch(query) {
    fetch(`/menu/search?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            currentMenuItems = data;
            renderMenuItems(data);
        })
        .catch(error => console.error('Search error:', error));
}

function filterByCategory(categoryId) {
    fetch(`/menu/items?category_id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
            currentMenuItems = data;
            renderMenuItems(data);
        })
        .catch(error => console.error('Filter error:', error));
}

function loadMenuItems() {
    fetch('/menu/items')
        .then(response => response.json())
        .then(data => {
            currentMenuItems = data;
            renderMenuItems(data);
        })
        .catch(error => console.error('Load error:', error));
}

function renderMenuItems(items) {
    const menuContainer = document.getElementById('menuGrid');
    if (!menuContainer) return;

    menuContainer.innerHTML = '';
    
    items.forEach(item => {
        const isSelected = selectedItems.has(item.id);
        const itemHtml = `
            <div class="food-card bg-white dark:bg-gray-800 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-200 overflow-hidden flex flex-col cursor-pointer transform hover:scale-105 border-2 ${isSelected ? 'border-orange-500' : 'border-transparent'} ${isSelected ? 'selected' : ''}" 
                 data-category="${item.category_id}" 
                 data-name="${item.name.toLowerCase()}"
                 data-item-id="${item.id}"
                 onclick="handleCardClick(event, ${item.id}, '${item.name}', ${item.price})">
                <div class="h-24 bg-gradient-to-br from-orange-200 to-orange-400 dark:from-gray-700 dark:to-gray-900 flex items-center justify-center">
                    ${item.image ? `<img src="/storage/${item.image}" alt="${item.name}" class="w-full h-full object-cover">` : `<img src="/images/placeholder-square.svg" alt="${item.name}" class="w-full h-full object-cover">`}
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white leading-tight">${item.name}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">${item.category.name}</span>
                            ${!item.is_available_for_delivery ? '<span class="text-xs text-red-500 bg-red-100 dark:bg-red-900 px-2 py-1 rounded">No Delivery</span>' : ''}
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm font-bold text-orange-500 dark:text-orange-300">₦${item.price.toLocaleString()}</span>
                        <div class="w-8 h-8 bg-orange-500 dark:bg-orange-600 rounded-full flex items-center justify-center text-white text-xs font-semibold transition-all duration-200 cursor-pointer hover:bg-orange-600 dark:hover:bg-orange-700"
                             onclick="showDishDetails(event, ${item.id}, '${item.name}', '${item.description || 'No description available.'}', '₦${item.price.toLocaleString()}', '${item.category.name}', '${item.ingredients || 'Ingredients not specified.'}', '${item.allergens || 'No allergens specified.'}')">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                    </div>
                </div>
            </div>
        `;
        menuContainer.innerHTML += itemHtml;
    });
}

// Handle card click for selection and cart addition
function handleCardClick(event, itemId, name, price) {
    console.log('Card clicked:', itemId, name, price); // Debug log
    
    // Add to cart
    console.log('Adding to cart:', itemId, name, price); // Debug log
    addToCart(itemId, name, price);
    
    // Add visual feedback
    const clickedCard = event.currentTarget;
    clickedCard.style.transform = 'scale(0.95)';
    setTimeout(() => {
        clickedCard.style.transform = 'scale(1.02)';
    }, 100);
}

// Show cart notification
function showCartNotification(itemName) {
    console.log('showCartNotification called with:', itemName); // Debug log
    
    // Remove any existing notifications
    const existingNotification = document.querySelector('.cart-notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Create notification with more prominent positioning
    const notification = document.createElement('div');
    notification.className = 'cart-notification fixed top-32 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-[9999] flex items-center gap-3 max-w-sm border-2 border-green-400';
    notification.style.cssText = 'position: fixed !important; top: 120px !important; left: 50% !important; transform: translateX(-50%) !important; z-index: 9999 !important; background-color: #10b981 !important; color: white !important; padding: 16px 24px !important; border-radius: 8px !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important; display: flex !important; align-items: center !important; gap: 12px !important; max-width: 384px !important; border: 2px solid #34d399 !important;';
    
    notification.innerHTML = `
        <div class="w-6 h-6 bg-white rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check text-green-500 text-sm"></i>
        </div>
        <div class="flex-1">
            <div class="font-semibold text-sm">Added to cart!</div>
            <div class="text-xs opacity-90">${itemName}</div>
        </div>
        <button onclick="this.parentElement.remove()" class="text-white opacity-70 hover:opacity-100 transition-opacity">
            <i class="fas fa-times text-sm"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    console.log('Notification element created and added to DOM'); // Debug log
    console.log('Notification element:', notification); // Debug log
    console.log('Notification position:', notification.getBoundingClientRect()); // Debug log
    
    // Add haptic feedback (vibration) if supported
    if (navigator.vibrate) {
        navigator.vibrate(100);
    }
    
    // Remove after 5 seconds (increased for testing)
    setTimeout(() => {
        notification.classList.add('hide');
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Cart functionality
function addToCart(itemId, name, price) {
    console.log('addToCart called with:', itemId, name, price); // Debug log
    
    // Send request to server to add item to cart
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            menu_item_id: itemId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Item added to cart successfully');
            showCartNotification(name);
            updateCartDisplay();
        } else {
            console.error('Failed to add item to cart:', data.message);
        }
    })
    .catch(error => {
        console.error('Error adding item to cart:', error);
    });
}

function updateCartDisplay() {
    // Fetch cart count from server
    fetch('/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.getElementById('cartCount');
            const menuCartCount = document.getElementById('menuCartCount');
            const totalItems = data.count || 0;
            
            console.log('updateCartDisplay called - totalItems:', totalItems);
            
            if (cartCount) {
                cartCount.textContent = totalItems;
                cartCount.classList.toggle('hidden', totalItems === 0);
                console.log('Cart count updated to:', totalItems);
            } else {
                console.error('cartCount element not found!');
            }
            
            if (menuCartCount) {
                menuCartCount.textContent = totalItems;
                menuCartCount.classList.toggle('hidden', totalItems === 0);
            }
        })
        .catch(error => {
            console.error('Error fetching cart count:', error);
        });
}

function openCart() {
    window.location.href = '/cart';
}

// Dish Details Modal
function showDishDetails(event, itemId, name, description, price, category, ingredients, allergens) {
    event.stopPropagation(); // Prevent card click from triggering

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
    modal.id = 'dishDetailsModal';

    // Extract numeric price from formatted price string
    const numericPrice = price.replace(/[₦,]/g, '');

    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-md transform transition-all shadow-2xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">${name}</h2>
                <button onclick="closeDishDetails()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="text-gray-700 dark:text-gray-300 text-base space-y-3">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Description</h3>
                    <p class="text-sm">${description}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Price</h3>
                    <p class="text-lg font-bold text-orange-500 dark:text-orange-400">${price}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Category</h3>
                    <p class="text-sm">${category}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Ingredients</h3>
                    <p class="text-sm">${ingredients}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Allergens</h3>
                    <p class="text-sm">${allergens}</p>
                </div>
                <div class="pt-4">
                    <button onclick="addToCartFromModal(${itemId}, '${name}', ${numericPrice})" 
                            class="w-full bg-orange-500 text-white px-4 py-3 rounded-lg text-sm font-semibold hover:bg-orange-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add to Cart
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeDishDetails();
        }
    });
}

function closeDishDetails() {
    const modal = document.getElementById('dishDetailsModal');
    if (modal) {
        modal.remove();
    }
}

function addToCartFromModal(itemId, name, price) {
    addToCart(itemId, name, price);
    closeDishDetails();
}

// Test functions for debugging
function testNotification() {
    console.log('Testing notification...');
    showCartNotification('Test Item');
}

function testCart() {
    console.log('Testing cart...');
    addToCart(1, 'Test Item', 100);
    updateCartDisplay();
    updateMenuCartCount();
}


</script>

<!-- Pass stories from PHP to JavaScript -->
<script>
window.stories = @json($stories);
</script>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const storiesContainer = document.querySelector('.hide-scrollbar.w-full.whitespace-nowrap');
    if (storiesContainer) {
        // Scroll 80px to the right after a short delay for effect
        setTimeout(() => {
            storiesContainer.scrollTo({ left: 80, behavior: 'smooth' });
        }, 400);
    }

    // Kitchen Live slider auto-scroll
    const kitchenSlider = document.querySelector('.kitchen-live-slider');
    if (kitchenSlider) {
        let scrollDirection = 1; // 1 for right, -1 for left
        let scrollAmount = 0;
        const maxScroll = kitchenSlider.scrollWidth - kitchenSlider.clientWidth;
        
        function autoScroll() {
            if (scrollAmount >= maxScroll) {
                scrollDirection = -1; // Change direction to left
            } else if (scrollAmount <= 0) {
                scrollDirection = 1; // Change direction to right
            }
            
            scrollAmount += scrollDirection * 2; // Scroll 2px at a time
            kitchenSlider.scrollTo({ left: scrollAmount, behavior: 'auto' });
        }
        
        // Start auto-scroll every 50ms (smooth animation)
        const autoScrollInterval = setInterval(autoScroll, 50);
        
        // Pause auto-scroll when user interacts
        kitchenSlider.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
        kitchenSlider.addEventListener('touchstart', () => clearInterval(autoScrollInterval));
    }
});

// Pass stories from PHP to JavaScript
window.stories = @json($stories);
</script>
@endpush
</div>
</div>
@endsection
<div id="sideMenu" class="fixed top-0 left-0 h-full w-80 max-w-[85vw] bg-white dark:bg-gray-800 shadow-2xl backdrop-blur-sm transition-transform duration-300 ease-in-out z-50" style="transform: translateX(-100%);">
    <!-- Menu Header -->
    <div class="flex items-center justify-between p-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Menu</h2>
        <button id="closeMenu" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <!-- Menu Items -->
    <div class="p-3 space-y-3">
        <!-- Home -->
        <a href="/menu" class="flex items-center gap-3 p-2 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition">
            <i class="fas fa-home text-lg"></i>
            <span class="font-medium">Home</span>
        </a>

        <!-- Quick Actions -->
        <div class="space-y-1">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Quick Actions</h3>
            <a href="/cart" class="flex items-center gap-3 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-shopping-cart text-lg"></i>
                <span>My Cart</span>
                <span id="menuCartCount" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden" style="background-color: #ef4444 !important; color: white !important;">0</span>
            </a>
            <a href="{{ route('user.orders') }}" class="flex items-center gap-3 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-receipt text-lg"></i>
                <span>My Orders</span>
            </a>
            <a href="{{ route('login') }}" class="flex items-center gap-3 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-user text-lg"></i>
                <span>Login</span>
            </a>
        </div>
        <!-- Contact -->
        <div class="space-y-1">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Contact</h3>
            <a href="https://wa.me/" target="_blank" class="flex items-center gap-3 p-2 rounded-lg text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                <i class="fab fa-whatsapp text-lg"></i>
                <span>WhatsApp Support</span>
            </a>
            <a href="tel:+234" class="flex items-center gap-3 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-phone text-lg"></i>
                <span>Call Us</span>
            </a>
        </div>
    </div>
</div>
