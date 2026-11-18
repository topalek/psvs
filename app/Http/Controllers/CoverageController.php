<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\View\View;

class CoverageController extends Controller
{
    public function index(): View
    {
        $areas = Area::query()->published()->orderBy('sort_order')->get();

        return view('coverage.index', ['areas' => $areas]);
    }
}
