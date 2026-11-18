<?php

namespace Database\Seeders;

use App\Enums\CustomerType;
use App\Models\Area;
use App\Models\Service;
use App\Models\Tariff;
use App\Models\Technology;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $gpon = Technology::query()->updateOrCreate(
            ['slug' => 'gpon'],
            [
                'name' => 'GPON',
                'short_description' => 'Оптоволоконная линия до квартиры',
                'description' => 'GPON (Gigabit Passive Optical Network) — технология пассивных оптических сетей. Интернет поступает в дом по оптоволоконной линии.',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        $wimax = Technology::query()->updateOrCreate(
            ['slug' => 'wimax'],
            [
                'name' => 'WiMAX',
                'short_description' => 'Беспроводное подключение',
                'description' => 'WiMAX — телекоммуникационная технология для предоставления беспроводной связи на больших расстояниях.',
                'is_active' => true,
                'sort_order' => 2,
            ],
        );

        $iptv = Service::query()->updateOrCreate(
            ['slug' => 'iptv'],
            [
                'name' => 'IPTV',
                'short_description' => 'Цифровое интерактивное телевидение',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        $krymsk = Area::query()->updateOrCreate(
            ['slug' => 'krymsk'],
            [
                'name' => 'Крымск',
                'type' => 'locality',
                'district' => 'Крымский район',
                'verification_status' => 'unverified',
                'is_published' => true,
                'sort_order' => 1,
                'internal_notes' => 'Проверить актуальный перечень улиц и домов перед публикацией.',
            ],
        );

        $abinsk = Area::query()->updateOrCreate(
            ['slug' => 'abinsk'],
            [
                'name' => 'Абинск',
                'type' => 'locality',
                'district' => 'Абинский район',
                'verification_status' => 'unverified',
                'is_published' => true,
                'sort_order' => 2,
                'internal_notes' => 'Проверить актуальный перечень улиц и домов перед публикацией.',
            ],
        );

        $erivanskaya = Area::query()->updateOrCreate(
            ['slug' => 'erivanskaya'],
            [
                'name' => 'хутор Эриванский',
                'type' => 'settlement',
                'district' => 'Абинский район',
                'verification_status' => 'unverified',
                'is_published' => true,
                'sort_order' => 3,
            ],
        );

        $svetlogorskoye = Area::query()->updateOrCreate(
            ['slug' => 'svetlogorskoye'],
            [
                'name' => 'село Светлогорское',
                'type' => 'settlement',
                'district' => 'Абинский район',
                'verification_status' => 'unverified',
                'is_published' => true,
                'sort_order' => 4,
            ],
        );

        $tariffs = [
            ['gpon-start', 'Старт', $gpon, 100, 95000, 85000, 'hit', true, [$krymsk]],
            ['gpon-pro', 'Профи', $gpon, 300, 120000, 105000, 'хит', true, [$krymsk]],
            ['gpon-ultra', 'Ультра', $gpon, 500, 180000, 160000, null, false, [$krymsk]],
            ['gpon-start-district', 'Старт — район', $gpon, 100, 105000, 95000, null, false, [$abinsk, $erivanskaya, $svetlogorskoye]],
            ['gpon-pro-district', 'Профи — район', $gpon, 300, 130000, 115000, null, false, [$abinsk, $erivanskaya, $svetlogorskoye]],
            ['gpon-ultra-district', 'Ультра — район', $gpon, 500, 190000, 170000, null, false, [$abinsk, $erivanskaya, $svetlogorskoye]],
            ['wimax-start', 'Старт (WiMAX)', $wimax, 10, 95000, 85000, null, false, [$erivanskaya, $svetlogorskoye]],
            ['wimax-pro', 'Профи (WiMAX)', $wimax, 16, 120000, 105000, null, false, [$erivanskaya, $svetlogorskoye]],
            ['wimax-start-district', 'Старт — район (WiMAX)', $wimax, 10, 105000, 95000, null, false, [$abinsk, $erivanskaya, $svetlogorskoye]],
            ['wimax-pro-district', 'Профи — район (WiMAX)', $wimax, 16, 130000, 115000, null, false, [$abinsk, $erivanskaya, $svetlogorskoye]],
        ];

        foreach ($tariffs as $index => [$slug, $name, $technology, $speed, $regular, $promo, $badge, $featured, $areas]) {
            $tariff = Tariff::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'technology_id' => $technology->id,
                    'name' => $name,
                    'customer_type' => CustomerType::Residential,
                    'speed_mbps' => $speed,
                    'regular_price' => $regular,
                    'promo_price' => $promo,
                    'price_period_days' => 30,
                    'price_after_promo' => $regular,
                    'promo_starts_at' => now()->subMonth(),
                    'promo_ends_at' => null,
                    'included_services' => ['IPTV'],
                    'terms' => 'Цена указана за 30 календарных дней. Условия акции и доступность адреса требуют подтверждения оператором.',
                    'badge' => $badge,
                    'is_featured' => $featured,
                    'is_active' => true,
                    'is_published' => true,
                    'effective_date' => now()->toDateString(),
                    'last_verified_at' => null,
                    'sort_order' => $index + 1,
                ],
            );

            $tariff->areas()->sync(collect($areas)->pluck('id')->all());
            $tariff->services()->sync([$iptv->id]);
        }
    }
}
