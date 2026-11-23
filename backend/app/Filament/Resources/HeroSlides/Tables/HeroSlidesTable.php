<?php

namespace App\Filament\Resources\HeroSlides\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HeroSlidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('تصویر')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title')
                    ->label('عنوان اصلی')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subtitle')
                    ->label('زیرعنوان')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('ترتیب نمایش')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('وضعیت')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
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
