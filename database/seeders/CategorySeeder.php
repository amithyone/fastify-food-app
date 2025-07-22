<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Fadded VIP 🔆 Delightful Dishes', 'slug' => 'delightful-dishes'],
            ['name' => 'Fadded VIP 🔆 Protein', 'slug' => 'protein'],
            ['name' => 'Fadded VIP 🔆 Salad', 'slug' => 'salad'],
            ['name' => 'Fadded VIP 🔆 Drinks', 'slug' => 'drinks'],
            ['name' => 'Fadded VIP 🔆 Combo Deals', 'slug' => 'combo-deals'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
