<?php

namespace App\Filament\Resources\HelpArticles;

use App\Filament\Resources\HelpArticles\Pages\CreateHelpArticle;
use App\Filament\Resources\HelpArticles\Pages\EditHelpArticle;
use App\Filament\Resources\HelpArticles\Pages\ListHelpArticles;
use App\Models\HelpArticle;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
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

class HelpArticleResource extends Resource
{
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = HelpArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Поддержка';

    protected static ?string $navigationLabel = 'Справочные статьи';

    protected static ?string $modelLabel = 'справочная статья';

    protected static ?string $pluralModelLabel = 'Справочные статьи';

    protected static ?int $navigationSort = 43;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Статья')
                    ->schema([
                        Select::make('help_category_id')
                            ->label('Категория')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('URL-идентификатор')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->label('Краткое описание')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Текст статьи')
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->label('Опубликована'),
                        DateTimePicker::make('last_reviewed_at')
                            ->label('Проверена'),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)->columnSpan(2),
                Section::make('Пошаговая инструкция')
                    ->schema([
                        Repeater::make('steps')
                            ->label('Шаги')
                            ->defaultItems(0)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Заголовок шага')
                                    ->required(),
                                Textarea::make('text')
                                    ->label('Описание шага')
                                    ->rows(2)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->collapsed()->columnSpan(2),
                Section::make('Связанные ссылки')
                    ->schema([
                        Repeater::make('related_links')
                            ->label('Ссылки')
                            ->defaultItems(0)
                            ->schema([
                                TextInput::make('label')
                                    ->label('Подпись')
                                    ->required(),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->collapsed()->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Категория')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(80),
                IconColumn::make('is_published')
                    ->label('Опубликована')
                    ->boolean(),
                TextColumn::make('last_reviewed_at')
                    ->label('Проверена')
                    ->dateTime('d.m.Y')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('help_category_id')
                    ->label('Категория')
                    ->relationship('category', 'name'),
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
            'index' => ListHelpArticles::route('/'),
            'create' => CreateHelpArticle::route('/create'),
            'edit' => EditHelpArticle::route('/{record}/edit'),
        ];
    }
}
