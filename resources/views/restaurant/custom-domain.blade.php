@extends('layouts.app')

@section('title', 'Custom Domain - ' . $restaurant->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-8">
                <div class="flex items-center space-x-4">
                    @if($restaurant->logo_url)
                        <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-16 h-16 rounded-lg object-contain bg-gray-100 dark:bg-gray-700">
                    @else
                        <img src="{{ \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $restaurant->name }}" class="w-16 h-16 rounded-lg object-contain bg-gray-100 dark:bg-gray-700">
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $restaurant->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Custom Domain Management</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Custom Domain Form -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Set Custom Domain</h3>
                <form action="{{ route('restaurant.custom-domain.update', $restaurant->slug) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="custom_domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Custom Domain
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                https://
                            </span>
                            <input type="text" id="custom_domain" name="custom_domain" 
                                value="{{ $restaurant->custom_domain }}"
                                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-r-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                placeholder="your-restaurant.com">
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Enter your domain without https:// (e.g., your-restaurant.com)
                        </p>
                    </div>
                    
                    <button type="submit" 
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>
                        Update Custom Domain
                    </button>
                </form>
            </div>

            <!-- Domain Status & Instructions -->
            <div class="space-y-6">
                <!-- Domain Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Domain Status</h3>
                    
                    @if($restaurant->custom_domain)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Domain:</span>
                                <span class="text-sm text-gray-900 dark:text-white font-mono">{{ $restaurant->custom_domain }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($restaurant->custom_domain_status === 'verified') bg-green-100 text-green-800
                                    @elseif($restaurant->custom_domain_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($restaurant->custom_domain_status) }}
                                </span>
                            </div>
                            
                            @if($restaurant->custom_domain_verified_at)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Verified:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $restaurant->custom_domain_verified_at->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
                            
                            @if(!$restaurant->custom_domain_verified)
                                <form action="{{ route('restaurant.custom-domain.verify', $restaurant->slug) }}" method="POST" class="pt-3">
                                    @csrf
                                    <button type="submit" 
                                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                        <i class="fas fa-check mr-2"></i>
                                        Verify Domain
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No custom domain set yet.</p>
                    @endif
                </div>

                <!-- DNS Instructions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">DNS Configuration</h3>
                    <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <p>To use your custom domain, configure these DNS records:</p>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                            <p class="font-mono text-xs">
                                <strong>Type:</strong> CNAME<br>
                                <strong>Name:</strong> @ or your domain<br>
                                <strong>Value:</strong> {{ config('app.domain') }}
                            </p>
                        </div>
                        
                        <p class="text-xs">Allow up to 24 hours for DNS changes to propagate.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 