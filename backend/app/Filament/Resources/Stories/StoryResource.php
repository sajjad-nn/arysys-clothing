<?php

namespace App\Filament\Resources\Stories;

use App\Filament\Resources\Stories\Pages\ManageStories;
use App\Models\Story;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class StoryResource extends Resource
{
    protected static ?string $model = Story::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlayCircle;

    protected static string|UnitEnum|null $navigationGroup = 'استوری و بنر';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'استوری';

    protected static ?string $pluralModelLabel = 'استوری‌ها';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('عنوان')
                ->required()
                ->maxLength(40),

            FileUpload::make('cover_image')
                ->label('تصویر دایره (کاور)')
                ->image()
                ->imageEditor()
                ->disk('public')
                ->visibility('public')
                ->directory('stories/covers')
                ->required(),

            FileUpload::make('media_path')
                ->label('عکس یا ویدیو استوری')
                ->acceptedFileTypes([
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'video/mp4',
                    'video/webm',
                ])
                ->maxSize(51200)
                ->disk('public')
                ->visibility('public')
                ->directory('stories/media')
                ->required(),

            TextInput::make('button_text')
                ->label('متن دکمه (اختیاری)')
                ->maxLength(30),

            TextInput::make('button_url')
                ->label('لینک دکمه (اختیاری)')
                ->helperText('داخلی: /shop?category=shoes   یا کامل: https://...')
                ->maxLength(255),

            Toggle::make('is_active')
                ->label('فعال')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('کاور')
                    ->disk('public')
                    ->circular(),

                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),

                TextColumn::make('media_type')
                    ->label('نوع')
                    ->badge(),

                ToggleColumn::make('is_active')
                    ->label('فعال'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStories::route('/'),
        ];
    }
}
