<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\FaqCategory;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $faqCategories = FaqCategory::query()
            ->with(['faqs' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        $helpCategories = HelpCategory::query()
            ->with(['articles' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        return view('clients.index', [
            'faqCategories' => $faqCategories,
            'helpCategories' => $helpCategories,
        ]);
    }

    public function payment(): View
    {
        return view('clients.payment');
    }

    public function trustPayment(): View
    {
        return view('clients.trust-payment');
    }

    public function speedTest(): View
    {
        return view('clients.speed-test');
    }

    public function noInternet(): View
    {
        $articles = HelpArticle::query()
            ->published()
            ->whereIn('slug', ['net-interneta', 'proverit-skorost'])
            ->get();

        return view('clients.no-internet', ['articles' => $articles]);
    }

    public function router(): View
    {
        return view('clients.router');
    }

    public function documents(): View
    {
        $documents = Document::query()->published()->orderBy('sort_order')->get();

        return view('clients.documents', ['documents' => $documents]);
    }

    public function changeTariff(): View
    {
        return view('clients.change-tariff');
    }

    public function cabinet(): View
    {
        return view('clients.cabinet');
    }

    public function help(HelpArticle $article): View
    {
        abort_unless($article->is_published, 404);

        $article->load('category');

        return view('clients.help', ['article' => $article]);
    }

    public function faq(): View
    {
        $categories = FaqCategory::query()
            ->with(['faqs' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        return view('clients.faq', ['categories' => $categories]);
    }
}
