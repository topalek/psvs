@extends('layouts.app')

@section('title', 'Проверить скорость интернета — '.$companyName)
@section('meta_description', 'Как корректно проверить скорость интернета и что влияет на результат измерения.')
@section('canonical', route('clients.speed-test'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Проверка скорости</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Проверить скорость интернета</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Чтобы результат был корректным, подготовьте подключение и закройте лишние программы.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-6">
            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Как измерить скорость</h2>
                <ol class="mt-4 grid gap-3 text-sm leading-6 text-slate-600">
                    <li><span class="font-semibold text-brand-900">1.</span> Подключите компьютер к роутеру кабелем или к сети Wi-Fi 5 ГГц.</li>
                    <li><span class="font-semibold text-brand-900">2.</span> Закройте загрузки, стримы и другие программы, которые используют интернет.</li>
                    <li><span class="font-semibold text-brand-900">3.</span> Выполните замер на независимом сервисе проверки скорости.</li>
                    <li><span class="font-semibold text-brand-900">4.</span> Повторите замер 2–3 раза и сравните результаты.</li>
                </ol>
                <p class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-600">
                    На результат влияют загруженность сети, возможности устройства, тип подключения и Wi-Fi. Скорость по Wi-Fi может быть ниже, чем по кабелю.
                </p>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Если скорость ниже ожидаемой</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Перезагрузите роутер, проверьте подключение по кабелю и повторите замер. Если результат стабильно ниже, обратитесь в поддержку и укажите тариф, результаты замеров и способ подключения.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('clients.no-internet') }}" class="btn-secondary">Диагностика</a>
                    @if ($contactPhones->first())
                        <a href="{{ $contactPhones->first()->link() }}" class="btn-primary">Позвонить в поддержку</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
