@extends('layouts.app')

@section('title', ($promotion->seo_title ?: $promotion->title).' — '.$companyName)
@section('meta_description', $promotion->seo_description ?: ($promotion->excerpt ?? ''))
@section('canonical', $promotion->canonical_url ?: route('promotions.show', $promotion))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="{{ route('promotions.index') }}" class="hover:text-brand-700">Акции</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">{{ $promotion->title }}</li>
                </ol>
            </nav>
            <h1 class="mt-4 max-w-3xl text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">{{ $promotion->title }}</h1>
            <p class="mt-3 text-sm text-slate-600">
                @if ($promotion->is_indefinite)
                    Бессрочная акция
                @elseif ($promotion->ends_at)
                    Действует до {{ $promotion->ends_at->translatedFormat('j F Y') }}
                @else
                    Срок действия уточняйте у оператора
                @endif
            </p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[1fr_380px] lg:items-start">
        <div class="grid gap-6">
            @if ($promotion->coverUrl())
                <img src="{{ $promotion->coverUrl() }}" alt="{{ $promotion->title }}" class="w-full rounded-2xl"
                     width="960" height="540">
            @endif
            <article class="prose-site max-w-none">
                {!! \App\Support\Html::rich($promotion->body) !!}
                @if ($promotion->terms)
                    <h2>Условия акции</h2>
                    <p>{{ $promotion->terms }}</p>
                @endif
            </article>

            @if ($promotion->tariffs->isNotEmpty())
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-brand-900">Тарифы, к которым применима акция</h2>
                    <ul class="mt-3 grid gap-2 sm:grid-cols-2">
                        @foreach ($promotion->tariffs as $tariff)
                            <li>
                                <a href="{{ route('tariffs.show', $tariff) }}"
                                   class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3 text-sm hover:border-brand-300">
                                    <span>
                                        <span class="block font-semibold text-brand-900">{{ $tariff->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $tariff->technology?->name }} · {{ $tariff->speed_mbps }} Мбит/с</span>
                                    </span>
                                    <span
                                        class="font-semibold text-brand-700">{{ $tariff->formattedRegularPrice() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <aside class="grid gap-5 lg:sticky lg:top-28">
            <div class="card p-5">
                <h2 class="text-base font-bold text-brand-900">Как воспользоваться</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Свяжитесь с оператором или оставьте заявку. Условия
                    акции требуют подтверждения при оформлении.</p>
                @if ($promotion->cta_label && $promotion->cta_url)
                    <a href="{{ $promotion->cta_url }}" class="btn-primary mt-4 w-full">{{ $promotion->cta_label }}</a>
                @else
                    <a href="{{ route('coverage') }}" class="btn-primary mt-4 w-full">Оставить заявку</a>
                @endif
                @if ($contactPhones->first())
                    <a href="{{ $contactPhones->first()->link() }}" class="btn-secondary mt-2 w-full">Позвонить</a>
                @endif
            </div>
            @include('partials.lead-form', [
                'formType' => 'connection',
                'formTitle' => 'Заявка на подключение',
                'formSubtitle' => 'Укажите контакты — оператор свяжется с вами.',
                'formButtonLabel' => 'Отправить',
                'formMessageLabel' => 'Комментарий',
            ])
        </aside>
    </div>
@endsection
