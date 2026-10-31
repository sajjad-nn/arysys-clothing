<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // اطلاعات عمومی محصول
                TextInput::make('name')
                    ->label('نام محصول')
                    ->required()
                    ->maxLength(255),

                Select::make('product_type')
                    ->label('نوع محصول')
                    ->options([
                        'simple' => 'محصول ساده',
                        'variant' => 'محصول تنوع‌دار',
                    ])
                    ->default('simple')
                    ->required()
                    ->live(),

                Select::make('category_id')
                    ->label('دسته‌بندی')
                    ->options(
                        Category::query()
                            ->where('is_active', true)
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->required(),

                Textarea::make('description')
                    ->label('توضیحات محصول')
                    ->rows(4)
                    ->columnSpanFull(),

                // اطلاعات محصول ساده
                TextInput::make('price')
                    ->label('قیمت اصلی (تومان)')
                    ->numeric()
                    ->minValue(0)
                    ->required(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    )
                    ->visible(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    )
                    ->dehydrated(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    ),

                TextInput::make('discount_price')
                    ->label('قیمت تخفیف‌خورده (تومان) - اختیاری')
                    ->numeric()
                    ->nullable()
                    ->minValue(0)
                    ->visible(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    )
                    ->dehydrated(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    ),

                TextInput::make('stock')
                    ->label('موجودی انبار')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    )
                    ->visible(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    )
                    ->dehydrated(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    ),

                FileUpload::make('image')
                    ->label('تصویر اصلی محصول')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->visible(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    )
                    ->dehydrated(
                        fn ($get) =>
                        $get('product_type') === 'simple'
                    ),

                // تنوع‌های محصول
                Repeater::make('variants')
                    ->label('تنوع‌های محصول')
                    ->relationship()
                    ->visible(
                        fn ($get) =>
                        $get('product_type') === 'variant'
                    )
                    ->schema([

                        TextInput::make('color')
                            ->label('رنگ')
                            ->placeholder('مثلاً مشکی')
                            ->maxLength(100),

                        TextInput::make('size')
                            ->label('سایز')
                            ->placeholder('مثلاً L')
                            ->maxLength(50),

                        TextInput::make('sku')
                            ->label('کد محصول')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('price')
                            ->label('قیمت اصلی (تومان)')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        TextInput::make('discount_price')
                            ->label('قیمت تخفیف‌خورده (تومان) - اختیاری')
                            ->numeric()
                            ->nullable()
                            ->minValue(0),

                        TextInput::make('stock')
                            ->label('موجودی این تنوع')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0),

                        FileUpload::make('image')
                            ->label('تصویر این تنوع')
                            ->image()
                            ->disk('public')
                            ->directory('products/variants')
                            ->visibility('public'),

                        Toggle::make('is_active')
                            ->label('فعال بودن این تنوع')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->itemLabel(
                        fn (array $state): ?string =>
                        trim(
                            ($state['color'] ?? '') .
                            ' - ' .
                            ($state['size'] ?? ''),
                            ' -'
                        ) ?: 'تنوع جدید'
                    )
                    ->addActionLabel('افزودن رنگ و سایز')
                    ->columnSpanFull(),

                // وضعیت کلی محصول
                Toggle::make('is_active')
                    ->label('فعال بودن محصول')
                    ->default(true),

            ]);
    }
}
