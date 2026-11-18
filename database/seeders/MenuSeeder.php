<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $main = Menu::query()->updateOrCreate(
            ['slug' => 'main'],
            ['name' => 'Главное меню', 'location' => 'header', 'is_active' => true],
        );

        $mainItems = [
            ['Интернет для дома', '/internet/'],
            ['Интернет для бизнеса', '/business/'],
            ['Тарифы', '/tariffs/'],
            ['Акции', '/promotions/'],
            ['Абонентам', '/clients/'],
            ['Новости', '/news/'],
            ['Контакты', '/contacts/'],
        ];

        $main->allItems()->delete();

        foreach ($mainItems as $i => [$label, $url]) {
            MenuItem::query()->create([
                'menu_id' => $main->id,
                'label' => $label,
                'url' => $url,
                'sort_order' => $i + 1,
                'is_active' => true,
            ]);
        }

        $footer = Menu::query()->updateOrCreate(
            ['slug' => 'footer'],
            ['name' => 'Меню в подвале', 'location' => 'footer', 'is_active' => true],
        );

        $footerItems = [
            ['О компании', '/about/'],
            ['Тарифы', '/tariffs/'],
            ['Акции', '/promotions/'],
            ['Абонентам', '/clients/'],
            ['Документы', '/clients/documents/'],
            ['Новости', '/news/'],
            ['Контакты', '/contacts/'],
            ['Политика обработки персональных данных', '/privacy/'],
            ['Согласие на обработку персональных данных', '/personal-data-consent/'],
            ['Условия оказания услуг', '/terms/'],
        ];

        $footer->allItems()->delete();

        foreach ($footerItems as $i => [$label, $url]) {
            MenuItem::query()->create([
                'menu_id' => $footer->id,
                'label' => $label,
                'url' => $url,
                'sort_order' => $i + 1,
                'is_active' => true,
            ]);
        }
    }
}
