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
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                @if($story->emoji)
                                    <span class="text-2xl mr-3">{{ $story->emoji }}</span>
                                @endif
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
    const url = currentStoryId 
        ? `/restaurant/{{ $restaurant->slug }}/stories/${currentStoryId}`
        : `/restaurant/{{ $restaurant->slug }}/stories`;
    
    // Get CSRF token from the form
    const csrfToken = document.querySelector('input[name="_token"]').value;
    
    // Log form data for debugging
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    fetch(url, {
        method: currentStoryId ? 'PUT' : 'POST',
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