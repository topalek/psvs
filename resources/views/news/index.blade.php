@extends('layouts.app')

@section('title', 'Новости и работы на сети — '.$companyName)
@section('meta_description', 'Новости компании, изменения тарифов, плановые работы, аварии и расширение сети.')
@section('canonical', route('news.index'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Новости</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Новости и работы на сети</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Новости компании, изменения условий, плановые работы и информация о расширении сети.</p>
        </div>
    </div>

    <div class="container-site py-10">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="min-w-56">
                <label for="type" class="field-label">Тип публикации</label>
                <select id="type" name="type" class="field-input" onchange="this.form.submit()">
                    <option value="">Все типы</option>
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if (request()->filled('type'))
                <a href="{{ route('news.index') }}" class="btn-secondary">Сбросить</a>
            @endif
        </form>

        @if ($posts->isEmpty())
            <div class="card mt-8 p-8 text-center">
                <h2 class="text-lg font-bold text-brand-900">Публикаций пока нет</h2>
                <p class="mt-2 text-sm text-slate-600">Загляните позже или посмотрите тарифы.</p>
            </div>
        @else
            <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    @include('partials.post-card', ['post' => $post])
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </div>
@endsection
