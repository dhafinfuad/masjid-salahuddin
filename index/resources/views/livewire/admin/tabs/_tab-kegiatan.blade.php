            <!-- ======================================================== -->
            <!-- TAB: MANAJEMEN KEGIATAN TERPADU -->
            <!-- (Kajian Pekanan, Shalat Jumat, Kegiatan Akbar, ODOJ) -->
            <!-- ======================================================== -->
            <div class="space-y-4">
                <!-- Top Header & Action Bar (Dinamis sesuai Sub-tab aktif) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gov-border shadow-2xs">
                    <!-- Left: Icon & Dynamic Title/Subtitle -->
                    <div class="flex items-center space-x-2.5">
                        <div class="hidden sm:flex w-8 h-8 rounded-lg bg-slate-100 text-gov-navy items-center justify-center border border-gov-border shrink-0">
                            <i data-lucide="calendar-range" class="w-4 h-4 text-gov-navy"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gov-textMain">Manajemen Kegiatan Masjid</h3>
                            <span class="sr-only">Manajemen Jadwal Kajian Agenda Masjid</span>
                            <div class="text-xs text-gov-textMuted mt-0.5">
                                <span x-show="kegiatanSubTab === 'pekanan'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'pekanan' }">
                                    Kelola jadwal kajian, kegiatan akbar, dan One Day One Juz.
                                </span>
                                <span x-show="kegiatanSubTab === 'jumat'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'jumat' }">
                                    Penjadwalan kepengurusan petugas Shalat Jumat, tema khutbah, dan naskah MC.
                                </span>
                                <span x-show="kegiatanSubTab === 'agenda'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'agenda' }">
                                    History, laporan pelaksanaan, anggaran, dan perencanaan kegiatan akbar DKM.
                                </span>
                                <span x-show="kegiatanSubTab === 'odoj'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'odoj' }">
                                    Rotasi otomatis juz harian untuk tadarus Al-Qur'an secara terpadu & laporan bertilawah.
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Action Buttons (Disesuaikan per Sub-tab) -->
                    <div class="w-full sm:w-auto">
                        <!-- Action Buttons for Kajian Pekanan & Jumat -->
                        <div x-show="kegiatanSubTab === 'pekanan' || kegiatanSubTab === 'jumat'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'pekanan' && kegiatanSubTab !== 'jumat' }" class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto">
                            @if(Auth::user()->canManage())
                                <button type="button"
                                    @click="showImportKajianModal = true; $wire.openImportKajian(kegiatanSubTab === 'jumat' ? 'jumat' : 'pekanan')"
                                    class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="file-up" class="w-4 h-4 text-gov-navy"></i>
                                    <span>Import</span>
                                </button>
                            @endif

                            <div class="relative w-full sm:w-auto {{ !Auth::user()->canManage() ? 'col-span-2 sm:col-span-1' : '' }}" x-data="{ openExport: false }" @click.outside="openExport = false">
                                <button type="button" @click="openExport = !openExport"
                                    class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="file-down" class="w-4 h-4 text-emerald-600"></i>
                                    <span>Export</span>
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                                </button>
                                <div x-show="openExport" x-cloak
                                    class="absolute right-0 mt-1 w-52 bg-white rounded-lg shadow-xl border border-gov-border py-1.5 z-20 text-xs animate-in fade-in zoom-in-95 duration-100">
                                    <a :href="'{{ route('admin.kajian.export-excel') }}?type=' + (kegiatanSubTab === 'jumat' ? 'jumat' : 'pekanan') + '&month=' + $wire.kajianMonthFilter + '&year=' + $wire.kajianYearFilter"
                                        class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium">
                                        <i data-lucide="sheet" class="w-4 h-4 text-emerald-600"></i>
                                        <span>Download Excel (.xlsx)</span>
                                    </a>
                                    <a :href="'{{ route('admin.kajian.export-pdf') }}?type=' + (kegiatanSubTab === 'jumat' ? 'jumat' : 'pekanan') + '&month=' + $wire.kajianMonthFilter + '&year=' + $wire.kajianYearFilter" target="_blank"
                                        class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-800 transition font-medium">
                                        <i data-lucide="printer" class="w-4 h-4 text-rose-600"></i>
                                        <span>Cetak Jadwal Rapi (PDF)</span>
                                    </a>
                                </div>
                            </div>

                            @if(Auth::user()->canManage())
                                <button type="button" @click="openCreateKajian(kegiatanSubTab === 'jumat' ? 'jumat' : 'pekanan')"
                                    class="col-span-2 sm:col-span-1 w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer">
                                    <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
                                    <span>Tambah Jadwal</span>
                                </button>
                            @endif
                        </div>

                        <!-- Action Buttons for Kegiatan Akbar -->
                        <div x-show="kegiatanSubTab === 'agenda'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'agenda' }" class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto">
                            @if(Auth::user()->canManage())
                                <button type="button"
                                    @click="showImportAgendaModal = true; if(window.createLucideIcons) window.createLucideIcons();"
                                    class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="file-up" class="w-4 h-4 text-gov-navy"></i>
                                    <span>Import</span>
                                </button>
                            @endif

                            <div class="relative w-full sm:w-auto {{ !Auth::user()->canManage() ? 'col-span-2 sm:col-span-1' : '' }}" x-data="{ openExport: false }" @click.outside="openExport = false">
                                <button type="button" @click="openExport = !openExport"
                                    class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="file-down" class="w-4 h-4 text-emerald-600"></i>
                                    <span>Export</span>
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                                </button>
                                <div x-show="openExport" x-cloak
                                    class="absolute right-0 mt-1 w-52 bg-white rounded-lg shadow-xl border border-gov-border py-1.5 z-20 text-xs animate-in fade-in zoom-in-95 duration-100">
                                    <a href="{{ route('admin.agenda.export-excel') }}"
                                        class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium">
                                        <i data-lucide="sheet" class="w-4 h-4 text-emerald-600"></i>
                                        <span>Download Format Excel (.csv)</span>
                                    </a>
                                    <a href="{{ route('admin.agenda.export-pdf') }}" target="_blank"
                                        class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-800 transition font-medium">
                                        <i data-lucide="printer" class="w-4 h-4 text-rose-600"></i>
                                        <span>Cetak Format Rapi (PDF)</span>
                                    </a>
                                </div>
                            </div>

                            @if(Auth::user()->canManage())
                                <button type="button" @click="openCreateAgenda()"
                                    class="col-span-2 sm:col-span-1 w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer">
                                    <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
                                    <span>Tambah Agenda</span>
                                </button>
                            @endif
                        </div>

                        <!-- Action Buttons for ODOJ -->
                        <div x-show="kegiatanSubTab === 'odoj'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'odoj' }" class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto">
                            @if(Auth::user()->canManage())
                                <button type="button" @click="openAssignOdoj(1, '')"
                                    class="w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer">
                                    <i data-lucide="users" class="w-4 h-4 text-amber-400"></i>
                                    <span>Atur Juz</span>
                                </button>
                            @endif
                            <button type="button" @click="copyOdojToClipboard({{ Js::from($odojWhatsappText) }})"
                                class="{{ !Auth::user()->canManage() ? 'col-span-2 sm:col-span-1' : '' }} w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer shrink-0 whitespace-nowrap"
                                title="Salin rekap ODOJ ke clipboard untuk WhatsApp">
                                <i data-lucide="clipboard-copy" class="w-4 h-4 text-amber-400 shrink-0 inline-block"></i>
                                <span class="whitespace-nowrap">Salin Rekap WA</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4 Sub-tab Unified Filter Navigation Bar -->
                <div class="text-xs flex flex-col sm:flex-row sm:items-center justify-start gap-3 bg-white p-3 rounded-xl border border-gov-border shadow-2xs">
                    <button type="button"
                        @click="switchSubTab('pekanan')"
                        :class="kegiatanSubTab === 'pekanan' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'"
                        class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                        <span>Kajian</span>
                    </button>
                    <button type="button"
                        @click="switchSubTab('jumat')"
                        :class="kegiatanSubTab === 'jumat' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'"
                        class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2">
                        <i data-lucide="mic" class="w-4 h-4"></i>
                        <span>Khutbah Jumat</span>
                    </button>
                    <button type="button"
                        @click="switchSubTab('agenda')"
                        :class="kegiatanSubTab === 'agenda' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'"
                        class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2">
                        <i data-lucide="calendar-range" class="w-4 h-4"></i>
                        <span>Kegiatan Akbar</span>
                    </button>
                    <button type="button"
                        @click="switchSubTab('odoj')"
                        :class="kegiatanSubTab === 'odoj' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'"
                        class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                        <span>One Day One Juz</span>
                    </button>
                </div>

                <!-- Kajian Search & Filter (Hanya tampil saat subtab Pekanan atau Jumat) -->
                <div x-show="kegiatanSubTab === 'pekanan' || kegiatanSubTab === 'jumat'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'pekanan' && kegiatanSubTab !== 'jumat' }" class="space-y-4">
                <!-- Search & Filter Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-gov-border shadow-2xs">
                    <div class="relative flex-1 max-w-sm" x-data="{
                        clearSearch() {
                            const sub = kegiatanSubTab || 'pekanan';
                            $wire.call('clearKajianSearch', sub);
                        }
                    }">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input x-show="kegiatanSubTab === 'pekanan'" wire:key="pekanan-search-input" wire:model.live.debounce.300ms="pekananSearch" type="text" placeholder="Cari judul kajian, narasumber..." class="h-[32px] w-full pl-8 pr-9 py-1 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                        <input x-show="kegiatanSubTab === 'jumat'" wire:key="jumat-search-input" wire:model.live.debounce.300ms="jumatSearch" type="text" placeholder="Cari tema khutbah, khatib, MC, muadzin..." class="h-[32px] w-full pl-8 pr-9 py-1 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                        <button type="button" @click="clearSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 transition cursor-pointer flex items-center justify-center" title="Reset pencarian ke data seharusnya">
                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto">
                        <select wire:key="kajian-month-filter" wire:model.live="kajianMonthFilter" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-gov-navy cursor-pointer">
                            <option value="all">Semua Bulan</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">
                                    {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>

                        <select wire:key="kajian-year-filter" wire:model.live="kajianYearFilter" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-gov-navy cursor-pointer">
                            <option value="all">Semua Tahun</option>
                            @foreach($availableKajianYears as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>

                        @if($kajianMonthFilter !== 'all' || $kajianYearFilter !== 'all' || !empty($pekananSearch) || !empty($jumatSearch) || !empty($search))
                            <button type="button" wire:click="resetKajianFilters"
                                class="col-span-2 sm:col-span-1 w-full sm:w-auto justify-center px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-gov-border hover:border-rose-200 transition cursor-pointer flex items-center gap-1"
                                title="Reset Filter ke Semua">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>Reset</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

                <!-- ======================================================== -->
                <!-- SUB-TAB 1: TABEL KAJIAN PEKANAN -->
                <!-- ======================================================== -->
                <div x-show="kegiatanSubTab === 'pekanan'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'pekanan' }"
                    class="space-y-3">
                    <!-- Desktop Executive Table (Smart Compact Merged Columns) -->
                    <div
                        class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-visible">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-gov-border text-slate-500 uppercase tracking-wider text-xs font-semibold">
                                    <th class="p-3.5 text-center w-12 rounded-tl-xl">No</th>
                                    <th wire:click="sortBy('date', 'pekanan')" class="p-3.5 w-36 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Hari & Tanggal</span>
                                            <x-sort-icon field="date" table="pekanan" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('time_display', 'pekanan')" class="p-3.5 w-28 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Waktu</span>
                                            <x-sort-icon field="time_display" table="pekanan" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('title', 'pekanan')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Judul / Tema Kajian</span>
                                            <x-sort-icon field="title" table="pekanan" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('speaker_name', 'pekanan')" class="p-3.5 min-w-[150px] whitespace-normal cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Narasumber</span>
                                            <x-sort-icon field="speaker_name" table="pekanan" />
                                        </div>
                                    </th>
                                    <th class="p-3.5 text-center w-20 rounded-tr-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 transition-opacity duration-150">
                                @forelse($allKajianPekanan as $idx => $k)
                                    @php
                                        $kDate = $k->date ? Carbon\Carbon::parse($k->date)->format('Y-m-d') : '';
                                        $isNextKajian = ($nextPekananKajianId && $k->id === $nextPekananKajianId);
                                        $isCurrentActiveWeek = ($kDate >= $todayDate && $kDate <= $kajianCurrentWeekEnd);
                                        $isHighlight = ($isNextKajian || $isCurrentActiveWeek);
                                        $isToday = ($kDate === $todayDate);
                                    @endphp
                                    <tr wire:key="pekanan-row-{{ $k->id }}" class="{{ $isHighlight ? 'hover:bg-amber-100/70 transition bg-amber-100/40 border-l-4 border-l-amber-500' : 'hover:bg-slate-50/80 transition' }}">
                                        <td class="p-3.5 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                        <td class="p-3.5 font-medium whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-gov-textMain">
                                                    {{ Carbon\Carbon::parse($k->date)->translatedFormat('l') }}
                                                </span>
                                            </div>
                                            <div class="text-slate-500 text-[11px]">
                                                {{ Carbon\Carbon::parse($k->date)->translatedFormat('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            <span
                                                class="h-[20px] flex items-center text-[11px] font-semibold text-slate-700">
                                                {{ $k->time_display }}
                                            </span>
                                        </td>
                                        <td class="p-3.5 font-semibold text-gov-textMain">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                @if($k->type === 'tematik')
                                                    <span class="inline-flex text-[11px] text-slate-500">Tematik</span>
                                                @else
                                                    <span class="inline-flex text-[11px] text-slate-500">Pekanan</span>
                                                @endif
                                                @if(!empty($k->notula))
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800" title="Notula kajian tersimpan">
                                                        <svg class="w-3 h-3 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                                        Notula Ada
                                                    </span>
                                                @endif
                                                @if(!empty($k->youtube_url))
                                                    <a href="{{ $k->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-100 hover:bg-rose-200 text-rose-800 transition" title="Buka siaran YouTube">
                                                        <svg class="w-3 h-3 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                        </svg>
                                                        YouTube Ada
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="line-clamp-2 leading-relaxed">{{ $k->title }}</div>
                                        </td>
                                        <td
                                            class="p-3.5 font-normal min-w-[150px] whitespace-normal break-words [overflow-wrap:break-word]">
                                            <div class="flex items-center gap-2 text-gov-navy font-normal">
                                                @if($k->valid_speaker_photo_url)
                                                    <img src="{{ $k->valid_speaker_photo_url }}" alt="{{ $k->speaker_name }}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-slate-200 shadow-2xs">
                                                @else
                                                    <i data-lucide="user-check"
                                                        class="w-3.5 h-3.5 text-gov-navy shrink-0"></i>
                                                @endif
                                                <span
                                                    class="break-words whitespace-normal font-normal">{{ $k->speaker_name ?: '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="p-3.5 text-center whitespace-nowrap relative" x-data="{ openMenu: false }">
                                            @php
                                                $isPekanan = ($k->type === 'pekanan');
                                                $posterMainTitle = $isPekanan ? 'KAJIAN PEKANAN' : 'KAJIAN TEMATIK';
                                                $posterTitle1 = 'Kajian';
                                                $posterTitle2 = $isPekanan ? 'KAJIAN PEKANAN' : 'Tematik';
                                            @endphp
                                            <div class="inline-flex items-center justify-center">
                                                <!-- Tombol Titik Tiga Vertikal -->
                                                <button type="button" 
                                                    @click="openMenu = !openMenu" 
                                                    class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                                    :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''"
                                                    title="Menu Aksi">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                                    </svg>
                                                </button>

                                                <!-- Dropdown Menu Aksi -->
                                                <div x-show="openMenu" 
                                                    x-cloak
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                    @click.outside="openMenu = false"
                                                    @keydown.escape.window="openMenu = false"
                                                    class="absolute right-3 top-full mt-1 w-56 bg-white rounded-xl shadow-xl border border-gov-border py-1 z-40 text-xs text-left divide-y divide-slate-100">
                                                    
                                                    <div class="py-1">
                                                        <!-- Unduh Poster -->
                                                        <button type="button"
                                                            @click="openMenu = false; $dispatch('generate-poster', {{ Js::from([
                                                                'title' => $posterMainTitle,
                                                                'title1' => $posterTitle1,
                                                                'title2' => $posterTitle2,
                                                                'subtitle' => $k->title,
                                                                'speaker' => $k->speaker_name ?: 'Asatidz',
                                                                'date' => \Carbon\Carbon::parse($k->date)->translatedFormat('d F Y'),
                                                                'time' => $k->time_display,
                                                                'location' => 'Masjid Salahuddin',
                                                                'photo' => $k->valid_speaker_photo_url ?? '',
                                                                'template' => $isPekanan ? 2 : 1,
                                                            ]) }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-sky-50 hover:text-sky-800 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Unduh Poster</div>
                                                        </button>

                                                        <!-- Salin Jarkoman WhatsApp -->
                                                        <button type="button"
                                                            @click="openMenu = false; copyJarkomanToClipboard({{ Js::from($k->whatsapp_broadcast_text) }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-indigo-50 hover:text-indigo-800 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-indigo-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Salin Jarkoman</div>
                                                        </button>

                                                        <!-- Hubungi Pembicara via WhatsApp -->
                                                        @if($k->speaker_phone)
                                                            <a href="{{ wa_link($k->speaker_phone) }}"
                                                                target="_blank"
                                                                @click="openMenu = false"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hubungi Pemateri</div>
                                                            </a>
                                                        @endif
                                                        <!-- Notula Kajian (Khusus Role Master) -->
                                                        @if(Auth::user()->isMaster())
                                                            <button type="button"
                                                                @click="openMenu = false; $wire.openNotulaModal({{ $k->id }})"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-gov-50 hover:text-gov-900 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-gov-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                                                    <path d="M6 6h10"/><path d="M6 10h10"/><path d="M6 14h6"/>
                                                                </svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Notula</div>
                                                                @if(!empty($k->notula))
                                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Notula sudah tersimpan"></span>
                                                                @endif
                                                            </button>
                                                        @endif
                                                    </div>

                                                    @if(Auth::user()->canManage())
                                                        <div class="py-1">
                                                            <!-- Link YouTube -->
                                                            <button type="button"
                                                                @click="openMenu = false; $wire.openYoutubeModal({{ $k->id }}, 'kajian')"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-900 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                                </svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Link YouTube</div>
                                                                @if(!empty($k->youtube_url))
                                                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0" title="Link YouTube sudah tersimpan"></span>
                                                                @endif
                                                            </button>

                                                            <!-- Edit Jadwal -->
                                                            <button type="button" 
                                                                @click="openMenu = false; openEditKajian({{ Js::from([
                                                                    'id' => $k->id,
                                                                    'type' => $k->type,
                                                                    'date' => $k->date ? Carbon\Carbon::parse($k->date)->format('Y-m-d') : '',
                                                                    'time_display' => $k->time_display,
                                                                    'title' => $k->title,
                                                                    'speaker_name' => $k->speaker_name,
                                                                    'speaker_phone' => $k->speaker_phone,
                                                                    'speaker_photo' => $k->speaker_photo,
                                                                    'youtube_url' => $k->youtube_url,
                                                                    'is_holiday_disabled' => false,
                                                                ]) }})"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Edit Jadwal</div>
                                                            </button>

                                                            <!-- Hapus Jadwal -->
                                                            <button type="button"
                                                                @click="openMenu = false; openDeleteModal({{ Js::from([
                                                                    'action' => 'deleteKajian',
                                                                    'id' => $k->id,
                                                                    'title' => 'Hapus Jadwal Kajian',
                                                                    'message' => 'Apakah Anda yakin ingin menghapus jadwal kajian ini dari kalender dan display TV?',
                                                                    'itemName' => $k->title,
                                                                ]) }})"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Jadwal</div>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300"></i>
                                                <p>Belum ada jadwal kajian yang ditambahkan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Responsive Cards -->
                    <div class="md:hidden space-y-3">
                        @forelse($allKajianPekanan as $idx => $k)
                            @php
                                $kDate = $k->date ? Carbon\Carbon::parse($k->date)->format('Y-m-d') : '';
                                $isNextKajian = ($nextPekananKajianId && $k->id === $nextPekananKajianId);
                                $isCurrentActiveWeek = ($kDate >= $todayDate && $kDate <= $kajianCurrentWeekEnd);
                                $isHighlight = ($isNextKajian || $isCurrentActiveWeek);
                                $isToday = ($kDate === $todayDate);
                                $isPekanan = ($k->type === 'pekanan');
                                $posterMainTitle = $isPekanan ? 'KAJIAN PEKANAN' : 'KAJIAN TEMATIK';
                                $posterTitle1 = 'Kajian';
                                $posterTitle2 = $isPekanan ? 'KAJIAN PEKANAN' : 'Tematik';
                            @endphp
                            <div wire:key="pekanan-card-{{ $k->id }}" class="rounded-xl border p-4 shadow-2xs space-y-3 transition {{ $isHighlight ? 'bg-amber-100/40 border-l-[3px] border-amber-300' : 'bg-white border-gov-border' }}" x-data="{ openMenu: false }">
                                <div class="flex items-center justify-between relative">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if($k->type === 'tematik')
                                            <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-amber-100 text-amber-800 border-amber-300">Tematik</span>
                                        @else
                                            <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">Pekanan</span>
                                        @endif
                                        @if(!empty($k->notula))
                                            <span class="inline-flex items-center gap-1 h-[20px] px-2 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 leading-none" title="Notula kajian tersimpan">
                                                <svg class="w-2.5 h-2.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                                Notula Ada
                                            </span>
                                        @endif
                                        @if(!empty($k->youtube_url))
                                            <a href="{{ $k->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1 h-[20px] px-2 rounded-full text-[10px] font-semibold bg-rose-100 hover:bg-rose-200 text-rose-800 border border-rose-300 leading-none transition" title="Buka siaran YouTube">
                                                <svg class="w-2.5 h-2.5 text-rose-600" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                </svg>
                                                YouTube Ada
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Tombol Titik Tiga Vertikal Mobile -->
                                    <div class="relative">
                                        <button type="button" 
                                            @click="openMenu = !openMenu" 
                                            class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                            :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''"
                                            title="Menu Aksi">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu Aksi Mobile -->
                                        <div x-show="openMenu" 
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            @click.outside="openMenu = false"
                                            @keydown.escape.window="openMenu = false"
                                            class="absolute right-0 top-full mt-1 w-56 bg-white rounded-xl shadow-xl border border-gov-border py-1 z-40 text-xs text-left divide-y divide-slate-100">
                                            
                                            <div class="py-1">
                                                <!-- Unduh Poster -->
                                                <button type="button"
                                                    @click="openMenu = false; $dispatch('generate-poster', {{ Js::from([
                                                        'title' => $posterMainTitle,
                                                        'title1' => $posterTitle1,
                                                        'title2' => $posterTitle2,
                                                        'subtitle' => $k->title,
                                                        'speaker' => $k->speaker_name ?: 'Asatidz',
                                                        'date' => \Carbon\Carbon::parse($k->date)->translatedFormat('d F Y'),
                                                        'time' => $k->time_display,
                                                        'location' => 'Masjid Salahuddin',
                                                        'photo' => $k->valid_speaker_photo_url ?? '',
                                                        'template' => $isPekanan ? 2 : 1,
                                                    ]) }})"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-sky-50 hover:text-sky-800 transition font-medium text-xs cursor-pointer">
                                                    <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Unduh Poster</div>
                                                </button>

                                                <!-- Salin Jarkoman WhatsApp -->
                                                <button type="button"
                                                    @click="openMenu = false; copyJarkomanToClipboard({{ Js::from($k->whatsapp_broadcast_text) }})"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-indigo-50 hover:text-indigo-800 transition font-medium text-xs cursor-pointer">
                                                    <svg class="w-4 h-4 text-indigo-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                                                    <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Salin Jarkoman</div>
                                                </button>

                                                <!-- Hubungi Pembicara via WhatsApp -->
                                                @if($k->speaker_phone)
                                                    <a href="{{ wa_link($k->speaker_phone) }}"
                                                        target="_blank"
                                                        @click="openMenu = false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hubungi Pemateri</div>
                                                    </a>
                                                @endif
                                                <!-- Notula Kajian (Khusus Role Master) -->
                                                @if(Auth::user()->isMaster())
                                                    <button type="button"
                                                        @click="openMenu = false; $wire.openNotulaModal({{ $k->id }})"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-gov-50 hover:text-gov-900 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-gov-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                                            <path d="M6 6h10"/><path d="M6 10h10"/><path d="M6 14h6"/>
                                                        </svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Notula</div>
                                                        @if(!empty($k->notula))
                                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Notula sudah tersimpan"></span>
                                                        @endif
                                                    </button>
                                                @endif
                                            </div>

                                            @if(Auth::user()->canManage())
                                                <div class="py-1">
                                                    <!-- Link YouTube -->
                                                    <button type="button"
                                                        @click="openMenu = false; $wire.openYoutubeModal({{ $k->id }}, 'kajian')"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-900 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                        </svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Link YouTube</div>
                                                        @if(!empty($k->youtube_url))
                                                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0" title="Link YouTube sudah tersimpan"></span>
                                                        @endif
                                                    </button>

                                                    <!-- Edit Jadwal -->
                                                    <button type="button" 
                                                        @click="openMenu = false; openEditKajian({{ Js::from([
                                                            'id' => $k->id,
                                                            'type' => $k->type,
                                                            'date' => $k->date ? Carbon\Carbon::parse($k->date)->format('Y-m-d') : '',
                                                            'time_display' => $k->time_display,
                                                            'title' => $k->title,
                                                            'speaker_name' => $k->speaker_name,
                                                            'speaker_phone' => $k->speaker_phone,
                                                            'speaker_photo' => $k->speaker_photo,
                                                            'youtube_url' => $k->youtube_url,
                                                            'is_holiday_disabled' => false,
                                                        ]) }})"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Edit Jadwal</div>
                                                    </button>

                                                    <!-- Hapus Jadwal -->
                                                    <button type="button"
                                                        @click="openMenu = false; openDeleteModal({{ Js::from([
                                                            'action' => 'deleteKajian',
                                                            'id' => $k->id,
                                                            'title' => 'Hapus Jadwal Kajian',
                                                            'message' => 'Apakah Anda yakin ingin menghapus jadwal kajian ini dari kalender dan display TV?',
                                                            'itemName' => $k->title,
                                                        ]) }})"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Jadwal</div>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="font-bold text-gov-textMain text-sm">
                                        {{ Carbon\Carbon::parse($k->date)->translatedFormat('l, d M Y') }}
                                    </div>
                                    <span class="h-[20px] flex items-center text-[11px] font-semibold text-slate-700">
                                        {{ $k->time_display }}
                                    </span>
                                </div>
                                
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex flex-col items-start justify-between gap-2">
                                    <div class="font-semibold text-gov-navy text-xs leading-snug">
                                        {{ $k->title }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-slate-800 font-normal truncate">
                                        @if($k->valid_speaker_photo_url)
                                            <img src="{{ $k->valid_speaker_photo_url }}" alt="{{ $k->speaker_name }}" class="w-5 h-5 rounded-full object-cover shrink-0 border border-slate-200 shadow-2xs">
                                        @else
                                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-gov-navy shrink-0"></i>
                                        @endif
                                        <span class="truncate">{{ $k->speaker_name ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-xl border border-gov-border p-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300"></i>
                                    <p>Belum ada jadwal kajian yang ditambahkan.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($allKajianPekanan, 'hasPages') && $allKajianPekanan->hasPages())
                        <div class="pt-2">
                            {{ $allKajianPekanan->links(data: ['scrollTo' => false]) }}
                        </div>
                    @endif
                </div>

                <!-- SUB-TAB 2: TABEL KAJIAN JUMAT & SHALAT JUMAT -->
                <!-- ======================================================== -->
                <div x-show="kegiatanSubTab === 'jumat'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'jumat' }"
                    class="space-y-3">
                    <!-- Desktop Executive Table (Smart Merged Columns: Waktu & Status, Tema, Petugas Shalat, Aksi) -->
                    <div
                        class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-visible">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-gov-border text-slate-500 uppercase tracking-wider text-xs font-semibold">
                                    <th class="p-3.5 text-center w-12 rounded-tl-xl">No</th>
                                    <th wire:click="sortBy('date', 'jumat')" class="p-3.5 w-48 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Waktu & Status</span>
                                            <x-sort-icon field="date" table="jumat" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('khatib_name', 'jumat')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Khatib</span>
                                            <x-sort-icon field="khatib_name" table="jumat" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('mc_name', 'jumat')" class="p-3.5 w-64 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>MC & Muadzin</span>
                                            <x-sort-icon field="mc_name" table="jumat" />
                                        </div>
                                    </th>
                                    <th class="p-3.5 text-center w-20 rounded-tr-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 transition-opacity duration-150">
                                @forelse($allKajianJumat as $idx => $k)
                                    @php
                                        $kDate = $k->date ? Carbon\Carbon::parse($k->date)->format('Y-m-d') : '';
                                        $isNextKajian = ($nextJumatKajianId && $k->id === $nextJumatKajianId);
                                        $isCurrentActiveWeek = ($kDate >= $todayDate && $kDate <= $kajianCurrentWeekEnd);
                                        $isHighlight = ($isNextKajian || $isCurrentActiveWeek);
                                        $isToday = ($kDate === $todayDate);
                                    @endphp
                                    <tr wire:key="jumat-row-{{ $k->id }}"
                                        class="{{ $isHighlight ? 'hover:bg-amber-100/70 transition bg-amber-100/40 border-l-4 border-l-amber-500' : ($k->is_holiday_disabled ? 'hover:bg-slate-50/80 transition bg-rose-50/30' : 'hover:bg-slate-50/80 transition') }}">
                                        <td class="p-3.5 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>

                                        <!-- WAKTU & STATUS (MERGED) -->
                                        <td class="p-3.5">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-gov-textMain">
                                                    {{ Carbon\Carbon::parse($k->date)->translatedFormat('l') }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ Carbon\Carbon::parse($k->date)->translatedFormat('d M Y') }} •
                                                {{ $k->time_display }}
                                            </div>
                                            <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                                                @if(Auth::user()->canManage())
                                                    <button type="button" wire:click="toggleHolidayDisabled({{ $k->id }})"
                                                        class="inline-flex items-center gap-1 h-[20px] px-2.5 rounded-full text-[11px] font-semibold transition cursor-pointer border leading-none shrink-0 whitespace-nowrap {{ $k->is_holiday_disabled ? 'bg-rose-100 text-rose-800 border-rose-300 hover:bg-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}"
                                                        title="Klik untuk mengubah status hari libur">
                                                        @if($k->is_holiday_disabled)
                                                            <i data-lucide="alert-circle" class="w-3 h-3 text-rose-600 shrink-0 inline-block"></i>
                                                            <span class="whitespace-nowrap">Libur (Nonaktif)</span>
                                                        @else
                                                            <i data-lucide="check-circle-2" class="w-3 h-3 text-emerald-600 shrink-0 inline-block"></i>
                                                            <span class="whitespace-nowrap">Petugas Aktif</span>
                                                        @endif
                                                    </button>
                                                @else
                                                    <span
                                                        class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold leading-none {{ $k->is_holiday_disabled ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-emerald-100 text-emerald-800 border border-emerald-300' }}">
                                                        {{ $k->is_holiday_disabled ? 'Libur' : 'Aktif' }}
                                                    </span>
                                                @endif

                                                @if(!empty($k->notula))
                                                    <span class="inline-flex items-center gap-1 h-[20px] px-2 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 leading-none shrink-0" title="Notula khutbah tersimpan">
                                                        <svg class="w-2.5 h-2.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                                        Notula Ada
                                                    </span>
                                                @endif
                                                @if(!empty($k->youtube_url))
                                                    <a href="{{ $k->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                                        class="inline-flex items-center gap-1 h-[20px] px-2 rounded-full text-[10px] font-semibold bg-rose-100 hover:bg-rose-200 text-rose-800 border border-rose-300 leading-none shrink-0 transition" title="Buka siaran YouTube">
                                                        <svg class="w-2.5 h-2.5 text-rose-600" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                        </svg>
                                                        YouTube Ada
                                                    </a>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- KHATIB -->
                                        <td class="p-3.5">
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="font-bold text-gov-navy text-xs">{{ $k->khatib_name ?: '-' }}</span>
                                            </div>
                                        </td>

                                        <!-- PETUGAS SHALAT JUMAT (MC & MUADZIN) -->
                                        <td class="p-3.5 space-y-1">
                                            <!-- MC / Protokol -->
                                            <div class="flex items-center gap-1.5 text-xs text-slate-600 truncate">
                                                <span class="text-[11px] text-slate-600 truncate"><span
                                                        class="text-slate-400">MC:</span> {{ $k->mc_name ?: '-' }}</span>
                                            </div>

                                            <!-- Muadzin -->
                                            <div class="flex items-center gap-1.5 text-xs text-slate-600 truncate">
                                                <span class="text-[11px] text-slate-600 truncate"><span
                                                        class="text-slate-400">Bilal:</span>
                                                    {{ $k->muadzin_name ?: '-' }}</span>
                                            </div>
                                        </td>

                                        <!-- AKSI (SUBMENU TITIK TIGA VERTIKAL) -->
                                        <td class="p-3.5 text-center whitespace-nowrap relative" x-data="{ openMenu: false }">
                                            <div class="inline-flex items-center justify-center">
                                                <!-- Tombol Titik Tiga Vertikal -->
                                                <button type="button" 
                                                    @click="openMenu = !openMenu" 
                                                    class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                                    :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''"
                                                    title="Menu Aksi">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                                    </svg>
                                                </button>

                                                <!-- Dropdown Menu Aksi -->
                                                <div x-show="openMenu" 
                                                    x-cloak
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                    @click.outside="openMenu = false"
                                                    @keydown.escape.window="openMenu = false"
                                                    class="absolute right-3 top-full mt-1 w-56 bg-white rounded-xl shadow-xl border border-gov-border py-1 z-40 text-xs text-left divide-y divide-slate-100">
                                                    
                                                    <div class="py-1">
                                                        <!-- Naskah / Teks MC PDF -->
                                                        <a href="{{ route('admin.kajian.teks-mc', $k->id) }}" target="_blank"
                                                            @click="openMenu = false"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-sky-50 hover:text-sky-800 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-sky-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Cetak Teks MC</div>
                                                        </a>

                                                        <!-- Salin Jarkoman WhatsApp -->
                                                        <button type="button"
                                                            @click="openMenu = false; copyJarkomanToClipboard({{ Js::from($k->whatsapp_broadcast_text) }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-indigo-50 hover:text-indigo-800 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-indigo-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Salin Jarkoman</div>
                                                        </button>

                                                        <!-- Hubungi Khatib via WhatsApp -->
                                                        @if($k->khatib_phone)
                                                            <a href="{{ wa_link($k->khatib_phone) }}"
                                                                target="_blank"
                                                                @click="openMenu = false"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hubungi Khatib</div>
                                                            </a>
                                                        @endif

                                                        <!-- Notula Khutbah Jumat (Khusus Role Master) -->
                                                        @if(Auth::user()->isMaster())
                                                            <button type="button"
                                                                @click="openMenu = false; $wire.openNotulaModal({{ $k->id }})"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-gov-50 hover:text-gov-900 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-gov-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                                                    <path d="M6 6h10"/><path d="M6 10h10"/><path d="M6 14h6"/>
                                                                </svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Notula Khutbah</div>
                                                                @if(!empty($k->notula))
                                                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Notula sudah tersimpan"></span>
                                                                @endif
                                                            </button>
                                                        @endif
                                                    </div>

                                                    @if(Auth::user()->canManage())
                                                        <div class="py-1">
                                                            <!-- Link YouTube -->
                                                            <button type="button"
                                                                @click="openMenu = false; $wire.openYoutubeModal({{ $k->id }}, 'kajian')"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-900 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                                </svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Link YouTube</div>
                                                                @if(!empty($k->youtube_url))
                                                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0" title="Link YouTube sudah tersimpan"></span>
                                                                @endif
                                                            </button>

                                                            <!-- Edit Jadwal -->
                                                            <button type="button" 
                                                                @click="openMenu = false; openEditKajian({{ Js::from([
                                                                    'id' => $k->id,
                                                                    'type' => $k->type,
                                                                    'date' => $k->date ? Carbon\Carbon::parse($k->date)->format('Y-m-d') : '',
                                                                    'time_display' => $k->time_display,
                                                                    'title' => $k->title,
                                                                    'speaker_name' => '',
                                                                    'speaker_phone' => '',
                                                                    'is_holiday_disabled' => (bool) $k->is_holiday_disabled,
                                                                    'khatib_name' => $k->khatib_name,
                                                                    'mc_name' => $k->mc_name,
                                                                    'muadzin_name' => $k->muadzin_name,
                                                                    'khatib_phone' => $k->khatib_phone,
                                                                    'mc_notes' => $k->mc_notes,
                                                                    'youtube_url' => $k->youtube_url,
                                                                ]) }})"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Edit Jadwal</div>
                                                            </button>

                                                            <!-- Hapus Jadwal -->
                                                            <button type="button"
                                                                @click="openMenu = false; openDeleteModal({{ Js::from([
                                                                    'action' => 'deleteKajian',
                                                                    'id' => $k->id,
                                                                    'title' => 'Hapus Jadwal Shalat Jumat',
                                                                    'message' => 'Apakah Anda yakin ingin menghapus jadwal Shalat Jumat ini?',
                                                                    'itemName' => $k->khatib_name ? ('Khatib: ' . $k->khatib_name . ' (' . Carbon\Carbon::parse($k->date)->translatedFormat('d M Y') . ')') : $k->title,
                                                                ]) }})"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Jadwal</div>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300"></i>
                                                <p>Belum ada jadwal Shalat Jumat yang ditambahkan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Responsive Cards -->
                    <div class="md:hidden space-y-3">
                        @forelse($allKajianJumat as $idx => $k)
                            @php
                                $kDate = $k->date ? Carbon\Carbon::parse($k->date)->format('Y-m-d') : '';
                                $isNextKajian = ($nextJumatKajianId && $k->id === $nextJumatKajianId);
                                $isCurrentActiveWeek = ($kDate >= $todayDate && $kDate <= $kajianCurrentWeekEnd);
                                $isHighlight = ($isNextKajian || $isCurrentActiveWeek);
                                $isToday = ($kDate === $todayDate);
                            @endphp
                            <div wire:key="jumat-card-{{ $k->id }}"
                                class="rounded-xl border p-4 shadow-2xs space-y-3 transition {{ $isHighlight ? 'bg-amber-100/40 border-l-[3px] border-amber-300' : ($k->is_holiday_disabled ? 'border-rose-200 bg-rose-50/20 bg-white' : 'bg-white border-gov-border') }}"
                                x-data="{ openMenu: false }">
                                <div class="flex items-start justify-between gap-2 relative">
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-gov-textMain text-sm">
                                                {{ Carbon\Carbon::parse($k->date)->translatedFormat('l, d M Y') }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-slate-500">{{ $k->time_display }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        @if($k->is_holiday_disabled)
                                            <span
                                                class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-700 border border-rose-200 leading-none">Diliburkan</span>
                                        @else
                                            <span
                                                class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 leading-none">Aktif</span>
                                        @endif
                                        @if(!empty($k->notula))
                                            <span class="inline-flex items-center gap-1 h-[20px] px-2 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 leading-none" title="Notula khutbah tersimpan">
                                                <svg class="w-2.5 h-2.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                                Notula Ada
                                            </span>
                                        @endif
                                        @if(!empty($k->youtube_url))
                                            <a href="{{ $k->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1 h-[20px] px-2 rounded-full text-[10px] font-semibold bg-rose-100 hover:bg-rose-200 text-rose-800 border border-rose-300 leading-none transition" title="Buka siaran YouTube">
                                                <svg class="w-2.5 h-2.5 text-rose-600" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                </svg>
                                                YouTube Ada
                                            </a>
                                        @endif

                                        <!-- Tombol Titik Tiga Vertikal Mobile -->
                                        <div class="relative">
                                            <button type="button" 
                                                @click="openMenu = !openMenu" 
                                                class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                                :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''"
                                                title="Menu Aksi">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu Aksi Mobile -->
                                            <div x-show="openMenu" 
                                                x-cloak
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                @click.outside="openMenu = false"
                                                @keydown.escape.window="openMenu = false"
                                                class="absolute right-0 top-full mt-1 w-56 bg-white rounded-xl shadow-xl border border-gov-border py-1 z-40 text-xs text-left divide-y divide-slate-100">
                                                
                                                <div class="py-1">
                                                    <!-- Naskah / Teks MC PDF -->
                                                    <a href="{{ route('admin.kajian.teks-mc', $k->id) }}" target="_blank"
                                                        @click="openMenu = false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-sky-50 hover:text-sky-800 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-sky-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Cetak Teks MC</div>
                                                    </a>

                                                    <!-- Salin Jarkoman WhatsApp -->
                                                    <button type="button"
                                                        @click="openMenu = false; copyJarkomanToClipboard({{ Js::from($k->whatsapp_broadcast_text) }})"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-indigo-50 hover:text-indigo-800 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-indigo-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Salin Jarkoman</div>
                                                    </button>

                                                    <!-- Hubungi Khatib via WhatsApp -->
                                                    @if($k->khatib_phone)
                                                        <a href="{{ wa_link($k->khatib_phone) }}"
                                                            target="_blank"
                                                            @click="openMenu = false"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hubungi Khatib</div>
                                                        </a>
                                                    @endif

                                                    <!-- Notula Khutbah Jumat (Khusus Role Master) -->
                                                    @if(Auth::user()->isMaster())
                                                        <button type="button"
                                                            @click="openMenu = false; $wire.openNotulaModal({{ $k->id }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-gov-50 hover:text-gov-900 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-gov-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                                                <path d="M6 6h10"/><path d="M6 10h10"/><path d="M6 14h6"/>
                                                            </svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Notula Khutbah</div>
                                                            @if(!empty($k->notula))
                                                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Notula sudah tersimpan"></span>
                                                            @endif
                                                        </button>
                                                    @endif
                                                </div>

                                                @if(Auth::user()->canManage())
                                                    <div class="py-1">
                                                        <!-- Link YouTube -->
                                                        <button type="button"
                                                            @click="openMenu = false; $wire.openYoutubeModal({{ $k->id }}, 'kajian')"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-900 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                            </svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Link YouTube</div>
                                                            @if(!empty($k->youtube_url))
                                                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0" title="Link YouTube sudah tersimpan"></span>
                                                            @endif
                                                        </button>

                                                        <!-- Edit Jadwal -->
                                                        <button type="button" 
                                                            @click="openMenu = false; openEditKajian({{ Js::from([
                                                                'id' => $k->id,
                                                                'type' => $k->type,
                                                                'date' => $k->date ? Carbon\Carbon::parse($k->date)->format('Y-m-d') : '',
                                                                'time_display' => $k->time_display,
                                                                'title' => $k->title,
                                                                'speaker_name' => '',
                                                                'speaker_phone' => '',
                                                                'is_holiday_disabled' => (bool) $k->is_holiday_disabled,
                                                                'khatib_name' => $k->khatib_name,
                                                                'mc_name' => $k->mc_name,
                                                                'muadzin_name' => $k->muadzin_name,
                                                                'khatib_phone' => $k->khatib_phone,
                                                                'mc_notes' => $k->mc_notes,
                                                                'youtube_url' => $k->youtube_url,
                                                            ]) }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Edit Jadwal</div>
                                                        </button>

                                                        <!-- Hapus Jadwal -->
                                                        <button type="button"
                                                            @click="openMenu = false; openDeleteModal({{ Js::from([
                                                                'action' => 'deleteKajian',
                                                                'id' => $k->id,
                                                                'title' => 'Hapus Jadwal Shalat Jumat',
                                                                'message' => 'Apakah Anda yakin ingin menghapus jadwal Shalat Jumat ini?',
                                                                'itemName' => $k->khatib_name ? ('Khatib: ' . $k->khatib_name . ' (' . Carbon\Carbon::parse($k->date)->translatedFormat('d M Y') . ')') : $k->title,
                                                            ]) }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Jadwal</div>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 space-y-2 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-medium text-slate-400">Khatib:</span>
                                        <span class="font-bold text-gov-navy">{{ $k->khatib_name ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-slate-600">
                                        <span class="font-medium text-slate-400">MC:</span>
                                        <span>{{ $k->mc_name ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-slate-600">
                                        <span class="font-medium text-slate-400">Muadzin:</span>
                                        <span>{{ $k->muadzin_name ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-xl border border-gov-border p-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300"></i>
                                    <p>Belum ada jadwal Shalat Jumat yang ditambahkan.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($allKajianJumat, 'hasPages') && $allKajianJumat->hasPages())
                        <div class="pt-2">
                            {{ $allKajianJumat->links(data: ['scrollTo' => false]) }}
                        </div>
                    @endif
                </div>

                <!-- SUB-TAB 3: TABEL AGENDA KEGIATAN (KEGIATAN AKBAR) -->
                <!-- ======================================================== -->
                <div x-show="kegiatanSubTab === 'agenda'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'agenda' }"
                    class="space-y-3">
                    <!-- Search & Filter Bar -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-gov-border shadow-2xs">
                        <div class="relative flex-1 max-w-sm" x-data="{
                            clearSearch() {
                                $wire.clearAgendaSearch();
                            }
                        }">
                            <i data-lucide="search"
                                class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input wire:key="agenda-search-input" wire:model.live.debounce.300ms="agendaSearch" type="text"
                                placeholder="Cari agenda kegiatan..."
                                class="h-[32px] w-full pl-8 pr-9 py-1 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                            <button type="button" @click="clearSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 transition cursor-pointer flex items-center justify-center" title="Reset pencarian ke data seharusnya">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <select wire:key="agenda-status-filter" wire:model.live="agendaFilterStatus"
                                class="w-full sm:w-auto px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-gov-navy cursor-pointer">
                                <option value="all">Semua Status</option>
                                <option value="Direncanakan">Direncanakan</option>
                                <option value="Berjalan">Berjalan</option>
                                <option value="SELESAI">Selesai</option>
                            </select>

                            @if($agendaFilterStatus !== 'all' || !empty($agendaSearch))
                                <button type="button" wire:click="resetAgendaFilters"
                                    class="shrink-0 px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-gov-border hover:border-rose-200 transition cursor-pointer flex items-center gap-1"
                                    title="Reset Filter ke Semua">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span class="hidden sm:inline">Reset</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Desktop Executive Table -->
                    <div
                        class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-visible">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-gov-border text-slate-500 uppercase tracking-wider text-xs font-semibold">
                                    <th class="p-3.5 text-center w-12 rounded-tl-xl">No</th>
                                    <th wire:click="sortBy('title', 'agenda')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Nama Agenda Kegiatan</span>
                                            <x-sort-icon field="title" table="agenda" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('event_date', 'agenda')" class="p-3.5 w-40 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Tanggal Kegiatan</span>
                                            <x-sort-icon field="event_date" table="agenda" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('budget', 'agenda')" class="p-3.5 w-32 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Anggaran</span>
                                            <x-sort-icon field="budget" table="agenda" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('committee_members', 'agenda')" class="p-3.5 w-32 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Panitia</span>
                                            <x-sort-icon field="committee_members" table="agenda" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('status', 'agenda')" class="p-3.5 w-32 whitespace-nowrap cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Status</span>
                                            <x-sort-icon field="status" table="agenda" />
                                        </div>
                                    </th>
                                    <th class="p-3.5 text-center w-20 rounded-tr-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 transition-opacity duration-150">
                                @forelse($allAgendas as $idx => $agenda)
                                    <tr wire:key="agenda-row-{{ $agenda->id }}" class="hover:bg-slate-50/80 transition">
                                        <td class="p-3.5 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                        <td class="p-3.5">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-gov-textMain">{{ $agenda->title }}</span>
                                                @if(!empty($agenda->youtube_url))
                                                    <a href="{{ $agenda->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-100 hover:bg-rose-200 text-rose-800 transition" title="Buka siaran YouTube">
                                                        <svg class="w-3 h-3 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                        </svg>
                                                        YouTube Ada
                                                    </a>
                                                @endif
                                            </div>
                                            @if($agenda->description)
                                                <div class="text-slate-500 text-[11px] line-clamp-1 mt-0.5">
                                                    {{ $agenda->description }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            <div class="font-bold text-gov-textMain">
                                                {{ Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d M Y') }}
                                            </div>
                                            <div class="text-slate-500 text-[11px]">
                                                {{ Carbon\Carbon::parse($agenda->event_date)->translatedFormat('l') }}
                                            </div>
                                        </td>
                                        <td class="p-3.5 font-bold text-slate-800 whitespace-nowrap">
                                            {{ $agenda->formatted_budget }}
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                <i data-lucide="users" class="w-3 h-3 text-slate-500"></i>
                                                <span>{{ $agenda->committee_count }} Orang</span>
                                            </span>
                                        </td>
                                        <td class="p-3.5 whitespace-nowrap">
                                            @if(Auth::user()->canManage())
                                                <select wire:change="updateAgendaStatus({{ $agenda->id }}, $event.target.value)"
                                                    class="table-select text-xs font-medium rounded-lg border border-gov-border py-1 px-2 focus:ring-1 focus:ring-gov-navy transition cursor-pointer
                                                    {{ $agenda->status === 'Direncanakan' ? 'bg-sky-50 text-sky-800 border-sky-200' : ($agenda->status === 'Berjalan' ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200') }}">
                                                    <option value="Direncanakan" @selected($agenda->status === 'Direncanakan')>
                                                        Direncanakan</option>
                                                    <option value="Berjalan" @selected($agenda->status === 'Berjalan')>Berjalan
                                                    </option>
                                                    <option value="SELESAI" @selected($agenda->status === 'SELESAI')>Selesai
                                                    </option>
                                                </select>
                                            @else
                                                <span
                                                    class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold
                                                    {{ $agenda->status === 'Direncanakan' ? 'bg-sky-100 text-sky-800' : ($agenda->status === 'Berjalan' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                                    {{ $agenda->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3.5 text-center whitespace-nowrap relative" x-data="{ openMenu: false }">
                                            <div class="inline-flex items-center justify-center">
                                                <!-- Tombol Titik Tiga Vertikal -->
                                                <button type="button" 
                                                    @click="openMenu = !openMenu" 
                                                    class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                                    :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''"
                                                    title="Menu Aksi">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                                    </svg>
                                                </button>

                                                <!-- Dropdown Menu Aksi -->
                                                <div x-show="openMenu" 
                                                    x-cloak
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                    @click.outside="openMenu = false"
                                                    @keydown.escape.window="openMenu = false"
                                                    class="absolute right-3 top-full mt-1 w-56 bg-white rounded-xl shadow-xl border border-gov-border py-1 z-40 text-xs text-left divide-y divide-slate-100">
                                                    
                                                    <div class="py-1">
                                                        <!-- Cetak Berkas LPJ -->
                                                        <a href="{{ route('admin.agenda.lpj', $agenda->id) }}" target="_blank"
                                                            @click="openMenu = false"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-sky-50 hover:text-sky-800 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-sky-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Cetak Berkas LPJ</div>
                                                        </a>
                                                    </div>

                                                    @if(Auth::user()->canManage())
                                                        <div class="py-1">
                                                            <!-- Link YouTube -->
                                                            <button type="button"
                                                                @click="openMenu = false; $wire.openYoutubeModal({{ $agenda->id }}, 'agenda')"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-900 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                                </svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Link YouTube</div>
                                                                @if(!empty($agenda->youtube_url))
                                                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0" title="Link YouTube sudah tersimpan"></span>
                                                                @endif
                                                            </button>

                                                            <!-- Edit Agenda -->
                                                            <button type="button" 
                                                                @click="openMenu = false; openEditAgenda({{ Js::from($agenda) }})"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Edit Agenda</div>
                                                            </button>

                                                            <!-- Hapus Agenda -->
                                                            <button type="button"
                                                                @click="openMenu = false; openDeleteModal({{ Js::from([
                                                                    'action' => 'deleteAgenda',
                                                                    'id' => $agenda->id,
                                                                    'title' => 'Hapus Agenda Kegiatan',
                                                                    'message' => 'Apakah Anda yakin ingin menghapus agenda kegiatan ini?',
                                                                    'itemName' => $agenda->title,
                                                                ]) }})"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                                <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Agenda</div>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-8 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300"></i>
                                                <p>Belum ada data agenda kegiatan yang sesuai filter.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Responsive Cards -->
                    <div class="md:hidden space-y-3">
                        @forelse($allAgendas as $idx => $agenda)
                            <div wire:key="agenda-card-{{ $agenda->id }}" class="bg-white rounded-xl border border-gov-border p-4 shadow-2xs space-y-3" x-data="{ openMenu: false }">
                                <div class="flex items-start justify-between gap-2 relative">
                                    <div>
                                        <div class="font-bold text-gov-textMain text-sm">{{ $agenda->title }}</div>
                                        <div class="text-slate-500 text-xs">
                                            {{ Carbon\Carbon::parse($agenda->event_date)->translatedFormat('l, d M Y') }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span
                                            class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold
                                            {{ $agenda->status === 'Direncanakan' ? 'bg-sky-100 text-sky-800' : ($agenda->status === 'Berjalan' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                            {{ $agenda->status }}
                                        </span>
                                        @if(!empty($agenda->youtube_url))
                                            <a href="{{ $agenda->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1 h-[20px] px-2 rounded-full text-[10px] font-semibold bg-rose-100 hover:bg-rose-200 text-rose-800 border border-rose-300 leading-none transition" title="Buka siaran YouTube">
                                                <svg class="w-2.5 h-2.5 text-rose-600" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                </svg>
                                                YouTube Ada
                                            </a>
                                        @endif

                                        <!-- Tombol Titik Tiga Vertikal Mobile -->
                                        <div class="relative">
                                            <button type="button" 
                                                @click="openMenu = !openMenu" 
                                                class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                                :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''"
                                                title="Menu Aksi">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu Aksi Mobile -->
                                            <div x-show="openMenu" 
                                                x-cloak
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                @click.outside="openMenu = false"
                                                @keydown.escape.window="openMenu = false"
                                                class="absolute right-0 top-full mt-1 w-56 bg-white rounded-xl shadow-xl border border-gov-border py-1 z-40 text-xs text-left divide-y divide-slate-100">
                                                
                                                <div class="py-1">
                                                    <!-- Cetak Berkas LPJ -->
                                                    <a href="{{ route('admin.agenda.lpj', $agenda->id) }}" target="_blank"
                                                        @click="openMenu = false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-sky-50 hover:text-sky-800 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-sky-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Cetak Berkas LPJ</div>
                                                    </a>
                                                </div>

                                                @if(Auth::user()->canManage())
                                                    <div class="py-1">
                                                        <!-- Link YouTube -->
                                                        <button type="button"
                                                            @click="openMenu = false; $wire.openYoutubeModal({{ $agenda->id }}, 'agenda')"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-900 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                            </svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Link YouTube</div>
                                                            @if(!empty($agenda->youtube_url))
                                                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0" title="Link YouTube sudah tersimpan"></span>
                                                            @endif
                                                        </button>

                                                        <!-- Edit Agenda -->
                                                        <button type="button" 
                                                            @click="openMenu = false; openEditAgenda({{ Js::from($agenda) }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Edit Agenda</div>
                                                        </button>

                                                        <!-- Hapus Agenda -->
                                                        <button type="button"
                                                            @click="openMenu = false; openDeleteModal({{ Js::from([
                                                                'action' => 'deleteAgenda',
                                                                'id' => $agenda->id,
                                                                'title' => 'Hapus Agenda Kegiatan',
                                                                'message' => 'Apakah Anda yakin ingin menghapus agenda kegiatan ini?',
                                                                'itemName' => $agenda->title,
                                                            ]) }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Agenda</div>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-slate-100">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold block">Anggaran</span>
                                        <span class="font-bold text-slate-800">{{ $agenda->formatted_budget }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold block">Panitia</span>
                                        <span class="font-medium text-slate-700">{{ $agenda->committee_count }} Orang</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 bg-white rounded-xl border border-gov-border">
                                <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                                <p>Belum ada data agenda kegiatan yang sesuai filter.</p>
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($allAgendas, 'hasPages') && $allAgendas->hasPages())
                        <div class="pt-2">
                            {{ $allAgendas->links(data: ['scrollTo' => false]) }}
                        </div>
                    @endif
                </div>


                <!-- SUB-TAB 4: ONE DAY ONE JUZ (ODOJ) -->
                <!-- ======================================================== -->
                <div x-show="kegiatanSubTab === 'odoj'" x-cloak :class="{ 'hidden': kegiatanSubTab !== 'odoj' }"
                    class="space-y-4">
                    <!-- Top Filter Bar: Date selector & batch status actions -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-gov-border shadow-2xs">

                        <!-- Date Navigator: Previous, Input Date, Next -->
                        <div class="flex items-center gap-1.5 w-full sm:w-auto">
                            <button type="button" wire:click="previousOdojDate" title="Hari Sebelumnya"
                                class="w-9 py-1.5 rounded-lg border border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-gov-navy transition cursor-pointer inline-flex items-center justify-center shrink-0 shadow-2xs">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            </button>
                            <input type="date" wire:model.live="odojDate"
                                class="flex-1 sm:w-36 px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-bold text-slate-800 text-center focus:ring-1 focus:ring-gov-navy focus:outline-none cursor-pointer shadow-2xs">
                            <button type="button" wire:click="nextOdojDate" title="Hari Berikutnya"
                                class="w-9 py-1.5 rounded-lg border border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-gov-navy transition cursor-pointer inline-flex items-center justify-center shrink-0 shadow-2xs">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <!-- Right Control Group: Indicator Badge & Action Buttons (Align Right on Desktop) -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2.5 w-full sm:w-auto">
                            <!-- Indicator Badge: 0/30 Juz Selesai -->
                            <div
                                class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-slate-50 text-gov-textMain text-xs font-bold border border-gov-border shadow-2xs shrink-0">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block mr-1.5 shrink-0"></span>
                                <span class="whitespace-nowrap">{{ $odojCompletedCount }}/30 Juz Selesai</span>
                            </div>

                            @if(Auth::user()->canManage())
                                <!-- Action Buttons: Grid 2 cols on Mobile, Inline on Desktop -->
                                <div class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto shrink-0">
                                    <button type="button" wire:click="setAllOdojStatus('Selesai')" wire:loading.attr="disabled"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 shadow-2xs transition cursor-pointer disabled:opacity-50 shrink-0 whitespace-nowrap">
                                        <i data-lucide="check-check" class="w-3.5 h-3.5 text-emerald-600 shrink-0 inline-block"></i>
                                        <span class="whitespace-nowrap">Semua Sudah</span>
                                    </button>
                                    <button type="button" wire:click="setAllOdojStatus('Belum')" wire:loading.attr="disabled"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs border border-gov-border shadow-2xs transition cursor-pointer disabled:opacity-50 shrink-0 whitespace-nowrap">
                                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-500 shrink-0 inline-block"></i>
                                        <span class="whitespace-nowrap">Reset Belum</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 30-Card Grid: Juz 1 through 30 (3 Kolom pada Desktop & Tablet, 1 Kolom pada Mobile) -->
                    @php
                        $juzMetadata = [
                            1 => "Al-Baqarah : 1",
                            2 => "Al-Baqarah : 142",
                            3 => "Al-Baqarah : 253",
                            4 => "Ali 'Imran : 93",
                            5 => "An-Nisa' : 24",
                            6 => "An-Nisa' : 148",
                            7 => "Al-Ma'idah : 82",
                            8 => "Al-An'am : 111",
                            9 => "Al-A'raf : 88",
                            10 => "Al-Anfal : 41",
                            11 => "At-Taubah : 93",
                            12 => "Hud : 6",
                            13 => "Yusuf : 53",
                            14 => "Al-Hijr : 1",
                            15 => "Al-Isra' : 1",
                            16 => "Al-Kahf : 75",
                            17 => "Al-Anbiya' : 1",
                            18 => "Al-Mu'minun : 1",
                            19 => "Al-Furqan : 21",
                            20 => "An-Naml : 56",
                            21 => "Al-'Ankabut : 46",
                            22 => "Al-Ahzab : 31",
                            23 => "Yasin : 28",
                            24 => "Az-Zumar : 32",
                            25 => "Fussilat : 47",
                            26 => "Al-Ahqaf : 1",
                            27 => "Az-Zariyat : 31",
                            28 => "Al-Mujadilah : 1",
                            29 => "Al-Mulk : 1",
                            30 => "An-Naba' : 1",
                        ];
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                        @foreach($odojEntries as $entry)
                            @php
                                $isDone = ($entry->status === 'Selesai');
                                $surahInfo = $juzMetadata[$entry->juz_number] ?? '';
                            @endphp
                            @if($isDone)
                                <!-- COMPACT HORIZONTAL ROW (SUDAH DIBACA) -->
                                <div wire:key="odoj-done-{{ $entry->id }}"
                                    class="bg-emerald-50/80 rounded-xl px-3.5 py-2.5 border border-emerald-200 shadow-xs flex items-center justify-between gap-3 transition-colors">
                                    <!-- Kiri: Nomor Juz Hijau + Nama & Status Waktu -->
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="w-9 h-9 rounded-lg bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                            {{ $entry->juz_number }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate"
                                                title="{{ $entry->jamaah_name ?: 'Belum diatur' }}">
                                                {{ $entry->jamaah_name ?: 'Belum diatur' }}
                                            </p>
                                            <span class="text-[10px] text-emerald-700 font-medium">{{ $surahInfo }}</span>
                                        </div>
                                    </div>

                                    <!-- Kanan: Tombol Edit & Tombol Batalkan -->
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        @if(Auth::user()->canManage())
                                            <button type="button"
                                                @click="openAssignOdoj({{ $entry->juz_number }}, {{ Js::from($entry->jamaah_name) }})"
                                                class="text-slate-400 hover:text-slate-600 p-1 rounded transition-colors cursor-pointer"
                                                title="Ubah Nama">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="setSingleOdojStatus({{ $entry->id }}, 'Belum')"
                                                wire:loading.attr="disabled"
                                                class="group/undo px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 hover:bg-rose-50 text-emerald-800 hover:text-rose-700 border border-emerald-200 hover:border-rose-200 transition-all shadow-2xs cursor-pointer disabled:opacity-50">
                                                <span class="group-hover/undo:hidden inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Sudah
                                                </span>
                                                <span class="hidden group-hover/undo:inline text-rose-700">Batalkan</span>
                                            </button>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                Sudah
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <!-- COMPACT HORIZONTAL ROW (BELUM DIBACA) -->
                                <div wire:key="odoj-todo-{{ $entry->id }}"
                                    class="bg-white rounded-xl px-3.5 py-2.5 border border-slate-200 hover:border-slate-300 shadow-xs flex items-center justify-between gap-3 transition-colors">
                                    <!-- Kiri: Nomor Juz + Nama & Surah -->
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-xs flex items-center justify-center shrink-0 border border-slate-200 shadow-2xs">
                                            {{ $entry->juz_number }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate"
                                                title="{{ $entry->jamaah_name ?: 'Belum diatur' }}">
                                                {{ $entry->jamaah_name ?: 'Belum diatur' }}
                                            </p>
                                            <span class="text-[10px] text-slate-500 font-medium">{{ $surahInfo }}</span>
                                        </div>
                                    </div>

                                    <!-- Kanan: Tombol Edit & Tombol Selesai -->
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        @if(Auth::user()->canManage())
                                            <button type="button"
                                                @click="openAssignOdoj({{ $entry->juz_number }}, {{ Js::from($entry->jamaah_name) }})"
                                                class="text-slate-300 hover:text-slate-600 p-1 rounded transition-colors cursor-pointer"
                                                title="Ubah Nama">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="setSingleOdojStatus({{ $entry->id }}, 'Selesai')"
                                                wire:loading.attr="disabled"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white hover:bg-emerald-600 text-slate-700 hover:text-white border border-slate-200 hover:border-emerald-600 transition-all shadow-2xs flex items-center gap-1 cursor-pointer disabled:opacity-50">
                                                <svg class="w-3 h-3 text-slate-400 group-hover:text-white" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span>Selesai</span>
                                            </button>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                Belum
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- WhatsApp Message Generator Box -->
                    <div class="bg-white rounded-xl border border-gov-border p-5 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2 border-b border-slate-200 pb-3">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200 shrink-0">
                                    <i data-lucide="message-square" class="w-4 h-4 text-emerald-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gov-textMain">Pesan Generator WhatsApp (Laporan
                                        Grup Tilawah)</h4>
                                    <p class="text-xs text-gov-textMuted">Teks rekap otomatis pembagian dan status
                                        tilawah 30 juz harian yang siap disalin ke grup WhatsApp.</p>
                                </div>
                            </div>
                            <button type="button" @click="copyOdojToClipboard({{ Js::from($odojWhatsappText) }})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer shrink-0 whitespace-nowrap"
                                title="Salin pesan laporan WhatsApp ke clipboard">
                                <i data-lucide="copy" class="w-4 h-4 text-amber-400 shrink-0 inline-block"></i>
                                <span class="whitespace-nowrap">Salin Pesan WhatsApp</span>
                            </button>
                        </div>

                        <div id="odoj-whatsapp-text-content"
                            class="bg-slate-50 p-4 rounded-lg border border-gov-border text-xs font-mono text-slate-700 max-h-60 overflow-y-auto leading-relaxed whitespace-pre-line select-all">
                            {{ $odojWhatsappText }}
                        </div>
                    </div>
                </div>
            </div>
