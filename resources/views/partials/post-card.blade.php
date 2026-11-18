@php($cover = $post->coverUrl('thumb'))
<article class="card group flex h-full flex-col overflow-hidden">
    <a href="{{ route('news.show', $post) }}" class="block aspect-[16/9] overflow-hidden bg-brand-100">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
        @else
            <span class="flex h-full items-center justify-center bg-gradient-to-br from-brand-800 to-brand-950 text-brand-200">
                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4zM8 10h8M8 14h5"/></svg>
            </span>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="badge">{{ $post->type->label() }}</span>
            <time datetime="{{ $post->published_at?->toDateString() }}" class="text-slate-500">{{ $post->published_at?->translatedFormat('j F Y') }}</time>
        </div>
        <h3 class="mt-3 text-lg font-bold leading-snug text-brand-900">
            <a href="{{ route('news.show', $post) }}" class="hover:text-accent-600">{{ $post->title }}</a>
        </h3>
        @if ($post->excerpt)
            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $post->excerpt }}</p>
        @endif
        <a href="{{ route('news.show', $post) }}" class="mt-auto pt-4 text-sm font-semibold text-brand-600 hover:text-accent-600">Читать далее →</a>
    </div>
</article>
