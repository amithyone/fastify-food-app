<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Restaurant;
use App\Models\RestaurantDeliverySetting;
use App\Models\MenuItem;

class PopulateDeliverySettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delivery:populate {--force : Force update even if settings exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate delivery settings for all existing restaurants and menu items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting delivery settings population...');
        
        // Populate restaurant delivery settings
        $query = Restaurant::query();
        
        if (!$this->option('force')) {
            $query->whereDoesntHave('deliverySetting');
        }
        
        $restaurants = $query->get();
        $this->info("📋 Found {$restaurants->count()} restaurants to process");
        
        $bar = $this->output->createProgressBar($restaurants->count());
        $bar->start();
        
        foreach ($restaurants as $restaurant) {
            // Check if delivery setting already exists
            $existingSetting = $restaurant->deliverySetting;
            
            if (!$existingSetting) {
                // Create new delivery settings
                RestaurantDeliverySetting::create([
                    'restaurant_id' => $restaurant->id,
                    'delivery_mode' => 'flexible',
                    'delivery_enabled' => true,
                    'pickup_enabled' => true,
                    'in_restaurant_enabled' => true,
                    'delivery_fee' => 500,
                    'delivery_time_minutes' => 30,
                    'pickup_time_minutes' => 20,
                    'minimum_delivery_amount' => 0,
                    'delivery_notes' => 'Free delivery for orders above ₦2000',
                    'pickup_notes' => 'Please have your pickup code ready'
                ]);
            } elseif ($this->option('force')) {
                // Update existing settings
                $existingSetting->update([
                    'delivery_enabled' => true,
                    'pickup_enabled' => true,
                    'in_restaurant_enabled' => true,
                ]);
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        // Populate menu item delivery settings
        $this->info("\n🍽️  Processing menu items...");
        
        $menuItemsQuery = MenuItem::query();
        
        if (!$this->option('force')) {
            $menuItemsQuery->where(function($q) {
                $q->whereNull('is_available_for_delivery')
                  ->orWhereNull('is_available_for_pickup')
                  ->orWhereNull('is_available_for_restaurant');
            });
        }
        
        $menuItems = $menuItemsQuery->get();
        $this->info("🍽️  Found {$menuItems->count()} menu items to process");
        
        $menuBar = $this->output->createProgressBar($menuItems->count());
        $menuBar->start();
        
        foreach ($menuItems as $menuItem) {
            $menuItem->update([
                'is_available_for_delivery' => $menuItem->is_available_for_delivery ?? true,
                'is_available_for_pickup' => $menuItem->is_available_for_pickup ?? true,
                'is_available_for_restaurant' => $menuItem->is_available_for_restaurant ?? true,
            ]);
            
            $menuBar->advance();
        }
        
        $menuBar->finish();
        $this->newLine();
        
        // Summary
        $totalRestaurants = Restaurant::count();
        $restaurantsWithSettings = Restaurant::whereHas('deliverySetting')->count();
        $totalMenuItems = MenuItem::count();
        $menuItemsWithSettings = MenuItem::whereNotNull('is_available_for_delivery')
            ->whereNotNull('is_available_for_pickup')
            ->whereNotNull('is_available_for_restaurant')
            ->count();
        
        $this->info("\n📊 Delivery Settings Population Summary:");
        $this->info("   Restaurants: {$restaurantsWithSettings}/{$totalRestaurants} have delivery settings");
        $this->info("   Menu Items: {$menuItemsWithSettings}/{$totalMenuItems} have delivery settings");
        
        if ($restaurantsWithSettings === $totalRestaurants && $menuItemsWithSettings === $totalMenuItems) {
            $this->info("\n✅ All restaurants and menu items now have delivery settings!");
        } else {
            $this->warn("\n⚠️  Some items may still need manual configuration");
        }
        
        $this->info("\n✨ Delivery settings population completed successfully!");
        
        return 0;
    }
}
