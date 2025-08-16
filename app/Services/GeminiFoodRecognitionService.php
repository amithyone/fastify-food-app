<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiFoodRecognitionService
{
    protected $apiKey;
    protected $model;
    protected $maxTokens;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.google_gemini.api_key');
        $this->model = config('services.google_gemini.model', 'gemini-1.5-flash');
        $this->maxTokens = config('services.google_gemini.max_tokens', 2048);
    }

    /**
     * Recognize food from uploaded image using Google Gemini
     */
    public function recognizeFood(UploadedFile $image)
    {
        try {
            // Validate image
            if (!$image || !$image->isValid()) {
                Log::error('Invalid image uploaded for Gemini recognition', [
                    'image_valid' => $image ? $image->isValid() : false,
                    'image_name' => $image ? $image->getClientOriginalName() : 'null',
                    'image_size' => $image ? $image->getSize() : 0,
                    'image_mime' => $image ? $image->getMimeType() : 'null'
                ]);
                return $this->getErrorResponse('Invalid image file. Please try again with a valid image.');
            }

            // Check if API key is configured
            if (empty($this->apiKey)) {
                Log::warning('Google Gemini API key not configured');
                return $this->getErrorResponse('Google Gemini API not configured. Please contact administrator.');
            }

            // Log image details
            Log::info('Starting Gemini food recognition', [
                'file_name' => $image->getClientOriginalName(),
                'file_size' => $image->getSize(),
                'file_mime' => $image->getMimeType(),
                'model' => $this->model,
                'max_tokens' => $this->maxTokens
            ]);

            // Convert image to base64
            $imageData = base64_encode(file_get_contents($image->getPathname()));
            $mimeType = $image->getMimeType();

            // Prepare the prompt for food recognition
            $prompt = $this->buildFoodRecognitionPrompt();

            // Make API request to Gemini
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => $this->maxTokens,
                    'temperature' => 0.1, // Low temperature for more consistent results
                    'topP' => 0.8,
                    'topK' => 40
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->parseGeminiResponse($data);
            } else {
                Log::error('Gemini API request failed', [
                    'status_code' => $response->status(),
                    'response' => $response->body(),
                    'error' => $response->json()
                ]);
                return $this->getErrorResponse('Failed to recognize food. Please try again.');
            }

        } catch (\Exception $e) {
            Log::error('Gemini food recognition error', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return $this->getErrorResponse('Error processing image. Please try again.');
        }
    }

    /**
     * Build the prompt for food recognition
     */
    private function buildFoodRecognitionPrompt()
    {
        return "You are an expert food recognition AI. Analyze this image and identify the food item. 

Please provide your response in the following JSON format:
{
    \"food_name\": \"The name of the food item\",
    \"confidence\": 95,
    \"category\": \"main_course|appetizer|dessert|beverage|side_dish\",
    \"description\": \"A brief description of the food\",
    \"ingredients\": \"Common ingredients used in this dish\",
    \"allergens\": \"Potential allergens (if any)\",
    \"is_vegetarian\": true/false,
    \"is_spicy\": true/false,
    \"cuisine_type\": \"The type of cuisine (e.g., Nigerian, Italian, Chinese, etc.)\",
    \"cooking_method\": \"How this food is typically prepared\",
    \"nutritional_info\": \"Brief nutritional information\"
}

Important guidelines:
1. Be specific with food names (e.g., 'Jollof Rice' not just 'Rice')
2. For Nigerian/African food, use local names when appropriate
3. Confidence should be 0-100 based on how certain you are
4. If you can't identify the food clearly, set confidence below 50
5. Consider the visual appearance, color, texture, and presentation
6. For beverages, specify if it's hot or cold
7. For desserts, mention if it's sweet, creamy, etc.

Focus on accuracy and provide detailed, helpful information for restaurant menu management.";
    }

    /**
     * Parse Gemini API response
     */
    private function parseGeminiResponse($data)
    {
        try {
            // Extract the text content from Gemini response
            $textContent = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            Log::info('Gemini raw response', [
                'text_content' => $textContent,
                'response_length' => strlen($textContent)
            ]);

            // Try to extract JSON from the response
            $jsonMatch = $this->extractJsonFromText($textContent);
            
            if ($jsonMatch) {
                $foodData = json_decode($jsonMatch, true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($foodData['food_name'])) {
                    // Validate and enhance the data
                    $enhancedData = $this->enhanceFoodData($foodData);
                    
                    Log::info('Gemini food recognition successful', [
                        'food_name' => $enhancedData['food_name'],
                        'confidence' => $enhancedData['confidence'],
                        'category' => $enhancedData['category'],
                        'cuisine_type' => $enhancedData['cuisine_type'] ?? 'Unknown'
                    ]);

                    return [
                        'success' => true,
                        'food_name' => $enhancedData['food_name'],
                        'category' => $enhancedData['category'],
                        'description' => $enhancedData['description'],
                        'confidence' => $enhancedData['confidence'],
                        'ingredients' => $enhancedData['ingredients'],
                        'allergens' => $enhancedData['allergens'],
                        'is_vegetarian' => $enhancedData['is_vegetarian'],
                        'is_spicy' => $enhancedData['is_spicy'],
                        'cuisine_type' => $enhancedData['cuisine_type'],
                        'cooking_method' => $enhancedData['cooking_method'],
                        'nutritional_info' => $enhancedData['nutritional_info'],
                        'service_used' => 'Google Gemini',
                        'model' => $this->model
                    ];
                }
            }

            // Fallback: try to extract food name from text if JSON parsing fails
            $foodName = $this->extractFoodNameFromText($textContent);
            
            if ($foodName) {
                Log::info('Gemini fallback recognition', [
                    'food_name' => $foodName,
                    'raw_text' => $textContent
                ]);

                return [
                    'success' => true,
                    'food_name' => $foodName,
                    'category' => $this->categorizeFood($foodName),
                    'description' => $this->generateDescription($foodName),
                    'confidence' => 75, // Lower confidence for fallback
                    'ingredients' => $this->suggestIngredients($foodName),
                    'allergens' => $this->suggestAllergens($foodName),
                    'is_vegetarian' => $this->isVegetarian($foodName),
                    'is_spicy' => $this->isSpicy($foodName),
                    'cuisine_type' => $this->detectCuisineType($foodName),
                    'cooking_method' => $this->suggestCookingMethod($foodName),
                    'nutritional_info' => $this->suggestNutritionalInfo($foodName),
                    'service_used' => 'Google Gemini (Fallback)',
                    'model' => $this->model
                ];
            }

            Log::warning('Gemini response parsing failed', [
                'text_content' => $textContent,
                'json_match' => $jsonMatch ?? 'none'
            ]);

            return $this->getErrorResponse('Could not recognize food from image. Please try with a clearer image.');

        } catch (\Exception $e) {
            Log::error('Error parsing Gemini response', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return $this->getErrorResponse('Error processing AI response. Please try again.');
        }
    }

    /**
     * Extract JSON from text response
     */
    private function extractJsonFromText($text)
    {
        // Look for JSON pattern in the text
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            return $matches[0];
        }
        return null;
    }

    /**
     * Extract food name from text when JSON parsing fails
     */
    private function extractFoodNameFromText($text)
    {
        // Common patterns for food names in text
        $patterns = [
            '/food[:\s]+([^,\n]+)/i',
            '/dish[:\s]+([^,\n]+)/i',
            '/this is ([^,\n]+)/i',
            '/appears to be ([^,\n]+)/i',
            '/looks like ([^,\n]+)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $foodName = trim($matches[1]);
                // Clean up the food name
                $foodName = preg_replace('/[^\w\s\-]/', '', $foodName);
                return ucwords(strtolower($foodName));
            }
        }

        return null;
    }

    /**
     * Enhance and validate food data
     */
    private function enhanceFoodData($data)
    {
        return [
            'food_name' => $this->cleanFoodName($data['food_name'] ?? 'Unknown Food'),
            'confidence' => min(100, max(0, intval($data['confidence'] ?? 75))),
            'category' => $this->validateCategory($data['category'] ?? 'main_course'),
            'description' => $data['description'] ?? $this->generateDescription($data['food_name'] ?? ''),
            'ingredients' => $data['ingredients'] ?? $this->suggestIngredients($data['food_name'] ?? ''),
            'allergens' => $data['allergens'] ?? $this->suggestAllergens($data['food_name'] ?? ''),
            'is_vegetarian' => boolval($data['is_vegetarian'] ?? $this->isVegetarian($data['food_name'] ?? '')),
            'is_spicy' => boolval($data['is_spicy'] ?? $this->isSpicy($data['food_name'] ?? '')),
            'cuisine_type' => $data['cuisine_type'] ?? $this->detectCuisineType($data['food_name'] ?? ''),
            'cooking_method' => $data['cooking_method'] ?? $this->suggestCookingMethod($data['food_name'] ?? ''),
            'nutritional_info' => $data['nutritional_info'] ?? $this->suggestNutritionalInfo($data['food_name'] ?? '')
        ];
    }

    /**
     * Clean food name
     */
    private function cleanFoodName($name)
    {
        $name = trim($name);
        $name = preg_replace('/[^\w\s\-]/', '', $name);
        return ucwords(strtolower($name));
    }

    /**
     * Validate category
     */
    private function validateCategory($category)
    {
        $validCategories = ['main_course', 'appetizer', 'dessert', 'beverage', 'side_dish'];
        return in_array(strtolower($category), $validCategories) ? strtolower($category) : 'main_course';
    }

    /**
     * Categorize food based on name
     */
    private function categorizeFood($foodName)
    {
        $foodName = strtolower($foodName);
        
        $categories = [
            'dessert' => ['cake', 'ice cream', 'pudding', 'chocolate', 'sweet', 'pastry', 'cookie'],
            'beverage' => ['drink', 'juice', 'smoothie', 'tea', 'coffee', 'water', 'soda'],
            'appetizer' => ['starter', 'appetizer', 'snack', 'finger food'],
            'side_dish' => ['salad', 'vegetable', 'side', 'accompaniment']
        ];

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($foodName, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return 'main_course';
    }

    /**
     * Generate description
     */
    private function generateDescription($foodName)
    {
        return "Delicious {$foodName} prepared with fresh ingredients and authentic flavors.";
    }

    /**
     * Suggest ingredients
     */
    private function suggestIngredients($foodName)
    {
        $foodName = strtolower($foodName);
        
        // Nigerian food ingredients
        $nigerianIngredients = [
            'jollof' => 'Rice, tomatoes, peppers, onions, spices, oil',
            'egusi' => 'Melon seeds, vegetables, palm oil, meat/fish',
            'suya' => 'Beef, groundnut powder, spices, onions, tomatoes',
            'amala' => 'Yam flour, water, ewedu soup',
            'pepper soup' => 'Meat/fish, hot peppers, spices, herbs',
            'banga' => 'Palm fruit, meat/fish, spices, vegetables',
            'moi moi' => 'Beans, peppers, onions, oil, spices',
            'zobo' => 'Hibiscus flowers, ginger, pineapple, sugar',
            'kunu' => 'Millet, ginger, sugar, water'
        ];

        foreach ($nigerianIngredients as $dish => $ingredients) {
            if (strpos($foodName, $dish) !== false) {
                return $ingredients;
            }
        }

        return 'Fresh ingredients, herbs, and spices';
    }

    /**
     * Suggest allergens
     */
    private function suggestAllergens($foodName)
    {
        $foodName = strtolower($foodName);
        
        $allergens = [
            'nuts' => ['peanut', 'groundnut', 'almond', 'cashew'],
            'dairy' => ['milk', 'cheese', 'cream', 'yogurt'],
            'gluten' => ['wheat', 'bread', 'pasta', 'flour'],
            'seafood' => ['fish', 'shrimp', 'crab', 'lobster']
        ];

        $foundAllergens = [];
        foreach ($allergens as $allergen => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($foodName, $keyword) !== false) {
                    $foundAllergens[] = $allergen;
                    break;
                }
            }
        }

        return empty($foundAllergens) ? 'None known' : implode(', ', $foundAllergens);
    }

    /**
     * Check if vegetarian
     */
    private function isVegetarian($foodName)
    {
        $foodName = strtolower($foodName);
        $meatKeywords = ['beef', 'chicken', 'pork', 'lamb', 'fish', 'meat', 'suya'];
        
        foreach ($meatKeywords as $keyword) {
            if (strpos($foodName, $keyword) !== false) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if spicy
     */
    private function isSpicy($foodName)
    {
        $foodName = strtolower($foodName);
        $spicyKeywords = ['pepper', 'spicy', 'hot', 'chili', 'suya', 'pepper soup'];
        
        foreach ($spicyKeywords as $keyword) {
            if (strpos($foodName, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Detect cuisine type
     */
    private function detectCuisineType($foodName)
    {
        $foodName = strtolower($foodName);
        
        $nigerianDishes = ['jollof', 'egusi', 'suya', 'amala', 'pepper soup', 'banga', 'moi moi', 'zobo', 'kunu'];
        
        foreach ($nigerianDishes as $dish) {
            if (strpos($foodName, $dish) !== false) {
                return 'Nigerian';
            }
        }
        
        return 'International';
    }

    /**
     * Suggest cooking method
     */
    private function suggestCookingMethod($foodName)
    {
        $foodName = strtolower($foodName);
        
        if (strpos($foodName, 'soup') !== false) {
            return 'Simmered';
        } elseif (strpos($foodName, 'grill') !== false || strpos($foodName, 'suya') !== false) {
            return 'Grilled';
        } elseif (strpos($foodName, 'fried') !== false) {
            return 'Fried';
        } elseif (strpos($foodName, 'baked') !== false) {
            return 'Baked';
        }
        
        return 'Traditional cooking method';
    }

    /**
     * Suggest nutritional info
     */
    private function suggestNutritionalInfo($foodName)
    {
        return 'Contains essential nutrients and vitamins. Portion size and nutritional content may vary.';
    }

    /**
     * Get error response
     */
    private function getErrorResponse($message)
    {
        return [
            'success' => false,
            'error' => $message,
            'service_used' => 'Google Gemini',
            'model' => $this->model
        ];
    }
}
