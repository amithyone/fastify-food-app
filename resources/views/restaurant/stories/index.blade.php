@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manage Stories</h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Create and manage stories for {{ $restaurant->name }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Dashboard
                        </a>
                        <button onclick="openCreateModal()" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <i class="fas fa-plus mr-2"></i>
                            Add Story
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stories Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($stories as $story)
                @php
                    $gradient = $story->color_gradient ?? 'orange';
                    $gradientClasses = [
                        'orange' => 'bg-gradient-to-tr from-orange-200 to-orange-400 dark:from-orange-700 dark:to-orange-900',
                        'pink' => 'bg-gradient-to-tr from-pink-200 to-pink-400 dark:from-pink-700 dark:to-pink-900',
                        'green' => 'bg-gradient-to-tr from-green-200 to-green-400 dark:from-green-700 dark:to-green-900',
                        'blue' => 'bg-gradient-to-tr from-blue-200 to-blue-400 dark:from-blue-700 dark:to-blue-900',
                        'purple' => 'bg-gradient-to-tr from-purple-200 to-purple-400 dark:from-purple-700 dark:to-purple-900',
                        'emerald' => 'bg-gradient-to-tr from-emerald-200 to-emerald-400 dark:from-emerald-700 dark:to-emerald-900',
                        'red' => 'bg-gradient-to-tr from-red-200 to-red-400 dark:from-red-700 dark:to-red-900',
                    ];
                    $gradientClass = $gradientClasses[$gradient] ?? $gradientClasses['orange'];
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg {{ $gradientClass }} flex items-center justify-center mr-3">
                                    @if($story->emoji)
                                        <span class="text-white text-sm">{{ $story->emoji }}</span>
                                    @else
                                        <span class="text-white text-sm">📖</span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $story->title }}</h3>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button onclick="toggleStoryStatus({{ $story->id }})" 
                                        class="p-2 rounded-full {{ $story->is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} hover:bg-opacity-75">
                                    <i class="fas {{ $story->is_active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                </button>
                                <button onclick="openEditModal({{ $story->id }})" 
                                        class="p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteStory({{ $story->id }})" 
                                        class="p-2 rounded-full bg-red-100 text-red-600 hover:bg-red-200">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if($story->subtitle)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $story->subtitle }}</p>
                        @endif
                        
                        <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">{{ Str::limit($story->content, 100) }}</p>
                        
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span class="capitalize">{{ $story->type }}</span>
                            <span>Order: {{ $story->sort_order }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-images text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No stories</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first story.</p>
                        <div class="mt-6">
                            <button onclick="openCreateModal()" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <i class="fas fa-plus mr-2"></i>
                                Add Story
                            </button>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="storyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800 px-8">
        <div class="mt-3">
            <h3 id="modalTitle" class="text-lg font-medium text-gray-900 dark:text-white mb-4">Add New Story</h3>
            
            <form id="storyForm" class="space-y-4">
                @csrf
                <input type="hidden" id="storyId" name="story_id">
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="promotion">Promotion</option>
                        <option value="announcement">Announcement</option>
                        <option value="featured">Featured</option>
                        <option value="news">News</option>
                    </select>
                </div>
                
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                    <input type="text" id="title" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="subtitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtitle (Optional)</label>
                    <input type="text" id="subtitle" name="subtitle" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                    <textarea id="content" name="content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>
                
                <div>
                    <label for="emoji" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Emoji (Optional)</label>
                    <input type="text" id="emoji" name="emoji" placeholder="🍕" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div>
                    <label for="color_gradient" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color Gradient</label>
                    <div class="mt-2 grid grid-cols-7 gap-2">
                        <div class="gradient-option cursor-pointer rounded-lg p-2 text-center text-white text-xs font-medium" data-gradient="orange" onclick="selectGradient('orange')">
                            <div class="w-full h-8 rounded bg-gradient-to-tr from-orange-200 to-orange-400 mb-1"></div>
                            Orange
                        </div>
                        <div class="gradient-option cursor-pointer rounded-lg p-2 text-center text-white text-xs font-medium" data-gradient="pink" onclick="selectGradient('pink')">
                            <div class="w-full h-8 rounded bg-gradient-to-tr from-pink-200 to-pink-400 mb-1"></div>
                            Pink
                        </div>
                        <div class="gradient-option cursor-pointer rounded-lg p-2 text-center text-white text-xs font-medium" data-gradient="green" onclick="selectGradient('green')">
                            <div class="w-full h-8 rounded bg-gradient-to-tr from-green-200 to-green-400 mb-1"></div>
                            Green
                        </div>
                        <div class="gradient-option cursor-pointer rounded-lg p-2 text-center text-white text-xs font-medium" data-gradient="blue" onclick="selectGradient('blue')">
                            <div class="w-full h-8 rounded bg-gradient-to-tr from-blue-200 to-blue-400 mb-1"></div>
                            Blue
                        </div>
                        <div class="gradient-option cursor-pointer rounded-lg p-2 text-center text-white text-xs font-medium" data-gradient="purple" onclick="selectGradient('purple')">
                            <div class="w-full h-8 rounded bg-gradient-to-tr from-purple-200 to-purple-400 mb-1"></div>
                            Purple
                        </div>
                        <div class="gradient-option cursor-pointer rounded-lg p-2 text-center text-white text-xs font-medium" data-gradient="emerald" onclick="selectGradient('emerald')">
                            <div class="w-full h-8 rounded bg-gradient-to-tr from-emerald-200 to-emerald-400 mb-1"></div>
                            Emerald
                        </div>
                        <div class="gradient-option cursor-pointer rounded-lg p-2 text-center text-white text-xs font-medium" data-gradient="red" onclick="selectGradient('red')">
                            <div class="w-full h-8 rounded bg-gradient-to-tr from-red-200 to-red-400 mb-1"></div>
                            Red
                        </div>
                    </div>
                    <input type="hidden" id="color_gradient" name="color_gradient" value="orange">
                </div>
                
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="0" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">Active</label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        Save Story
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentStoryId = null;

function openCreateModal() {
    currentStoryId = null;
    document.getElementById('modalTitle').textContent = 'Add New Story';
    document.getElementById('storyForm').reset();
    document.getElementById('storyId').value = '';
    
    // Set default gradient
    selectGradient('orange');
    
    document.getElementById('storyModal').classList.remove('hidden');
}

function openEditModal(storyId) {
    currentStoryId = storyId;
    document.getElementById('modalTitle').textContent = 'Edit Story';
    
    // Fetch story data and populate form
    fetch(`/restaurant/{{ $restaurant->slug }}/stories/${storyId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const story = data.story;
                document.getElementById('storyId').value = story.id;
                document.getElementById('type').value = story.type;
                document.getElementById('title').value = story.title;
                document.getElementById('subtitle').value = story.subtitle || '';
                document.getElementById('content').value = story.content;
                document.getElementById('emoji').value = story.emoji || '';
                document.getElementById('sort_order').value = story.sort_order;
                document.getElementById('is_active').checked = story.is_active;
                
                // Set color gradient
                const colorGradient = story.color_gradient || 'orange';
                document.getElementById('color_gradient').value = colorGradient;
                selectGradient(colorGradient);
                
                document.getElementById('storyModal').classList.remove('hidden');
            } else {
                alert('Error loading story: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading story data');
        });
}

function closeModal() {
    document.getElementById('storyModal').classList.add('hidden');
}

function selectGradient(gradient) {
    // Remove active class from all gradient options
    document.querySelectorAll('.gradient-option').forEach(option => {
        option.classList.remove('ring-2', 'ring-orange-500', 'ring-offset-2');
    });
    
    // Add active class to selected gradient
    const selectedOption = document.querySelector(`[data-gradient="${gradient}"]`);
    if (selectedOption) {
        selectedOption.classList.add('ring-2', 'ring-orange-500', 'ring-offset-2');
    }
    
    // Update hidden input value
    document.getElementById('color_gradient').value = gradient;
}

function toggleStoryStatus(storyId) {
    if (confirm('Are you sure you want to toggle this story\'s status?')) {
        fetch(`/restaurant/{{ $restaurant->slug }}/stories/${storyId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating story status');
        });
    }
}

function deleteStory(storyId) {
    if (confirm('Are you sure you want to delete this story? This action cannot be undone.')) {
        fetch(`/restaurant/{{ $restaurant->slug }}/stories/${storyId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting story');
        });
    }
}

document.getElementById('storyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const storyId = document.getElementById('storyId').value;
    const url = storyId 
        ? `/restaurant/{{ $restaurant->slug }}/stories/${storyId}`
        : `/restaurant/{{ $restaurant->slug }}/stories`;
    
    // Get CSRF token from the form
    const csrfToken = document.querySelector('input[name="_token"]').value;
    
    // Log form data for debugging
    console.log('Form data being sent:');
    console.log('Story ID from form:', storyId);
    console.log('Current Story ID variable:', currentStoryId);
    console.log('URL being used:', url);
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Add _method field for Laravel method spoofing
    if (storyId) {
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST', // Always use POST, Laravel will handle the method spoofing
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Server response:', text);
                throw new Error('Server returned: ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success response:', data);
        if (data.success) {
            closeModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving story: ' + error.message);
    });
});

// Close modal when clicking outside
document.getElementById('storyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection 