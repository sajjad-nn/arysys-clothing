<?php

namespace App\Filament\Resources\ArticleCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ArticleCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('نام دسته‌بندی')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('آدرس دسته‌بندی')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('articles_count')
                    ->label('تعداد مقالات')
                    ->counts('articles')
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string =>
                        $state ? 'فعال' : 'غیرفعال'
                    )
                    ->color(fn (bool $state): string =>
                        $state ? 'success' : 'gray'
                    ),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([

                TernaryFilter::make('is_active')
                    ->label('وضعیت دسته‌بندی')
                    ->placeholder('همه دسته‌بندی‌ها')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),

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
