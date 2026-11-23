<?php

namespace App\Filament\Resources\AboutSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AboutSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات اسلایدر درباره ما')
                    ->description('تصاویر و نام‌های نمایشی چرخ‌وفلک سه‌بعدی را مدیریت کنید.')
                    ->schema([
                        FileUpload::make('image')
                            ->label('تصویر')
                            ->image()
                            ->disk('public')
                            ->directory('about-slides')
                            ->imageEditor()
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('نام نمایشی')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('sort_order')
                                    ->label('ترتیب نمایش')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ]),

                        Toggle::make('is_active')
                            ->label('اسلاید فعال باشد')
                            ->default(true),
                    ])
                    ->columns(1),
            ]);
    }
}
