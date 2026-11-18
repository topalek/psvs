<x-filament-panels::page>
    @php
        $mapApiKey = config('services.yandex_maps.key');
        $mapCenter = [44.940183, 37.990017];
        $mapZoom = 16;
    @endphp

    <div
        id="network-map-app"
        data-api-key="{{ $mapApiKey }}"
        data-center="{{ implode(',', $mapCenter) }}"
        data-zoom="{{ $mapZoom }}"
    ></div>

    <script type="application/json" id="network-map-data">{!! $areas->toJson(JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

    @if (filled($mapApiKey))
        @once
            <script src="https://api-maps.yandex.ru/v3/?apikey={{ $mapApiKey }}&lang=ru_RU"></script>
        @endonce
    @endif

    @vite('resources/js/map/main.js')
</x-filament-panels::page>
