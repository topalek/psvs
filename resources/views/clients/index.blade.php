@extends('layouts.app')

@section('title', 'Абонентам — '.$companyName)
@section('meta_description', 'Оплата, документы, инструкции, проверка скорости и помощь абонентам Сити Телеком Сервис.')
@section('canonical', route('clients.index'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Абонентам</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Абонентам</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Оплата, документы, настройка оборудования и ответы на частые вопросы.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-8">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ([
                    ['clients.payment', 'Оплатить услуги', 'Инструкции по оплате в офисе, банке и через кабинет.'],
                    ['clients.cabinet', 'Личный кабинет', 'Переход во внешний кабинет абонента.'],
                    ['clients.no-internet', 'Нет интернета', 'Что проверить до звонка в поддержку.'],
                    ['clients.documents', 'Документы', 'Договоры, памятки и заявления.'],
                    ['clients.speed-test', 'Проверить скорость', 'Как корректно измерить скорость.'],
                    ['clients.router', 'Настройка роутера', 'Общие рекомендации по оборудованию.'],
                ] as [$route, $title, $text])
                    <a href="{{ route($route) }}" class="card block p-5 transition hover:border-brand-300 hover:shadow-md">
                        <h2 class="text-base font-bold text-brand-900">{{ $title }}</h2>
                        <p class="mt-1.5 text-sm leading-6 text-slate-600">{{ $text }}</p>
                        <span class="mt-3 inline-flex text-sm font-semibold text-brand-600">Перейти →</span>
                    </a>
                @endforeach
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Помощь и инструкции</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    @foreach ($helpCategories as $category)
                        <div>
                            <h3 class="text-sm font-bold tracking-wide text-brand-700 uppercase">{{ $category->name }}</h3>
                            <ul class="mt-2 space-y-1.5 text-sm">
                                @foreach ($category->articles as $article)
                                    <li>
                                        <a href="{{ route('clients.help.show', $article) }}" class="text-brand-600 hover:text-accent-600">{{ $article->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Частые вопросы</h2>
                <div class="mt-4 grid gap-3">
                    @foreach ($faqCategories as $category)
                        @foreach ($category->faqs as $faq)
                            <details class="group rounded-xl border border-slate-200 p-4">
                                <summary class="cursor-pointer list-none text-sm font-semibold text-brand-900 marker:hidden">
                                    {{ $faq->question }}
                                </summary>
                                <div class="prose-site mt-2 text-sm">{!! \App\Support\Html::rich($faq->answer) !!}</div>
                            </details>
                        @endforeach
                    @endforeach
                </div>
                <a href="{{ route('clients.faq') }}" class="btn-secondary mt-5">Все вопросы и ответы</a>
            </div>
        </div>
    </div>
@endsection
