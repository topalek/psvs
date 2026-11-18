<?php

namespace Database\Seeders;

use App\Models\ContactChannel;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['company_name', 'Сити Телеком Сервис', 'general', 'string'],
            ['company_legal_name', 'ИП Месяченко В.М.', 'general', 'string'],
            ['company_inn', '233701824662', 'general', 'string'],
            ['company_ogrnip', '323237500360851', 'general', 'string'],
            ['company_address', 'г. Крымск, ул. Фадеева, д. 64/1', 'contacts', 'string'],
            ['office_hours', 'Ежедневно с 09:00 до 21:00', 'contacts', 'string'],
            ['support_hours', 'Уточните у оператора', 'contacts', 'string'],
            ['cabinet_url', 'https://lk.p-svs.ru/', 'integrations', 'string'],
            ['payment_url', '', 'integrations', 'string'],
            ['map_url', '', 'integrations', 'string'],
            ['default_seo_title', 'Сити Телеком Сервис — интернет в Крымске и Абинском районе', 'seo', 'string'],
            ['default_seo_description', 'Домашний интернет по технологиям GPON и WiMAX, тарифы, акции и поддержка абонентов.', 'seo', 'string'],
            ['header_cta_label', 'Проверить подключение', 'header', 'string'],
            ['header_cta_url', '/#check', 'header', 'string'],
            ['consent_version', '2026-11-01', 'legal', 'string'],
            ['consent_text_url', '/personal-data-consent/', 'legal', 'string'],
            ['footer_note', 'Информация на сайте не является публичной офертой. Условия подключения уточняйте у оператора.', 'footer', 'string'],
            ['analytics_yandex_id', '', 'integrations', 'string'],
            ['analytics_google_id', '', 'integrations', 'string'],
        ];

        foreach ($settings as [$key, $value, $group, $type]) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group, 'type' => $type, 'is_public' => true],
            );
        }

        ContactChannel::query()->delete();

        ContactChannel::query()->create([
            'type' => 'phone',
            'label' => 'Офис',
            'value' => '8 (918) 066-08-33',
            'sort_order' => 1,
        ]);

        ContactChannel::query()->create([
            'type' => 'phone',
            'label' => 'Подключение',
            'value' => '8 (918) 493-41-88',
            'sort_order' => 2,
        ]);

        ContactChannel::query()->create([
            'type' => 'email',
            'label' => 'E-mail',
            'value' => 'info@p-svs.ru',
            'sort_order' => 3,
        ]);

        SiteSetting::forgetCache();
    }
}
