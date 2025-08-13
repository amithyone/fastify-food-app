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
            SampleRestaurantSeeder::class,
            FeaturedRestaurantSeeder::class,
            VideoPackageTemplateSeeder::class,
            SubscriptionPlanSeeder::class,
            ParentCategorySeeder::class,
            AssignParentCategoriesSeeder::class,
        ]);
    }
}
