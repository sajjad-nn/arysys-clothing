<?php

namespace App\Filament\Resources\Articles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ArticlesTable
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
                    ->label('عنوان مقاله')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('category.name')
                    ->label('دسته‌بندی')
                    ->placeholder('بدون دسته‌بندی')
                    ->sortable(),

                TextColumn::make('is_published')
                    ->label('وضعیت انتشار')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string =>
                        $state ? 'منتشرشده' : 'پیش‌نویس'
                    )
                    ->color(fn (bool $state): string =>
                        $state ? 'success' : 'gray'
                    ),

                TextColumn::make('published_at')
                    ->label('تاریخ انتشار')
                    ->dateTime('Y/m/d H:i')
                    // ->jalaliDate()
                    ->sortable()
                    ->placeholder('تعیین نشده'),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([

                SelectFilter::make('article_category_id')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name'),

                TernaryFilter::make('is_published')
                    ->label('وضعیت انتشار')
                    ->placeholder('همه مقالات')
                    ->trueLabel('منتشرشده')
                    ->falseLabel('پیش‌نویس'),

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
