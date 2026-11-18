<?php

namespace Tests\Feature;

use App\Models\Tariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TariffPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_promo_price_is_active_inside_the_promo_window(): void
    {
        $tariff = $this->makeTariff([
            'regular_price' => 95000,
            'promo_price' => 85000,
            'promo_starts_at' => now()->subDay(),
            'promo_ends_at' => now()->addDay(),
        ]);

        $this->assertTrue($tariff->isPromoActive());
        $this->assertSame(85000, $tariff->currentPrice());
    }

    public function test_promo_price_is_not_active_before_it_starts(): void
    {
        $tariff = $this->makeTariff([
            'regular_price' => 95000,
            'promo_price' => 85000,
            'promo_starts_at' => now()->addDay(),
        ]);

        $this->assertFalse($tariff->isPromoActive());
        $this->assertSame(95000, $tariff->currentPrice());
    }

    public function test_promo_price_is_not_active_after_it_expires(): void
    {
        $tariff = $this->makeTariff([
            'regular_price' => 95000,
            'promo_price' => 85000,
            'promo_ends_at' => now()->subDay(),
        ]);

        $this->assertFalse($tariff->isPromoActive());
        $this->assertSame(95000, $tariff->currentPrice());
    }

    public function test_tariff_without_promo_price_uses_regular_price(): void
    {
        $tariff = $this->makeTariff([
            'regular_price' => 120000,
            'promo_price' => null,
        ]);

        $this->assertFalse($tariff->isPromoActive());
        $this->assertSame(120000, $tariff->currentPrice());
    }

    public function test_money_is_stored_in_minor_units_and_formatted_in_rubles(): void
    {
        $tariff = $this->makeTariff([
            'regular_price' => 95000,
            'promo_price' => 85000,
            'price_after_promo' => 95000,
        ]);

        $this->assertSame('950 ₽', $tariff->formattedRegularPrice());
        $this->assertSame('850 ₽', $tariff->formattedPromoPrice());
        $this->assertSame('950 ₽', $tariff->formattedPriceAfterPromo());
        $this->assertSame('—', Tariff::formatMoney(null));
        $this->assertSame('1 234 ₽', Tariff::formatMoney(123400));
    }

    public function test_published_scope_excludes_inactive_and_unpublished_tariffs(): void
    {
        $this->makeTariff(['slug' => 'published', 'is_published' => true, 'is_active' => true]);
        $this->makeTariff(['slug' => 'draft', 'is_published' => false, 'is_active' => true]);
        $this->makeTariff(['slug' => 'inactive', 'is_published' => true, 'is_active' => false]);

        $slugs = Tariff::query()->published()->pluck('slug')->all();

        $this->assertContains('published', $slugs);
        $this->assertNotContains('draft', $slugs);
        $this->assertNotContains('inactive', $slugs);
    }

    public function test_tariff_index_filters_by_customer_type(): void
    {
        $this->makeTariff([
            'slug' => 'residential-tariff',
            'name' => 'Домашний',
            'customer_type' => 'residential',
            'is_published' => true,
            'is_active' => true,
        ]);

        $this->makeTariff([
            'slug' => 'business-tariff',
            'name' => 'Бизнес',
            'customer_type' => 'business',
            'is_published' => true,
            'is_active' => true,
        ]);

        $this->get('/tariffs?customer_type=residential')
            ->assertOk()
            ->assertSee('Домашний')
            ->assertDontSee('Бизнес');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeTariff(array $attributes = []): Tariff
    {
        return Tariff::query()->create(array_merge([
            'name' => 'Тестовый тариф',
            'slug' => 'test-tariff-'.uniqid(),
            'regular_price' => 95000,
            'is_published' => true,
            'is_active' => true,
        ], $attributes));
    }
}
