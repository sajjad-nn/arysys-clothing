<?php

namespace App\Filament\Resources\ArticleComments;

use App\Filament\Resources\ArticleComments\Pages\CreateArticleComment;
use App\Filament\Resources\ArticleComments\Pages\EditArticleComment;
use App\Filament\Resources\ArticleComments\Pages\ListArticleComments;
use App\Filament\Resources\ArticleComments\Schemas\ArticleCommentForm;
use App\Filament\Resources\ArticleComments\Tables\ArticleCommentsTable;
use App\Models\ArticleComment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ArticleCommentResource extends Resource
{
    protected static ?string $model = ArticleComment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'body';

    protected static string|UnitEnum|null $navigationGroup = 'محتوا';

    protected static ?string $navigationLabel = 'نظرات مقالات';
    protected static ?int $navigationSort = 2;


    public static function form(Schema $schema): Schema
    {
        return ArticleCommentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArticleCommentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticleComments::route('/'),
            'create' => CreateArticleComment::route('/create'),
            'edit' => EditArticleComment::route('/{record}/edit'),
        ];
    }
}
