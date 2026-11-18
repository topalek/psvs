<!DOCTYPE html>
<html lang="ru" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $companyName.' — интернет в Крымске и Абинском районе')</title>
    <meta name="description" content="@yield('meta_description', 'Домашний интернет по технологиям GPON и WiMAX, тарифы, акции и поддержка абонентов.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta name="robots" content="@yield('robots', 'index,follow')">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:title" content="@yield('title', $companyName)">
    <meta property="og:description" content="@yield('meta_description', 'Домашний интернет по технологиям GPON и WiMAX.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif

    <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50">
<a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-brand-900 focus:shadow">
    Перейти к содержанию
</a>

@include('partials.header')

<main id="main">
    @if (session('lead_success'))
        <div class="container-site pt-6">
            <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-900">
                <p class="font-semibold">Спасибо! Заявка получена.</p>
                <p class="mt-1">{{ session('lead_success') }}</p>
            </div>
        </div>
    @endif

    @yield('content')
</main>

@include('partials.footer')
@include('partials.mobile-bar')

@stack('scripts')
</body>
</html>
