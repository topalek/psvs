@extends('layouts.app')

@section('title', 'Проверить подключение — '.$companyName)
@section('meta_description', 'Проверьте возможность подключения по адресу. Доступность подтверждает специалист после обработки заявки.')
@section('canonical', route('coverage'))

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="container-site py-10 sm:py-12">
            <nav aria-label="Хлебные крошки" class="text-xs text-slate-500">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-700">Главная</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-slate-700">Проверка подключения</li>
                </ol>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-900 sm:text-4xl">Проверить возможность подключения</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">
                Укажите населённый пункт и адрес. Мы не подтверждаем доступность автоматически: заявку проверит специалист и свяжется с вами.
            </p>
        </div>
    </div>

    <div class="container-site grid gap-8 py-10 lg:grid-cols-[1fr_420px] lg:items-start">
        <div class="grid gap-6">
            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Как это работает</h2>
                <ol class="mt-4 grid gap-3">
                    @foreach ([
                        ['Оставьте адрес', 'Укажите населённый пункт, улицу и дом. Если населённого пункта нет в списке, впишите его вручную.'],
                        ['Мы проверим', 'Специалист уточнит техническую возможность подключения по вашему адресу.'],
                        ['Согласуем условия', 'Обсудим тариф, оборудование, стоимость и удобную дату подключения.'],
                    ] as $i => [$title, $text])
                        <li class="flex gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-700">{{ $i + 1 }}</span>
                            <div>
                                <p class="text-sm font-semibold text-brand-900">{{ $title }}</p>
                                <p class="mt-0.5 text-sm leading-6 text-slate-600">{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
                <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-900">
                    Заявка не является подтверждением доступности и не заключает договор. Возможность подключения зависит от технической инфраструктуры по конкретному адресу.
                </p>
            </div>

            <div class="card p-6">
                <h2 class="text-xl font-bold text-brand-900">Населённые пункты в работе</h2>
                <p class="mt-1.5 text-sm text-slate-600">Список носит справочный характер и не гарантирует подключение по конкретному адресу.</p>
                <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                    @forelse ($areas as $area)
                        <li class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-4 py-3">
                            <span>
                                <span class="block text-sm font-medium text-brand-900">{{ $area->name }}</span>
                                @if ($area->district)
                                    <span class="text-xs text-slate-500">{{ $area->district }}</span>
                                @endif
                            </span>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">проверка</span>
                        </li>
                    @empty
                        <li class="text-sm text-slate-600">Список населённых пунктов уточняется.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <aside class="lg:sticky lg:top-28">
            @include('partials.lead-form', [
                'formType' => 'coverage_check',
                'formTitle' => 'Заявка на проверку адреса',
                'formSubtitle' => 'Заполните адрес и контакты. Специалист свяжется с вами.',
                'formShowAddress' => true,
                'formButtonLabel' => 'Проверить адрес',
                'formMessageLabel' => 'Комментарий',
            ])
        </aside>
    </div>
@endsection
