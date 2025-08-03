<!-- Bank Transfer Payment Modal -->
<div id="bankTransferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bank Transfer Payment</h3>
                <button onclick="closeBankTransferModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Loading State -->
            <div id="bankTransferLoading" class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto mb-4"></div>
                <p class="text-gray-600 dark:text-gray-400">Initializing payment...</p>
            </div>

            <!-- Payment Details -->
            <div id="bankTransferDetails" class="hidden">
                <!-- Reward Points Info -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-gift text-green-600 mr-2"></i>
                        <div>
                            <h4 class="font-medium text-green-800 dark:text-green-200">Earn Rewards!</h4>
                            <p class="text-sm text-green-600 dark:text-green-400">
                                Earn <span id="rewardPointsRate">1</span> point per ₦<span id="rewardThreshold">100</span> spent
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Amount -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Amount to Pay:</span>
                        <span class="font-semibold text-gray-900 dark:text-white" id="paymentAmount">₦0</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-600 dark:text-gray-400">Service Charge (2%):</span>
                        <span class="font-semibold text-orange-600" id="serviceCharge">₦0</span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-600 mt-2 pt-2">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-900 dark:text-white">Total:</span>
                            <span class="font-bold text-lg text-gray-900 dark:text-white" id="totalAmount">₦0</span>
                        </div>
                    </div>
                </div>

                <!-- Virtual Account Details -->
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-3">Transfer Details</h4>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Number</label>
                            <div class="flex items-center">
                                <input type="text" id="virtualAccountNumber" readonly 
                                       class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-mono text-sm">
                                <button onclick="copyToClipboard('virtualAccountNumber')" 
                                        class="ml-2 px-3 py-2 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-lg transition-colors">
                                    <i class="fas fa-copy text-gray-600 dark:text-gray-400"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bank Name</label>
                            <input type="text" id="bankName" readonly 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Name</label>
                            <input type="text" id="accountName" readonly 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Reference</label>
                            <div class="flex items-center">
                                <input type="text" id="paymentReference" readonly 
                                       class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-mono text-sm">
                                <button onclick="copyToClipboard('paymentReference')" 
                                        class="ml-2 px-3 py-2 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-lg transition-colors">
                                    <i class="fas fa-copy text-gray-600 dark:text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-2">Payment Instructions</h4>
                    <div class="text-sm text-blue-700 dark:text-blue-300 space-y-1" id="paymentInstructions">
                        <!-- Instructions will be populated here -->
                    </div>
                </div>

                <!-- Payment Status -->
                <div id="paymentStatus" class="mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Payment Status:</span>
                        <span id="statusBadge" class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            Pending
                        </span>
                    </div>
                    <div class="mt-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Expires in: </span>
                        <span id="timeRemaining" class="text-xs font-medium text-gray-700 dark:text-gray-300">24 hours</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button onclick="checkPaymentStatus()" 
                            class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Check Status
                    </button>
                    <button onclick="closeBankTransferModal()" 
                            class="flex-1 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg transition-colors">
                        Close
                    </button>
                </div>

                <!-- Partial Payment Section -->
                <div id="partialPaymentSection" class="hidden mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                    <h4 class="font-medium text-yellow-800 dark:text-yellow-200 mb-2">Partial Payment Detected</h4>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-3">
                        You've made a partial payment. Please pay the remaining amount to complete your order.
                    </p>
                    <div class="mb-3">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Amount Paid: </span>
                        <span id="amountPaid" class="font-medium text-gray-900 dark:text-white">₦0</span>
                    </div>
                    <div class="mb-3">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Amount Remaining: </span>
                        <span id="amountRemaining" class="font-medium text-orange-600">₦0</span>
                    </div>
                    <button onclick="generateNewAccount()" 
                            class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Generate New Account for Remaining Amount
                    </button>
                </div>
            </div>

            <!-- Error State -->
            <div id="bankTransferError" class="hidden text-center py-8">
                <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                <h4 class="font-medium text-red-800 dark:text-red-200 mb-2">Payment Initialization Failed</h4>
                <p class="text-sm text-red-600 dark:text-red-400 mb-4" id="errorMessage">An error occurred while initializing the payment.</p>
                <button onclick="closeBankTransferModal()" 
                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPaymentId = null;
let paymentStatusInterval = null;

function openBankTransferModal(orderId, amount) {
    document.getElementById('bankTransferModal').classList.remove('hidden');
    document.getElementById('bankTransferLoading').classList.remove('hidden');
    document.getElementById('bankTransferDetails').classList.add('hidden');
    document.getElementById('bankTransferError').classList.add('hidden');
    document.getElementById('partialPaymentSection').classList.add('hidden');

    // Initialize payment
    initializeBankTransferPayment(orderId, amount);
}

function closeBankTransferModal() {
    document.getElementById('bankTransferModal').classList.add('hidden');
    if (paymentStatusInterval) {
        clearInterval(paymentStatusInterval);
        paymentStatusInterval = null;
    }
}

function initializeBankTransferPayment(orderId, amount) {
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
        if (data.success) {
            displayPaymentDetails(data.data);
            startPaymentStatusCheck(data.data.payment_id);
        } else {
            showPaymentError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showPaymentError('Failed to initialize payment. Please try again.');
    });
}

function displayPaymentDetails(paymentData) {
    document.getElementById('bankTransferLoading').classList.add('hidden');
    document.getElementById('bankTransferDetails').classList.remove('hidden');

    // Set payment details
    document.getElementById('paymentAmount').textContent = '₦' + parseFloat(paymentData.amount).toLocaleString();
    document.getElementById('serviceCharge').textContent = '₦' + parseFloat(paymentData.service_charge_amount).toLocaleString();
    document.getElementById('totalAmount').textContent = '₦' + (parseFloat(paymentData.amount) + parseFloat(paymentData.service_charge_amount)).toLocaleString();
    
    document.getElementById('virtualAccountNumber').value = paymentData.virtual_account_number;
    document.getElementById('bankName').value = paymentData.bank_name;
    document.getElementById('accountName').value = paymentData.account_name;
    document.getElementById('paymentReference').value = paymentData.payment_reference;
    
    document.getElementById('rewardPointsRate').textContent = paymentData.reward_points_rate;
    document.getElementById('rewardThreshold').textContent = paymentData.reward_points_threshold;
    
    // Set payment instructions
    const instructions = paymentData.payment_instructions.split('\n');
    const instructionsHtml = instructions.map(instruction => `<div>${instruction}</div>`).join('');
    document.getElementById('paymentInstructions').innerHTML = instructionsHtml;
    
    // Set expiration time
    const expiresAt = new Date(paymentData.expires_at);
    document.getElementById('timeRemaining').textContent = getTimeRemaining(expiresAt);
    
    currentPaymentId = paymentData.payment_id;
}

function startPaymentStatusCheck(paymentId) {
    // Check status immediately
    checkPaymentStatus();
    
    // Check status every 30 seconds
    paymentStatusInterval = setInterval(() => {
        checkPaymentStatus();
    }, 30000);
}

function checkPaymentStatus() {
    if (!currentPaymentId) return;
    
    fetch(`/bank-transfer/status/${currentPaymentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updatePaymentStatus(data.data);
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
        });
}

function updatePaymentStatus(statusData) {
    const statusBadge = document.getElementById('statusBadge');
    const statusText = statusData.status.charAt(0).toUpperCase() + statusData.status.slice(1);
    
    // Update status badge
    statusBadge.textContent = statusText;
    statusBadge.className = 'px-2 py-1 text-xs font-medium rounded-full';
    
    switch (statusData.status) {
        case 'pending':
            statusBadge.classList.add('bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900', 'dark:text-yellow-200');
            break;
        case 'partial':
            statusBadge.classList.add('bg-orange-100', 'text-orange-800', 'dark:bg-orange-900', 'dark:text-orange-200');
            showPartialPaymentSection(statusData);
            break;
        case 'completed':
            statusBadge.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-900', 'dark:text-green-200');
            handlePaymentCompleted();
            break;
        case 'failed':
            statusBadge.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-900', 'dark:text-red-200');
            break;
        case 'expired':
            statusBadge.classList.add('bg-gray-100', 'text-gray-800', 'dark:bg-gray-900', 'dark:text-gray-200');
            break;
    }
    
    // Update time remaining
    document.getElementById('timeRemaining').textContent = statusData.time_remaining;
}

function showPartialPaymentSection(statusData) {
    document.getElementById('partialPaymentSection').classList.remove('hidden');
    document.getElementById('amountPaid').textContent = '₦' + parseFloat(statusData.amount_paid).toLocaleString();
    document.getElementById('amountRemaining').textContent = '₦' + parseFloat(statusData.amount_remaining).toLocaleString();
}

function generateNewAccount() {
    if (!currentPaymentId) return;
    
    fetch(`/bank-transfer/generate-new-account/${currentPaymentId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update account details
            document.getElementById('virtualAccountNumber').value = data.data.virtual_account_number;
            document.getElementById('bankName').value = data.data.bank_name;
            document.getElementById('accountName').value = data.data.account_name;
            document.getElementById('paymentReference').value = data.data.payment_reference;
            
            // Update amounts
            document.getElementById('amountRemaining').textContent = '₦' + parseFloat(data.data.amount_remaining).toLocaleString();
            document.getElementById('serviceCharge').textContent = '₦' + parseFloat(data.data.service_charge_amount).toLocaleString();
            
            // Show success message
            showNotification('New account generated successfully!', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to generate new account', 'error');
    });
}

function handlePaymentCompleted() {
    showNotification('Payment completed successfully! Your order has been confirmed.', 'success');
    
    // Clear interval
    if (paymentStatusInterval) {
        clearInterval(paymentStatusInterval);
        paymentStatusInterval = null;
    }
    
    // Close modal after 3 seconds
    setTimeout(() => {
        closeBankTransferModal();
        // Redirect to order confirmation or refresh page
        window.location.reload();
    }, 3000);
}

function showPaymentError(message) {
    document.getElementById('bankTransferLoading').classList.add('hidden');
    document.getElementById('bankTransferError').classList.remove('hidden');
    document.getElementById('errorMessage').textContent = message;
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand('copy');
    
    showNotification('Copied to clipboard!', 'success');
}

function getTimeRemaining(expiresAt) {
    const now = new Date();
    const diff = expiresAt - now;
    
    if (diff <= 0) {
        return 'Expired';
    }
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    
    if (hours > 0) {
        return `${hours}h ${minutes}m`;
    } else {
        return `${minutes}m`;
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script> 