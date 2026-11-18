<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\CreateSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\ListSiteSettings;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Настройки';

    protected static ?string $navigationLabel = 'Настройки сайта';

    protected static ?string $modelLabel = 'настройка';

    protected static ?string $pluralModelLabel = 'Настройки сайта';

    protected static ?int $navigationSort = 61;

    protected static ?string $recordTitleAttribute = 'key';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Настройка')
                    ->schema([
                        TextInput::make('key')
                            ->label('Ключ')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('group')
                            ->label('Группа')
                            ->default('general')
                            ->maxLength(64),
                        Select::make('type')
                            ->label('Тип значения')
                            ->options([
                                'string' => 'Строка',
                                'text' => 'Текст',
                                'url' => 'URL',
                                'email' => 'Email',
                                'phone' => 'Телефон',
                                'boolean' => 'Да/Нет',
                            ])
                            ->default('string')
                            ->required(),
                        Toggle::make('is_public')
                            ->label('Доступно публично')
                            ->default(true),
                        Textarea::make('value')
                            ->label('Значение')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Ключ')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Значение')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('group')
                    ->label('Группа')
                    ->badge()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->badge(),
                IconColumn::make('is_public')
                    ->label('Публичная')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('group')
            ->filters([
                SelectFilter::make('group')
                    ->label('Группа')
                    ->options(fn () => SiteSetting::query()->distinct()->pluck('group', 'group')->all()),
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
            'index' => ListSiteSettings::route('/'),
            'create' => CreateSiteSetting::route('/create'),
            'edit' => EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
