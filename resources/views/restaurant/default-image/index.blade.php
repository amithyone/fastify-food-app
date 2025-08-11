@extends('layouts.app')

@section('title', 'Default Image Management - ' . $restaurant->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                       class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Default Image Management</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $restaurant->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        <i class="fas fa-crown mr-1"></i>
                        Premium Feature
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Premium Feature Notice -->
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 border border-purple-200 dark:border-purple-700 rounded-lg p-6 mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-crown text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-purple-900 dark:text-purple-100">Premium Feature</h3>
                    <p class="mt-1 text-sm text-purple-700 dark:text-purple-300">
                        Set a custom default image that will be used for menu items that don't have their own image. 
                        This helps maintain a consistent brand appearance across your menu.
                    </p>
                </div>
            </div>
        </div>

        <!-- Current Default Image -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Default Image</h2>
            
            @if($restaurant->hasCustomDefaultImage())
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <img src="{{ $restaurant->default_image_url }}" 
                             alt="Current default image" 
                             class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-600">
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Active Default Image</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            This image will be used for menu items without their own image.
                        </p>
                        <div class="mt-4 flex space-x-3">
                            <button onclick="removeDefaultImage()" 
                                    class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-red-600 dark:hover:bg-red-900/20 transition-colors">
                                <i class="fas fa-trash mr-2"></i>
                                Remove Default Image
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">No Default Image Set</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Upload a default image to use for menu items without their own image.
                    </p>
                </div>
            @endif
        </div>

        <!-- Upload New Default Image -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upload New Default Image</h2>
            
            <form id="defaultImageForm" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <!-- File Upload Area -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-purple-400 dark:hover:border-purple-500 transition-colors">
                    <div class="space-y-4">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500"></i>
                        <div>
                            <label for="defaultImage" class="cursor-pointer">
                                <span class="text-lg font-medium text-gray-900 dark:text-white">Choose a default image</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 block mt-1">or drag and drop</span>
                            </label>
                            <input type="file" id="defaultImage" name="default_image" accept="image/*" class="hidden" onchange="previewDefaultImage(this)">
                        </div>
                    </div>
                </div>

                <!-- Image Preview -->
                <div id="imagePreview" class="hidden">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Preview</h3>
                    <div class="flex items-center space-x-4">
                        <img id="previewImage" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-600">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                This image will be used as the default placeholder for menu items without images.
                            </p>
                            <div class="mt-2">
                                <button type="button" onclick="clearPreview()" class="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="fas fa-times mr-1"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Button -->
                <button type="submit" id="uploadBtn" 
                        class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-upload mr-2"></i>
                    Set as Default Image
                </button>
            </form>
        </div>

        <!-- Guidelines -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mt-8">
            <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-3">
                <i class="fas fa-info-circle mr-2"></i>
                Guidelines for Default Images
            </h3>
            <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    <span>Use high-quality images (recommended: 800x600 pixels or larger)</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    <span>Choose images that represent your restaurant's style and cuisine</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    <span>Ensure the image works well as a placeholder (not too busy or detailed)</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    <span>Supported formats: JPEG, PNG, GIF, WebP (max 5MB)</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
function previewDefaultImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}

function clearPreview() {
    document.getElementById('defaultImage').value = '';
    document.getElementById('imagePreview').classList.add('hidden');
}

function removeDefaultImage() {
    if (confirm('Are you sure you want to remove the default image? Menu items without images will use the system default.')) {
        fetch(`{{ route('restaurant.default-image.destroy', $restaurant->slug) }}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the default image.');
        });
    }
}

// Handle form submission
document.getElementById('defaultImageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const uploadBtn = document.getElementById('uploadBtn');
    
    // Disable button and show loading state
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
    
    fetch(`{{ route('restaurant.default-image.store', $restaurant->slug) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Default image uploaded successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while uploading the default image.');
    })
    .finally(() => {
        // Re-enable button
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Set as Default Image';
    });
});
</script>
@endsection
