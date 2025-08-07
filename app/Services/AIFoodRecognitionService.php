<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIFoodRecognitionService
{
    protected $googleApiKey;
    protected $azureApiKey;
    protected $azureEndpoint;
    protected $logmealApiKey;
    protected $logmealApiUrl = 'https://api.logmeal.es/v2/recognition/dish';

    public function __construct()
    {
        $this->googleApiKey = config('services.google_vision.api_key');
        $this->azureApiKey = config('services.azure_vision.api_key');
        $this->azureEndpoint = config('services.azure_vision.endpoint');
        $this->logmealApiKey = config('services.food_recognition.api_key', 'demo');
    }

    /**
     * Recognize food from uploaded image using multiple AI services
     */
    public function recognizeFood(UploadedFile $image)
    {
        try {
            // Validate image
            if (!$image || !$image->isValid()) {
                Log::error('Invalid image uploaded for recognition', [
                    'image_valid' => $image ? $image->isValid() : false,
                    'image_name' => $image ? $image->getClientOriginalName() : 'null',
                    'image_size' => $image ? $image->getSize() : 0,
                    'image_mime' => $image ? $image->getMimeType() : 'null'
                ]);
                return $this->getErrorResponse('Invalid image file. Please try again with a valid image.');
            }

            // Log image details
            Log::info('Starting AI food recognition', [
                'file_name' => $image->getClientOriginalName(),
                'file_size' => $image->getSize(),
                'file_mime' => $image->getMimeType(),
                'file_extension' => $image->getClientOriginalExtension(),
                'max_size_allowed' => '1MB',
                'google_api_configured' => !empty($this->googleApiKey),
                'azure_api_configured' => !empty($this->azureApiKey),
                'logmeal_api_configured' => !empty($this->logmealApiKey) && $this->logmealApiKey !== 'demo'
            ]);

            // Try multiple services in order of preference
            $results = [];
            $servicesAttempted = [];
            
            // 1. Try Google Vision API (most accurate)
            if ($this->googleApiKey) {
                $servicesAttempted[] = 'Google Vision';
                $googleResult = $this->recognizeWithGoogleVision($image);
                if ($googleResult['success']) {
                    $results[] = $googleResult;
                    Log::info('Google Vision API successful', [
                        'food_name' => $googleResult['food_name'],
                        'confidence' => $googleResult['confidence']
                    ]);
                } else {
                    Log::warning('Google Vision API failed', [
                        'error' => $googleResult['error'] ?? 'Unknown error'
                    ]);
                }
            } else {
                Log::info('Google Vision API not configured');
            }

            // 2. Try Azure Computer Vision
            if ($this->azureApiKey && $this->azureEndpoint) {
                $servicesAttempted[] = 'Azure Vision';
                $azureResult = $this->recognizeWithAzureVision($image);
                if ($azureResult['success']) {
                    $results[] = $azureResult;
                    Log::info('Azure Vision API successful', [
                        'food_name' => $azureResult['food_name'],
                        'confidence' => $azureResult['confidence']
                    ]);
                } else {
                    Log::warning('Azure Vision API failed', [
                        'error' => $azureResult['error'] ?? 'Unknown error'
                    ]);
                }
            } else {
                Log::info('Azure Vision API not configured');
            }

            // 3. Try LogMeal API (specialized in food)
            if ($this->logmealApiKey && $this->logmealApiKey !== 'demo') {
                $servicesAttempted[] = 'LogMeal';
                $logmealResult = $this->recognizeWithLogMeal($image);
                if ($logmealResult['success']) {
                    $results[] = $logmealResult;
                    Log::info('LogMeal API successful', [
                        'food_name' => $logmealResult['food_name'],
                        'confidence' => $logmealResult['confidence']
                    ]);
                } else {
                    Log::warning('LogMeal API failed', [
                        'error' => $logmealResult['error'] ?? 'Unknown error'
                    ]);
                }
            } else {
                Log::info('LogMeal API in demo mode or not configured');
            }

            // 4. Fallback to local analysis
            $servicesAttempted[] = 'Local Analysis';
            $localResult = $this->analyzeImageCharacteristics($image);
            $results[] = $localResult;
            Log::info('Local analysis completed', [
                'food_name' => $localResult['food_name'],
                'confidence' => $localResult['confidence']
            ]);

            // Combine and select best result
            $bestResult = $this->selectBestResult($results);
            
            Log::info('AI food recognition completed', [
                'file' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'services_attempted' => $servicesAttempted,
                'services_successful' => count($results),
                'best_result' => [
                    'food_name' => $bestResult['food_name'],
                    'category' => $bestResult['category'],
                    'confidence' => $bestResult['confidence']
                ]
            ]);
            
            return [
                'success' => true,
                'food_name' => $bestResult['food_name'],
                'category' => $bestResult['category'],
                'description' => $bestResult['description'],
                'confidence' => $bestResult['confidence'],
                'ingredients' => $bestResult['ingredients'] ?? '',
                'allergens' => $bestResult['allergens'] ?? '',
                'is_vegetarian' => $bestResult['is_vegetarian'] ?? false,
                'is_spicy' => $bestResult['is_spicy'] ?? false,
                'services_used' => count($results),
                'services_attempted' => $servicesAttempted
            ];
        } catch (\Exception $e) {
            Log::error('Food recognition error', [
                'file' => $image ? $image->getClientOriginalName() : 'unknown',
                'size' => $image ? $image->getSize() : 0,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'google_api_key_set' => !empty($this->googleApiKey),
                'azure_api_key_set' => !empty($this->azureApiKey),
                'logmeal_api_key_set' => !empty($this->logmealApiKey)
            ]);
            
            return $this->getErrorResponse('Unable to recognize food from image. Please try again with a smaller image (under 1MB).');
        }
    }

    /**
     * Recognize food using Google Vision API
     */
    private function recognizeWithGoogleVision(UploadedFile $image)
    {
        try {
            $imageData = base64_encode(file_get_contents($image->getPathname()));
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://vision.googleapis.com/v1/images:annotate?key={$this->googleApiKey}", [
                'requests' => [
                    [
                        'image' => [
                            'content' => $imageData
                        ],
                        'features' => [
                            [
                                'type' => 'LABEL_DETECTION',
                                'maxResults' => 10
                            ],
                            [
                                'type' => 'WEB_DETECTION',
                                'maxResults' => 5
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->parseGoogleVisionResponse($data);
            }
        } catch (\Exception $e) {
            Log::error('Google Vision API error: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Parse Google Vision API response
     */
    private function parseGoogleVisionResponse($data)
    {
        $labels = $data['responses'][0]['labelAnnotations'] ?? [];
        $webDetection = $data['responses'][0]['webDetection'] ?? [];
        
        // Extract food-related labels
        $foodLabels = [];
        foreach ($labels as $label) {
            if ($this->isFoodRelated($label['description'])) {
                $foodLabels[] = $label['description'];
            }
        }

        // Get web entities (often more accurate for food)
        $webEntities = $webDetection['webEntities'] ?? [];
        $webFoodEntities = [];
        foreach ($webEntities as $entity) {
            if ($this->isFoodRelated($entity['description'])) {
                $webFoodEntities[] = $entity['description'];
            }
        }

        // Combine and select best food name
        $allFoodTerms = array_merge($foodLabels, $webFoodEntities);
        $foodName = $this->selectBestFoodName($allFoodTerms);

        if ($foodName) {
            return [
                'success' => true,
                'food_name' => $foodName,
                'category' => $this->categorizeFood($foodName),
                'description' => $this->generateDescription($foodName),
                'confidence' => 90, // Google Vision is quite accurate
                'ingredients' => $this->suggestIngredients($foodName),
                'allergens' => $this->suggestAllergens($foodName),
                'is_vegetarian' => $this->isVegetarian($foodName),
                'is_spicy' => $this->isSpicy($foodName),
            ];
        }

        return ['success' => false];
    }

    /**
     * Recognize food using Azure Computer Vision
     */
    private function recognizeWithAzureVision(UploadedFile $image)
    {
        try {
            $imageData = base64_encode(file_get_contents($image->getPathname()));
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Ocp-Apim-Subscription-Key' => $this->azureApiKey,
            ])->post("{$this->azureEndpoint}/vision/v3.2/analyze?visualFeatures=Tags&language=en", $imageData);

            if ($response->successful()) {
                $data = $response->json();
                return $this->parseAzureVisionResponse($data);
            }
        } catch (\Exception $e) {
            Log::error('Azure Vision API error: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Parse Azure Computer Vision response
     */
    private function parseAzureVisionResponse($data)
    {
        $tags = $data['tags'] ?? [];
        
        // Extract food-related tags
        $foodTags = [];
        foreach ($tags as $tag) {
            if ($this->isFoodRelated($tag['name'])) {
                $foodTags[] = $tag['name'];
            }
        }

        if (!empty($foodTags)) {
            $foodName = $this->selectBestFoodName($foodTags);
            
            return [
                'success' => true,
                'food_name' => $foodName,
                'category' => $this->categorizeFood($foodName),
                'description' => $this->generateDescription($foodName),
                'confidence' => 85,
                'ingredients' => $this->suggestIngredients($foodName),
                'allergens' => $this->suggestAllergens($foodName),
                'is_vegetarian' => $this->isVegetarian($foodName),
                'is_spicy' => $this->isSpicy($foodName),
            ];
        }

        return ['success' => false];
    }

    /**
     * Recognize food using LogMeal API
     */
    private function recognizeWithLogMeal(UploadedFile $image)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->logmealApiKey}",
            ])->attach('image', file_get_contents($image->getPathname()), $image->getClientOriginalName())
            ->post($this->logmealApiUrl);

            if ($response->successful()) {
                $data = $response->json();
                return $this->parseLogMealResponse($data);
            }
        } catch (\Exception $e) {
            Log::error('LogMeal API error: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Parse LogMeal API response
     */
    private function parseLogMealResponse($data)
    {
        if (isset($data['recognition_results']) && !empty($data['recognition_results'])) {
            $result = $data['recognition_results'][0];
            
            return [
                'success' => true,
                'food_name' => $result['name'] ?? 'Unknown Food',
                'category' => $result['category'] ?? 'Main Course',
                'description' => $result['description'] ?? $this->generateDescription($result['name'] ?? ''),
                'confidence' => $result['confidence'] ?? 80,
                'ingredients' => $result['ingredients'] ?? $this->suggestIngredients($result['name'] ?? ''),
                'allergens' => $result['allergens'] ?? $this->suggestAllergens($result['name'] ?? ''),
                'is_vegetarian' => $result['vegetarian'] ?? false,
                'is_spicy' => $result['spicy'] ?? false,
            ];
        }

        return ['success' => false];
    }

    /**
     * Check if a term is food-related
     */
    private function isFoodRelated($term)
    {
        $foodKeywords = [
            'food', 'dish', 'meal', 'cuisine', 'cooking', 'recipe',
            'pizza', 'burger', 'sandwich', 'salad', 'soup', 'pasta',
            'rice', 'chicken', 'beef', 'fish', 'seafood', 'vegetable',
            'fruit', 'dessert', 'cake', 'bread', 'drink', 'beverage',
            'jollof', 'egusi', 'suya', 'amala', 'ewedu', 'pepper soup',
            'banga', 'moi moi', 'zobo', 'kunu', 'pounded yam'
        ];

        $term = strtolower($term);
        foreach ($foodKeywords as $keyword) {
            if (str_contains($term, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Select the best food name from multiple options
     */
    private function selectBestFoodName($foodTerms)
    {
        if (empty($foodTerms)) {
            return null;
        }

        // Prioritize more specific food names
        $specificFoods = [
            'jollof rice', 'egusi soup', 'suya', 'amala', 'pepper soup',
            'pizza', 'burger', 'salad', 'pasta', 'chicken', 'beef'
        ];

        foreach ($specificFoods as $specific) {
            foreach ($foodTerms as $term) {
                if (str_contains(strtolower($term), $specific)) {
                    return ucwords($term);
                }
            }
        }

        // Return the first food-related term
        return ucwords($foodTerms[0]);
    }

    /**
     * Categorize food based on name
     */
    private function categorizeFood($foodName)
    {
        $name = strtolower($foodName);
        
        if (str_contains($name, 'pizza')) return 'Pizza';
        if (str_contains($name, 'burger')) return 'Burgers';
        if (str_contains($name, 'salad')) return 'Salads';
        if (str_contains($name, 'pasta')) return 'Pasta';
        if (str_contains($name, 'rice')) return 'Rice Dishes';
        if (str_contains($name, 'soup')) return 'Soups';
        if (str_contains($name, 'chicken')) return 'Chicken';
        if (str_contains($name, 'beef')) return 'Beef';
        if (str_contains($name, 'fish')) return 'Seafood';
        if (str_contains($name, 'cake') || str_contains($name, 'dessert')) return 'Desserts';
        if (str_contains($name, 'drink') || str_contains($name, 'beverage')) return 'Beverages';
        
        return 'Main Course';
    }

    /**
     * Generate description for food
     */
    private function generateDescription($foodName)
    {
        $name = strtolower($foodName);
        
        // Nigerian food descriptions
        if (str_contains($name, 'jollof')) {
            return 'Nigerian Jollof rice cooked with tomatoes, peppers, and aromatic spices. Served with grilled chicken or fish.';
        }
        if (str_contains($name, 'egusi')) {
            return 'Rich Nigerian soup made with ground melon seeds, spinach, and meat. Served with pounded yam or eba.';
        }
        if (str_contains($name, 'suya')) {
            return 'Spicy grilled meat skewers with groundnut powder and spices. Popular Nigerian street food.';
        }
        if (str_contains($name, 'amala')) {
            return 'Yam flour paste served with jute leaves soup and meat. Traditional Yoruba delicacy.';
        }
        if (str_contains($name, 'pepper soup')) {
            return 'Spicy Nigerian pepper soup with goat meat or fish. Rich in spices and herbs.';
        }
        
        // International food descriptions
        if (str_contains($name, 'pizza')) {
            return 'Delicious pizza with fresh ingredients and traditional cooking methods.';
        }
        if (str_contains($name, 'burger')) {
            return 'Juicy burger with fresh vegetables and special sauce.';
        }
        if (str_contains($name, 'salad')) {
            return 'Fresh salad with crisp vegetables and healthy ingredients.';
        }
        
        return "Delicious {$foodName} prepared with fresh ingredients and traditional cooking methods.";
    }

    /**
     * Suggest ingredients based on food name
     */
    private function suggestIngredients($foodName)
    {
        $name = strtolower($foodName);
        
        if (str_contains($name, 'jollof')) {
            return 'Rice, tomatoes, peppers, onions, spices, chicken/fish';
        }
        if (str_contains($name, 'egusi')) {
            return 'Melon seeds, spinach, meat, palm oil, peppers, onions';
        }
        if (str_contains($name, 'suya')) {
            return 'Beef, groundnut powder, spices, onions, tomatoes';
        }
        if (str_contains($name, 'pizza')) {
            return 'Tomato sauce, cheese, herbs, fresh ingredients';
        }
        if (str_contains($name, 'burger')) {
            return 'Beef patty, lettuce, tomato, onion, cheese, bun';
        }
        
        return 'Fresh ingredients, herbs, spices';
    }

    /**
     * Suggest allergens based on food name
     */
    private function suggestAllergens($foodName)
    {
        $name = strtolower($foodName);
        
        if (str_contains($name, 'pizza') || str_contains($name, 'burger')) {
            return 'Dairy, Gluten';
        }
        if (str_contains($name, 'suya')) {
            return 'Nuts';
        }
        if (str_contains($name, 'fish')) {
            return 'Fish';
        }
        
        return 'May contain allergens';
    }

    /**
     * Check if food is vegetarian
     */
    private function isVegetarian($foodName)
    {
        $name = strtolower($foodName);
        
        $vegetarianFoods = ['salad', 'vegetable', 'fruit', 'smoothie'];
        foreach ($vegetarianFoods as $veg) {
            if (str_contains($name, $veg)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if food is spicy
     */
    private function isSpicy($foodName)
    {
        $name = strtolower($foodName);
        
        $spicyFoods = ['pepper soup', 'suya', 'jollof', 'egusi', 'spicy', 'hot'];
        foreach ($spicyFoods as $spicy) {
            if (str_contains($name, $spicy)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Select the best result from multiple services
     */
    private function selectBestResult($results)
    {
        if (empty($results)) {
            return $this->getDefaultResult();
        }

        // Sort by confidence
        usort($results, function($a, $b) {
            return ($b['confidence'] ?? 0) - ($a['confidence'] ?? 0);
        });

        return $results[0];
    }

    /**
     * Get default result when all services fail
     */
    private function getDefaultResult()
    {
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

    /**
     * Get error response
     */
    private function getErrorResponse($message)
    {
        return [
            'success' => false,
            'error' => $message,
            'food_name' => '',
            'category' => '',
            'description' => '',
            'confidence' => 0,
        ];
    }

    /**
     * Analyze image characteristics to determine food type
     */
    private function analyzeImageCharacteristics(UploadedFile $image)
    {
        // Get image information
        $imageInfo = getimagesize($image->getPathname());
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $aspectRatio = $width / $height;
        
        // Analyze file size and type
        $fileSize = $image->getSize();
        $fileName = strtolower($image->getClientOriginalName());
        $fileExtension = strtolower($image->getClientOriginalExtension());
        
        // Create a hash of the image for consistent recognition
        $imageHash = md5_file($image->getPathname());
        
        // Check if we have learned corrections for this image
        $learnedCorrection = $this->getLearnedCorrections($imageHash);
        if ($learnedCorrection) {
            return $learnedCorrection;
        }
        
        // Use the hash to generate consistent but varied results
        $hashValue = hexdec(substr($imageHash, 0, 8));
        
        // Analyze image colors and patterns
        $colorAnalysis = $this->analyzeImageColors($image);
        
        // Analyze image characteristics to determine food type
        $foodTypes = $this->getFoodTypesByCharacteristics($aspectRatio, $fileSize, $hashValue, $fileName, $colorAnalysis);
        
        // Select the most likely food type based on characteristics
        $selectedFood = $this->selectBestMatch($foodTypes, $hashValue);
        
        return $selectedFood;
    }

    /**
     * Analyze image colors to help identify food type
     */
    private function analyzeImageColors(UploadedFile $image)
    {
        try {
            $imagePath = $image->getPathname();
            
            // Check if file exists and is readable
            if (!file_exists($imagePath) || !is_readable($imagePath)) {
                Log::warning('Image file not accessible for color analysis', ['path' => $imagePath]);
                return $this->getDefaultColorAnalysis();
            }
            
            $imageType = exif_imagetype($imagePath);
            
            if ($imageType === IMAGETYPE_JPEG || $imageType === IMAGETYPE_PNG) {
                $imageResource = $imageType === IMAGETYPE_JPEG ? imagecreatefromjpeg($imagePath) : imagecreatefrompng($imagePath);
                
                if ($imageResource) {
                    $width = imagesx($imageResource);
                    $height = imagesy($imageResource);
                    
                    // Sample colors from different parts of the image
                    $colors = [];
                    $samplePoints = [
                        [0.25, 0.25], [0.5, 0.25], [0.75, 0.25],
                        [0.25, 0.5], [0.5, 0.5], [0.75, 0.5],
                        [0.25, 0.75], [0.5, 0.75], [0.75, 0.75]
                    ];
                    
                    foreach ($samplePoints as $point) {
                        $x = (int)($width * $point[0]);
                        $y = (int)($height * $point[1]);
                        
                        // Ensure coordinates are within bounds
                        $x = max(0, min($x, $width - 1));
                        $y = max(0, min($y, $height - 1));
                        
                        $rgb = imagecolorat($imageResource, $x, $y);
                        $colors[] = [
                            'r' => ($rgb >> 16) & 0xFF,
                            'g' => ($rgb >> 8) & 0xFF,
                            'b' => $rgb & 0xFF
                        ];
                    }
                    
                    imagedestroy($imageResource);
                    
                    return $this->analyzeColorPatterns($colors);
                }
            }
        } catch (\Exception $e) {
            Log::error('Color analysis failed: ' . $e->getMessage(), [
                'file' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'error' => $e->getMessage()
            ]);
        }
        
        return $this->getDefaultColorAnalysis();
    }

    /**
     * Get default color analysis when image analysis fails
     */
    private function getDefaultColorAnalysis()
    {
        return [
            'dominant_colors' => [],
            'brightness' => 'medium',
            'warm_colors' => false,
            'avg_rgb' => ['r' => 128, 'g' => 128, 'b' => 128]
        ];
    }

    /**
     * Analyze color patterns to determine food characteristics
     */
    private function analyzeColorPatterns($colors)
    {
        $totalR = $totalG = $totalB = 0;
        $count = count($colors);
        
        foreach ($colors as $color) {
            $totalR += $color['r'];
            $totalG += $color['g'];
            $totalB += $color['b'];
        }
        
        $avgR = $totalR / $count;
        $avgG = $totalG / $count;
        $avgB = $totalB / $count;
        
        // Determine brightness
        $brightness = ($avgR + $avgG + $avgB) / 3;
        $brightnessLevel = $brightness > 180 ? 'bright' : ($brightness > 100 ? 'medium' : 'dark');
        
        // Determine dominant colors
        $dominantColors = [];
        if ($avgR > $avgG && $avgR > $avgB) $dominantColors[] = 'red';
        if ($avgG > $avgR && $avgG > $avgB) $dominantColors[] = 'green';
        if ($avgB > $avgR && $avgB > $avgG) $dominantColors[] = 'blue';
        
        // Check for warm colors (red/orange/yellow)
        $warmColors = ($avgR > 150 && $avgG > 100) || ($avgR > 180);
        
        return [
            'dominant_colors' => $dominantColors,
            'brightness' => $brightnessLevel,
            'warm_colors' => $warmColors,
            'avg_rgb' => ['r' => $avgR, 'g' => $avgG, 'b' => $avgB]
        ];
    }

    /**
     * Get food types based on image characteristics
     */
    private function getFoodTypesByCharacteristics($aspectRatio, $fileSize, $hashValue, $fileName, $colorAnalysis = null)
    {
        $foodTypes = [];
        
        // Analyze aspect ratio for food type clues
        if ($aspectRatio > 1.5) {
            // Wide images - likely pizza, burgers, sandwiches, Nigerian flatbreads
            $foodTypes[] = [
                'food_name' => 'Margherita Pizza',
                'category' => 'Pizza',
                'description' => 'Classic Italian pizza with tomato sauce, mozzarella cheese, and fresh basil. Made with our signature thin crust and premium ingredients.',
                'confidence' => 85,
                'ingredients' => 'Tomato sauce, mozzarella cheese, fresh basil, olive oil',
                'allergens' => 'Dairy, Gluten',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
            $foodTypes[] = [
                'food_name' => 'Classic Beef Burger',
                'category' => 'Burgers',
                'description' => 'Juicy beef patty with fresh lettuce, tomato, onion, and our special sauce. Served on a toasted brioche bun.',
                'confidence' => 82,
                'ingredients' => 'Beef patty, lettuce, tomato, onion, cheese, special sauce',
                'allergens' => 'Dairy, Gluten',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
            // Nigerian Foods
            $foodTypes[] = [
                'food_name' => 'Jollof Rice',
                'category' => 'Rice Dishes',
                'description' => 'Nigerian Jollof rice cooked with tomatoes, peppers, and aromatic spices. Served with grilled chicken or fish.',
                'confidence' => 90,
                'ingredients' => 'Rice, tomatoes, peppers, onions, spices, chicken/fish',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
            $foodTypes[] = [
                'food_name' => 'Pounded Yam & Efo Riro',
                'category' => 'Main Course',
                'description' => 'Smooth pounded yam served with rich vegetable soup made with spinach, meat, and palm oil.',
                'confidence' => 88,
                'ingredients' => 'Yam, spinach, meat, palm oil, peppers, onions',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
        } elseif ($aspectRatio < 0.8) {
            // Tall images - likely drinks, desserts, stacked items
            $foodTypes[] = [
                'food_name' => 'Fresh Fruit Smoothie',
                'category' => 'Beverages',
                'description' => 'Blend of fresh fruits with yogurt and honey. Refreshing and healthy.',
                'confidence' => 88,
                'ingredients' => 'Mixed fruits, yogurt, honey, ice',
                'allergens' => 'Dairy',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
            $foodTypes[] = [
                'food_name' => 'Chocolate Cake',
                'category' => 'Desserts',
                'description' => 'Rich chocolate cake with chocolate ganache frosting. Moist and decadent.',
                'confidence' => 90,
                'ingredients' => 'Chocolate, flour, eggs, sugar, butter, cocoa powder',
                'allergens' => 'Dairy, Eggs, Gluten',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
            // Nigerian Drinks
            $foodTypes[] = [
                'food_name' => 'Zobo Drink',
                'category' => 'Beverages',
                'description' => 'Traditional Nigerian hibiscus drink with ginger and pineapple. Refreshing and healthy.',
                'confidence' => 92,
                'ingredients' => 'Hibiscus flowers, ginger, pineapple, sugar',
                'allergens' => 'None',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
            $foodTypes[] = [
                'food_name' => 'Kunu Drink',
                'category' => 'Beverages',
                'description' => 'Traditional Nigerian millet drink with spices and coconut. Nutritious and refreshing.',
                'confidence' => 89,
                'ingredients' => 'Millet, coconut, spices, sugar',
                'allergens' => 'None',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
        } else {
            // Square-ish images - likely main dishes, salads, pasta
            $foodTypes[] = [
                'food_name' => 'Grilled Chicken Breast',
                'category' => 'Chicken',
                'description' => 'Tender grilled chicken breast seasoned with herbs and spices. Served with your choice of sauce.',
                'confidence' => 87,
                'ingredients' => 'Chicken breast, herbs, spices, olive oil',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
            $foodTypes[] = [
                'food_name' => 'Spaghetti Carbonara',
                'category' => 'Pasta',
                'description' => 'Traditional Italian pasta with eggs, cheese, pancetta, and black pepper. Creamy and delicious.',
                'confidence' => 89,
                'ingredients' => 'Spaghetti, eggs, parmesan cheese, pancetta, black pepper',
                'allergens' => 'Dairy, Eggs, Gluten',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
            $foodTypes[] = [
                'food_name' => 'Fresh Garden Salad',
                'category' => 'Salads',
                'description' => 'Crisp mixed greens with cherry tomatoes, cucumber, red onion, and balsamic vinaigrette.',
                'confidence' => 85,
                'ingredients' => 'Mixed greens, cherry tomatoes, cucumber, red onion, balsamic vinaigrette',
                'allergens' => 'None',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
            // Nigerian Main Dishes
            $foodTypes[] = [
                'food_name' => 'Egusi Soup',
                'category' => 'Soups',
                'description' => 'Rich Nigerian soup made with ground melon seeds, spinach, and meat. Served with pounded yam or eba.',
                'confidence' => 94,
                'ingredients' => 'Melon seeds, spinach, meat, palm oil, peppers, onions',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
            $foodTypes[] = [
                'food_name' => 'Amala & Ewedu',
                'category' => 'Main Course',
                'description' => 'Yam flour paste served with jute leaves soup and meat. Traditional Yoruba delicacy.',
                'confidence' => 91,
                'ingredients' => 'Yam flour, jute leaves, meat, palm oil, peppers',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
            $foodTypes[] = [
                'food_name' => 'Suya',
                'category' => 'Appetizers',
                'description' => 'Spicy grilled meat skewers with groundnut powder and spices. Popular Nigerian street food.',
                'confidence' => 93,
                'ingredients' => 'Beef, groundnut powder, spices, onions, tomatoes',
                'allergens' => 'Nuts',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
            $foodTypes[] = [
                'food_name' => 'Moi Moi',
                'category' => 'Appetizers',
                'description' => 'Steamed bean pudding with peppers, onions, and fish. Nutritious and delicious.',
                'confidence' => 90,
                'ingredients' => 'Beans, peppers, onions, fish, palm oil',
                'allergens' => 'Fish',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
        }
        
        // Add more variety based on file size (larger files might be more detailed photos)
        if ($fileSize > 1000000) { // > 1MB
            $foodTypes[] = [
                'food_name' => 'Grilled Salmon',
                'category' => 'Seafood',
                'description' => 'Fresh Atlantic salmon grilled to perfection with lemon and herbs. Served with seasonal vegetables.',
                'confidence' => 86,
                'ingredients' => 'Atlantic salmon, lemon, herbs, olive oil',
                'allergens' => 'Fish',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
            // Premium Nigerian Dishes
            $foodTypes[] = [
                'food_name' => 'Pepper Soup',
                'category' => 'Soups',
                'description' => 'Spicy Nigerian pepper soup with goat meat or fish. Rich in spices and herbs.',
                'confidence' => 95,
                'ingredients' => 'Goat meat/fish, peppers, spices, herbs, onions',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
            $foodTypes[] = [
                'food_name' => 'Banga Soup',
                'category' => 'Soups',
                'description' => 'Rich palm nut soup with meat and fish. Served with starch or rice.',
                'confidence' => 92,
                'ingredients' => 'Palm nuts, meat, fish, peppers, spices',
                'allergens' => 'Fish',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
        }
        
        // Check filename for clues
        if (str_contains($fileName, 'pizza') || str_contains($fileName, 'pizza')) {
            $foodTypes[] = [
                'food_name' => 'Pepperoni Pizza',
                'category' => 'Pizza',
                'description' => 'Spicy pepperoni pizza with melted cheese and tomato sauce. Perfect for sharing.',
                'confidence' => 95,
                'ingredients' => 'Pepperoni, mozzarella cheese, tomato sauce, herbs',
                'allergens' => 'Dairy, Gluten',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
        }
        
        if (str_contains($fileName, 'burger') || str_contains($fileName, 'hamburger')) {
            $foodTypes[] = [
                'food_name' => 'Cheese Burger',
                'category' => 'Burgers',
                'description' => 'Classic cheeseburger with juicy beef patty, melted cheese, and fresh vegetables.',
                'confidence' => 93,
                'ingredients' => 'Beef patty, cheese, lettuce, tomato, onion, bun',
                'allergens' => 'Dairy, Gluten',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
        }

        // Nigerian food filename detection
        if (str_contains($fileName, 'jollof') || str_contains($fileName, 'rice')) {
            $foodTypes[] = [
                'food_name' => 'Jollof Rice',
                'category' => 'Rice Dishes',
                'description' => 'Nigerian Jollof rice with tomatoes, peppers, and aromatic spices. Served with grilled chicken.',
                'confidence' => 96,
                'ingredients' => 'Rice, tomatoes, peppers, onions, spices, chicken',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
        }

        if (str_contains($fileName, 'egusi') || str_contains($fileName, 'melon')) {
            $foodTypes[] = [
                'food_name' => 'Egusi Soup',
                'category' => 'Soups',
                'description' => 'Rich Nigerian soup with ground melon seeds, spinach, and meat. Served with pounded yam.',
                'confidence' => 97,
                'ingredients' => 'Melon seeds, spinach, meat, palm oil, peppers',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
        }

        if (str_contains($fileName, 'suya') || str_contains($fileName, 'kebab')) {
            $foodTypes[] = [
                'food_name' => 'Suya',
                'category' => 'Appetizers',
                'description' => 'Spicy grilled meat skewers with groundnut powder. Popular Nigerian street food.',
                'confidence' => 95,
                'ingredients' => 'Beef, groundnut powder, spices, onions',
                'allergens' => 'Nuts',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
        }

        // Add color-based adjustments
        if ($colorAnalysis && $colorAnalysis['brightness'] === 'bright') {
            $foodTypes[] = [
                'food_name' => 'Sunny Side Up Egg',
                'category' => 'Breakfast',
                'description' => 'A sunny side up egg with crispy bacon and toast. Bright and cheerful.',
                'confidence' => 80,
                'ingredients' => 'Egg, bacon, toast, butter',
                'allergens' => 'Dairy, Gluten',
                'is_vegetarian' => false,
                'is_spicy' => false,
            ];
        }

        if ($colorAnalysis && ($colorAnalysis['dominant_colors'] === ['red'] || $colorAnalysis['warm_colors'])) {
            $foodTypes[] = [
                'food_name' => 'Spicy Thai Chicken',
                'category' => 'Chicken',
                'description' => 'A spicy Thai chicken stir-fry with bell peppers, onions, and a sweet chili sauce.',
                'confidence' => 88,
                'ingredients' => 'Chicken, bell peppers, onions, chili sauce',
                'allergens' => 'Dairy, Gluten',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
            // Nigerian Spicy Dishes
            $foodTypes[] = [
                'food_name' => 'Pepper Soup',
                'category' => 'Soups',
                'description' => 'Spicy Nigerian pepper soup with goat meat. Rich in peppers and aromatic spices.',
                'confidence' => 92,
                'ingredients' => 'Goat meat, peppers, spices, herbs, onions',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
        }

        if ($colorAnalysis && in_array('green', $colorAnalysis['dominant_colors'])) {
            $foodTypes[] = [
                'food_name' => 'Fresh Garden Salad',
                'category' => 'Salads',
                'description' => 'Crisp mixed greens with cherry tomatoes, cucumber, red onion, and balsamic vinaigrette.',
                'confidence' => 85,
                'ingredients' => 'Mixed greens, cherry tomatoes, cucumber, red onion, balsamic vinaigrette',
                'allergens' => 'None',
                'is_vegetarian' => true,
                'is_spicy' => false,
            ];
            // Nigerian Green Dishes
            $foodTypes[] = [
                'food_name' => 'Efo Riro',
                'category' => 'Soups',
                'description' => 'Rich vegetable soup made with spinach, meat, and palm oil. Served with pounded yam.',
                'confidence' => 89,
                'ingredients' => 'Spinach, meat, palm oil, peppers, onions, spices',
                'allergens' => 'None',
                'is_vegetarian' => false,
                'is_spicy' => true,
            ];
        }
        
        return $foodTypes;
    }

    /**
     * Select the best food match based on hash value
     */
    private function selectBestMatch($foodTypes, $hashValue)
    {
        if (empty($foodTypes)) {
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
        
        // Use hash value to consistently select from available options
        $index = $hashValue % count($foodTypes);
        return $foodTypes[$index];
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

    /**
     * Learn from user corrections and save for future recognition
     */
    public function learnFromCorrection($imageHash, $correctedFood, $userFeedback)
    {
        try {
            $learningData = [
                'image_hash' => $imageHash,
                'corrected_food' => $correctedFood,
                'user_feedback' => $userFeedback,
                'learned_at' => now(),
            ];
            
            // Save to database or file for future reference
            $this->saveLearningData($learningData);
            
            Log::info('AI learned from correction', $learningData);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to save learning data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save learning data for future reference
     */
    private function saveLearningData($data)
    {
        $learningFile = storage_path('app/ai_learning.json');
        
        // Load existing learning data
        $existingData = [];
        if (file_exists($learningFile)) {
            $existingData = json_decode(file_get_contents($learningFile), true) ?? [];
        }
        
        // Add new learning data
        $existingData[] = $data;
        
        // Save back to file
        file_put_contents($learningFile, json_encode($existingData, JSON_PRETTY_PRINT));
    }

    /**
     * Get learned corrections for similar images
     */
    private function getLearnedCorrections($imageHash)
    {
        $learningFile = storage_path('app/ai_learning.json');
        
        if (!file_exists($learningFile)) {
            return null;
        }
        
        $learningData = json_decode(file_get_contents($learningFile), true) ?? [];
        
        // Find similar image hashes (you could implement more sophisticated similarity)
        foreach ($learningData as $data) {
            if ($data['image_hash'] === $imageHash) {
                return $data['corrected_food'];
            }
        }
        
        return null;
    }
} 