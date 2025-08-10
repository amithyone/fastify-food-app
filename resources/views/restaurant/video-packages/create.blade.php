@extends('layouts.app')

@section('title', 'Create Video Package - ' . $restaurant->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create Video Package</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Request professional video content for your restaurant</p>
                </div>
                <a href="{{ route('restaurant.video-packages.index', $restaurant->slug) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Video Packages
                </a>
            </div>
        </div>

        <!-- Package Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Package Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($templates as $template)
                        <div class="text-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg {{ $template->slug === 'premium' ? 'bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800' : '' }}">
                            <div class="text-2xl font-bold {{ $template->color_classes }} mb-2">{{ $template->name }}</div>
                            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $template->formatted_price }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                {{ $template->number_of_videos }} {{ Str::plural('video', $template->number_of_videos) }}, {{ $template->duration_text }}
                            </div>
                            <div class="text-xs text-gray-500">
                                @foreach(array_slice($template->features, 0, 3) as $feature)
                                    <div>• {{ $feature }}</div>
                                @endforeach
                                @if(count($template->features) > 3)
                                    <div class="text-gray-400">• +{{ count($template->features) - 3 }} more features</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Filming Instructions -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-blue-200 dark:border-blue-700">
                <div class="flex items-center">
                    <i class="fas fa-video text-blue-600 mr-3"></i>
                    <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100">Filming Preparation Instructions</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-3">📋 What We Need From You:</h4>
                        <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                <span><strong>3-10 Signature Dishes:</strong> Prepare your best dishes for filming</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                <span><strong>Staff Coordination:</strong> Inform all staff about our filming schedule</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                <span><strong>Clean Environment:</strong> Ensure filming areas are clean and presentable</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                <span><strong>Good Lighting:</strong> Natural light or well-lit areas preferred</span>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-3">🎬 Filming Day Checklist:</h4>
                        <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                            <li class="flex items-start">
                                <i class="fas fa-clock text-orange-500 mr-2 mt-0.5"></i>
                                <span><strong>Timing:</strong> Allow 2-4 hours for filming session</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-users text-orange-500 mr-2 mt-0.5"></i>
                                <span><strong>Staff:</strong> Have key staff available for interviews</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-utensils text-orange-500 mr-2 mt-0.5"></i>
                                <span><strong>Dishes:</strong> Prepare dishes fresh during filming</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-camera text-orange-500 mr-2 mt-0.5"></i>
                                <span><strong>Space:</strong> Clear filming areas of unnecessary items</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-4 p-4 bg-blue-100 dark:bg-blue-800/30 rounded-lg">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> Our team will contact you 24-48 hours before filming to confirm details and discuss any specific requirements for your restaurant.
                    </p>
                </div>
            </div>
        </div>

        <!-- Video Package Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Video Package Request Form</h3>
            </div>

            <form action="{{ route('restaurant.video-packages.store', $restaurant->slug) }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <!-- Package Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="package_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Package Name *
                        </label>
                        <input type="text" 
                               name="package_name" 
                               id="package_name" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="e.g., Restaurant Introduction Video">
                        @error('package_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="package_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Package Type *
                        </label>
                        <select name="package_type" 
                                id="package_type" 
                                required
                                onchange="updateFormFromTemplate(this.value)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select Package Type</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->slug }}" 
                                        data-price="{{ $template->base_price }}"
                                        data-duration="{{ $template->video_duration }}"
                                        data-videos="{{ $template->number_of_videos }}">
                                    {{ $template->name }} Package
                                </option>
                            @endforeach
                        </select>
                        @error('package_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Project Description *
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                              placeholder="Describe your video project, goals, and what you want to showcase..."></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Video Specifications -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="video_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Video Duration (seconds) *
                        </label>
                        <input type="number" 
                               name="video_duration" 
                               id="video_duration" 
                               required
                               min="15" 
                               max="300"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="60">
                        <p class="mt-1 text-xs text-gray-500">15 seconds to 5 minutes</p>
                        @error('video_duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="number_of_videos" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Number of Videos
                        </label>
                        <input type="number" 
                               name="number_of_videos" 
                               id="number_of_videos" 
                               min="1" 
                               max="10"
                               value="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('number_of_videos')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Budget (₦) *
                        </label>
                        <input type="number" 
                               name="price" 
                               id="price" 
                               required
                               min="10000" 
                               step="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="50000">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Shoot Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="shoot_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Preferred Shoot Date
                        </label>
                        <input type="date" 
                               name="shoot_date" 
                               id="shoot_date" 
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('shoot_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="shoot_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Preferred Shoot Time
                        </label>
                        <input type="time" 
                               name="shoot_time" 
                               id="shoot_time" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('shoot_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location and Contact -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="location_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Shoot Location Address
                        </label>
                        <input type="text" 
                               name="location_address" 
                               id="location_address" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Restaurant address or preferred location">
                        @error('location_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Contact Person
                        </label>
                        <input type="text" 
                               name="contact_person" 
                               id="contact_person" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Name of person to contact">
                        @error('contact_person')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contact Phone
                    </label>
                    <input type="tel" 
                           name="contact_phone" 
                           id="contact_phone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="Phone number for coordination">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                                       <!-- Filming Preparation -->
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                           <div>
                               <label for="dishes_to_film" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                   Dishes to Film *
                               </label>
                               <input type="text"
                                      name="dishes_to_film"
                                      id="dishes_to_film"
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                      placeholder="e.g., Jollof Rice, Suya, Pounded Yam, Egusi Soup">
                               <p class="mt-1 text-xs text-gray-500">List 3-10 signature dishes you want filmed</p>
                               @error('dishes_to_film')
                                   <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                               @enderror
                           </div>

                           <div>
                               <label for="staff_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                   Staff Contact Person
                               </label>
                               <input type="text"
                                      name="staff_contact"
                                      id="staff_contact"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                      placeholder="Name of staff member coordinating filming">
                               <p class="mt-1 text-xs text-gray-500">Person responsible for filming coordination</p>
                               @error('staff_contact')
                                   <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                               @enderror
                           </div>
                       </div>

                       <div>
                           <label for="filming_requirements" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                               Filming Requirements & Preparation
                           </label>
                           <textarea name="filming_requirements"
                                     id="filming_requirements"
                                     rows="3"
                                     class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                     placeholder="Any specific filming requirements, preferred timing, special equipment needs, or preparation notes..."></textarea>
                           <p class="mt-1 text-xs text-gray-500">Describe any special requirements or preparation needed for filming</p>
                           @error('filming_requirements')
                               <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                           @enderror
                       </div>

                       <!-- Special Instructions -->
                       <div>
                           <label for="special_instructions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                               Special Instructions
                           </label>
                           <textarea name="special_instructions"
                                     id="special_instructions"
                                     rows="3"
                                     class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                     placeholder="Any specific requirements, style preferences, or special requests..."></textarea>
                           @error('special_instructions')
                               <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                           @enderror
                       </div>



                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                        <i class="fas fa-video mr-2"></i>
                        Submit Video Package Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
function updateFormFromTemplate(templateSlug) {
    if (!templateSlug) return;
    
    const select = document.getElementById('package_type');
    const selectedOption = select.querySelector(`option[value="${templateSlug}"]`);
    
    if (selectedOption) {
        const price = selectedOption.dataset.price;
        const duration = selectedOption.dataset.duration;
        const videos = selectedOption.dataset.videos;
        
        // Update form fields
        if (price) document.getElementById('price').value = price;
        if (duration) document.getElementById('video_duration').value = duration;
        if (videos) document.getElementById('number_of_videos').value = videos;
        
        // Update package name if empty
        const packageName = document.getElementById('package_name');
        if (!packageName.value) {
            packageName.value = `${selectedOption.textContent} Request`;
        }
    }
}

// Initialize form with first template if available
document.addEventListener('DOMContentLoaded', function() {
    const templates = @json($templates);
    if (templates.length > 0) {
        // Set first template as default
        const firstTemplate = templates[0];
        document.getElementById('package_type').value = firstTemplate.slug;
        updateFormFromTemplate(firstTemplate.slug);
    }
});
</script>
