@php($cover = $promotion->coverUrl('thumb'))
<article class="card group flex h-full flex-col overflow-hidden">
    <a href="{{ route('promotions.show', $promotion) }}" class="block aspect-[16/9] overflow-hidden bg-accent-100">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $promotion->title }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
        @else
            <span class="flex h-full items-center justify-center bg-gradient-to-br from-accent-400 to-accent-600 text-white">
                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l2.6 5.3 5.9.9-4.3 4.1 1 5.8-5.2-2.7-5.2 2.7 1-5.8-4.3-4.1 5.9-.9z"/></svg>
            </span>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="badge bg-accent-50 text-accent-700">Акция</span>
            @if ($promotion->is_indefinite)
                <span class="text-slate-500">Бессрочно</span>
            @elseif ($promotion->ends_at)
                <span class="text-slate-500">до {{ $promotion->ends_at->translatedFormat('j F Y') }}</span>
            @endif
        </div>
        <h3 class="mt-3 text-lg font-bold leading-snug text-brand-900">
            <a href="{{ route('promotions.show', $promotion) }}" class="hover:text-accent-600">{{ $promotion->title }}</a>
        </h3>
        @if ($promotion->excerpt)
            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $promotion->excerpt }}</p>
        @endif
        <a href="{{ route('promotions.show', $promotion) }}" class="mt-auto pt-4 text-sm font-semibold text-brand-600 hover:text-accent-600">Подробнее →</a>
    </div>
</article>
