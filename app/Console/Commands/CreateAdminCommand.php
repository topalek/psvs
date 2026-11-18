<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateAdminCommand extends Command
{
    protected $signature = 'app:create-admin
                            {email? : Email администратора}
                            {--name= : Имя администратора}
                            {--password= : Пароль (не рекомендуется передавать в командной строке)}';

    protected $description = 'Создать или обновить администратора сайта';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Email администратора');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Укажите корректный email.');

            return self::FAILURE;
        }

        $name = $this->option('name') ?: $this->ask('Имя', 'Администратор');
        $password = $this->option('password');

        if (! is_string($password) || $password === '') {
            $password = $this->secret('Пароль (не короче 12 символов)');
        }

        if (! is_string($password) || mb_strlen($password) < 12) {
            $this->error('Пароль должен быть не короче 12 символов.');

            return self::FAILURE;
        }

        Role::findOrCreate('admin', 'web');

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => is_string($name) && $name !== '' ? $name : 'Администратор',
                'password' => $password,
                'is_active' => true,
            ],
        );

        $user->assignRole('admin');

        $this->info("Администратор {$user->email} готов. Роль admin назначена.");

        return self::SUCCESS;
    }
}
