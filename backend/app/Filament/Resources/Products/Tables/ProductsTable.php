<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('تصویر')
                    ->disk('public'),

                TextColumn::make('name')
                    ->label('نام محصول')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('قیمت')
                    ->numeric()
                    ->sortable()
                    ->suffix(' تومان'),

                TextColumn::make('stock')
                    ->label('موجودی')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('دسته‌بندی'),

                ToggleColumn::make('is_active')
                    ->label('فعال'),

            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make()
                    ->label('ویرایش'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف انتخاب‌شده‌ها'),
                ]),
            ]);
    }
}
