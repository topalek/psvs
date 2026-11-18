<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Page;
use App\Models\Post;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\Slide;
use App\Models\Slider;
use App\Models\Tariff;
use App\Models\Technology;
use App\Support\Contacts;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $page = Page::query()->published()->where('slug', 'home')->firstOrFail();
        $sections = $page->sections()->where('is_enabled', true)->get();

        $slider = Slider::query()->where('is_active', true)->where('slug', 'home-hero')->first();
        $slides = $slider ? Slide::query()->visible()->where('slider_id', $slider->id)->get() : collect();

        $featuredTariffs = Tariff::query()
            ->published()
            ->with(['technology', 'areas', 'services'])
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        if ($featuredTariffs->isEmpty()) {
            $featuredTariffs = Tariff::query()
                ->published()
                ->with(['technology', 'areas', 'services'])
                ->orderBy('sort_order')
                ->limit(3)
                ->get();
        }

        $promotions = Promotion::query()->active()->orderBy('sort_order')->limit(3)->get();

        $news = Post::query()->published()->orderByDesc('published_at')->limit(6)->get();

        $operational = Post::query()
            ->published()
            ->operational()
            ->whereIn('incident_status', ['planned', 'in_progress'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $areas = Area::query()->published()->orderBy('sort_order')->get();

        $technologies = Technology::query()->where('is_active', true)->orderBy('sort_order')->get();
        $services = Service::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.home', [
            'page' => $page,
            'sections' => $sections,
            'slides' => $slides,
            'featuredTariffs' => $featuredTariffs,
            'promotions' => $promotions,
            'news' => $news,
            'operational' => $operational,
            'areas' => $areas,
            'technologies' => $technologies,
            'services' => $services,
            'contacts' => Contacts::class,
        ]);
    }
}
