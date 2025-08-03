@extends('layouts.app')
@section('title', $restaurant->name . ' - Menu')

@section('content')
<!-- Fixed/Sticky Top Bar with Restaurant Branding -->
<div class="fixed top-0 left-0 right-0 z-50 border-b border-gray-200 dark:border-gray-700 py-2 shadow-lg max-w-md mx-auto w-full mt-15" 
     style="background: linear-gradient(135deg, {{ $restaurant->theme_color }} 0%, {{ $restaurant->secondary_color }} 100%);">
    <div class="flex items-center gap-2 px-4">
        <!-- Back Button -->
        <button onclick="history.back()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 text-white transition hover:bg-white/30">
            <i class="fas fa-arrow-left"></i>
        </button>
        
        <!-- Restaurant Logo/Name -->
        <div class="flex items-center flex-1">
            @if($restaurant->logo)
                <img src="{{ $restaurant->logo_url ?? \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $restaurant->name }}" class="w-8 h-8 rounded-full mr-2">
            @else
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-2">
                    <i class="fas fa-utensils text-white text-sm"></i>
                </div>
            @endif
            <div>
                <h1 class="text-white font-semibold text-sm">{{ $restaurant->name }}</h1>
                <p class="text-white/80 text-xs">Digital Menu</p>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div class="flex-1 relative">
            <input type="text" id="searchInput" placeholder="Search menu..." 
                   class="w-full px-4 py-2 pl-10 border border-white/20 rounded-full focus:outline-none focus:ring-2 focus:ring-white/50 bg-white/10 text-white placeholder-white/70 text-sm">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/70"></i>
        </div>
        
        <!-- Theme Toggle Button -->
        <button id="themeToggle" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/20 text-white transition hover:bg-white/30">
            <i id="themeIcon" class="fas fa-moon"></i>
        </button>
    </div>
</div>

<div class="w-full min-h-screen" style="background: linear-gradient(135deg, {{ $restaurant->theme_color }}10 0%, {{ $restaurant->secondary_color }}10 100%);">
    <div class="max-w-md mx-auto px-4 py-4">
        
        <!-- Restaurant Banner -->
        @if($restaurant->banner_image)
            <div class="mb-6 rounded-xl overflow-hidden shadow-lg" style="margin-top: 60px;">
                <img src="{{ $restaurant->banner_url ?? \App\Helpers\PWAHelper::getPlaceholderImage('rectangle') }}" alt="{{ $restaurant->name }}" class="w-full h-32 object-cover">
            </div>
        @endif

        <!-- Restaurant Info -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg" style="margin-top: {{ $restaurant->banner_image ? '0' : '60px' }};">
            <div class="flex items-start space-x-4">
                @if($restaurant->logo)
                    <img src="{{ $restaurant->logo_url ?? \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $restaurant->name }}" class="w-16 h-16 rounded-lg object-cover">
                @else
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-utensils text-white text-2xl"></i>
                    </div>
                @endif
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $restaurant->name }}</h2>
                    @if($restaurant->description)
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">{{ $restaurant->description }}</p>
                    @endif
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span>{{ $restaurant->city }}, {{ $restaurant->state }}</span>
                        </div>
                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-phone mr-1"></i>
                            <span>{{ $restaurant->whatsapp_number }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stories Section -->
        @if($stories->count() > 0)
            <div class="mb-6">
                <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar w-full whitespace-nowrap">
                    @foreach($stories as $story)
                        <div class="flex-shrink-0 w-20 h-28 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white shadow cursor-pointer hover:scale-105 transition-transform"
                             style="background: linear-gradient(135deg, {{ $restaurant->theme_color }} 0%, {{ $restaurant->secondary_color }} 100%);"
                             onclick="showStory('{{ $story->id }}')">
                            <div class="text-lg mb-1">{{ $story->emoji ?? '🍽️' }}</div>
                            <div class="text-center">
                                <div class="font-bold">{{ $story->title }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Categories -->
        <div class="mb-6">
            <div class="flex gap-2 overflow-x-auto pb-2 hide-scrollbar">
                <button class="category-btn active px-4 py-2 rounded-full text-sm font-medium transition-all whitespace-nowrap"
                        data-category="all" 
                        style="background: {{ $restaurant->theme_color }}; color: white;">
                    All Items
                </button>
                @foreach($categories as $category)
                    <button class="category-btn px-4 py-2 rounded-full text-sm font-medium transition-all whitespace-nowrap bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600"
                            data-category="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Menu Items -->
        <div id="menuItems" class="space-y-4">
            @foreach($menuItems as $item)
                <div class="menu-item bg-white dark:bg-gray-800 rounded-xl p-4 shadow-lg border border-gray-100 dark:border-gray-700"
                     data-category="{{ $item->category_id }}">
                    <div class="flex space-x-4">
                        <img src="{{ $item->image ? Storage::url($item->image) : \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $item->name }}" class="w-20 h-20 rounded-lg object-cover">
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->name }}</h3>
                                <span class="font-bold text-lg" style="color: {{ $restaurant->theme_color }};">
                                    {{ $restaurant->currency }}{{ number_format($item->price / 100, 2) }}
                                </span>
                            </div>
                            @if($item->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">{{ $item->description }}</p>
                            @endif
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    @if($item->is_vegetarian)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Vegetarian</span>
                                    @endif
                                    @if($item->is_spicy)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Spicy</span>
                                    @endif
                                </div>
                                <button onclick="addToCart({{ $item->id }}, '{{ $item->name }}', {{ $item->price }})"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
                                        style="background: {{ $restaurant->theme_color }}; color: white; hover:opacity-80;">
                                    <i class="fas fa-plus mr-1"></i>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        @if($menuItems->count() === 0)
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-utensils text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Menu Items Yet</h3>
                <p class="text-gray-600 dark:text-gray-400">Menu items will appear here once they're added.</p>
            </div>
        @endif
    </div>
</div>

<!-- Cart Floating Button -->
<div id="cartButton" class="fixed bottom-6 right-6 z-40 hidden">
    <button onclick="showCart()" 
            class="w-16 h-16 rounded-full shadow-lg flex items-center justify-center text-white font-bold transition-all transform hover:scale-110"
            style="background: {{ $restaurant->theme_color }};">
        <i class="fas fa-shopping-cart text-xl"></i>
        <span id="cartCount" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">0</span>
    </button>
</div>

<!-- Cart Modal -->
<div id="cartModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-end justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-t-xl w-full max-w-md max-h-[80vh] overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Your Order</h3>
                    <button onclick="hideCart()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div id="cartItems" class="p-4 max-h-96 overflow-y-auto">
                <!-- Cart items will be populated here -->
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold text-gray-900 dark:text-white">Total:</span>
                    <span id="cartTotal" class="font-bold text-lg" style="color: {{ $restaurant->theme_color }};">{{ $restaurant->currency }}0.00</span>
                </div>
                <button onclick="proceedToCheckout()" 
                        class="w-full py-3 rounded-lg font-semibold text-white transition-all"
                        style="background: {{ $restaurant->theme_color }};">
                    Proceed to Checkout
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Cart functionality

function addToCart(itemId, name, price) {
    console.log('addToCart called with:', itemId, name, price);
    
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
    // Fetch cart data from server
    fetch('/cart')
        .then(response => response.text())
        .then(html => {
            // Create a temporary div to parse the HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Extract cart items from the response
            const cartItemsContainer = tempDiv.querySelector('#cartItems');
            const cartTotalElement = tempDiv.querySelector('#cartTotal');
            const cartCountElement = document.getElementById('cartCount');
            
            if (cartItemsContainer && cartTotalElement) {
                document.getElementById('cartItems').innerHTML = cartItemsContainer.innerHTML;
                document.getElementById('cartTotal').innerHTML = cartTotalElement.innerHTML;
                
                // Update cart count
                if (cartCountElement) {
                    const totalItems = cartItemsContainer.children.length;
                    cartCountElement.textContent = totalItems;
                    cartCountElement.classList.toggle('hidden', totalItems === 0);
                }
                
                // Show/hide cart button based on items
                if (totalItems > 0) {
                    showCartButton();
                } else {
                    hideCartButton();
                }
            }
        })
        .catch(error => {
            console.error('Error fetching cart data:', error);
        });
}

function updateQuantity(itemId, change) {
    // Send request to server to update quantity
    fetch('/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            menu_item_id: itemId,
            quantity: change
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Cart updated successfully');
            updateCartDisplay();
        } else {
            console.error('Failed to update cart:', data.message);
        }
    })
    .catch(error => {
        console.error('Error updating cart:', error);
    });
}

function showCartButton() {
    document.getElementById('cartButton').classList.remove('hidden');
}

function hideCartButton() {
    document.getElementById('cartButton').classList.add('hidden');
}

function showCart() {
    document.getElementById('cartModal').classList.remove('hidden');
}

function hideCart() {
    document.getElementById('cartModal').classList.add('hidden');
}

function proceedToCheckout() {
    // Redirect to checkout - cart data is already in session
    window.location.href = '{{ route("checkout.index") }}';
}

// Category filtering
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const category = this.dataset.category;
        
        // Update active button
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        this.style.background = '{{ $restaurant->theme_color }}';
        this.style.color = 'white';
        
        // Filter items
        document.querySelectorAll('.menu-item').forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.menu-item').forEach(item => {
        const name = item.querySelector('h3').textContent.toLowerCase();
        const description = item.querySelector('p')?.textContent.toLowerCase() || '';
        
        if (name.includes(query) || description.includes(query)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Theme toggle
document.getElementById('themeToggle').addEventListener('click', function() {
    document.documentElement.classList.toggle('dark');
    const icon = document.getElementById('themeIcon');
    if (document.documentElement.classList.contains('dark')) {
        icon.className = 'fas fa-sun';
    } else {
        icon.className = 'fas fa-moon';
    }
});
</script>
@endsection 