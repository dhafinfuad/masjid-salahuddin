@props(['field', 'table' => null])
@php
    $isSorted = false;
    $dir = 'asc';
    if (isset($this)) {
        $isSorted = $this->isSorted($field, $table);
        $dir = $this->getSortDirection($field, $table);
    }
@endphp
@if($isSorted)
    @if($dir === 'asc')
        <svg class="w-3.5 h-3.5 text-gov-navy shrink-0 select-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="m5 12 7-7 7 7"/>
            <path d="M12 19V5"/>
        </svg>
    @else
        <svg class="w-3.5 h-3.5 text-gov-navy shrink-0 select-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 5v14"/>
            <path d="m19 12-7 7-7-7"/>
        </svg>
    @endif
@else
    <svg class="w-3.5 h-3.5 text-slate-400 opacity-40 group-hover:opacity-100 shrink-0 transition-opacity select-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="m7 15 5 5 5-5"/>
        <path d="m7 9 5-5 5 5"/>
    </svg>
@endif
