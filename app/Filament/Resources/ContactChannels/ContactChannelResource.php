<?php

namespace App\Filament\Resources\ContactChannels;

use App\Filament\Resources\ContactChannels\Pages\CreateContactChannel;
use App\Filament\Resources\ContactChannels\Pages\EditContactChannel;
use App\Filament\Resources\ContactChannels\Pages\ListContactChannels;
use App\Models\ContactChannel;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactChannelResource extends Resource
{
    protected static ?string $model = ContactChannel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static string|\UnitEnum|null $navigationGroup = 'Настройки';

    protected static ?string $navigationLabel = 'Контакты';

    protected static ?string $modelLabel = 'контакт';

    protected static ?string $pluralModelLabel = 'Контакты';

    protected static ?int $navigationSort = 62;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Контакт')
                    ->schema([
                        Select::make('type')
                            ->label('Тип')
                            ->options([
                                'phone' => 'Телефон',
                                'email' => 'Email',
                                'address' => 'Адрес',
                                'messenger' => 'Мессенджер',
                                'other' => 'Другое',
                            ])
                            ->required(),
                        TextInput::make('label')
                            ->label('Подпись')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('value')
                            ->label('Значение')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Телефон, email или адрес в исходном виде.'),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
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
                TextColumn::make('type')
                    ->label('Тип')
                    ->badge(),
                TextColumn::make('label')
                    ->label('Подпись')
                    ->searchable(),
                TextColumn::make('value')
                    ->label('Значение')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('type')
                    ->label('Тип')
                    ->options([
                        'phone' => 'Телефон',
                        'email' => 'Email',
                        'address' => 'Адрес',
                        'messenger' => 'Мессенджер',
                        'other' => 'Другое',
                    ]),
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
            'index' => ListContactChannels::route('/'),
            'create' => CreateContactChannel::route('/create'),
            'edit' => EditContactChannel::route('/{record}/edit'),
        ];
    }
}
