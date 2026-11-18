<?php

namespace App\Filament\Resources\Media;

use App\Filament\Resources\Media\Pages\ManageMedia;
use App\Models\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $navigationLabel = 'Медиатека';

    protected static ?string $modelLabel = 'файл';

    protected static ?string $pluralModelLabel = 'Медиатека';

    protected static ?int $navigationSort = 28;

    protected static ?string $recordTitleAttribute = 'file_name';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
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
                TextColumn::make('file_name')
                    ->label('Файл')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('collection_name')
                    ->label('Коллекция')
                    ->badge(),
                TextColumn::make('mime_type')
                    ->label('Тип')
                    ->toggleable(),
                TextColumn::make('human_readable_size')
                    ->label('Размер')
                    ->state(fn (Media $record) => $record->human_readable_size),
                TextColumn::make('model_type')
                    ->label('Объект')
                    ->formatStateUsing(fn ($state) => $state ? class_basename((string) $state) : '—'),
                TextColumn::make('created_at')
                    ->label('Загружен')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('collection_name')
                    ->label('Коллекция')
                    ->options(fn () => Media::query()->distinct()->pluck('collection_name', 'collection_name')->all()),
                SelectFilter::make('model_type')
                    ->label('Объект')
                    ->options(fn () => Media::query()
                        ->distinct()
                        ->pluck('model_type', 'model_type')
                        ->mapWithKeys(fn ($value) => [$value => class_basename((string) $value)])
                        ->all()),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Открыть')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Media $record) => $record->getUrl())
                    ->openUrlInNewTab(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMedia::route('/'),
        ];
    }
}
