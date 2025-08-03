@php
    $restaurant = $featured->restaurant;
    $rating = $restaurant->average_rating ?? 0;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
    @if($featured->badge_text)
        <div class="relative">
            <div class="absolute top-3 left-3 z-10">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                    {{ $featured->badge_text }}
                </span>
            </div>
        </div>
    @endif

    <div class="relative h-48 bg-gray-200 dark:bg-gray-700">
        @if($featured->ad_image_url || $restaurant->logo_url)
            <img src="{{ $featured->ad_image_url ?? $restaurant->logo_url }}" 
                 alt="{{ $featured->display_title }}" 
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-utensils text-4xl text-gray-400 dark:text-gray-500"></i>
            </div>
        @endif
    </div>

    <div class="p-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
            {{ $featured->display_title }}
        </h3>

        @if($featured->display_description)
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                {{ Str::limit($featured->display_description, 80) }}
            </p>
        @endif

        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $restaurant->name }}
            </span>
            
            <div class="flex items-center space-x-1">
                <div class="flex text-orange-400">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rating)
                            <i class="fas fa-star text-xs"></i>
                        @else
                            <i class="far fa-star text-xs"></i>
                        @endif
                    @endfor
                </div>
                @if($rating > 0)
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ number_format($rating, 1) }})</span>
                @endif
            </div>
        </div>

        <a href="{{ $featured->cta_url }}" 
           class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
            <i class="fas fa-utensils mr-2"></i>
            {{ $featured->cta_text }}
        </a>
    </div>
</div> 