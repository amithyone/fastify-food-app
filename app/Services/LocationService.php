<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Restaurant;

class LocationService
{
    /**
     * Get user location from IP address
     */
    public function getUserLocation(Request $request): array
    {
        $ip = $request->ip();
        
        // Try to get from cache first
        $cacheKey = "user_location_{$ip}";
        $location = Cache::get($cacheKey);
        
        if ($location) {
            return $location;
        }
        
        // Default location (Abuja, Nigeria)
        $defaultLocation = [
            'city' => 'Abuja',
            'state' => 'FCT',
            'country' => 'Nigeria',
            'latitude' => 9.0820,
            'longitude' => 8.6753
        ];
        
        try {
            // Use ipapi.co for geolocation (free tier available)
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success') {
                    $location = [
                        'city' => $data['city'] ?? 'Abuja',
                        'state' => $data['regionName'] ?? 'FCT',
                        'country' => $data['country'] ?? 'Nigeria',
                        'latitude' => $data['lat'] ?? 9.0820,
                        'longitude' => $data['lon'] ?? 8.6753
                    ];
                    
                    // Cache for 1 hour
                    Cache::put($cacheKey, $location, 3600);
                    
                    return $location;
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail
            \Log::warning("Failed to get location from IP: {$ip}", ['error' => $e->getMessage()]);
        }
        
        return $defaultLocation;
    }
    
    /**
     * Get restaurants by location
     */
    public function getRestaurantsByLocation(string $city, string $state = null, string $country = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Restaurant::where('is_active', true);
        
        // Filter by city (case-insensitive)
        $query->whereRaw('LOWER(city) = ?', [strtolower($city)]);
        
        // Filter by state if provided
        if ($state) {
            $query->whereRaw('LOWER(state) = ?', [strtolower($state)]);
        }
        
        // Filter by country if provided
        if ($country) {
            $query->whereRaw('LOWER(country) = ?', [strtolower($country)]);
        }
        
        return $query->with(['categories.menuItems' => function($query) {
            $query->where('is_available', true);
        }])->get();
    }
    
    /**
     * Get nearby restaurants (within radius)
     */
    public function getNearbyRestaurants(float $latitude, float $longitude, float $radiusKm = 10): \Illuminate\Database\Eloquent\Collection
    {
        // This is a simplified version - in production you'd use proper geospatial queries
        // For now, we'll return restaurants in the same city
        return Restaurant::where('is_active', true)
            ->whereNotNull('city')
            ->get()
            ->filter(function($restaurant) use ($latitude, $longitude, $radiusKm) {
                // Calculate distance (simplified)
                $distance = $this->calculateDistance(
                    $latitude, $longitude,
                    $restaurant->latitude ?? 0, $restaurant->longitude ?? 0
                );
                
                return $distance <= $radiusKm;
            });
    }
    
    /**
     * Calculate distance between two points (Haversine formula)
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Get popular cities with restaurants
     */
    public function getPopularCities(): array
    {
        return Restaurant::where('is_active', true)
            ->selectRaw('city, state, country, COUNT(*) as restaurant_count')
            ->groupBy('city', 'state', 'country')
            ->having('restaurant_count', '>', 0)
            ->orderBy('restaurant_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'city' => $item->city,
                    'state' => $item->state,
                    'country' => $item->country,
                    'restaurant_count' => $item->restaurant_count
                ];
            })
            ->toArray();
    }
} 