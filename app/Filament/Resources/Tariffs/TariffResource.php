<?php

namespace App\Filament\Resources\Tariffs;

use App\Enums\CustomerType;
use App\Filament\Resources\Tariffs\Pages\CreateTariff;
use App\Filament\Resources\Tariffs\Pages\EditTariff;
use App\Filament\Resources\Tariffs\Pages\ListTariffs;
use App\Filament\Support\FieldHelpers;
use App\Models\Tariff;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TariffResource extends Resource
{
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Tariff::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|\UnitEnum|null $navigationGroup = 'Каталог';

    protected static ?string $navigationLabel = 'Тарифы';

    protected static ?string $modelLabel = 'тариф';

    protected static ?string $pluralModelLabel = 'Тарифы';

    protected static ?int $navigationSort = 13;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основное')
                    ->schema([
                        Select::make('technology_id')
                            ->label('Технология')
                            ->relationship('technology', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Название тарифа')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('URL-идентификатор')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('internal_code')
                            ->label('Внутренний код')
                            ->maxLength(255),
                        Select::make('customer_type')
                            ->label('Тип клиента')
                            ->options(CustomerType::options())
                            ->default(CustomerType::Residential->value)
                            ->required(),
                        TextInput::make('speed_mbps')
                            ->label('Скорость, Мбит/с')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('upload_speed_mbps')
                            ->label('Исходящая скорость, Мбит/с')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('badge')
                            ->label('Бейдж')
                            ->maxLength(255),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                        DatePicker::make('effective_date')
                            ->label('Дата вступления в силу'),
                        DateTimePicker::make('last_verified_at')
                            ->label('Цены проверены'),
                        Toggle::make('is_featured')
                            ->label('Показывать как популярный'),
                        Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                        Toggle::make('is_published')
                            ->label('Опубликован'),
                    ])
                    ->columns(2),
                Section::make('Стоимость')
                    ->description('Суммы вводятся в рублях. В базе хранятся копейки.')
                    ->schema([
                        TextInput::make('regular_price')
                            ->label('Абонентская плата, ₽')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(0)
                            ->formatStateUsing(fn ($state) => FieldHelpers::minorToRubles($state))
                            ->dehydrateStateUsing(fn ($state) => FieldHelpers::rublesToMinor($state) ?? 0),
                        TextInput::make('promo_price')
                            ->label('Акционная цена, ₽')
                            ->numeric()
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => FieldHelpers::minorToRubles($state))
                            ->dehydrateStateUsing(fn ($state) => FieldHelpers::rublesToMinor($state)),
                        TextInput::make('price_after_promo')
                            ->label('Цена после акции, ₽')
                            ->numeric()
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => FieldHelpers::minorToRubles($state))
                            ->dehydrateStateUsing(fn ($state) => FieldHelpers::rublesToMinor($state)),
                        TextInput::make('price_period_days')
                            ->label('Период цены, дней')
                            ->numeric()
                            ->minValue(1)
                            ->default(30)
                            ->required(),
                        TextInput::make('connection_fee')
                            ->label('Стоимость подключения, ₽')
                            ->numeric()
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => FieldHelpers::minorToRubles($state))
                            ->dehydrateStateUsing(fn ($state) => FieldHelpers::rublesToMinor($state)),
                        TextInput::make('equipment_fee')
                            ->label('Стоимость оборудования, ₽')
                            ->numeric()
                            ->minValue(0)
                            ->formatStateUsing(fn ($state) => FieldHelpers::minorToRubles($state))
                            ->dehydrateStateUsing(fn ($state) => FieldHelpers::rublesToMinor($state)),
                        DateTimePicker::make('promo_starts_at')
                            ->label('Акция с'),
                        DateTimePicker::make('promo_ends_at')
                            ->label('Акция до'),
                    ])
                    ->columns(2),
                Section::make('Условия и состав')
                    ->schema([
                        TagsInput::make('included_services')
                            ->label('Включённые услуги')
                            ->placeholder('Добавьте услугу и нажмите Enter'),
                        Textarea::make('equipment_info')
                            ->label('Информация об оборудовании')
                            ->rows(3),
                        Textarea::make('terms')
                            ->label('Условия и примечания')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Доступность')
                    ->schema([
                        Select::make('areas')
                            ->label('Зоны покрытия')
                            ->relationship('areas', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        Select::make('services')
                            ->label('Услуги')
                            ->relationship('services', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('technology.name')
                    ->label('Технология')
                    ->searchable(),
                TextColumn::make('customer_type')
                    ->label('Клиент')
                    ->formatStateUsing(fn ($state) => FieldHelpers::enumLabel($state))
                    ->badge(),
                TextColumn::make('speed_mbps')
                    ->label('Скорость')
                    ->suffix(' Мбит/с')
                    ->sortable(),
                TextColumn::make('regular_price')
                    ->label('Цена')
                    ->formatStateUsing(fn ($state) => FieldHelpers::money($state))
                    ->sortable(),
                TextColumn::make('promo_price')
                    ->label('Акция')
                    ->formatStateUsing(fn ($state) => FieldHelpers::money($state))
                    ->toggleable(),
                TextColumn::make('areas_count')
                    ->label('Зон')
                    ->counts('areas'),
                IconColumn::make('is_featured')
                    ->label('Популярный')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                IconColumn::make('is_published')
                    ->label('Опубликован')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('technology_id')
                    ->label('Технология')
                    ->relationship('technology', 'name'),
                SelectFilter::make('customer_type')
                    ->label('Тип клиента')
                    ->options(CustomerType::options()),
                TernaryFilter::make('is_published')->label('Публикация'),
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
            'index' => ListTariffs::route('/'),
            'create' => CreateTariff::route('/create'),
            'edit' => EditTariff::route('/{record}/edit'),
        ];
    }
}
