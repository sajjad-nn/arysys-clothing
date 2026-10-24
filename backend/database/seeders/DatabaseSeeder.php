<?php

namespace Database\Seeders;
<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $womenCategory = Category::where('slug', 'women-clothing')->first();
        $menCategory = Category::where('slug', 'men-clothing')->first();

        Product::factory()
            ->count(5)
            ->create([
                'category_id' => $womenCategory->id,
            ]);

        Product::factory()
            ->count(5)
            ->create([
                'category_id' => $menCategory->id,
            ]);
    }
}
