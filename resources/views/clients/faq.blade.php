@extends('layouts.app')

@section('title', 'Вопросы и ответы — '.$companyName)
@section('meta_description', 'Ответы на частые вопросы абонентов о подключении, оплате и поддержке.')
@section('canonical', route('clients.faq'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Вопросы и ответы</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Вопросы и ответы</h1>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[280px_1fr] lg:items-start">
        <aside class="lg:sticky lg:top-28">@include('partials.clients-nav')</aside>

        <div class="grid gap-6">
            @forelse ($categories as $category)
                <div class="card p-6">
                    <h2 class="text-xl font-bold text-brand-900">{{ $category->name }}</h2>
                    <div class="mt-4 grid gap-3">
                        @foreach ($category->faqs as $faq)
                            <details class="rounded-xl border border-slate-200 p-4">
                                <summary class="cursor-pointer list-none text-sm font-semibold text-brand-900">{{ $faq->question }}</summary>
                                <div class="prose-site mt-2 text-sm">{!! \App\Support\Html::rich($faq->answer) !!}</div>
                            </details>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-sm text-slate-600">Вопросы и ответы пока не опубликованы.</div>
            @endforelse

            <div class="card p-6">
                <h2 class="text-lg font-bold text-brand-900">Не нашли ответ?</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Свяжитесь с поддержкой — поможем разобраться.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @if ($contactPhones->first())
                        <a href="{{ $contactPhones->first()->link() }}" class="btn-primary">Позвонить</a>
                    @endif
                    <a href="{{ route('contacts') }}" class="btn-secondary">Контакты</a>
                </div>
            </div>
        </div>
    </div>
@endsection
