<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use App\Models\Story;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'نمای کلی سایت';

    protected function getStats(): array
    {
        return [
            Stat::make('محصولات فعال', Product::where('is_active', true)->count())
                ->description('کل محصولات: ' . Product::count())
                ->icon('heroicon-o-shopping-bag')
                ->color('primary'),

            Stat::make(
                'محصولات تخفیف‌دار',
                Product::whereNotNull('discount_price')
                    ->whereColumn('discount_price', '<', 'price')
                    ->count()
            )
                ->description('در فروش ویژه')
                ->icon('heroicon-o-receipt-percent')
                ->color('danger'),

            Stat::make('دسته‌بندی‌ها', Category::count())
                ->description('فعال: ' . Category::where('is_active', true)->count())
                ->icon('heroicon-o-squares-2x2')
                ->color('info'),

            Stat::make('استوری‌های فعال', Story::where('is_active', true)->count())
                ->description('کل استوری‌ها: ' . Story::count())
                ->icon('heroicon-o-play-circle')
                ->color('success'),
        ];
    }
}
