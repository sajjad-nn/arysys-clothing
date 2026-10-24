<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name' => 'لباس زنانه',
            'slug' => 'women-clothing',
            'description' => 'انواع لباس زنانه',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'لباس مردانه',
            'slug' => 'men-clothing',
            'description' => 'انواع لباس مردانه',
            'is_active' => true,
        ]);
    }
}
