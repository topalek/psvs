<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapTypesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $markerTypes = [
            ['Муфта абонентская', 'Муфты', 1, 10],
            ['Муфта магистральная', 'Муфты', 0, 20],
            ['Муфта транзитная', 'Муфты', 0, 30],
            ['Запас кабеля', 'Кабельная инфраструктура', 0, 40],
            ['Опора', 'Инфраструктура', 0, 50],
            ['Кросс', 'Инфраструктура', 0, 60],
            ['Базовая станция', 'Оборудование', 0, 70],
            ['ТКД', 'Оборудование', 0, 80],
            ['Шкаф для камер', 'Оборудование', 0, 90],
            ['Ящик для камер', 'Оборудование', 0, 100],
            ['Абонентская точка', 'Абоненты', 0, 110],
            ['Прочее', 'Прочее', 0, 999],
        ];

        foreach ($markerTypes as [$name, $category, $isDefault, $sort]) {
            DB::table('marker_types')->updateOrInsert(
                ['name' => $name],
                ['category' => $category, 'is_default' => $isDefault, 'sort_order' => $sort, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $connectionTypes = [
            ['КО 1 вол', 'Оптический кабель', 10],
            ['КО 2 вол', 'Оптический кабель', 20],
            ['КО 4 вол', 'Оптический кабель', 30],
            ['КО 8 вол', 'Оптический кабель', 40],
            ['КО 16 вол', 'Оптический кабель', 50],
            ['КО 24 вол', 'Оптический кабель', 60],
            ['КО 32 вол', 'Оптический кабель', 70],
            ['КО 48 вол', 'Оптический кабель', 80],
            ['КО 64 вол', 'Оптический кабель', 90],
            ['Витая пара', 'Медный кабель', 100],
            ['МО 16 вол', 'Магистральный оптический кабель', 110],
            ['Другое', 'Прочее', 999],
        ];

        foreach ($connectionTypes as [$name, $category, $sort]) {
            DB::table('connection_types')->updateOrInsert(
                ['name' => $name],
                ['category' => $category, 'sort_order' => $sort, 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}
