<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام محصول')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('توضیحات محصول')
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('قیمت (تومان)')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('stock')
                    ->label('موجودی انبار')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0),

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

                FileUpload::make('image')
                    ->label('تصویر محصول')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public'),


                Repeater::make('variants')
                    ->label('تنوع‌های محصول')
                    ->relationship()
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
                            ->label('قیمت (تومان)')
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        TextInput::make('discount_price')
                            ->label('قیمت تخفیف‌خورده (تومان)')
                            ->numeric()
                            ->nullable()
                            ->minValue(0),

                        TextInput::make('stock')
                            ->label('موجودی')
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
                        fn(array $state): ?string =>
                        trim(($state['color'] ?? '') . ' - ' . ($state['size'] ?? '')) ?: 'تنوع جدید'
                    )
                    ->addActionLabel('افزودن رنگ و سایز')
                    ->columnSpanFull(),


                Toggle::make('is_active')
                    ->label('فعال بودن محصول')
                    ->default(true),
            ]);
    }
}
