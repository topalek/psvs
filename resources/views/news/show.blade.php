@extends('layouts.app')

@section('title', ($post->seo_title ?: $post->title).' — '.$companyName)
@section('meta_description', $post->seo_description ?: ($post->excerpt ?? ''))
@section('canonical', $post->canonical_url ?: route('news.show', $post))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-brand-700">Новости</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">{{ $post->title }}</li>
                </ol>
            </nav>
            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                <span class="badge">{{ $post->type->label() }}</span>
                <time datetime="{{ $post->published_at?->toDateString() }}" class="text-slate-500">{{ $post->published_at?->translatedFormat('j F Y') }}</time>
                @if ($post->incident_status)
                    <span class="badge {{ $post->incident_status->isActive() ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">{{ $post->incident_status->label() }}</span>
                @endif
            </div>
            <h1 class="mt-3 max-w-3xl text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">{{ $post->title }}</h1>
            @if ($post->excerpt)
                <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">{{ $post->excerpt }}</p>
            @endif
            <p class="mt-4 text-xs text-slate-500">Обновлено: {{ $post->updated_at?->translatedFormat('j F Y, H:i') }}</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[1fr_320px] lg:items-start">
        <article class="grid gap-6">
            @if ($post->coverUrl())
                <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" class="w-full rounded-2xl" width="960" height="540">
            @endif
            <div class="prose-site max-w-none">
                {!! \App\Support\Html::rich($post->body) !!}
            </div>

            @if ($post->operational_note)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
                    <p class="font-semibold">Информация о работах</p>
                    <p class="mt-1">{{ $post->operational_note }}</p>
                </div>
            @endif

            @if ($post->areas->isNotEmpty() || $post->services->isNotEmpty())
                <div class="card p-5">
                    <h2 class="text-base font-bold text-brand-900">Затронутые направления</h2>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach ($post->areas as $area)
                            <li class="badge">{{ $area->name }}</li>
                        @endforeach
                        @foreach ($post->services as $service)
                            <li class="badge">{{ $service->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </article>

        <aside class="grid gap-5 lg:sticky lg:top-28">
            <div class="card p-5">
                <h2 class="text-base font-bold text-brand-900">Нужна помощь?</h2>
                <p class="mt-1.5 text-sm leading-6 text-slate-600">Если проблема не решена, свяжитесь с поддержкой.</p>
                @if ($contactPhones->first())
                    <a href="{{ $contactPhones->first()->link() }}" class="btn-primary mt-4 w-full">Позвонить</a>
                @endif
                <a href="{{ route('clients.no-internet') }}" class="btn-secondary mt-2 w-full">Что делать без интернета</a>
            </div>
            <div class="card p-5">
                <h2 class="text-base font-bold text-brand-900">Другие новости</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach ($related as $item)
                        <li><a href="{{ route('news.show', $item) }}" class="text-brand-600 hover:text-accent-600">{{ $item->title }}</a></li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>
@endsection
