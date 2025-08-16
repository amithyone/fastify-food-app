@extends('layouts.app')

@section('title', 'My Wallet - Abuja Eat')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            @php
                // Determine the current restaurant context
                $currentRestaurant = null;
                
                // Check if we're on a restaurant-specific page
                if (isset($restaurant)) {
                    $currentRestaurant = $restaurant;
                } elseif (request()->routeIs('menu.index') && request()->segment(2)) {
                    // We're on a restaurant-specific menu page
                    $currentRestaurant = \App\Models\Restaurant::where('slug', request()->segment(2))->first();
                } elseif (session('qr_restaurant_id')) {
                    // We're in a QR code context
                    $currentRestaurant = \App\Models\Restaurant::find(session('qr_restaurant_id'));
                }
                
                // Determine menu URL
                if ($currentRestaurant) {
                    $menuUrl = route('menu.index', $currentRestaurant->slug);
                } else {
                    $menuUrl = route('menu.index');
                }
            @endphp
            <a href="{{ $menuUrl }}" class="text-gray-600 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Wallet</h1>
        </div>
        <button id="darkModeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
            <i class="fas fa-sun"></i>
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Email Verification Notice -->
    @if(!Auth::user()->hasVerifiedEmail())
        <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>Please verify your email address to access wallet features.</span>
                </div>
                <a href="{{ route('verification.notice') }}" class="text-yellow-800 hover:text-yellow-900 font-medium">
                    Verify Now
                </a>
            </div>
        </div>
    @endif

    <!-- Wallet Balance Card -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 mb-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-white">Wallet Balance</h2>
            <i class="fas fa-wallet text-2xl opacity-80 text-white"></i>
        </div>
        <div class="text-3xl font-bold mb-2 text-white">{{ $wallet->formatted_balance }}</div>
        <div class="text-orange-100 text-sm">{{ $wallet->points_display }}</div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <button onclick="showAddFundsModal()" class="bg-orange-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition">
            <i class="fas fa-plus mr-2"></i>Add Funds
        </button>
        <a href="{{ route('wallet.transactions') }}" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-lg font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition text-center">
            <i class="fas fa-history mr-2"></i>History
        </a>
    </div>

    <!-- Reward Info -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <h3 class="text-lg font-semibold text-blue-800 dark:text-white mb-2">🎁 Reward Points</h3>
        <div class="text-blue-700 dark:text-white text-sm space-y-1">
            <p>• Earn 1 point for every ₦100 spent</p>
            <p>• Only available with bank transfer payments</p>
            <p>• Points expire after 6 months</p>
            <p>• Use points for future discounts</p>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($transactions->take(5) as $transaction)
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $transaction->description }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                        @if($transaction->points_earned > 0)
                        <p class="text-xs text-green-600 dark:text-green-400">+{{ $transaction->points_earned }} points</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="font-semibold {{ $transaction->type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $transaction->formatted_amount }}
                        </p>
                        <span class="inline-block px-2 py-1 text-xs rounded-full {{ $transaction->type_badge }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-inbox text-2xl mb-2"></i>
                <p>No transactions yet</p>
            </div>
            @endforelse
        </div>
        @if($transactions->count() > 5)
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('wallet.transactions') }}" class="text-orange-500 hover:text-orange-600 text-sm font-medium">
                View all transactions →
            </a>
        </div>
        @endif
    </div>

    <!-- Recent Rewards -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 mb-20">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Rewards</h3>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($rewards->take(5) as $reward)
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Order #{{ $reward->order_id }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $reward->created_at->format('M d, Y H:i') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $reward->formatted_order_amount }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-green-600 dark:text-green-400">+{{ $reward->points_earned }} points</p>
                        <span class="inline-block px-2 py-1 text-xs rounded-full {{ $reward->status_badge }}">
                            {{ ucfirst($reward->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-gift text-2xl mb-2"></i>
                <p>No rewards yet</p>
                <p class="text-xs mt-1">Pay with bank transfer to earn points!</p>
            </div>
            @endforelse
        </div>
        @if($rewards->count() > 5)
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('wallet.rewards') }}" class="text-orange-500 hover:text-orange-600 text-sm font-medium">
                View all rewards →
            </a>
        </div>
        @endif
    </div>

    <!-- Add Funds Modal -->
    <div id="addFundsModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Add Funds</h2>
                <button onclick="closeAddFundsModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="addFundsForm" class="space-y-4">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (₦)</label>
                    <input type="number" id="amount" name="amount" min="100" step="100" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="Enter amount">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="transfer" class="mr-2 text-orange-500" checked>
                            <span class="text-gray-700 dark:text-gray-300">Bank Transfer</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="card" class="mr-2 text-orange-500">
                            <span class="text-gray-700 dark:text-gray-300">Card Payment</span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="w-full bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600 transition">
                    Add Funds
                </button>
            </form>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50 flex justify-around items-center py-1 px-2 max-w-md mx-auto w-full">
        @php
            // Determine the current restaurant context
            $currentRestaurant = null;
            
            // Check if we're on a restaurant-specific page
            if (isset($restaurant)) {
                $currentRestaurant = $restaurant;
            } elseif (request()->routeIs('menu.index') && request()->segment(2)) {
                // We're on a restaurant-specific menu page
                $currentRestaurant = \App\Models\Restaurant::where('slug', request()->segment(2))->first();
            } elseif (session('qr_restaurant_id')) {
                // We're in a QR code context
                $currentRestaurant = \App\Models\Restaurant::find(session('qr_restaurant_id'));
            }
            
            // Determine menu URL
            if ($currentRestaurant) {
                $menuUrl = route('menu.index', $currentRestaurant->slug);
            } else {
                $menuUrl = route('menu.index');
            }
        @endphp
        <a href="{{ $menuUrl }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
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
            <span class="text-xs mt-0.5">Cart</span>
        </a>
        <a href="{{ route('user.orders') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-xs mt-0.5">Orders</span>
        </a>
        <a href="{{ route('wallet.index') }}" class="flex flex-col items-center text-orange-500 dark:text-orange-300">
            <i class="fas fa-wallet text-xl"></i>
            <span class="text-xs mt-0.5">Wallet</span>
        </a>
        <a href="{{ Auth::check() ? route('profile.edit') : route('login') }}" class="flex flex-col items-center text-orange-500 dark:text-orange-300">
            <!-- Login/Profile Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1" />
            </svg>
            <span class="text-xs mt-0.5">{{ Auth::check() ? 'Profile' : 'Login' }}</span>
        </a>
    </nav>

    <script>
        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const themeIcon = document.getElementById('themeIcon');

        function setTheme(dark) {
            if (dark) {
                document.documentElement.classList.add('dark');
                darkModeToggle.innerHTML = '<i class="fas fa-moon text-yellow-400"></i>';
            } else {
                document.documentElement.classList.remove('dark');
                darkModeToggle.innerHTML = '<i class="fas fa-sun text-gray-600"></i>';
            }
        }

        const userPref = localStorage.getItem('theme');
        const systemPref = window.matchMedia('(prefers-color-scheme: dark)').matches;
        setTheme(userPref === 'dark' || (!userPref && systemPref));

        darkModeToggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            setTheme(isDark);
        });

        // Add funds modal
        function showAddFundsModal() {
            document.getElementById('addFundsModal').classList.remove('hidden');
        }

        function closeAddFundsModal() {
            document.getElementById('addFundsModal').classList.add('hidden');
        }

        // Add funds form
        document.getElementById('addFundsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const amount = formData.get('amount');
            const paymentMethod = formData.get('payment_method');

            fetch('{{ route("wallet.add-funds") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    amount: amount,
                    payment_method: paymentMethod
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add funds. Please try again.');
            });
        });

        // Close modal when clicking outside
        document.getElementById('addFundsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddFundsModal();
            }
        });
    </script>
</div>
@endsection 