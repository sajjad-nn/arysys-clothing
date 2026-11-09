<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\CategoriesChart;
use App\Filament\Widgets\ProductsChart;
use App\Filament\Widgets\SiteStats;
use Filament\Actions\Action;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $siteUrl = env('FRONTEND_URL', 'http://localhost:5173');

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            // هویت پنل
            ->brandName('ARYSYS')
            ->font('Vazirmatn')
            ->defaultThemeMode(ThemeMode::Dark)

            // رنگ‌ها
            ->colors([
                'primary' => [
                    50  => '#fbf7ee',
                    100 => '#f5ecd6',
                    200 => '#ecd9ad',
                    300 => '#e0c48c',
                    400 => '#d4b57a',
                    500 => '#c7a66a',
                    600 => '#a98a52',
                    700 => '#8b7657',
                    800 => '#6e5d45',
                    900 => '#574a38',
                    950 => '#2f281e',
                ],
                'gray' => [
                    50  => '#f7f5f2',
                    100 => '#ebe8e3',
                    200 => '#d6d1c9',
                    300 => '#b8b1a6',
                    400 => '#948c80',
                    500 => '#756e63',
                    600 => '#5a544b',
                    700 => '#423d36',
                    800 => '#2b2723',
                    900 => '#1c1916',
                    950 => '#12100e',
                ],
            ])

            // ظاهر مدرن
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            ->spa()
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->favicon(asset('favicon.svg'))

            // گروه‌های منو (ترتیب نوشتن = ترتیب نمایش)
            // اسم هر گروه باید دقیقاً با navigationGroup در Resourceها یکی باشد
            ->navigationGroups([
                NavigationGroup::make('فروشگاه'),
                NavigationGroup::make('محتوا'),
                NavigationGroup::make('استوری و بنر'),
                NavigationGroup::make('تنظیمات')->collapsed(),
            ])

            // دکمه مشاهده سایت در نوار بالا
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn () => Blade::render(
                    '<x-filament::button
                        tag="a"
                        href="{{ $url }}"
                        target="_blank"
                        rel="noopener"
                        color="primary"
                        size="sm"
                        icon="heroicon-o-globe-alt"
                    >
                        مشاهده سایت
                    </x-filament::button>',
                    ['url' => $siteUrl]
                ),
            )

            // لینک مشاهده سایت در منوی کاربر
            ->userMenuItems([
                Action::make('view-site')
                    ->label('مشاهده سایت')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->url($siteUrl)
                    ->openUrlInNewTab(),
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                SiteStats::class,
                ProductsChart::class,
                CategoriesChart::class,
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
