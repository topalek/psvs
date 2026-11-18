<?php

namespace App\Filament\Resources\HelpArticles\Pages;

use App\Filament\Resources\HelpArticles\HelpArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHelpArticle extends CreateRecord
{
    protected static string $resource = HelpArticleResource::class;

    public function getTitle(): string
    {
        return 'Создать статью';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
