<?php

namespace App\Filament\Resources\MenuItems;

use App\Filament\Resources\MenuItems\Pages\CreateMenuItem;
use App\Filament\Resources\MenuItems\Pages\EditMenuItem;
use App\Filament\Resources\MenuItems\Pages\ListMenuItems;
use App\Models\MenuItem;
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

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|\UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $navigationLabel = 'Пункты меню';

    protected static ?string $modelLabel = 'пункт меню';

    protected static ?string $pluralModelLabel = 'Пункты меню';

    protected static ?int $navigationSort = 27;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Пункт меню')
                    ->schema([
                        Select::make('menu_id')
                            ->label('Меню')
                            ->relationship('menu', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('parent_id')
                            ->label('Родительский пункт')
                            ->relationship('parent', 'label')
                            ->searchable()
                            ->preload(),
                        TextInput::make('label')
                            ->label('Подпись')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('url')
                            ->label('Ссылка')
                            ->required()
                            ->maxLength(255),
                        Select::make('target')
                            ->label('Открывать')
                            ->options([
                                '_self' => 'В текущем окне',
                                '_blank' => 'В новом окне',
                            ])
                            ->default('_self')
                            ->required(),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('menu.name')
                    ->label('Меню')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.label')
                    ->label('Родитель')
                    ->placeholder('—'),
                TextColumn::make('label')
                    ->label('Подпись')
                    ->searchable(),
                TextColumn::make('url')
                    ->label('Ссылка')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('target')
                    ->label('Цель')
                    ->badge(),
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
                SelectFilter::make('menu_id')
                    ->label('Меню')
                    ->relationship('menu', 'name'),
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
            'index' => ListMenuItems::route('/'),
            'create' => CreateMenuItem::route('/create'),
            'edit' => EditMenuItem::route('/{record}/edit'),
        ];
    }
}
