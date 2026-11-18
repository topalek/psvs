<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ManageActivities;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Пользователи и доступ';

    protected static ?string $navigationLabel = 'Журнал действий';

    protected static ?string $modelLabel = 'запись журнала';

    protected static ?string $pluralModelLabel = 'Журнал действий';

    protected static ?int $navigationSort = 52;

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('audit.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('Пользователь')
                    ->placeholder('Система')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Событие')
                    ->badge(),
                TextColumn::make('description')
                    ->label('Описание')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('subject_type')
                    ->label('Объект')
                    ->formatStateUsing(fn ($state) => $state ? class_basename((string) $state) : '—'),
                TextColumn::make('subject_id')
                    ->label('ID объекта')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('log_name')
                    ->label('Канал')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->label('Событие')
                    ->options(fn () => Activity::query()->distinct()->pluck('event', 'event')->filter()->all()),
                SelectFilter::make('log_name')
                    ->label('Канал')
                    ->options(fn () => Activity::query()->distinct()->pluck('log_name', 'log_name')->filter()->all()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageActivities::route('/'),
        ];
    }
}
