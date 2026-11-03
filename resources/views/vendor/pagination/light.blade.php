{{-- Pagination tema terang khusus aplikasi (tanpa varian dark: bawaan Laravel
     yang aktif saat OS/browser pengguna memakai dark mode). --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between mt-6">
        <p class="text-sm text-slate-500">
            Halaman <span class="font-semibold text-slate-700">{{ $paginator->currentPage() }}</span>
            dari <span class="font-semibold text-slate-700">{{ $paginator->lastPage() }}</span>
        </p>

        <div class="flex items-center gap-1">
            {{-- Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-400 bg-white border border-slate-200 rounded-lg cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-colors"
                   aria-label="@lang('pagination.previous')">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center px-3 py-2 text-sm text-slate-400 select-none">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center px-3.5 py-2 text-sm font-semibold text-white bg-blue-600 border border-blue-600 rounded-lg shadow-sm shadow-blue-600/30 select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Selanjutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-colors"
                   aria-label="@lang('pagination.next')">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-400 bg-white border border-slate-200 rounded-lg cursor-not-allowed select-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
