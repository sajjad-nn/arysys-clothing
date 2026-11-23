<?php

namespace App\Filament\Resources\ArticleComments\Tables;

use App\Models\ArticleComment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticleCommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('نام کاربر')
                    ->searchable(),

                TextColumn::make('article.title')
                    ->label('عنوان مقاله')
                    ->searchable(),

                TextColumn::make('body')
                    ->label('متن نظر')
                    ->limit(60)
                    ->searchable(),

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
                    ->visible(fn (ArticleComment $record) => $record->status !== 'approved')
                    ->action(fn (ArticleComment $record) => $record->update([
                        'status' => 'approved',
                    ])),

                Action::make('reject')
                    ->label('رد نظر')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (ArticleComment $record) => $record->status !== 'rejected')
                    ->action(fn (ArticleComment $record) => $record->update([
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
