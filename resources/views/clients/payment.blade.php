@extends('layouts.app')

@section('title', 'Оплата услуг — '.$companyName)
@section('meta_description', 'Способы оплаты интернета: офис, банк, личный кабинет. Актуальные условия уточняйте у оператора.')
@section('canonical', route('clients.payment'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Оплата</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Оплата услуг</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Выберите удобный способ оплаты. Если платёж не отобразился, обратитесь в поддержку.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">В офисе</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Оплата наличными или банковской картой в офисе компании.</p>
                    @if ($companyAddress)
                        <p class="mt-3 text-sm font-medium text-brand-900">{{ $companyAddress }}</p>
                    @endif
                    @if ($officeHours)
                        <p class="mt-1 text-sm text-slate-600">{{ $officeHours }}</p>
                    @endif
                    <p class="mt-3 text-xs text-slate-500">Возможность оплаты картой уточняйте у оператора.</p>
                </div>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">В банке</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Оплата по реквизитам или через отделение банка. Сохраните платёжный документ до зачисления средств.</p>
                    <a href="{{ route('clients.help.show', 'oplata-v-banke') }}" class="btn-secondary mt-4">Инструкция по оплате в банке</a>
                </div>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Онлайн в личном кабинете</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Пополнение баланса через внешний личный кабинет абонента.</p>
                    <a href="{{ $cabinetUrl }}" target="_blank" rel="noopener" class="btn-primary mt-4">Перейти в кабинет</a>
                    <a href="{{ route('clients.help.show', 'oplata-onlayn') }}" class="btn-ghost mt-2">Инструкция по оплате онлайн</a>
                </div>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Другие способы</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Актуальный перечень способов оплаты и реквизиты уточняйте у оператора или в офисе компании.</p>
                    @if ($paymentUrl)
                        <a href="{{ $paymentUrl }}" target="_blank" rel="noopener" class="btn-secondary mt-4">Оплатить онлайн</a>
                    @endif
                    @if ($contactPhones->first())
                        <a href="{{ $contactPhones->first()->link() }}" class="btn-ghost mt-2">Позвонить оператору</a>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-900">
                <p class="font-semibold">Важно</p>
                <p class="mt-1">Срок зачисления зависит от выбранного способа оплаты и банка. Комиссии и ограничения уточняйте у платёжной организации. Сайт не обрабатывает данные банковских карт.</p>
            </div>
        </div>
    </div>
@endsection
