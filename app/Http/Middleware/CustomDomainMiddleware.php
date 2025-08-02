<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use Symfony\Component\HttpFoundation\Response;

class CustomDomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Skip if it's the main domain
        if ($host === config('app.domain') || $host === 'localhost' || $host === '127.0.0.1') {
            return $next($request);
        }

        // Find restaurant by custom domain
        $restaurant = Restaurant::where('custom_domain', $host)
            ->where('custom_domain_verified', true)
            ->where('is_active', true)
            ->first();

        if (!$restaurant) {
            abort(404, 'Restaurant not found or domain not verified.');
        }

        // Add restaurant to request for use in controllers
        $request->attributes->set('restaurant', $restaurant);
        
        return $next($request);
    }
}
