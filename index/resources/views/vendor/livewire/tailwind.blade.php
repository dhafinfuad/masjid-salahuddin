@php
if (! isset($scrollTo)) {
    $scrollTo = false;
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            {{-- Info Text (Menampilkan X sampai Y dari Z data) --}}
            <div class="text-center sm:text-left text-slate-500 font-medium order-2 sm:order-1">
                <p class="leading-5">
                    <span>{!! __('pagination.showing') !== 'pagination.showing' ? __('pagination.showing') : 'Menampilkan' !!}</span>
                    <span class="font-bold text-slate-800">{{ $paginator->firstItem() }}</span>
                    <span>{!! __('pagination.to') !== 'pagination.to' ? __('pagination.to') : 'sampai' !!}</span>
                    <span class="font-bold text-slate-800">{{ $paginator->lastItem() }}</span>
                    <span>{!! __('pagination.of') !== 'pagination.of' ? __('pagination.of') : 'dari' !!}</span>
                    <span class="font-bold text-slate-800">{{ $paginator->total() }}</span>
                    <span>{!! __('pagination.results') !== 'pagination.results' ? __('pagination.results') : 'data' !!}</span>
                </p>
            </div>

            {{-- Numeric Pagination Bar (Matches Admin UI Specification) --}}
            <div class="max-w-full overflow-x-auto py-0.5 flex items-center justify-center sm:justify-end order-1 sm:order-2 w-full sm:w-auto">
                <div class="inline-flex items-center rounded-lg border border-slate-200 bg-white shadow-2xs overflow-hidden shrink-0 text-xs select-none">
                    
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{!! __('pagination.previous') !!}"
                              class="inline-flex items-center justify-center w-9 h-9 text-slate-300 bg-slate-50/50 cursor-not-allowed border-r border-slate-200 shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                        </span>
                    @else
                        <button type="button"
                                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                wire:loading.attr="disabled"
                                aria-label="{!! __('pagination.previous') !!}"
                                class="inline-flex items-center justify-center w-9 h-9 text-slate-600 hover:text-gov-navy hover:bg-slate-50 border-r border-slate-200 transition cursor-pointer shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true"
                                  class="inline-flex items-center justify-center min-w-[34px] h-9 px-2 text-xs font-semibold text-slate-400 bg-slate-50/40 border-r border-slate-200 cursor-default shrink-0">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                          class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-xs font-bold text-white bg-gov-navy border-r border-gov-navy cursor-default z-10 shrink-0 shadow-2xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button type="button"
                                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-xs font-medium text-slate-700 hover:text-gov-navy hover:bg-slate-50 border-r border-slate-200 transition cursor-pointer shrink-0">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button type="button"
                                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                wire:loading.attr="disabled"
                                aria-label="{!! __('pagination.next') !!}"
                                class="inline-flex items-center justify-center w-9 h-9 text-slate-600 hover:text-gov-navy hover:bg-slate-50 transition cursor-pointer shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{!! __('pagination.next') !!}"
                              class="inline-flex items-center justify-center w-9 h-9 text-slate-300 bg-slate-50/50 cursor-not-allowed shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </span>
                    @endif

                </div>
            </div>
        </nav>
    @endif
</div>
