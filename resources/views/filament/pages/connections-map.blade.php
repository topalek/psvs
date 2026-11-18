<x-filament-panels::page>
@php
    $mapApiKey = config('services.yandex_maps.key');
@endphp

<div id="map" style="width:100%; height:calc(100vh - 13rem); min-height:500px; border-radius:12px; overflow:hidden; background:#eef2f7;"></div>

@if (blank($mapApiKey))
    <div class="mt-3 text-sm text-danger-600">Не задан ключ Яндекс Карт. Проверьте YANDEX_MAP_API в .env и services.yandex_maps.key в config/services.php.</div>
@else
    @once
        <script src="https://api-maps.yandex.ru/v3/?apikey={{ $mapApiKey }}&lang=ru_RU"></script>
        <script>
            initMap();

            async function initMap() {
                await ymaps3.ready;

                const {YMap, YMapDefaultSchemeLayer} = ymaps3;

                const map = new YMap(
                    document.getElementById('map'),
                    {
                        location: {
                            center: [44.940183, 37.990017],
                            zoom: 20,
                        }
                    }
                );

                map.addChild(new YMapDefaultSchemeLayer());
            }
        </script>
    @endonce
@endif
</x-filament-panels::page>
