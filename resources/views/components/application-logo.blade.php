<div class="flex items-center justify-center {{ $attributes->get('class', '') }}">
    <div class="relative">
        <!-- Main 'y' shape with food elements -->
        <div class="relative w-8 h-8 md:w-10 md:h-10">
            <!-- Main 'y' shape in orange -->
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-600 rounded-full flex items-center justify-center">
                <div class="text-white font-bold text-xs md:text-sm tracking-wider">y</div>
            </div>
            
            <!-- Food elements overlay -->
            <div class="absolute -top-1 -left-1 w-3 h-3 bg-red-500 rounded-full"></div>
            <div class="absolute -bottom-1 -right-1 w-2 h-2 bg-green-600 rounded-full"></div>
        </div>
    </div>
</div>
