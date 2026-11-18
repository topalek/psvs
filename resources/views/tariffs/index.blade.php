@extends('layouts.app')

@section('title', 'Тарифы — '.$companyName)
@section('meta_description', 'Тарифы на домашний интернет по GPON и WiMAX. Цены указаны за 30 календарных дней. Доступность уточняется по адресу.')
@section('canonical', route('tariffs.index'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Тарифы</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Тарифные планы</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">
                Тарифы для физических лиц, цена указана за 30 календарных дней. Доступность тарифа по конкретному адресу уточняет специалист.
            </p>
        </div>
    </div>

    <div class="container-site py-10">
        <form method="GET" action="{{ route('tariffs.index') }}" class="card grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
            <div>
                <label for="technology" class="field-label">Технология</label>
                <select id="technology" name="technology" class="field-input">
                    <option value="">Все</option>
                    @foreach ($technologies as $technology)
                        <option value="{{ $technology->slug }}" @selected(request('technology') === $technology->slug)>{{ $technology->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="area" class="field-label">Населённый пункт</label>
                <select id="area" name="area" class="field-input">
                    <option value="">Все</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->slug }}" @selected(request('area') === $area->slug)>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="customer_type" class="field-label">Для кого</label>
                <select id="customer_type" name="customer_type" class="field-input">
                    <option value="">Все</option>
                    @foreach ($customerTypes as $value => $label)
                        <option value="{{ $value }}" @selected(request('customer_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="service" class="field-label">Услуга</label>
                <select id="service" name="service" class="field-input">
                    <option value="">Все</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->slug }}" @selected(request('service') === $service->slug)>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="max_price" class="field-label">Цена до, ₽</label>
                <input id="max_price" name="max_price" type="number" min="0" step="50" value="{{ request('max_price') }}" class="field-input" placeholder="Например, 1200">
            </div>
            <div class="flex gap-2 sm:col-span-2 lg:col-span-5">
                <button type="submit" class="btn-primary">Показать тарифы</button>
                @if (request()->hasAny(['technology', 'area', 'customer_type', 'service', 'max_price']))
                    <a href="{{ route('tariffs.index') }}" class="btn-secondary">Сбросить</a>
                @endif
                <span class="ml-auto self-center text-sm text-slate-500">Найдено: {{ $tariffs->total() }}</span>
            </div>
        </form>

        @if ($tariffs->isEmpty())
            <div class="card mt-8 p-8 text-center">
                <h2 class="text-lg font-bold text-brand-900">По выбранным условиям тарифов нет</h2>
                <p class="mt-2 text-sm text-slate-600">Попробуйте изменить фильтры или оставьте заявку — специалист подберёт подходящий вариант.</p>
                <a href="{{ route('contacts') }}" class="btn-primary mt-5">Оставить заявку</a>
            </div>
        @else
            <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($tariffs as $tariff)
                    @include('partials.tariff-card', ['tariff' => $tariff])
                @endforeach
            </div>
            <div class="mt-8">{{ $tariffs->links() }}</div>
        @endif

        <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-brand-900">Важно</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Цены указаны за 30 календарных дней. Акционная цена действует при условиях, указанных в карточке тарифа. Стоимость подключения, оборудования и доступность по адресу уточняйте у оператора. Информация на сайте не является публичной офертой.
            </p>
        </div>
    </div>
@endsection
