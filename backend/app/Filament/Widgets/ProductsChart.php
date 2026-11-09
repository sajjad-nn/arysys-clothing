<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ProductsChart extends ChartWidget
{
    protected ?string $heading = 'محصولات ثبت‌شده در ۱۲ ماه اخیر';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $labels = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $labels[] = $month->translatedFormat('F');
            $counts[] = Product::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'محصولات جدید',
                    'data' => $counts,
                    'fill' => true,
                    'tension' => 0.4,
                    'borderColor' => '#c7a66a',
                    'backgroundColor' => 'rgba(199, 166, 106, 0.18)',
                    'pointBackgroundColor' => '#c7a66a',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
        ];
    }
}
