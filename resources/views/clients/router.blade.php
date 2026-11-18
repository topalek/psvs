@extends('layouts.app')

@section('title', 'Настройка роутера — '.$companyName)
@section('meta_description', 'Общие рекомендации по настройке роутера и подключению оборудования.')
@section('canonical', route('clients.router'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Настройка роутера</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Настройка роутера</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Общие шаги для оборудования. Конкретные параметры зависят от модели роутера и способа подключения.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-6">
            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Общий порядок</h2>
                <ol class="mt-4 grid gap-3 text-sm leading-6 text-slate-600">
                    <li><span class="font-semibold text-brand-900">1.</span> Подключите роутер к питанию и кабелю провайдера.</li>
                    <li><span class="font-semibold text-brand-900">2.</span> Подключитесь к роутеру по кабелю или Wi-Fi.</li>
                    <li><span class="font-semibold text-brand-900">3.</span> Откройте адрес веб-интерфейса из инструкции производителя.</li>
                    <li><span class="font-semibold text-brand-900">4.</span> Настройте подключение и Wi-Fi согласно инструкции устройства.</li>
                    <li><span class="font-semibold text-brand-900">5.</span> Сохраните настройки и перезагрузите роутер.</li>
                </ol>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Нужны параметры подключения?</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Для оборудования, предоставленного оператором, параметры уточняйте у поддержки. Не сбрасывайте настройки роутера, если не уверены в результате.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @if ($contactPhones->first())
                        <a href="{{ $contactPhones->first()->link() }}" class="btn-primary">Позвонить в поддержку</a>
                    @endif
                    <a href="{{ route('clients.help.show', 'nastroyka-routera') }}" class="btn-secondary">Статья о настройке</a>
                </div>
            </div>
        </div>
    </div>
@endsection
