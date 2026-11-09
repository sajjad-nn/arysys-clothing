<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class CategoriesChart extends ChartWidget
{
    protected ?string $heading = 'محصولات هر دسته‌بندی';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $categories = Category::withCount([
            'products' => fn($query) => $query->where('is_active', true),
        ])->get();

        return [
            'datasets' => [
                [
                    'data' => $categories->pluck('products_count')->all(),
                    'backgroundColor' => [
                        '#c7a66a',
                        '#e0c48c',
                        '#8b7657',
                        '#a98a52',
                        '#ecd9ad',
                        '#6e5d45',
                        '#d4b57a',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $categories->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }
}
