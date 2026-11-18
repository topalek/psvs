<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'type',
        'title',
        'subtitle',
        'body',
        'data',
        'sort_order',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_enabled' => 'boolean',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /** @return array<string, string> */
    public static function types(): array
    {
        return [
            'hero' => 'Главный баннер',
            'address_check' => 'Проверка адреса',
            'featured_tariffs' => 'Популярные тарифы',
            'services' => 'Услуги и технологии',
            'benefits' => 'Преимущества',
            'steps' => 'Этапы подключения',
            'coverage' => 'Покрытие',
            'promotions' => 'Акции',
            'news' => 'Новости и работы на сети',
            'subscriber_links' => 'Быстрые ссылки абонентам',
            'contacts' => 'Контакты',
            'rich_text' => 'Текстовый блок',
        ];
    }
}
