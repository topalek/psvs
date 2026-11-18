@extends('layouts.app')

@section('title', 'Нет интернета — '.$companyName)
@section('meta_description', 'Что проверить, если пропал интернет: питание роутера, кабели, перезагрузка оборудования и обращение в поддержку.')
@section('canonical', route('clients.no-internet'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Нет интернета</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Нет интернета: что проверить</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Простые шаги, которые помогают решить проблему без вызова специалиста.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-6">
            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Проверьте по порядку</h2>
                <ol class="mt-4 grid gap-4">
                    @foreach ([
                        ['Питание роутера', 'Убедитесь, что роутер включён и индикаторы горят. Проверьте блок питания и розетку.'],
                        ['Кабели', 'Проверьте, плотно ли подключены кабель питания и интернет-кабель. Попробуйте переподключить их.'],
                        ['Перезагрузка', 'Выключите роутер из розетки на 30 секунд и включите снова. Дождитесь загрузки.'],
                        ['Устройство', 'Проверьте, работает ли интернет на другом устройстве. Перезагрузите проблемное устройство.'],
                        ['Статус сети', 'Посмотрите новости и сообщения о работах на сети.'],
                    ] as $i => [$title, $text])
                        <li class="flex gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-700">{{ $i + 1 }}</span>
                            <div>
                                <p class="text-sm font-semibold text-brand-900">{{ $title }}</p>
                                <p class="mt-0.5 text-sm leading-6 text-slate-600">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Если не помогло</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Свяжитесь с поддержкой и подготовьте: номер лицевого счёта или адрес, описание проблемы, индикаторы роутера.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @if ($contactPhones->first())
                        <a href="{{ $contactPhones->first()->link() }}" class="btn-primary">Позвонить в поддержку</a>
                    @endif
                    <a href="{{ route('news.index') }}" class="btn-secondary">Работы на сети</a>
                    <a href="{{ route('clients.speed-test') }}" class="btn-ghost">Проверить скорость</a>
                </div>
            </div>

            @if ($articles->isNotEmpty())
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Полезные инструкции</h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($articles as $article)
                            <li><a href="{{ route('clients.help.show', $article) }}" class="text-brand-600 hover:text-accent-600">{{ $article->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection
