// Restaurant Menu Management JavaScript
// Handles category and menu item management functionality

class RestaurantMenuManager {
    constructor() {
        this.initializeElements();
        this.bindEvents();
        this.initializeOnLoad();
    }

    initializeElements() {
        // Get modal elements
        this.categoryModal = document.getElementById('categoryModal');
        this.menuItemModal = document.getElementById('menuItemModal');
        this.imageSelectorModal = document.getElementById('imageSelectorModal');
        
        // Get form elements
        this.categoryForm = document.getElementById('categoryForm');
        this.menuItemForm = document.getElementById('menuItemForm');
        
        console.log('Restaurant menu elements initialized');
    }

    bindEvents() {
        // Category type selection
        document.querySelectorAll('input[name="category_type"]').forEach(radio => {
            radio.addEventListener('change', (e) => this.handleCategoryTypeChange(e));
        });

        // Parent category selection
        const categoryParent = document.getElementById('categoryParent');
        if (categoryParent) {
            categoryParent.addEventListener('change', (e) => this.handleParentCategoryChange(e));
        }

        // Form submissions
        if (this.categoryForm) {
            this.categoryForm.addEventListener('submit', (e) => this.handleCategorySubmit(e));
        }
        
        if (this.menuItemForm) {
            this.menuItemForm.addEventListener('submit', (e) => this.handleMenuItemSubmit(e));
        }

        // Close modals when clicking outside
        document.addEventListener('click', (e) => this.handleOutsideClick(e));
    }

    initializeOnLoad() {
        console.log('Restaurant menu manager initialized');
    }

    // Category Management Functions
    openCategoryModal(parentId = null, parentName = null) {
        console.log('Opening category modal:', { parentId, parentName });
        
        if (parentId && parentName) {
            document.getElementById('categoryModalTitle').textContent = `Add Sub-Category to ${parentName}`;
            document.getElementById('categoryParent').value = parentId;
            this.handleParentCategoryChange({ target: { value: parentId } });
        } else {
            document.getElementById('categoryModalTitle').textContent = 'Add Sub-Category';
        }
        
        this.categoryModal.classList.remove('hidden');
    }

    closeCategoryModal() {
        this.categoryModal.classList.add('hidden');
        this.resetCategoryForm();
    }

    handleCategoryTypeChange(event) {
        const type = event.target.value;
        const existingForm = document.getElementById('existingCategoryForm');
        const customForm = document.getElementById('customCategoryForm');
        
        if (type === 'existing') {
            existingForm.style.display = 'block';
            customForm.style.display = 'none';
        } else {
            existingForm.style.display = 'none';
            customForm.style.display = 'block';
        }
    }

    handleParentCategoryChange(event) {
        const parentId = event.target.value;
        if (!parentId) return;
        
        this.loadExistingSubCategories(parentId);
    }

    async loadExistingSubCategories(parentId) {
        try {
            const response = await fetch(`/api/categories/${parentId}/subcategories`);
            const data = await response.json();
            
            if (data.success) {
                this.displayExistingSubCategories(data.subcategories);
            } else {
                console.error('Failed to load sub-categories:', data.message);
            }
        } catch (error) {
            console.error('Error loading sub-categories:', error);
        }
    }

    displayExistingSubCategories(subcategories) {
        const container = document.getElementById('existingSubCategoriesList');
        let html = '';
        
        if (subcategories.length > 0) {
            subcategories.forEach(category => {
                html += `
                    <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                        <input type="radio" name="existing_category_id" value="${category.id}" class="text-blue-600 focus:ring-blue-500 mr-3">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${category.name}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                ${category.restaurant_count || 0} restaurants using this
                            </div>
                        </div>
                    </label>
                `;
            });
        } else {
            html = `
                <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                    <i class="fas fa-info-circle mb-2"></i>
                    <p>No existing sub-categories found for this parent category</p>
                </div>
            `;
        }
        
        container.innerHTML = html;
    }

    async handleCategorySubmit(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('/restaurant/categories', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Category created successfully!', 'success');
                this.closeCategoryModal();
                window.location.reload();
            } else {
                this.showNotification(data.message || 'Failed to create category', 'error');
            }
        } catch (error) {
            console.error('Error creating category:', error);
            this.showNotification('Error creating category. Please try again.', 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    resetCategoryForm() {
        if (this.categoryForm) {
            this.categoryForm.reset();
        }
        document.getElementById('existingSubCategoriesList').innerHTML = `
            <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                <i class="fas fa-info-circle mb-2"></i>
                <p>Select a parent category to see available sub-categories</p>
            </div>
        `;
    }

    // Menu Item Management Functions
    openMenuItemModal() {
        console.log('Opening menu item modal');
        this.menuItemModal.classList.remove('hidden');
    }

    closeMenuItemModal() {
        this.menuItemModal.classList.add('hidden');
        this.resetMenuItemForm();
    }

    editMenuItem(id, name, price, description, categoryId, isAvailable, imageUrl, ingredients, allergens, isFeatured, isVegetarian, isSpicy, restaurantImageId, isDelivery, isPickup, isRestaurant) {
        console.log('Editing menu item:', { id, name, price });
        
        // Populate form fields
        document.getElementById('menuItemId').value = id;
        document.getElementById('menuItemName').value = name;
        document.getElementById('menuItemPrice').value = price;
        document.getElementById('menuItemDescription').value = description || '';
        document.getElementById('menuItemCategory').value = categoryId || '';
        document.getElementById('menuItemAvailable').checked = isAvailable;
        document.getElementById('menuItemFeatured').checked = isFeatured;
        document.getElementById('menuItemVegetarian').checked = isVegetarian;
        document.getElementById('menuItemSpicy').checked = isSpicy;
        document.getElementById('menuItemIngredients').value = ingredients || '';
        document.getElementById('menuItemAllergens').value = allergens || '';
        document.getElementById('isDelivery').checked = isDelivery;
        document.getElementById('isPickup').checked = isPickup;
        document.getElementById('isRestaurant').checked = isRestaurant;
        
        // Set image if exists
        if (imageUrl && imageUrl !== 'null') {
            document.getElementById('selectedImagePath').value = imageUrl;
            document.getElementById('selectedImageText').textContent = 'Current image';
            this.setImagePreview(imageUrl);
        }
        
        // Update modal title and button
        document.getElementById('menuItemModalTitle').textContent = 'Edit Menu Item';
        document.getElementById('menuItemSubmitBtn').textContent = 'Update Item';
        
        this.openMenuItemModal();
    }

    async handleMenuItemSubmit(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;
        
        try {
            const response = await fetch('/restaurant/menu-items', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Menu item saved successfully!', 'success');
                this.closeMenuItemModal();
                window.location.reload();
            } else {
                this.showNotification(data.message || 'Failed to save menu item', 'error');
            }
        } catch (error) {
            console.error('Error saving menu item:', error);
            this.showNotification('Error saving menu item. Please try again.', 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    resetMenuItemForm() {
        if (this.menuItemForm) {
            this.menuItemForm.reset();
        }
        
        // Reset image selection
        document.getElementById('selectedImageId').value = '';
        document.getElementById('selectedImagePath').value = '';
        document.getElementById('selectedImageText').textContent = 'Choose from uploaded images';
        this.resetImagePreview();
        
        // Reset modal title and button
        document.getElementById('menuItemModalTitle').textContent = 'Add Menu Item';
        document.getElementById('menuItemSubmitBtn').textContent = 'Add Item';
    }

    // Image Management Functions
    async loadRestaurantImages() {
        try {
            const response = await fetch('/restaurant/images');
            const data = await response.json();
            
            if (data.success) {
                this.showImageSelectorModal(data.images);
            } else {
                console.error('Failed to load images:', data.message);
            }
        } catch (error) {
            console.error('Error loading images:', error);
        }
    }

    showImageSelectorModal(images) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 modal-overlay';
        modal.id = 'imageSelectorModal';
        
        let imagesHtml = '';
        if (images.length > 0) {
            images.forEach(image => {
                imagesHtml += `
                    <div class="cursor-pointer hover:scale-105 transition-transform p-2" onclick="restaurantMenuManager.selectImageFromModal('${image.url}', ${image.id}, '${image.original_name}')">
                        <div class="relative">
                            <img src="${image.thumbnail_url}?v=${Date.now()}" alt="${image.original_name}" 
                                 class="w-full h-20 object-cover rounded-lg border-2 border-transparent hover:border-orange-500 shadow-sm bg-gray-200 dark:bg-gray-600 modal-image-preview"
                                 onerror="this.src='${image.url}?v=${Date.now()}'">
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
                    <a href="/restaurant/images" class="inline-block mt-4 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
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
                        <button onclick="restaurantMenuManager.closeImageSelectorModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="menu-items-grid">
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

    closeImageSelectorModal() {
        const modal = document.getElementById('imageSelectorModal');
        if (modal) {
            modal.remove();
        }
    }

    selectImageFromModal(imageUrl, imageId, originalName) {
        document.getElementById('selectedImageId').value = imageId;
        document.getElementById('selectedImagePath').value = imageUrl;
        document.getElementById('selectedImageText').textContent = originalName;
        
        this.setImagePreview(imageUrl);
        this.closeImageSelectorModal();
    }

    setImagePreview(imageUrl) {
        const preview = document.getElementById('imagePreview');
        if (preview) {
            preview.innerHTML = `
                <img src="${imageUrl}" alt="Preview" class="w-full h-24 object-cover rounded-lg image-preview">
            `;
        }
    }

    resetImagePreview() {
        const preview = document.getElementById('imagePreview');
        if (preview) {
            preview.innerHTML = `
                <div class="w-full h-24 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center image-preview">
                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                </div>
            `;
        }
    }

    clearSelectedImage() {
        document.getElementById('selectedImageId').value = '';
        document.getElementById('selectedImagePath').value = '';
        document.getElementById('selectedImageText').textContent = 'Choose from uploaded images';
        this.resetImagePreview();
    }

    // Utility Functions
    handleOutsideClick(event) {
        if (event.target === this.categoryModal) {
            this.closeCategoryModal();
        }
        if (event.target === this.menuItemModal) {
            this.closeMenuItemModal();
        }
        if (event.target === this.imageSelectorModal) {
            this.closeImageSelectorModal();
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg fade-in ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Delete Functions
    async deleteCategory(categoryId) {
        if (!confirm('Are you sure you want to delete this category?')) return;
        
        try {
            const response = await fetch(`/restaurant/categories/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Category deleted successfully!', 'success');
                window.location.reload();
            } else {
                this.showNotification(data.message || 'Failed to delete category', 'error');
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            this.showNotification('Error deleting category. Please try again.', 'error');
        }
    }

    async deleteMenuItem(itemId) {
        if (!confirm('Are you sure you want to delete this menu item?')) return;
        
        try {
            const response = await fetch(`/restaurant/menu-items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Menu item deleted successfully!', 'success');
                window.location.reload();
            } else {
                this.showNotification(data.message || 'Failed to delete menu item', 'error');
            }
        } catch (error) {
            console.error('Error deleting menu item:', error);
            this.showNotification('Error deleting menu item. Please try again.', 'error');
        }
    }

    async toggleItemStatus(itemId, newStatus) {
        try {
            const response = await fetch(`/restaurant/menu-items/${itemId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_available: newStatus })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(`Menu item ${newStatus ? 'activated' : 'deactivated'} successfully!`, 'success');
                window.location.reload();
            } else {
                this.showNotification(data.message || 'Failed to update menu item status', 'error');
            }
        } catch (error) {
            console.error('Error updating menu item status:', error);
            this.showNotification('Error updating menu item status. Please try again.', 'error');
        }
    }
}

// Initialize restaurant menu manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing restaurant menu manager...');
    window.restaurantMenuManager = new RestaurantMenuManager();
});

// Global functions for onclick handlers
window.openCategoryModal = function(parentId, parentName) {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.openCategoryModal(parentId, parentName);
    }
};

window.closeCategoryModal = function() {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.closeCategoryModal();
    }
};

window.openMenuItemModal = function() {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.openMenuItemModal();
    }
};

window.closeMenuItemModal = function() {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.closeMenuItemModal();
    }
};

window.editMenuItem = function(id, name, price, description, categoryId, isAvailable, imageUrl, ingredients, allergens, isFeatured, isVegetarian, isSpicy, restaurantImageId, isDelivery, isPickup, isRestaurant) {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.editMenuItem(id, name, price, description, categoryId, isAvailable, imageUrl, ingredients, allergens, isFeatured, isVegetarian, isSpicy, restaurantImageId, isDelivery, isPickup, isRestaurant);
    }
};

window.deleteCategory = function(categoryId) {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.deleteCategory(categoryId);
    }
};

window.deleteMenuItem = function(itemId) {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.deleteMenuItem(itemId);
    }
};

window.toggleItemStatus = function(itemId, newStatus) {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.toggleItemStatus(itemId, newStatus);
    }
};

window.loadRestaurantImages = function() {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.loadRestaurantImages();
    }
};

window.clearSelectedImage = function() {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.clearSelectedImage();
    }
};

// Export for use in other modules
export default RestaurantMenuManager;
