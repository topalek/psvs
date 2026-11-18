<?php

namespace App\Filament\Resources\Promotions;

use App\Enums\ContentStatus;
use App\Enums\DiscountType;
use App\Filament\Resources\Promotions\Pages\CreatePromotion;
use App\Filament\Resources\Promotions\Pages\EditPromotion;
use App\Filament\Resources\Promotions\Pages\ListPromotions;
use App\Filament\Support\FieldHelpers;
use App\Models\Promotion;
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

class PromotionResource extends Resource
{
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Promotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|\UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $navigationLabel = 'Акции';

    protected static ?string $modelLabel = 'акция';

    protected static ?string $pluralModelLabel = 'Акции';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основное')
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
                        Textarea::make('excerpt')
                            ->label('Краткое описание')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->label('Текст акции')
                            ->columnSpanFull(),
                        Select::make('status')
                            ->label('Статус')
                            ->options(ContentStatus::options())
                            ->default(ContentStatus::Draft->value)
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Дата публикации'),
                        DateTimePicker::make('starts_at')
                            ->label('Действует с'),
                        DateTimePicker::make('ends_at')
                            ->label('Действует до'),
                        Toggle::make('is_indefinite')
                            ->label('Бессрочная акция'),
                        Toggle::make('is_featured')
                            ->label('Показывать на главной'),
                        TextInput::make('sort_order')
                            ->label('Порядок сортировки')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Условия')
                    ->schema([
                        Select::make('discount_type')
                            ->label('Тип скидки')
                            ->options(DiscountType::options()),
                        TextInput::make('discount_value')
                            ->label('Размер скидки')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('cta_label')
                            ->label('Текст кнопки')
                            ->maxLength(255),
                        TextInput::make('cta_url')
                            ->label('Ссылка кнопки')
                            ->maxLength(255),
                        Textarea::make('terms')
                            ->label('Условия акции')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)->columnSpanFull(),
                Section::make('Тарифы')
                    ->schema([
                        Select::make('tariffs')
                            ->label('Тарифы, участвующие в акции')
                            ->relationship('tariffs', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ]),
                Section::make('Медиа')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('cover')
                            ->label('Обложка')
                            ->collection('cover')
                            ->image()
                            ->rules(['mimes:jpg,jpeg,png,gif,webp', 'max:10240'])
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
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
                TextColumn::make('starts_at')
                    ->label('С')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('По')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                IconColumn::make('is_indefinite')
                    ->label('Бессрочно')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->label('На главной')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(ContentStatus::options()),
                TernaryFilter::make('is_featured')->label('На главной'),
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
            'index' => ListPromotions::route('/'),
            'create' => CreatePromotion::route('/create'),
            'edit' => EditPromotion::route('/{record}/edit'),
        ];
    }
}
