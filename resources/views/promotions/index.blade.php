@extends('layouts.app')

@section('title', 'Акции — '.$companyName)
@section('meta_description', 'Действующие акции и специальные предложения для абонентов. Условия и сроки указаны в каждой акции.')
@section('canonical', route('promotions.index'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Акции</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Акции</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Выгодные предложения и специальные акции. Здесь
                показываются только действующие предложения; условия и сроки указаны в карточке.</p>
        </div>
    </div>

    <div class="container-site py-10">
        @if ($promotions->isEmpty())
            <div class="card p-8 text-center">
                <h2 class="text-lg font-bold text-brand-900">Сейчас активных акций нет</h2>
                <p class="mt-2 text-sm text-slate-600">Следите за новостями или посмотрите действующие тарифы.</p>
                <a href="{{ route('tariffs.index') }}" class="btn-primary mt-5">Смотреть тарифы</a>
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($promotions as $promotion)
                    @include('partials.promotion-card', ['promotion' => $promotion])
                @endforeach
            </div>
            <div class="mt-8">{{ $promotions->links() }}</div>
        @endif
    </div>
@endsection
