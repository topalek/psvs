@php
    $promoActive = $tariff->isPromoActive();
    $areas = $tariff->areas->take(3)->pluck('name')->implode(', ');
@endphp
<article class="card flex h-full flex-col overflow-hidden">
    <div class="flex items-start justify-between gap-3 border-b border-slate-100 bg-slate-50/70 px-5 py-4">
        <div>
            <p class="text-xs font-semibold tracking-wide text-brand-500 uppercase">{{ $tariff->technology?->name ?? 'Интернет' }}</p>
            <h3 class="mt-1 text-lg font-bold text-brand-900">{{ $tariff->name }}</h3>
        </div>
        @if ($tariff->badge)
            <span class="rounded-full bg-accent-500 px-2.5 py-1 text-[11px] font-bold text-white">{{ $tariff->badge }}</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        @if ($tariff->speed_mbps)
            <p class="text-3xl font-extrabold tracking-tight text-brand-900">
                {{ $tariff->speed_mbps }} <span class="text-base font-semibold text-slate-500">Мбит/с</span>
            </p>
        @endif

        @if ($tariff->included_services || $tariff->services->isNotEmpty())
            <ul class="mt-3 flex flex-wrap gap-1.5">
                @foreach ($tariff->included_services ?? [] as $service)
                    <li class="badge">{{ $service }}</li>
                @endforeach
                @foreach ($tariff->services as $service)
                    @if (! in_array($service->name, $tariff->included_services ?? [], true))
                        <li class="badge">{{ $service->name }}</li>
                    @endif
                @endforeach
            </ul>
        @endif

        <div class="mt-4">
            @if ($promoActive)
                <p class="text-sm text-slate-500">
                    <s>{{ $tariff->formattedRegularPrice() }}</s>
                    <span class="ml-1 rounded bg-emerald-50 px-1.5 py-0.5 text-xs font-semibold text-emerald-700">акция</span>
                </p>
                <p class="mt-1 text-3xl font-extrabold tracking-tight text-brand-900">
                    {{ $tariff->formattedPromoPrice() }}
                    <span class="text-sm font-medium text-slate-500">/ {{ $tariff->price_period_days }} дней</span>
                </p>
                <p class="mt-1 text-xs text-slate-500">После акции — {{ $tariff->formattedPriceAfterPromo() ?? $tariff->formattedRegularPrice() }} за {{ $tariff->price_period_days }} дней</p>
            @else
                <p class="text-3xl font-extrabold tracking-tight text-brand-900">
                    {{ $tariff->formattedRegularPrice() }}
                    <span class="text-sm font-medium text-slate-500">/ {{ $tariff->price_period_days }} дней</span>
                </p>
            @endif
        </div>

        @if ($areas)
            <p class="mt-3 text-xs leading-5 text-slate-500">Доступен: {{ $areas }}</p>
        @endif

        <div class="mt-auto grid gap-2 pt-5">
            <a href="{{ route('coverage', ['tariff' => $tariff->id]) }}" class="btn-primary w-full">Подключить</a>
            <a href="{{ route('tariffs.show', $tariff) }}" class="btn-secondary w-full">Подробнее о тарифе</a>
        </div>
    </div>
</article>
