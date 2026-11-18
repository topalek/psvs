<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()->published()->where('slug', $slug)->firstOrFail();

        return view('pages.show', ['page' => $page]);
    }

    public function internet(): View
    {
        return $this->show('internet');
    }

    public function business(): View
    {
        return $this->show('business');
    }

    public function about(): View
    {
        return $this->show('about');
    }
}
