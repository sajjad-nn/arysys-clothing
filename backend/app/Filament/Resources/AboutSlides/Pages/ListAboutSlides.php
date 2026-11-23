<?php

namespace App\Filament\Resources\AboutSlides\Pages;

use App\Filament\Resources\AboutSlides\AboutSlideResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAboutSlides extends ListRecords
{
    protected static string $resource = AboutSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
