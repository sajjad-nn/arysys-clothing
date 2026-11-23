<?php

namespace App\Filament\Resources\AboutSlides\Pages;

use App\Filament\Resources\AboutSlides\AboutSlideResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAboutSlide extends EditRecord
{
    protected static string $resource = AboutSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
