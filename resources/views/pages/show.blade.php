@extends('layouts.app')

@section('title', $page->seo_title ?: $page->title)
@section('meta_description', $page->seo_description ?? '')
@section('canonical', $page->canonical_url ?: url('/'.$page->slug))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">{{ $page->title }}</li>
                </ol>
            </nav>
            <h1 class="mt-4 max-w-3xl text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">{{ $page->title }}</h1>
            @if ($page->seo_description)
                <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">{{ $page->seo_description }}</p>
            @endif
        </div>
    </div>

    <div class="container-site grid gap-10 py-10 lg:grid-cols-[1fr_360px] lg:items-start">
        <article class="prose-site max-w-none">
            {!! \App\Support\Html::rich($page->body) !!}

            @if ($page->slug === 'about')
                <img src="{{ asset('images/about.jpg') }}" alt="О компании {{ $companyName }}"
                     class="mt-8 w-full rounded-2xl" loading="lazy" width="900" height="500">
            @endif
        </article>

        <aside class="grid gap-5 lg:sticky lg:top-28">
            @if (in_array($page->slug, ['internet', 'business'], true))
                <div class="card p-5">
                    <h2 class="text-base font-bold text-brand-900">Подобрать подключение</h2>
                    <p class="mt-1.5 text-sm leading-6 text-slate-600">Оставьте заявку — специалист уточнит доступность
                        адреса и условия.</p>
                    <a href="{{ route('coverage') }}" class="btn-primary mt-4 w-full">Проверить адрес</a>
                    <a href="{{ route('tariffs.index', ['customer_type' => $page->slug === 'business' ? 'business' : 'residential']) }}"
                       class="btn-secondary mt-2 w-full">Смотреть тарифы</a>
                </div>
            @endif

            <div class="card p-5">
                <h2 class="text-base font-bold text-brand-900">Контакты</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach ($contactPhones as $phone)
                        <li><a href="{{ $phone->link() }}"
                               class="font-semibold text-brand-600 hover:text-accent-600">{{ $phone->value }}</a></li>
                    @endforeach
                    @if ($companyAddress)
                        <li class="text-slate-600">{{ $companyAddress }}</li>
                    @endif
                    @if ($officeHours)
                        <li class="text-slate-600">{{ $officeHours }}</li>
                    @endif
                </ul>
                <a href="{{ route('contacts') }}" class="btn-ghost mt-3 w-full">Все контакты</a>
            </div>
        </aside>
    </div>
@endsection
