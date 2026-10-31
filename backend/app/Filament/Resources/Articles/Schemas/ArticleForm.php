<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('اطلاعات اصلی مقاله')
                    ->description('اطلاعات پایه مقاله را وارد کنید.')
                    ->schema([

                        TextInput::make('title')
                            ->label('عنوان مقاله')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('آدرس مقاله (Slug)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('article_category_id')
                            ->label('دسته‌بندی مقاله')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Textarea::make('excerpt')
                            ->label('خلاصه مقاله')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

                Section::make('محتوای مقاله')
                    ->schema([

                        RichEditor::make('content')
                            ->label('متن مقاله')
                            ->required()
                            ->columnSpanFull(),

                    ]),

                Section::make('تصویر شاخص')
                    ->description('تصویر اصلی مقاله را بارگذاری کنید.')
                    ->schema([

                        FileUpload::make('image')
                            ->label('تصویر مقاله')
                            ->image()
                            ->disk('public')
                            ->directory('articles')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->columnSpanFull(),

                    ]),

                Section::make('تنظیمات انتشار')
                    ->schema([

                        Toggle::make('is_published')
                            ->label('انتشار مقاله')
                            ->default(false)
                            ->live(),

                        DateTimePicker::make('published_at')
                            ->label('تاریخ انتشار')
                            ->seconds(false)
                            ->default(now()),

                    ])
                    ->columns(2),

            ]);
    }
}
