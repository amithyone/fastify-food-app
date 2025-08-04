<div class="restaurant-card bg-white dark:bg-gray-800 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-200 overflow-hidden flex flex-col cursor-pointer transform hover:scale-105 border-2 border-transparent relative" 
     data-name="{{ strtolower($restaurant->name) }}" 
     data-cuisine="{{ strtolower($restaurant->cuisine_type ?? '') }}" 
     data-location="{{ strtolower($restaurant->city ?? '') }}"
     onclick="window.location.href='{{ route('menu.restaurant', $restaurant->slug) }}'">
    
    @if(isset($featured) && $featured)
        <!-- Featured Badge -->
        <div class="absolute top-2 right-2 z-20">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                @if($featured->badge_color === 'red') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                @elseif($featured->badge_color === 'green') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                @elseif($featured->badge_color === 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                @elseif($featured->badge_color === 'purple') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @endif">
                <i class="fas fa-star mr-1"></i>
                {{ $featured->badge_text }}
            </span>
        </div>
    @endif

    <!-- Open/Close Status Badge - Top Left -->
    <div class="absolute top-2 left-2 z-20">
        @php
            $statusDisplay = $restaurant->status_display ?? [
                'status' => $restaurant->is_open ? 'open' : 'closed',
                'text' => $restaurant->is_open ? 'Open' : 'Closed',
                'icon' => $restaurant->is_open ? 'fas fa-circle' : 'fas fa-times-circle'
            ];
        @endphp
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold shadow-lg
            @if($statusDisplay['status'] === 'open') 
                bg-green-500 text-white border-2 border-green-400
            @else 
                bg-red-500 text-white border-2 border-red-400
            @endif">
            <i class="{{ $statusDisplay['icon'] }} mr-1.5 text-xs"></i>
            {{ $statusDisplay['text'] }}
        </span>
    </div>
    
    <!-- Top Section - Background Image with Logo Overlay -->
    <div class="h-32 bg-gradient-to-br from-orange-200 to-orange-400 dark:from-gray-700 dark:to-gray-900 flex items-center justify-center relative overflow-hidden">
        @if($restaurant->banner_url)
            <img src="{{ $restaurant->banner_url }}" 
                 alt="{{ $restaurant->name }}" 
                 class="w-full h-full object-cover">
        @else
            <img src="{{ \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" 
                 alt="{{ $restaurant->name }}" 
                 class="w-full h-full object-cover">
        @endif
        
        <!-- Restaurant Logo Overlay - Bottom Left -->
        @if($restaurant->logo_url)
            <div class="absolute bottom-2 left-2 z-10">
                <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-lg shadow-lg flex items-center justify-center border-2 border-white dark:border-gray-700">
                    <img src="{{ $restaurant->logo_url }}" 
                         alt="{{ $restaurant->name }} Logo" 
                         class="w-10 h-10 object-cover rounded">
                </div>
            </div>
        @endif
        
        <!-- Details Icon - Top Right -->
        <button onclick="event.stopPropagation(); showRestaurantDetails({{ $restaurant->id }})" 
                class="absolute top-2 right-2 w-8 h-8 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors z-10">
            <i class="fas fa-sticky-note text-orange-500 text-sm"></i>
        </button>
    </div>
    
    <!-- Bottom Section - Content -->
    <div class="p-4 flex-1 flex flex-col justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white leading-tight restaurant-name mb-2">
                @if(isset($featured) && $featured && $featured->title)
                    {{ $featured->title }}
                @else
                    {{ $restaurant->name }}
                @endif
            </h3>
            
            <span class="inline-block text-xs text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full font-medium cuisine-type mb-2">
                {{ $restaurant->cuisine_type ?? 'Restaurant' }}
            </span>
            
            @if(isset($featured) && $featured && $featured->description)
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2 line-clamp-2">{{ Str::limit($featured->description, 80) }}</p>
            @endif
            
            <!-- Location -->
            @if($restaurant->city || $restaurant->state)
                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center mb-2">
                    <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                    {{ $restaurant->city }}{{ $restaurant->city && $restaurant->state ? ', ' : '' }}{{ $restaurant->state }}
                </p>
            @endif
        </div>
        
        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex text-orange-400 mr-2">
                    @php
                        $rating = $restaurant->average_rating ?? 0;
                        $fullStars = floor($rating);
                        $hasHalfStar = $rating - $fullStars >= 0.5;
                    @endphp
                    
                    @if($rating > 0)
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $fullStars)
                                <i class="fas fa-star text-sm"></i>
                            @elseif($i == $fullStars + 1 && $hasHalfStar)
                                <i class="fas fa-star-half-alt text-sm"></i>
                            @else
                                <i class="far fa-star text-sm"></i>
                            @endif
                        @endfor
                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">({{ number_format($rating, 1) }})</span>
                    @else
                        @for($i = 1; $i <= 5; $i++)
                            <i class="far fa-star text-sm"></i>
                        @endfor
                        <span class="text-sm text-gray-400 dark:text-gray-500 ml-1">(No ratings)</span>
                    @endif
                </div>
            </div>
            
            <!-- Rating Button - Bottom Right -->
            <div class="w-10 h-10 bg-orange-500 dark:bg-orange-600 rounded-full flex items-center justify-center text-white text-sm font-semibold transition-all duration-200 cursor-pointer hover:bg-orange-600 dark:hover:bg-orange-700 shadow-lg"
                 onclick="event.stopPropagation(); showRatingModal({{ $restaurant->id }}, '{{ $restaurant->name }}')">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>
</div> 