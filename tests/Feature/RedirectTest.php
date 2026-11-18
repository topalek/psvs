<?php

namespace Tests\Feature;

use App\Http\Middleware\NormalizeTrailingSlash;
use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_legacy_urls_return_permanent_redirects(): void
    {
        $this->get('/tariff')->assertStatus(301)->assertRedirect('/tariffs/');
        $this->get('/promo')->assertStatus(301)->assertRedirect('/promotions/');
        $this->get('/info')->assertStatus(301)->assertRedirect('/clients/');
        $this->get('/policy')->assertStatus(301)->assertRedirect('/privacy/');
        $this->get('/info/dokumenty/')->assertStatus(301)->assertRedirect('/clients/documents/');
    }

    public function test_cabinet_url_uses_temporary_external_redirect(): void
    {
        $this->get('/lk')->assertStatus(302)->assertRedirect('https://lk.p-svs.ru/');
    }

    public function test_trailing_slashes_are_normalized_with_permanent_redirect(): void
    {
        $middleware = new NormalizeTrailingSlash;

        $response = $middleware->handle(
            Request::create('/tariffs/', 'GET'),
            fn () => response('ok'),
        );

        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame(url('/tariffs'), $response->headers->get('Location'));

        $adminResponse = $middleware->handle(
            Request::create('/admin/login/', 'GET'),
            fn () => response('ok'),
        );

        $this->assertSame(200, $adminResponse->getStatusCode());
    }

    public function test_redirect_hit_counter_is_incremented(): void
    {
        $redirect = Redirect::query()->where('source_path', '/promo')->firstOrFail();

        $this->assertSame(0, $redirect->hits);

        $this->get('/promo');

        $this->assertSame(1, $redirect->fresh()->hits);
    }

    public function test_inactive_redirect_is_ignored(): void
    {
        Redirect::query()->create([
            'source_path' => '/old-page',
            'destination_path' => '/new-page',
            'status_code' => 301,
            'is_active' => false,
        ]);

        $this->get('/old-page')->assertNotFound();
    }

    public function test_unknown_url_returns_custom_404_page(): void
    {
        $this->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('404');
    }
}
