<?php

namespace Database\Seeders;

use App\Models\Redirect;
use Illuminate\Database\Seeder;

class RedirectSeeder extends Seeder
{
    public function run(): void
    {
        $redirects = [
            ['/tariff/', '/tariffs/', 301],
            ['/tariff', '/tariffs/', 301],
            ['/promo/', '/promotions/', 301],
            ['/promo', '/promotions/', 301],
            ['/info/', '/clients/', 301],
            ['/info', '/clients/', 301],
            ['/policy/', '/privacy/', 301],
            ['/policy', '/privacy/', 301],
            ['/info/pamyatka-abonenta/', '/clients/', 301],
            ['/info/instruktsiya-po-oplate-na-sayte/', '/clients/payment/', 301],
            ['/info/instruktsiya-po-oplate-v-banke/', '/clients/payment/', 301],
            ['/info/dokumenty/', '/clients/documents/', 301],
            ['/info/doveritelnyy-platezh/', '/clients/trust-payment/', 301],
            ['/info/proverit-skorost-interneta/', '/clients/speed-test/', 301],
            ['/lk/', 'https://lk.p-svs.ru/', 302],
            ['/lk', 'https://lk.p-svs.ru/', 302],
        ];

        foreach ($redirects as [$source, $destination, $status]) {
            Redirect::query()->updateOrCreate(
                ['source_path' => $source],
                ['destination_path' => $destination, 'status_code' => $status, 'is_active' => true],
            );
        }
    }
}
