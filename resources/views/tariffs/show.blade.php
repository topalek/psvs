@extends('layouts.app')

@section('title', $tariff->name.' — тариф — '.$companyName)
@section('meta_description', 'Тариф «'.$tariff->name.'»: '.($tariff->speed_mbps ? $tariff->speed_mbps.' Мбит/с, ' : '').$tariff->formattedRegularPrice().' за '.$tariff->price_period_days.' дней. Условия и доступность уточняйте у оператора.')
@section('canonical', route('tariffs.show', $tariff))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('tariffs.index') }}" class="hover:text-brand-700">Тарифы</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">{{ $tariff->name }}</li>
                </ol>
            </nav>
            <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-brand-500">{{ $tariff->technology?->name ?? 'Интернет' }}</p>
                    <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">{{ $tariff->name }}</h1>
                    @if ($tariff->badge)
                        <span class="mt-2 inline-flex rounded-full bg-accent-500 px-3 py-1 text-xs font-bold text-white">{{ $tariff->badge }}</span>
                    @endif
                </div>
                <a href="{{ route('coverage', ['tariff' => $tariff->id]) }}" class="btn-primary">Подключить тариф</a>
            </div>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[1fr_380px] lg:items-start">
        <div class="grid gap-6">
            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Стоимость и условия</h2>
                @if ($tariff->isPromoActive())
                    <div class="mt-4 flex flex-wrap items-end gap-3">
                        <p class="text-sm text-slate-500"><s>{{ $tariff->formattedRegularPrice() }}</s></p>
                        <p class="text-4xl font-extrabold tracking-tight text-brand-900">{{ $tariff->formattedPromoPrice() }}</p>
                        <p class="pb-1 text-sm text-slate-500">за {{ $tariff->price_period_days }} календарных дней</p>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">
                        Акционная цена.
                        @if ($tariff->promo_starts_at && $tariff->promo_ends_at)
                            Период акции: {{ $tariff->promo_starts_at->translatedFormat('j F Y') }} — {{ $tariff->promo_ends_at->translatedFormat('j F Y') }}.
                        @elseif ($tariff->promo_ends_at)
                            Акция действует до {{ $tariff->promo_ends_at->translatedFormat('j F Y') }}.
                        @else
                            Срок действия акции уточняйте у оператора.
                        @endif
                        После акции — {{ $tariff->formattedPriceAfterPromo() ?? $tariff->formattedRegularPrice() }} за {{ $tariff->price_period_days }} дней.
                    </p>
                @else
                    <div class="mt-4 flex flex-wrap items-end gap-3">
                        <p class="text-4xl font-extrabold tracking-tight text-brand-900">{{ $tariff->formattedRegularPrice() }}</p>
                        <p class="pb-1 text-sm text-slate-500">за {{ $tariff->price_period_days }} календарных дней</p>
                    </div>
                @endif

                <dl class="mt-6 grid gap-3 border-t border-slate-100 pt-5 text-sm sm:grid-cols-2">
                    @if ($tariff->speed_mbps)
                        <div><dt class="text-slate-500">Скорость</dt><dd class="font-semibold text-brand-900">до {{ $tariff->speed_mbps }} Мбит/с</dd></div>
                    @endif
                    <div><dt class="text-slate-500">Технология</dt><dd class="font-semibold text-brand-900">{{ $tariff->technology?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-500">Для кого</dt><dd class="font-semibold text-brand-900">{{ $tariff->customer_type->label() }}</dd></div>
                    <div><dt class="text-slate-500">Период оплаты</dt><dd class="font-semibold text-brand-900">{{ $tariff->price_period_days }} календарных дней</dd></div>
                    @if ($tariff->connection_fee !== null)
                        <div><dt class="text-slate-500">Подключение</dt><dd class="font-semibold text-brand-900">{{ \App\Models\Tariff::formatMoney($tariff->connection_fee) }}</dd></div>
                    @endif
                    @if ($tariff->equipment_fee !== null)
                        <div><dt class="text-slate-500">Оборудование</dt><dd class="font-semibold text-brand-900">{{ \App\Models\Tariff::formatMoney($tariff->equipment_fee) }}</dd></div>
                    @endif
                </dl>

                @if ($tariff->equipment_info)
                    <p class="mt-4 text-sm leading-6 text-slate-600">{{ $tariff->equipment_info }}</p>
                @endif
            </div>

            @if ($tariff->included_services || $tariff->services->isNotEmpty())
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Включено в тариф</h2>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach ($tariff->included_services ?? [] as $service)
                            <li class="badge">{{ $service }}</li>
                        @endforeach
                        @foreach ($tariff->services as $service)
                            @if (! in_array($service->name, $tariff->included_services ?? [], true))
                                <li class="badge">{{ $service->name }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($tariff->areas->isNotEmpty())
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Доступность</h2>
                    <p class="mt-2 text-sm text-slate-600">Тариф заявлен для населённых пунктов:</p>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach ($tariff->areas as $area)
                            <li class="badge">{{ $area->name }}</li>
                        @endforeach
                    </ul>
                    <p class="mt-3 text-xs leading-5 text-slate-500">Наличие технической возможности по конкретному адресу подтверждает специалист. Список населённых пунктов не гарантирует подключение.</p>
                </div>
            @endif

            @if ($tariff->terms)
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Условия</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $tariff->terms }}</p>
                </div>
            @endif

            @if ($promotions->isNotEmpty())
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Действующие акции</h2>
                    <ul class="mt-3 grid gap-2">
                        @foreach ($promotions as $promotion)
                            <li>
                                <a href="{{ route('promotions.show', $promotion) }}" class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm hover:border-accent-300">
                                    <span class="font-medium text-brand-900">{{ $promotion->title }}</span>
                                    <span class="text-brand-600">Подробнее →</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <aside class="grid gap-5 lg:sticky lg:top-28">
            @include('partials.lead-form', [
                'formType' => 'connection',
                'formTitle' => 'Подключить тариф',
                'formSubtitle' => 'Оставьте заявку — оператор уточнит доступность адреса и условия подключения.',
                'formShowAddress' => true,
                'formSelectedTariff' => $tariff,
                'formButtonLabel' => 'Оставить заявку',
            ])
        </aside>
    </div>

    @if ($related->isNotEmpty())
        <section class="bg-white py-12">
            <div class="container-site">
                <h2 class="section-title">Другие тарифы</h2>
                <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        @include('partials.tariff-card', ['tariff' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
