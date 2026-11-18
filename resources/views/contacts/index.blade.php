@extends('layouts.app')

@section('title', 'Контакты — '.$companyName)
@section('meta_description', 'Контакты Сити Телеком Сервис: телефоны, адрес офиса, режим работы и форма обратной связи.')
@section('canonical', route('contacts'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Контакты</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Контакты</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">Свяжитесь с нами удобным способом или оставьте обращение через форму.</p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[1fr_420px] lg:items-start">
        <div class="grid gap-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="card p-6">
                    <h2 class="text-sm font-semibold tracking-wide text-brand-500 uppercase">Телефоны</h2>
                    <ul class="mt-3 space-y-3">
                        @foreach ($phones as $phone)
                            <li>
                                <a href="{{ $phone->link() }}" class="text-lg font-bold text-brand-900 hover:text-accent-600">{{ $phone->value }}</a>
                                <span class="block text-xs text-slate-500">{{ $phone->label }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card p-6">
                    <h2 class="text-sm font-semibold tracking-wide text-brand-500 uppercase">E-mail</h2>
                    <ul class="mt-3 space-y-2">
                        @foreach ($emails as $email)
                            <li><a href="{{ $email->link() }}" class="text-base font-semibold text-brand-900 hover:text-accent-600">{{ $email->value }}</a></li>
                        @endforeach
                    </ul>
                    <h2 class="mt-6 text-sm font-semibold tracking-wide text-brand-500 uppercase">Личный кабинет</h2>
                    <a href="{{ $cabinetUrl }}" target="_blank" rel="noopener" class="mt-2 inline-flex text-sm font-semibold text-brand-600 hover:text-accent-600">Перейти в кабинет →</a>
                </div>
                <div class="card p-6">
                    <h2 class="text-sm font-semibold tracking-wide text-brand-500 uppercase">Адрес офиса</h2>
                    <p class="mt-3 text-base font-semibold text-brand-900">{{ $address }}</p>
                    @if ($officeHours)
                        <p class="mt-1 text-sm text-slate-600">{{ $officeHours }}</p>
                    @endif
                </div>
                <div class="card p-6">
                    <h2 class="text-sm font-semibold tracking-wide text-brand-500 uppercase">Техническая поддержка</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $supportHours }}</p>
                    <a href="{{ route('clients.no-internet') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-600 hover:text-accent-600">Что делать без интернета →</a>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="border-b border-slate-100 p-5">
                    <h2 class="text-lg font-bold text-brand-900">Как добраться</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $address }}</p>
                </div>
                @if ($mapUrl)
                    <div class="aspect-[16/9] w-full bg-slate-100">
                        <iframe src="{{ $mapUrl }}" title="Карта: {{ $address }}" class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                @else
                    <div class="flex aspect-[16/9] w-full flex-col items-center justify-center gap-2 bg-slate-100 p-6 text-center">
                        <svg class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.686 7-11a7 7 0 10-14 0c0 5.314 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        <p class="text-sm font-medium text-slate-600">{{ $address }}</p>
                        <p class="text-xs text-slate-500">Ссылка на карту будет добавлена после подтверждения.</p>
                    </div>
                @endif
            </div>
        </div>

        <aside class="lg:sticky lg:top-28">
            @include('partials.lead-form', [
                'formType' => 'contact',
                'formTitle' => 'Форма обратной связи',
                'formSubtitle' => 'Опишите вопрос — специалист свяжется с вами.',
                'formButtonLabel' => 'Отправить обращение',
                'formMessageLabel' => 'Сообщение',
            ])
        </aside>
    </div>
@endsection
