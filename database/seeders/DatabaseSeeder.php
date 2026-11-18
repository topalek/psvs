<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingsSeeder::class,
            CatalogSeeder::class,
            ContentSeeder::class,
            MenuSeeder::class,
            RedirectSeeder::class,
        ]);

        if (app()->environment('local', 'testing')) {
            $this->call(DemoUsersSeeder::class);
        }
    }
}
