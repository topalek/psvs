@extends('layouts.app')

@section('title', $page->seo_title ?: $page->title)
@section('meta_description', $page->seo_description ?? '')
@section('canonical', $page->canonical_url ?: url('/'))

@section('content')
    @php
        $hero = $sections->firstWhere('type', 'hero');
        $addressSection = $sections->firstWhere('type', 'address_check');
        $featuredSection = $sections->firstWhere('type', 'featured_tariffs');
        $servicesSection = $sections->firstWhere('type', 'services');
        $benefitsSection = $sections->firstWhere('type', 'benefits');
        $stepsSection = $sections->firstWhere('type', 'steps');
        $coverageSection = $sections->firstWhere('type', 'coverage');
        $promotionsSection = $sections->firstWhere('type', 'promotions');
        $newsSection = $sections->firstWhere('type', 'news');
        $subscriberSection = $sections->firstWhere('type', 'subscriber_links');
        $contactsSection = $sections->firstWhere('type', 'contacts');

        $heroSlides = $slides->map(fn ($slide) => [
            'headline' => $slide->headline ?: $hero?->title,
            'subtitle' => $slide->subtitle ?: $hero?->subtitle,
            'cta_label' => $slide->cta_label ?: 'Проверить подключение',
            'cta_url' => $slide->cta_url ?: '#check',
            'image' => $slide->getFirstMediaUrl('desktop_image')
                ?: $slide->getFirstMediaUrl('mobile_image')
                ?: asset('images/home.jpg'),
            'mobile_image' => $slide->getFirstMediaUrl('mobile_image') ?: null,
            'alt' => $slide->alt_text ?: ($slide->headline ?: $slide->name),
        ])->values();

        if ($heroSlides->isEmpty()) {
            $heroSlides = collect([[
                'headline' => $hero?->title,
                'subtitle' => $hero?->subtitle,
                'cta_label' => 'Проверить подключение',
                'cta_url' => '#check',
                'image' => asset('images/home.jpg'),
                'mobile_image' => null,
                'alt' => 'Домашний интернет Сити Телеком Сервис',
            ]]);
        }
    @endphp

    @if ($hero)
        <section class="home-hero relative isolate overflow-hidden bg-brand-100 text-brand-950">
            <div class="swiper home-hero-swiper" data-home-hero aria-label="Главный баннер">
                <div class="swiper-wrapper">
                    @foreach ($heroSlides as $index => $heroSlide)
                        <div class="swiper-slide relative flex min-h-[560px] items-center lg:min-h-[600px]">
                            <picture class="absolute inset-0 -z-20">
                                @if ($heroSlide['mobile_image'])
                                    <source media="(max-width: 1023px)" srcset="{{ $heroSlide['mobile_image'] }}">
                                @endif
                                <img
                                    src="{{ $heroSlide['image'] }}"
                                    alt="{{ $heroSlide['alt'] }}"
                                    class="h-full w-full object-cover object-bottom lg:object-right"
                                    @if ($index === 0) fetchpriority="high" @else loading="lazy" @endif
                                >
                            </picture>

                            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-white/85 via-white/35 to-transparent lg:bg-gradient-to-r lg:from-white/85 lg:via-white/35 lg:to-transparent" aria-hidden="true"></div>

                            <div class="container-site relative py-12 lg:py-20">
                                <div class="max-w-xl lg:max-w-[60%]">
                                    <h1 class="text-3xl font-extrabold leading-tight tracking-tight text-brand-950 sm:text-4xl lg:text-[40px] lg:leading-[44px]">
                                        {{ $heroSlide['headline'] }}
                                    </h1>
                                    <p class="mt-4 max-w-xl text-base font-medium leading-7 text-brand-900 sm:text-lg">
                                        {{ $heroSlide['subtitle'] }}
                                    </p>
                                    <ul class="mt-6 space-y-3 text-sm font-semibold text-brand-950 sm:text-base">
                                        @foreach (['GPON и WiMAX', 'IPTV', 'Поддержка по телефону'] as $feature)
                                            <li class="flex items-center gap-3">
                                                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-950 text-accent-400">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </span>
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="mt-8">
                                        <a href="{{ $heroSlide['cta_url'] }}" class="btn bg-white text-brand-950 shadow-sm ring-1 ring-brand-950/10 hover:bg-brand-50">
                                            {{ $heroSlide['cta_label'] }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($heroSlides->count() > 1)
                    <div class="absolute inset-x-0 top-6 z-20 hidden lg:block">
                        <div class="container-site flex gap-2">
                            <button
                                type="button"
                                data-home-hero-prev
                                aria-label="Предыдущий слайд"
                                class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white/60 text-brand-950 backdrop-blur transition hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                data-home-hero-next
                                aria-label="Следующий слайд"
                                class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white/60 text-brand-950 backdrop-blur transition hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="swiper-pagination" data-home-hero-pagination></div>
                @endif
            </div>
        </section>
    @endif

    @if ($operational->isNotEmpty())
        <section class="border-b border-amber-200 bg-amber-50">
            <div class="container-site py-4">
                <h2 class="text-sm font-bold text-amber-900">Работы на сети и статус сервисов</h2>
                <ul class="mt-2 grid gap-2 text-sm text-amber-900 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($operational as $notice)
                        <li>
                            <a href="{{ route('news.show', $notice) }}" class="font-medium underline decoration-amber-300 underline-offset-2 hover:text-amber-950">
                                {{ $notice->title }}
                            </a>
                            <span class="text-xs text-amber-700">— {{ $notice->incident_status?->label() }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($addressSection)
        <section class="py-14 sm:py-16" id="check">
            <div class="container-site grid gap-8 lg:grid-cols-[1fr_0.9fr] lg:items-start">
                <div>
                    <h2 class="section-title">{{ $addressSection->title }}</h2>
                    <p class="section-subtitle">{{ $addressSection->subtitle }}</p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        @foreach ([
                            ['Проверка адреса специалистом', 'Мы не подтверждаем доступность автоматически: заявку проверит оператор.'],
                            ['Без обязательств', 'Заявка не является договором и не гарантирует подключение.'],
                            ['Понятные цены', 'Стоимость и период указаны в карточке тарифа.'],
                            ['Помощь с выбором', 'Подскажем технологию и тариф под ваш адрес.'],
                        ] as [$title, $text])
                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <p class="text-sm font-semibold text-brand-900">{{ $title }}</p>
                                <p class="mt-1 text-xs leading-5 text-slate-600">{{ $text }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @include('partials.lead-form', [
                    'formType' => 'coverage_check',
                    'formTitle' => 'Проверить адрес',
                    'formSubtitle' => 'Укажите населённый пункт и адрес. Специалист свяжется с вами и уточнит возможность подключения.',
                    'formShowAddress' => true,
                    'formButtonLabel' => 'Проверить адрес',
                    'formMessageLabel' => 'Комментарий (необязательно)',
                ])
            </div>
        </section>
    @endif

    @if ($featuredSection && $featuredTariffs->isNotEmpty())
        <section class="bg-white py-14 sm:py-16">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h2 class="section-title">{{ $featuredSection->title }}</h2>
                        <p class="section-subtitle">{{ $featuredSection->subtitle }}</p>
                    </div>
                    <a href="{{ route('tariffs.index') }}" class="btn-secondary">Все тарифы</a>
                </div>
                <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featuredTariffs as $tariff)
                        @include('partials.tariff-card', ['tariff' => $tariff])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($servicesSection)
        <section class="py-14 sm:py-16">
            <div class="container-site">
                <h2 class="section-title">{{ $servicesSection->title }}</h2>
                <p class="section-subtitle">{{ $servicesSection->subtitle }}</p>
                <div class="mt-8 grid gap-5 md:grid-cols-3">
                    @foreach ($technologies as $technology)
                        <article class="card p-6">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10"/></svg>
                            </span>
                            <h3 class="mt-4 text-lg font-bold text-brand-900">{{ $technology->name }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $technology->short_description }}</p>
                            <a href="{{ route('tariffs.index', ['technology' => $technology->slug]) }}" class="mt-4 inline-flex text-sm font-semibold text-brand-600 hover:text-accent-600">Тарифы {{ $technology->name }} →</a>
                        </article>
                    @endforeach
                    @foreach ($services as $service)
                        <article class="card p-6">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-accent-50 text-accent-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="5" width="18" height="12" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                            </span>
                            <h3 class="mt-4 text-lg font-bold text-brand-900">{{ $service->name }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $service->short_description }}</p>
                            <a href="{{ route('tariffs.index', ['service' => $service->slug]) }}" class="mt-4 inline-flex text-sm font-semibold text-brand-600 hover:text-accent-600">Тарифы с {{ $service->name }} →</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($benefitsSection && ! empty($benefitsSection->data))
        <section class="bg-brand-900 py-14 text-white sm:py-16">
            <div class="container-site">
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $benefitsSection->title }}</h2>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($benefitsSection->data as $benefit)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-mint-500/20 text-mint-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <h3 class="mt-3 text-base font-semibold text-white">{{ $benefit['title'] ?? '' }}</h3>
                            <p class="mt-1.5 text-sm leading-6 text-brand-200">{{ $benefit['text'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($stepsSection && ! empty($stepsSection->data))
        <section class="py-14 sm:py-16">
            <div class="container-site">
                <h2 class="section-title">{{ $stepsSection->title }}</h2>
                <ol class="mt-8 grid gap-5 md:grid-cols-3">
                    @foreach ($stepsSection->data as $i => $step)
                        <li class="card p-6">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-accent-500 text-base font-bold text-white">{{ $i + 1 }}</span>
                            <h3 class="mt-4 text-lg font-bold text-brand-900">{{ $step['title'] ?? '' }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $step['text'] ?? '' }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    @if ($coverageSection)
        <section class="bg-white py-14 sm:py-16">
            <div class="container-site grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
                <div>
                    <h2 class="section-title">{{ $coverageSection->title }}</h2>
                    <p class="section-subtitle">{{ $coverageSection->subtitle }}</p>
                    <a href="#check" class="btn-primary mt-6">Проверить адрес</a>
                </div>
                <ul class="grid gap-2 sm:grid-cols-2">
                    @foreach ($areas as $area)
                        <li class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3">
                            <span class="text-sm font-medium text-brand-900">{{ $area->name }}</span>
                            <span class="text-[11px] font-semibold text-slate-500">проверка адреса</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($promotionsSection && $promotions->isNotEmpty())
        <section class="py-14 sm:py-16">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <h2 class="section-title">{{ $promotionsSection->title }}</h2>
                    <a href="{{ route('promotions.index') }}" class="btn-secondary">Все акции</a>
                </div>
                <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($promotions as $promotion)
                        @include('partials.promotion-card', ['promotion' => $promotion])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($newsSection && $news->isNotEmpty())
        <section class="bg-white py-14 sm:py-16">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <h2 class="section-title">{{ $newsSection->title }}</h2>
                    <a href="{{ route('news.index') }}" class="btn-secondary">Все новости</a>
                </div>
                <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($news as $post)
                        @include('partials.post-card', ['post' => $post])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($subscriberSection)
        <section class="py-14 sm:py-16">
            <div class="container-site">
                <h2 class="section-title">{{ $subscriberSection->title }}</h2>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['Оплата', '/clients/payment', 'Способы оплаты и инструкции.'],
                        ['Документы', '/clients/documents', 'Договоры, памятки и заявления.'],
                        ['Нет интернета', '/clients/no-internet', 'Базовые шаги диагностики.'],
                        ['Личный кабинет', '/clients/cabinet', 'Переход во внешний кабинет.'],
                    ] as [$title, $url, $text])
                        <a href="{{ $url }}" class="card block p-5 transition hover:border-brand-300 hover:shadow-md">
                            <h3 class="text-base font-bold text-brand-900">{{ $title }}</h3>
                            <p class="mt-1.5 text-sm leading-6 text-slate-600">{{ $text }}</p>
                            <span class="mt-3 inline-flex text-sm font-semibold text-brand-600">Перейти →</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($contactsSection)
        <section class="bg-white py-14 sm:py-16" id="contacts">
            <div class="container-site grid gap-8 lg:grid-cols-2">
                <div>
                    <h2 class="section-title">{{ $contactsSection->title }}</h2>
                    <dl class="mt-6 space-y-4 text-sm">
                        @if ($companyAddress)
                            <div>
                                <dt class="font-semibold text-brand-900">Адрес</dt>
                                <dd class="mt-1 text-slate-600">{{ $companyAddress }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="font-semibold text-brand-900">Телефоны</dt>
                            <dd class="mt-1 space-y-1">
                                @foreach ($contactPhones as $phone)
                                    <a href="{{ $phone->link() }}" class="block text-brand-600 hover:text-accent-600">{{ $phone->value }}</a>
                                @endforeach
                            </dd>
                        </div>
                        @if ($officeHours)
                            <div>
                                <dt class="font-semibold text-brand-900">Режим работы офиса</dt>
                                <dd class="mt-1 text-slate-600">{{ $officeHours }}</dd>
                            </div>
                        @endif
                        @if ($supportHours)
                            <div>
                                <dt class="font-semibold text-brand-900">Техническая поддержка</dt>
                                <dd class="mt-1 text-slate-600">{{ $supportHours }}</dd>
                            </div>
                        @endif
                    </dl>
                    <a href="{{ route('contacts') }}" class="btn-secondary mt-6">Все контакты</a>
                </div>
                @include('partials.lead-form', [
                    'formType' => 'connection',
                    'formTitle' => 'Заявка на подключение',
                    'formSubtitle' => 'Оставьте контакты — оператор свяжется с вами, уточнит адрес и условия.',
                    'formShowAddress' => true,
                    'formButtonLabel' => 'Оставить заявку',
                ])
            </div>
        </section>
    @endif
@endsection
