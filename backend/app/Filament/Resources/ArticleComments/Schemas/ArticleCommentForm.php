<?php

namespace App\Filament\Resources\ArticleComments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ArticleCommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('body')
                    ->label('متن نظر')
                    ->rows(6)
                    ->disabled()
                    ->columnSpanFull(),

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
