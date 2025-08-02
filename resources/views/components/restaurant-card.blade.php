<div class="restaurant-card bg-white dark:bg-gray-800 rounded-xl shadow-xl hover:shadow-2xl transition-all duration-200 overflow-hidden flex flex-col cursor-pointer transform hover:scale-105 border-2 border-transparent relative" 
     data-name="{{ strtolower($restaurant->name) }}" 
     data-cuisine="{{ strtolower($restaurant->cuisine_type ?? '') }}" 
     data-location="{{ strtolower($restaurant->city ?? '') }}"
     onclick="window.location.href='{{ route('menu.restaurant', $restaurant->slug) }}'">
    
    <!-- Top Section - Background Image -->
    <div class="h-24 bg-gradient-to-br from-orange-200 to-orange-400 dark:from-gray-700 dark:to-gray-900 flex items-center justify-center relative">
        @if($restaurant->banner)
            <img src="{{ Storage::url($restaurant->banner) }}" 
                 alt="{{ $restaurant->name }}" 
                 class="w-full h-full object-cover">
        @else
            <img src="{{ \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" 
                 alt="{{ $restaurant->name }}" 
                 class="w-full h-full object-cover">
        @endif
        
        <!-- Details Icon - Top Right -->
        <button onclick="event.stopPropagation(); showRestaurantDetails({{ $restaurant->id }})" 
                class="absolute top-1 right-1 w-6 h-6 bg-white dark:bg-gray-800 rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors z-10" style="right: 4px; top: 4px;">
            <i class="fas fa-sticky-note text-orange-500 text-xs"></i>
        </button>
    </div>
    
    <!-- Bottom Section - Content -->
    <div class="p-3 flex-1 flex flex-col justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-800 dark:text-white leading-tight restaurant-name">{{ $restaurant->name }}</h3>
            <span class="text-xs text-gray-500 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded cuisine-type">{{ $restaurant->cuisine_type ?? 'Restaurant' }}</span>
            
            <!-- Location -->
            @if($restaurant->city || $restaurant->state)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                    <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                    {{ $restaurant->city }}{{ $restaurant->city && $restaurant->state ? ', ' : '' }}{{ $restaurant->state }}
                </p>
            @endif
        </div>
        
        <div class="flex items-center justify-between mt-2">
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
                                <i class="fas fa-star text-xs"></i>
                            @elseif($i == $fullStars + 1 && $hasHalfStar)
                                <i class="fas fa-star-half-alt text-xs"></i>
                            @else
                                <i class="far fa-star text-xs"></i>
                            @endif
                        @endfor
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">({{ number_format($rating, 1) }})</span>
                    @else
                        @for($i = 1; $i <= 5; $i++)
                            <i class="far fa-star text-xs"></i>
                        @endfor
                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">(No ratings)</span>
                    @endif
                </div>
            </div>
            
            <!-- Rating Button - Bottom Right -->
            <div class="w-8 h-8 bg-orange-500 dark:bg-orange-600 rounded-full flex items-center justify-center text-white text-xs font-semibold transition-all duration-200 cursor-pointer hover:bg-orange-600 dark:hover:bg-orange-700"
                 onclick="event.stopPropagation(); showRatingModal({{ $restaurant->id }}, '{{ $restaurant->name }}')">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>
</div> 