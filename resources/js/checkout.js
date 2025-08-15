// Checkout Page JavaScript
// Handles order type selection, form validation, and payment processing

class CheckoutManager {
    constructor() {
        this.initializeElements();
        this.bindEvents();
        this.initializeOnLoad();
    }

    initializeElements() {
        // Order type radio buttons
        this.deliveryRadio = document.getElementById('deliveryRadio');
        this.pickupRadio = document.getElementById('pickupRadio');
        this.restaurantRadio = document.getElementById('restaurantRadio');

        // Form sections
        this.customerInfoSection = document.getElementById('customerInfoSection');
        this.restaurantInfoSection = document.getElementById('restaurantInfoSection');
        this.pickupInfoSection = document.getElementById('pickupInfoSection');
        this.addressSection = document.getElementById('addressSection');

        // Customer forms
        this.deliveryCustomerForm = document.getElementById('deliveryCustomerForm');
        this.pickupCustomerForm = document.getElementById('pickupCustomerForm');

        // Theme elements
        this.themeToggle = document.getElementById('themeToggle');
        this.themeIcon = document.getElementById('themeIcon');

        console.log('Checkout elements initialized:', {
            deliveryRadio: !!this.deliveryRadio,
            pickupRadio: !!this.pickupRadio,
            restaurantRadio: !!this.restaurantRadio,
            customerInfoSection: !!this.customerInfoSection,
            restaurantInfoSection: !!this.restaurantInfoSection,
            pickupInfoSection: !!this.pickupInfoSection,
            addressSection: !!this.addressSection
        });
    }

    bindEvents() {
        // Order type change events
        if (this.deliveryRadio) {
            this.deliveryRadio.addEventListener('change', () => this.handleOrderTypeChange());
        }
        if (this.pickupRadio) {
            this.pickupRadio.addEventListener('change', () => this.handleOrderTypeChange());
        }
        if (this.restaurantRadio) {
            this.restaurantRadio.addEventListener('change', () => this.handleOrderTypeChange());
        }

        // Theme toggle
        if (this.themeToggle) {
            this.themeToggle.addEventListener('click', () => this.toggleTheme());
        }

        // Payment method change
        document.addEventListener('change', (e) => {
            if (e.target.name === 'payment_method') {
                this.handlePaymentMethodChange(e);
            }
        });
    }

    initializeOnLoad() {
        // Initialize theme
        this.initializeTheme();
        
        // Initialize order type
        this.handleOrderTypeChange();
        
        // Setup QR code
        this.setupQRCode();
        
        // Load cart and wallet
        this.loadCart();
        this.updateCartCount();
        this.loadWalletBalance();
    }

    handleOrderTypeChange() {
        console.log('Order type change triggered');
        
        const isRestaurant = this.restaurantRadio && this.restaurantRadio.checked;
        const isPickup = this.pickupRadio && this.pickupRadio.checked;
        const isDelivery = this.deliveryRadio && this.deliveryRadio.checked;
        
        console.log('Current selection:', { isRestaurant, isPickup, isDelivery });
        
        // Reset all sections first
        this.resetAllSections();
        
        if (isRestaurant) {
            this.showRestaurantMode();
        } else if (isPickup) {
            this.showPickupMode();
        } else if (isDelivery) {
            this.showDeliveryMode();
        }
    }

    resetAllSections() {
        // Show customer info section by default
        if (this.customerInfoSection) {
            this.customerInfoSection.style.display = 'block';
        }
        
        // Hide other sections
        if (this.restaurantInfoSection) {
            this.restaurantInfoSection.style.display = 'none';
        }
        if (this.pickupInfoSection) {
            this.pickupInfoSection.style.display = 'none';
        }
        if (this.addressSection) {
            this.addressSection.style.display = 'none';
        }
        
        // Hide all customer forms
        if (this.deliveryCustomerForm) {
            this.deliveryCustomerForm.style.display = 'none';
        }
        if (this.pickupCustomerForm) {
            this.pickupCustomerForm.style.display = 'none';
        }
    }

    showRestaurantMode() {
        console.log('Restaurant mode selected');
        
        // Hide customer info, show only table info
        if (this.customerInfoSection) {
            this.customerInfoSection.style.display = 'none';
        }
        if (this.restaurantInfoSection) {
            this.restaurantInfoSection.style.display = 'block';
        }
        
        // Make table number required
        const tableNumber = document.getElementById('tableNumber');
        if (tableNumber) {
            tableNumber.setAttribute('required', 'required');
        }
    }

    showPickupMode() {
        console.log('Pickup mode selected');
        
        // Show customer info (phone only) + pickup info
        if (this.customerInfoSection) {
            this.customerInfoSection.style.display = 'block';
        }
        if (this.pickupInfoSection) {
            this.pickupInfoSection.style.display = 'block';
        }
        if (this.pickupCustomerForm) {
            this.pickupCustomerForm.style.display = 'block';
        }
        
        // Make pickup fields required
        const pickupPhone = document.getElementById('pickupPhoneOnly');
        const pickupName = document.getElementById('pickupName');
        if (pickupPhone) {
            pickupPhone.setAttribute('required', 'required');
        }
        if (pickupName) {
            pickupName.setAttribute('required', 'required');
        }
    }

    showDeliveryMode() {
        console.log('Delivery mode selected');
        
        // Show customer info (full form) + address
        if (this.customerInfoSection) {
            this.customerInfoSection.style.display = 'block';
        }
        if (this.addressSection) {
            this.addressSection.style.display = 'block';
        }
        if (this.deliveryCustomerForm) {
            this.deliveryCustomerForm.style.display = 'block';
        }
        
        // Make delivery fields required
        const deliveryName = document.getElementById('deliveryName');
        const deliveryPhone = document.getElementById('deliveryPhone');
        if (deliveryName) {
            deliveryName.setAttribute('required', 'required');
        }
        if (deliveryPhone) {
            deliveryPhone.setAttribute('required', 'required');
        }
    }

    setupQRCode() {
        const qrTableNumber = window.qrTableNumber || '';
        console.log('Setting up QR code with table number:', qrTableNumber);
        
        if (qrTableNumber) {
            const tableNumberField = document.getElementById('tableNumber');
            if (tableNumberField) {
                tableNumberField.value = qrTableNumber;
                console.log('Pre-filled table number from QR code:', qrTableNumber);
                
                // Add visual indicator that table number was pre-filled
                tableNumberField.style.backgroundColor = '#fef3c7';
                tableNumberField.style.borderColor = '#f59e0b';
                tableNumberField.style.color = '#000000';
                tableNumberField.placeholder = 'Table number from QR code';
                
                // Add a small note below the field
                const tableNumberContainer = tableNumberField.parentElement;
                if (!tableNumberContainer.querySelector('.qr-note')) {
                    const note = document.createElement('p');
                    note.className = 'text-xs text-orange-600 dark:text-orange-400 mt-1 qr-note';
                    note.innerHTML = '<i class="fas fa-qrcode mr-1"></i>Table number from QR code';
                    tableNumberContainer.appendChild(note);
                }
            }
        }
    }

    initializeTheme() {
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.classList.toggle('dark', currentTheme === 'dark');
        this.updateThemeIcon(currentTheme);
    }

    toggleTheme() {
        const isDark = document.documentElement.classList.toggle('dark');
        const theme = isDark ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        this.updateThemeIcon(theme);
    }

    updateThemeIcon(theme) {
        if (this.themeIcon) {
            this.themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    handlePaymentMethodChange(event) {
        const total = parseFloat(document.getElementById('total').textContent.replace('₦', '').replace(',', ''));
        const walletBalance = parseFloat(document.getElementById('walletBalanceAmount').textContent.replace('₦', '').replace(',', '')) || 0;
        
        if (event.target.value === 'wallet' && walletBalance < total) {
            alert('Insufficient wallet balance. Please add more funds or choose another payment method.');
            event.target.checked = false;
            const cashRadio = document.querySelector('input[name="payment_method"][value="cash"]');
            if (cashRadio) {
                cashRadio.checked = true;
            }
        }
    }

    loadCart() {
        // Cart loading logic
        console.log('Loading cart...');
    }

    updateCartCount() {
        // Cart count update logic
        console.log('Updating cart count...');
    }

    loadWalletBalance() {
        console.log('Loading wallet balance...');
        fetch('/wallet/info', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Wallet response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Wallet data:', data);
            this.displayWalletBalance(data);
        })
        .catch(error => {
            console.log('Error loading wallet:', error);
            this.displayWalletBalance({ balance: 0, points: 0 });
        });
    }

    displayWalletBalance(data) {
        const walletBalance = document.getElementById('walletBalance');
        const walletBalanceAmount = document.getElementById('walletBalanceAmount');
        const walletPoints = document.getElementById('walletPoints');
        const walletPaymentOption = document.getElementById('walletPaymentOption');

        if (data.balance > 0) {
            if (walletBalance) walletBalance.classList.remove('hidden');
            if (walletBalanceAmount) walletBalanceAmount.textContent = data.formatted_balance || `₦${data.balance}`;
            if (walletPoints) walletPoints.textContent = data.points_display || `${data.points} points`;
            if (walletPaymentOption) walletPaymentOption.classList.remove('hidden');
        } else {
            if (walletPaymentOption) walletPaymentOption.classList.remove('hidden');
            if (walletBalance) walletBalance.classList.remove('hidden');
            if (walletBalanceAmount) walletBalanceAmount.textContent = '₦0';
            if (walletPoints) walletPoints.textContent = '0 points';
        }
    }

    // Bank Transfer Functions
    handleBankTransferSelection() {
        const transferRadio = document.querySelector('input[name="payment_method"][value="transfer"]');
        if (transferRadio && transferRadio.checked) {
            console.log('Bank transfer selected - will be handled after order creation');
        }
    }

    initializeBankTransferPayment(orderId, amount) {
        const modal = document.getElementById('bankTransferModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
        
        fetch('/bank-transfer/initialize', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                order_id: orderId,
                amount: amount
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Bank transfer initialization response:', data);
            if (data.success) {
                if (typeof displayPaymentDetails === 'function') {
                    displayPaymentDetails(data.data);
                    if (data.data.payment_id) {
                        startPaymentStatusCheck(data.data.payment_id);
                    }
                } else {
                    console.error('displayPaymentDetails function not found');
                    this.showNotification('Payment initialized but display function not available', 'error');
                }
            } else {
                console.error('Bank transfer initialization failed:', data.message);
                this.showNotification(data.message || 'Failed to initialize payment', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Failed to initialize payment. Please try again.', 'error');
        });
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
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
}

// Initialize checkout when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing checkout manager...');
    window.checkoutManager = new CheckoutManager();
});

// Export for use in other modules
export default CheckoutManager;
