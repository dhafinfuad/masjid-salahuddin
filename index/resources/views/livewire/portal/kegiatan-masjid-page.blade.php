<div class="min-h-screen flex flex-col bg-gov-canvas text-gov-textMain"
     x-data="{
         showPortalNotulaModal: false,
         portalNotulaData: {
             id: null,
             title: '',
             type: 'pekanan',
             speaker_name: '',
             date: '',
             time_display: '',
             notula: '',
         },
         portalNotulaFontSizeIndex: 1,
         openPortalNotula(data) {
             if (!data || typeof data !== 'object') return;
             this.portalNotulaData = {
                 id: data.id || null,
                 title: data.title || '',
                 type: data.type || 'pekanan',
                 speaker_name: data.speaker_name || '',
                 date: data.date || '',
                 time_display: data.time_display || '',
                 notula: data.notula || '',
             };
             this.portalNotulaFontSizeIndex = 1;
             this.showPortalNotulaModal = true;
         },
         adjustPortalNotulaFontSize(delta) {
             const next = this.portalNotulaFontSizeIndex + delta;
             if (next >= 0 && next <= 3) this.portalNotulaFontSizeIndex = next;
         }
     }"
>
    
    <!-- Top Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gov-border shadow-2xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            
            <!-- Left: Back to Portal & Title -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" wire:navigate 
                   class="inline-flex items-center justify-center p-2 rounded-lg border border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-600 transition shadow-2xs cursor-pointer" 
                   title="Kembali ke Portal">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>

                <div>
                    <h1 class="text-sm sm:text-base font-bold text-gov-textMain leading-tight">Kegiatan Masjid</h1>
                    <p class="text-[11px] text-slate-500 leading-none mt-0.5">{{ $settings->name ?? 'Masjid Salahuddin' }} • KPP Madya Malang</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Header Card Banner -->
        <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-2">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">KALENDER & JADWAL MASJID</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gov-textMain mt-1">
                        Daftar Kegiatan & Ibadah Masjid
                        @if($selectedMonth !== 'all' && isset($months[(int)$selectedMonth]))
                            — {{ $months[(int)$selectedMonth] }} {{ $selectedYear }}
                        @elseif($selectedMonth === 'all')
                            — Seluruh Periode {{ $selectedYear }}
                        @endif
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        Jadwal lengkap kajian rutin pekanan, kajian tematik, khutbah Shalat Jumat, serta agenda kegiatan besar {{ $settings->name ?? 'Masjid Salahuddin' }}.
                    </p>
                </div>

                <!-- Livewire Loading Indicator (Compliant with PANDUAN_ARSITEKTUR_DAN_PERFORMA) -->
                <div wire:loading.inline-flex style="display: none;" 
                     class="inline-flex flex-row items-center gap-2 text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-amber-600 inline-block" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="whitespace-nowrap leading-none">Memperbarui kegiatan...</span>
                </div>
            </div>

            <!-- Search & Month/Year Toolbar (Row on Desktop, Stacked on Mobile) -->
            <div class="pt-1">
                <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                    
                    <!-- Search Input (flex-1) -->
                    <div class="flex-1 min-w-0">
                        <label for="search-activity-input" class="block text-xs font-bold uppercase text-slate-500 mb-1.5 text-left">
                            Pencarian
                        </label>
                        <div class="relative">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input 
                                type="text" 
                                id="search-activity-input"
                                wire:model.live.debounce.300ms="search" 
                                placeholder="Cari judul kajian, pemateri, khatib, muadzin, MC..." 
                                class="w-full pl-9 pr-8 py-1.5 text-xs bg-slate-50 border border-gov-border rounded-lg text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition"
                            >
                            @if(!empty($search))
                                <button type="button" 
                                        wire:click="$set('search', '')" 
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 rounded transition cursor-pointer"
                                        title="Hapus pencarian">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Filter Bulan (w-full sm:w-48) -->
                    <div class="w-full sm:w-48 shrink-0">
                        <label for="filter-activity-month" class="block text-xs font-bold uppercase text-slate-500 mb-1.5 text-left">
                            Filter Bulan
                        </label>
                        <div class="relative">
                            <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center" wire:ignore>
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600 shrink-0 inline-block"></i>
                            </div>
                            <select 
                                id="filter-activity-month" 
                                wire:model.live="selectedMonth" 
                                class="w-full pl-8 pr-8 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs font-medium text-gov-textMain cursor-pointer transition shadow-2xs">
                                <option value="all">Semua Bulan</option>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filter Tahun (w-full sm:w-36) -->
                    <div class="w-full sm:w-36 shrink-0">
                        <label for="filter-activity-year" class="block text-xs font-bold uppercase text-slate-500 mb-1.5 text-left">
                            Tahun
                        </label>
                        <div class="relative">
                            <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center" wire:ignore>
                                <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-indigo-600 shrink-0 inline-block"></i>
                            </div>
                            <select 
                                id="filter-activity-year" 
                                wire:model.live="selectedYear" 
                                class="w-full pl-8 pr-8 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs font-medium text-gov-textMain cursor-pointer transition shadow-2xs">
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}">{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Reset Filter Button (w-full sm:w-auto) -->
                    <div class="w-full sm:w-auto shrink-0">
                        <span class="hidden sm:block text-xs font-bold uppercase text-transparent select-none mb-1.5">&nbsp;</span>
                        <button 
                            type="button" 
                            wire:click="resetFilters" 
                            class="w-full sm:w-auto px-4 py-1.5 rounded-lg border border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer shrink-0"
                            title="Reset seluruh filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-500"></i>
                            <span>Reset</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Card 2: Filter Kategori & Counter Ringkasan (Card Tersendiri di Bawahnya) -->
        <div class="bg-white rounded-xl p-4 sm:p-5 border border-gov-border shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            
            <!-- Category Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="setCategory('all')" 
                        class="px-3.5 py-2 text-xs rounded-lg transition cursor-pointer {{ $selectedCategory === 'all' ? 'bg-gov-navy text-white shadow-2xs font-bold' : 'bg-slate-50 border border-gov-border text-slate-600 hover:bg-slate-100 font-medium' }}">
                    Semua Kegiatan
                </button>
                <button type="button" wire:click="setCategory('pekanan')" 
                        class="px-3.5 py-2 text-xs rounded-lg transition cursor-pointer {{ $selectedCategory === 'pekanan' ? 'bg-gov-navy text-white shadow-2xs font-bold' : 'bg-slate-50 border border-gov-border text-slate-600 hover:bg-slate-100 font-medium' }}">
                    Kajian Pekanan
                </button>
                <button type="button" wire:click="setCategory('tematik')" 
                        class="px-3.5 py-2 text-xs rounded-lg transition cursor-pointer {{ $selectedCategory === 'tematik' ? 'bg-gov-navy text-white shadow-2xs font-bold' : 'bg-slate-50 border border-gov-border text-slate-600 hover:bg-slate-100 font-medium' }}">
                    Kajian Tematik
                </button>
                <button type="button" wire:click="setCategory('jumat')" 
                        class="px-3.5 py-2 text-xs rounded-lg transition cursor-pointer {{ $selectedCategory === 'jumat' ? 'bg-gov-navy text-white shadow-2xs font-bold' : 'bg-slate-50 border border-gov-border text-slate-600 hover:bg-slate-100 font-medium' }}">
                    Khutbah Jumat
                </button>
                <button type="button" wire:click="setCategory('akbar')" 
                        class="px-3.5 py-2 text-xs rounded-lg transition cursor-pointer {{ $selectedCategory === 'akbar' ? 'bg-gov-navy text-white shadow-2xs font-bold' : 'bg-slate-50 border border-gov-border text-slate-600 hover:bg-slate-100 font-medium' }}">
                    Kegiatan Akbar
                </button>
            </div>

        </div>

        <!-- Activities Card Grid (Desktop: 2 Columns, Large: 3 Columns) -->
        @if($items->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($items as $item)
                    @php
                        $isJumat = ($item['type'] === 'jumat');
                        $isTematik = ($item['type'] === 'tematik');
                        $isPekanan = ($item['type'] === 'pekanan');
                        $isAkbar = ($item['type'] === 'akbar');

                        $badgeText = 'Pekanan';
                        $badgeStyle = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                        if ($isJumat) {
                            $badgeText = 'Khutbah Jumat';
                            $badgeStyle = 'bg-amber-50 text-amber-800 border-amber-200';
                        } elseif ($isTematik) {
                            $badgeText = 'Tematik';
                            $badgeStyle = 'bg-blue-50 text-blue-700 border-blue-200';
                        } elseif ($isAkbar) {
                            $badgeText = 'Kegiatan Akbar';
                            $badgeStyle = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                        }

                        $itemDateStr = $item['date'] ? Carbon\Carbon::parse($item['date'])->format('Y-m-d') : '';

                        $isNext = false;
                        if ($item['source'] === 'kajian') {
                            if ($isJumat) {
                                $isNext = ($nextJumatKajianId && $item['id'] === $nextJumatKajianId);
                            } else {
                                $isNext = ($nextPekananKajianId && $item['id'] === $nextPekananKajianId);
                            }
                        } elseif ($item['source'] === 'agenda') {
                            $isNext = ($nextAgendaId && $item['id'] === $nextAgendaId);
                        }

                        $isCurrentActiveWeek = ($itemDateStr >= $todayDate && $itemDateStr <= $kajianCurrentWeekEnd);
                        $isHighlight = ($isNext || $isCurrentActiveWeek);
                    @endphp

                    <div class="border rounded-xl p-4 transition space-y-3 flex flex-col justify-between group {{ $isHighlight ? 'bg-amber-100/40 hover:bg-amber-100/80 border-l-4 border-l-amber-500 border-amber-300 shadow-2xs' : 'bg-white border-gov-border hover:shadow-2xs' }}">
                        
                        <div class="space-y-2.5">
                            <!-- Badge & Date Row -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none whitespace-nowrap shrink-0 {{ $badgeStyle }}">
                                    {{ $badgeText }}
                                </span>
                                <span class="text-xs {{ $isHighlight ? 'font-bold text-amber-900' : 'font-semibold text-slate-600' }} whitespace-nowrap shrink-0">
                                    {{ $item['date'] ? $item['date']->translatedFormat('d M Y') : '-' }}
                                    @if(!empty($item['time_display']) && $item['time_display'] !== 'Sesuai Jadwal')
                                        • {{ $item['time_display'] }}
                                    @endif
                                </span>
                            </div>

                            <!-- Title & Speaker -->
                            <div>
                                @if($isJumat)
                                    <h3 class="text-sm sm:text-base font-bold text-gov-textMain group-hover:text-gov-navy transition leading-snug">
                                        {{ $item['khatib_name'] ?: ($item['title'] ?: 'Khatib Shalat Jumat') }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                        <span>Muadzin: <strong class="text-slate-700 font-medium">{{ $item['muadzin_name'] ?: '-' }}</strong></span>
                                        <span class="mx-1.5 text-slate-300">•</span>
                                        <span>MC: <strong class="text-slate-700 font-medium">{{ $item['mc_name'] ?: '-' }}</strong></span>
                                    </p>
                                @elseif($isAkbar)
                                    <h3 class="text-sm sm:text-base font-bold text-gov-textMain group-hover:text-gov-navy transition leading-snug">
                                        {{ $item['title'] }}
                                    </h3>
                                    @if(!empty($item['description']))
                                        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed line-clamp-2">
                                            {{ $item['description'] }}
                                        </p>
                                    @endif
                                    @if(!empty($item['budget']))
                                        <div class="mt-2 text-xs font-semibold text-gov-navy">
                                            Anggaran: Rp {{ number_format($item['budget'], 0, ',', '.') }}
                                        </div>
                                    @endif
                                @else
                                    <h3 class="text-sm sm:text-base font-bold text-gov-textMain group-hover:text-gov-navy transition leading-snug">
                                        {{ $item['title'] }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                        <span class="font-medium text-slate-700">{{ $item['speaker_name'] ?: 'Pemateri Kajian' }}</span>
                                        @if(!empty($item['location']))
                                            <span class="mx-1.5 text-slate-300">•</span>
                                            <span>{{ $item['location'] }}</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Footer Card Action (Teks MC & Notula) -->
                        @if($isJumat || !empty($item['notula']) || !empty($item['youtube_url']))
                            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-end mt-auto">
                                <div>
                                    @if($isJumat)
                                        <a href="{{ route('admin.kajian.teks-mc', $item['id']) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-gov-50 hover:bg-gov-100 text-gov-navy border border-gov-border font-semibold text-xs shadow-2xs transition cursor-pointer shrink-0"
                                           title="Download & Cetak Teks MC Sholat Jumat (PDF)">
                                            <i data-lucide="printer" class="w-3.5 h-3.5 text-gov-navy shrink-0"></i>
                                            <span>Teks MC</span>
                                        </a>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if(!empty($item['youtube_url']))
                                        <a href="{{ $item['youtube_url'] }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 border border-rose-200 font-semibold text-xs shadow-2xs transition cursor-pointer shrink-0"
                                            title="Tonton Siaran di YouTube">
                                            <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                            </svg>
                                            <span>YouTube</span>
                                        </a>
                                    @endif
                                    @if(!empty($item['notula']))
                                        <button type="button"
                                            @click="openPortalNotula({{ json_encode([
                                                'id' => $item['id'],
                                                'title' => $item['title'] ?: ($isJumat ? 'Khutbah Jumat' : 'Kajian'),
                                                'type' => $item['type'] ?? 'pekanan',
                                                'speaker_name' => $item['speaker_name'] ?: ($item['khatib_name'] ?: 'Asatidz'),
                                                'date' => !empty($item['date']) ? (\Carbon\Carbon::parse($item['date'])->translatedFormat('l, d F Y')) : '',
                                                'time_display' => $item['time_display'] ?? '',
                                                'notula' => $item['notula'],
                                            ]) }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-gov-50 hover:bg-gov-100 text-gov-navy border border-gov-border font-semibold text-xs shadow-2xs transition cursor-pointer shrink-0"
                                            title="Baca Risalah Notula Kajian">
                                            <svg class="w-3.5 h-3.5 text-gov-navy shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                                <path d="M6 6h10"/><path d="M6 10h10"/><path d="M6 14h6"/>
                                            </svg>
                                            <span>Notula</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-4 w-full">
                {{ $items->links() }}
            </div>

            <!-- Card Legend -->
            <div class="p-3.5 bg-slate-50/70 border border-gov-border rounded-xl text-xs text-slate-500 flex items-center gap-2">
                <span class="w-3 h-3 rounded bg-amber-200 border border-amber-300 inline-block shrink-0"></span>
                <span>Kartu kuning menandakan jadwal kajian selanjutnya atau kegiatan pada pekan berjalan.</span>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white p-12 rounded-xl text-center text-slate-400 border border-dashed border-gov-border space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto text-slate-400">
                    <i data-lucide="calendar-x" class="w-6 h-6"></i>
                </div>
                <h3 class="text-sm font-bold text-gov-textMain">Tidak ada kegiatan yang cocok dengan filter</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                    Tidak ditemukan jadwal ibadah atau kegiatan pada filter pencarian atau periode bulan yang dipilih.
                </p>
                <div class="pt-2">
                    <button type="button" wire:click="resetFilters"
                            class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-2 cursor-pointer">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        <span>Tampilkan Semua Kegiatan</span>
                    </button>
                </div>
            </div>
        @endif

    </main>

    @script
    <script>
        $wire.hook('morph.updated', () => {
            if (window.createLucideIcons) window.createLucideIcons();
        });
    </script>
    @endscript

    <!-- ======================================================== -->
    <!-- MODAL: NOTULA KAJIAN PUBLIK (PORTAL JAMAAH) -->
    <!-- ======================================================== -->
    <div x-show="showPortalNotulaModal" x-cloak wire:ignore @keydown.escape.window="showPortalNotulaModal = false"
        @click="if (window.isBackdropClick ? window.isBackdropClick($event, $el) : $event.target === $el) showPortalNotulaModal = false"
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 md:p-6"
        style="display: none;">
        <div class="w-full max-w-4xl bg-gov-950 rounded-2xl shadow-2xl border border-slate-800/80 overflow-hidden flex flex-col h-[92vh] max-h-[92vh] outline-none animate-in fade-in duration-200 transition-all">
            <!-- Top Action Bar -->
            <header class="no-print bg-gov-950 text-white px-4 sm:px-6 py-3 flex items-center justify-between gap-3 border-b border-gov-900 sticky top-0 z-30 shadow-xs shrink-0">
                <div class="flex items-center gap-2 min-w-0 max-w-[60%]">
                    <svg class="w-4 h-4 text-gov-300 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                        <path d="M6 6h10"/><path d="M6 10h10"/><path d="M6 14h6"/>
                    </svg>
                    <span class="text-xs sm:text-sm font-bold text-white truncate" x-text="portalNotulaData.title || 'Notula Kajian'"></span>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Zoom Buttons -->
                    <div class="flex items-center bg-gov-900 rounded-lg p-0.5 border border-slate-700/70 text-slate-300">
                        <button type="button" @click="adjustPortalNotulaFontSize(-1)" title="Perkecil Teks (A-)" class="p-1.5 hover:text-white hover:bg-gov-800 rounded-md transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <span class="text-[11px] px-1.5 font-mono font-medium text-slate-300" x-text="['A-', 'A', 'A+', 'A++'][portalNotulaFontSizeIndex]"></span>
                        <button type="button" @click="adjustPortalNotulaFontSize(1)" title="Perbesar Teks (A+)" class="p-1.5 hover:text-white hover:bg-gov-800 rounded-md transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                    </div>

                    <!-- Print / PDF -->
                    <button type="button" 
                        @click="window.printNotulaDocument ? window.printNotulaDocument(window.parseNotulaToHtml(portalNotulaData.notula, portalNotulaData), portalNotulaData.title || 'Notula Kajian') : window.print()" 
                        class="inline-flex items-center gap-1.5 bg-gov-700 hover:bg-gov-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg border border-gov-600/60 shadow-xs transition cursor-pointer"
                        title="Cetak / PDF">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                        <span class="hidden sm:inline">Cetak /</span><span>PDF</span>
                    </button>

                    <!-- Tutup -->
                    <button type="button" @click="showPortalNotulaModal = false"
                        class="inline-flex items-center gap-1 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-medium px-2.5 py-1.5 rounded-lg border border-slate-700 transition cursor-pointer">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span class="hidden sm:inline">Tutup</span>
                    </button>
                </div>
            </header>

            <!-- Document Body -->
            <div class="min-h-0 flex-1 overflow-y-auto bg-slate-100 p-3 sm:p-6 md:p-8 flex justify-center notula-scrollable">
                <div class="w-full max-w-3xl sm:max-w-4xl bg-white rounded-xl sm:rounded-2xl shadow-md border border-slate-200/80 overflow-hidden flex flex-col h-fit">
                    <main 
                        class="p-5 sm:p-8 md:p-11 space-y-6 sm:space-y-7 leading-relaxed text-slate-700"
                        :class="['text-sm', 'text-base', 'text-lg', 'text-xl'][portalNotulaFontSizeIndex]"
                        x-html="window.parseNotulaToHtml ? window.parseNotulaToHtml(portalNotulaData.notula, portalNotulaData) : portalNotulaData.notula">
                    </main>
                </div>
            </div>
        </div>
    </div>
</div>
