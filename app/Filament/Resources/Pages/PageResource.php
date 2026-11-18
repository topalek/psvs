<?php

namespace App\Filament\Resources\Pages;

use App\Enums\ContentStatus;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Support\FieldHelpers;
use App\Models\Page;
use App\Models\PageSection;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PageResource extends Resource
{
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Page::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $navigationLabel = 'Страницы';

    protected static ?string $modelLabel = 'страница';

    protected static ?string $pluralModelLabel = 'Страницы';

    protected static ?int $navigationSort = 22;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Страница')
                    ->schema([
                        TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('URL-идентификатор')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('status')
                            ->label('Статус')
                            ->options(ContentStatus::options())
                            ->default(ContentStatus::Draft->value)
                            ->required(),
                        Select::make('template')
                            ->label('Шаблон')
                            ->options([
                                'default' => 'Стандартная',
                                'home' => 'Главная',
                                'legal' => 'Юридическая',
                            ])
                            ->default('default')
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Дата публикации'),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_system')
                            ->label('Системная страница')
                            ->disabled()
                            ->dehydrated(false),
                        RichEditor::make('body')
                            ->label('Содержимое')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Секции страницы')
                    ->description('Блоки, из которых собирается страница.')
                    ->schema([
                        Repeater::make('sections')
                            ->relationship()
                            ->label('Секции')
                            ->defaultItems(0)
                            ->schema([
                                Select::make('type')
                                    ->label('Тип секции')
                                    ->options(PageSection::types())
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Заголовок'),
                                TextInput::make('subtitle')
                                    ->label('Подзаголовок'),
                                Textarea::make('body')
                                    ->label('Текст')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                KeyValue::make('data')
                                    ->label('Дополнительные данные')
                                    ->columnSpanFull(),
                                TextInput::make('sort_order')
                                    ->label('Порядок')
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('is_enabled')
                                    ->label('Включена')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->orderColumn('sort_order')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? $state['type'] ?? null)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO-заголовок')
                            ->maxLength(255),
                        TextInput::make('canonical_url')
                            ->label('Canonical URL')
                            ->url()
                            ->maxLength(255),
                        Textarea::make('seo_description')
                            ->label('SEO-описание')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('og_image')
                            ->label('OG-изображение (URL или путь)')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => '/'.ltrim((string) $state, '/')),
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => FieldHelpers::enumLabel($state))
                    ->badge()
                    ->color(fn ($state) => match ($state instanceof ContentStatus ? $state : ContentStatus::tryFrom((string) $state)) {
                        ContentStatus::Published => 'success',
                        ContentStatus::Scheduled => 'warning',
                        ContentStatus::Archived => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('template')
                    ->label('Шаблон')
                    ->badge(),
                IconColumn::make('is_system')
                    ->label('Системная')
                    ->boolean(),
                TextColumn::make('sections_count')
                    ->label('Секций')
                    ->counts('sections'),
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(ContentStatus::options()),
                SelectFilter::make('template')
                    ->label('Шаблон')
                    ->options([
                        'default' => 'Стандартная',
                        'home' => 'Главная',
                        'legal' => 'Юридическая',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (Page $record) => ! $record->is_system),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => false),
            ]);
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof Page && ! $record->is_system && parent::canDelete($record);
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
