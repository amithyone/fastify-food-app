@extends('layouts.app')

@section('title', 'Manage Menu - ' . $restaurant->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 space-y-4 sm:space-y-0">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Manage Menu</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $restaurant->name }}</p>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                
                <!-- Dropdown Add Button -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="w-full sm:w-auto px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>Add
                        <i class="fas fa-chevron-down ml-2"></i>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 sm:right-auto left-0 sm:left-auto mt-2 w-full sm:w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                        <div class="py-1">
                            <button onclick="openMenuItemModal()" 
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-utensils mr-2"></i>Add Menu Item
                            </button>
                            <button onclick="openCategoryModal()" 
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-folder mr-2"></i>Add Category
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Two-Column Layout: Category Management (32%) + Menu Management (68%) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="display: grid; grid-template-columns: 32% 68%; gap: 1.5rem; width: 100%;">
            
            <!-- Left Column: Category Management Widget (32% width) -->
            <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-lg shadow" style="width: 100%; min-width: 100%; max-width: 100%;">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Categories</h2>
                        <button onclick="openCategoryModal()" class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 transition-colors">
                            <i class="fas fa-plus mr-1"></i>Add
                        </button>
                    </div>
                </div>
                
                <div class="p-4">
                    <div class="space-y-3">
                        @php
                            $globalParentCategories = $globalCategories;
                            $restaurantCategories = $allCategories->groupBy('parent_id');
                            // Only show categories as "unassigned" if they have a restaurant_id but no parent_id
                            $restaurantMainCategories = $allCategories->where('restaurant_id', '!=', null)
                                                                      ->where('parent_id', null)
                                                                      ->where('type', '!=', 'main'); // Exclude main categories
                        @endphp
                        
                        <!-- Global Main Categories -->
                        @foreach($globalParentCategories as $parentCategory)
                            @php
                                $subCategories = $restaurantCategories->get($parentCategory->id, collect());
                            @endphp
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-folder-open text-orange-500 mr-2 text-xs"></i>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">{{ $parentCategory->name }}</span>
                                        <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">(Global)</span>
                                    </div>
                                    <button onclick="openCategoryModal('{{ $parentCategory->id }}', '{{ $parentCategory->name }}')" 
                                            class="text-xs bg-blue-500 text-white px-1.5 py-0.5 rounded hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                
                                @if($subCategories->count() > 0)
                                    @foreach($subCategories as $category)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors ml-3">
                                            <div class="flex items-center min-w-0 flex-1">
                                                <i class="fas fa-folder text-blue-500 mr-2 text-xs"></i>
                                                <div class="min-w-0 flex-1">
                                                    <span class="text-xs font-medium text-gray-900 dark:text-white truncate block">
                                                        {{ $category->name }}
                                                        @if($category->isShared())
                                                            <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full ml-1">
                                                                <i class="fas fa-share-alt text-xs mr-0.5"></i>
                                                                Shared
                                                            </span>
                                                        @endif
                                                    </span>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $category->menuItems->count() }} items
                                                        @if($category->isShared())
                                                            • {{ count($category->restaurant_ids ?? []) }} restaurants
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-1 ml-2">
                                                @if($category->restaurant_id == $restaurant->id)
                                                    {{-- Only show edit button for categories created by this restaurant --}}
                                                    <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->parent_id }}')" class="text-gray-400 hover:text-blue-600" title="Edit category">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if(!$category->isShared())
                                                        {{-- Only show delete for non-shared categories created by this restaurant --}}
                                                        <button onclick="deleteCategory({{ $category->id }})" class="text-gray-400 hover:text-red-600" title="Delete category">
                                                            <i class="fas fa-trash text-xs"></i>
                                                        </button>
                                                    @else
                                                        {{-- For shared categories, only allow removal from this restaurant --}}
                                                        <button onclick="deactivateCategory({{ $category->id }})" class="text-gray-400 hover:text-red-600" title="Remove from this restaurant">
                                                            <i class="fas fa-times text-xs"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    {{-- For categories not created by this restaurant, only allow removal from this restaurant --}}
                                                    <button onclick="deactivateCategory({{ $category->id }})" class="text-gray-400 hover:text-red-600" title="Remove from this restaurant">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-1 ml-3">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">No sub-categories</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        
                        <!-- Unassigned Categories -->
                        @if($restaurantMainCategories->count() > 0)
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2 text-xs"></i>
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Unassigned</span>
                                    </div>
                                </div>
                                
                                @foreach($restaurantMainCategories as $category)
                                    <div class="flex items-center justify-between p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-800 ml-3">
                                        <div class="flex items-center min-w-0 flex-1">
                                            <i class="fas fa-folder text-yellow-500 mr-2 text-xs"></i>
                                            <div class="min-w-0 flex-1">
                                                <span class="text-xs font-medium text-gray-900 dark:text-white truncate block">{{ $category->name }}</span>
                                                <div class="text-xs text-yellow-600 dark:text-yellow-400">
                                                    Needs parent
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1 ml-2">
                                            <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '')" class="text-yellow-600 hover:text-yellow-800">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button onclick="deleteCategory({{ $category->id }})" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        @if($restaurantCategories->count() == 0)
                            <div class="text-center py-4">
                                <i class="fas fa-folder-open text-xl text-gray-400 mb-2"></i>
                                <p class="text-xs text-gray-500 dark:text-gray-400">No categories</p>
                                <button onclick="openCategoryModal()" class="mt-2 px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Add
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Menu Items Management (68% width) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow" style="width: 100%; min-width: 100%; max-width: 100%;">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Menu Items ({{ $menuItems->count() }})</h2>
                        <button onclick="openMenuItemModal()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>
                </div>
                
                @if($menuItems->count() > 0)
                    <div class="p-6 space-y-6">
                        @php
                            $menuItemsByParent = $menuItems->groupBy(function($item) {
                                return $item->category && $item->category->parent ? $item->category->parent->name : ($item->category ? 'Main Categories' : 'Uncategorized');
                            });
                        @endphp
                        
                        @foreach($menuItemsByParent as $parentName => $items)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                        @if($parentName === 'Main Categories')
                                            <i class="fas fa-folder-open text-green-500 mr-2"></i>
                                        @elseif($parentName === 'Uncategorized')
                                            <i class="fas fa-folder-open text-gray-500 mr-2"></i>
                                        @else
                                            <i class="fas fa-folder-open text-orange-500 mr-2"></i>
                                        @endif
                                        {{ $parentName }}
                                        <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">({{ $items->count() }} items)</span>
                                    </h3>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach($items as $item)
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                                        <div class="p-4">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex items-center min-w-0 flex-1">
                                                    <div class="flex-shrink-0 h-12 w-12 mr-3">
                                                        <img src="{{ $item->image_url }}" 
                                                             alt="{{ $item->name }}" 
                                                             class="h-12 w-12 rounded-lg object-cover"
                                                             onerror="this.src='{{ \App\Helpers\PWAHelper::getPlaceholderImage('square') }}'">
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                            {{ $item->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                            {{ $item->category->name ?? 'Uncategorized' }}
                                                        </div>
                                                        <div class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
                                                            {{ $restaurant->currency }}{{ number_format($item->price) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-1 ml-2">
                                                    <button onclick="editMenuItem({{ $item->id }}, '{{ $item->name }}', {{ $item->price }}, '{{ $item->description ?? '' }}', {{ $item->category_id ?? 'null' }}, {{ $item->is_available ? 'true' : 'false' }}, '{{ $item->image_url }}', '{{ $item->ingredients ?? '' }}', '{{ $item->allergens ?? '' }}', {{ $item->is_featured ? 'true' : 'false' }}, {{ $item->is_vegetarian ? 'true' : 'false' }}, {{ $item->is_spicy ? 'true' : 'false' }}, {{ $item->restaurant_image_id ?? 'null' }}, {{ $item->is_available_for_delivery ? 'true' : 'false' }}, {{ $item->is_available_for_pickup ? 'true' : 'false' }}, {{ $item->is_available_for_restaurant ? 'true' : 'false' }})" 
                                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    <button onclick="deleteMenuItem({{ $item->id }})" 
                                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <button onclick="toggleItemStatus({{ $item->id }}, {{ $item->is_available ? 'false' : 'true' }})" 
                                                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $item->is_available ? 'bg-green-600' : 'bg-gray-200' }}">
                                                        <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform {{ $item->is_available ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                                    </button>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $item->is_available ? 'Available' : 'Hidden' }}
                                                    </span>
                                                </div>
                                                
                                                <div class="flex items-center space-x-1">
                                                    @if($item->is_featured)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 rounded-full">
                                                            <i class="fas fa-star text-xs mr-0.5"></i>
                                                        </span>
                                                    @endif
                                                    @if($item->is_vegetarian)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                                            <i class="fas fa-leaf text-xs mr-0.5"></i>
                                                        </span>
                                                    @endif
                                                    @if($item->is_spicy)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                                            <i class="fas fa-fire text-xs mr-0.5"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-utensils text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Menu Items Yet</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">Start by adding your first menu item.</p>
                        <button onclick="openMenuItemModal()" 
                               class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add First Menu Item
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button (FAB) -->
<div class="fixed bottom-6 right-6 z-50" x-data="{ open: false }">
    <!-- Main FAB Button -->
    <button @click="open = !open" 
            class="w-14 h-14 bg-orange-500 hover:bg-orange-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110">
        <i class="fas fa-plus text-xl" :class="{ 'rotate-45': open }"></i>
    </button>
    
    <!-- FAB Menu Items -->
    <div x-show="open" @click.away="open = false" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute bottom-16 right-0 space-y-2">
        
        <!-- Add Menu Item -->
        <button onclick="openMenuItemModal(); open = false;" 
                class="w-12 h-12 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 transform hover:scale-110">
            <i class="fas fa-utensils text-sm"></i>
        </button>
        
        <!-- Add Category -->
        <button onclick="openCategoryModal(); open = false;" 
                class="w-12 h-12 bg-blue-500 hover:bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 transform hover:scale-110">
            <i class="fas fa-folder text-sm"></i>
        </button>
        
        <!-- Add Note -->
        <button onclick="addQuickNote(); open = false;" 
                class="w-12 h-12 bg-purple-500 hover:bg-purple-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 transform hover:scale-110">
            <i class="fas fa-sticky-note text-sm"></i>
        </button>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4" id="categoryModalTitle">Add Sub-Category</h3>
            
            <!-- Category Type Selection -->
            <div class="mb-6">
                <div class="flex space-x-4 mb-4">
                    <label class="flex items-center">
                        <input type="radio" name="category_type" value="existing" checked class="text-orange-600 focus:ring-orange-500 mr-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Existing Sub-Category</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="category_type" value="custom" class="text-orange-600 focus:ring-orange-500 mr-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Create New Sub-Category</span>
                    </label>
                </div>
                
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Select Existing:</strong> Choose from sub-categories already created by other restaurants<br>
                    <strong>Create New:</strong> Create a new sub-category (we'll check for similar ones to avoid duplication)
                </p>
            </div>
            
            <!-- Category Form -->
            <form id="categoryForm" class="space-y-4">
                @csrf
                <input type="hidden" name="use_existing_category" value="0">
                <input type="hidden" name="existing_category_id" value="">
                
                <!-- Existing Sub-Category Selection -->
                <div id="existingCategoryForm">
                    <div class="mb-4">
                        <label for="categoryParent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent Category *</label>
                        <select id="categoryParent" name="parent_id_existing" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">Select a parent category</option>
                            @foreach($globalCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }} (Global)</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Select the main category under which you want to add a sub-category
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Available Sub-Categories</label>
                        <div id="existingSubCategoriesList" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                <i class="fas fa-info-circle mb-2"></i>
                                <p>Select a parent category to see available sub-categories</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <i class="fas fa-share-alt mr-1"></i>
                            Click on a sub-category to use it for your restaurant. This will share it with other restaurants.
                        </p>
                    </div>
                </div>
                
                <!-- New Sub-Category Creation -->
                <div id="customCategoryForm" class="hidden">
                    <div class="mb-4">
                        <label for="categoryName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sub-Category Name</label>
                        <input type="text" id="categoryName" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                               placeholder="e.g., Caesar Salad, Grilled Chicken, Chocolate Cake">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Smart Matching:</strong> We'll check if similar sub-categories exist and suggest sharing them to avoid duplication.
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="newCategoryParent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent Category *</label>
                        <select id="newCategoryParent" name="parent_id_new"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">Select a parent category</option>
                            @foreach($globalCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }} (Global)</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Required: Select the main category under which to create this sub-category
                        </p>
                    </div>
                    
                    <!-- Force Create Option -->
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="force_create" value="1" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Force create new sub-category (skip smart matching)</span>
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-6">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Only use this if you're sure you want a completely new sub-category
                        </p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" onclick="closeCategoryModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        <span id="submitButtonText">Add Sub-Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Menu Item Modal -->
<div id="menuItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-6 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4" id="menuItemModalTitle">Add Menu Item</h3>
            <form id="menuItemForm" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="itemName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Item Name</label>
                        <input type="text" id="itemName" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="itemCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                        <select id="itemCategory" name="category_id" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">Select Category</option>
                            @foreach($allCategories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                    @if($category->restaurant_id === null)
                                        (Global)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="itemPrice" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price ({{ $restaurant->currency }})</label>
                        <input type="number" id="itemPrice" name="price" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Item Image</label>
                        
                        <!-- Image Selection Options -->
                        <div class="space-y-4">
                            <!-- Upload New Image -->
                            <div>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="image_source" value="upload" checked class="text-orange-600 focus:ring-orange-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Upload New Image</span>
                                </label>
                                <div id="uploadSection" class="mt-2">
                                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                                        <div class="space-y-1 text-center">
                                            <div class="flex flex-col items-center">
                                                <div id="imagePreview" class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-3">
                                                    <i class="fas fa-camera text-gray-400 text-2xl"></i>
                                                </div>
                                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                                    <label for="itemImage" class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                        <span>Upload a file</span>
                                                        <input id="itemImage" name="image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                                    </label>
                                                    <p class="pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    PNG, JPG, GIF up to 10MB
                                                </p>
                                            </div>
                                        </div>
                                    </div>
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
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Image Selection JavaScript - Placed close to the radio buttons -->
                    <script>
                        // Image selection functionality - placed close to the HTML elements
                        function setupImageSelectionHandlers() {
                            console.log('Setting up image selection handlers...');
                            const imageSourceRadios = document.querySelectorAll('input[name="image_source"]');
                            const uploadSection = document.getElementById('uploadSection');
                            const existingSection = document.getElementById('existingSection');
                            
                            console.log('Found radio buttons:', imageSourceRadios.length);
                            console.log('Upload section:', uploadSection);
                            console.log('Existing section:', existingSection);
                            
                            imageSourceRadios.forEach(radio => {
                                radio.addEventListener('change', function() {
                                    console.log('Radio button changed to:', this.value);
                                    if (this.value === 'upload') {
                                        uploadSection.classList.remove('hidden');
                                        existingSection.classList.add('hidden');
                                        clearSelectedImage();
                                    } else if (this.value === 'existing') {
                                        uploadSection.classList.add('hidden');
                                        existingSection.classList.remove('hidden');
                                        document.getElementById('itemImage').value = '';
                                        resetImagePreview();
                                    }
                                });
                            });
                        }
                        
                        // Open image selector modal
                        function openImageSelector() {
                            console.log('Opening image selector...');
                            const url = `{{ route('restaurant.images.get', ['slug' => $restaurant->slug]) }}`;
                            console.log('Fetching from URL:', url);
                            
                            fetch(url, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                credentials: 'same-origin'
                            })
                                .then(response => {
                                    console.log('Response status:', response.status);
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Received data:', data);
                                    if (data.success) {
                                        showImageSelectorModal(data.images);
                                    } else {
                                        alert('Failed to load images: ' + (data.message || 'Unknown error'));
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading images:', error);
                                    alert('Error loading images. Please try again. Error: ' + error.message);
                                });
                        }
                        
                        // Show image selector modal
                        function showImageSelectorModal(images) {
                            console.log('Creating modal with', images.length, 'images');
                            const modal = document.createElement('div');
                            modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
                            modal.id = 'imageSelectorModal';
                            
                            let imagesHtml = '';
                            if (images.length > 0) {
                                images.forEach(image => {
                                    imagesHtml += `
                                        <div class="cursor-pointer hover:scale-105 transition-transform p-2 border-2 border-transparent hover:border-orange-500 rounded-lg" onclick="selectImageFromModal('${image.url}', ${image.id}, '${image.original_name}')">
                                            <div class="relative">
                                                <img src="${image.thumbnail_url}?v=${Date.now()}" alt="${image.original_name}" 
                                                     class="w-full h-24 object-cover rounded-lg shadow-sm"
                                                     onload="console.log('Image loaded successfully:', '${image.original_name}')"
                                                     onerror="console.log('Image failed to load:', '${image.original_name}'); this.src='${image.url}?v=${Date.now()}'"
                                                     style="min-height: 96px;">
                                                <div class="absolute top-2 right-2 bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-200">
                                                    <i class="fas fa-check text-xs"></i>
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
                                <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                                    <div class="p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Image for Menu Item</h3>
                                            <button onclick="closeImageSelectorModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-4 gap-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
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
                            console.log('Modal created with grid layout');
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
                            setImagePreview(imageUrl);
                            
                            closeImageSelectorModal();
                        }
                        
                        // Clear selected image
                        function clearSelectedImage() {
                            document.getElementById('selectedImageId').value = '';
                            document.getElementById('selectedImagePath').value = '';
                            document.getElementById('selectedImageText').textContent = 'Choose from uploaded images';
                            resetImagePreview();
                        }
                        
                        // Close modal when clicking outside
                        document.addEventListener('click', function(e) {
                            const modal = document.getElementById('imageSelectorModal');
                            if (modal && e.target === modal) {
                                closeImageSelectorModal();
                            }
                        });
                        
                        // Setup handlers when this script loads
                        document.addEventListener('DOMContentLoaded', setupImageSelectionHandlers);
                        
                        // Also setup when modal opens (in case DOM is already loaded)
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', setupImageSelectionHandlers);
                        } else {
                            setupImageSelectionHandlers();
                        }
                    </script>
                    
                    <div>
                        <label for="itemDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea id="itemDescription" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    
                    <div>
                        <label for="itemIngredients" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ingredients</label>
                        <textarea id="itemIngredients" name="ingredients" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="List the main ingredients (e.g., rice, chicken, vegetables, spices)"></textarea>
                    </div>
                    
                    <div>
                        <label for="itemAllergens" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Allergens</label>
                        <textarea id="itemAllergens" name="allergens" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                  placeholder="List any allergens (e.g., nuts, dairy, gluten, shellfish)"></textarea>
                    </div>
                    
                    <!-- Item Options -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Item Options</h4>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="itemAvailable" name="is_available" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="itemAvailable" class="ml-2 block text-sm text-gray-900 dark:text-white">Available for Order</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="itemFeatured" name="is_featured" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                            <label for="itemFeatured" class="ml-2 block text-sm text-gray-900 dark:text-white">Featured Item</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="itemVegetarian" name="is_vegetarian" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <label for="itemVegetarian" class="ml-2 block text-sm text-gray-900 dark:text-white">Vegetarian</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="itemSpicy" name="is_spicy" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="itemSpicy" class="ml-2 block text-sm text-gray-900 dark:text-white">Spicy</label>
                        </div>
                        
                        <!-- Delivery Method Options -->
                        <div class="space-y-2 mt-4">
                            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Methods</h5>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="itemAvailableForDelivery" name="is_available_for_delivery" value="1" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="itemAvailableForDelivery" class="ml-2 block text-sm text-gray-900 dark:text-white">Available for Delivery</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="itemAvailableForPickup" name="is_available_for_pickup" value="1" checked class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="itemAvailableForPickup" class="ml-2 block text-sm text-gray-900 dark:text-white">Available for Pickup</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="itemAvailableForRestaurant" name="is_available_for_restaurant" value="1" checked class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                <label for="itemAvailableForRestaurant" class="ml-2 block text-sm text-gray-900 dark:text-white">Available for In-Restaurant</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeMenuItemModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>Save Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Similar Categories Modal -->
<div id="similarCategoriesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Similar Categories Found</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                We found similar categories that already exist. You can either use one of these or create a new category.
            </p>
            
            <div id="similarCategoriesList" class="space-y-3 mb-6">
                <!-- Similar categories will be populated here -->
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button onclick="closeSimilarCategoriesModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="forceCreateCategory()" 
                        class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    Create New Category
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Note Modal -->
<div id="quickNoteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Note</h3>
            <div class="space-y-4">
                <div>
                    <label for="noteTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note Title</label>
                    <input type="text" id="noteTitle" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="noteContent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note Content</label>
                    <textarea id="noteContent" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeQuickNoteModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button onclick="saveQuickNote()" 
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        Save Note
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Widget Management JavaScript -->
<script>
let draggedWidget = null;
let editingCategoryId = null;
let editingMenuItemId = null;

function toggleWidget(button) {
    const widget = button.closest('.widget');
    const content = widget.querySelector('.widget-content');
    const icon = button.querySelector('i');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.className = 'fas fa-chevron-up';
    } else {
        content.style.display = 'none';
        icon.className = 'fas fa-chevron-down';
    }
}

function startDrag(button) {
    const widget = button.closest('.widget');
    widget.style.cursor = 'grabbing';
    widget.style.opacity = '0.8';
    draggedWidget = widget;
    
    document.addEventListener('mousemove', onDrag);
    document.addEventListener('mouseup', stopDrag);
}

function onDrag(e) {
    if (!draggedWidget) return;
    
    const container = document.getElementById('widgets-container');
    const widgets = Array.from(container.children);
    const mouseY = e.clientY;
    
    const targetWidget = widgets.find(widget => {
        const rect = widget.getBoundingClientRect();
        return mouseY >= rect.top && mouseY <= rect.bottom;
    });
    
    if (targetWidget && targetWidget !== draggedWidget) {
        const draggedIndex = widgets.indexOf(draggedWidget);
        const targetIndex = widgets.indexOf(targetWidget);
        
        if (draggedIndex < targetIndex) {
            targetWidget.parentNode.insertBefore(draggedWidget, targetWidget.nextSibling);
        } else {
            targetWidget.parentNode.insertBefore(draggedWidget, targetWidget);
        }
    }
}

function stopDrag() {
    if (draggedWidget) {
        draggedWidget.style.cursor = '';
        draggedWidget.style.opacity = '';
        draggedWidget = null;
    }
    
    document.removeEventListener('mousemove', onDrag);
    document.removeEventListener('mouseup', stopDrag);
}

// Category Management
function openCategoryModal(parentId = '', parentName = '') {
    editingCategoryId = null;
    
    // Reset form
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryParent').value = '';
    document.getElementById('newCategoryParent').value = '';
    document.querySelector('input[name="use_existing_category"]').value = '0';
    document.querySelector('input[name="existing_category_id"]').value = '';
    
    // Reset category type selection
    document.querySelector('input[name="category_type"][value="existing"]').checked = true;
    showExistingCategoryForm();
    
    // Ensure required attributes are properly set
    document.getElementById('newCategoryParent').removeAttribute('required');
    
    // Clear existing category selection
    clearExistingCategorySelection();
    
    if (parentId && parentName) {
        document.getElementById('categoryModalTitle').textContent = `Add Sub-Category to ${parentName}`;
        document.getElementById('categoryParent').value = parentId;
        document.getElementById('newCategoryParent').value = parentId;
        document.getElementById('categoryParent').disabled = true;
        document.getElementById('newCategoryParent').disabled = true;
        // Load sub-categories for this parent
        loadExistingSubCategories(parentId);
    } else {
        document.getElementById('categoryModalTitle').textContent = 'Add Sub-Category';
        document.getElementById('categoryParent').value = '';
        document.getElementById('newCategoryParent').value = '';
        document.getElementById('categoryParent').disabled = false;
        document.getElementById('newCategoryParent').disabled = false;
        // Clear sub-categories list
        clearExistingSubCategoriesList();
    }
    
    document.getElementById('categoryModal').classList.remove('hidden');
}

function showExistingCategoryForm() {
    document.getElementById('existingCategoryForm').classList.remove('hidden');
    document.getElementById('customCategoryForm').classList.add('hidden');
    document.getElementById('categoryName').required = false;
    document.getElementById('newCategoryParent').removeAttribute('required');
    document.getElementById('submitButtonText').textContent = 'Select Sub-Category';
}

function showCustomCategoryForm() {
    document.getElementById('existingCategoryForm').classList.add('hidden');
    document.getElementById('customCategoryForm').classList.remove('hidden');
    document.getElementById('categoryName').required = true;
    document.getElementById('newCategoryParent').setAttribute('required', 'required');
    document.getElementById('submitButtonText').textContent = 'Create Sub-Category';
}

function loadExistingSubCategories(parentId) {
    const listContainer = document.getElementById('existingSubCategoriesList');
    
    // Filter existing sub-categories by parent
    const subCategories = @json($existingSubCategories).filter(cat => cat.parent_id == parentId);
    
    if (subCategories.length === 0) {
        listContainer.innerHTML = `
            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                <i class="fas fa-info-circle mb-2"></i>
                <p>No existing sub-categories found for this parent category</p>
                <p class="text-xs mt-1">You can create a new one using the "Create New Sub-Category" option</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    subCategories.forEach(category => {
        const isShared = category.is_shared || false;
        const restaurantCount = category.restaurant_ids ? category.restaurant_ids.length : 1;
        
        html += `
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                 onclick="selectExistingCategory(${category.id}, '${category.name}')">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">${category.name}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            ${isShared ? `Shared by ${restaurantCount} restaurants` : 'Single restaurant'}
                        </div>
                    </div>
                    <div class="text-green-500">
                        <i class="fas fa-check-circle hidden" id="selected-existing-${category.id}"></i>
                    </div>
                </div>
            </div>
        `;
    });
    
    listContainer.innerHTML = html;
}

function clearExistingSubCategoriesList() {
    const listContainer = document.getElementById('existingSubCategoriesList');
    listContainer.innerHTML = `
        <div class="text-center text-gray-500 dark:text-gray-400 py-4">
            <i class="fas fa-info-circle mb-2"></i>
            <p>Select a parent category to see available sub-categories</p>
        </div>
    `;
}

function selectExistingCategory(categoryId, categoryName) {
    console.log('Selecting existing category:', { categoryId, categoryName });
    
    // Clear previous selection
    clearExistingCategorySelection();
    
    // Set the selected category
    const useExistingInput = document.querySelector('input[name="use_existing_category"]');
    const existingCategoryInput = document.querySelector('input[name="existing_category_id"]');
    
    useExistingInput.value = '1';
    existingCategoryInput.value = categoryId;
    
    // Also set the parent_id to avoid form validation errors
    const parentSelect = document.getElementById('categoryParent');
    if (parentSelect && parentSelect.value) {
        // The parent_id is already set from the dropdown selection
        console.log('Parent ID already set:', parentSelect.value);
    }
    
    console.log('Set form values:', { 
        useExisting: useExistingInput.value, 
        existingCategoryId: existingCategoryInput.value,
        parentId: parentSelect ? parentSelect.value : 'not set'
    });
    
    // Show selection indicator
    const selectedIcon = document.getElementById(`selected-existing-${categoryId}`);
    if (selectedIcon) {
        selectedIcon.classList.remove('hidden');
    }
    
    // Update submit button text
    document.getElementById('submitButtonText').textContent = `Use "${categoryName}"`;
    
    console.log('Category selection completed');
}

function clearExistingCategorySelection() {
    // Clear all selection indicators
    const selectedIcons = document.querySelectorAll('[id^="selected-existing-"]');
    selectedIcons.forEach(icon => icon.classList.add('hidden'));
    
    // Clear form values
    document.querySelector('input[name="use_existing_category"]').value = '0';
    document.querySelector('input[name="existing_category_id"]').value = '';
}

// Similar Categories Functions
function showSimilarCategoriesModal(similarCategories) {
    const modal = document.getElementById('similarCategoriesModal');
    const listContainer = document.getElementById('similarCategoriesList');
    
    // Clear previous content
    listContainer.innerHTML = '';
    
    // Add similar categories to the list
    similarCategories.forEach(category => {
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors';
        categoryDiv.onclick = () => useSimilarCategory(category.id, category.name);
        
        categoryDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium text-gray-900 dark:text-white">${category.name}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        ${category.is_shared ? `Shared by ${category.restaurant_count} restaurants` : 'Single restaurant category'}
                    </div>
                </div>
                <div class="text-green-500">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>
        `;
        
        listContainer.appendChild(categoryDiv);
    });
    
    modal.classList.remove('hidden');
}

function closeSimilarCategoriesModal() {
    document.getElementById('similarCategoriesModal').classList.add('hidden');
}

function useSimilarCategory(categoryId, categoryName) {
    // Add the restaurant to the shared category
    fetch(`{{ route('restaurant.categories.share', ['slug' => $restaurant->slug ?? 'restaurant']) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            category_id: categoryId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeSimilarCategoriesModal();
            closeCategoryModal();
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sharing category');
    });
}

function forceCreateCategory() {
    // Set force create flag and submit the form
    document.querySelector('input[name="force_create"]').value = '1';
    closeSimilarCategoriesModal();
    
    // Submit the category form
    document.getElementById('categoryForm').dispatchEvent(new Event('submit'));
}

function removeFromSharedCategory(categoryId) {
    if (confirm('Are you sure you want to remove this category from your restaurant? This will not delete the category, just remove it from your restaurant.')) {
        fetch(`{{ route('restaurant.categories.unshare', ['slug' => $restaurant->slug ?? 'restaurant']) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                category_id: categoryId
            })
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
            alert('Error removing category');
        });
    }
}

function deactivateCategory(categoryId) {
    if (confirm('Are you sure you want to remove this category from your restaurant? This will completely remove it from your menu but won\'t affect other restaurants using it.')) {
        fetch(`{{ route('restaurant.categories.deactivate', ['slug' => $restaurant->slug ?? 'restaurant']) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                category_id: categoryId
            })
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
            alert('Error deactivating category');
        });
    }
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function editCategory(id, name, parentId = '') {
    console.log('Editing category:', { id, name, parentId });
    editingCategoryId = id;
    document.getElementById('categoryModalTitle').textContent = 'Edit Category';
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryParent').value = parentId || '';
    document.getElementById('categoryModal').classList.remove('hidden');
    console.log('Category modal opened for editing');
}

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        fetch(`{{ route('restaurant.categories.destroy', ['slug' => $restaurant->slug, 'category' => 'CATEGORY_ID']) }}`.replace('CATEGORY_ID', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then(response => {
            return response.json().then(data => {
                console.log('Delete response:', data);
                if (response.ok) {
                    console.log('Category deleted successfully, reloading page...');
                    setTimeout(() => {
                        window.location.href = window.location.href + '?t=' + Date.now();
                    }, 100);
                } else {
                    console.error('Error response:', data);
                    alert('Error deleting category: ' + (data.message || 'Unknown error'));
                }
            });
        }).catch(error => {
            console.error('Error:', error);
            alert('Error deleting category');
        });
    }
}

function deactivateCategory(id) {
    if (confirm('Are you sure you want to remove this category from your restaurant? This will completely remove it from your menu but won\'t affect other restaurants using it.')) {
        fetch(`{{ route('restaurant.categories.deactivate', ['slug' => $restaurant->slug]) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                category_id: id
            })
        }).then(response => {
            return response.json().then(data => {
                console.log('Deactivate response:', data);
                if (response.ok) {
                    console.log('Category removed successfully, reloading page...');
                    setTimeout(() => {
                        window.location.href = window.location.href + '?t=' + Date.now();
                    }, 100);
                } else {
                    console.error('Error response:', data);
                    alert('Error removing category: ' + (data.message || 'Unknown error'));
                }
            });
        }).catch(error => {
            console.error('Error:', error);
            alert('Error removing category');
        });
    }
}

// Menu Item Management
function openMenuItemModal() {
    editingMenuItemId = null;
    document.getElementById('menuItemModalTitle').textContent = 'Add Menu Item';
    document.getElementById('itemName').value = '';
    document.getElementById('itemCategory').value = '';
    document.getElementById('itemPrice').value = '';
    document.getElementById('itemDescription').value = '';
    document.getElementById('itemIngredients').value = '';
    document.getElementById('itemAllergens').value = '';
    document.getElementById('itemAvailable').checked = true;
    document.getElementById('itemFeatured').checked = false;
    document.getElementById('itemVegetarian').checked = false;
    document.getElementById('itemSpicy').checked = false;
    
    // Set delivery methods to checked by default
    document.getElementById('itemAvailableForDelivery').checked = true;
    document.getElementById('itemAvailableForPickup').checked = true;
    document.getElementById('itemAvailableForRestaurant').checked = true;
    
    resetImagePreview();
    document.getElementById('menuItemModal').classList.remove('hidden');
}

function closeMenuItemModal() {
    document.getElementById('menuItemModal').classList.add('hidden');
}

function editMenuItem(id, name, price, description, categoryId, isAvailable, imageUrl = null, ingredients = '', allergens = '', isFeatured = false, isVegetarian = false, isSpicy = false, restaurantImageId = null, isAvailableForDelivery = true, isAvailableForPickup = true, isAvailableForRestaurant = true) {
    editingMenuItemId = id;
    document.getElementById('menuItemModalTitle').textContent = 'Edit Menu Item';
    document.getElementById('itemName').value = name;
    document.getElementById('itemCategory').value = categoryId || '';
    document.getElementById('itemPrice').value = price;
    document.getElementById('itemDescription').value = description || '';
    document.getElementById('itemIngredients').value = ingredients || '';
    document.getElementById('itemAllergens').value = allergens || '';
    document.getElementById('itemAvailable').checked = isAvailable;
    document.getElementById('itemFeatured').checked = isFeatured;
    document.getElementById('itemVegetarian').checked = isVegetarian;
    document.getElementById('itemSpicy').checked = isSpicy;
    
    // Set delivery method checkboxes
    document.getElementById('itemAvailableForDelivery').checked = isAvailableForDelivery;
    document.getElementById('itemAvailableForPickup').checked = isAvailableForPickup;
    document.getElementById('itemAvailableForRestaurant').checked = isAvailableForRestaurant;
    
    // Handle image preview for editing
    if (restaurantImageId && restaurantImageId !== 'null') {
        // Set up for existing restaurant image
        document.querySelector('input[name="image_source"][value="existing"]').checked = true;
        document.getElementById('uploadSection').classList.add('hidden');
        document.getElementById('existingSection').classList.remove('hidden');
        document.getElementById('selectedImageId').value = restaurantImageId;
        document.getElementById('selectedImageText').textContent = 'Image selected from gallery';
        
        if (imageUrl) {
            setImagePreview(imageUrl);
        }
    } else if (imageUrl) {
        // Set up for uploaded image
        document.querySelector('input[name="image_source"][value="upload"]').checked = true;
        document.getElementById('uploadSection').classList.remove('hidden');
        document.getElementById('existingSection').classList.add('hidden');
        setImagePreview(imageUrl);
    } else {
        // No image
        document.querySelector('input[name="image_source"][value="upload"]').checked = true;
        document.getElementById('uploadSection').classList.remove('hidden');
        document.getElementById('existingSection').classList.add('hidden');
        resetImagePreview();
    }
    
    document.getElementById('menuItemModal').classList.remove('hidden');
}

function previewImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover rounded-lg">`;
        };
        reader.readAsDataURL(file);
    } else {
        resetImagePreview();
    }
}

function setImagePreview(imageUrl) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = `<img src="${imageUrl}" alt="Current Image" class="w-full h-full object-cover rounded-lg">`;
}

function resetImagePreview() {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = `<i class="fas fa-camera text-gray-400 text-2xl"></i>`;
}

function toggleItemStatus(id, newStatus) {
    // Implement status toggle
    console.log('Toggle item status:', id, newStatus);
}

function deleteMenuItem(id) {
    if (confirm('Are you sure you want to delete this menu item?')) {
        fetch(`{{ route('restaurant.menu.destroy', ['slug' => $restaurant->slug, 'item' => 'ITEM_ID']) }}`.replace('ITEM_ID', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Error deleting menu item');
            }
        });
    }
}

// Quick Actions
function importMenu() {
    // Create file input for CSV import
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.csv';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            console.log('Importing CSV:', file.name);
            // Implement CSV import logic
            alert('CSV import feature coming soon!');
        }
    };
    input.click();
}

function exportMenu() {
    console.log('Exporting menu...');
    // Implement menu export logic
    alert('Menu export feature coming soon!');
}

function bulkPriceUpdate() {
    console.log('Opening bulk price update...');
    // Implement bulk price update
    alert('Bulk price update feature coming soon!');
}

function menuTemplates() {
    console.log('Opening menu templates...');
    // Implement menu templates
    alert('Menu templates feature coming soon!');
}

// Kitchen Notes
function saveKitchenNotes() {
    const notes = document.getElementById('kitchenNotes').value;
    console.log('Saving kitchen notes:', notes);
    // Implement save kitchen notes
    alert('Kitchen notes saved!');
}

// Quick Notes
function addQuickNote() {
    document.getElementById('quickNoteModal').classList.remove('hidden');
}

function closeQuickNoteModal() {
    document.getElementById('quickNoteModal').classList.add('hidden');
}

function saveQuickNote() {
    const title = document.getElementById('noteTitle').value;
    const content = document.getElementById('noteContent').value;
    console.log('Saving quick note:', { title, content });
    // Implement save quick note
    alert('Quick note saved!');
    closeQuickNoteModal();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const dragButtons = document.querySelectorAll('.widget-drag');
    dragButtons.forEach(button => {
        button.addEventListener('touchstart', function(e) {
            e.preventDefault();
            startDrag(this);
        });
    });
    
    // Category type selection handlers
    document.querySelectorAll('input[name="category_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'custom') {
                showCustomCategoryForm();
            } else if (this.value === 'existing') {
                showExistingCategoryForm();
            }
        });
    });
    
    // Parent category selection handler for existing sub-categories
    document.getElementById('categoryParent').addEventListener('change', function() {
        const parentId = this.value;
        if (parentId) {
            loadExistingSubCategories(parentId);
        } else {
            clearExistingSubCategoriesList();
        }
    });
    
    // Category form submission
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Category form submitted');
        console.log('Editing category ID:', editingCategoryId);
        
        // Check if we're using an existing category before form validation
        const useExistingCategory = e.target.querySelector('input[name="use_existing_category"]').value;
        const existingCategoryId = e.target.querySelector('input[name="existing_category_id"]').value;
        
        console.log('Pre-validation check:', { useExistingCategory, existingCategoryId });
        
        // If using existing category, bypass form validation and submit directly
        if (useExistingCategory === '1' && existingCategoryId) {
            console.log('Bypassing form validation for existing category');
            
            console.log('Sending share request with category_id:', existingCategoryId);
            fetch(`{{ route('restaurant.categories.share', ['slug' => $restaurant->slug]) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    category_id: existingCategoryId
                })
            }).then(response => {
                console.log('Share response status:', response.status);
                return response.json().then(data => {
                    console.log('Share response data:', data);
                    if (response.ok) {
                        console.log('Category shared successfully');
                        console.log('Reloading page to show new category...');
                        setTimeout(() => {
                            window.location.reload();
                        }, 500); // Small delay to ensure the database update is complete
                    } else {
                        console.error('Error response:', data);
                        alert('Error using category: ' + (data.message || 'Unknown error'));
                    }
                });
            }).catch(error => {
                console.error('Fetch error:', error);
                alert('Error using category: ' + error.message);
            });
            
            closeCategoryModal();
            return;
        }
        
        // For new category creation, ensure parent_id is properly set
        const parentIdField = document.querySelector('select[name="parent_id_new"]');
        const isCustomForm = document.getElementById('customCategoryForm').classList.contains('hidden') === false;
        
        if (isCustomForm && parentIdField && !parentIdField.value) {
            console.log('Parent ID is required but not set');
            parentIdField.focus();
            alert('Please select a parent category');
            return;
        }
        
        // Copy the parent_id_new value to parent_id for the backend
        if (parentIdField && parentIdField.value) {
            const hiddenParentId = document.createElement('input');
            hiddenParentId.type = 'hidden';
            hiddenParentId.name = 'parent_id';
            hiddenParentId.value = parentIdField.value;
            e.target.appendChild(hiddenParentId);
        }
        
        const formData = new FormData(e.target);
        
        // Log form data
        console.log('Form data:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Check if we're using an existing category (this should not be reached for existing categories)
        const useExistingCategoryCheck = formData.get('use_existing_category');
        const existingCategoryIdCheck = formData.get('existing_category_id');
        
        console.log('Form submission check:', { useExistingCategoryCheck, existingCategoryIdCheck });
        
        // This section should only handle new category creation and editing
        if (useExistingCategoryCheck === '1' && existingCategoryIdCheck) {
            console.log('Unexpected: Existing category logic reached in form data section');
            return;
        } else if (editingCategoryId) {
            // Update existing category
            console.log('Updating category with ID:', editingCategoryId);
            
            // Add the _method field for PUT request
            formData.append('_method', 'PUT');
            
            fetch(`{{ route('restaurant.categories.update', ['slug' => $restaurant->slug, 'category' => 'CATEGORY_ID']) }}`.replace('CATEGORY_ID', editingCategoryId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            }).then(response => {
                console.log('Update response status:', response.status);
                return response.json().then(data => {
                    console.log('Update response data:', data);
                    if (response.ok) {
                        console.log('Category updated successfully');
                        window.location.reload();
                    } else {
                        console.error('Error response:', data);
                        alert('Error updating category: ' + (data.message || 'Unknown error'));
                    }
                });
            }).catch(error => {
                console.error('Fetch error:', error);
                alert('Error updating category: ' + error.message);
            });
        } else {
            // Create new category
            console.log('Creating new category with form data:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            fetch(`{{ route('restaurant.categories.store', ['slug' => $restaurant->slug]) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            }).then(response => {
                return response.json().then(data => {
                    if (response.ok) {
                        window.location.reload();
                    } else if (response.status === 422 && data.suggest_sharing) {
                        // Show similar categories modal
                        showSimilarCategoriesModal(data.similar_categories);
                    } else {
                        console.error('Error response:', data);
                        alert('Error creating category: ' + (data.message || 'Unknown error'));
                    }
                });
            }).catch(error => {
                console.error('Fetch error:', error);
                alert('Error creating category');
            });
        }
        closeCategoryModal();
    });
    
    // Menu item form submission
    document.getElementById('menuItemForm').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Menu item form submitted');
        
        const formData = new FormData(e.target);
        
        // Log form data for debugging
        console.log('Form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Use price as-is (no conversion)
        const priceField = document.getElementById('itemPrice');
        if (priceField && priceField.value) {
            const price = parseFloat(priceField.value);
            formData.set('price', price);
            console.log('Price set as:', price);
        }
        
        if (editingMenuItemId) {
            // Update existing menu item
            console.log('Updating menu item:', editingMenuItemId);
            
            // Add the _method field for PUT request
            formData.append('_method', 'PUT');
            
            fetch(`{{ route('restaurant.menu.update', ['slug' => $restaurant->slug, 'item' => 'ITEM_ID']) }}`.replace('ITEM_ID', editingMenuItemId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            }).then(response => {
                console.log('Update response status:', response.status);
                if (response.ok) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        console.error('Error response:', text);
                        throw new Error('Update failed - Server returned: ' + text.substring(0, 100));
                    });
                }
            }).then(data => {
                console.log('Update success:', data);
                window.location.reload();
            }).catch(error => {
                console.error('Update error:', error);
                alert('Error updating menu item: ' + error.message);
            });
        } else {
            // Create new menu item
            console.log('Creating new menu item');
            fetch(`{{ route('restaurant.menu.store', ['slug' => $restaurant->slug]) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            }).then(response => {
                console.log('Create response status:', response.status);
                if (response.ok) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        console.error('Error response:', text);
                        throw new Error('Creation failed - Server returned: ' + text.substring(0, 100));
                    });
                }
            }).then(data => {
                console.log('Create success:', data);
                window.location.reload();
            }).catch(error => {
                console.error('Create error:', error);
                alert('Error creating menu item: ' + error.message + '\nIf no image was selected, your restaurant default may be used automatically (Premium/Trial).');
            });
        }
        closeMenuItemModal();
    });
    

    
    // Open image selector modal
    function openImageSelector() {
        console.log('Opening image selector...');
        const url = `{{ route('restaurant.images.get', ['slug' => $restaurant->slug]) }}`;
        console.log('Fetching from URL:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                if (data.success) {
                    showImageSelectorModal(data.images);
                } else {
                    alert('Failed to load images: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error loading images:', error);
                alert('Error loading images. Please try again. Error: ' + error.message);
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
        setImagePreview(imageUrl);
        
        closeImageSelectorModal();
    }
    
    // Clear selected image
    function clearSelectedImage() {
        document.getElementById('selectedImageId').value = '';
        document.getElementById('selectedImagePath').value = '';
        document.getElementById('selectedImageText').textContent = 'Choose from uploaded images';
        resetImagePreview();
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('imageSelectorModal');
        if (modal && e.target === modal) {
            closeImageSelectorModal();
        }
    });
});
</script>
@endsection 