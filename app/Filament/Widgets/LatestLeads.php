<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Filament\Support\FieldHelpers;
use App\Models\Lead;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestLeads extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('leads.view') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Последние заявки')
            ->query(Lead::query()->latest())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i'),
                TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => FieldHelpers::enumLabel($state))
                    ->badge(),
                TextColumn::make('name')
                    ->label('Имя'),
                TextColumn::make('phone')
                    ->label('Телефон'),
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => FieldHelpers::enumLabel($state))
                    ->badge()
                    ->color(fn ($state) => $state instanceof LeadStatus ? $state->color() : 'gray'),
            ])
            ->paginated(false);
    }
}
