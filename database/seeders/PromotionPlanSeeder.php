<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PromotionPlan;

class PromotionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Promotion',
                'slug' => 'basic',
                'description' => 'Perfect for new restaurants looking to get noticed',
                'price' => 5000, // ₦50
                'duration_days' => 7,
                'max_impressions' => 1000,
                'features' => [
                    'Featured on homepage',
                    'Basic badge display',
                    'Standard promotion card',
                    '7 days duration'
                ],
                'sort_order' => 1
            ],
            [
                'name' => 'Premium Promotion',
                'slug' => 'premium',
                'description' => 'Great for established restaurants wanting more visibility',
                'price' => 15000, // ₦150
                'duration_days' => 14,
                'max_impressions' => 5000,
                'features' => [
                    'Featured on homepage',
                    'Premium badge display',
                    'Enhanced promotion card',
                    'Custom promotion image',
                    'Priority placement',
                    '14 days duration',
                    'Analytics tracking'
                ],
                'sort_order' => 2
            ],
            [
                'name' => 'Featured Promotion',
                'slug' => 'featured',
                'description' => 'Maximum visibility for top restaurants',
                'price' => 30000, // ₦300
                'duration_days' => 30,
                'max_impressions' => 15000,
                'features' => [
                    'Featured on homepage',
                    'Premium badge display',
                    'Enhanced promotion card',
                    'Custom promotion image',
                    'Top priority placement',
                    '30 days duration',
                    'Analytics tracking',
                    'Click tracking',
                    'Custom call-to-action',
                    'Featured section highlight'
                ],
                'sort_order' => 3
            ],
            [
                'name' => 'Starter Boost',
                'slug' => 'starter',
                'description' => 'Quick boost for new restaurants',
                'price' => 2500, // ₦25
                'duration_days' => 3,
                'max_impressions' => 500,
                'features' => [
                    'Featured on homepage',
                    'Basic badge display',
                    'Standard promotion card',
                    '3 days duration'
                ],
                'sort_order' => 0
            ]
        ];

        foreach ($plans as $plan) {
            PromotionPlan::create($plan);
        }

        $this->command->info('Promotion plans created successfully!');
    }
}
