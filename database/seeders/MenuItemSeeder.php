<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MenuItem;
use App\Models\Category;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get category IDs
        $delightfulDishes = Category::where('slug', 'delightful-dishes')->first();
        $protein = Category::where('slug', 'protein')->first();
        $salad = Category::where('slug', 'salad')->first();
        $drinks = Category::where('slug', 'drinks')->first();
        $comboDeals = Category::where('slug', 'combo-deals')->first();

        $menuItems = [
            // Delightful Dishes
            [
                'name' => 'Jollof Rice',
                'description' => 'Perfectly seasoned Nigerian jollof rice',
                'ingredients' => 'Rice, tomatoes, peppers, onions, spices, vegetable oil',
                'allergens' => 'None',
                'price' => 4000,
                'category_id' => $delightfulDishes->id,
            ],
            [
                'name' => 'Special Fried Rice',
                'description' => 'Our signature fried rice with mixed vegetables',
                'ingredients' => 'Rice, carrots, peas, corn, eggs, soy sauce, vegetable oil',
                'allergens' => 'Eggs, Soy',
                'price' => 5000,
                'category_id' => $delightfulDishes->id,
            ],
            [
                'name' => 'Spaghetti Stir Fry',
                'description' => 'Stir-fried spaghetti with fresh vegetables',
                'ingredients' => 'Spaghetti, bell peppers, onions, carrots, soy sauce, vegetable oil',
                'allergens' => 'Wheat, Soy',
                'price' => 5000,
                'category_id' => $delightfulDishes->id,
            ],
            [
                'name' => 'Coconut Rice',
                'description' => 'Aromatic rice cooked in coconut milk',
                'ingredients' => 'Rice, coconut milk, onions, spices, vegetable oil',
                'allergens' => 'None',
                'price' => 5000,
                'category_id' => $delightfulDishes->id,
            ],
            [
                'name' => 'Jambalaya Pasta',
                'description' => 'Fusion pasta with Nigerian flavors',
                'ingredients' => 'Pasta, chicken, shrimp, bell peppers, onions, tomatoes, spices',
                'allergens' => 'Wheat, Shellfish',
                'price' => 7500,
                'category_id' => $delightfulDishes->id,
            ],
            [
                'name' => 'Dodo (Fried Plantain)',
                'description' => 'Sweet fried plantain slices',
                'ingredients' => 'Ripe plantains, vegetable oil, salt',
                'allergens' => 'None',
                'price' => 4500,
                'category_id' => $delightfulDishes->id,
            ],
            [
                'name' => 'Spicy Beef Pasta',
                'description' => 'Pasta with tender beef in spicy sauce',
                'ingredients' => 'Pasta, beef, tomatoes, peppers, onions, spices, vegetable oil',
                'allergens' => 'Wheat',
                'price' => 7000,
                'category_id' => $delightfulDishes->id,
            ],
            [
                'name' => 'Jambalaya Rice',
                'description' => 'Rice dish with mixed proteins and spices',
                'ingredients' => 'Rice, chicken, shrimp, sausage, bell peppers, onions, tomatoes, spices',
                'allergens' => 'Shellfish',
                'price' => 8000,
                'category_id' => $delightfulDishes->id,
            ],
            [
                'name' => 'Chinese Rice',
                'description' => 'Asian-style fried rice',
                'ingredients' => 'Rice, eggs, carrots, peas, soy sauce, vegetable oil',
                'allergens' => 'Eggs, Soy',
                'price' => 6000,
                'category_id' => $delightfulDishes->id,
            ],
            [
                'name' => 'Gizzdodo',
                'description' => 'Gizzard and plantain delicacy',
                'ingredients' => 'Chicken gizzards, plantains, peppers, onions, spices, vegetable oil',
                'allergens' => 'None',
                'price' => 6500,
                'category_id' => $delightfulDishes->id,
            ],

            // Proteins
            [
                'name' => 'Prepared Chicken',
                'description' => 'Well-seasoned grilled chicken',
                'ingredients' => 'Chicken, spices, herbs, vegetable oil',
                'allergens' => 'None',
                'price' => 2500,
                'category_id' => $protein->id,
            ],
            [
                'name' => 'Prepared Beef',
                'description' => 'Tender beef chunks',
                'ingredients' => 'Beef, spices, herbs, vegetable oil',
                'allergens' => 'None',
                'price' => 2000,
                'category_id' => $protein->id,
            ],
            [
                'name' => 'Chicken Kebab',
                'description' => 'Grilled chicken skewers',
                'ingredients' => 'Chicken, spices, herbs, vegetable oil',
                'allergens' => 'None',
                'price' => 2000,
                'category_id' => $protein->id,
            ],
            [
                'name' => 'Beef Kebab',
                'description' => 'Grilled beef skewers',
                'ingredients' => 'Beef, spices, herbs, vegetable oil',
                'allergens' => 'None',
                'price' => 2000,
                'category_id' => $protein->id,
            ],

            // Salads
            [
                'name' => 'Chicken Caesar Salad',
                'description' => 'Fresh Caesar salad with grilled chicken',
                'ingredients' => 'Chicken, lettuce, tomatoes, onions, dressing',
                'allergens' => 'None',
                'price' => 7000,
                'category_id' => $salad->id,
            ],

            // Drinks
            [
                'name' => 'Zobo (Hibiscus Drink)',
                'description' => 'Refreshing hibiscus drink (50CL)',
                'ingredients' => 'Hibiscus, sugar, water',
                'allergens' => 'None',
                'price' => 1000,
                'category_id' => $drinks->id,
            ],
            [
                'name' => 'Kunu Aya (Tiger Nut Milk)',
                'description' => 'Creamy tiger nut milk (50CL)',
                'ingredients' => 'Tiger nuts, water, sugar',
                'allergens' => 'None',
                'price' => 1500,
                'category_id' => $drinks->id,
            ],

            // Combo Deals
            [
                'name' => 'Jollof Rice, Chicken & a Pet Drink',
                'description' => 'Complete meal with jollof rice, chicken, and drink',
                'ingredients' => 'Jollof rice, chicken, drink',
                'allergens' => 'None',
                'price' => 6500,
                'category_id' => $comboDeals->id,
            ],
            [
                'name' => 'Fried Rice, Chicken & a Pet Drink',
                'description' => 'Complete meal with fried rice, chicken, and drink',
                'ingredients' => 'Fried rice, chicken, drink',
                'allergens' => 'None',
                'price' => 7000,
                'category_id' => $comboDeals->id,
            ],
        ];

        foreach ($menuItems as $item) {
            MenuItem::create($item);
        }
    }
}
