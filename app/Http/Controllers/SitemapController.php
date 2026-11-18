<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Tariff;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => url('/'), 'lastmod' => now()],
            ['loc' => url('/internet'), 'lastmod' => now()],
            ['loc' => url('/business'), 'lastmod' => now()],
            ['loc' => url('/tariffs'), 'lastmod' => now()],
            ['loc' => url('/coverage'), 'lastmod' => now()],
            ['loc' => url('/promotions'), 'lastmod' => now()],
            ['loc' => url('/clients'), 'lastmod' => now()],
            ['loc' => url('/clients/payment'), 'lastmod' => now()],
            ['loc' => url('/clients/trust-payment'), 'lastmod' => now()],
            ['loc' => url('/clients/speed-test'), 'lastmod' => now()],
            ['loc' => url('/clients/no-internet'), 'lastmod' => now()],
            ['loc' => url('/clients/router'), 'lastmod' => now()],
            ['loc' => url('/clients/documents'), 'lastmod' => now()],
            ['loc' => url('/clients/change-tariff'), 'lastmod' => now()],
            ['loc' => url('/clients/cabinet'), 'lastmod' => now()],
            ['loc' => url('/news'), 'lastmod' => now()],
            ['loc' => url('/about'), 'lastmod' => now()],
            ['loc' => url('/contacts'), 'lastmod' => now()],
            ['loc' => url('/privacy'), 'lastmod' => now()],
            ['loc' => url('/personal-data-consent'), 'lastmod' => now()],
            ['loc' => url('/terms'), 'lastmod' => now()],
        ]);

        Tariff::query()->published()->get()->each(function (Tariff $tariff) use ($urls) {
            $urls->push(['loc' => route('tariffs.show', $tariff), 'lastmod' => $tariff->updated_at]);
        });

        Promotion::query()->active()->get()->each(function (Promotion $promotion) use ($urls) {
            $urls->push(['loc' => route('promotions.show', $promotion), 'lastmod' => $promotion->updated_at]);
        });

        Post::query()->published()->get()->each(function (Post $post) use ($urls) {
            $urls->push(['loc' => route('news.show', $post), 'lastmod' => $post->updated_at]);
        });

        Page::query()->published()->whereNotIn('slug', ['home'])->get()->each(function (Page $page) use ($urls) {
            if (in_array($page->slug, ['internet', 'business', 'about', 'privacy', 'personal-data-consent', 'terms'], true)) {
                return;
            }

            $urls->push(['loc' => url('/'.$page->slug), 'lastmod' => $page->updated_at]);
        });

        return response()->view('sitemap', ['urls' => $urls->unique('loc')->values()])
            ->header('Content-Type', 'application/xml');
    }
}
