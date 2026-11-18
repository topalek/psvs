<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\PostType;
use App\Models\FaqCategory;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\Page;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Tariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_all_main_public_pages_render_successfully(): void
    {
        $urls = [
            '/',
            '/internet',
            '/business',
            '/about',
            '/tariffs',
            '/promotions',
            '/news',
            '/clients',
            '/clients/payment',
            '/clients/trust-payment',
            '/clients/speed-test',
            '/clients/no-internet',
            '/clients/router',
            '/clients/documents',
            '/clients/change-tariff',
            '/clients/cabinet',
            '/clients/faq',
            '/contacts',
            '/privacy',
            '/personal-data-consent',
            '/terms',
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_tariff_detail_page_renders_seeded_tariff(): void
    {
        $tariff = Tariff::query()->where('slug', 'gpon-pro')->firstOrFail();

        $this->get('/tariffs/'.$tariff->slug)
            ->assertOk()
            ->assertSee($tariff->name)
            ->assertSee('GPON');
    }

    public function test_news_detail_page_renders_published_post(): void
    {
        $this->get('/news/teper-my-v-max')
            ->assertOk()
            ->assertSee('Теперь мы в MAX');
    }

    public function test_unpublished_and_draft_content_is_not_publicly_visible(): void
    {
        Page::query()->create([
            'title' => 'Черновик',
            'slug' => 'draft-page',
            'status' => ContentStatus::Draft,
        ]);

        Tariff::query()->create([
            'name' => 'Скрытый тариф',
            'slug' => 'hidden-tariff',
            'regular_price' => 100000,
            'is_published' => false,
        ]);

        Promotion::query()->create([
            'title' => 'Истёкшая акция',
            'slug' => 'expired-promotion',
            'status' => ContentStatus::Published,
            'published_at' => now()->subMonth(),
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->subDay(),
            'is_indefinite' => false,
        ]);

        Post::query()->create([
            'title' => 'Запланированная новость',
            'slug' => 'scheduled-post',
            'type' => PostType::CompanyNews,
            'status' => ContentStatus::Published,
            'published_at' => now()->addWeek(),
        ]);

        Post::query()->create([
            'title' => 'Снятая с публикации новость',
            'slug' => 'unpublished-post',
            'type' => PostType::CompanyNews,
            'status' => ContentStatus::Published,
            'published_at' => now()->subWeek(),
            'unpublish_at' => now()->subDay(),
        ]);

        $category = HelpCategory::query()->create(['name' => 'Тест', 'slug' => 'test-help']);
        HelpArticle::query()->create([
            'help_category_id' => $category->id,
            'title' => 'Скрытая инструкция',
            'slug' => 'hidden-help',
            'body' => 'Текст',
            'is_published' => false,
        ]);

        $this->get('/draft-page')->assertNotFound();
        $this->get('/tariffs/hidden-tariff')->assertNotFound();
        $this->get('/promotions/expired-promotion')->assertNotFound();
        $this->get('/news/scheduled-post')->assertNotFound();
        $this->get('/news/unpublished-post')->assertNotFound();
        $this->get('/clients/help/hidden-help')->assertNotFound();
    }

    public function test_home_page_hides_unpublished_news_and_tariffs(): void
    {
        Tariff::query()->create([
            'name' => 'Скрытый тариф на главной',
            'slug' => 'hidden-home-tariff',
            'regular_price' => 100000,
            'is_featured' => true,
            'is_published' => false,
        ]);

        Post::query()->create([
            'title' => 'Скрытая новость на главной',
            'slug' => 'hidden-home-post',
            'type' => PostType::CompanyNews,
            'status' => ContentStatus::Draft,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Скрытый тариф на главной')
            ->assertDontSee('Скрытая новость на главной');
    }

    public function test_faq_page_lists_only_published_answers(): void
    {
        $category = FaqCategory::query()->firstOrFail();

        $category->faqs()->create([
            'question' => 'Опубликованный вопрос?',
            'answer' => 'Да.',
            'is_published' => true,
        ]);

        $category->faqs()->create([
            'question' => 'Черновой вопрос?',
            'answer' => 'Нет.',
            'is_published' => false,
        ]);

        $this->get('/clients/faq')
            ->assertOk()
            ->assertSee('Опубликованный вопрос?')
            ->assertDontSee('Черновой вопрос?');
    }

    public function test_lead_form_type_values_are_rendered_on_contacts_page(): void
    {
        $this->get('/contacts')
            ->assertOk()
            ->assertSee('name="type" value="contact"', false);
    }
}
