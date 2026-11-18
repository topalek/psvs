@php($firstPhone = $contactPhones->first())
<div class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 p-2 backdrop-blur sm:hidden">
    <div class="grid grid-cols-3 gap-2">
        <a href="{{ $headerCtaUrl }}" class="flex flex-col items-center gap-0.5 rounded-xl bg-accent-500 px-2 py-2 text-[11px] font-semibold text-white">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.686 7-11a7 7 0 10-14 0c0 5.314 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
            Проверить
        </a>
        @if ($firstPhone)
            <a href="{{ $firstPhone->link() }}" class="flex flex-col items-center gap-0.5 rounded-xl border border-slate-200 px-2 py-2 text-[11px] font-semibold text-brand-900">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5.5A2.5 2.5 0 015.5 3h1.6c.5 0 .9.3 1 .8l.8 3.2a1 1 0 01-.3 1L7.4 9.4a13 13 0 007.2 7.2l1.4-1.2a1 1 0 011-.3l3.2.8c.5.1.8.5.8 1v1.6a2.5 2.5 0 01-2.5 2.5C10.6 21 3 13.4 3 5.5z"/></svg>
                Позвонить
            </a>
        @endif
        <a href="{{ $cabinetUrl }}" target="_blank" rel="noopener" class="flex flex-col items-center gap-0.5 rounded-xl border border-slate-200 px-2 py-2 text-[11px] font-semibold text-brand-900">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0"/></svg>
            Кабинет
        </a>
    </div>
</div>
<div class="h-20 sm:hidden" aria-hidden="true"></div>
