@extends('layouts.app')

@section('title', 'Доверительный платёж — '.$companyName)
@section('meta_description', 'Информация об услуге доверительного платежа. Условия уточняйте у оператора.')
@section('canonical', route('clients.trust-payment'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Доверительный платёж</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Доверительный платёж</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Услуга временного пополнения баланса при отсутствии средств на счёте.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-6">
            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Как подключить</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Возможность и условия доверительного платежа зависят от состояния лицевого счёта и тарифа. Актуальные условия уточняйте у оператора по телефону или в офисе компании.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @if ($contactPhones->first())
                        <a href="{{ $contactPhones->first()->link() }}" class="btn-primary">Позвонить оператору</a>
                    @endif
                    <a href="{{ $cabinetUrl }}" target="_blank" rel="noopener" class="btn-secondary">Личный кабинет</a>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 text-sm leading-6 text-slate-600">
                <p class="font-semibold text-brand-900">Обратите внимание</p>
                <p class="mt-1">Мы не публикуем неподтверждённые размеры, сроки и ограничения услуги. Информация на сайте не является публичной офертой.</p>
            </div>
        </div>
    </div>
@endsection
