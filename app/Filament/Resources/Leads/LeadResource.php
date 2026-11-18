<?php

namespace App\Filament\Resources\Leads;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Support\FieldHelpers;
use App\Models\Lead;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|\UnitEnum|null $navigationGroup = 'Заявки';

    protected static ?string $navigationLabel = 'Заявки';

    protected static ?string $modelLabel = 'заявка';

    protected static ?string $pluralModelLabel = 'Заявки';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        $count = Lead::query()->where('status', LeadStatus::New->value)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Заявка')
                    ->schema([
                        Select::make('type')
                            ->label('Тип заявки')
                            ->options(LeadType::options())
                            ->disabled(),
                        TextInput::make('name')
                            ->label('Имя')
                            ->disabled(),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel()
                            ->telRegex('/^[0-9+()\-\s]*$/')
                            ->disabled(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->disabled(),
                        TextInput::make('company_name')
                            ->label('Компания')
                            ->disabled(),
                        TextInput::make('source_page')
                            ->label('Страница-источник')
                            ->disabled(),
                        DateTimePicker::make('created_at')
                            ->label('Создана')
                            ->disabled(),
                        Textarea::make('message')
                            ->label('Сообщение')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Адрес и тариф')
                    ->schema([
                        Select::make('area_id')
                            ->label('Зона покрытия')
                            ->relationship('area', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Select::make('tariff_id')
                            ->label('Тариф')
                            ->relationship('tariff', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        TextInput::make('locality')
                            ->label('Населённый пункт')
                            ->disabled(),
                        TextInput::make('street')
                            ->label('Улица')
                            ->disabled(),
                        TextInput::make('house')
                            ->label('Дом')
                            ->disabled(),
                        TextInput::make('object_type')
                            ->label('Тип объекта')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('Обработка')
                    ->schema([
                        Select::make('status')
                            ->label('Статус')
                            ->options(LeadStatus::options())
                            ->required(),
                        Select::make('assigned_to')
                            ->label('Ответственный')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn () => auth()->user()?->can('leads.assign') ?? false),
                    ])
                    ->columns(2),
                Section::make('Согласие')
                    ->schema([
                        TextInput::make('consent_version')
                            ->label('Версия согласия')
                            ->disabled(),
                        DateTimePicker::make('consent_at')
                            ->label('Дата согласия')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('Внутренние заметки')
                    ->schema([
                        Repeater::make('notes')
                            ->relationship()
                            ->label('Заметки')
                            ->schema([
                                Hidden::make('user_id')
                                    ->default(fn () => auth()->id()),
                                Textarea::make('body')
                                    ->label('Текст заметки')
                                    ->rows(2)
                                    ->required()
                                    ->columnSpanFull(),
                                Toggle::make('is_internal')
                                    ->label('Внутренняя')
                                    ->default(true),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Добавить заметку')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => isset($state['body']) ? str($state['body'])->limit(60) : null)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => FieldHelpers::enumLabel($state))
                    ->badge(),
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),
                TextColumn::make('locality')
                    ->label('Населённый пункт')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => FieldHelpers::enumLabel($state))
                    ->badge()
                    ->color(fn ($state) => $state instanceof LeadStatus ? $state->color() : 'gray'),
                TextColumn::make('assignee.name')
                    ->label('Ответственный')
                    ->placeholder('—'),
                TextColumn::make('source_page')
                    ->label('Источник')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(LeadStatus::options()),
                SelectFilter::make('type')
                    ->label('Тип')
                    ->options(LeadType::options()),
                SelectFilter::make('assigned_to')
                    ->label('Ответственный')
                    ->relationship('assignee', 'name'),
            ])
            ->recordActions([
                EditAction::make()->label('Открыть'),
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
            'index' => ListLeads::route('/'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
