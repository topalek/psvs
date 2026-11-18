<?php

namespace App\Filament\Resources\Posts;

use App\Enums\ContentStatus;
use App\Enums\IncidentStatus;
use App\Enums\PostType;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Support\FieldHelpers;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|\UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $navigationLabel = 'Новости и работы';

    protected static ?string $modelLabel = 'публикация';

    protected static ?string $pluralModelLabel = 'Новости и работы';

    protected static ?int $navigationSort = 21;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Публикация')
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
                        Select::make('type')
                            ->label('Тип')
                            ->options(PostType::options())
                            ->default(PostType::CompanyNews->value)
                            ->required(),
                        Select::make('status')
                            ->label('Статус')
                            ->options(ContentStatus::options())
                            ->default(ContentStatus::Draft->value)
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Дата публикации'),
                        DateTimePicker::make('unpublish_at')
                            ->label('Снять с публикации'),
                        Select::make('author_id')
                            ->label('Автор')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id()),
                        Textarea::make('excerpt')
                            ->label('Краткое описание')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Текст публикации')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Оперативная информация')
                    ->description('Заполняйте для плановых работ и аварий.')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Начало работ'),
                        DateTimePicker::make('ends_at')
                            ->label('Окончание работ'),
                        Select::make('incident_status')
                            ->label('Статус работ')
                            ->options(IncidentStatus::options()),
                        Textarea::make('operational_note')
                            ->label('Примечание для абонентов')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('Затронутые направления')
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
                    ->columns(2)
                    ->collapsed(),
                Section::make('Медиа')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('cover')
                            ->label('Обложка')
                            ->collection('cover')
                            ->image()
                            ->rules(['mimes:jpg,jpeg,png,gif,webp', 'max:10240'])
                            ->imageEditor()
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
                TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => FieldHelpers::enumLabel($state))
                    ->badge(),
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
                TextColumn::make('published_at')
                    ->label('Опубликовано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label('Автор')
                    ->searchable(),
                TextColumn::make('incident_status')
                    ->label('Работы')
                    ->formatStateUsing(fn ($state) => $state ? FieldHelpers::enumLabel($state) : '—')
                    ->badge()
                    ->color(fn ($state) => ($state instanceof IncidentStatus ? $state->isActive() : false) ? 'warning' : 'gray'),
                IconColumn::make('is_active_incident')
                    ->label('Активно')
                    ->state(fn (Post $record) => $record->isActiveIncident())
                    ->boolean(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Тип')
                    ->options(PostType::options()),
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(ContentStatus::options()),
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
