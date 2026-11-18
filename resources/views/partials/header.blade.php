<header x-data="{ open: false }" class="sticky top-0 z-40">
    <div class="bg-brand-950 text-white">
        <div class="container-site flex flex-wrap items-center justify-between gap-2 py-2 text-xs sm:text-sm">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-brand-100">
                @if ($companyAddress)
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-mint-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.686 7-11a7 7 0 10-14 0c0 5.314 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        {{ $companyAddress }}
                    </span>
                @endif
                @if ($officeHours)
                    <span class="hidden sm:inline">{{ $officeHours }}</span>
                @endif
            </div>
            <div class="flex items-center gap-4">
                @foreach ($contactPhones as $phone)
                    <a href="{{ $phone->link() }}" class="inline-flex items-center gap-1.5 font-semibold text-white hover:text-mint-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5.5A2.5 2.5 0 015.5 3h1.6c.5 0 .9.3 1 .8l.8 3.2a1 1 0 01-.3 1L7.4 9.4a13 13 0 007.2 7.2l1.4-1.2a1 1 0 011-.3l3.2.8c.5.1.8.5.8 1v1.6a2.5 2.5 0 01-2.5 2.5C10.6 21 3 13.4 3 5.5z"/></svg>
                        {{ $phone->value }}
                    </a>
                @endforeach
                <a href="{{ $cabinetUrl }}" target="_blank" rel="noopener" class="hidden items-center gap-1.5 rounded-lg bg-white/10 px-3 py-1.5 font-medium text-white hover:bg-white/20 sm:inline-flex">
                    Личный кабинет
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M9 7h8v8"/></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="container-site flex items-center justify-between gap-4 py-3">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ $companyName }} — на главную">
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-10 w-auto" width="48" height="40">
                {{-- <span class="leading-tight"> --}}
                    {{-- <span class="block text-base font-extrabold tracking-tight text-brand-900">{{ $companyName }}</span> --}}
                    {{-- <span class="block text-[11px] font-medium text-slate-500">Интернет · Телевидение · Поддержка</span> --}}
                {{-- </span> --}}
            </a>

            <nav class="hidden items-center gap-0.5 xl:flex" aria-label="Основная навигация">
                @if ($mainMenu)
                    @foreach ($mainMenu->items as $item)
                        <a href="{{ $item->url }}"
                           class="nav-link {{ request()->is(ltrim($item->url, '/')) || request()->is(ltrim($item->url, '/').'/*') ? 'nav-link-active' : '' }}"
                           @if ($item->target === '_blank') target="_blank" rel="noopener" @endif>
                            {{ $item->label }}
                        </a>
                    @endforeach
                @endif
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ $headerCtaUrl }}" class="btn-primary hidden sm:inline-flex">{{ $headerCtaLabel }}</a>
                <button type="button"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-brand-800 hover:bg-slate-50 xl:hidden"
                        @click="open = !open"
                        :aria-expanded="open.toString()"
                        aria-controls="mobile-menu"
                        aria-label="Открыть меню">
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>
        </div>

        <div id="mobile-menu" x-show="open" x-cloak x-transition.opacity class="border-t border-slate-200 bg-white xl:hidden">
            <nav class="container-site grid gap-1 py-4" aria-label="Мобильная навигация">
                @if ($mainMenu)
                    @foreach ($mainMenu->items as $item)
                        <a href="{{ $item->url }}" class="rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-brand-50 hover:text-brand-900">
                            {{ $item->label }}
                        </a>
                    @endforeach
                @endif
                <div class="mt-3 grid gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ $headerCtaUrl }}" class="btn-primary w-full">{{ $headerCtaLabel }}</a>
                    <a href="{{ $cabinetUrl }}" target="_blank" rel="noopener" class="btn-secondary w-full">Личный кабинет</a>
                    @foreach ($contactPhones as $phone)
                        <a href="{{ $phone->link() }}" class="btn-ghost w-full">{{ $phone->label }}: {{ $phone->value }}</a>
                    @endforeach
                </div>
            </nav>
        </div>
    </div>
</header>
