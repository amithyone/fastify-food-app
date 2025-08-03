@extends('layouts.app')

@section('title', 'Delivery Settings - ' . $restaurant->name)

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Delivery Settings</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Configure delivery methods and menu item availability</p>
                </div>
                <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- General Delivery Settings -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">General Settings</h2>
                    
                    <form action="{{ route('restaurant.delivery-settings.update', $restaurant->slug) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Delivery Mode -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Delivery Mode
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" name="delivery_mode" value="flexible" 
                                           {{ $deliverySetting->delivery_mode === 'flexible' ? 'checked' : '' }}
                                           class="mr-3 text-orange-500 focus:ring-orange-500">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Flexible Mode</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Choose which delivery methods are available for each menu item individually
                                        </div>
                                    </div>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="delivery_mode" value="fixed" 
                                           {{ $deliverySetting->delivery_mode === 'fixed' ? 'checked' : '' }}
                                           class="mr-3 text-orange-500 focus:ring-orange-500">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Fixed Mode</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Apply the same delivery settings to all menu items
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Delivery Methods -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Available Delivery Methods
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="delivery_enabled" value="1" 
                                           {{ $deliverySetting->delivery_enabled ? 'checked' : '' }}
                                           class="mr-3 text-orange-500 focus:ring-orange-500">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Delivery</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Deliver orders to customer addresses
                                        </div>
                                    </div>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="pickup_enabled" value="1" 
                                           {{ $deliverySetting->pickup_enabled ? 'checked' : '' }}
                                           class="mr-3 text-orange-500 focus:ring-orange-500">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Pickup</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Customers collect orders from your restaurant
                                        </div>
                                    </div>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="in_restaurant_enabled" value="1" 
                                           {{ $deliverySetting->in_restaurant_enabled ? 'checked' : '' }}
                                           class="mr-3 text-orange-500 focus:ring-orange-500">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">In Restaurant</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Dine-in orders at your restaurant
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Delivery Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="delivery_fee" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Delivery Fee (₦)
                                </label>
                                <input type="number" id="delivery_fee" name="delivery_fee" 
                                       value="{{ $deliverySetting->delivery_fee }}" step="0.01" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label for="minimum_delivery_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Minimum Delivery Amount (₦)
                                </label>
                                <input type="number" id="minimum_delivery_amount" name="minimum_delivery_amount" 
                                       value="{{ $deliverySetting->minimum_delivery_amount }}" step="0.01" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label for="delivery_time_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Delivery Time (minutes)
                                </label>
                                <input type="number" id="delivery_time_minutes" name="delivery_time_minutes" 
                                       value="{{ $deliverySetting->delivery_time_minutes }}" min="10"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label for="pickup_time_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Pickup Time (minutes)
                                </label>
                                <input type="number" id="pickup_time_minutes" name="pickup_time_minutes" 
                                       value="{{ $deliverySetting->pickup_time_minutes }}" min="5"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="delivery_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Delivery Notes
                                    </label>
                                    <textarea id="delivery_notes" name="delivery_notes" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                                              placeholder="Special instructions for delivery orders...">{{ $deliverySetting->delivery_notes }}</textarea>
                                </div>
                                <div>
                                    <label for="pickup_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Pickup Notes
                                    </label>
                                    <textarea id="pickup_notes" name="pickup_notes" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                                              placeholder="Special instructions for pickup orders...">{{ $deliverySetting->pickup_notes }}</textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" 
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                            Save General Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Menu Item Delivery Methods (Flexible Mode Only) -->
            @if($deliverySetting->isFlexibleMode())
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Menu Item Settings</h2>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Configure which delivery methods are available for each menu item. 
                            You can also set additional fees for specific delivery methods.
                        </p>
                    </div>

                    <div id="menuItemsContainer" class="space-y-4">
                        @foreach($menuItems as $menuItem)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4" data-menu-item-id="{{ $menuItem->id }}">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $menuItem->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $menuItem->formatted_price }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                        {{ $menuItem->category->name ?? 'Uncategorized' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                @foreach(['delivery', 'pickup', 'in_restaurant'] as $method)
                                @php
                                    $methodRecord = $menuItem->deliveryMethods->where('delivery_method', $method)->first();
                                    $isEnabled = $methodRecord ? $methodRecord->enabled : false;
                                    $additionalFee = $methodRecord ? $methodRecord->additional_fee : 0;
                                @endphp
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   name="menu_items[{{ $menuItem->id }}][delivery_methods][{{ $method }}][enabled]" 
                                                   value="1" 
                                                   {{ $isEnabled ? 'checked' : '' }}
                                                   class="mr-2 text-orange-500 focus:ring-orange-500 menu-item-method-checkbox">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ ucfirst($method) }}
                                            </span>
                                        </label>
                                    </div>
                                    <div class="space-y-2">
                                        <input type="number" 
                                               name="menu_items[{{ $menuItem->id }}][delivery_methods][{{ $method }}][additional_fee]" 
                                               value="{{ $additionalFee }}" 
                                               step="0.01" min="0" 
                                               placeholder="Additional fee"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                        <input type="hidden" 
                                               name="menu_items[{{ $menuItem->id }}][delivery_methods][{{ $method }}][method]" 
                                               value="{{ $method }}">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" id="saveMenuItemSettings" 
                            class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        Save Menu Item Settings
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Save menu item delivery methods
    document.getElementById('saveMenuItemSettings').addEventListener('click', function() {
        const menuItems = [];
        
        document.querySelectorAll('[data-menu-item-id]').forEach(container => {
            const menuItemId = container.dataset.menuItemId;
            const deliveryMethods = [];
            
            container.querySelectorAll('.menu-item-method-checkbox').forEach(checkbox => {
                const method = checkbox.name.match(/\[([^\]]+)\]$/)[1];
                const additionalFeeInput = checkbox.closest('.border').querySelector('input[type="number"]');
                
                deliveryMethods.push({
                    method: method,
                    enabled: checkbox.checked,
                    additional_fee: parseFloat(additionalFeeInput.value) || 0
                });
            });
            
            menuItems.push({
                id: menuItemId,
                delivery_methods: deliveryMethods
            });
        });
        
        fetch('{{ route("restaurant.delivery-settings.menu-items", $restaurant->slug) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ menu_items: menuItems })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const successDiv = document.createElement('div');
                successDiv.className = 'mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4';
                successDiv.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                ${data.message}
                            </p>
                        </div>
                    </div>
                `;
                
                document.querySelector('.max-w-7xl').insertBefore(successDiv, document.querySelector('.grid'));
                
                // Remove success message after 3 seconds
                setTimeout(() => {
                    successDiv.remove();
                }, 3000);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving menu item settings');
        });
    });
});
</script>
@endpush
@endsection 