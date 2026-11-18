<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),

                TextInput::make('phone')
                    ->label('Телефон')
                    ->tel()
                    ->maxLength(30)
                    ->regex('/^\+7\s?\(\d{3}\)\s?\d{3}-\d{2}-\d{2}$/')
                    ->validationMessages([
                        'regex' => 'Введите телефон в формате +7 (999) 123-45-67.',
                    ]),

                $this->getEmailFormComponent(),

                $this->getPasswordFormComponent(),

                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
