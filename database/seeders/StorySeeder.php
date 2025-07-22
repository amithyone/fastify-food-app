<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Story;

class StorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stories = [
            [
                'type' => 'special',
                'title' => "🍕 Today's Special",
                'content' => "Fresh from our oven with premium pepperoni",
                'emoji' => '🍕',
                'subtitle' => 'Pepperoni Pizza',
                'description' => 'Fresh from our oven with premium pepperoni',
                'price' => 2500,
                'original_price' => 3000,
                'show_button' => true,
                'button_text' => 'Add to Cart',
                'button_action' => 'addSpecialToCart',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'type' => 'new',
                'title' => "🆕 New Arrivals",
                'content' => "Fresh New Dishes",
                'emoji' => '🆕',
                'subtitle' => 'Fresh New Dishes',
                'description' => 'Discover our latest menu additions',
                'show_button' => true,
                'button_text' => 'Explore Menu',
                'button_action' => 'closeStory',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'type' => 'chef',
                'title' => "👨‍🍳 Chef's Pick",
                'content' => "Our head chef's favorite dish of the week",
                'emoji' => '👨‍🍳',
                'subtitle' => "Chef's Recommendation",
                'description' => 'Our head chef\'s favorite dish of the week',
                'price' => 3500,
                'show_button' => true,
                'button_text' => 'Order Now',
                'button_action' => 'addChefPickToCart',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'type' => 'discount',
                'title' => "💰 Special Offers",
                'content' => "Limited Time Offers",
                'emoji' => '💰',
                'subtitle' => 'Limited Time Offers',
                'description' => 'Exclusive deals and discounts',
                'show_button' => true,
                'button_text' => 'Shop Now',
                'button_action' => 'closeStory',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'type' => 'rewards',
                'title' => "🎁 Reward System",
                'content' => "Pay with Bank Transfer & Get Points",
                'emoji' => '🎁',
                'subtitle' => 'Earn Rewards!',
                'description' => 'Pay with Bank Transfer & Get Points',
                'show_button' => true,
                'button_text' => 'Start Earning',
                'button_action' => 'closeStory',
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'type' => 'kitchen',
                'title' => "📹 Kitchen Live",
                'content' => "Watch our chefs in action",
                'emoji' => '📹',
                'subtitle' => 'Kitchen Live',
                'description' => 'Watch our chefs in action',
                'show_button' => true,
                'button_text' => 'Watch Now',
                'button_action' => 'openKitchenLive',
                'is_active' => true,
                'sort_order' => 6
            ]
        ];

        foreach ($stories as $story) {
            Story::create($story);
        }
    }
}
