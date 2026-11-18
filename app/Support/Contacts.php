<?php

namespace App\Support;

use App\Models\ContactChannel;
use App\Models\SiteSetting;

class Contacts
{
    public static function phones(): array
    {
        return ContactChannel::query()->active()->where('type', 'phone')->get()->all();
    }

    public static function emails(): array
    {
        return ContactChannel::query()->active()->where('type', 'email')->get()->all();
    }

    public static function address(): string
    {
        return (string) SiteSetting::get('company_address', '');
    }

    public static function officeHours(): string
    {
        return (string) SiteSetting::get('office_hours', '');
    }

    public static function supportHours(): string
    {
        return (string) SiteSetting::get('support_hours', '');
    }

    public static function cabinetUrl(): string
    {
        return (string) SiteSetting::get('cabinet_url', 'https://lk.p-svs.ru/');
    }

    public static function paymentUrl(): ?string
    {
        $url = SiteSetting::get('payment_url');

        return $url ? (string) $url : null;
    }

    public static function mapUrl(): ?string
    {
        $url = SiteSetting::get('map_url');

        return $url ? (string) $url : null;
    }
}
