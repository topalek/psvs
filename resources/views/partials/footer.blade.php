<footer class="mt-20 border-t border-slate-200 bg-brand-950 text-brand-100">
    <div class="container-site grid gap-10 py-12 md:grid-cols-2 lg:grid-cols-4">
        <div class="lg:col-span-1">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-10 w-auto brightness-0 invert" width="48" height="40">
                <span class="text-base font-extrabold text-white">{{ $companyName }}</span>
            </div>
            <p class="mt-4 max-w-xs text-sm leading-6 text-brand-200">
                Интернет и телевидение в Крымском и Абинском районах Краснодарского края.
            </p>
            <a href="{{ $cabinetUrl }}" target="_blank" rel="noopener" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/20">
                Личный кабинет
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M9 7h8v8"/></svg>
            </a>
        </div>

        <div>
            <h2 class="text-sm font-semibold tracking-wide text-white uppercase">Разделы</h2>
            <ul class="mt-4 space-y-2 text-sm">
                @if ($footerMenu)
                    @foreach ($footerMenu->items->take(6) as $item)
                        <li><a href="{{ $item->url }}" class="text-brand-200 hover:text-white">{{ $item->label }}</a></li>
                    @endforeach
                @endif
            </ul>
        </div>

        <div>
            <h2 class="text-sm font-semibold tracking-wide text-white uppercase">Документы</h2>
            <ul class="mt-4 space-y-2 text-sm">
                @if ($footerMenu)
                    @foreach ($footerMenu->items->slice(6) as $item)
                        <li><a href="{{ $item->url }}" class="text-brand-200 hover:text-white">{{ $item->label }}</a></li>
                    @endforeach
                @endif
            </ul>
        </div>

        <div>
            <h2 class="text-sm font-semibold tracking-wide text-white uppercase">Контакты</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @foreach ($contactPhones as $phone)
                    <li>
                        <a href="{{ $phone->link() }}" class="font-semibold text-white hover:text-mint-400">{{ $phone->value }}</a>
                        <span class="block text-xs text-brand-300">{{ $phone->label }}</span>
                    </li>
                @endforeach
                @foreach ($contactEmails as $email)
                    <li><a href="{{ $email->link() }}" class="text-brand-200 hover:text-white">{{ $email->value }}</a></li>
                @endforeach
                @if ($companyAddress)
                    <li class="text-brand-200">{{ $companyAddress }}</li>
                @endif
                @if ($officeHours)
                    <li class="text-brand-300">{{ $officeHours }}</li>
                @endif
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-site flex flex-col gap-2 py-5 text-xs text-brand-300 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} {{ $companyName }}. Все права защищены.</p>
            <p class="max-w-xl sm:text-right">{{ $footerNote }}</p>
        </div>
    </div>
</footer>
