@extends('layouts.app')

@section('title', 'Личный кабинет — '.$companyName)
@section('meta_description', 'Вход во внешний личный кабинет абонента. Проверка баланса и оплата услуг.')
@section('canonical', route('clients.cabinet'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Личный кабинет</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Личный кабинет</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Личный кабинет абонента — внешний сервис. Вход и управление услугами выполняются на стороне биллинга.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-6">
            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Что можно сделать в кабинете</h2>
                <ul class="mt-3 grid gap-2 text-sm leading-6 text-slate-600">
                    <li>• проверить состояние лицевого счёта;</li>
                    <li>• оплатить услуги онлайн;</li>
                    <li>• посмотреть информацию по подключённым услугам.</li>
                </ul>
                <a href="{{ $cabinetUrl }}" target="_blank" rel="noopener" class="btn-primary mt-5">Перейти в личный кабинет</a>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-900">
                <p class="font-semibold">Если кабинет недоступен</p>
                <p class="mt-1">Воспользуйтесь другим способом оплаты или свяжитесь с поддержкой. Мы не храним пароли абонентов и не создаём второй личный кабинет на этом сайте.</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('clients.payment') }}" class="btn-secondary">Способы оплаты</a>
                    @if ($contactPhones->first())
                        <a href="{{ $contactPhones->first()->link() }}" class="btn-ghost">Позвонить</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
