@extends('layouts.app')
@section('title', 'Menu - Abuja Eat')
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
        <!-- Manual PWA Install Button (for testing) -->
        <div class="mb-4 text-center">
            <button id="manualPWAInstall" class="bg-purple-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-600 transition-colors">
                📱 Install App
            </button>
        </div>
        
        <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar w-full whitespace-nowrap" >
            <!-- Today's Special -->
            <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-orange-200 to-orange-400 dark:from-orange-700 dark:to-orange-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('special')" >
                <div class="text-lg mb-1">🍕</div>
                <div class="text-center">
                    <div class="font-bold">Today's</div>
                    <div>Special</div>
                </div>
            </div>
            
            <!-- New Arrivals -->
            <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-pink-200 to-pink-400 dark:from-pink-700 dark:to-pink-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('new')">
                <div class="text-lg mb-1">🆕</div>
                <div class="text-center">
                    <div class="font-bold">New</div>
                    <div>Arrivals</div>
                </div>
            </div>
            
            <!-- Chef's Pick -->
            <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-green-200 to-green-400 dark:from-green-700 dark:to-green-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('chef')">
                <div class="text-lg mb-1">👨‍🍳</div>
                <div class="text-center">
                    <div class="font-bold">Chef's</div>
                    <div>Pick</div>
                </div>
            </div>
            
            <!-- Discounts -->
            <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-blue-200 to-blue-400 dark:from-blue-700 dark:to-blue-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('discount')">
                <div class="text-lg mb-1">💰</div>
                <div class="text-center">
                    <div class="font-bold">Special</div>
                    <div>Offers</div>
                </div>
            </div>
            
            <!-- Reward System -->
            <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-emerald-200 to-emerald-400 dark:from-emerald-700 dark:to-emerald-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('rewards')">
                <div class="text-lg mb-1">🎁</div>
                <div class="text-center">
                    <div class="font-bold">Earn</div>
                    <div>Rewards</div>
                </div>
            </div>
            
            <!-- Kitchen Live -->
            <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-purple-200 to-purple-400 dark:from-purple-700 dark:to-purple-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="showStory('kitchen')">
                <div class="text-lg mb-1">📹</div>
                <div class="text-center">
                    <div class="font-bold">Kitchen</div>
                    <div>Live</div>
                </div>
            </div>
        </div>
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
    <div class="grid grid-cols-2 gap-4 mb-24" id="menuGrid">
        @foreach($menuItems as $item)
            <div class="food-card bg-white dark:bg-gray-800 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-200 overflow-hidden flex flex-col cursor-pointer transform hover:scale-105 border-2 border-transparent relative" 
                 data-category="{{ $item->category_id }}" 
                 data-name="{{ strtolower($item->name) }}"
                 data-item-id="{{ $item->id }}"
                 onclick="handleCardClick(event, {{ $item->id }}, '{{ $item->name }}', {{ $item->price }})">
                
                <div class="h-24 bg-gradient-to-br from-orange-200 to-orange-400 dark:from-gray-700 dark:to-gray-900 flex items-center justify-center">
                    <i class="fas fa-utensils text-2xl text-white opacity-80"></i>
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

<!-- Bottom Navigation Bar -->
<nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-2xl z-50 flex justify-around items-center py-1 px-2 max-w-md mx-auto w-full">
    <a href="#" class="flex flex-col items-center text-orange-500 dark:text-orange-300">
        <!-- Home Icon (modern) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6" />
        </svg>
        <span class="text-xs mt-0.5">Home</span>
    </a>
    <a href="#" onclick="openCart()" class="flex flex-col items-center text-gray-400 dark:text-gray-400 relative">
        <!-- Cart Icon (modern) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m12-8l2 8" />
        </svg>
        <span id="cartCount" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden" style="background-color: #ef4444 !important; color: white !important;">0</span>
        <span class="text-xs mt-0.5">Cart</span>
    </a>
    <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <!-- Orders Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span class="text-xs mt-0.5">Orders</span>
    </a>
    <a href="{{ route('wallet.index') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <!-- Wallet Icon -->
        <i class="fas fa-wallet text-xl"></i>
        <span class="text-xs mt-0.5">Wallet</span>
    </a>
    <a href="https://wa.me/" target="_blank" class="flex flex-col items-center text-green-500 dark:text-green-400">
        <!-- WhatsApp Icon (modern) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.72 13.06a6.5 6.5 0 10-2.72 2.72l3.85 1.1a1 1 0 001.26-1.26l-1.1-3.85z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 11a3.5 3.5 0 005 0" />
        </svg>
        <span class="text-xs mt-0.5">WhatsApp</span>
    </a>
    <a href="{{ Auth::check() ? route('profile.edit') : route('login') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <!-- Login/User Icon (modern) -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="8" r="4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1" />
        </svg>
        <span class="text-xs mt-0.5">{{ Auth::check() ? 'Profile' : 'Login' }}</span>
    </a>
</nav>

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
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let cartTotal = 0;
let currentMenuItems = [];
let selectedItems = new Set(); // Track selected items

// Initialize cart display on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing cart from localStorage:', cart);
    updateCartDisplay();
    updateMenuCartCount();
});

// Story functionality
function showStory(type) {
    // Get story from database via stories array
    const story = window.stories.find(s => s.type === type);
    
    if (!story) {
        console.error('Story not found:', type);
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
                    <i class="fas fa-utensils text-2xl text-white opacity-80"></i>
                </div>
                <div class="p-3 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white leading-tight">${item.name}</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">${item.category.name}</span>
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
    
    const existingItem = cart.find(item => item.id === itemId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: itemId,
            name: name,
            price: price,
            quantity: 1
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
    console.log('Calling showCartNotification with:', name); // Debug log
    showCartNotification(name);
}

function updateCartDisplay() {
    const cartCount = document.getElementById('cartCount');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    console.log('updateCartDisplay called - totalItems:', totalItems);
    console.log('cartCount element:', cartCount);
    
    if (cartCount) {
        cartCount.textContent = totalItems;
        cartCount.classList.toggle('hidden', totalItems === 0);
        console.log('Cart count updated to:', totalItems);
        console.log('Cart count element classes:', cartCount.className);
        console.log('Cart count element style:', cartCount.style.cssText);
    } else {
        console.error('cartCount element not found!');
    }
}

function openCart() {
    window.location.href = '/cart';
}

// Update cart count in side menu
function updateMenuCartCount() {
    const menuCartCount = document.getElementById('menuCartCount');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    if (menuCartCount) {
        menuCartCount.textContent = totalItems;
        menuCartCount.classList.toggle('hidden', totalItems === 0);
    }
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

// Manual PWA Install
document.addEventListener('DOMContentLoaded', function() {
    const manualPWAInstall = document.getElementById('manualPWAInstall');
    if (manualPWAInstall) {
        manualPWAInstall.addEventListener('click', function() {
            console.log('Manual PWA install button clicked');
            
            // Check if we have a deferred prompt
            if (window.deferredPrompt) {
                console.log('Triggering install prompt');
                window.deferredPrompt.prompt();
                window.deferredPrompt.userChoice.then((choiceResult) => {
                    console.log('User choice:', choiceResult.outcome);
                    window.deferredPrompt = null;
                });
            } else {
                console.log('No deferred prompt available');
                alert('PWA install prompt not available. Try refreshing the page or check browser compatibility.');
            }
        });
    }
});
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
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Menu</h2>
        <button id="closeMenu" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <!-- Menu Items -->
    <div class="p-4 space-y-4">
        <!-- Home -->
        <a href="/menu" class="flex items-center gap-3 p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition">
            <i class="fas fa-home text-lg"></i>
            <span class="font-medium">Home</span>
        </a>
        <!-- Categories -->
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Categories</h3>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-pizza-slice text-lg"></i>
                <span>Pizza</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-hamburger text-lg"></i>
                <span>Burgers</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-drumstick-bite text-lg"></i>
                <span>Chicken</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-fish text-lg"></i>
                <span>Seafood</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-ice-cream text-lg"></i>
                <span>Desserts</span>
            </a>
            <a href="#" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-coffee text-lg"></i>
                <span>Beverages</span>
            </a>
        </div>
        <!-- Quick Actions -->
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Quick Actions</h3>
            <a href="/cart" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-shopping-cart text-lg"></i>
                <span>My Cart</span>
                <span id="menuCartCount" class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 hidden" style="background-color: #ef4444 !important; color: white !important;">0</span>
            </a>
            <a href="{{ route('user.orders') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-receipt text-lg"></i>
                <span>My Orders</span>
            </a>
            <a href="{{ route('login') }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-user text-lg"></i>
                <span>Login</span>
            </a>
        </div>
        <!-- Contact -->
        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Contact</h3>
            <a href="https://wa.me/" target="_blank" class="flex items-center gap-3 p-3 rounded-lg text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                <i class="fab fa-whatsapp text-lg"></i>
                <span>WhatsApp Support</span>
            </a>
            <a href="tel:+234" class="flex items-center gap-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <i class="fas fa-phone text-lg"></i>
                <span>Call Us</span>
            </a>
        </div>
    </div>
</div>
