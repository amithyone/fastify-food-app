@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                <i class="fas fa-star text-yellow-500 mr-2"></i>
                                {{ $plan->name }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                {{ $plan->description }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('restaurant.promotions', $restaurant->slug) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Plans
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Plan Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Plan Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <div class="text-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $plan->name }}</h2>
                            <div class="text-4xl font-bold text-orange-600 dark:text-orange-400 mb-2">
                                {{ $plan->formatted_price }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                for {{ $plan->duration_text }}
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Duration:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $plan->duration_text }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Max Impressions:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ number_format($plan->max_impressions) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Price per Day:</span>
                                <span class="font-medium text-gray-900 dark:text-white">₦{{ number_format($plan->price / 100 / $plan->duration_days, 0) }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Features Included:</h3>
                            <ul class="space-y-2">
                                @foreach($plan->features_list as $feature)
                                <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Promotion Form -->
                    <div class="bg-white dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create Your Promotion</h3>
                        
                        <form id="promotionForm" class="space-y-4">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Promotion Title
                                </label>
                                <input type="text" id="title" name="title" 
                                       value="{{ $restaurant->name }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                                       placeholder="Enter your promotion title">
                                <p class="text-xs text-gray-500 mt-1">This will be displayed as the main headline</p>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                                          placeholder="Describe your promotion, special offers, or unique selling points">{{ $restaurant->description }}</textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="badge_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Badge Text
                                    </label>
                                    <select id="badge_text" name="badge_text"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                        <option value="">No Badge</option>
                                        <option value="New">New</option>
                                        <option value="Popular">Popular</option>
                                        <option value="Limited Time">Limited Time</option>
                                        <option value="Special Offer">Special Offer</option>
                                        <option value="Best Seller">Best Seller</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="badge_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Badge Color
                                    </label>
                                    <select id="badge_color" name="badge_color"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                        <option value="orange">Orange</option>
                                        <option value="green">Green</option>
                                        <option value="red">Red</option>
                                        <option value="blue">Blue</option>
                                        <option value="purple">Purple</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="cta_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Button Text
                                    </label>
                                    <input type="text" id="cta_text" name="cta_text" value="Order Now"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                </div>
                                
                                <div>
                                    <label for="cta_link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Custom Link (Optional)
                                    </label>
                                    <input type="url" id="cta_link" name="cta_link"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white"
                                           placeholder="Leave empty to use menu link">
                                </div>
                            </div>

                            <div>
                                <label for="ad_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Promotion Image (Optional)
                                </label>
                                <input type="file" id="ad_image" name="ad_image" accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-800 dark:text-white">
                                <p class="text-xs text-gray-500 mt-1">Upload a custom image for your promotion. If not provided, your restaurant logo will be used.</p>
                            </div>

                            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 border border-orange-200 dark:border-orange-700">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-info-circle text-orange-600 mr-2"></i>
                                    <span class="font-medium text-orange-800 dark:text-orange-200">Payment Required</span>
                                </div>
                                <p class="text-sm text-orange-700 dark:text-orange-300">
                                    You will be charged <strong>{{ $plan->formatted_amount }}</strong> for this promotion. 
                                    Payment will be processed via bank transfer.
                                </p>
                            </div>

                            <button type="submit" 
                                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                                <i class="fas fa-credit-card mr-2"></i>
                                Create Promotion - {{ $plan->formatted_price }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Promotion Preview</h3>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900 dark:text-white" id="previewTitle">{{ $restaurant->name }}</h4>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200" id="previewBadge" style="display: none;">
                                New
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3" id="previewDescription">{{ $restaurant->description }}</p>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $restaurant->name }}</span>
                            <button class="bg-orange-500 hover:bg-orange-600 text-white text-sm px-4 py-2 rounded-lg transition-colors" id="previewCta">
                                Order Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Live preview updates
document.getElementById('title').addEventListener('input', function() {
    document.getElementById('previewTitle').textContent = this.value || '{{ $restaurant->name }}';
});

document.getElementById('description').addEventListener('input', function() {
    document.getElementById('previewDescription').textContent = this.value || '{{ $restaurant->description }}';
});

document.getElementById('badge_text').addEventListener('change', function() {
    const badge = document.getElementById('previewBadge');
    if (this.value) {
        badge.textContent = this.value;
        badge.style.display = 'inline-flex';
    } else {
        badge.style.display = 'none';
    }
});

document.getElementById('cta_text').addEventListener('input', function() {
    document.getElementById('previewCta').textContent = this.value || 'Order Now';
});

// Form submission
document.getElementById('promotionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("restaurant.promotions.payment", $restaurant->slug) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            alert('Error creating promotion: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating promotion. Please try again.');
    });
});
</script>
@endsection 