<?php

namespace App\Filament\Resources\ArticleComments\Pages;

use App\Filament\Resources\ArticleComments\ArticleCommentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticleComment extends CreateRecord
{
    protected static string $resource = ArticleCommentResource::class;
}
