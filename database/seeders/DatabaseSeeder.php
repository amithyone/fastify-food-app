<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GlobalCategorySeeder::class, // Global categories should be seeded first
            SampleRestaurantSeeder::class,
            FeaturedRestaurantSeeder::class,
            VideoPackageTemplateSeeder::class,
            SubscriptionPlanSeeder::class,
            ParentCategorySeeder::class,
            AssignParentCategoriesSeeder::class,
        ]);
    }
}
