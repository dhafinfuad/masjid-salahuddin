<div class="min-h-screen flex flex-col bg-gov-canvas text-gov-textMain selection:bg-gov-navy selection:text-white pb-16"
     x-data="{
         openCityModal: false,
         searchCity: '',
         selectedCityId: '{{ $cityId }}',
         selectedCityName: '{{ addslashes(ucwords(strtolower($cityName))) }}',
         formatCity(name) {
             if (!name) return '';
             return name.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
         },
         popularCities: [
             { id: '1634', lokasi: 'Kota Malang' },
             { id: '1638', lokasi: 'Kota Surabaya' },
             { id: '1301', lokasi: 'Kota Jakarta' },
             { id: '1203', lokasi: 'Kota Bandung' },
             { id: '1609', lokasi: 'Kab. Malang' },
             { id: '1635', lokasi: 'Kota Batu' }
         ],
         allCities: {{ Js::from($cities) }},
         get filteredCities() {
             const q = this.searchCity.toLowerCase().trim();
             if (!q) return this.popularCities;
             return this.allCities.filter(c => c.lokasi.toLowerCase().includes(q)).slice(0, 50);
         },
         selectCity(id, name) {
             this.selectedCityId = id;
             this.selectedCityName = name;
             this.openCityModal = false;
             $wire.selectCity(id, name);
         },
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
    <!-- Top Sticky Header / Status Bar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-gov-border sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" wire:navigate.hover class="flex items-end gap-3 hover:opacity-90 transition group">
                <img 
                    src="{{ asset('resources/Logo Masjid Salahuddin.webp') }}" 
                    alt="Logo {{ $settings->name }}" 
                    class="h-10 sm:h-11 w-auto object-contain shrink-0 transition-transform duration-200 group-hover:scale-105"
                    onerror="this.onerror=null; this.src='/masjid-salahuddin/resources/Logo%20Masjid%20Salahuddin.webp';"
                >
                <div>
                    <h1 class="text-base font-bold tracking-tight text-gov-textMain leading-tight">{{ $settings->name }}</h1>
                    <p class="text-xs text-slate-500 font-medium">KPP Madya Malang</p>
                </div>
            </a>

            <!-- Tombol Login -->
            @auth
                <!-- Mobile View -->
                <a href="{{ route('admin.dashboard') }}" wire:navigate.hover 
                    class="sm:hidden px-4 py-2 rounded-lg text-gov-navy transition cursor-pointer inline-flex items-center justify-center gap-2" 
                    title="Buka Panel Dashboard Admin">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="layout-dashboard" aria-hidden="true" class="lucide lucide-layout-dashboard w-5 h-5"><rect width="7" height="9" x="3" y="3" rx="1"></rect><rect width="7" height="5" x="14" y="3" rx="1"></rect><rect width="7" height="9" x="14" y="12" rx="1"></rect><rect width="7" height="5" x="3" y="16" rx="1"></rect></svg>
                </a>

                <!-- Desktop View -->
                <a href="{{ route('admin.dashboard') }}" wire:navigate.hover 
                    class="hidden sm:inline-flex px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs sm:text-sm shadow-2xs transition cursor-pointer items-center justify-center gap-2"
                    title="Buka Panel Dashboard Admin">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate.hover 
                    class="p-2 font-bold transition cursor-pointer"
                    title="Login Pengurus & Petugas DKM">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        
        <!-- Clean Hero Banner with Corporate Islamic Navy Pattern -->
        <div class="bg-gov-navy text-white rounded-xl p-5 sm:p-7 relative overflow-hidden shadow-xs bg-gov-pattern border border-gov-navyDark">
            <!-- Top Label -->
            <div class="flex items-center space-x-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-sm text-amber-300 font-medium">{{ $hijriDate }}</span>
            </div>

            <!-- Mosque Title & Description -->
            <h2 class="text-2xl sm:text-4xl font-bold text-white tracking-tight mt-1">{{ $settings->name }}</h2>
            <p class="text-xs sm:text-sm text-slate-200 font-normal leading-relaxed mt-2 max-w-2xl">{{ $settings->description ?: 'Menebar Manfaat, Meraih Berkah' }}</p>

            <!-- Quick Navigation Tabs (Responsive grid on Mobile, Flex on Desktop) -->
            <div class="mt-5 pt-1 grid grid-cols-3 gap-2 sm:flex sm:flex-wrap sm:gap-2.5">
                <a href="#kegiatan-masjid" class="px-2.5 sm:px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 flex items-center justify-center gap-1.5 sm:gap-2 transition shadow-2xs text-center">
                    <i data-lucide="calendar" class="w-4 h-4 text-amber-400 shrink-0"></i>
                    <span class="truncate">Kegiatan</span>
                </a>
                <a href="#program-sosial" class="px-2.5 sm:px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 flex items-center justify-center gap-1.5 sm:gap-2 transition shadow-2xs text-center">
                    <i data-lucide="heart-handshake" class="w-4 h-4 text-amber-400 shrink-0"></i>
                    <span class="truncate">Program Sosial</span>
                </a>
            </div>
        </div>

        <!-- Main Content Area: Responsive Grid (Mobile: Vertical Stack via order, Desktop: 2 Balanced Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN (Desktop: 8 cols, Mobile: contents) -->
            <div class="contents lg:flex lg:flex-col lg:gap-6 lg:col-span-8">
                <!-- CARD 1: Jadwal Sholat -->
                            <section id="jadwal-sholat" class="order-1 w-full bg-white rounded-xl p-4 sm:p-6 border border-gov-border shadow-2xs space-y-2">
                                    <!-- Header Info Row -->
                                    <div class="flex flex-wrap items-start justify-between gap-4 pb-1">
                                        <div>
                                            <div class="flex items-center gap-2 text-xs font-semibold tracking-wider text-slate-500 uppercase">
                                                <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span>SHOLAT SELANJUTNYA</span>
                                            </div>
                                            <h3 class="text-2xl sm:text-[32px] font-bold text-gov-textMain mt-2">{{ $nextPrayer['prayer']['name'] ?? 'Subuh' }}</h3>
                                            <p class="text-xs sm:text-sm font-medium text-amber-600 mt-0.5" id="subuh-countdown">{{ $nextPrayer['countdown_text'] ?? 'Menuju Waktu Sholat' }}</p>
                                        </div>
                
                                        <!-- Location Dropdown Selector & Selengkapnya -->
                                        <div class="flex items-center gap-2">
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gov-border bg-slate-50 text-xs font-medium text-gov-textMain hover:bg-slate-100 cursor-pointer transition shadow-2xs"
                                                 @click="openCityModal = true">
                                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-500"></i>
                                                <span x-text="formatCity(selectedCityName)">{{ ucwords(strtolower($cityName)) }}</span>
                                                <i data-lucide="search" class="w-3 h-3 text-slate-400 ml-1"></i>
                                            </div>
                
                                            <a href="{{ route('portal.jadwal-sholat') }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold transition shadow-2xs cursor-pointer">
                                                <span>Selengkapnya</span>
                                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                            </a>
                                        </div>
                                    </div>
                
                                    <!-- Prayer Times Grid (5 Cards in 1 Row) -->
                                    <div class="grid grid-cols-5 gap-1.5 sm:gap-3 pt-2 tnum">
                                        @php
                                            $hasAnyOngoingPortal = false;
                                            foreach ($prayers as $p) {
                                                if (!empty($p['is_ongoing'])) {
                                                    $hasAnyOngoingPortal = true;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @foreach($prayers as $prayer)
                                            @php
                                                $isActive = isset($prayer['is_active'])
                                                    ? (bool)$prayer['is_active']
                                                    : ($hasAnyOngoingPortal ? !empty($prayer['is_ongoing']) : !empty($prayer['is_next']));
                                            @endphp
                                            @if($isActive)
                                                <!-- Active Next Prayer (Amber Highlighted) -->
                                                <div class="bg-amber-400 text-gov-navy rounded-lg p-1.5 sm:p-3.5 text-center shadow-xs border border-amber-500 ring-1 sm:ring-2 ring-amber-400/30 flex flex-col justify-center">
                                                    <span class="text-[9px] min-[360px]:text-[10px] sm:text-xs uppercase text-gov-navy font-bold tracking-tight block truncate">{{ strtoupper($prayer['name']) }}</span>
                                                    <span class="font-extrabold text-gov-navy text-xs min-[360px]:text-sm sm:text-2xl block mt-0.5 sm:mt-1 tracking-tight">{{ $prayer['adzan'] }}</span>
                                                </div>
                                            @else
                                                <!-- Normal Prayer -->
                                                <div class="bg-slate-50 hover:bg-white border border-gov-border rounded-lg p-1.5 sm:p-3.5 text-center transition flex flex-col justify-center">
                                                    <span class="text-[9px] min-[360px]:text-[10px] sm:text-xs uppercase text-slate-500 block font-medium tracking-tight truncate">{{ strtoupper($prayer['name']) }}</span>
                                                    <span class="font-bold text-gov-textMain text-xs min-[360px]:text-sm sm:text-2xl block mt-0.5 sm:mt-1 tracking-tight">{{ $prayer['adzan'] }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </section>

                <!-- CARD 2: Kegiatan Masjid -->
                            <section id="kegiatan-masjid" 
                                     class="order-3 w-full bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-2"
                                         x-data="{
                                             activeFilter: 'all',
                                             searchQuery: '',
                                             kajians: {{ Js::from($kajians->map(fn($k) => [
                                                 'id' => $k->id,
                                                 'type' => $k->type,
                                                 'title' => $k->title,
                                                 'speaker' => $k->type === 'jumat' ? ($k->khatib_name ?: 'Khatib Shalat Jumat') : ($k->speaker_name ?: 'Pemateri Kajian'),
                                                 'search' => strtolower($k->title . ' ' . ($k->speaker_name ?? '') . ' ' . ($k->khatib_name ?? '') . ' ' . ($k->muadzin_name ?? '') . ' ' . ($k->mc_name ?? '') . ' ' . ($k->time_display ?? '') . ' ' . ($k->type === 'tematik' ? 'tematik' : ($k->type === 'pekanan' ? 'pekanan' : 'jumat khutbah')))
                                             ])) }},
                                             agendas: {{ Js::from($agendas->map(fn($a) => [
                                                 'id' => $a->id,
                                                 'title' => $a->title,
                                                 'description' => $a->description,
                                                 'status' => $a->status,
                                                 'search' => strtolower($a->title . ' ' . ($a->description ?? '') . ' ' . ($a->status ?? ''))
                                             ])) }},
                                             get visibleKajianCount() {
                                                 if (this.activeFilter === 'akbar') return 0;
                                                 const q = this.searchQuery.toLowerCase().trim();
                                                 return this.kajians.filter(k => {
                                                     const typeMatch = (this.activeFilter === 'all' || 
                                                         ((this.activeFilter === 'kajian' || this.activeFilter === 'pekanan') && k.type !== 'jumat') ||
                                                         (this.activeFilter === 'khutbah' && k.type === 'jumat'));
                                                     if (!typeMatch) return false;
                                                     if (!q) return true;
                                                     return k.search.includes(q);
                                                 }).length;
                                             },
                                             get visibleAgendaCount() {
                                                 if (this.activeFilter === 'kajian' || this.activeFilter === 'pekanan' || this.activeFilter === 'khutbah') return 0;
                                                 const q = this.searchQuery.toLowerCase().trim();
                                                 return this.agendas.filter(a => {
                                                     if (!q) return true;
                                                     return a.search.includes(q);
                                                 }).length;
                                             },
                                             get visibleCount() {
                                                 return this.visibleKajianCount + this.visibleAgendaCount;
                                             },
                                             isKajianMatch(type, searchStr) {
                                                 if (this.activeFilter === 'akbar') return false;
                                                 const typeMatch = (this.activeFilter === 'all' || 
                                                     ((this.activeFilter === 'kajian' || this.activeFilter === 'pekanan') && type !== 'jumat') ||
                                                     (this.activeFilter === 'khutbah' && type === 'jumat'));
                                                 if (!typeMatch) return false;
                                                 const q = this.searchQuery.toLowerCase().trim();
                                                 if (!q) return true;
                                                 return searchStr.toLowerCase().includes(q);
                                             },
                                             isAgendaMatch(searchStr) {
                                                 if (this.activeFilter === 'kajian' || this.activeFilter === 'pekanan' || this.activeFilter === 'khutbah') return false;
                                                 const q = this.searchQuery.toLowerCase().trim();
                                                 if (!q) return true;
                                                 return searchStr.toLowerCase().includes(q);
                                             }
                                         }"
                                >
                                    <!-- Top Row Title & Search -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                                                <i data-lucide="calendar-range" class="w-5 h-5"></i>
                                            </div>
                                            <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Kegiatan Masjid</h3>
                                        </div>
                                        
                                        <div class="flex items-center gap-2.5">
                                            <div class="relative w-full sm:w-64">
                                                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                                                <input 
                                                    type="text" 
                                                    x-model="searchQuery"
                                                    id="activity-search"
                                                    placeholder="Cari kegiatan masjid..." 
                                                    class="h-[32px] w-full pl-8 pr-8 py-1 text-xs bg-slate-50 border border-gov-border rounded-lg text-gov-textMain focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition"
                                                >
                                                <button x-show="searchQuery.length > 0" 
                                                        @click="searchQuery = ''" 
                                                        type="button" 
                                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 rounded transition cursor-pointer"
                                                        title="Hapus pencarian">
                                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                
                                    <!-- Category Filter Buttons -->
                                    <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-slate-100">
                                        <button type="button" @click="activeFilter = 'all'" 
                                                :class="activeFilter === 'all' ? 'bg-gov-navy text-white shadow-2xs font-semibold' : 'bg-white border border-gov-border text-slate-600 hover:bg-slate-50 font-medium'"
                                                class="tab-btn px-3 py-1.5 text-xs rounded-lg transition cursor-pointer">
                                            Semua
                                        </button>
                                        <button type="button" @click="activeFilter = 'kajian'" 
                                                :class="(activeFilter === 'kajian' || activeFilter === 'pekanan') ? 'bg-gov-navy text-white shadow-2xs font-semibold' : 'bg-white border border-gov-border text-slate-600 hover:bg-slate-50 font-medium'"
                                                class="tab-btn px-3 py-1.5 text-xs rounded-lg transition cursor-pointer">
                                            Kajian
                                        </button>
                                        <button type="button" @click="activeFilter = 'khutbah'" 
                                                :class="activeFilter === 'khutbah' ? 'bg-gov-navy text-white shadow-2xs font-semibold' : 'bg-white border border-gov-border text-slate-600 hover:bg-slate-50 font-medium'"
                                                class="tab-btn px-3 py-1.5 text-xs rounded-lg transition cursor-pointer">
                                            Khutbah Jumat
                                        </button>
                                        <button type="button" @click="activeFilter = 'akbar'" 
                                                :class="activeFilter === 'akbar' ? 'bg-gov-navy text-white shadow-2xs font-semibold' : 'bg-white border border-gov-border text-slate-600 hover:bg-slate-50 font-medium'"
                                                class="tab-btn px-3 py-1.5 text-xs rounded-lg transition cursor-pointer">
                                            Kegiatan Akbar
                                        </button>
                                    </div>
                
                                    <!-- Activity Cards Grid (Desktop: 2 Columns) -->
                                    <div id="activity-cards-grid" class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-1">
                                        
                                        <!-- Real Kajian Cards (Pekanan & Tematik & Jumat) -->
                                        @foreach($kajians as $kajian)
                                            <div x-show="isKajianMatch('{{ $kajian->type }}', '{{ strtolower(addslashes($kajian->title . ' ' . ($kajian->speaker_name ?? '') . ' ' . ($kajian->khatib_name ?? '') . ' ' . ($kajian->muadzin_name ?? '') . ' ' . ($kajian->mc_name ?? '') . ' ' . ($kajian->time_display ?? '') . ' ' . ($kajian->type === 'tematik' ? 'tematik' : ($kajian->type === 'pekanan' ? 'pekanan' : 'jumat khutbah')))) }}')"
                                                 class="activity-card group bg-slate-50/70 hover:bg-white border border-gov-border rounded-xl p-3.5 transition hover:shadow-2xs space-y-2 flex flex-col justify-between"
                                                 data-category="{{ $kajian->type === 'jumat' ? 'khutbah' : 'kajian' }}"
                                                 data-title="{{ strtolower($kajian->title) }}">
                                                <div class="space-y-2">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none whitespace-nowrap shrink-0 {{ $kajian->type === 'jumat' ? 'bg-amber-50 text-amber-800 border-amber-200' : ($kajian->type === 'tematik' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200') }}">
                                                            {{ $kajian->type === 'jumat' ? 'Khutbah Jumat' : ($kajian->type === 'tematik' ? 'Tematik' : 'Pekanan') }}
                                                        </span>
                                                        <span class="text-xs font-semibold text-slate-600 whitespace-nowrap shrink-0">
                                                            {{ $kajian->date->translatedFormat('d M Y') }} • {{ $kajian->time_display ?? '11:45 WIB' }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        @if($kajian->type === 'jumat')
                                                            <h4 class="text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition leading-snug">
                                                                {{ $kajian->khatib_name ?: ($kajian->title ?: 'Khatib Shalat Jumat') }}
                                                            </h4>
                                                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                                                <span>Muadzin: <strong class="text-slate-600 font-medium">{{ $kajian->muadzin_name ?: '-' }}</strong></span>
                                                                <span class="mx-1">•</span>
                                                                <span>MC: <strong class="text-slate-600 font-medium">{{ $kajian->mc_name ?: '-' }}</strong></span>
                                                            </p>
                                                        @else
                                                            <h4 class="text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition leading-snug">
                                                                {{ $kajian->title }}
                                                            </h4>
                                                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                                                {{ $kajian->speaker_name ?: 'Pemateri Kajian' }}
                                                                @if($kajian->location) • {{ $kajian->location }} @endif
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                
                                                @if($kajian->type === 'jumat' || !empty($kajian->notula) || !empty($kajian->youtube_url))
                                                    <div class="pt-2.5 border-t border-slate-100 flex items-center justify-end mt-auto">
                                                        <div>
                                                            @if($kajian->type === 'jumat')
                                                                <a href="{{ route('admin.kajian.teks-mc', $kajian->id) }}" target="_blank"
                                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-gov-50 hover:bg-gov-100 text-gov-navy border border-gov-border font-semibold text-xs shadow-2xs transition cursor-pointer shrink-0"
                                                                    title="Download & Cetak Teks MC Sholat Jumat (PDF)">
                                                                    <i data-lucide="printer" class="w-3.5 h-3.5 text-gov-navy shrink-0"></i>
                                                                    <span>Teks MC</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            @if(!empty($kajian->youtube_url))
                                                                <a href="{{ $kajian->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 border border-rose-200 font-semibold text-xs shadow-2xs transition cursor-pointer shrink-0"
                                                                    title="Tonton Siaran Kajian di YouTube">
                                                                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                                    </svg>
                                                                    <span>YouTube</span>
                                                                </a>
                                                            @endif
                                                            @if(!empty($kajian->notula))
                                                                <button type="button"
                                                                    @click="openPortalNotula({{ json_encode([
                                                                        'id' => $kajian->id,
                                                                        'title' => $kajian->title ?: ($kajian->type === 'jumat' ? 'Khutbah Jumat' : 'Kajian'),
                                                                        'type' => $kajian->type,
                                                                        'speaker_name' => $kajian->speaker_name ?: ($kajian->khatib_name ?: 'Asatidz'),
                                                                        'date' => $kajian->date ? $kajian->date->translatedFormat('l, d F Y') : '',
                                                                        'time_display' => $kajian->time_display ?: '',
                                                                        'notula' => $kajian->notula,
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
                
                                        <!-- Real Agenda Cards (Kegiatan Akbar) -->
                                        @foreach($agendas as $agenda)
                                            <div x-show="isAgendaMatch('{{ strtolower(addslashes($agenda->title . ' ' . ($agenda->description ?? '') . ' ' . ($agenda->status ?? ''))) }}')"
                                                 class="activity-card group bg-slate-50/70 hover:bg-white border border-gov-border rounded-xl p-3.5 transition hover:shadow-2xs space-y-2 flex flex-col justify-between"
                                                 data-category="akbar"
                                                 data-title="{{ strtolower($agenda->title . ' ' . ($agenda->description ?? '')) }}">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none {{ $agenda->status === 'SELESAI' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                                                        {{ ucfirst(strtolower($agenda->status ?: 'Aktif')) }}
                                                    </span>
                                                    <span class="text-xs font-semibold text-slate-600">
                                                        {{ $agenda->budget ? 'Rp ' . number_format($agenda->budget, 0, ',', '.') : ($agenda->event_date ? $agenda->event_date->translatedFormat('d M Y') : '-') }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition leading-snug">
                                                        {{ $agenda->title }}
                                                    </h4>
                                                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                                        {{ $agenda->description }}
                                                    </p>
                                                </div>
                                                @if(!empty($agenda->youtube_url))
                                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end mt-auto">
                                                        <a href="{{ $agenda->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 border border-rose-200 font-semibold text-xs shadow-2xs transition cursor-pointer shrink-0"
                                                            title="Tonton Siaran Agenda di YouTube">
                                                            <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                            </svg>
                                                            <span>YouTube</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                
                                    </div>
                
                                    <!-- Empty State When Filter Returns 0 -->
                                    <div x-show="visibleCount === 0" 
                                         class="bg-white p-8 rounded-xl text-center text-slate-400 border border-dashed border-gov-border"
                                         style="display: none;">
                                        <i data-lucide="calendar-x" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                        <p class="text-xs font-semibold text-gov-textMain">Tidak ada kegiatan yang cocok dengan filter atau kata kunci ini.</p>
                                        <p class="text-xs text-slate-500 mt-1">Coba hapus kata pencarian atau pilih tab "Semua".</p>
                                    </div>

                                    <!-- Footer Action: Selengkapnya -->
                                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <p class="text-xs text-slate-500">
                                            Menampilkan jadwal kegiatan terdekat. Buka kalender lengkap untuk melihat seluruh agenda & filter bulan.
                                        </p>
                                        <a href="{{ route('portal.kegiatan') }}" wire:navigate 
                                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition cursor-pointer shrink-0"
                                           title="Buka Halaman Kegiatan Masjid">
                                            <span>Selengkapnya</span>
                                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                        </a>
                                    </div>
                                </section>

                <!-- CARD 3: Program Sosial & Komitmen Jamaah -->
                            <section id="program-sosial" class="order-5 w-full bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-6">
                                    
                                    <!-- Header Section -->
                                    <div class="flex items-start gap-3 pb-4 border-b border-slate-100">
                                        <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                                            <i data-lucide="heart-handshake" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Program Sosial & Komitmen Jamaah</h3>
                                            <p class="text-xs text-slate-500 mt-0.5">Penyaluran amanah berkala: santunan anak yatim, infaq rutin pegawai, dan zakat mal</p>
                                        </div>
                                    </div>
                
                                    <!-- Programs 2x2 Grid (Desktop) -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                        
                                        @forelse($socialPrograms as $prog)
                                            @php
                                                $iconName = 'heart-handshake';
                                                if (stripos($prog->name, 'yatim') !== false) $iconName = 'heart';
                                                elseif (stripos($prog->name, 'infaq') !== false) $iconName = 'wallet';
                                                elseif (stripos($prog->name, 'zakat') !== false) $iconName = 'coins';
                                                elseif (stripos($prog->name, 'qurban') !== false) $iconName = 'scale';
                                                elseif (!empty($prog->icon)) $iconName = $prog->icon;
                
                                                $collected = $prog->total_collected;
                                                $participants = $prog->active_participants_count;
                                            @endphp
                
                                            <!-- Program Card -->
                                            <div class="border border-gov-border rounded-xl p-4 bg-white hover:border-slate-400 transition flex flex-col justify-between space-y-3">
                                                <div>
                                                    <div class="flex items-center gap-2.5">
                                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                                                            <i data-lucide="{{ $iconName }}" class="w-4 h-4"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-xs sm:text-sm font-bold text-gov-textMain">{{ $prog->name }}</h4>
                                                            <p class="text-[11px] text-slate-500"><strong class="font-semibold text-slate-700">{{ $participants }}</strong> Peserta Aktif</p>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-slate-500 mt-2.5 leading-relaxed">
                                                        {{ $prog->description }}
                                                    </p>
                                                </div>
                
                                                <!-- Program Metrics -->
                                                <div class="pt-2 border-t border-slate-100 flex justify-between items-center text-xs">
                                                    <span class="text-slate-500">Terkumpul</span>
                                                    <span class="font-semibold text-gov-textMain">Rp {{ number_format($collected, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-span-full py-8 text-center text-slate-400 text-xs">
                                                Belum ada program sosial yang aktif saat ini.
                                            </div>
                                        @endforelse
                
                                    </div>
                
                                    <!-- Footer Action: Daftar Sekarang -->
                                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <p class="text-xs text-slate-500">
                                            Tertarik berkontribusi atau mendaftar komitmen rutin program sosial & dakwah masjid?
                                        </p>
                                        <button type="button" 
                                            @click="{{ auth()->check() ? 'window.location.href = \'' . url('/admin/programs') . '\'' : '$dispatch(\'toast\', { message: \'Silakan login terlebih dahulu untuk mendaftar!\' })' }}"
                                            class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
                                            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                                            <span>Daftar Sekarang</span>
                                        </button>
                                    </div>
                
                                </section>
            </div>

            <!-- RIGHT COLUMN (Desktop: 4 cols, Mobile: contents) -->
            <div class="contents lg:flex lg:flex-col lg:gap-6 lg:col-span-4">
                <!-- CARD 1: Petugas Shalat -->
                            <section class="order-2 w-full bg-gov-navy text-white rounded-xl p-5 relative overflow-hidden shadow-xs bg-gov-pattern border border-gov-navyDark space-y-4">
                                    <div class="flex items-center justify-between gap-2 border-b border-white/15 pb-3">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar" class="w-4 h-4 text-amber-400"></i>
                                            <h3 class="text-xs font-bold uppercase tracking-wider text-white leading-snug">{{ $dutyTitle }}</h3>
                                        </div>
                                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-white/10 text-amber-300 border border-white/20 leading-none whitespace-nowrap">
                                            {{ $dutyDate->translatedFormat('l, d F Y') }}
                                        </span>
                                    </div>
                
                                    <!-- Petugas Roles Stack -->
                                    @if($isFridayDuty && !$isFridayAsharDuty)
                                        <div class="space-y-2.5">
                                            <!-- Khatib & Imam -->
                                            <div class="bg-white/10 backdrop-blur-xs rounded-lg p-2.5 border border-white/15">
                                                <div class="text-[10px] font-semibold tracking-wider uppercase text-amber-300">KHATIB &amp; IMAM</div>
                                                <div class="text-xs text-white mt-0.5 truncate" title="{{ $fridayKhatib }}">{{ $fridayKhatib }}</div>
                                            </div>
                
                                            <!-- MC / Protokol -->
                                            <div class="bg-white/10 backdrop-blur-xs rounded-lg p-2.5 border border-white/15">
                                                <div class="text-[10px] font-semibold tracking-wider uppercase text-amber-300">MC / PROTOKOL</div>
                                                <div class="text-xs text-white mt-0.5 truncate" title="{{ $fridayMc }}">{{ $fridayMc }}</div>
                                            </div>
                
                                            <!-- Bilal / Muadzin -->
                                            <div class="bg-white/10 backdrop-blur-xs rounded-lg p-2.5 border border-white/15">
                                                <div class="text-[10px] font-semibold tracking-wider uppercase text-amber-300">BILAL / MUADZIN</div>
                                                <div class="text-xs text-white mt-0.5 truncate" title="{{ $fridayMuadzin }}">{{ $fridayMuadzin }}</div>
                                            </div>
                                        </div>
                                    @elseif($isFridayDuty && $isFridayAsharDuty)
                                        <div class="space-y-2.5">
                                            <!-- Imam Shalat Ashar -->
                                            <div class="bg-white/10 backdrop-blur-xs rounded-lg p-2.5 border border-white/15">
                                                <div class="text-[10px] font-semibold tracking-wider uppercase text-amber-300">IMAM SHALAT ASHAR</div>
                                                <div class="text-xs text-white mt-0.5 truncate" title="{{ $asharDuty?->imam_name ?: '-' }}">{{ $asharDuty?->imam_name ?: '-' }}</div>
                                            </div>

                                            <!-- Bilal / Muadzin -->
                                            <div class="bg-white/10 backdrop-blur-xs rounded-lg p-2.5 border border-white/15">
                                                <div class="text-[10px] font-semibold tracking-wider uppercase text-amber-300">BILAL / MUADZIN</div>
                                                <div class="text-xs text-white mt-0.5 truncate" title="{{ $asharDuty?->muadzin_name ?: '-' }}">{{ $asharDuty?->muadzin_name ?: '-' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="space-y-2.5">
                                            <!-- Sholat Dzuhur -->
                                            <div class="bg-white/10 backdrop-blur-xs rounded-lg p-2.5 border border-white/15">
                                                <div class="flex items-center justify-between pb-1 border-b border-white/10">
                                                    <span class="text-[10px] font-bold tracking-wider uppercase text-amber-300">SHOLAT DZUHUR</span>
                                                    <span class="text-[10px] text-amber-200/70">11:45 WIB</span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2 mt-1.5">
                                                    <div>
                                                        <span class="text-[10px] uppercase tracking-wider text-slate-300 block font-medium">Imam</span>
                                                        <span class="text-xs font-semibold text-white truncate block mt-0.5" title="{{ $dzuhurDuty?->imam_name ?: '-' }}">{{ $dzuhurDuty?->imam_name ?: '-' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-[10px] uppercase tracking-wider text-slate-300 block font-medium">Muadzin</span>
                                                        <span class="text-xs font-semibold text-white truncate block mt-0.5" title="{{ $dzuhurDuty?->muadzin_name ?: '-' }}">{{ $dzuhurDuty?->muadzin_name ?: '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                
                                            <!-- Sholat Ashar -->
                                            <div class="bg-white/10 backdrop-blur-xs rounded-lg p-2.5 border border-white/15">
                                                <div class="flex items-center justify-between pb-1 border-b border-white/10">
                                                    <span class="text-[10px] font-bold tracking-wider uppercase text-amber-300">SHOLAT ASHAR</span>
                                                    <span class="text-[10px] text-slate-300/70">15:00 WIB</span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2 mt-1.5">
                                                    <div>
                                                        <span class="text-[10px] uppercase tracking-wider text-slate-300 block font-medium">Imam</span>
                                                        <span class="text-xs font-semibold text-white truncate block mt-0.5" title="{{ $asharDuty?->imam_name ?: '-' }}">{{ $asharDuty?->imam_name ?: '-' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-[10px] uppercase tracking-wider text-slate-300 block font-medium">Muadzin</span>
                                                        <span class="text-xs font-semibold text-white truncate block mt-0.5" title="{{ $asharDuty?->muadzin_name ?: '-' }}">{{ $asharDuty?->muadzin_name ?: '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                
                                    <!-- Footer Action: Selengkapnya -->
                                    <div class="pt-3 border-t border-white/15 flex justify-end">
                                        <a href="{{ route('portal.petugas-sholat') }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 transition shadow-2xs cursor-pointer">
                                            <span>Selengkapnya</span>
                                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                        </a>
                                    </div>
                                </section>

                <!-- CARD 2: Infaq & Sedekah Masjid -->
                            <section class="order-4 w-full bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-3">
                                    <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                                        <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                                            <i data-lucide="wallet" class="w-5 h-5"></i>
                                        </div>
                                        <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Infaq & Sedekah Masjid</h3>
                                    </div>
                
                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        Dukung kemakmuran operasional masjid dan santunan anak yatim melalui transfer rekening resmi DKM {{ $settings->name }}:
                                    </p>
                
                                    <div class="space-y-2.5 pt-1">
                                        <!-- Kode QR QRIS Infaq -->
                                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col items-center text-center space-y-2">
                                            <div class="flex items-center justify-between w-full pb-2 border-b border-slate-200/60">
                                                <div class="flex items-center gap-1.5">
                                                    <i data-lucide="qr-code" class="w-4 h-4 text-gov-navy"></i>
                                                    <span class="text-xs font-bold uppercase text-gov-textMain tracking-wide">QRIS Infaq Digital</span>
                                                </div>
                                            </div>
                                            <div class="w-full max-w-[200px] sm:max-w-[220px] mx-auto bg-white p-2 rounded-lg border border-gov-border shadow-2xs">
                                                <img 
                                                    src="{{ asset('images/QRIS.webp') }}" 
                                                    alt="QRIS Infaq Masjid Salahuddin" 
                                                    class="w-full h-auto object-contain rounded"
                                                    loading="lazy"
                                                    onerror="this.onerror=null; this.src='/masjid-salahuddin/resources/QRIS.webp';"
                                                >
                                            </div>
                                            <p class="text-[11px] text-slate-500 leading-tight">
                                                Scan via Mobile Banking (BCA, Mandiri, BSI, dll) atau E-Wallet (GoPay, OVO, ShopeePay, DANA)
                                            </p>
                                        </div>
                
                                        <!-- Rekening Bank (BSI) -->
                                        @if(!empty($settings->bank_accounts))
                                            @foreach(array_slice($settings->bank_accounts, 0, 1) as $bank)
                                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3" x-data="{ copied: false }">
                                                    <div class="min-w-0">
                                                        <span class="text-xs font-bold uppercase text-slate-500 block">{{ $bank['bank'] }}</span>
                                                        <span class="text-sm sm:text-base font-bold text-gov-textMain block">{{ $bank['account_number'] }}</span>
                                                        <span class="text-xs text-slate-500 truncate block">a.n. {{ $bank['holder'] }}</span>
                                                    </div>
                                                    <button 
                                                        type="button"
                                                        @click="copyRekening('{{ $bank['account_number'] }}', $el); copied = true; setTimeout(() => copied = false, 2500)" 
                                                        class="shrink-0 px-3 py-1.5 rounded-lg bg-white border border-gov-border hover:bg-slate-50 text-slate-700 text-xs inline-flex items-center justify-center gap-1.5 font-semibold transition shadow-2xs cursor-pointer select-none whitespace-nowrap min-w-[82px]"
                                                        title="Salin Rekening"
                                                    >
                                                        <span x-show="!copied" class="inline-flex items-center gap-1.5 shrink-0 whitespace-nowrap">
                                                            <i data-lucide="copy" class="w-3.5 h-3.5 text-slate-400 shrink-0 inline-block"></i>
                                                            <span class="text-xs font-medium whitespace-nowrap">Salin</span>
                                                        </span>
                                                        <span x-show="copied" x-cloak class="inline-flex items-center gap-1.5 text-emerald-700 font-bold shrink-0 whitespace-nowrap" style="display: none;">
                                                            <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 shrink-0 inline-block"></i>
                                                            <span class="text-xs font-semibold whitespace-nowrap">Tersalin</span>
                                                        </span>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </section>

                <!-- CARD: Kotak Saran & Aspirasi Jamaah -->
                <section class="order-5 w-full bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-3">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-5 h-5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-gov-textMain leading-tight">Kotak Saran & Aspirasi</h3>
                            <p class="text-[11px] text-slate-500">Masukan & Usulan Jamaah</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed">
                        Punya kritik membangun, pengaduan fasilitas/kebersihan, atau usulan kajian untuk kemakmuran bersama? Sampaikan langsung kepada Pengurus Takmir DKM.
                    </p>

                    <div class="pt-1">
                        <a href="{{ route('portal.saran') }}" wire:navigate 
                           class="w-full py-2 px-3 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer">
                            <span>Sampaikan Saran & Kritik</span>
                            <svg class="w-3.5 h-3.5 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </section>

                <!-- CARD 3: Profil Masjid -->
                            <section class="order-6 w-full bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-3 overflow-hidden">
                                    <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                                        <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                                            <i data-lucide="building" class="w-5 h-5"></i>
                                        </div>
                                        <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Profil Masjid</h3>
                                    </div>
                
                                    <div class="space-y-2.5 text-xs">
                                        <!-- Alamat -->
                                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80">
                                            <span class="text-xs uppercase font-bold text-slate-500 block">ALAMAT</span>
                                            <p class="text-gov-textMain font-medium text-xs mt-0.5 leading-relaxed">{{ $settings->address }}</p>
                                            <span class="text-xs text-slate-500 mt-1 block italic">Arah Kiblat: {{ $settings->qibla_angle }}° dari Utara</span>
                                        </div>
                
                                        <!-- Jam Operasional -->
                                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80">
                                            <span class="text-xs uppercase font-bold text-slate-500 block">JAM OPERASIONAL</span>
                                            <p class="text-gov-textMain font-medium text-xs mt-0.5">Setiap hari • 04:00 — 22:00 WIB</p>
                                            <span class="text-xs text-slate-500 mt-1 block">Sholat Jumat: Adzan 11:45 • Khutbah 12:00</span>
                                        </div>
                
                                        <!-- Telepon & Email Grid -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-2">
                                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center gap-2.5 min-w-0">
                                                <i data-lucide="phone" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <span class="text-xs font-bold uppercase text-slate-500 block text-left">TELEPON DKM</span>
                                                    <span class="text-gov-textMain font-medium text-xs text-left block truncate" title="{{ $settings->phone }}">{{ $settings->phone }}</span>
                                                </div>
                                            </div>
                
                                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center gap-2.5 min-w-0">
                                                <i data-lucide="mail" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <span class="text-xs font-bold uppercase text-slate-500 block text-left">EMAIL RESMI</span>
                                                    <a href="mailto:{{ $settings->email }}" class="text-slate-600 hover:text-gov-navy font-medium text-xs truncate block text-left transition" title="{{ $settings->email }}">{{ $settings->email }}</a>
                                                </div>
                                            </div>
                                        </div>
                
                                        <!-- Kapasitas & Berdiri Sejak Grid -->
                                        <div class="grid grid-cols-2 gap-2 pt-1">
                                            <div class="p-2.5 bg-blue-50/60 border border-blue-100 rounded-lg flex items-center gap-2.5 min-w-0">
                                                <i data-lucide="users" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <span class="text-xs font-bold uppercase text-slate-500 block text-left">KAPASITAS</span>
                                                    <span class="text-xs font-medium text-gov-textMain block text-left truncate">100 <span class="text-xs font-normal text-slate-500">jamaah</span></span>
                                                </div>
                                            </div>
                                            <div class="p-2.5 bg-slate-50 border border-slate-200/80 rounded-lg flex items-center gap-2.5 min-w-0">
                                                <i data-lucide="calendar" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                                <div class="min-w-0 flex-1">
                                                    <span class="text-xs font-bold uppercase text-slate-500 block text-left">BERDIRI SEJAK</span>
                                                    <span class="text-xs font-medium text-gov-textMain block text-left truncate">Tahun 2010</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Action: Selengkapnya -->
                                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                                        <a href="{{ route('portal.profil') }}" wire:navigate 
                                           class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer shrink-0
                                           title="Buka Halaman Profil Masjid & Susunan Pengurus">
                                            <span>Selengkapnya</span>
                                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                        </a>
                                    </div>
                                </section>
            </div>

        </div>

        <!-- Footer -->
        <footer class="bg-white rounded-xl border border-gov-border shadow-2xs p-4 sm:p-5 text-center space-y-1">
            <div class="font-bold text-xs text-gov-textMain">{{ $settings->name }}</div>
            <p class="text-xs text-slate-500">KPP Madya Malang</p>
        </footer>

    </main>

    <!-- Modal: City Selector Kemenag RI (518 Kota) -->
    <div x-show="openCityModal" x-cloak
        @click="if (window.isBackdropClick($event, $el)) openCityModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="openCityModal = false">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl border border-gov-border relative max-h-[88vh] flex flex-col transition-all">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200 shrink-0">
                        <i data-lucide="map-pin" class="w-4 h-4 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gov-textMain">Pilih Kota / Kabupaten</h3>
                        <p class="text-[11px] text-slate-500">Jadwal sholat resmi hisab Kemenag RI</p>
                    </div>
                </div>
                <button type="button" @click="openCityModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Search City Input -->
            <div class="pt-3 pb-2">
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                    <input x-model="searchCity" 
                           type="text" 
                           placeholder="Ketik nama kota / kabupaten..." 
                           class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-gov-border rounded-lg text-gov-textMain focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                </div>
            </div>

            <!-- Popular Cities Quick Select -->
            <div class="py-2 border-b border-slate-100">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">KOTA POPULER</span>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="p in popularCities" :key="p.id">
                        <button type="button" 
                                @click="selectCity(p.id, p.lokasi)" 
                                :class="selectedCityId == p.id ? 'bg-gov-navy text-white border-gov-navy font-bold' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border-gov-border font-medium'"
                                class="px-2.5 py-1 rounded-lg border text-xs transition cursor-pointer">
                            <span x-text="formatCity(p.lokasi)"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Filtered Cities List (Instant in-memory filtering) -->
            <div class="flex-1 overflow-y-auto py-2 space-y-1 max-h-60 sm:max-h-68 scrollbar-thin">
                <template x-for="city in filteredCities" :key="city.id">
                    <button type="button" 
                            @click="selectCity(city.id, city.lokasi)" 
                            :class="selectedCityId == city.id ? 'bg-blue-50 text-gov-navy border-blue-200 font-bold' : 'hover:bg-slate-50 text-slate-700 border-transparent font-medium'" 
                            class="w-full text-left px-3.5 py-2.5 rounded-lg border text-sm flex items-center justify-between transition cursor-pointer">
                        <span x-text="formatCity(city.lokasi)"></span>
                        <span x-show="selectedCityId == city.id" class="inline-flex items-center px-2.5 h-[20px] rounded-full text-[11px] font-semibold bg-gov-navy text-white leading-none">
                            Aktif ✓
                        </span>
                    </button>
                </template>
                <div x-show="filteredCities.length === 0" class="py-8 text-center text-slate-400 text-xs" style="display: none;">
                    Kota atau kabupaten "<span x-text="searchCity" class="font-semibold"></span>" tidak ditemukan.
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500">
                <span>518 Kota / Kab Kemenag RI</span>
                <button type="button" @click="openCityModal = false" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition cursor-pointer">
                    Batal / Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Daftar Komitmen Program Sosial -->
    <div x-show="$wire.showSocialRegisterModal" x-cloak
        @click="if (window.isBackdropClick($event, $el)) $wire.closeSocialRegisterModal()"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="$wire.closeSocialRegisterModal()">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200 shrink-0">
                        <i data-lucide="heart-handshake" class="w-4 h-4 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gov-textMain">Daftar Komitmen Jamaah</h3>
                        <p class="text-[11px] text-slate-500">Masjid Salahuddin KPP Madya Malang</p>
                    </div>
                </div>
                <button type="button" wire:click="closeSocialRegisterModal" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            @if($socialRegisterSuccess)
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs space-y-2">
                    <div class="flex items-center gap-2 font-bold text-emerald-900">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>Pendaftaran Berhasil!</span>
                    </div>
                    <p class="leading-relaxed">{{ $socialRegisterMessage }}</p>
                    <button type="button" wire:click="closeSocialRegisterModal"
                        class="mt-2 w-full py-2 bg-gov-navy hover:bg-gov-navyHover text-white font-bold rounded-lg transition shadow-2xs cursor-pointer text-center">
                        Tutup
                    </button>
                </div>
            @else
                <form wire:submit.prevent="submitSocialRegistration" class="space-y-3.5 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="socialParticipantName"
                            placeholder="Nama sesuai KTP / Pegawai"
                            class="w-full px-3.5 py-2 border border-gov-border rounded-lg text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition">
                        @error('socialParticipantName') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Program yang Diikuti <span class="text-rose-500">*</span></label>
                        <select wire:model="socialParticipantProgram"
                            class="w-full px-3.5 py-1.5 border border-gov-border rounded-lg bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white font-medium text-xs text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition shadow-2xs">
                            @foreach($socialPrograms as $prog)
                                <option value="{{ $prog->name }}">{{ $prog->name }}</option>
                            @endforeach
                        </select>
                        @error('socialParticipantProgram') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nominal Komitmen (Rp) <span class="text-rose-500">*</span></label>
                            <input type="text" inputmode="numeric" wire:model="socialParticipantAmount" x-ribuan="$wire.socialParticipantAmount"
                                placeholder="Contoh: 250.000"
                                class="w-full px-3.5 py-1.5 border border-gov-border rounded-lg bg-white text-gov-textMain font-bold focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition shadow-2xs">
                            @error('socialParticipantAmount') <span class="text-rose-500 text-[11px] block mt-0.5">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Periode Komitmen</label>
                            <select wire:model="socialParticipantPeriod"
                                class="w-full px-3.5 py-1.5 border border-gov-border rounded-lg bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white font-medium text-xs text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition shadow-2xs">
                                <option value="Bulanan">Bulanan</option>
                                <option value="Tahunan">Tahunan</option>
                                <option value="Satu Kali">Satu Kali</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeSocialRegisterModal"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                            <span wire:loading.remove wire:target="submitSocialRegistration">Kirim Komitmen</span>
                            <span wire:loading.inline-flex wire:target="submitSocialRegistration" style="display: none;"
                                class="inline-flex flex-row items-center justify-center gap-2 shrink-0 whitespace-nowrap">
                                <svg class="w-4 h-4 animate-spin shrink-0 inline-block text-white" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="whitespace-nowrap leading-none">Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

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

@push('scripts')
<script>
    function updateDigitalClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        const timeStr = `${hrs}:${mins}:${secs}`;

        const clockEl = document.getElementById('digital-clock');
        if (clockEl) clockEl.textContent = timeStr;

        const portalClock = document.getElementById('portal-live-clock');
        if (portalClock) portalClock.textContent = timeStr;
    }

    function copyRekening(text, btnElement) {
        if (window.copyTextToClipboard) {
            window.copyTextToClipboard(text, () => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Nomor rekening berhasil disalin!' } }));
            });
        } else if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
            } catch (err) {}
            document.body.removeChild(textArea);
        }
    }

    function updatePortalCountdown() {
        const targetTimeStr = "{{ $nextPrayer['target_time'] ?? ($nextPrayer['prayer']['adzan'] ?? '') }}";
        if (!targetTimeStr) return;

        const now = new Date();
        const parts = targetTimeStr.split(':');
        if (parts.length < 2) return;

        const targetH = parseInt(parts[0], 10);
        const targetM = parseInt(parts[1], 10);

        let targetDate = new Date();
        targetDate.setHours(targetH, targetM, 0, 0);

        @if(!empty($nextPrayer['is_tomorrow']))
            targetDate.setDate(targetDate.getDate() + 1);
        @endif

        const diffMs = targetDate - now;
        const el = document.getElementById('subuh-countdown') || document.getElementById('portal-countdown-display');
        if (el && diffMs > 0) {
            const totalSecs = Math.floor(diffMs / 1000);
            const hrs = Math.floor(totalSecs / 3600);
            const mins = Math.floor((totalSecs % 3600) / 60);
            const secs = totalSecs % 60;

            if (hrs > 0) {
                el.textContent = `${hrs} jam ${mins} menit lagi`;
            } else {
                el.textContent = `${mins} menit lagi`;
            }
        } else if (diffMs <= 0 && window.Livewire) {
            if (!window._portalRefreshTriggered) {
                window._portalRefreshTriggered = true;
                $wire.call('loadPrayers');
                setTimeout(() => { window._portalRefreshTriggered = false; }, 8000);
            }
        }
    }

    updateDigitalClock();
    setInterval(updateDigitalClock, 1000);

    updatePortalCountdown();
    setInterval(updatePortalCountdown, 1000);
</script>
@endpush
