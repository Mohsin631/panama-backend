<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Apparel',
            'Furniture',
            'Janitorial Supplies',
            'Home Variety',
            'Appliances',
            'Hardware & Tools',
            'Jewellery and Accessories',
            'Cosmetics and Fragrances',
            'Electronics',
            'Food and Beverage',
            'Auto Parts',
            'Auto Accessories',
            'Automotive',
            'Construction',
            'Office',
            'Outdoor',
            'Baby',
        ];

        foreach ($names as $name) {
            Category::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
