<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات اسلایدر صفحه اصلی')
                    ->description('محتوای نمایشی اسلاید بالای صفحه اصلی را مدیریت کنید.')
                    ->schema([
                        FileUpload::make('image')
                            ->label('تصویر اسلاید')
                            ->image()
                            ->disk('public')
                            ->directory('hero-slides')
                            ->imageEditor()
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('subtitle')
                                    ->label('زیرعنوان')
                                    ->maxLength(255),

                                TextInput::make('title')
                                    ->label('عنوان اصلی')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('highlighted_title')
                                    ->label('عنوان برجسته')
                                    ->maxLength(255),

                                TextInput::make('sort_order')
                                    ->label('ترتیب نمایش')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ]),

                        Textarea::make('description')
                            ->label('توضیحات')
                            ->rows(4)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('اسلاید فعال باشد')
                            ->default(true),
                    ])
                    ->columns(1),
            ]);
    }
}
