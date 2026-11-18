<?php

namespace App\Filament\Resources\Technologies\Pages;

use App\Filament\Resources\Technologies\TechnologyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTechnology extends CreateRecord
{
    protected static string $resource = TechnologyResource::class;

    public function getTitle(): string
    {
        return 'Создать технологию';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
