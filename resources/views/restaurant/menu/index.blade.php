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

        <!-- 3-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-2">
            
            <!-- Column 1: Category Management -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Categories</h2>
                        <button onclick="openCategoryModal()" class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                            <i class="fas fa-plus mr-1"></i>Add
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($categories as $category)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center">
                                    <i class="fas fa-folder text-blue-500 mr-3"></i>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $category->menuItems->count() }} items
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}')" class="text-gray-400 hover:text-blue-600">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button onclick="deleteCategory({{ $category->id }})" class="text-gray-400 hover:text-red-600">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($categories->count() == 0)
                            <div class="text-center py-8">
                                <i class="fas fa-folder-open text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">No categories yet</p>
                                <button onclick="openCategoryModal()" class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                    <i class="fas fa-plus mr-1"></i>Create First Category
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Column 2: Menu Items Management (Fixed Center) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Menu Items ({{ $menuItems->count() }})</h2>
                </div>
                
                @if($menuItems->count() > 0)
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/3">
                                        Item
                                    </th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/6">
                                        Price
                                    </th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/6">
                                        Status
                                    </th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-1/6">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($menuItems as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-3 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    @if($item->image)
                                                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" 
                                                             class="h-8 w-8 rounded-lg object-cover">
                                                    @else
                                                        <img src="{{ \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" 
                                                             alt="{{ $item->name }}" class="h-8 w-8 rounded-lg object-cover">
                                                    @endif
                                                </div>
                                                <div class="ml-2 min-w-0 flex-1">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                        {{ $item->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                        {{ $item->category->name ?? 'Uncategorized' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $restaurant->currency }}{{ number_format($item->price / 100, 2) }}
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <button onclick="toggleItemStatus({{ $item->id }}, {{ $item->is_available ? 'false' : 'true' }})" 
                                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $item->is_available ? 'bg-green-600' : 'bg-gray-200' }}">
                                                    <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform {{ $item->is_available ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                                </button>
                                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $item->is_available ? 'On' : 'Off' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-2 py-3 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-1">
                                                <button onclick="editMenuItem({{ $item->id }}, '{{ $item->name }}', {{ $item->price }}, '{{ $item->description ?? '' }}', {{ $item->category_id ?? 'null' }}, {{ $item->is_available ? 'true' : 'false' }}, '{{ $item->image ? Storage::url($item->image) : null }}' )" 
                                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                <button onclick="deleteMenuItem({{ $item->id }})" 
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

            <!-- Column 3: Flexible Widgets -->
            <div class="space-y-6">
                <!-- Widget Container -->
                <div id="widgets-container" class="space-y-6">
                    
                    <!-- Top Selling Items Widget -->
                    <div class="widget bg-white dark:bg-gray-800 rounded-lg shadow p-6" data-widget="top-selling">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Selling Items</h3>
                            <div class="flex items-center space-x-2">
                                <button class="widget-toggle text-gray-400 hover:text-gray-600" onclick="toggleWidget(this)">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <button class="widget-drag text-gray-400 hover:text-gray-600 cursor-move" onclick="startDrag(this)">
                                    <i class="fas fa-grip-vertical"></i>
                                </button>
                            </div>
                        </div>
                        <div class="widget-content">
                            <div class="space-y-3">
                                @foreach($menuItems->take(5) as $index => $item)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-2">#{{ $index + 1 }}</span>
                                            <div class="w-8 h-8 rounded-lg overflow-hidden mr-3">
                                                @if($item->image)
                                                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <img src="{{ \App\Helpers\PWAHelper::getPlaceholderImage('square') }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                                @endif
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</span>
                                        </div>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $restaurant->currency }}{{ number_format($item->price / 100, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Widget -->
                    <div class="widget bg-white dark:bg-gray-800 rounded-lg shadow p-6" data-widget="quick-actions">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                            <div class="flex items-center space-x-2">
                                <button class="widget-toggle text-gray-400 hover:text-gray-600" onclick="toggleWidget(this)">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <button class="widget-drag text-gray-400 hover:text-gray-600 cursor-move" onclick="startDrag(this)">
                                    <i class="fas fa-grip-vertical"></i>
                                </button>
                            </div>
                        </div>
                        <div class="widget-content">
                            <div class="space-y-3">
                                <button onclick="importMenu()" class="w-full text-left p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-upload text-blue-500 mr-3"></i>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Import Menu (CSV)</span>
                                    </div>
                                </button>
                                <button onclick="exportMenu()" class="w-full text-left p-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-download text-green-500 mr-3"></i>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Export Menu</span>
                                    </div>
                                </button>
                                <button onclick="bulkPriceUpdate()" class="w-full text-left p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-tags text-purple-500 mr-3"></i>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Bulk Price Update</span>
                                    </div>
                                </button>
                                <button onclick="menuTemplates()" class="w-full text-left p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                                    <div class="flex items-center">
                                        <i class="fas fa-layer-group text-orange-500 mr-3"></i>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Menu Templates</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Health Widget -->
                    <div class="widget bg-white dark:bg-gray-800 rounded-lg shadow p-6" data-widget="menu-health">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Menu Health</h3>
                            <div class="flex items-center space-x-2">
                                <button class="widget-toggle text-gray-400 hover:text-gray-600" onclick="toggleWidget(this)">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <button class="widget-drag text-gray-400 hover:text-gray-600 cursor-move" onclick="startDrag(this)">
                                    <i class="fas fa-grip-vertical"></i>
                                </button>
                            </div>
                        </div>
                        <div class="widget-content">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Items</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $menuItems->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Available</span>
                                    <span class="text-lg font-bold text-green-600">{{ $menuItems->where('is_available', true)->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Categories</span>
                                    <span class="text-lg font-bold text-blue-600">{{ $categories->count() }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    @php
                                        $healthScore = $menuItems->count() > 0 ? 
                                            ($menuItems->where('is_available', true)->count() / $menuItems->count()) * 100 : 0;
                                    @endphp
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $healthScore }}%"></div>
                                </div>
                                <div class="text-center">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Health Score: {{ number_format($healthScore, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Alerts Widget -->
                    <div class="widget bg-white dark:bg-gray-800 rounded-lg shadow p-6" data-widget="inventory-alerts">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Inventory Alerts</h3>
                            <div class="flex items-center space-x-2">
                                <button class="widget-toggle text-gray-400 hover:text-gray-600" onclick="toggleWidget(this)">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <button class="widget-drag text-gray-400 hover:text-gray-600 cursor-move" onclick="startDrag(this)">
                                    <i class="fas fa-grip-vertical"></i>
                                </button>
                            </div>
                        </div>
                        <div class="widget-content">
                            <div class="space-y-3">
                                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                        <span class="text-sm text-yellow-800 dark:text-yellow-200">Low stock alerts coming soon</span>
                                    </div>
                                </div>
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                        <span class="text-sm text-blue-800 dark:text-blue-200">Inventory tracking in development</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kitchen Notes Widget -->
                    <div class="widget bg-white dark:bg-gray-800 rounded-lg shadow p-6" data-widget="kitchen-notes">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kitchen Notes</h3>
                            <div class="flex items-center space-x-2">
                                <button class="widget-toggle text-gray-400 hover:text-gray-600" onclick="toggleWidget(this)">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <button class="widget-drag text-gray-400 hover:text-gray-600 cursor-move" onclick="startDrag(this)">
                                    <i class="fas fa-grip-vertical"></i>
                                </button>
                            </div>
                        </div>
                        <div class="widget-content">
                            <div class="space-y-3">
                                <textarea id="kitchenNotes" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" 
                                          rows="4" placeholder="Add kitchen notes here..."></textarea>
                                <button onclick="saveKitchenNotes()" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-save mr-2"></i>Save Notes
                                </button>
                            </div>
                        </div>
                    </div>

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
    <div class="relative top-20 mx-auto p-6 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4" id="categoryModalTitle">Add Category</h3>
            <form id="categoryForm">
                <div class="mb-4">
                    <label for="categoryName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Name</label>
                    <input type="text" id="categoryName" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCategoryModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        Save Category
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
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="itemPrice" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Price ({{ $restaurant->currency }})</label>
                        <input type="number" id="itemPrice" name="price" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                    
                    <div>
                        <label for="itemImage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Item Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
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
                    
                    <div>
                        <label for="itemDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea id="itemDescription" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="itemAvailable" name="is_available" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="itemAvailable" class="ml-2 block text-sm text-gray-900 dark:text-white">Available</label>
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
function openCategoryModal() {
    editingCategoryId = null;
    document.getElementById('categoryModalTitle').textContent = 'Add Category';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function editCategory(id, name) {
    editingCategoryId = id;
    document.getElementById('categoryModalTitle').textContent = 'Edit Category';
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryModal').classList.remove('hidden');
}

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        fetch(`{{ route('restaurant.categories.destroy', ['slug' => $restaurant->slug, 'category' => 'CATEGORY_ID']) }}`.replace('CATEGORY_ID', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Error deleting category');
            }
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
    document.getElementById('itemAvailable').checked = true;
    resetImagePreview();
    document.getElementById('menuItemModal').classList.remove('hidden');
}

function closeMenuItemModal() {
    document.getElementById('menuItemModal').classList.add('hidden');
}

function editMenuItem(id, name, price, description, categoryId, isAvailable, imageUrl = null) {
    editingMenuItemId = id;
    document.getElementById('menuItemModalTitle').textContent = 'Edit Menu Item';
    document.getElementById('itemName').value = name;
    document.getElementById('itemCategory').value = categoryId || '';
    document.getElementById('itemPrice').value = (price / 100).toFixed(2);
    document.getElementById('itemDescription').value = description || '';
    document.getElementById('itemAvailable').checked = isAvailable;
    
    // Handle image preview for editing
    if (imageUrl) {
        setImagePreview(imageUrl);
    } else {
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
    
    // Category form submission
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        if (editingCategoryId) {
            // Update existing category
            fetch(`{{ route('restaurant.categories.update', ['slug' => $restaurant->slug, 'category' => 'CATEGORY_ID']) }}`.replace('CATEGORY_ID', editingCategoryId), {
                method: 'PUT',
                body: formData
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    response.text().then(text => {
                        console.error('Error response:', text);
                        alert('Error updating category: ' + text);
                    });
                }
            }).catch(error => {
                console.error('Fetch error:', error);
                alert('Error updating category');
            });
        } else {
            // Create new category
            fetch(`{{ route('restaurant.categories.store', ['slug' => $restaurant->slug]) }}`, {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    response.text().then(text => {
                        console.error('Error response:', text);
                        alert('Error creating category: ' + text);
                    });
                }
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
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Convert price to cents (multiply by 100)
        const priceField = document.getElementById('itemPrice');
        if (priceField && priceField.value) {
            const priceInCents = Math.round(parseFloat(priceField.value) * 100);
            formData.set('price', priceInCents);
            console.log('Price converted to cents:', priceInCents);
        }
        
        if (editingMenuItemId) {
            // Update existing menu item
            console.log('Updating menu item:', editingMenuItemId);
            fetch(`{{ route('restaurant.menu.update', ['slug' => $restaurant->slug, 'item' => 'ITEM_ID']) }}`.replace('ITEM_ID', editingMenuItemId), {
                method: 'PUT',
                body: formData
            }).then(response => {
                console.log('Update response status:', response.status);
                if (response.ok) {
                    return response.json();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Update failed');
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
                body: formData
            }).then(response => {
                console.log('Create response status:', response.status);
                if (response.ok) {
                    return response.json();
                } else {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Creation failed');
                    });
                }
            }).then(data => {
                console.log('Create success:', data);
                window.location.reload();
            }).catch(error => {
                console.error('Create error:', error);
                alert('Error creating menu item: ' + error.message);
            });
        }
        closeMenuItemModal();
    });
});
</script>
@endsection 