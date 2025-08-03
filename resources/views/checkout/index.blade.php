@extends('layouts.app')

@section('title', 'Checkout - Abuja Eat')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <a href="/cart" class="text-gray-600 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Checkout</h1>
        <button id="themeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
            <i id="themeIcon" class="fas fa-moon"></i>
        </button>
    </div>

    <!-- Order Type Selection -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Type</h3>
        <div class="space-y-3">
            <!-- Delivery Option -->
            <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" id="deliveryOption">
                <input type="radio" name="orderType" value="delivery" id="deliveryRadio" class="mr-3 text-orange-500 focus:ring-orange-500" checked>
                <div class="flex items-center flex-1">
                    <div class="flex-shrink-0">
                        <i class="fas fa-motorcycle text-orange-500 text-xl mr-3"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900 dark:text-white">Delivery</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Deliver to your address</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">₦500</div>
            </label>
            
            <!-- In Restaurant Option -->
            <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" id="restaurantOption">
                <input type="radio" name="orderType" value="restaurant" id="restaurantRadio" class="mr-3 text-orange-500 focus:ring-orange-500">
                <div class="flex items-center flex-1">
                    <div class="flex-shrink-0">
                        <i class="fas fa-utensils text-orange-500 text-xl mr-3"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900 dark:text-white">In Restaurant</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Dine in at the restaurant</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">₦0</div>
            </label>
        </div>
    </div>

    <form id="checkoutForm" class="space-y-6">
        <!-- Customer Information Section -->
        <div id="customerInfoSection" class="space-y-4 transition-all duration-300 ease-in-out">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Customer Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name *</label>
                    <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email (Optional)</label>
                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                </div>
            </div>
        </div>

        <!-- Restaurant Information Section -->
        <div id="restaurantInfoSection" class="space-y-4 transition-all duration-300 ease-in-out" style="display: none;">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Restaurant Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="tableNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Table Number *</label>
                    <input type="text" id="tableNumber" name="tableNumber" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white" placeholder="e.g., Table 5, A1, etc.">
                </div>
                
                <div>
                    <label for="restaurantNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Special Instructions (Optional)</label>
                    <textarea id="restaurantNotes" name="restaurantNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white" placeholder="Any special requests or instructions..."></textarea>
                </div>
            </div>
        </div>

        <!-- Delivery Address Section -->
        <div id="addressSection" class="space-y-4 transition-all duration-300 ease-in-out">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Delivery Address</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Street Address *</label>
                    <input type="text" id="address" name="address" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City *</label>
                        <input type="text" id="city" name="city" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">State *</label>
                        <input type="text" id="state" name="state" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
                
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Postal Code (Optional)</label>
                    <input type="text" id="postal_code" name="postal_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                </div>
                
                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Delivery Instructions (Optional)</label>
                    <textarea id="instructions" name="instructions" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white" placeholder="Any special delivery instructions..."></textarea>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h2>
            <div id="orderItems" class="space-y-3 mb-4">
                @if(empty($cartItems))
                    <p class="text-gray-500 dark:text-gray-400 text-center">No items in cart</p>
                @else
                    @foreach($cartItems as $restaurantGroup)
                        @foreach($restaurantGroup['items'] as $item)
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                                        <span class="text-orange-600 dark:text-orange-400 font-semibold">{{ $item['quantity'] }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">₦{{ number_format($item['price']) }}</div>
                                    </div>
                                </div>
                                <div class="text-gray-900 dark:text-white font-semibold">₦{{ number_format($item['total']) }}</div>
                            </div>
                        @endforeach
                    @endforeach
                @endif
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                    <span id="subtotal" class="text-gray-900 dark:text-white">₦{{ number_format($total) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Delivery Fee:</span>
                    <span id="deliveryFee" class="text-gray-900 dark:text-white">₦0</span>
                </div>
                <div class="flex justify-between text-lg font-semibold">
                    <span class="text-gray-900 dark:text-white">Total:</span>
                    <span id="total" class="text-orange-600 dark:text-orange-400">₦{{ number_format($total) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Method</h2>
            
            <!-- Wallet Balance Display -->
            <div id="walletBalance" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-4 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-green-800 dark:text-green-200">Wallet Balance</h3>
                        <p class="text-sm text-green-700 dark:text-green-300">Available for payment</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-green-800 dark:text-green-200" id="walletBalanceAmount">₦0</p>
                        <p class="text-xs text-green-600 dark:text-green-400" id="walletPoints">0 points</p>
                    </div>
                </div>
            </div>
            
            <div class="space-y-3">
                <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                    <input type="radio" name="payment_method" value="cash" class="mr-3 text-orange-500 focus:ring-orange-500" checked>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">Cash on Delivery</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pay when your order arrives</div>
                    </div>
                </label>
                
                <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800">
                    <input type="radio" name="payment_method" value="transfer" class="mr-3 text-orange-500 focus:ring-orange-500">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <div class="font-medium text-gray-900 dark:text-white">Bank Transfer</div>
                            <div class="flex items-center gap-1 bg-green-100 dark:bg-green-900/20 px-2 py-1 rounded-full">
                                <i class="fas fa-gift text-green-600 dark:text-green-400 text-xs"></i>
                                <span class="text-xs text-green-600 dark:text-green-400 font-semibold">EARN REWARDS</span>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pay via bank transfer and earn 1 point per ₦100</div>
                    </div>
                </label>
                
                <label id="walletPaymentOption" class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 hidden">
                    <input type="radio" name="payment_method" value="wallet" class="mr-3 text-orange-500 focus:ring-orange-500">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">Wallet Payment</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pay using your wallet balance</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start space-x-3">
            <input type="checkbox" id="terms" name="terms" required class="mt-1 text-orange-500 focus:ring-orange-500">
            <label for="terms" class="text-sm text-gray-700 dark:text-gray-300">
                I agree to the <a href="#" class="text-orange-500 hover:text-orange-600">Terms and Conditions</a> and <a href="#" class="text-orange-500 hover:text-orange-600">Privacy Policy</a>
            </label>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3 pt-4">
            <button type="submit" class="w-full bg-orange-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                Place Order
            </button>
            <a href="/cart" class="block w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-lg font-semibold text-center hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                Back to Cart
            </a>
        </div>
    </form>
</div>

<!-- Bottom Navigation -->
<nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50 flex justify-around items-center py-1 px-2 max-w-md mx-auto w-full">
    <a href="/menu" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6" />
        </svg>
        <span class="text-xs mt-0.5">Home</span>
    </a>
    <a href="/cart" class="flex flex-col items-center text-gray-400 dark:text-gray-400 relative">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m12-8l2 8" />
        </svg>
        <span id="cartCount" class="absolute -top-1 -right-1 bg-yellow-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
        <span class="text-xs mt-0.5">Cart</span>
    </a>
    <a href="/orders" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <span class="text-xs mt-0.5">Orders</span>
    </a>
    <a href="https://wa.me/" target="_blank" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <i class="fab fa-whatsapp text-xl"></i>
        <span class="text-xs mt-0.5">WhatsApp</span>
    </a>
    <a href="/phone/login" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <span class="text-xs mt-0.5">Login</span>
    </a>
</nav>

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

// Order Type Selection
document.addEventListener('DOMContentLoaded', function() {
    const deliveryRadio = document.getElementById('deliveryRadio');
    const restaurantRadio = document.getElementById('restaurantRadio');
    const deliveryOption = document.getElementById('deliveryOption');
    const restaurantOption = document.getElementById('restaurantOption');
    const customerInfoSection = document.getElementById('customerInfoSection');
    const restaurantInfoSection = document.getElementById('restaurantInfoSection');
    const addressSection = document.getElementById('addressSection');
    
    console.log('Radio elements:', { deliveryRadio, restaurantRadio });
    console.log('Section elements:', { customerInfoSection, restaurantInfoSection, addressSection });
    
    function handleOrderTypeChange() {
        const isRestaurant = restaurantRadio.checked;
        console.log('Order type changed, restaurant mode:', isRestaurant);
        
        if (isRestaurant) {
            // Restaurant mode - hide delivery sections, show restaurant section
            customerInfoSection.style.maxHeight = '0px';
            customerInfoSection.style.overflow = 'hidden';
            customerInfoSection.style.opacity = '0';
            customerInfoSection.style.marginBottom = '0px';
            
            addressSection.style.maxHeight = '0px';
            addressSection.style.overflow = 'hidden';
            addressSection.style.opacity = '0';
            addressSection.style.marginBottom = '0px';
            
            // Show restaurant info section
            restaurantInfoSection.style.display = 'block';
            restaurantInfoSection.style.maxHeight = '1000px';
            restaurantInfoSection.style.overflow = 'visible';
            restaurantInfoSection.style.opacity = '1';
            restaurantInfoSection.style.marginBottom = '24px';
            
            // Remove required attributes from delivery sections
            document.querySelectorAll('#customerInfoSection input, #addressSection input, #addressSection textarea').forEach(input => {
                input.removeAttribute('required');
            });
            
            // Add required attribute to table number
            document.getElementById('tableNumber').setAttribute('required', 'required');
            
            // Update delivery fee to 0
            updateDeliveryFee(0);
            
            // Update option styling
            restaurantOption.classList.add('bg-orange-50', 'dark:bg-orange-900/20', 'border-orange-500', 'dark:border-orange-400');
            deliveryOption.classList.remove('bg-orange-50', 'dark:bg-orange-900/20', 'border-orange-500', 'dark:border-orange-400');
            
            console.log('Restaurant mode - restaurant section shown, delivery sections hidden');
        } else {
            // Delivery mode - show delivery sections, hide restaurant section
            customerInfoSection.style.maxHeight = '1000px';
            customerInfoSection.style.overflow = 'visible';
            customerInfoSection.style.opacity = '1';
            customerInfoSection.style.marginBottom = '24px';
            
            addressSection.style.maxHeight = '1000px';
            addressSection.style.overflow = 'visible';
            addressSection.style.opacity = '1';
            addressSection.style.marginBottom = '24px';
            
            // Hide restaurant info section
            restaurantInfoSection.style.display = 'none';
            restaurantInfoSection.style.maxHeight = '0px';
            restaurantInfoSection.style.overflow = 'hidden';
            restaurantInfoSection.style.opacity = '0';
            restaurantInfoSection.style.marginBottom = '0px';
            
            // Add required attributes back to delivery sections
            document.querySelectorAll('#customerInfoSection input[type="text"], #customerInfoSection input[type="tel"], #addressSection input[type="text"]').forEach(input => {
                if (input.id !== 'email' && input.id !== 'postal_code' && input.id !== 'instructions') {
                    input.setAttribute('required', 'required');
                }
            });
            
            // Remove required attribute from table number
            document.getElementById('tableNumber').removeAttribute('required');
            
            // Update delivery fee back to normal
            updateDeliveryFee(500);
            
            // Update option styling
            deliveryOption.classList.add('bg-orange-50', 'dark:bg-orange-900/20', 'border-orange-500', 'dark:border-orange-400');
            restaurantOption.classList.remove('bg-orange-50', 'dark:bg-orange-900/20', 'border-orange-500', 'dark:border-orange-400');
            
            console.log('Delivery mode - delivery sections shown, restaurant section hidden');
        }
    }
    
    // Add event listeners
    if (deliveryRadio && restaurantRadio) {
        deliveryRadio.addEventListener('change', handleOrderTypeChange);
        restaurantRadio.addEventListener('change', handleOrderTypeChange);
        
        // Initialize with delivery mode
        handleOrderTypeChange();
    } else {
        console.error('Radio buttons not found');
    }
});

// Load cart data from server
function loadCart() {
    // Cart data is now loaded from server and displayed in the Blade template
    // No need to fetch from localStorage
    updateOrderSummary();
}

function updateOrderSummary() {
    // Get the current subtotal from the server-side data
    const subtotalElement = document.getElementById('subtotal');
    const subtotalText = subtotalElement.textContent;
    const subtotal = parseFloat(subtotalText.replace('₦', '').replace(',', '')) || 0;
    
    const restaurantRadio = document.getElementById('restaurantRadio');
    const deliveryFee = restaurantRadio && restaurantRadio.checked ? 0 : 500;
    const total = subtotal + deliveryFee;
    
    document.getElementById('deliveryFee').textContent = `₦${deliveryFee.toLocaleString()}`;
    document.getElementById('total').textContent = `₦${total.toLocaleString()}`;
}

function updateDeliveryFee(fee) {
    document.getElementById('deliveryFee').textContent = `₦${fee.toLocaleString()}`;
    updateOrderSummary();
}

// Update cart count from server
function updateCartCount() {
    fetch('/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.getElementById('cartCount');
            if (cartCount) {
                cartCount.textContent = data.count;
                cartCount.classList.toggle('hidden', data.count === 0);
            }
        });
}

// Form submission
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const restaurantRadio = document.getElementById('restaurantRadio');
    
    // Use server-side cart data instead of parsing HTML
    const cartItems = @json($cartItems);
    const orderItems = [];
    
    // Flatten the cart items structure
    cartItems.forEach(restaurantGroup => {
        restaurantGroup.items.forEach(item => {
            orderItems.push({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.quantity
            });
        });
    });
    
    // Calculate totals directly to ensure accuracy
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('₦', '').replace(',', '')) || 0;
    const deliveryFee = restaurantRadio && restaurantRadio.checked ? 0 : 500;
    const calculatedTotal = subtotal + deliveryFee;
    
    const orderData = {
        items: orderItems,
        customer_info: restaurantRadio.checked ? {
            in_restaurant: true,
            table_number: formData.get('tableNumber'),
            restaurant_notes: formData.get('restaurantNotes')
        } : {
            name: formData.get('name'),
            phone: formData.get('phone'),
            email: formData.get('email'),
            address: formData.get('address'),
            city: formData.get('city'),
            state: formData.get('state'),
            postal_code: formData.get('postal_code'),
            instructions: formData.get('instructions'),
            in_restaurant: false
        },
        payment_method: formData.get('payment_method'),
        subtotal: subtotal,
        delivery_fee: deliveryFee,
        total: calculatedTotal
    };
    
    // Debug: Log the order data
    console.log('Sending order data:', orderData);
    
    // Send order to server
    fetch('/orders', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear cart after successful order
            fetch('/cart/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            // Redirect to order confirmation
            window.location.href = `/orders/${data.order_id}`;
        } else {
            alert('Error placing order: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error placing order. Please try again.');
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    updateCartCount();
    loadWalletBalance();
    
    // Pre-fill table number from QR code if available
    const qrTableNumber = '{{ session("qr_table_number") }}';
    if (qrTableNumber) {
        document.getElementById('tableNumber').value = qrTableNumber;
        console.log('Pre-filled table number from QR code:', qrTableNumber);
        
        // Add visual indicator that table number was pre-filled
        const tableNumberField = document.getElementById('tableNumber');
        tableNumberField.style.backgroundColor = '#fef3c7';
        tableNumberField.style.borderColor = '#f59e0b';
        tableNumberField.style.color = '#000000';
        tableNumberField.placeholder = 'Table number from QR code';
        
        // Add a small note below the field
        const tableNumberContainer = tableNumberField.parentElement;
        const note = document.createElement('p');
        note.className = 'text-xs text-orange-600 dark:text-orange-400 mt-1';
        note.innerHTML = '<i class="fas fa-qrcode mr-1"></i>Table number from QR code';
        tableNumberContainer.appendChild(note);
    }
});

// Load wallet balance
function loadWalletBalance() {
    console.log('Loading wallet balance...');
    fetch('/wallet/info', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Wallet response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Wallet data:', data);
        if (data.balance > 0) {
            // Show wallet balance
            document.getElementById('walletBalance').classList.remove('hidden');
            document.getElementById('walletBalanceAmount').textContent = data.formatted_balance;
            document.getElementById('walletPoints').textContent = data.points_display;
            
            // Show wallet payment option
            document.getElementById('walletPaymentOption').classList.remove('hidden');
        } else {
            console.log('No wallet balance or user not logged in');
            // For testing, show wallet option even with 0 balance
            document.getElementById('walletPaymentOption').classList.remove('hidden');
            document.getElementById('walletBalance').classList.remove('hidden');
            document.getElementById('walletBalanceAmount').textContent = '₦0';
            document.getElementById('walletPoints').textContent = '0 points';
        }
    })
    .catch(error => {
        console.log('Error loading wallet:', error);
        // For testing, show wallet option even if there's an error
        document.getElementById('walletPaymentOption').classList.remove('hidden');
        document.getElementById('walletBalance').classList.remove('hidden');
        document.getElementById('walletBalanceAmount').textContent = '₦0';
        document.getElementById('walletPoints').textContent = '0 points';
    });
}

// Handle payment method change
document.addEventListener('change', function(e) {
    if (e.target.name === 'payment_method') {
        const total = parseFloat(document.getElementById('total').textContent.replace('₦', '').replace(',', ''));
        const walletBalance = parseFloat(document.getElementById('walletBalanceAmount').textContent.replace('₦', '').replace(',', '')) || 0;
        
        if (e.target.value === 'wallet' && walletBalance < total) {
            alert('Insufficient wallet balance. Please add more funds or choose another payment method.');
            e.target.checked = false;
            document.querySelector('input[name="payment_method"][value="cash"]').checked = true;
        }
    }
});
</script>
@endsection 