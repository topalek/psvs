<?php

namespace App\Filament\Resources\Tariffs\Pages;

use App\Filament\Resources\Tariffs\TariffResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTariff extends CreateRecord
{
    protected static string $resource = TariffResource::class;

    public function getTitle(): string
    {
        return 'Создать тариф';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
