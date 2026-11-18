@extends('layouts.app')

@section('title', 'Страница не найдена — '.$companyName)
@section('meta_description', 'Страница не найдена. Воспользуйтесь поиском по разделам или свяжитесь с нами.')
@section('robots', 'noindex,follow')

@section('content')
    <div class="container-site py-20 text-center">
        <p class="text-sm font-bold tracking-widest text-accent-600 uppercase">Ошибка 404</p>
        <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Страница не найдена</h1>
        <p class="mx-auto mt-3 max-w-xl text-base leading-7 text-slate-600">Возможно, страница была перемещена или адрес указан неверно. Воспользуйтесь ссылками ниже.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-2">
            <a href="{{ route('home') }}" class="btn-primary">На главную</a>
            <a href="{{ route('coverage') }}" class="btn-secondary">Проверить подключение</a>
            <a href="{{ route('tariffs.index') }}" class="btn-ghost">Тарифы</a>
            <a href="{{ route('contacts') }}" class="btn-ghost">Контакты</a>
        </div>
    </div>
@endsection
