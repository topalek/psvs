<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::query()
            ->active()
            ->with('tariffs')
            ->orderBy('sort_order')
            ->paginate(12);

        return view('promotions.index', ['promotions' => $promotions]);
    }

    public function show(Promotion $promotion): View
    {
        abort_unless($promotion->isActiveNow(), 404);

        $promotion->load('tariffs.technology');

        return view('promotions.show', ['promotion' => $promotion]);
    }
}
