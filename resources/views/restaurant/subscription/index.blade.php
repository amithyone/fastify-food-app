@extends('layouts.app')

@section('title', 'Subscription Management - ' . $restaurant->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Subscription Management</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your restaurant's subscription plan and features</p>
                </div>
                <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Current Subscription Status -->
        @if($subscription)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Current Subscription</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Plan Type</h4>
                            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white capitalize">{{ $subscription->plan_type }} Restaurant</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</h4>
                            <div class="mt-1 flex items-center">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    bg-{{ $subscription->status_color }}-100 text-{{ $subscription->status_color }}-800 dark:bg-{{ $subscription->status_color }}-900 dark:text-{{ $subscription->status_color }}-200">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                                @if($subscription->isTrial())
                                    <span class="ml-2 text-sm text-blue-600 dark:text-blue-400">
                                        ({{ $subscription->days_remaining }} days remaining)
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Monthly Fee</h4>
                            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $subscription->formatted_price }}</p>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Current Features</h4>
                        @if($subscription->isTrial())
                            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    <i class="fas fa-star mr-2"></i>
                                    <strong>Trial Benefits:</strong> You have access to ALL premium features during your trial period!
                                </p>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    @if($subscription->isTrial())
                                        Unlimited menu items
                                    @else
                                        {{ $subscription->unlimited_menu_items ? 'Unlimited' : $subscription->menu_item_limit }} menu items
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center">
                                <i class="{{ ($subscription->isTrial() || $subscription->custom_domain_enabled) ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500' }} mr-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Custom domain</span>
                            </div>
                            <div class="flex items-center">
                                <i class="{{ ($subscription->isTrial() || $subscription->video_packages_enabled) ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500' }} mr-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Video packages</span>
                            </div>
                            <div class="flex items-center">
                                <i class="{{ ($subscription->isTrial() || $subscription->social_media_promotion_enabled) ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500' }} mr-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Social media promotion</span>
                            </div>
                            <div class="flex items-center">
                                <i class="{{ ($subscription->isTrial() || $subscription->priority_support) ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500' }} mr-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Priority support</span>
                            </div>
                            <div class="flex items-center">
                                <i class="{{ ($subscription->isTrial() || $subscription->advanced_analytics) ? 'fas fa-check text-green-500' : 'fas fa-times text-red-500' }} mr-2"></i>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Advanced analytics</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex space-x-3">
                        @if($subscription->isExpired())
                            <form action="{{ route('restaurant.subscription.renew', $restaurant->slug) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                    <i class="fas fa-sync mr-2"></i>
                                    Renew Subscription
                                </button>
                            </form>
                        @endif
                        
                        @if($subscription->status !== 'cancelled')
                            <form action="{{ route('restaurant.subscription.cancel', $restaurant->slug) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel Subscription
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg mb-8">
                <div class="p-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">No Active Subscription</h3>
                            <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                You don't have an active subscription. Please choose a plan below to continue using our services.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Available Plans -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Available Plans</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($plans as $plan)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6 {{ $plan->slug === 'premium' ? 'ring-2 ring-orange-500' : '' }}">
                            <div class="text-center">
                                <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ $plan->name }}</h4>
                                <div class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $plan->formatted_price }}<span class="text-sm font-normal text-gray-500">/month</span></div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">{{ $plan->description }}</p>
                            </div>

                            <!-- Features -->
                            <ul class="space-y-3 mb-6">
                                @foreach(array_slice($plan->features, 0, 6) as $feature)
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                                    </li>
                                @endforeach
                                @if(count($plan->features) > 6)
                                    <li class="text-sm text-gray-500 dark:text-gray-400">+{{ count($plan->features) - 6 }} more features</li>
                                @endif
                            </ul>

                            <!-- Limitations -->
                            @if($plan->limitations)
                                <div class="mb-6">
                                    <h5 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Limitations:</h5>
                                    <ul class="space-y-1">
                                        @foreach($plan->limitations as $limitation)
                                            <li class="flex items-start">
                                                <i class="fas fa-times text-red-500 mr-2 mt-0.5 text-xs"></i>
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $limitation }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Action Button -->
                            <form action="{{ route('restaurant.subscription.upgrade', $restaurant->slug) }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan_type" value="{{ $plan->slug }}">
                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white 
                                               {{ $plan->slug === 'premium' ? 'bg-orange-600 hover:bg-orange-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                                    @if($subscription && $subscription->plan_type === $plan->slug)
                                        <i class="fas fa-check mr-2"></i>
                                        Current Plan
                                    @else
                                        <i class="fas fa-arrow-up mr-2"></i>
                                        {{ $subscription ? 'Upgrade' : 'Choose' }} Plan
                                    @endif
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
