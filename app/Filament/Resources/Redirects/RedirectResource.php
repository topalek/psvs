<?php

namespace App\Filament\Resources\Redirects;

use App\Filament\Resources\Redirects\Pages\CreateRedirect;
use App\Filament\Resources\Redirects\Pages\EditRedirect;
use App\Filament\Resources\Redirects\Pages\ListRedirects;
use App\Models\Redirect;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Настройки';

    protected static ?string $navigationLabel = 'Редиректы';

    protected static ?string $modelLabel = 'редирект';

    protected static ?string $pluralModelLabel = 'Редиректы';

    protected static ?int $navigationSort = 60;

    protected static ?string $recordTitleAttribute = 'source_path';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Правило')
                    ->schema([
                        TextInput::make('source_path')
                            ->label('Старый путь')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Например: /tariff/'),
                        TextInput::make('destination_path')
                            ->label('Новый путь или URL')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Например: /tariffs/ или https://example.com/'),
                        Select::make('status_code')
                            ->label('Код ответа')
                            ->options([
                                301 => '301 — постоянный',
                                302 => '302 — временный',
                            ])
                            ->default(301)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source_path')
                    ->label('Старый путь')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('destination_path')
                    ->label('Новый путь')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('status_code')
                    ->label('Код')
                    ->badge()
                    ->color(fn ($state) => (int) $state === 301 ? 'success' : 'warning'),
                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                TextColumn::make('hits')
                    ->label('Переходов')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('source_path')
            ->filters([
                TernaryFilter::make('is_active')->label('Активность'),
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => ListRedirects::route('/'),
            'create' => CreateRedirect::route('/create'),
            'edit' => EditRedirect::route('/{record}/edit'),
        ];
    }
}
