<?php

namespace Tests\Feature;

use App\Models\Tariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapRobotsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_sitemap_lists_public_sections_and_published_tariffs(): void
    {
        $tariff = Tariff::query()->where('slug', 'gpon-pro')->firstOrFail();

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee(url('/tariffs'), false);
        $response->assertSee(route('tariffs.show', $tariff), false);
        $response->assertSee(url('/contacts'), false);
    }

    public function test_sitemap_does_not_include_unpublished_tariffs(): void
    {
        Tariff::query()->create([
            'name' => 'Скрытый тариф',
            'slug' => 'sitemap-hidden-tariff',
            'regular_price' => 100000,
            'is_published' => false,
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertDontSee('/tariffs/sitemap-hidden-tariff', false);
    }

    public function test_robots_txt_disallows_admin_and_livewire(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $this->assertStringContainsString('Disallow: /admin', $response->getContent());
        $this->assertStringContainsString('Disallow: /livewire', $response->getContent());
        $this->assertStringContainsString(url('/sitemap.xml'), $response->getContent());
    }

    public function test_public_page_has_canonical_and_meta_description(): void
    {
        $this->get('/tariffs')
            ->assertOk()
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('<meta name="description"', false);
    }
}
