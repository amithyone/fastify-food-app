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

        <!-- Main Container for Menu Management -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <!-- Two-Column Layout: Category Management (32%) + Menu Management (68%) -->
            <div class="restaurant-menu-container">
                
                <!-- Left Column: Category Management Widget (32% width) -->
                <div class="category-management-section">
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
                <div class="menu-management-section">
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
                                
                                <div class="menu-items-grid">
                                    @foreach($items as $item)
                                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 menu-item-card">
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
                               placeholder="e.g., Caesar Salad, Grilled Chicken, Chocolate Cake"
                               oninvalid="this.setCustomValidity('Please enter a sub-category name')"
                               oninput="this.setCustomValidity('')">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Smart Matching:</strong> We'll check if similar sub-categories exist and suggest sharing them to avoid duplication.
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="newCategoryParent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Parent Category *</label>
                        <select id="newCategoryParent" name="parent_id" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            oninvalid="this.setCustomValidity('Please select a parent category')"
                            onchange="this.setCustomValidity('')">
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
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors"
                            onclick="return validateCategoryForm()">
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


<script>
function validateCategoryForm() {
    // Get the selected category type
    const categoryType = document.querySelector('input[name="category_type"]:checked')?.value;
    
    if (!categoryType) {
        alert('Please select a category type (existing or custom)');
        return false;
    }
    
    // Remove required attributes from hidden fields to prevent browser validation
    if (categoryType === 'custom') {
        // Remove required from existing category form
        const existingForm = document.getElementById('existingCategoryForm');
        if (existingForm) {
            const requiredFields = existingForm.querySelectorAll('[required]');
            requiredFields.forEach(field => field.removeAttribute('required'));
        }
        
        // Validate custom category form
        const categoryName = document.getElementById('categoryName').value.trim();
        const parentCategory = document.getElementById('newCategoryParent').value;
        
        if (!categoryName) {
            alert('Please enter a sub-category name');
            document.getElementById('categoryName').focus();
            return false;
        }
        
        if (!parentCategory) {
            alert('Please select a parent category');
            document.getElementById('newCategoryParent').focus();
            return false;
        }
    } else if (categoryType === 'existing') {
        // Remove required from custom category form
        const customForm = document.getElementById('customCategoryForm');
        if (customForm) {
            const requiredFields = customForm.querySelectorAll('[required]');
            requiredFields.forEach(field => field.removeAttribute('required'));
        }
        
        // Validate existing category form
        const selectedCategory = document.querySelector('input[name="existing_category_id"]:checked');
        const parentCategory = document.getElementById('categoryParent').value;
        
        if (!selectedCategory) {
            alert('Please select an existing sub-category');
            return false;
        }
        
        if (!parentCategory) {
            alert('Please select a parent category');
            document.getElementById('categoryParent').focus();
            return false;
        }
    }
    
    return true;
}
</script>

@endsection 
