<?php

namespace App\Filament\Resources\ProductReviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('body')
                    ->label('متن نظر')
                    ->rows(5)
                    ->disabled()
                    ->columnSpanFull(),

                TextInput::make('rating')
                    ->label('امتیاز')
                    ->numeric()
                    ->disabled(),

                Select::make('status')
                    ->label('وضعیت نظر')
                    ->options([
                        'pending' => 'در انتظار تأیید',
                        'approved' => 'تأیید شده',
                        'rejected' => 'رد شده',
                    ])
                    ->required(),
            ]);
    }
}
