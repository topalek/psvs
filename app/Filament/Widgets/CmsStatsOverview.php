<?php

namespace App\Filament\Widgets;

use App\Enums\ContentStatus;
use App\Enums\LeadStatus;
use App\Models\Document;
use App\Models\Lead;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Tariff;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CmsStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->check();
    }

    protected function getStats(): array
    {
        $stats = [
            Stat::make('Опубликованные тарифы', (string) Tariff::query()->where('is_published', true)->where('is_active', true)->count())
                ->description('Доступны на сайте')
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->color('success'),
            Stat::make('Опубликованные новости', (string) Post::query()->where('status', ContentStatus::Published->value)->count())
                ->description('Всего опубликовано')
                ->icon(Heroicon::OutlinedNewspaper)
                ->color('info'),
            Stat::make('Активные акции', (string) Promotion::query()->where('status', ContentStatus::Published->value)->count())
                ->description('Опубликованные акции')
                ->icon(Heroicon::OutlinedMegaphone)
                ->color('warning'),
            Stat::make('Документы', (string) Document::query()->where('is_published', true)->count())
                ->description('Опубликовано')
                ->icon(Heroicon::OutlinedFolder)
                ->color('gray'),
        ];

        if (auth()->user()?->can('leads.view')) {
            array_unshift($stats, Stat::make('Новые заявки', (string) Lead::query()->where('status', LeadStatus::New->value)->count())
                ->description('Требуют обработки')
                ->icon(Heroicon::OutlinedInbox)
                ->color('danger'));
        }

        return $stats;
    }
}
