<?php

namespace App\Filament\Resources\ContactChannels\Pages;

use App\Filament\Resources\ContactChannels\ContactChannelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContactChannel extends CreateRecord
{
    protected static string $resource = ContactChannelResource::class;

    public function getTitle(): string
    {
        return 'Создать контакт';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
