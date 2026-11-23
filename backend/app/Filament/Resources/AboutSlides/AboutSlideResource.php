<?php

namespace App\Filament\Resources\AboutSlides;

use App\Filament\Resources\AboutSlides\Pages\CreateAboutSlide;
use App\Filament\Resources\AboutSlides\Pages\EditAboutSlide;
use App\Filament\Resources\AboutSlides\Pages\ListAboutSlides;
use App\Filament\Resources\AboutSlides\Schemas\AboutSlideForm;
use App\Filament\Resources\AboutSlides\Tables\AboutSlidesTable;
use App\Models\AboutSlide;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AboutSlideResource extends Resource
{
    protected static ?string $model = AboutSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'اسلایدر درباره ما';

    protected static ?string $modelLabel = 'اسلاید';

    protected static ?string $pluralModelLabel = 'اسلایدر درباره ما';

    protected static string|UnitEnum|null $navigationGroup = 'اسلایدر';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return AboutSlideForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AboutSlidesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAboutSlides::route('/'),
            'create' => CreateAboutSlide::route('/create'),
            'edit' => EditAboutSlide::route('/{record}/edit'),
        ];
    }
}
