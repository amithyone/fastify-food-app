@extends('layouts.app')

@section('title', 'Wallet Transactions - Abuja Eat')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-md bg-white dark:bg-gray-900 min-h-screen">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('wallet.index') }}" class="text-gray-600 dark:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Transaction History</h1>
        </div>
        <button id="darkModeToggle" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
            <i class="fas fa-sun"></i>
        </button>
    </div>

    <!-- Wallet Balance Card -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 mb-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-white">Current Balance</h2>
            <i class="fas fa-wallet text-2xl opacity-80 text-white"></i>
        </div>
        <div class="text-3xl font-bold mb-2 text-white">{{ $wallet->formatted_balance }}</div>
        <div class="text-orange-100 text-sm">{{ $wallet->points_display }}</div>
    </div>

    <!-- Transactions List -->
    <div class="space-y-4 mb-20">
        @forelse($transactions as $transaction)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $transaction->description }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                    @if($transaction->order)
                    <p class="text-xs text-gray-600 dark:text-gray-400">Order #{{ $transaction->order_id }}</p>
                    @endif
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
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                <i class="fas fa-inbox text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No transactions yet</h3>
            <p class="text-gray-600 dark:text-gray-400">Your transaction history will appear here</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
    <div class="mb-20">
        {{ $transactions->links() }}
    </div>
    @endif

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
        <a href="{{ route('login') }}" class="flex flex-col items-center text-gray-400 dark:text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1" />
            </svg>
            <span class="text-xs mt-0.5">Login</span>
        </a>
    </nav>

    <script>
        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');

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
    </script>
</div>
@endsection 