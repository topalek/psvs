<?php

namespace App\Http\Controllers;

use App\Enums\CustomerType;
use App\Models\Area;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\Tariff;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TariffController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tariff::query()
            ->published()
            ->with(['technology', 'areas', 'services'])
            ->orderBy('sort_order');

        if ($request->filled('technology')) {
            $query->whereHas('technology', fn ($q) => $q->where('slug', $request->string('technology')));
        }

        if ($request->filled('area')) {
            $query->whereHas('areas', fn ($q) => $q->where('slug', $request->string('area')));
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->string('customer_type'));
        }

        if ($request->filled('service')) {
            $query->whereHas('services', fn ($q) => $q->where('slug', $request->string('service')));
        }

        if ($request->filled('max_price') && is_numeric($request->input('max_price'))) {
            $query->where('regular_price', '<=', (int) $request->input('max_price') * 100);
        }

        $tariffs = $query->paginate(12)->withQueryString();

        $technologies = Technology::query()
            ->where('is_active', true)
            ->whereHas('tariffs', fn ($q) => $q->published())
            ->orderBy('sort_order')
            ->get();

        $areas = Area::query()
            ->published()
            ->whereHas('tariffs', fn ($q) => $q->published())
            ->orderBy('sort_order')
            ->get();

        $services = Service::query()
            ->where('is_active', true)
            ->whereHas('tariffs', fn ($q) => $q->published())
            ->orderBy('sort_order')
            ->get();

        return view('tariffs.index', [
            'tariffs' => $tariffs,
            'technologies' => $technologies,
            'areas' => $areas,
            'services' => $services,
            'customerTypes' => CustomerType::options(),
        ]);
    }

    public function show(Tariff $tariff): View
    {
        abort_unless($tariff->is_published && $tariff->is_active, 404);

        $tariff->load(['technology', 'areas', 'services']);

        $related = Tariff::query()
            ->published()
            ->where('id', '!=', $tariff->id)
            ->where('technology_id', $tariff->technology_id)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        $promotions = Promotion::query()
            ->active()
            ->whereHas('tariffs', fn ($q) => $q->where('tariffs.id', $tariff->id))
            ->get();

        return view('tariffs.show', [
            'tariff' => $tariff,
            'related' => $related,
            'promotions' => $promotions,
        ]);
    }
}
