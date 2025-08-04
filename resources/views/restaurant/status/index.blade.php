@extends('layouts.app')

@section('title', 'Restaurant Status - ' . $restaurant->name)

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Restaurant Status</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $restaurant->name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Current Status Display -->
                        <div class="text-center">
                            @php
                                $statusDisplay = $restaurant->status_display;
                            @endphp
                            <div class="text-sm text-gray-500 dark:text-gray-400">Current Status</div>
                            <div class="flex items-center mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if($statusDisplay['status'] === 'open') 
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else 
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    <i class="{{ $statusDisplay['icon'] }} mr-2"></i>
                                    {{ $statusDisplay['text'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                    
                    <!-- Toggle Status Button -->
                    <div class="mb-6">
                        <button id="toggleStatusBtn" 
                                class="w-full py-3 px-4 rounded-lg font-medium transition-colors duration-200
                                    @if($restaurant->isCurrentlyOpen())
                                        bg-red-500 hover:bg-red-600 text-white
                                    @else
                                        bg-green-500 hover:bg-green-600 text-white
                                    @endif">
                            <i class="fas fa-power-off mr-2"></i>
                            @if($restaurant->isCurrentlyOpen())
                                Close Restaurant
                            @else
                                Open Restaurant
                            @endif
                        </button>
                    </div>

                    <!-- Individual Open/Close Buttons -->
                    <div class="grid grid-cols-2 gap-3">
                        <button id="openBtn" 
                                class="py-2 px-4 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-door-open mr-2"></i>
                            Open
                        </button>
                        <button id="closeBtn" 
                                class="py-2 px-4 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-door-closed mr-2"></i>
                            Close
                        </button>
                    </div>

                    <!-- Status Message -->
                    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Status Message</div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $restaurant->status_message ?: $restaurant->status_message }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Hours -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Business Hours</h2>
                    
                    <form id="businessHoursForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Opening Time -->
                        <div class="mb-4">
                            <label for="opening_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Opening Time
                            </label>
                            <input type="time" 
                                   id="opening_time" 
                                   name="opening_time" 
                                   value="{{ $restaurant->opening_time?->format('H:i') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Closing Time -->
                        <div class="mb-4">
                            <label for="closing_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Closing Time
                            </label>
                            <input type="time" 
                                   id="closing_time" 
                                   name="closing_time" 
                                   value="{{ $restaurant->closing_time?->format('H:i') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>

                        <!-- Auto Open/Close -->
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="auto_open_close" 
                                       name="auto_open_close" 
                                       value="1"
                                       {{ $restaurant->auto_open_close ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Auto open/close based on business hours
                                </span>
                            </label>
                        </div>

                        <!-- Status Message -->
                        <div class="mb-4">
                            <label for="status_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Custom Status Message (Optional)
                            </label>
                            <textarea id="status_message" 
                                      name="status_message" 
                                      rows="3"
                                      placeholder="e.g., 'We're temporarily closed for maintenance'"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ $restaurant->status_message }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Save Business Hours
                        </button>
                    </form>

                    <!-- Current Hours Display -->
                    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Current Business Hours</div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $restaurant->formatted_business_hours }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status History -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Status Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Manual Status</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $restaurant->is_open ? 'Open' : 'Closed' }}
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Auto Status</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $restaurant->auto_open_close ? 'Enabled' : 'Disabled' }}
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Current Time</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white" id="currentTime">
                            {{ now()->format('g:i A') }}
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Effective Status</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $restaurant->isCurrentlyOpen() ? 'Open' : 'Closed' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleStatusBtn = document.getElementById('toggleStatusBtn');
    const openBtn = document.getElementById('openBtn');
    const closeBtn = document.getElementById('closeBtn');
    const businessHoursForm = document.getElementById('businessHoursForm');
    const currentTimeElement = document.getElementById('currentTime');

    // Update current time every minute
    function updateCurrentTime() {
        const now = new Date();
        currentTimeElement.textContent = now.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
    }
    
    updateCurrentTime();
    setInterval(updateCurrentTime, 60000);

    // Toggle Status
    toggleStatusBtn.addEventListener('click', function() {
        fetch(`{{ route('restaurant.status.toggle', $restaurant->slug) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button text and style
                if (data.status === 'open') {
                    toggleStatusBtn.innerHTML = '<i class="fas fa-power-off mr-2"></i>Close Restaurant';
                    toggleStatusBtn.className = 'w-full py-3 px-4 rounded-lg font-medium transition-colors duration-200 bg-red-500 hover:bg-red-600 text-white';
                } else {
                    toggleStatusBtn.innerHTML = '<i class="fas fa-power-off mr-2"></i>Open Restaurant';
                    toggleStatusBtn.className = 'w-full py-3 px-4 rounded-lg font-medium transition-colors duration-200 bg-green-500 hover:bg-green-600 text-white';
                }
                
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating status', 'error');
        });
    });

    // Open Restaurant
    openBtn.addEventListener('click', function() {
        fetch(`{{ route('restaurant.status.open', $restaurant->slug) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error opening restaurant', 'error');
        });
    });

    // Close Restaurant
    closeBtn.addEventListener('click', function() {
        fetch(`{{ route('restaurant.status.close', $restaurant->slug) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error closing restaurant', 'error');
        });
    });

    // Update Business Hours
    businessHoursForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(businessHoursForm);
        const data = Object.fromEntries(formData.entries());
        
        fetch(`{{ route('restaurant.status.business-hours', $restaurant->slug) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating business hours', 'error');
        });
    });

    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endpush
@endsection 