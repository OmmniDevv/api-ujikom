@if ($paginator->hasPages())
<nav class="flex items-center justify-between gap-4 flex-wrap" aria-label="Pagination">
    {{-- Info --}}
    <p class="text-xs text-white/40">
        Menampilkan <span class="text-white/70 font-medium">{{ $paginator->firstItem() }}</span>–<span class="text-white/70 font-medium">{{ $paginator->lastItem() }}</span>
        dari <span class="text-white/70 font-medium">{{ $paginator->total() }}</span> data
    </p>

    {{-- Links --}}
    <div class="flex items-center gap-1">
        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 text-xs rounded-md text-white/20 cursor-not-allowed bg-white/3 border border-white/5">←</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-1.5 text-xs rounded-md text-white/60 hover:text-orange-400 bg-white/5 border border-white/10 hover:border-orange-500/40 transition-all">←</a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-1.5 text-xs text-white/30">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 text-xs rounded-md font-semibold text-white bg-gradient-to-r from-orange-600 to-red-600 border border-orange-500/50 shadow-sm shadow-orange-900/40">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-1.5 text-xs rounded-md text-white/60 hover:text-orange-400 bg-white/5 border border-white/10 hover:border-orange-500/40 transition-all">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-1.5 text-xs rounded-md text-white/60 hover:text-orange-400 bg-white/5 border border-white/10 hover:border-orange-500/40 transition-all">→</a>
        @else
            <span class="px-3 py-1.5 text-xs rounded-md text-white/20 cursor-not-allowed bg-white/3 border border-white/5">→</span>
        @endif
    </div>
</nav>
@endif
