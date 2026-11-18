@extends('layouts.app')

@section('title', ($article->title).' — '.$companyName)
@section('meta_description', $article->excerpt ?? '')
@section('canonical', route('clients.help.show', $article))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('clients.index') }}" class="hover:text-brand-700">Абонентам</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">{{ $article->title }}</li>
                </ol>
            </nav>
            <p class="mt-4 text-xs font-semibold tracking-wide text-brand-500 uppercase">{{ $article->category?->name }}</p>
            <h1 class="mt-2 max-w-3xl text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">{{ $article->title }}</h1>
            @if ($article->excerpt)
                <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">{{ $article->excerpt }}</p>
            @endif
            @if ($article->last_reviewed_at)
                <p class="mt-3 text-xs text-slate-500">Проверено: {{ $article->last_reviewed_at->translatedFormat('j F Y') }}</p>
            @endif
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[1fr_320px] lg:items-start">
        <article class="grid gap-6">
            <div class="prose-site max-w-none">
                {!! \App\Support\Html::rich($article->body) !!}
            </div>

            @if (! empty($article->steps))
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Пошагово</h2>
                    <ol class="mt-3 grid gap-3">
                        @foreach ($article->steps as $i => $step)
                            <li class="flex gap-3 text-sm leading-6 text-slate-600">
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-bold text-brand-700">{{ $i + 1 }}</span>
                                <span>{{ is_array($step) ? ($step['text'] ?? '') : $step }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif

            @if (! empty($article->related_links))
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Связанные материалы</h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($article->related_links as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="text-brand-600 hover:text-accent-600">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </article>

        <aside class="grid gap-5 lg:sticky lg:top-28">
            <div class="card p-5">
                <h2 class="text-base font-bold text-brand-900">Не помогло?</h2>
                <p class="mt-1.5 text-sm leading-6 text-slate-600">Свяжитесь с поддержкой или посмотрите другие инструкции.</p>
                @if ($contactPhones->first())
                    <a href="{{ $contactPhones->first()->link() }}" class="btn-primary mt-4 w-full">Позвонить</a>
                @endif
                <a href="{{ route('clients.index') }}" class="btn-secondary mt-2 w-full">Все инструкции</a>
            </div>
        </aside>
    </div>
@endsection
