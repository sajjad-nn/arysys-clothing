<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'پیراهن مردانه مشکی',
            'تیشرت سفید ساده',
            'شلوار جین آبی',
            'هودی طوسی',
            'کت مردانه مشکی',
            'سویشرت اسپرت',
        ]);

        return [
            'name' => $name,

            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),

            'short_description' => 'محصول با کیفیت و طراحی مدرن',

            'description' => 'این محصول با استفاده از مواد با کیفیت و طراحی مناسب تولید شده است.',

            'price' => fake()->numberBetween(500000, 5000000),

            'discount_price' => fake()->boolean(60)
                ? fake()->numberBetween(300000, 4000000)
                : null,

            'stock' => fake()->numberBetween(0, 50),

            'image' => null,

            'is_active' => true,
        ];
    }
}
