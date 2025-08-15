import './bootstrap';

// Import PWA utilities
import './pwa';

// Import checkout module
import './checkout';

// Import restaurant menu module
import './restaurant-menu';

// PWA Installation and Management
document.addEventListener('DOMContentLoaded', function() {
    // Initialize PWA features
    initializePWA();
    
    // Initialize existing app features
    initializeApp();
});

function initializePWA() {
    // Check if PWA is installed
    if (window.matchMedia('(display-mode: standalone)').matches) {
        document.body.classList.add('pwa-installed');
        console.log('Running in PWA mode');
    }
    
    // Add PWA-specific styles
    if (document.body.classList.contains('pwa-installed')) {
        // Hide browser UI elements when in PWA mode
        document.body.style.paddingTop = '0';
        document.body.style.paddingBottom = '0';
    }
}

function initializeApp() {
    // Initialize dark mode
    initializeDarkMode();
    
    // Initialize cart functionality
    initializeCart();
    
    // Initialize search functionality
    initializeSearch();
    
    // Initialize notifications
    initializeNotifications();
}

function initializeDarkMode() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (!darkModeToggle) return;
    
    // Check for saved theme preference or default to system preference
    const userPref = localStorage.getItem('theme');
    const systemPref = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = userPref === 'dark' || (!userPref && systemPref);
    
    // Apply theme
    setTheme(isDark);
    
    // Add event listener
    darkModeToggle.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        setTheme(isDark);
    });
}

function setTheme(isDark) {
    if (isDark) {
        document.documentElement.classList.add('dark');
        if (document.getElementById('darkModeToggle')) {
            document.getElementById('darkModeToggle').innerHTML = '<i class="fas fa-moon text-yellow-400"></i>';
        }
    } else {
        document.documentElement.classList.remove('dark');
        if (document.getElementById('darkModeToggle')) {
            document.getElementById('darkModeToggle').innerHTML = '<i class="fas fa-sun text-gray-600"></i>';
        }
    }
}

function initializeCart() {
    // Cart functionality is handled in individual pages
    // This is just for global cart state management
    updateCartCount();
}

function updateCartCount() {
    const cartCount = document.getElementById('cartCount');
    if (!cartCount) return;
    
    try {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const count = cart.reduce((total, item) => total + item.quantity, 0);
        
        cartCount.textContent = count;
        cartCount.classList.toggle('hidden', count === 0);
    } catch (error) {
        console.error('Error updating cart count:', error);
    }
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        const menuItems = document.querySelectorAll('.food-card');
        
        menuItems.forEach(item => {
            const name = item.dataset.name?.toLowerCase() || '';
            const category = item.dataset.category?.toLowerCase() || '';
            
            const matches = name.includes(query) || category.includes(query);
            item.style.display = matches ? 'flex' : 'none';
        });
    });
}

function initializeNotifications() {
    // Request notification permission if not already granted
    if ('Notification' in window && Notification.permission === 'default') {
        // Show a subtle notification permission request
        setTimeout(() => {
            showNotificationPermissionRequest();
        }, 5000);
    }
}

function showNotificationPermissionRequest() {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-4 left-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-200 dark:border-gray-700 z-50';
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Enable Notifications</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Get updates about your orders</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button onclick="enableNotifications(this)" class="bg-orange-500 text-white px-3 py-1 rounded text-sm font-medium hover:bg-orange-600 transition-colors">
                    Enable
                </button>
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 10000);
}

// Global function for enabling notifications
window.enableNotifications = async function(button) {
    try {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            button.parentElement.parentElement.parentElement.remove();
            
            // Subscribe to push notifications
            if (window.PWAUtils) {
                await window.PWAUtils.subscribeToPushNotifications();
            }
            
            // Show success message
            if (window.PWAUtils) {
                window.PWAUtils.showNotification('Notifications enabled!', 'success');
            }
        }
    } catch (error) {
        console.error('Failed to enable notifications:', error);
    }
};

// Global function for showing notifications
window.showNotification = function(message, type = 'info') {
    if (window.PWAUtils) {
        window.PWAUtils.showNotification(message, type);
    } else {
        // Fallback notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }
};

// Global function for updating cart
window.updateCart = function() {
    updateCartCount();
};

// Export functions for use in other modules
window.AppUtils = {
    setTheme,
    updateCartCount,
    showNotification,
    enableNotifications
};
