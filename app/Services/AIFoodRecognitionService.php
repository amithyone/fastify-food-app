<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIFoodRecognitionService
{
    protected $apiKey;
    protected $apiUrl = 'https://api.logmeal.es/v2/recognition/dish';

    public function __construct()
    {
        // Using a free food recognition API
        $this->apiKey = config('services.food_recognition.api_key', 'demo');
    }

    /**
     * Recognize food from uploaded image
     */
    public function recognizeFood(UploadedFile $image)
    {
        try {
            // For demo purposes, we'll use a mock response
            // In production, you'd use a real food recognition API
            $mockResponse = $this->getMockFoodRecognition($image);
            
            return [
                'success' => true,
                'food_name' => $mockResponse['food_name'],
                'category' => $mockResponse['category'],
                'description' => $mockResponse['description'],
                'confidence' => $mockResponse['confidence'],
                'ingredients' => $mockResponse['ingredients'] ?? '',
                'allergens' => $mockResponse['allergens'] ?? '',
                'is_vegetarian' => $mockResponse['is_vegetarian'] ?? false,
                'is_spicy' => $mockResponse['is_spicy'] ?? false,
            ];
        } catch (\Exception $e) {
            Log::error('Food recognition error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Unable to recognize food from image. Please try again or add manually.',
                'food_name' => '',
                'category' => '',
                'description' => '',
                'confidence' => 0,
            ];
        }
    }

    /**
     * Mock food recognition for demo purposes
     * In production, replace this with actual API call
     */
    private function getMockFoodRecognition(UploadedFile $image)
    {
        $fileName = strtolower($image->getClientOriginalName());
        
        // Simple pattern matching based on filename
        if (str_contains($fileName, 'pizza') || str_contains($fileName, 'pizza')) {
            return [
                'food_name' => 'Margherita Pizza',
                'category' => 'Pizza',
                'description' => 'Classic Italian pizza with tomato sauce, mozzarella cheese, and fresh basil. Made with our signature thin crust and premium ingredients.',
                'confidence' => 95,
                'ingredients' => 'Tomato sauce, mozzarella cheese, fresh basil, olive oil',
                'allergens' => 'Dairy, Gluten',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
        } elseif (str_contains($fileName, 'burger') || str_contains($fileName, 'hamburger')) {
            return [
                'food_name' => 'Classic Beef Burger',
                'category' => 'Burgers',
                'description' => 'Juicy beef patty with fresh lettuce, tomato, onion, and our special sauce. Served on a toasted brioche bun.',
                'confidence' => 92,
                'ingredients' => 'Beef patty, lettuce, tomato, onion, cheese, special sauce',
                'allergens' => 'Dairy, Gluten',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
        } elseif (str_contains($fileName, 'chicken') || str_contains($fileName, 'poultry')) {
            return [
                'food_name' => 'Grilled Chicken Breast',
                'category' => 'Chicken',
                'description' => 'Tender grilled chicken breast seasoned with herbs and spices. Served with your choice of sauce.',
                'confidence' => 88,
                'ingredients' => 'Chicken breast, herbs, spices, olive oil',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
        } elseif (str_contains($fileName, 'pasta') || str_contains($fileName, 'spaghetti')) {
            return [
                'food_name' => 'Spaghetti Carbonara',
                'category' => 'Pasta',
                'description' => 'Traditional Italian pasta with eggs, cheese, pancetta, and black pepper. Creamy and delicious.',
                'confidence' => 90,
                'ingredients' => 'Spaghetti, eggs, parmesan cheese, pancetta, black pepper',
                'allergens' => 'Dairy, Eggs, Gluten',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
        } elseif (str_contains($fileName, 'salad') || str_contains($fileName, 'vegetable')) {
            return [
                'food_name' => 'Fresh Garden Salad',
                'category' => 'Salads',
                'description' => 'Crisp mixed greens with cherry tomatoes, cucumber, red onion, and balsamic vinaigrette.',
                'confidence' => 85,
                'ingredients' => 'Mixed greens, cherry tomatoes, cucumber, red onion, balsamic vinaigrette',
                'allergens' => 'None',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
        } elseif (str_contains($fileName, 'fish') || str_contains($fileName, 'salmon')) {
            return [
                'food_name' => 'Grilled Salmon',
                'category' => 'Seafood',
                'description' => 'Fresh Atlantic salmon grilled to perfection with lemon and herbs. Served with seasonal vegetables.',
                'confidence' => 87,
                'ingredients' => 'Atlantic salmon, lemon, herbs, olive oil',
                'allergens' => 'Fish',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
        } elseif (str_contains($fileName, 'dessert') || str_contains($fileName, 'cake')) {
            return [
                'food_name' => 'Chocolate Cake',
                'category' => 'Desserts',
                'description' => 'Rich chocolate cake with chocolate ganache frosting. Moist and decadent.',
                'confidence' => 93,
                'ingredients' => 'Chocolate, flour, eggs, sugar, butter, cocoa powder',
                'allergens' => 'Dairy, Eggs, Gluten',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
        } elseif (str_contains($fileName, 'drink') || str_contains($fileName, 'beverage')) {
            return [
                'food_name' => 'Fresh Fruit Smoothie',
                'category' => 'Beverages',
                'description' => 'Blend of fresh fruits with yogurt and honey. Refreshing and healthy.',
                'confidence' => 89,
                'ingredients' => 'Mixed fruits, yogurt, honey, ice',
                'allergens' => 'Dairy',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
        } else {
            // Default response for unrecognized food
            return [
                'food_name' => 'Delicious Dish',
                'category' => 'Main Course',
                'description' => 'A delicious dish prepared with fresh ingredients and traditional cooking methods.',
                'confidence' => 75,
                'ingredients' => 'Fresh ingredients, herbs, spices',
                'allergens' => 'May contain allergens',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
        }
    }

    /**
     * Get available categories for food recognition
     */
    public function getAvailableCategories()
    {
        return [
            'Pizza',
            'Burgers',
            'Chicken',
            'Pasta',
            'Salads',
            'Seafood',
            'Desserts',
            'Beverages',
            'Main Course',
            'Appetizers',
            'Soups',
            'Sandwiches',
            'Wraps',
            'Rice Dishes',
            'Noodles',
        ];
    }

    /**
     * Suggest price based on food category and name
     */
    public function suggestPrice($category, $foodName)
    {
        $basePrices = [
            'Pizza' => 2500,
            'Burgers' => 1800,
            'Chicken' => 2200,
            'Pasta' => 2000,
            'Salads' => 1200,
            'Seafood' => 3500,
            'Desserts' => 800,
            'Beverages' => 600,
            'Main Course' => 2500,
            'Appetizers' => 1000,
            'Soups' => 800,
            'Sandwiches' => 1500,
            'Wraps' => 1600,
            'Rice Dishes' => 1800,
            'Noodles' => 1700,
        ];

        return $basePrices[$category] ?? 2000;
    }
} 