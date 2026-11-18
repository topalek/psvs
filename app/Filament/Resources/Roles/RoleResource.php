<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Support\RoleLabels;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Пользователи и доступ';

    protected static ?string $navigationLabel = 'Роли и права';

    protected static ?string $modelLabel = 'роль';

    protected static ?string $pluralModelLabel = 'Роли и права';

    protected static ?int $navigationSort = 51;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user !== null && ($user->can('roles.view') || $user->can('roles.manage'));
    }

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if ($record instanceof Role) {
            return RoleLabels::label($record->name);
        }

        return parent::getRecordTitle($record);
    }

    public static function canCreate(): bool
    {
        // The role set is fixed to admin, manager and engineer.
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof Role
            && $record->name !== 'admin'
            && (auth()->user()?->can('roles.manage') ?? false);
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Роль')
                    ->schema([
                        TextInput::make('name')
                            ->label('Системное имя')
                            ->helperText('Фиксированное имя роли: admin, manager или engineer.')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('permissions')
                            ->label('Разрешения')
                            ->relationship('permissions', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Роль')
                    ->formatStateUsing(fn (?string $state): string => RoleLabels::label($state))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Пользователей')
                    ->counts('users'),
                TextColumn::make('permissions_count')
                    ->label('Разрешений')
                    ->counts('permissions'),
                TextColumn::make('updated_at')
                    ->label('Обновлена')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Role $record): bool => $record->name !== 'admin'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
