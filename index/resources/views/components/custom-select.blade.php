@props([
    'options' => [],
    'placeholder' => null,
    'icon' => null,
    'searchable' => false,
    'searchPlaceholder' => 'Ketik untuk mencari...',
    'align' => 'left',
    'selected' => null,
    'buttonClass' => '',
    'dropdownClass' => '',
])

@php
    $wireModel = $attributes->wire('model');
    $xModelNumber = $attributes->get('x-model.number');
    $xModel = $attributes->get('x-model') ?? $xModelNumber;
    
    $normalizedOptions = [];
    if ($options instanceof \Illuminate\Support\Collection) {
        $options = $options->all();
    }
    if (is_array($options)) {
        $isList = array_is_list($options);
        foreach ($options as $key => $opt) {
            if (is_object($opt)) {
                $val = $opt->id ?? $opt->value ?? $opt->code ?? (string)$key;
                $lbl = $opt->name ?? $opt->label ?? $opt->title ?? (string)$val;
                $normalizedOptions[] = ['value' => (string)$val, 'label' => (string)$lbl];
            } elseif (is_array($opt)) {
                $val = $opt['id'] ?? $opt['value'] ?? $opt['code'] ?? (string)$key;
                $lbl = $opt['name'] ?? $opt['label'] ?? $opt['title'] ?? (string)$val;
                $isHeader = !empty($opt['is_header']);
                $normalizedOptions[] = [
                    'value' => (string)$val,
                    'label' => (string)$lbl,
                    'is_header' => $isHeader,
                ];
            } else {
                if ($isList) {
                    $val = (string)$opt;
                    $lbl = (string)$opt;
                } else {
                    $val = (string)$key;
                    $lbl = (string)$opt;
                }
                $normalizedOptions[] = ['value' => $val, 'label' => $lbl, 'is_header' => false];
            }
        }
    }
    $optionsHash = substr(md5(json_encode($normalizedOptions)), 0, 8);
    $passedWireKey = $attributes->get('wire:key');
    $modelKey = ($wireModel && $wireModel->value()) ? $wireModel->value() : ($xModel ?: 'sel');
    $wireKey = $passedWireKey 
        ? ($passedWireKey . '-' . $optionsHash)
        : ('csel-' . $modelKey . '-' . $optionsHash);
@endphp

<div 
    wire:key="{{ $wireKey }}"
    {{ $attributes->whereDoesntStartWith('wire:model')->whereDoesntStartWith('x-model')->except(['class', 'buttonClass', 'dropdownClass', 'wire:key']) }}
    x-data="{
        id: 'csel_' + Math.random().toString(36).substring(2, 9),
        open: false,
        search: '',
        searchable: {{ $searchable ? 'true' : 'false' }},
        @if($wireModel && $wireModel->value())
            selected: @entangle($attributes->wire('model')),
        @elseif($xModel)
            get selected() { return {{ $xModel }}; },
            set selected(val) { {{ $xModel }} = {{ $xModelNumber ? 'Number(val)' : 'val' }}; },
        @else
            selected: {{ Js::from($selected ?? '') }},
        @endif
        options: {{ Js::from($normalizedOptions) }},
        get currentLabel() {
            const found = this.options.find(o => !o.is_header && String(o.value) === String(this.selected));
            if (found) return found.label;
            return {{ Js::from($placeholder) }} || (this.options.find(o => !o.is_header) ? this.options.find(o => !o.is_header).label : '');
        },
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$dispatch('custom-select-opened', { id: this.id });
            }
        },
        select(val) {
            this.selected = val;
            this.open = false;
            this.search = '';
            this.$dispatch('change', val);
        },
        get filteredOptions() {
            if (!this.searchable || !this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o => o.is_header || o.label.toLowerCase().includes(q));
        }
    }"
    @custom-select-opened.window="if ($event.detail.id !== id) open = false"
    @custom-select-set-options.window="if (!$event.detail.name || $event.detail.name === '{{ $wireModel ? $wireModel->value() : '' }}') { options = $event.detail.options; if (typeof $event.detail.selected !== 'undefined') { selected = $event.detail.selected; } }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="relative {{ $attributes->get('class', 'w-full') }}"
>
    @php
        $hasCustomPadding = (bool) preg_match('/\b(p[xytblr]?-\S+)\b/', $buttonClass);
        $hasCustomBg = (bool) preg_match('/\bbg-\S+\b/', $buttonClass);
    @endphp
    <!-- Trigger Button -->
    <button 
        type="button" 
        @click="toggle()"
        class="w-full {{ $hasCustomPadding ? '' : 'px-2.5 py-1.5' }} rounded-lg border border-gov-border {{ $hasCustomBg ? '' : 'bg-white' }} text-xs font-medium text-gov-textMain cursor-pointer flex items-center justify-between gap-1.5 shadow-2xs hover:bg-slate-50 transition focus:outline-none focus:border-gov-navy select-none {{ $buttonClass }}"
    >
        <div class="flex items-center gap-1.5 min-w-0 truncate">
            @if($icon)
                <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
            @endif
            <span class="truncate" x-text="currentLabel"></span>
        </div>
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>

    <!-- Dropdown Menu Panel -->
    <div 
        x-show="open" 
        x-cloak 
        @click.stop
        wire:ignore.self
        class="absolute {{ $align === 'right' ? 'right-0' : 'left-0' }} top-full mt-1 w-full min-w-[170px] bg-white rounded-xl shadow-xl border border-gov-border py-1 z-50 max-h-60 overflow-hidden flex flex-col {{ $dropdownClass }}"
        style="display: none;"
    >
        @if($searchable)
            <div class="p-1 pb-1.5 border-b border-slate-100">
                <input 
                    x-model="search" 
                    type="text" 
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full px-2 py-1 text-xs rounded-md border border-slate-200 focus:outline-none focus:ring-1 focus:ring-gov-navy"
                >
            </div>
        @endif

        <div class="overflow-y-auto max-h-52 divide-y divide-slate-50">
            <template x-for="(opt, idx) in filteredOptions" :key="opt.value + '_' + idx">
                <div>
                    <template x-if="opt.is_header">
                        <div class="px-3 py-1.5 text-[10px] font-bold tracking-wider uppercase text-slate-400 bg-slate-50/90 border-y border-slate-100 select-none">
                            <span x-text="opt.label"></span>
                        </div>
                    </template>
                    <template x-if="!opt.is_header">
                        <button 
                            type="button" 
                            @click.stop="select(opt.value)"
                            class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between transition-colors"
                            :class="String(selected) === String(opt.value) ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'"
                        >
                            <span class="truncate" x-text="opt.label"></span>
                            <span x-show="String(selected) === String(opt.value)" class="text-gov-navy font-bold shrink-0 ml-1.5">✓</span>
                        </button>
                    </template>
                </div>
            </template>
            <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-slate-400 italic text-center text-xs">
                Tidak ada data ditemukan
            </div>
        </div>
    </div>

    <!-- Accessible / Testing Native Representation -->
    <select class="sr-only" tabindex="-1" aria-hidden="true" wire:ignore>
        @php
            $inOptGroup = false;
        @endphp
        @foreach($normalizedOptions as $opt)
            @if(!empty($opt['is_header']))
                @if($inOptGroup)
                    </optgroup>
                @endif
                <optgroup label="{{ $opt['label'] }}">
                @php $inOptGroup = true; @endphp
            @else
                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
            @endif
        @endforeach
        @if($inOptGroup)
            </optgroup>
        @endif
    </select>
</div>
