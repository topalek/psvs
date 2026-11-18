@php
    $links = [
        ['clients.index', 'Абонентам', 'Помощь и инструкции'],
        ['clients.payment', 'Оплата', 'Способы оплаты'],
        ['clients.trust-payment', 'Доверительный платёж', 'Условия услуги'],
        ['clients.speed-test', 'Проверить скорость', 'Инструкция'],
        ['clients.no-internet', 'Нет интернета', 'Диагностика'],
        ['clients.router', 'Настройка роутера', 'Общие шаги'],
        ['clients.documents', 'Документы', 'Договоры и памятки'],
        ['clients.change-tariff', 'Сменить тариф', 'Порядок действий'],
        ['clients.cabinet', 'Личный кабинет', 'Внешний сервис'],
        ['clients.faq', 'Вопросы и ответы', 'FAQ'],
    ];
@endphp
<nav aria-label="Раздел для абонентов" class="card p-3">
    <ul class="grid gap-1 sm:grid-cols-2 lg:grid-cols-1">
        @foreach ($links as [$route, $label, $hint])
            <li>
                <a href="{{ route($route) }}"
                   class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm transition {{ request()->routeIs($route) ? 'bg-brand-50 font-semibold text-brand-900' : 'text-slate-700 hover:bg-slate-50' }}">
                    <span>
                        <span class="block">{{ $label }}</span>
                        <span class="block text-xs font-normal text-slate-500">{{ $hint }}</span>
                    </span>
                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
