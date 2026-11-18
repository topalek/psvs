<?php

namespace App\Filament\Resources\HelpCategories\Pages;

use App\Filament\Resources\HelpCategories\HelpCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHelpCategory extends CreateRecord
{
    protected static string $resource = HelpCategoryResource::class;

    public function getTitle(): string
    {
        return 'Создать категорию справки';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
