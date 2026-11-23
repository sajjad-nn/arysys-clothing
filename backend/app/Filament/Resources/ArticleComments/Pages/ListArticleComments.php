<?php

namespace App\Filament\Resources\ArticleComments\Pages;

use App\Filament\Resources\ArticleComments\ArticleCommentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArticleComments extends ListRecords
{
    protected static string $resource = ArticleCommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
