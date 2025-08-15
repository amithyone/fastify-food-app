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

    getRestaurantSlug() {
        // Get the restaurant slug from the current URL
        const pathParts = window.location.pathname.split('/');
        
        console.log('Current URL pathname:', window.location.pathname);
        console.log('Path parts:', pathParts);
        console.log('Current page URL:', window.location.href);
        console.log('Document title:', document.title);
        
        let restaurantSlug;
        
        // First, try to get from data attribute (most reliable)
        const restaurantElement = document.querySelector('[data-restaurant-slug]');
        console.log('Restaurant element found:', restaurantElement);
        if (restaurantElement) {
            restaurantSlug = restaurantElement.getAttribute('data-restaurant-slug');
            console.log('Found restaurant slug from data attribute:', restaurantSlug);
            if (restaurantSlug && restaurantSlug.trim() !== '') {
                return restaurantSlug;
            }
        }
        
        // Debug: Check all data attributes on the page
        const allDataElements = document.querySelectorAll('[data-restaurant-slug]');
        console.log('All elements with data-restaurant-slug:', allDataElements);
        allDataElements.forEach((el, index) => {
            console.log(`Element ${index}:`, el.getAttribute('data-restaurant-slug'));
        });
        
        // If we can't get it from data attribute, try to extract from URL
        // The URL should be /restaurant/{slug}/menu or similar
        if (pathParts[1] === 'restaurant' && pathParts[2]) {
            // URL is /restaurant/{slug}/something
            restaurantSlug = pathParts[2];
            console.log('Extracted restaurant slug from URL path:', restaurantSlug);
        } else {
            // Fallback: try to get from the page title
            const pageTitle = document.title;
            console.log('Page title:', pageTitle);
            if (pageTitle.includes(' - Dashboard')) {
                // Extract restaurant name from title and convert to slug
                const restaurantName = pageTitle.replace(' - Dashboard', '');
                restaurantSlug = restaurantName.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
                console.log('Extracted restaurant slug from title:', restaurantSlug);
            } else {
                console.error('Could not determine restaurant slug from URL or page elements:', window.location.pathname);
                throw new Error('Restaurant slug not found');
            }
        }
        
        if (!restaurantSlug) {
            console.error('Could not determine restaurant slug from URL:', window.location.pathname);
            throw new Error('Restaurant slug not found in URL');
        }
        
        console.log('Final restaurant slug:', restaurantSlug);
        return restaurantSlug;
    }

    // Category Management Functions
    openCategoryModal(parentId = null, parentName = null) {
        console.log('Opening category modal:', { parentId, parentName });
        
        // Reset editing state for new category
        this.editingCategoryId = null;
        
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
            const restaurantSlug = this.getRestaurantSlug();
            
            console.log('Loading subcategories for parent ID:', parentId);
            console.log('Restaurant slug:', restaurantSlug);
            console.log('Full URL:', `/restaurant/${restaurantSlug}/categories/${parentId}/subcategories`);
            
            // Add CSRF token and proper headers
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };
            
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }
            
            const response = await fetch(`/restaurant/${restaurantSlug}/categories/${parentId}/subcategories`, {
                method: 'GET',
                headers: headers,
                credentials: 'same-origin'
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Response text:', errorText);
                
                // Handle specific error cases
                if (response.status === 404) {
                    console.warn('Subcategories endpoint not found - this might be normal if no subcategories exist');
                    this.displayExistingSubCategories([]);
                    return;
                }
                
                throw new Error(`HTTP ${response.status}: ${response.statusText} - ${errorText}`);
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const responseText = await response.text();
                console.error('Non-JSON response received:', responseText);
                throw new Error(`Server returned non-JSON response (${response.status}): ${responseText.substring(0, 200)}`);
            }
            
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                this.displayExistingSubCategories(data.subcategories);
            } else {
                console.error('Failed to load sub-categories:', data.message);
                this.displayExistingSubCategories([]);
            }
        } catch (error) {
            console.error('Error loading sub-categories:', error);
            // Fallback: show empty state
            this.displayExistingSubCategories([]);
        }
    }

    displayExistingSubCategories(subcategories) {
        const container = document.getElementById('existingSubCategoriesList');
        
        if (!container) {
            console.error('Container element "existingSubCategoriesList" not found');
            return;
        }
        
        let html = '';
        
        if (subcategories && subcategories.length > 0) {
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
        
        // Determine if this is an edit or create operation
        const isEdit = this.editingCategoryId !== null;
        const method = isEdit ? 'PUT' : 'POST';
        const url = isEdit 
            ? `/${this.getRestaurantSlug()}/categories/${this.editingCategoryId}`
            : `/${this.getRestaurantSlug()}/categories`;
        
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${isEdit ? 'Updating...' : 'Creating...'}`;
        submitBtn.disabled = true;
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(`Category ${isEdit ? 'updated' : 'created'} successfully!`, 'success');
                this.closeCategoryModal();
                window.location.reload();
            } else {
                this.showNotification(data.message || `Failed to ${isEdit ? 'update' : 'create'} category`, 'error');
            }
        } catch (error) {
            console.error(`Error ${isEdit ? 'updating' : 'creating'} category:`, error);
            this.showNotification(`Error ${isEdit ? 'updating' : 'creating'} category. Please try again.`, 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    // Helper method to get restaurant slug
    getRestaurantSlug() {
        const pathParts = window.location.pathname.split('/');
        return pathParts[1]; // Assuming URL is /restaurant-slug/menu
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
        
        // Reset editing state
        this.editingCategoryId = null;
        
        // Reset modal title
        const modalTitle = document.getElementById('categoryModalTitle');
        if (modalTitle) {
            modalTitle.textContent = 'Add Sub-Category';
        }
    }

    // Menu Item Management Functions
    openMenuItemModal() {
        console.log('Opening menu item modal');
        
        // Reset editing state for new menu item
        this.editingMenuItemId = null;
        
        this.menuItemModal.classList.remove('hidden');
    }

    closeMenuItemModal() {
        this.menuItemModal.classList.add('hidden');
        this.resetMenuItemForm();
    }

    editMenuItem(id, name, price, description, categoryId, isAvailable, imageUrl, ingredients, allergens, isFeatured, isVegetarian, isSpicy, restaurantImageId, isDelivery, isPickup, isRestaurant) {
        console.log('Editing menu item:', { id, name, price });
        
        // Set editing state
        this.editingMenuItemId = id;
        
        // Populate form fields - using correct element IDs from the form
        const nameElement = document.getElementById('itemName');
        const priceElement = document.getElementById('itemPrice');
        const descriptionElement = document.getElementById('itemDescription');
        const categoryElement = document.getElementById('itemCategory');
        const availableElement = document.getElementById('itemAvailable');
        const featuredElement = document.getElementById('itemFeatured');
        const vegetarianElement = document.getElementById('itemVegetarian');
        const spicyElement = document.getElementById('itemSpicy');
        const ingredientsElement = document.getElementById('itemIngredients');
        const allergensElement = document.getElementById('itemAllergens');
        const deliveryElement = document.getElementById('itemAvailableForDelivery');
        const pickupElement = document.getElementById('itemAvailableForPickup');
        const restaurantElement = document.getElementById('itemAvailableForRestaurant');
        
        // Check if elements exist before setting values
        if (nameElement) nameElement.value = name;
        if (priceElement) priceElement.value = price;
        if (descriptionElement) descriptionElement.value = description || '';
        if (categoryElement) categoryElement.value = categoryId || '';
        if (availableElement) availableElement.checked = isAvailable;
        if (featuredElement) featuredElement.checked = isFeatured;
        if (vegetarianElement) vegetarianElement.checked = isVegetarian;
        if (spicyElement) spicyElement.checked = isSpicy;
        if (ingredientsElement) ingredientsElement.value = ingredients || '';
        if (allergensElement) allergensElement.value = allergens || '';
        if (deliveryElement) deliveryElement.checked = isDelivery;
        if (pickupElement) pickupElement.checked = isPickup;
        if (restaurantElement) restaurantElement.checked = isRestaurant;
        
        // Set image if exists
        if (imageUrl && imageUrl !== 'null') {
            const selectedImagePath = document.getElementById('selectedImagePath');
            const selectedImageText = document.getElementById('selectedImageText');
            if (selectedImagePath) selectedImagePath.value = imageUrl;
            if (selectedImageText) selectedImageText.textContent = 'Current image';
            this.setImagePreview(imageUrl);
        }
        
        // Update modal title and button
        const modalTitle = document.getElementById('menuItemModalTitle');
        const submitBtn = document.querySelector('#menuItemForm button[type="submit"]');
        if (modalTitle) modalTitle.textContent = 'Edit Menu Item';
        if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Update Item';
        
        this.openMenuItemModal();
    }

    async handleMenuItemSubmit(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Determine if this is an edit or create operation
        const isEdit = this.editingMenuItemId !== null;
        const method = isEdit ? 'PUT' : 'POST';
        const url = isEdit 
            ? `/${this.getRestaurantSlug()}/menu/${this.editingMenuItemId}`
            : `/${this.getRestaurantSlug()}/menu`;
        
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${isEdit ? 'Updating...' : 'Saving...'}`;
        submitBtn.disabled = true;
        
        try {
            console.log('Submitting menu item:', { url, method, isEdit });
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const responseText = await response.text();
                console.error('Non-JSON response received:', responseText);
                throw new Error(`Server returned non-JSON response (${response.status}): ${responseText.substring(0, 200)}`);
            }
            
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                this.showNotification(`Menu item ${isEdit ? 'updated' : 'saved'} successfully!`, 'success');
                this.closeMenuItemModal();
                window.location.reload();
            } else {
                this.showNotification(data.message || `Failed to ${isEdit ? 'update' : 'save'} menu item`, 'error');
            }
        } catch (error) {
            console.error(`Error ${isEdit ? 'updating' : 'saving'} menu item:`, error);
            this.showNotification(`Error ${isEdit ? 'updating' : 'saving'} menu item: ${error.message}`, 'error');
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
        const selectedImageId = document.getElementById('selectedImageId');
        const selectedImagePath = document.getElementById('selectedImagePath');
        const selectedImageText = document.getElementById('selectedImageText');
        
        if (selectedImageId) selectedImageId.value = '';
        if (selectedImagePath) selectedImagePath.value = '';
        if (selectedImageText) selectedImageText.textContent = 'Choose from uploaded images';
        this.resetImagePreview();
        
        // Reset modal title and button
        const modalTitle = document.getElementById('menuItemModalTitle');
        const submitBtn = document.querySelector('#menuItemForm button[type="submit"]');
        if (modalTitle) modalTitle.textContent = 'Add Menu Item';
        if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Item';
        
        // Reset editing state
        this.editingMenuItemId = null;
    }

    // Image Management Functions
    async loadRestaurantImages() {
        try {
            const restaurantSlug = this.getRestaurantSlug();
            
            const response = await fetch(`/restaurant/${restaurantSlug}/images`);
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
        
        const restaurantSlug = this.getRestaurantSlug();
        
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
                    <a href="/${restaurantSlug}/images" class="inline-block mt-4 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
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
            // Get the restaurant slug from the current URL
            const pathParts = window.location.pathname.split('/');
            const restaurantSlug = pathParts[1]; // Assuming URL is /restaurant-slug/menu
            
            const response = await fetch(`/restaurant/${restaurantSlug}/categories/${categoryId}`, {
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
            // Get the restaurant slug from the current URL
            const pathParts = window.location.pathname.split('/');
            const restaurantSlug = pathParts[1]; // Assuming URL is /restaurant-slug/menu
            
            const response = await fetch(`/restaurant/${restaurantSlug}/menu/${itemId}`, {
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
            // Get the restaurant slug from the current URL
            const pathParts = window.location.pathname.split('/');
            const restaurantSlug = pathParts[1]; // Assuming URL is /restaurant-slug/menu
            
            const response = await fetch(`/restaurant/${restaurantSlug}/menu/${itemId}/toggle-status`, {
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

    // Category Management Methods
    editCategory(categoryId, categoryName, parentId) {
        console.log('Editing category:', { categoryId, categoryName, parentId });
        
        // Populate form fields
        document.getElementById('categoryName').value = categoryName;
        document.getElementById('categoryParent').value = parentId || '';
        document.getElementById('newCategoryParent').value = parentId || '';
        
        // Update modal title
        document.getElementById('categoryModalTitle').textContent = 'Edit Category';
        
        // Store the category ID for update
        this.editingCategoryId = categoryId;
        
        this.openCategoryModal();
    }

    async deactivateCategory(categoryId) {
        if (!confirm('Are you sure you want to remove this category from your restaurant?')) return;
        
        try {
            // Get the restaurant slug from the current URL
            const pathParts = window.location.pathname.split('/');
            const restaurantSlug = pathParts[1]; // Assuming URL is /restaurant-slug/menu
            
            const response = await fetch(`/restaurant/${restaurantSlug}/categories/deactivate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ category_id: categoryId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Category removed from restaurant successfully!', 'success');
                window.location.reload();
            } else {
                this.showNotification(data.message || 'Failed to remove category', 'error');
            }
        } catch (error) {
            console.error('Error removing category:', error);
            this.showNotification('Error removing category. Please try again.', 'error');
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

// Additional functions needed for the menu page
window.editCategory = function(categoryId, categoryName, parentId) {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.editCategory(categoryId, categoryName, parentId);
    }
};

window.deactivateCategory = function(categoryId) {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.deactivateCategory(categoryId);
    }
};

window.openImageSelector = function() {
    if (window.restaurantMenuManager) {
        window.restaurantMenuManager.loadRestaurantImages();
    }
};

window.closeSimilarCategoriesModal = function() {
    const modal = document.getElementById('similarCategoriesModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.forceCreateCategory = function() {
    // This function would handle forcing category creation
    console.log('Force create category function called');
    // Implementation would go here
};

window.addQuickNote = function() {
    const modal = document.getElementById('quickNoteModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
};

window.closeQuickNoteModal = function() {
    const modal = document.getElementById('quickNoteModal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

window.saveQuickNote = function() {
    // This function would handle saving quick notes
    console.log('Save quick note function called');
    // Implementation would go here
};

// Export for use in other modules
export default RestaurantMenuManager;
