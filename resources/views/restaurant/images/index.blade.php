@extends('layouts.app')

@section('title', 'Image Management - ' . $restaurant->name)

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
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Image Management</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $restaurant->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-all duration-200">
                        <i class="fas fa-utensils mr-2"></i>
                        Add Menu Item
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Bulk Upload Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Bulk Upload Images</h2>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Max 5MB per image, supports JPEG, PNG, GIF, WebP
                </div>
            </div>

            <form id="bulkUploadForm" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <!-- File Upload Area -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-orange-400 dark:hover:border-orange-500 transition-colors">
                    <div class="space-y-4">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500"></i>
                        <div>
                            <label for="images" class="cursor-pointer">
                                <span class="text-lg font-medium text-gray-900 dark:text-white">Choose images to upload</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 block mt-1">or drag and drop</span>
                            </label>
                            <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden">
                        </div>
                    </div>
                </div>

                <!-- Alt Text -->
                <div>
                    <label for="alt_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Alt Text (Optional)
                    </label>
                    <input type="text" id="alt_text" name="alt_text" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="Describe the images for accessibility">
                </div>

                <!-- Upload Progress -->
                <div id="uploadProgress" class="hidden">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div id="progressBar" class="bg-orange-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <span id="progressText" class="text-sm text-gray-600 dark:text-gray-400">0%</span>
                    </div>
                    <div id="uploadStatus" class="text-sm text-gray-600 dark:text-gray-400"></div>
                </div>

                <!-- Upload Button -->
                <button type="submit" id="uploadBtn" 
                        class="w-full bg-orange-500 text-white py-3 px-4 rounded-lg hover:bg-orange-600 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-upload mr-2"></i>
                    Upload Images
                </button>
            </form>
        </div>

        <!-- Image Gallery -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Unused Images -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Available Images</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $unusedImages->count() }} images</span>
                </div>

                @if($unusedImages->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($unusedImages as $image)
                            <div class="group relative bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                <img src="{{ $image->thumbnail_url }}" 
                                     alt="{{ $image->alt_text ?: $image->original_name }}"
                                     class="w-full h-32 object-cover">
                                
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 space-x-2">
                                        <button onclick="selectImage({{ $image->id }}, '{{ $image->url }}')" 
                                                class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600 transition-colors"
                                                title="Use this image">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                        <button onclick="deleteImage({{ $image->id }})" 
                                                class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors"
                                                title="Delete image">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="p-2">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate" title="{{ $image->original_name }}">
                                        {{ $image->original_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ $image->formatted_file_size }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-images text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No available images</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Upload some images to get started</p>
                    </div>
                @endif
            </div>

            <!-- Used Images -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Used Images</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $usedImages->count() }} images</span>
                </div>

                @if($usedImages->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($usedImages as $image)
                            <div class="group relative bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                <img src="{{ $image->thumbnail_url }}" 
                                     alt="{{ $image->alt_text ?: $image->original_name }}"
                                     class="w-full h-32 object-cover">
                                
                                <div class="absolute top-2 right-2">
                                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-check mr-1"></i>Used
                                    </span>
                                </div>
                                
                                <div class="p-2">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate" title="{{ $image->original_name }}">
                                        {{ $image->original_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ $image->formatted_file_size }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No used images</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Images will appear here when used in menu items</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Selection Modal -->
<div id="imageSelectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-96 overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Image</h3>
                    <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="imageSelectionContent" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <!-- Images will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bulk Upload Form
document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const uploadBtn = document.getElementById('uploadBtn');
    const progressDiv = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const uploadStatus = document.getElementById('uploadStatus');
    
    // Show progress
    progressDiv.classList.remove('hidden');
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
    
    fetch('{{ route("restaurant.images.bulk-upload", $restaurant->slug) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            uploadStatus.innerHTML = `<span class="text-green-600">${data.message}</span>`;
            progressBar.style.width = '100%';
            progressText.textContent = '100%';
            
            // Reset form
            document.getElementById('images').value = '';
            document.getElementById('alt_text').value = '';
            
            // Reload page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            uploadStatus.innerHTML = `<span class="text-red-600">${data.message}</span>`;
        }
    })
    .catch(error => {
        uploadStatus.innerHTML = `<span class="text-red-600">Upload failed: ${error.message}</span>`;
    })
    .finally(() => {
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload Images';
    });
});

// Delete Image
function deleteImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }
    
    fetch(`{{ route('restaurant.images.destroy', $restaurant->slug) }}/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the image element from DOM
            const imageElement = document.querySelector(`[onclick="deleteImage(${imageId})"]`).closest('.group');
            imageElement.remove();
        } else {
            alert('Failed to delete image: ' + data.message);
        }
    })
    .catch(error => {
        alert('Failed to delete image: ' + error.message);
    });
}

// Select Image (for use in menu creation)
function selectImage(imageId, imageUrl) {
    // Store selected image info in localStorage for menu creation form
    localStorage.setItem('selectedImageId', imageId);
    localStorage.setItem('selectedImageUrl', imageUrl);
    
    // Show success message
    alert('Image selected! You can now use it when creating menu items.');
}

// Drag and drop functionality
const dropZone = document.querySelector('.border-dashed');
const fileInput = document.getElementById('images');

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-orange-400', 'dark:border-orange-500');
});

dropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-orange-400', 'dark:border-orange-500');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-orange-400', 'dark:border-orange-500');
    
    const files = e.dataTransfer.files;
    fileInput.files = files;
    
    // Update the label to show selected files
    const label = dropZone.querySelector('label span');
    if (files.length > 0) {
        label.textContent = `${files.length} file(s) selected`;
    }
});
</script>
@endsection
