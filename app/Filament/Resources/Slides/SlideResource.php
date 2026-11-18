<?php

namespace App\Filament\Resources\Slides;

use App\Filament\Resources\Slides\Pages\CreateSlide;
use App\Filament\Resources\Slides\Pages\EditSlide;
use App\Filament\Resources\Slides\Pages\ListSlides;
use App\Models\Slide;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SlideResource extends Resource
{
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Slide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Контент';

    protected static ?string $navigationLabel = 'Слайды';

    protected static ?string $modelLabel = 'слайд';

    protected static ?string $pluralModelLabel = 'Слайды';

    protected static ?int $navigationSort = 25;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Слайд')
                    ->schema(static::slideFields())
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Shared slide fields used by both the standalone slide editor and the
     * slider repeater, so the schema is never duplicated.
     *
     * @return array<int, Component>
     */
    public static function slideFields(bool $includeSlider = true): array
    {
        return [
            ...($includeSlider ? [
                Select::make('slider_id')
                    ->label('Слайдер')
                    ->relationship('slider', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ] : []),
            TextInput::make('name')
                ->label('Название')
                ->required()
                ->maxLength(255),
            TextInput::make('headline')
                ->label('Заголовок')
                ->maxLength(255),
            TextInput::make('subtitle')
                ->label('Подзаголовок')
                ->maxLength(255),
            TextInput::make('alt_text')
                ->label('Alt-текст')
                ->maxLength(255),
            TextInput::make('cta_label')
                ->label('Текст кнопки')
                ->maxLength(255),
            TextInput::make('cta_url')
                ->label('Ссылка кнопки')
                ->maxLength(255),
            TextInput::make('theme')
                ->label('Тема')
                ->maxLength(32),
            TextInput::make('sort_order')
                ->label('Порядок')
                ->numeric()
                ->default(0),
            Toggle::make('is_enabled')
                ->label('Включён')
                ->default(true),
            DateTimePicker::make('starts_at')
                ->label('Показывать с'),
            DateTimePicker::make('ends_at')
                ->label('Показывать до'),
            SpatieMediaLibraryFileUpload::make('desktop_image')
                ->label('Изображение (desktop)')
                ->collection('desktop_image')
                ->image()
                ->rules(['mimes:jpg,jpeg,png,gif,webp', 'max:10240'])
                ->imageEditor(),
            SpatieMediaLibraryFileUpload::make('mobile_image')
                ->label('Изображение (mobile)')
                ->collection('mobile_image')
                ->image()
                ->rules(['mimes:jpg,jpeg,png,gif,webp', 'max:10240']),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slider.name')
                    ->label('Слайдер')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                TextColumn::make('headline')
                    ->label('Заголовок')
                    ->limit(50),
                IconColumn::make('is_enabled')
                    ->label('Включён')
                    ->boolean(),
                TextColumn::make('starts_at')
                    ->label('С')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('По')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('slider_id')
                    ->label('Слайдер')
                    ->relationship('slider', 'name'),
                TernaryFilter::make('is_enabled')->label('Включён'),
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
            'index' => ListSlides::route('/'),
            'create' => CreateSlide::route('/create'),
            'edit' => EditSlide::route('/{record}/edit'),
        ];
    }
}
