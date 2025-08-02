@extends('layouts.app')

@section('title', 'All Restaurants - Fastify')

@section('content')
<!-- Fixed/Sticky Top Bar: always at the very top -->
<div class="fixed top-0 left-0 right-0 z-50 bg-[#f1ecdc] dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 py-2 shadow-lg max-w-md mx-auto w-full mt-15">
    <div class="flex items-center gap-2 px-4">
        <!-- Back Button -->
        <button onclick="history.back()" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-orange-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i class="fas fa-arrow-left"></i>
        </button>
        <!-- Title -->
        <div class="flex-1 text-center">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">All Restaurants</h1>
        </div>
        <!-- Theme Toggle Button -->
        <button id="themeToggle" class="w-10 h-10 flex items-center justify-center rounded-full bg-orange-100 dark:bg-gray-700 text-orange-500 dark:text-yellow-300 transition hover:bg-orange-200 dark:hover:bg-gray-600">
            <i id="themeIcon" class="fas fa-moon"></i>
        </button>
    </div>
</div>

<div class="w-full min-h-screen bg-[#f1ecdc] dark:bg-gray-900">
    <div class="max-w-md mx-auto px-4 py-4">
        <!-- Content starts after fixed header -->
        <div style="margin-top: 60px;">
            <!-- Location Dropdown -->
            <div class="mb-6">
                <label for="stateSelect" class="block text-sm font-semibold text-gray-700 dark:text-white mb-2">Select State</label>
                <select id="stateSelect" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                    <option value="">All States</option>
                    <option value="Abuja">Abuja</option>
                    <option value="Lagos">Lagos</option>
                    <option value="Port Harcourt">Port Harcourt</option>
                    <option value="Kaduna">Kaduna</option>
                    <option value="Kano">Kano</option>
                    <option value="Enugu">Enugu</option>
                    <option value="Uyo">Uyo</option>
                    <option value="London">London</option>
                </select>
            </div>

            <!-- Location Categories Slider -->
            <div class="mb-6">
                <div class="flex gap-3 overflow-x-auto pb-2 hide-scrollbar w-full whitespace-nowrap">
                    <!-- Abuja Areas -->
                    <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-blue-200 to-blue-400 dark:from-blue-700 dark:to-blue-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="filterByLocation('Wuse')">
                        <div class="text-lg mb-1">🏢</div>
                        <div class="text-center">
                            <div class="font-bold">Wuse</div>
                            <div>Abuja</div>
                        </div>
                    </div>
                    
                    <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-green-200 to-green-400 dark:from-green-700 dark:to-green-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="filterByLocation('Garki')">
                        <div class="text-lg mb-1">🏛️</div>
                        <div class="text-center">
                            <div class="font-bold">Garki</div>
                            <div>Abuja</div>
                        </div>
                    </div>
                    
                    <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-purple-200 to-purple-400 dark:from-purple-700 dark:to-purple-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="filterByLocation('Maitama')">
                        <div class="text-lg mb-1">🏘️</div>
                        <div class="text-center">
                            <div class="font-bold">Maitama</div>
                            <div>Abuja</div>
                        </div>
                    </div>
                    
                    <!-- Lagos Areas -->
                    <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-yellow-200 to-yellow-400 dark:from-yellow-700 dark:to-yellow-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="filterByLocation('Victoria Island')">
                        <div class="text-lg mb-1">🏝️</div>
                        <div class="text-center">
                            <div class="font-bold">Victoria</div>
                            <div>Island</div>
                        </div>
                    </div>
                    
                    <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-red-200 to-red-400 dark:from-red-700 dark:to-red-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="filterByLocation('Lekki')">
                        <div class="text-lg mb-1">🌊</div>
                        <div class="text-center">
                            <div class="font-bold">Lekki</div>
                            <div>Lagos</div>
                        </div>
                    </div>
                    
                    <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-indigo-200 to-indigo-400 dark:from-indigo-700 dark:to-indigo-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="filterByLocation('Ikeja')">
                        <div class="text-lg mb-1">✈️</div>
                        <div class="text-center">
                            <div class="font-bold">Ikeja</div>
                            <div>Lagos</div>
                        </div>
                    </div>
                    
                    <!-- Port Harcourt Areas -->
                    <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-teal-200 to-teal-400 dark:from-teal-700 dark:to-teal-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="filterByLocation('GRA')">
                        <div class="text-lg mb-1">🏠</div>
                        <div class="text-center">
                            <div class="font-bold">GRA</div>
                            <div>Port Harcourt</div>
                        </div>
                    </div>
                    
                    <!-- London Areas -->
                    <div class="flex-shrink-0 w-20 h-28 bg-gradient-to-tr from-pink-200 to-pink-400 dark:from-pink-700 dark:to-pink-900 rounded-xl flex flex-col items-center justify-center text-xs font-semibold text-white dark:text-white shadow cursor-pointer hover:scale-105 transition-transform" onclick="filterByLocation('Westminster')">
                        <div class="text-lg mb-1">🏰</div>
                        <div class="text-center">
                            <div class="font-bold">Westminster</div>
                            <div>London</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search restaurants..." class="w-full px-4 py-3 pl-10 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-300 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-100">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-300"></i>
                </div>
            </div>

            <!-- Restaurants Grid -->
            <div class="grid grid-cols-2 gap-4" id="restaurantsGrid">
                @foreach($restaurants as $restaurant)
                    @include('components.restaurant-card', ['restaurant' => $restaurant])
                @endforeach
            </div>

            <!-- Restaurant Details Modal -->
            <div id="restaurantModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 hidden">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-md max-h-[80vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="restaurantModalTitle" class="text-xl font-bold text-gray-900 dark:text-white">Restaurant Details</h3>
                        <button onclick="closeRestaurantModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="restaurantModalContent" class="text-gray-700 dark:text-gray-300">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Rating Modal -->
            <div id="ratingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 hidden">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="ratingModalTitle" class="text-xl font-bold text-gray-900 dark:text-white">Rate Restaurant</h3>
                        <button onclick="closeRatingModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="ratingModalContent" class="text-gray-700 dark:text-gray-300">
                        <div class="text-center mb-6">
                            <h4 id="restaurantName" class="text-lg font-semibold text-gray-900 dark:text-white mb-4"></h4>
                            
                            <!-- Star Rating -->
                            <div class="flex justify-center items-center mb-4">
                                <div class="flex space-x-2" id="starRating">
                                    <i class="far fa-star text-2xl text-orange-400 cursor-pointer hover:text-orange-500 transition-colors" data-rating="1"></i>
                                    <i class="far fa-star text-2xl text-orange-400 cursor-pointer hover:text-orange-500 transition-colors" data-rating="2"></i>
                                    <i class="far fa-star text-2xl text-orange-400 cursor-pointer hover:text-orange-500 transition-colors" data-rating="3"></i>
                                    <i class="far fa-star text-2xl text-orange-400 cursor-pointer hover:text-orange-500 transition-colors" data-rating="4"></i>
                                    <i class="far fa-star text-2xl text-orange-400 cursor-pointer hover:text-orange-500 transition-colors" data-rating="5"></i>
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Tap the stars to rate</p>
                            
                            <!-- Comment -->
                            <div class="mb-6">
                                <label for="ratingComment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment (Optional)</label>
                                <textarea id="ratingComment" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none" placeholder="Share your experience..."></textarea>
                            </div>
                            
                            <!-- Submit Button -->
                            <button id="submitRating" onclick="submitRating()" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                Submit Rating
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden text-center py-8">
                <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No restaurants found</h3>
                <p class="text-gray-600 dark:text-gray-400">Try adjusting your search or location filter</p>
            </div>
        </div>
    </div>
</div>

<script>
let currentRestaurantId = null;
let currentRestaurantName = null;
let selectedRating = 0;

// Theme toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const searchInput = document.getElementById('searchInput');
    const stateSelect = document.getElementById('stateSelect');
    const restaurantsGrid = document.getElementById('restaurantsGrid');
    const noResults = document.getElementById('noResults');
    
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.classList.toggle('dark', currentTheme === 'dark');
    updateThemeIcon(currentTheme);
    
    themeToggle.addEventListener('click', function() {
        const isDark = document.documentElement.classList.toggle('dark');
        const theme = isDark ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        updateThemeIcon(theme);
    });
    
    function updateThemeIcon(theme) {
        themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
    
    // Search functionality
    function filterRestaurants() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedState = stateSelect.value.toLowerCase();
        const restaurantCards = document.querySelectorAll('.restaurant-card');
        let visibleCount = 0;
        
        restaurantCards.forEach(card => {
            const name = card.dataset.name;
            const cuisine = card.dataset.cuisine;
            const location = card.dataset.location;
            
            const matchesSearch = name.includes(searchTerm) || cuisine.includes(searchTerm);
            const matchesState = !selectedState || location.includes(selectedState);
            
            if (matchesSearch && matchesState) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterRestaurants);
    stateSelect.addEventListener('change', filterRestaurants);
});

// Restaurant Details Modal Functions
function showRestaurantDetails(restaurantId) {
    const modal = document.getElementById('restaurantModal');
    const modalTitle = document.getElementById('restaurantModalTitle');
    const modalContent = document.getElementById('restaurantModalContent');
    
    modal.classList.remove('hidden');
    modalTitle.textContent = 'Loading...';
    modalContent.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin text-2xl text-orange-500"></i></div>';
    
    // Fetch restaurant details from API
    fetch(`/api/restaurants/${restaurantId}/details`)
        .then(response => response.json())
        .then(data => {
            modalTitle.textContent = data.name;
            modalContent.innerHTML = `
                <div class="space-y-4">
                    ${data.logo ? `<img src="${data.logo}" alt="${data.name}" class="w-16 h-16 object-contain mx-auto rounded-lg">` : ''}
                    
                    <div class="text-center">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">${data.name}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">${data.cuisine_type || 'Restaurant'}</p>
                    </div>
                    
                    ${data.description ? `<div>
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">About</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">${data.description}</p>
                    </div>` : ''}
                    
                    ${data.address ? `<div>
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">Location</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                            ${data.address}
                        </p>
                    </div>` : ''}
                    
                    ${data.whatsapp_number ? `<div>
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">Contact</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <i class="fab fa-whatsapp text-green-500 mr-2"></i>
                            ${data.whatsapp_number}
                        </p>
                    </div>` : ''}
                    
                    ${data.opening_hours ? `<div>
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">Opening Hours</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">${data.opening_hours}</p>
                    </div>` : ''}
                    
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-utensils mr-1"></i>
                            ${data.menu_items_count} menu items
                        </div>
                        <div class="flex items-center">
                            <div class="flex text-orange-400 mr-2">
                                ${generateStarRating(data.average_rating || 0)}
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">(${data.average_rating ? data.average_rating.toFixed(1) : '0.0'})</span>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            modalTitle.textContent = 'Error';
            modalContent.innerHTML = '<p class="text-red-500">Failed to load restaurant details.</p>';
        });
}

function closeRestaurantModal() {
    const modal = document.getElementById('restaurantModal');
    modal.classList.add('hidden');
}

// Generate star rating HTML
function generateStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating - fullStars >= 0.5;
    let starsHtml = '';
    
    for (let i = 1; i <= 5; i++) {
        if (i <= fullStars) {
            starsHtml += '<i class="fas fa-star text-sm"></i>';
        } else if (i === fullStars + 1 && hasHalfStar) {
            starsHtml += '<i class="fas fa-star-half-alt text-sm"></i>';
        } else {
            starsHtml += '<i class="far fa-star text-sm"></i>';
        }
    }
    
    return starsHtml;
}

// Rating Modal Functions
function showRatingModal(restaurantId, restaurantName) {
    currentRestaurantId = restaurantId;
    currentRestaurantName = restaurantName;
    selectedRating = 0;
    
    const modal = document.getElementById('ratingModal');
    const restaurantNameElement = document.getElementById('restaurantName');
    const submitButton = document.getElementById('submitRating');
    
    modal.classList.remove('hidden');
    restaurantNameElement.textContent = restaurantName;
    submitButton.disabled = true;
    
    // Reset stars
    const stars = document.querySelectorAll('#starRating i');
    stars.forEach(star => {
        star.className = 'far fa-star text-2xl text-orange-400 cursor-pointer hover:text-orange-500 transition-colors';
    });
    
    // Check if user has already rated this restaurant
    fetch(`/ratings/restaurant/${restaurantId}/user`)
        .then(response => response.json())
        .then(data => {
            if (data.rating) {
                selectedRating = data.rating.rating;
                updateStars(selectedRating);
                submitButton.textContent = 'Update Rating';
            } else {
                submitButton.textContent = 'Submit Rating';
            }
        })
        .catch(error => {
            console.error('Error fetching user rating:', error);
        });
}

function closeRatingModal() {
    const modal = document.getElementById('ratingModal');
    modal.classList.add('hidden');
}

function updateStars(rating) {
    const stars = document.querySelectorAll('#starRating i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.className = 'fas fa-star text-2xl text-orange-400 cursor-pointer hover:text-orange-500 transition-colors';
        } else {
            star.className = 'far fa-star text-2xl text-orange-400 cursor-pointer hover:text-orange-500 transition-colors';
        }
    });
}

function submitRating() {
    if (selectedRating === 0) {
        alert('Please select a rating');
        return;
    }
    
    const comment = document.getElementById('ratingComment').value;
    const submitButton = document.getElementById('submitRating');
    
    submitButton.disabled = true;
    submitButton.textContent = 'Submitting...';
    
    fetch(`/ratings/restaurant/${currentRestaurantId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            rating: selectedRating,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeRatingModal();
            // Optionally refresh the page to update ratings
            location.reload();
        } else {
            alert('Failed to submit rating. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error submitting rating:', error);
        alert('Failed to submit rating. Please try again.');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.textContent = 'Submit Rating';
    });
}

// Star rating event listeners
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('#starRating i');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.getAttribute('data-rating'));
            updateStars(selectedRating);
            
            const submitButton = document.getElementById('submitRating');
            submitButton.disabled = false;
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            updateStars(rating);
        });
        
        star.addEventListener('mouseleave', function() {
            updateStars(selectedRating);
        });
    });
});

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const restaurantModal = document.getElementById('restaurantModal');
    const ratingModal = document.getElementById('ratingModal');
    
    if (event.target === restaurantModal) {
        closeRestaurantModal();
    }
    
    if (event.target === ratingModal) {
        closeRatingModal();
    }
});

// Close modals on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRestaurantModal();
        closeRatingModal();
    }
});

// Location filter function
function filterByLocation(location) {
    const stateSelect = document.getElementById('stateSelect');
    const searchInput = document.getElementById('searchInput');
    
    // Set the state dropdown based on location
    if (location.includes('Abuja')) {
        stateSelect.value = 'Abuja';
    } else if (location.includes('Lagos')) {
        stateSelect.value = 'Lagos';
    } else if (location.includes('Port Harcourt')) {
        stateSelect.value = 'Port Harcourt';
    } else if (location.includes('London')) {
        stateSelect.value = 'London';
    }
    
    // Trigger the filter
    const event = new Event('change');
    stateSelect.dispatchEvent(event);
}
</script>
@endsection 