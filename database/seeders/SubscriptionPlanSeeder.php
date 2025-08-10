<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Small Restaurant',
                'slug' => 'small',
                'description' => 'Perfect for small restaurants and food vendors starting their digital journey',
                'monthly_price' => 5000.00,
                'menu_item_limit' => 5,
                'custom_domain_enabled' => false,
                'unlimited_menu_items' => false,
                'priority_support' => false,
                'advanced_analytics' => false,
                'video_packages_enabled' => false,
                'social_media_promotion_enabled' => false,
                'features' => [
                    'Basic menu management',
                    'QR code generation',
                    'Order tracking',
                    'Basic analytics',
                    'Email support',
                    'Mobile responsive menu'
                ],
                'limitations' => [
                    'Limited to 5 menu items',
                    'No custom domain',
                    'No video packages',
                    'No social media promotion',
                    'Standard support only'
                ],
                'is_active' => true,
                'sort_order' => 1,
                'color_scheme' => 'blue'
            ],
            [
                'name' => 'Normal Restaurant',
                'slug' => 'normal',
                'description' => 'Ideal for growing restaurants that need more features and flexibility',
                'monthly_price' => 15000.00,
                'menu_item_limit' => 25,
                'custom_domain_enabled' => true,
                'unlimited_menu_items' => false,
                'priority_support' => false,
                'advanced_analytics' => true,
                'video_packages_enabled' => true,
                'social_media_promotion_enabled' => true,
                'features' => [
                    'Up to 25 menu items',
                    'Custom domain support',
                    'Advanced analytics',
                    'Video packages access',
                    'Social media promotion',
                    'Priority email support',
                    'Menu categories',
                    'Promotional features',
                    'Customer reviews',
                    'Order management'
                ],
                'limitations' => [
                    'Limited to 25 menu items',
                    'No unlimited menu items',
                    'Standard support (not priority)'
                ],
                'is_active' => true,
                'sort_order' => 2,
                'color_scheme' => 'purple'
            ],
            [
                'name' => 'Premium Restaurant',
                'slug' => 'premium',
                'description' => 'For established restaurants that want unlimited features and priority support',
                'monthly_price' => 30000.00,
                'menu_item_limit' => 0, // Unlimited
                'custom_domain_enabled' => true,
                'unlimited_menu_items' => true,
                'priority_support' => true,
                'advanced_analytics' => true,
                'video_packages_enabled' => true,
                'social_media_promotion_enabled' => true,
                'features' => [
                    'Unlimited menu items',
                    'Custom domain support',
                    'Advanced analytics & insights',
                    'Video packages access',
                    'Social media promotion',
                    'Priority phone & email support',
                    'Custom branding',
                    'API access',
                    'White-label options',
                    'Dedicated account manager',
                    'Quality of life improvements',
                    'Custom integrations',
                    'Advanced reporting',
                    'Multi-location support'
                ],
                'limitations' => [
                    'No menu item limits',
                    'No feature restrictions'
                ],
                'is_active' => true,
                'sort_order' => 3,
                'color_scheme' => 'orange'
            ]
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
