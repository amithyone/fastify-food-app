@extends('layouts.app')

@section('title', 'Cart - Abuja Eat')

@section('content')
<div class="container mx-auto px-2 py-4 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="/menu" class="text-gray-600 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Your Cart</h1>
        </div>
        <button id="darkModeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
            <i class="fas fa-sun"></i>
        </button>
    </div>

    <!-- Cart Items -->
    <div id="cartItems" class="space-y-3 mb-6">
        <!-- Cart items will be populated by JavaScript -->
    </div>

    <!-- Empty Cart State -->
    <div id="emptyCart" class="hidden text-center py-12">
        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle cx="9" cy="21" r="1" />
                <circle cx="20" cy="21" r="1" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m12-8l2 8" />
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Add some delicious items to get started!</p>
        <a href="/menu" class="inline-block bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition">
            Browse Menu
        </a>
    </div>

    <!-- Cart Summary -->
    <div id="cartSummary" class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
        <div class="flex justify-between items-center mb-4">
            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
            <span id="subtotal" class="font-semibold text-gray-900 dark:text-white">₦0.00</span>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
            <div class="flex justify-between items-center">
                <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                <span id="total" class="text-lg font-bold text-orange-500 dark:text-orange-400">₦0.00</span>
            </div>
        </div>
    </div>

    <!-- Checkout Button -->
    <button id="checkoutBtn" onclick="checkout()" class="w-full bg-orange-500 text-white py-4 rounded-lg font-semibold text-lg hover:bg-orange-600 transition mb-20">
        Proceed to Checkout
    </button>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50 flex justify-around items-center py-1 px-2 max-w-md mx-auto w-full">
        <a href="/menu" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
            <!-- Home Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0h6" />
            </svg>
            <span class="text-xs mt-0.5">Home</span>
        </a>
        <a href="/cart" class="flex flex-col items-center text-orange-500 dark:text-orange-300 relative">
            <!-- Cart Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1" />
                <circle cx="20" cy="21" r="1" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M1 1h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 8m12-8l2 8" />
            </svg>
            <span id="cartCountBottom" class="absolute -top-1 -right-1 bg-yellow-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
            <span class="text-xs mt-0.5">Cart</span>
        </a>
        <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
            <!-- Orders Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-xs mt-0.5">Orders</span>
        </a>
        <a href="https://wa.me/" target="_blank" class="flex flex-col items-center text-green-500 dark:text-green-400">
            <!-- WhatsApp Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.72 13.06a6.5 6.5 0 10-2.72 2.72l3.85 1.1a1 1 0 001.26-1.26l-1.1-3.85z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 11a3.5 3.5 0 005 0" />
            </svg>
            <span class="text-xs mt-0.5">WhatsApp</span>
        </a>
        <a href="{{ route('login') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
            <!-- Login Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1" />
            </svg>
            <span class="text-xs mt-0.5">Login</span>
        </a>
    </nav>

    <!-- Dark Mode Toggle Script -->
    <script>
        const darkModeToggle = document.getElementById('darkModeToggle');
        const html = document.documentElement;
        
        const currentTheme = localStorage.getItem('theme') || 'light';
        html.classList.toggle('dark', currentTheme === 'dark');
        updateToggleIcon();
        
        darkModeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            const theme = html.classList.contains('dark') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
            updateToggleIcon();
        });
        
        function updateToggleIcon() {
            const isDark = html.classList.contains('dark');
            darkModeToggle.innerHTML = isDark 
                ? '<i class="fas fa-moon text-yellow-400"></i>' 
                : '<i class="fas fa-sun text-gray-600"></i>';
        }
    </script>

    <!-- Cart Functionality -->
    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
        });

        function loadCart() {
            const cartItems = document.getElementById('cartItems');
            const emptyCart = document.getElementById('emptyCart');
            const cartSummary = document.getElementById('cartSummary');
            const checkoutBtn = document.getElementById('checkoutBtn');

            if (cart.length === 0) {
                cartItems.classList.add('hidden');
                emptyCart.classList.remove('hidden');
                cartSummary.classList.add('hidden');
                checkoutBtn.classList.add('hidden');
                updateCartCount();
                return;
            }

            cartItems.classList.remove('hidden');
            emptyCart.classList.add('hidden');
            cartSummary.classList.remove('hidden');
            checkoutBtn.classList.remove('hidden');

            cartItems.innerHTML = '';
            let subtotal = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                const itemHtml = `
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white">${item.name}</h3>
                                <p class="text-orange-500 dark:text-orange-400 font-bold">₦${item.price.toLocaleString()}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button onclick="updateQuantity(${index}, -1)" class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="text-gray-900 dark:text-white font-semibold w-8 text-center">${item.quantity}</span>
                                <button onclick="updateQuantity(${index}, 1)" class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white hover:bg-orange-600">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total: ₦${itemTotal.toLocaleString()}</span>
                            <button onclick="removeItem(${index})" class="text-red-500 hover:text-red-600 text-sm">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                `;
                cartItems.innerHTML += itemHtml;
            });

            const total = subtotal;

            document.getElementById('subtotal').textContent = `₦${subtotal.toLocaleString()}`;
            document.getElementById('total').textContent = `₦${total.toLocaleString()}`;
            updateCartCount();
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            notification.className = `fixed top-20 left-1/2 transform -translate-x-1/2 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3 transition-all duration-300 opacity-0 scale-95`;
            notification.innerHTML = `
                <div class="w-6 h-6 bg-white rounded-full flex items-center justify-center">
                    <i class="fas fa-${type === 'success' ? 'check' : 'times'} text-${type === 'success' ? 'green' : 'red'}-500 text-sm"></i>
                </div>
                <span class="font-semibold">${message}</span>
            `;
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('opacity-0', 'scale-95');
                notification.classList.add('opacity-100', 'scale-100');
            }, 100);
            
            // Add haptic feedback (vibration) if supported
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
            
            // Remove after 2 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 2000);
        }

        function updateQuantity(index, change) {
            const item = cart[index];
            const oldQuantity = item.quantity;
            
            cart[index].quantity += change;
            
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
                showNotification('Item removed from cart', 'success');
            } else {
                if (change > 0) {
                    showNotification('Quantity increased', 'success');
                } else {
                    showNotification('Quantity decreased', 'success');
                }
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
        }

        function removeItem(index) {
            const itemName = cart[index].name;
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            showNotification(`${itemName} removed from cart`, 'success');
            loadCart();
        }

        function updateCartCount() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const cartCount = document.getElementById('cartCountBottom');
            
            if (cartCount) {
                cartCount.textContent = totalItems;
                cartCount.classList.toggle('hidden', totalItems === 0);
            }
        }

        function checkout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            // Redirect to checkout page
            window.location.href = '/checkout';
        }
    </script>
</div>
@endsection