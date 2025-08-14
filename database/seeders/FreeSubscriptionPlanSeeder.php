<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class FreeSubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Free Plan
        SubscriptionPlan::updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free Restaurant',
                'slug' => 'free',
                'description' => 'Perfect for new restaurants getting started. Limited features to help you test the platform.',
                'monthly_price' => 0.00,
                'menu_item_limit' => 10,
                'custom_domain_enabled' => false,
                'unlimited_menu_items' => false,
                'priority_support' => false,
                'advanced_analytics' => false,
                'video_packages_enabled' => false,
                'social_media_promotion_enabled' => false,
                'features' => [
                    'basic_menu_management',
                    'global_categories_only',
                    'basic_orders',
                    'single_qr_code',
                    'basic_analytics'
                ],
                'limitations' => [
                    'no_custom_categories',
                    'no_stories',
                    'no_custom_domain',
                    'no_ai_menu_generation',
                    'limited_qr_codes',
                    'no_priority_support',
                    'no_advanced_analytics',
                    'no_video_packages',
                    'no_social_media_promotion'
                ],
                'is_active' => true,
                'sort_order' => 0,
                'color_scheme' => 'gray'
            ]
        );

        $this->command->info('Free subscription plan created successfully!');
    }
}
