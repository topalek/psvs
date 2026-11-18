<?php

namespace App\Filament\Resources\HelpCategories;

use App\Filament\Resources\HelpCategories\Pages\CreateHelpCategory;
use App\Filament\Resources\HelpCategories\Pages\EditHelpCategory;
use App\Filament\Resources\HelpCategories\Pages\ListHelpCategories;
use App\Models\HelpCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HelpCategoryResource extends Resource
{
    protected static ?string $model = HelpCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static string|\UnitEnum|null $navigationGroup = 'Поддержка';

    protected static ?string $navigationLabel = 'Категории справки';

    protected static ?string $modelLabel = 'категория справки';

    protected static ?string $pluralModelLabel = 'Категории справки';

    protected static ?int $navigationSort = 42;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Категория')
                    ->schema([
                        TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('URL-идентификатор')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
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
                TextColumn::make('slug')
                    ->label('URL-идентификатор')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('articles_count')
                    ->label('Статей')
                    ->counts('articles'),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
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
            'index' => ListHelpCategories::route('/'),
            'create' => CreateHelpCategory::route('/create'),
            'edit' => EditHelpCategory::route('/{record}/edit'),
        ];
    }
}
