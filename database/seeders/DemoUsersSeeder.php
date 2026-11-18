<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['Администратор', 'admin@psvs.local', 'admin'],
            ['Менеджер', 'manager@psvs.local', 'manager'],
            ['Инженер', 'engineer@psvs.local', 'engineer'],
        ];

        foreach ($users as [$name, $email, $role]) {
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ],
            );

            $user->syncRoles([$role]);
        }

        // Demo accounts created for the previous role matrix stay usable
        // after the migration to the three-role model.
        $legacyManagerEmails = [
            'content@psvs.local',
            'tariff@psvs.local',
            'leads@psvs.local',
            'support@psvs.local',
            'viewer@psvs.local',
        ];

        User::query()
            ->whereIn('email', $legacyManagerEmails)
            ->get()
            ->each(fn (User $user) => $user->syncRoles(['manager']));
    }
}
