<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TariffController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/internet', [PageController::class, 'internet'])->name('internet');
Route::get('/business', [PageController::class, 'business'])->name('business');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/tariffs', [TariffController::class, 'index'])->name('tariffs.index');
Route::get('/tariffs/{tariff:slug}', [TariffController::class, 'show'])->name('tariffs.show');

Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
Route::get('/promotions/{promotion:slug}', [PromotionController::class, 'show'])->name('promotions.show');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{post:slug}', [NewsController::class, 'show'])->name('news.show');

Route::prefix('clients')->name('clients.')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('index');
    Route::get('/payment', [ClientController::class, 'payment'])->name('payment');
    Route::get('/trust-payment', [ClientController::class, 'trustPayment'])->name('trust-payment');
    Route::get('/speed-test', [ClientController::class, 'speedTest'])->name('speed-test');
    Route::get('/no-internet', [ClientController::class, 'noInternet'])->name('no-internet');
    Route::get('/router', [ClientController::class, 'router'])->name('router');
    Route::get('/documents', [ClientController::class, 'documents'])->name('documents');
    Route::get('/change-tariff', [ClientController::class, 'changeTariff'])->name('change-tariff');
    Route::get('/cabinet', [ClientController::class, 'cabinet'])->name('cabinet');
    Route::get('/faq', [ClientController::class, 'faq'])->name('faq');
    Route::get('/help/{article:slug}', [ClientController::class, 'help'])->name('help.show');
});

Route::get('/contacts', ContactController::class)->name('contacts');

Route::get('/privacy', [PageController::class, 'show'])->defaults('slug', 'privacy')->name('privacy');
Route::get('/personal-data-consent', [PageController::class, 'show'])->defaults('slug', 'personal-data-consent')->name('personal-data-consent');
Route::get('/terms', [PageController::class, 'show'])->defaults('slug', 'terms')->name('terms');

Route::post('/leads', [LeadController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('leads.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\nDisallow: /admin\nDisallow: /livewire\n\nSitemap: " . url('/sitemap.xml') . "\n",
        200,
        ['Content-Type' => 'text/plain'],
    );
})->name('robots');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
