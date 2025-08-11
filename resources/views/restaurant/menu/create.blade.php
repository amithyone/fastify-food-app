@extends('layouts.app')

@section('title', 'Add Menu Item - ' . $restaurant->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add Menu Item</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $restaurant->name }}</p>
            </div>
            <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Menu
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form action="{{ route('restaurant.menu.store', $restaurant->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Item Name *
                        </label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               value="{{ old('name') }}" placeholder="Enter item name">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="Describe your menu item...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price and Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Price (₦) *
                            </label>
                            <input type="number" id="price" name="price" required min="0" step="0.01"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   value="{{ old('price') }}" placeholder="1000.00">
                            <p class="text-xs text-gray-500 mt-1">Enter price in Naira (e.g., 1000.00 = ₦1,000.00)</p>
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Category *
                            </label>
                            <select id="category_id" name="category_id" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Item Image
                        </label>
                        
                        <!-- Image Selection Options -->
                        <div class="space-y-4">
                            <!-- Upload New Image -->
                            <div>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="image_source" value="upload" checked class="text-orange-600 focus:ring-orange-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Upload New Image</span>
                                </label>
                                <div id="uploadSection" class="mt-2">
                                    <input type="file" id="image" name="image" accept="image/*"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    @error('image')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Select from Existing Images -->
                            <div>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="image_source" value="existing" class="text-orange-600 focus:ring-orange-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Select from Existing Images</span>
                                </label>
                                <div id="existingSection" class="mt-2 hidden">
                                    <button type="button" onclick="openImageSelector()" 
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-images mr-2"></i>
                                        <span id="selectedImageText">Choose from uploaded images</span>
                                    </button>
                                    <input type="hidden" id="selectedImageId" name="selected_image_id">
                                    <input type="hidden" id="selectedImagePath" name="selected_image_path">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Click to browse your uploaded images
                                    </p>
                                    <button type="button" onclick="testImageSelector()" 
                                            class="mt-2 px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Test Image Selector
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Image Preview -->
                        <div id="imagePreview" class="mt-4 hidden">
                            <div class="relative inline-block">
                                <img id="previewImage" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                <button type="button" onclick="clearSelectedImage()" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dietary Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="ingredients" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Ingredients
                            </label>
                            <textarea id="ingredients" name="ingredients" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                      placeholder="List main ingredients...">{{ old('ingredients') }}</textarea>
                        </div>

                        <div>
                            <label for="allergens" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Allergens
                            </label>
                            <textarea id="allergens" name="allergens" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                      placeholder="List any allergens...">{{ old('allergens') }}</textarea>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_available" name="is_available" value="1" 
                                   {{ old('is_available', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="is_available" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Available
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1" 
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Featured
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_available_for_delivery" name="is_available_for_delivery" value="1" 
                                   {{ old('is_available_for_delivery', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="is_available_for_delivery" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Available for Delivery
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_vegetarian" name="is_vegetarian" value="1" 
                                   {{ old('is_vegetarian') ? 'checked' : '' }}
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="is_vegetarian" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Vegetarian
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_spicy" name="is_spicy" value="1" 
                                   {{ old('is_spicy') ? 'checked' : '' }}
                                   class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="is_spicy" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Spicy
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-6">
                        <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                           class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Menu Item
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Test function for debugging
function testImageSelector() {
    console.log('Test button clicked');
    alert('Test function works! Now trying to open image selector...');
    openImageSelector();
}

// Image source toggle
document.querySelectorAll('input[name="image_source"]').forEach(radio => {
    radio.addEventListener('change', function() {
        console.log('Radio button changed to:', this.value);
        const uploadSection = document.getElementById('uploadSection');
        const existingSection = document.getElementById('existingSection');
        
        if (this.value === 'upload') {
            uploadSection.classList.remove('hidden');
            existingSection.classList.add('hidden');
        } else {
            uploadSection.classList.add('hidden');
            existingSection.classList.remove('hidden');
        }
    });
});

// Open image selector modal
function openImageSelector() {
    console.log('Opening image selector...');
    console.log('Function called successfully');
    
    const url = '{{ route("restaurant.images.get", $restaurant->slug) }}';
    console.log('Fetching from:', url);
    console.log('Restaurant slug:', '{{ $restaurant->slug }}');
    
    // Show loading state
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    modal.id = 'imageSelectorModal';
    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-orange-500 mb-4"></i>
            <p class="text-gray-600 dark:text-gray-400">Loading images...</p>
        </div>
    `;
    document.body.appendChild(modal);
    
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.text().then(text => {
                console.log('Response text:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    throw new Error('Server returned invalid JSON: ' + text.substring(0, 200));
                }
            });
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                showImageSelectorModal(data.images);
            } else {
                closeImageSelectorModal();
                alert('Failed to load images: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading images:', error);
            closeImageSelectorModal();
            alert('Failed to load images: ' + error.message);
        });
}

// Show image selector modal
function showImageSelectorModal(images) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
    modal.id = 'imageSelectorModal';
    
    let imagesHtml = '';
    if (images.length > 0) {
        images.forEach(image => {
            imagesHtml += `
                <div class="cursor-pointer hover:scale-105 transition-transform p-2" onclick="selectImageFromModal('${image.url}', ${image.id}, '${image.original_name}')">
                    <div class="relative">
                        <img src="${image.thumbnail_url}?v=${Date.now()}" alt="${image.original_name}" 
                             class="w-full h-20 object-cover rounded-lg border-2 border-transparent hover:border-orange-500 shadow-sm bg-gray-200 dark:bg-gray-600"
                             onerror="this.src='${image.url}?v=${Date.now()}'"
                             style="min-height: 80px;">
                        <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center z-10">
                            <i class="fas fa-check text-white opacity-0 hover:opacity-100 text-lg"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 truncate text-center">${image.original_name}</p>
                </div>
            `;
        });
    } else {
        imagesHtml = `
            <div class="col-span-full text-center py-8">
                <i class="fas fa-images text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">No images available</p>
                <p class="text-sm text-gray-400">Upload some images first</p>
                <a href="{{ route('restaurant.images.index', $restaurant->slug) }}" class="inline-block mt-4 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-upload mr-2"></i>Upload Images
                </a>
            </div>
        `;
    }
    
    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-5xl w-full max-h-[80vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Image for Menu Item</h3>
                    <button onclick="closeImageSelectorModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-3">
                    ${imagesHtml}
                </div>
                <div class="mt-4 text-center text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Click on an image to select it for your menu item
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Close image selector modal
function closeImageSelectorModal() {
    const modal = document.getElementById('imageSelectorModal');
    if (modal) {
        modal.remove();
    }
}

// Select image from modal
function selectImageFromModal(imageUrl, imageId, originalName) {
    document.getElementById('selectedImageId').value = imageId;
    document.getElementById('selectedImagePath').value = imageUrl;
    document.getElementById('selectedImageText').textContent = originalName;
    
    // Show preview
    document.getElementById('previewImage').src = imageUrl;
    document.getElementById('imagePreview').classList.remove('hidden');
    
    closeImageSelectorModal();
}

// Clear selected image
function clearSelectedImage() {
    document.getElementById('selectedImageId').value = '';
    document.getElementById('selectedImagePath').value = '';
    document.getElementById('selectedImageText').textContent = 'Choose from uploaded images';
    document.getElementById('imagePreview').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('imageSelectorModal');
    if (modal && e.target === modal) {
        closeImageSelectorModal();
    }
});
</script>
@endpush 