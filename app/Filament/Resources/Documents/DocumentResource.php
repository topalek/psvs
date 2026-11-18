<?php

namespace App\Filament\Resources\Documents;

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Models\Document;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

class DocumentResource extends Resource
{
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|\UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $navigationLabel = 'Документы';

    protected static ?string $modelLabel = 'документ';

    protected static ?string $pluralModelLabel = 'Документы';

    protected static ?int $navigationSort = 23;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Документ')
                    ->schema([
                        TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('URL-идентификатор')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('category')
                            ->label('Категория')
                            ->maxLength(255),
                        TextInput::make('version')
                            ->label('Версия')
                            ->maxLength(255),
                        DatePicker::make('effective_date')
                            ->label('Дата вступления в силу'),
                        Textarea::make('description')
                            ->label('Описание')
                            ->rows(4)
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->label('Опубликован'),
                        Toggle::make('is_public')
                            ->label('Доступен публично')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
                Section::make('Файл')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('file')
                            ->label('Файл документа')
                            ->collection('file')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->rules(['mimes:pdf,doc,xls,xlsx'])
                            ->maxSize(20480)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Категория')
                    ->searchable()
                    ->badge(),
                TextColumn::make('version')
                    ->label('Версия'),
                TextColumn::make('effective_date')
                    ->label('Действует с')
                    ->date('d.m.Y')
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Опубликован')
                    ->boolean(),
                IconColumn::make('is_public')
                    ->label('Публичный')
                    ->boolean(),
                TextColumn::make('media_count')
                    ->label('Файлов')
                    ->counts('media'),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('category')
                    ->label('Категория')
                    ->options(fn () => Document::query()->whereNotNull('category')->distinct()->pluck('category', 'category')->all()),
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
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
