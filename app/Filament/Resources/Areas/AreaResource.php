<?php

namespace App\Filament\Resources\Areas;

use App\Enums\VerificationStatus;
use App\Filament\Resources\Areas\Pages\CreateArea;
use App\Filament\Resources\Areas\Pages\EditArea;
use App\Filament\Resources\Areas\Pages\ListAreas;
use App\Filament\Support\FieldHelpers;
use App\Models\Area;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Каталог';

    protected static ?string $navigationLabel = 'Зоны покрытия';

    protected static ?string $modelLabel = 'зона покрытия';

    protected static ?string $pluralModelLabel = 'Зоны покрытия';

    protected static ?int $navigationSort = 12;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Населённый пункт / зона')
                    ->schema([
                        Select::make('parent_id')
                            ->label('Родительская зона')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('URL-идентификатор')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('type')
                            ->label('Тип')
                            ->options([
                                'locality' => 'Населённый пункт',
                                'settlement' => 'Посёлок / село / хутор',
                                'district' => 'Район',
                                'street' => 'Улица',
                                'house' => 'Дом',
                            ])
                            ->default('locality')
                            ->required(),
                        TextInput::make('district')
                            ->label('Район')
                            ->maxLength(255),
                        Select::make('verification_status')
                            ->label('Статус проверки')
                            ->options(VerificationStatus::options())
                            ->default(VerificationStatus::Unverified->value)
                            ->required(),
                        DateTimePicker::make('last_verified_at')
                            ->label('Проверено'),
                        Toggle::make('is_published')
                            ->label('Опубликовано'),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
                Section::make('Служебное')
                    ->schema([
                        Textarea::make('internal_notes')
                            ->label('Внутренние заметки')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
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
                TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'locality' => 'Населённый пункт',
                        'settlement' => 'Посёлок / село / хутор',
                        'district' => 'Район',
                        'street' => 'Улица',
                        'house' => 'Дом',
                        default => (string) $state,
                    })
                    ->badge(),
                TextColumn::make('district')
                    ->label('Район')
                    ->searchable(),
                TextColumn::make('verification_status')
                    ->label('Проверка')
                    ->formatStateUsing(fn ($state) => FieldHelpers::enumLabel($state))
                    ->badge()
                    ->color(fn ($state) => ($state instanceof VerificationStatus ? $state->value : $state) === VerificationStatus::Verified->value ? 'success' : 'warning'),
                IconColumn::make('is_published')
                    ->label('Опубликовано')
                    ->boolean(),
                TextColumn::make('last_verified_at')
                    ->label('Проверено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('verification_status')
                    ->label('Статус проверки')
                    ->options(VerificationStatus::options()),
                TernaryFilter::make('is_published')->label('Публикация'),
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
            'index' => ListAreas::route('/'),
            'create' => CreateArea::route('/create'),
            'edit' => EditArea::route('/{record}/edit'),
        ];
    }
}
