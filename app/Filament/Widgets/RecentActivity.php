<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Spatie\Activitylog\Models\Activity;

class RecentActivity extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('audit.view') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Последние действия в системе')
            ->query(Activity::query()->latest())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i'),
                TextColumn::make('causer.name')
                    ->label('Пользователь')
                    ->placeholder('Система'),
                TextColumn::make('event')
                    ->label('Событие')
                    ->badge(),
                TextColumn::make('description')
                    ->label('Описание')
                    ->limit(60),
            ])
            ->paginated(false);
    }
}
