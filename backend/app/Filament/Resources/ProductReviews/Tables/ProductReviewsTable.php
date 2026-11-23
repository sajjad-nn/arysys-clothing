<?php

namespace App\Filament\Resources\ProductReviews\Tables;

use App\Models\ProductReview;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('نام کاربر')
                    ->searchable(),

                TextColumn::make('product.name')
                    ->label('محصول')
                    ->searchable(),

                TextColumn::make('body')
                    ->label('متن نظر')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('امتیاز')
                    ->sortable()
                    ->suffix(' از ۵'),

                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'در انتظار تأیید',
                        'approved' => 'تأیید شده',
                        'rejected' => 'رد شده',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('تاریخ ثبت')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت نظر')
                    ->options([
                        'pending' => 'در انتظار تأیید',
                        'approved' => 'تأیید شده',
                        'rejected' => 'رد شده',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('تأیید')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ProductReview $record) => $record->status !== 'approved')
                    ->action(fn (ProductReview $record) => $record->update([
                        'status' => 'approved',
                    ])),

                Action::make('reject')
                    ->label('رد نظر')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ProductReview $record) => $record->status !== 'rejected')
                    ->action(fn (ProductReview $record) => $record->update([
                        'status' => 'rejected',
                    ])),

                EditAction::make()
                    ->label('ویرایش'),

                DeleteAction::make()
                    ->label('حذف'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف انتخاب‌شده‌ها'),
                ]),
            ]);
    }
}
