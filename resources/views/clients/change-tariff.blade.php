@extends('layouts.app')

@section('title', 'Сменить тариф — '.$companyName)
@section('meta_description', 'Как сменить тарифный план. Обратитесь в офис или позвоните оператору.')
@section('canonical', route('clients.change-tariff'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Сменить тариф</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Сменить тариф</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Смена тарифа оформляется через оператора. Сайт не изменяет данные биллинга и лицевого счёта.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-6">
            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Порядок действий</h2>
                <ol class="mt-4 grid gap-3 text-sm leading-6 text-slate-600">
                    <li><span class="font-semibold text-brand-900">1.</span> Выберите подходящий тариф в каталоге.</li>
                    <li><span class="font-semibold text-brand-900">2.</span> Позвоните оператору или обратитесь в офис.</li>
                    <li><span class="font-semibold text-brand-900">3.</span> Уточните условия перехода и дату вступления изменений.</li>
                </ol>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('tariffs.index') }}" class="btn-secondary">Смотреть тарифы</a>
                    @if ($contactPhones->first())
                        <a href="{{ $contactPhones->first()->link() }}" class="btn-primary">Позвонить оператору</a>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-sm leading-6 text-slate-600">
                <p class="font-semibold text-brand-900">Важно</p>
                <p class="mt-1">Условия смены тарифа, перерасчёт и возможные ограничения зависят от текущего договора. Уточняйте их у оператора.</p>
            </div>
        </div>
    </div>
@endsection
