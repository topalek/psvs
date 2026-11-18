@extends('layouts.app')

@section('title', 'Документы — '.$companyName)
@section('meta_description', 'Договоры, памятки и заявления для абонентов. Актуальные редакции документов.')
@section('canonical', route('clients.documents'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Документы</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Документы</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Действующие договоры, памятки и формы. Если нужного документа нет, запросите его в офисе компании.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-4">
            @forelse ($documents as $document)
                <div class="card flex flex-wrap items-center justify-between gap-4 p-5">
                    <div>
                        <h2 class="text-base font-bold text-brand-900">{{ $document->title }}</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            @if ($document->category){{ $document->category }}@endif
                            @if ($document->version) · версия {{ $document->version }}@endif
                            @if ($document->effective_date) · с {{ $document->effective_date->format('d.m.Y') }}@endif
                        </p>
                        @if ($document->description)
                            <p class="mt-1 text-xs leading-5 text-slate-500">{{ $document->description }}</p>
                        @endif
                    </div>
                    @if ($document->fileUrl())
                        <a href="{{ $document->fileUrl() }}" class="btn-secondary" target="_blank" rel="noopener">Скачать</a>
                    @else
                        <span class="text-xs text-slate-500">Файл готовится к публикации</span>
                    @endif
                </div>
            @empty
                <div class="card p-8 text-center">
                    <h2 class="text-lg font-bold text-brand-900">Документы пока не опубликованы</h2>
                    <p class="mt-2 text-sm text-slate-600">Запросите актуальные документы в офисе компании или по телефону.</p>
                    @if ($contactPhones->first())
                        <a href="{{ $contactPhones->first()->link() }}" class="btn-primary mt-5">Позвонить</a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
@endsection
