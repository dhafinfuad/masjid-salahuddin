            <!-- ======================================================== -->
            <!-- SUBTAB: KAS & KEUANGAN (MILESTONE 5) -->
            <!-- ======================================================== -->
            <div class="space-y-6">

                <!-- Executive Page Header (Consistent with Kegiatan, Kajian, Petugas, Agenda) -->
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gov-border shadow-2xs">
                    <div class="flex items-center space-x-2.5">
                        <div
                            class="hidden sm:flex w-8 h-8 rounded-lg bg-slate-100 text-gov-navy items-center justify-center border border-gov-border shrink-0">
                            <i data-lucide="wallet" class="w-4 h-4 text-gov-navy"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gov-textMain">Kas & Keuangan Masjid</h3>
                            <p class="text-xs text-gov-textMuted">Pembukuan kas, anggaran, sosial, & laporan.</p>
                        </div>
                    </div>

                    <!-- Right Action Buttons -->
                    <div class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto">
                        @if(Auth::user()->canManage())
                            <button type="button" @click="showFinanceImportModal = true"
                                class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="file-up" class="w-4 h-4 text-gov-navy"></i>
                                <span>Import</span>
                            </button>
                        @endif

                        <!-- Dropdown Export Kas -->
                        <div class="relative w-full sm:w-auto {{ !Auth::user()->canManage() ? 'col-span-2 sm:col-span-1' : '' }}" x-data="{ openFinanceExport: false }"
                            @click.outside="openFinanceExport = false">
                            <button type="button" @click="openFinanceExport = !openFinanceExport"
                                class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="file-down" class="w-4 h-4 text-emerald-600"></i>
                                <span>Export</span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                            </button>
                            <div x-show="openFinanceExport" x-cloak
                                class="absolute right-0 mt-1 w-52 bg-white rounded-lg shadow-xl border border-gov-border py-1.5 z-20 text-xs animate-in fade-in zoom-in-95 duration-100">
                                <a href="{{ route('admin.finance.export-excel') }}"
                                    class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium">
                                    <i data-lucide="sheet" class="w-4 h-4 text-emerald-600"></i>
                                    <span>Download Excel (.csv)</span>
                                </a>
                                <button type="button"
                                    @click="openFinanceExport = false; showPrintFinanceModal = true; $wire.openPrintFinanceModal()"
                                    class="w-full flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-800 transition font-medium text-left cursor-pointer">
                                    <i data-lucide="printer" class="w-4 h-4 text-rose-600"></i>
                                    <span>Cetak Laporan (PDF)</span>
                                </button>
                            </div>
                        </div>

                        @if(Auth::user()->canManage())
                            <button type="button" @click="openCreateFinance()"
                                class="col-span-2 sm:col-span-1 w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer">
                                <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
                                <span>Catat Transaksi</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Sub-tab Navigation Bar (Matching Sub-tab Pattern on Other Pages) -->
                <div class="text-xs flex flex-col sm:flex-row sm:items-center justify-start gap-3 bg-white p-3 rounded-xl border border-gov-border shadow-2xs">
                    <button type="button"
                        @click="financeSubTab = 'utama'; $wire.set('financeSubTab', 'utama', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                        :class="financeSubTab === 'utama' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'"
                        class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2"
                        title="Aliran Kas Utama">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                        <span>Kas Utama</span>
                    </button>
                    <button type="button"
                        @click="financeSubTab = 'kategori'; $wire.set('financeSubTab', 'kategori', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                        :class="financeSubTab === 'kategori' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'"
                        class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2"
                        title="Kelola Kategori Kas">
                        <i data-lucide="tags" class="w-4 h-4"></i>
                        <span>Kategori</span>
                    </button>
                </div>

                <!-- ==================================================== -->
                <!-- SUB-TAB 1: ALIRAN KAS UTAMA -->
                <!-- ==================================================== -->
                <div x-show="financeSubTab === 'utama'" x-cloak :class="{ 'hidden': financeSubTab !== 'utama' }"
                    class="space-y-6">

                    <!-- 4 Financial Metric Cards (Standardized to Dashboard 100% White Cards) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Saldo Awal -->
                        <div
                            class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs hover:border-slate-300 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gov-textMuted">Saldo
                                    Awal</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center border border-slate-200 shadow-2xs">
                                    <i data-lucide="history" class="w-4 h-4 text-slate-600"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-2xl font-bold text-gov-textMain tnum">
                                    Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                                </div>
                                <span class="text-xs text-slate-500 font-medium flex items-center space-x-1 mt-1">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    <span>Awal bulan berjalan</span>
                                </span>
                            </div>
                        </div>

                        <!-- Total Kas Masuk -->
                        <div
                            class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs hover:border-emerald-300 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gov-textMuted">Total
                                    Kas Masuk</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200 shadow-2xs">
                                    <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-600"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-2xl font-bold text-gov-textMain tnum">
                                    Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                                </div>
                                <span class="text-xs text-emerald-600 font-medium flex items-center space-x-1 mt-1">
                                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                                    <span>Infaq, sedekah, & tukin</span>
                                </span>
                            </div>
                        </div>

                        <!-- Total Kas Keluar -->
                        <div
                            class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs hover:border-rose-300 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gov-textMuted">Total
                                    Kas Keluar</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200 shadow-2xs">
                                    <i data-lucide="arrow-up-right" class="w-4 h-4 text-rose-600"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-2xl font-bold text-gov-textMain tnum">
                                    Rp {{ number_format($totalKasKeluar, 0, ',', '.') }}
                                </div>
                                <span class="text-xs text-rose-600 font-medium flex items-center space-x-1 mt-1">
                                    <i data-lucide="trending-down" class="w-3.5 h-3.5"></i>
                                    <span>Operasional & belanja rutin</span>
                                </span>
                            </div>
                        </div>

                        <!-- Saldo Saat Ini (Standard White Card Matching Dashboard) -->
                        <div
                            class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs hover:border-sky-300 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gov-textMuted">Saldo
                                    Kas Saat Ini</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-200 shadow-2xs">
                                    <i data-lucide="wallet" class="w-4 h-4 text-sky-600"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-2xl font-bold text-gov-navy tnum">
                                    Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}
                                </div>
                                <span class="text-xs text-sky-600 font-medium flex items-center space-x-1 mt-1">
                                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                    <span>Total kas bersih tersimpan</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Filter & Search Toolbar Card -->
                    <div class="bg-white rounded-xl border border-gov-border shadow-2xs p-4">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                            <div class="grid grid-cols-3 gap-1.5 w-full sm:flex sm:items-center sm:w-auto">
                                <!-- Type Pills (Matching Sub-tab Pattern) -->
                                <button type="button" wire:click="$set('financeTypeFilter', 'all')"
                                    class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg text-xs transition cursor-pointer text-center"
                                    :class="'{{ $financeTypeFilter }}' === 'all' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white border border-gov-border text-slate-700 hover:bg-slate-50 font-medium'">
                                    Semua
                                </button>
                                <button type="button" wire:click="$set('financeTypeFilter', 'pemasukan')"
                                    class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg text-xs transition cursor-pointer text-center"
                                    :class="'{{ $financeTypeFilter }}' === 'pemasukan' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white border border-gov-border text-slate-700 hover:bg-slate-50 font-medium'">
                                    Pemasukan
                                </button>
                                <button type="button" wire:click="$set('financeTypeFilter', 'pengeluaran')"
                                    class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg text-xs transition cursor-pointer text-center"
                                    :class="'{{ $financeTypeFilter }}' === 'pengeluaran' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white border border-gov-border text-slate-700 hover:bg-slate-50 font-medium'">
                                    Pengeluaran
                                </button>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full lg:w-auto text-xs">
                                <div class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto">
                                    <!-- Month Checklist Multi-Select Dropdown -->
                                    <div class="relative w-full sm:w-40" x-data="{
                                        open: false
                                    }">
                                        <button type="button" @click="open = !open"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium text-gov-textMain cursor-pointer flex items-center justify-between gap-1.5 shadow-2xs hover:bg-slate-50 transition focus:outline-none focus:border-gov-navy select-none"
                                            title="Pilih satu atau beberapa bulan">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                            @php
                                                $monthNames = [
                                                    1 => 'Januari',
                                                    2 => 'Februari',
                                                    3 => 'Maret',
                                                    4 => 'April',
                                                    5 => 'Mei',
                                                    6 => 'Juni',
                                                    7 => 'Juli',
                                                    8 => 'Agustus',
                                                    9 => 'September',
                                                    10 => 'Oktober',
                                                    11 => 'November',
                                                    12 => 'Desember'
                                                ];
                                                $availableMonths = array_map('strval', range(1, 12));
                                                $activeMonthsList = is_array($financeMonthFilter) 
                                                    ? array_values(array_filter($financeMonthFilter, fn($m) => $m !== 'all' && !empty($m)))
                                                    : ($financeMonthFilter !== 'all' && !empty($financeMonthFilter) ? [(string)$financeMonthFilter] : []);
                                                $activeMonthCount = count($activeMonthsList);
                                            @endphp
                                            @if($activeMonthCount === 0)
                                                <span class="text-gov-textMain font-medium truncate">Semua Bulan</span>
                                            @elseif($activeMonthCount === 1)
                                                <span class="text-gov-navy font-bold truncate">
                                                    {{ $monthNames[(int) $activeMonthsList[0]] ?? ('Bulan ' . $activeMonthsList[0]) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 font-bold text-gov-navy truncate">
                                                    <span>{{ $activeMonthCount }} Bulan</span>
                                                </span>
                                            @endif
                                        </div>
                                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                                    </button>

                                        <!-- Dropdown Checklist Panel -->
                                        <div x-show="open" @click.outside="open = false" x-cloak
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute left-0 mt-1.5 w-60 max-w-[calc(100vw-3rem)] bg-white rounded-xl shadow-xl border border-gov-border p-2.5 z-40 space-y-2 text-xs">
                                        
                                        <!-- Header Actions -->
                                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 px-1">
                                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pilih Bulan</span>
                                            <div class="flex items-center gap-2">
                                                <button type="button" 
                                                    wire:click="toggleAllFinanceMonths"
                                                    class="text-[11px] font-bold text-gov-navy hover:underline cursor-pointer">
                                                    <span>{{ $activeMonthCount === 12 ? 'Batal Semua' : 'Pilih Semua' }}</span>
                                                </button>
                                                <span class="text-slate-200">|</span>
                                                <button type="button" 
                                                    wire:click="resetFinanceMonths"
                                                    class="text-[11px] font-medium text-rose-600 hover:underline cursor-pointer">
                                                    Reset
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Checklist Items -->
                                        <div class="max-h-56 overflow-y-auto space-y-0.5 pr-1">
                                            @foreach($monthNames as $mNum => $mName)
                                                @php
                                                    $isCurrentMonth = (int) $mNum === (int) now()->month;
                                                @endphp
                                                <label class="flex items-center justify-between gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-50 transition cursor-pointer select-none group">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <input type="checkbox" wire:model.live="financeMonthFilter" value="{{ (string) $mNum }}"
                                                            class="w-4 h-4 rounded border-gov-border text-gov-navy focus:ring-gov-navy focus:ring-offset-0 cursor-pointer">
                                                        <span class="text-xs font-medium text-gov-textMain group-hover:text-gov-navy truncate">
                                                            {{ $mName }}
                                                        </span>
                                                    </div>
                                                    @if($isCurrentMonth)
                                                        <span class="inline-flex items-center h-[18px] px-1.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                                            Bulan Ini
                                                        </span>
                                                    @endif
                                                </label>
                                            @endforeach
                                        </div>

                                        <!-- Footer Info -->
                                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between px-1 text-[11px] text-slate-400">
                                            <span>12 total bulan</span>
                                            <button type="button" @click="open = false" class="text-xs font-bold text-gov-navy hover:underline cursor-pointer">
                                                Selesai
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                    <!-- Year Filter -->
                                    <div class="w-full sm:w-40">
                                        <select wire:model.live="financeYearFilter"
                                            class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer">
                                            <option value="all">Semua Tahun</option>
                                            <option value="2026">2026</option>
                                            <option value="2025">2025</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Search Input -->
                                <div class="relative w-full sm:w-64" x-data="{
                                    clearSearch() {
                                        $wire.clearFinanceSearch();
                                    }
                                }">
                                    <i data-lucide="search"
                                        class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                                    <input wire:model.live.debounce.300ms="financeSearch" type="text"
                                        placeholder="Cari transaksi..."
                                        class="w-full pl-8 pr-9 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy">
                                    <button type="button" @click="clearSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 transition cursor-pointer flex items-center justify-center" title="Reset pencarian ke data seharusnya">
                                        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Executive Table Card -->
                    <div class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead
                                    class="bg-slate-50 border-b border-gov-border text-xs font-semibold uppercase text-slate-500 tracking-wider">
                                    <tr>
                                        <th wire:click="sortBy('transaction_date', 'finance')" class="p-3.5 pl-5 w-28 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Tanggal</span>
                                                <x-sort-icon field="transaction_date" table="finance" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('category_id', 'finance')" class="p-3.5 w-60 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Pos Kategori</span>
                                                <x-sort-icon field="category_id" table="finance" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('type', 'finance')" class="p-3.5 w-48 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Jenis / Alokasi</span>
                                                <x-sort-icon field="type" table="finance" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('description', 'finance')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Uraian / Keterangan</span>
                                                <x-sort-icon field="description" table="finance" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('amount', 'finance')" class="p-3.5 text-right w-36 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center justify-end gap-1">
                                                <span>Nominal</span>
                                                <x-sort-icon field="amount" table="finance" />
                                            </div>
                                        </th>
                                        <th class="p-3.5 text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 transition-opacity duration-150">
                                    @forelse($financesList as $fin)
                                        <tr wire:key="fin-row-{{ $fin->id }}" class="hover:bg-slate-50/80 transition">
                                            <td class="p-3.5 pl-5 whitespace-nowrap">
                                                <div class="font-medium text-gov-textMain text-xs">
                                                    {{ $fin->transaction_date ? $fin->transaction_date->format('d/m/Y') : '-' }}
                                                </div>
                                            </td>
                                            <td class="p-3.5">
                                                <div class="font-semibold text-gov-textMain text-xs line-clamp-2 leading-relaxed mt-1"
                                                    title="{{ $fin->category?->name ?? 'Kas Umum' }}">
                                                    {{ $fin->category?->name ?? 'Kas Umum' }}
                                                </div>
                                            </td>
                                            <td class="p-3.5">
                                                <div class="mb-2">
                                                    @if($fin->type === 'pemasukan')
                                                        <span
                                                            class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 leading-none">
                                                            Pemasukan
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800 border border-rose-300 leading-none">
                                                            Pengeluaran
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="mb-2">
                                                    <span
                                                        class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-300 leading-none"
                                                        title="{{ $fin->program_name ?: 'Kas Umum' }}">
                                                        {{ $fin->program_name ?: 'Kas Umum' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="p-3.5 text-slate-700 whitespace-normal break-words [overflow-wrap:break-word] leading-relaxed"
                                                title="{{ $fin->description }}">
                                                {{ $fin->description }}
                                            </td>
                                            <td
                                                class="p-3.5 whitespace-nowrap text-right font-bold text-sm text-gov-textMain tnum">
                                                {{ $fin->type === 'pemasukan' ? '+' : '-' }}Rp
                                                {{ number_format($fin->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="p-3.5 whitespace-nowrap text-center">
                                                <div class="inline-flex items-center justify-center gap-1.5">
                                                    @php
                                                        $receiptUrls = $fin->receipt_urls;
                                                        $receiptCount = count($receiptUrls);
                                                    @endphp
                                                    @if($receiptCount > 0)
                                                        <button type="button"
                                                            @click="openReceiptModal({{ Js::from([
                                                                'items' => $receiptUrls,
                                                                'description' => $fin->description,
                                                                'date' => $fin->transaction_date ? $fin->transaction_date->translatedFormat('d F Y') : '-',
                                                                'amount' => ($fin->type === 'pemasukan' ? '+' : '-') . 'Rp ' . number_format($fin->amount, 0, ',', '.'),
                                                                'type' => $fin->type,
                                                            ]) }})"
                                                            class="inline-flex items-center justify-center gap-1 p-1.5 px-2 rounded-lg border border-sky-200 bg-sky-50 hover:bg-sky-100 text-sky-700 shadow-2xs transition cursor-pointer"
                                                            title="Lihat Bukti Transfer / Kwitansi ({{ $receiptCount }} berkas)">
                                                            <svg class="w-3.5 h-3.5 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                                <circle cx="12" cy="12" r="3"/>
                                                            </svg>
                                                            @if($receiptCount > 1)
                                                                <span class="text-[11px] font-bold text-sky-800 leading-none">{{ $receiptCount }}</span>
                                                            @endif
                                                        </button>
                                                    @endif

                                                    @if(Auth::user()->canManage())
                                                        <button type="button" @click="openEditFinance({{ Js::from($fin) }})"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700 shadow-2xs transition cursor-pointer"
                                                            title="Edit Transaksi">
                                                            <i data-lucide="edit-2" class="w-3.5 h-3.5 text-amber-600"></i>
                                                        </button>
                                                        <button type="button"
                                                            @click="openDeleteModal({{ Js::from([
                                                                'title' => 'Hapus Transaksi Kas',
                                                                'message' => 'Yakin ingin menghapus catatan transaksi kas ini?',
                                                                'itemName' => $fin->description . ' (Rp ' . number_format($fin->amount, 0, ',', '.') . ')',
                                                                'action' => 'deleteFinance',
                                                                'id' => $fin->id,
                                                            ]) }})"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 shadow-2xs transition cursor-pointer"
                                                            title="Hapus Transaksi">
                                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                        </button>
                                                    @elseif($receiptCount === 0)
                                                        <span class="text-slate-300">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="p-8 text-center text-slate-400">
                                                <div class="flex flex-col items-center justify-center py-6">
                                                    <i data-lucide="receipt" class="w-8 h-8 text-slate-300 mb-2"></i>
                                                    <p class="font-medium text-slate-500">Tidak ada transaksi kas yang
                                                        sesuai dengan filter.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(method_exists($financesList, 'hasPages') && $financesList->hasPages())
                            <div class="p-3 bg-white border-t border-gov-border">
                                {{ $financesList->links(data: ['scrollTo' => false]) }}
                            </div>
                        @endif
                    </div>

                    <!-- Mobile Standalone Responsive Cards -->
                    <div class="md:hidden space-y-3" wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage">
                        @forelse($financesList as $fin)
                            <div wire:key="fin-card-{{ $fin->id }}" class="bg-white rounded-xl border border-gov-border p-4 shadow-2xs space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="space-y-0.5">
                                        <div class="text-[11px] font-medium text-slate-400">
                                            {{ $fin->transaction_date ? $fin->transaction_date->translatedFormat('d M Y') : '-' }}
                                        </div>
                                        <div class="font-bold text-gov-textMain text-sm leading-snug">
                                            {{ $fin->category?->name ?? 'Kas Umum' }}
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="font-bold text-sm tnum {{ $fin->type === 'pemasukan' ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $fin->type === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($fin->amount, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-1.5">
                                    @if($fin->type === 'pemasukan')
                                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 leading-none">
                                            Pemasukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-800 border border-rose-300 leading-none">
                                            Pengeluaran
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-300 leading-none">
                                        {{ $fin->program_name ?: 'Kas Umum' }}
                                    </span>
                                </div>

                                @if($fin->description)
                                    <div class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100 leading-relaxed break-words">
                                        {{ $fin->description }}
                                    </div>
                                @endif

                                @php
                                    $receiptUrls = $fin->receipt_urls;
                                    $receiptCount = count($receiptUrls);
                                @endphp
                                @if($receiptCount > 0 || Auth::user()->canManage())
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-1.5">
                                        @if($receiptCount > 0)
                                            <button type="button"
                                                @click="openReceiptModal({{ Js::from([
                                                    'items' => $receiptUrls,
                                                    'description' => $fin->description,
                                                    'date' => $fin->transaction_date ? $fin->transaction_date->translatedFormat('d F Y') : '-',
                                                    'amount' => ($fin->type === 'pemasukan' ? '+' : '-') . 'Rp ' . number_format($fin->amount, 0, ',', '.'),
                                                    'type' => $fin->type,
                                                ]) }})"
                                                class="inline-flex items-center justify-center gap-1 p-1.5 px-2 rounded-lg border border-sky-200 bg-sky-50 hover:bg-sky-100 text-sky-700 shadow-2xs transition cursor-pointer"
                                                title="Lihat Bukti Transfer / Kwitansi ({{ $receiptCount }} berkas)">
                                                <svg class="w-3.5 h-3.5 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                                @if($receiptCount > 1)
                                                    <span class="text-[11px] font-bold text-sky-800 leading-none">{{ $receiptCount }}</span>
                                                @endif
                                            </button>
                                        @endif
                                        @if(Auth::user()->canManage())
                                            <button type="button" @click="openEditFinance({{ Js::from($fin) }})"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700 shadow-2xs transition cursor-pointer"
                                                title="Edit Transaksi">
                                                <i data-lucide="edit-2" class="w-3.5 h-3.5 text-amber-600"></i>
                                            </button>
                                            <button type="button"
                                                @click="openDeleteModal({{ Js::from([
                                                    'title' => 'Hapus Transaksi Kas',
                                                    'message' => 'Yakin ingin menghapus catatan transaksi kas ini?',
                                                    'itemName' => $fin->description . ' (Rp ' . number_format($fin->amount, 0, ',', '.') . ')',
                                                    'action' => 'deleteFinance',
                                                    'id' => $fin->id,
                                                ]) }})"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 shadow-2xs transition cursor-pointer"
                                                title="Hapus Transaksi">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="bg-white rounded-xl border border-gov-border p-8 text-center text-slate-400 shadow-2xs">
                                <div class="flex flex-col items-center justify-center py-4">
                                    <i data-lucide="receipt" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500 text-xs">Tidak ada transaksi kas yang sesuai dengan filter.</p>
                                </div>
                            </div>
                        @endforelse

                        @if(method_exists($financesList, 'hasPages') && $financesList->hasPages())
                            <div class="p-3 bg-white rounded-xl border border-gov-border shadow-2xs">
                                {{ $financesList->links(data: ['scrollTo' => false]) }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ==================================================== -->
                <!-- SUB-TAB 2: KELOLA KATEGORI KAS & POS ANGGARAN -->
                <!-- ==================================================== -->
                <div x-show="financeSubTab === 'kategori'" x-cloak :class="{ 'hidden': financeSubTab !== 'kategori' }"
                    class="space-y-6" x-data="{
                        catFilter: 'all',
                        catSearch: '',
                        matches(group, name) {
                            const matchGroup = (this.catFilter === 'all' || this.catFilter === group);
                            const matchSearch = (!this.catSearch || name.toLowerCase().includes(this.catSearch.toLowerCase()));
                            return matchGroup && matchSearch;
                        }
                    }">

                    <!-- 4 Category Metric Cards (Standardized White Cards) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Total Pos Anggaran -->
                        <div
                            class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs hover:border-slate-300 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gov-textMuted">Total
                                    Pos Anggaran</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center border border-slate-200 shadow-2xs">
                                    <i data-lucide="tags" class="w-4 h-4 text-slate-600"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-2xl font-bold text-gov-textMain tnum">
                                    {{ $financeCategories->count() }} <span
                                        class="text-sm font-semibold text-slate-500">Pos</span>
                                </div>
                                <span class="text-xs text-slate-500 font-medium flex items-center space-x-1 mt-1">
                                    <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                                    <span>Pos anggaran terdaftar</span>
                                </span>
                            </div>
                        </div>

                        <!-- Pos Penerimaan -->
                        @php
                            $penerimaanCats = $financeCategories->where('group', 'penerimaan');
                            $totalPenerimaanNominal = $penerimaanCats->sum('finances_sum_amount');
                        @endphp
                        <div
                            class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs hover:border-emerald-300 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gov-textMuted">Pos
                                    Penerimaan</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200 shadow-2xs">
                                    <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-600"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-2xl font-bold text-gov-textMain tnum">
                                    {{ $penerimaanCats->count() }} <span
                                        class="text-sm font-semibold text-slate-500">Pos</span>
                                </div>
                                <span class="text-xs text-emerald-600 font-medium flex items-center space-x-1 mt-1">
                                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                                    <span>Akumulasi: Rp {{ number_format($totalPenerimaanNominal, 0, ',', '.') }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Pos Pengeluaran Rutin -->
                        @php
                            $pengeluaranRutinCats = $financeCategories->where('group', 'pengeluaran_rutin');
                            $totalPengeluaranRutinNominal = $pengeluaranRutinCats->sum('finances_sum_amount');
                        @endphp
                        <div
                            class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs hover:border-rose-300 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs font-semibold uppercase tracking-wider text-gov-textMuted">Pengeluaran
                                    Rutin</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-200 shadow-2xs">
                                    <i data-lucide="arrow-up-right" class="w-4 h-4 text-rose-600"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-2xl font-bold text-gov-textMain tnum">
                                    {{ $pengeluaranRutinCats->count() }} <span
                                        class="text-sm font-semibold text-slate-500">Pos</span>
                                </div>
                                <span class="text-xs text-rose-600 font-medium flex items-center space-x-1 mt-1">
                                    <i data-lucide="trending-down" class="w-3.5 h-3.5"></i>
                                    <span>Akumulasi: Rp
                                        {{ number_format($totalPengeluaranRutinNominal, 0, ',', '.') }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Pos Pengeluaran Non-Rutin -->
                        @php
                            $pengeluaranNonRutinCats = $financeCategories->where('group', 'pengeluaran_nonrutin');
                            $totalPengeluaranNonRutinNominal = $pengeluaranNonRutinCats->sum('finances_sum_amount');
                        @endphp
                        <div
                            class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs hover:border-amber-300 transition flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs font-semibold uppercase tracking-wider text-gov-textMuted">Pengeluaran
                                    Non-Rutin</span>
                                <div
                                    class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200 shadow-2xs">
                                    <i data-lucide="wrench" class="w-4 h-4 text-amber-600"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-2xl font-bold text-gov-textMain tnum">
                                    {{ $pengeluaranNonRutinCats->count() }} <span
                                        class="text-sm font-semibold text-slate-500">Pos</span>
                                </div>
                                <span class="text-xs text-amber-600 font-medium flex items-center space-x-1 mt-1">
                                    <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                                    <span>Akumulasi: Rp
                                        {{ number_format($totalPengeluaranNonRutinNominal, 0, ',', '.') }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Search & Filter Bar (Standalone Card) -->
                    <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-gov-border shadow-2xs">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 flex-1">
                            <!-- Search Input -->
                            <div class="relative flex-1 max-w-sm" x-data="{
                                clearSearch() {
                                    catSearch = '';
                                }
                            }">
                                <i data-lucide="search"
                                    class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                                <input x-model="catSearch" type="text" placeholder="Cari pos anggaran..."
                                    class="w-full pl-8 pr-9 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                                <button type="button" @click="clearSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 transition cursor-pointer flex items-center justify-center" title="Reset pencarian ke data seharusnya">
                                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>

                            <!-- Kelompok / Status Filter Dropdown -->
                            <div class="w-full sm:w-auto">
                                <select x-model="catFilter"
                                    class="w-full sm:w-auto px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-gov-navy cursor-pointer">
                                    <option value="all">Semua Pos ({{ $financeCategories->count() }})</option>
                                    <option value="penerimaan">Penerimaan ({{ $penerimaanCats->count() }})</option>
                                    <option value="pengeluaran_rutin">Pengeluaran Rutin ({{ $pengeluaranRutinCats->count() }})</option>
                                    <option value="pengeluaran_nonrutin">Non-Rutin ({{ $pengeluaranNonRutinCats->count() }})</option>
                                </select>
                            </div>
                        </div>

                        <!-- Add Button -->
                        @if(Auth::user()->canManage())
                            <div class="w-full sm:w-auto flex items-center">
                                <button type="button" @click="openCreateFinanceCategory()"
                                    class="w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer shrink-0">
                                    <i data-lucide="plus" class="w-3.5 h-3.5 text-amber-400"></i>
                                    <span>Tambah Pos Anggaran</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Tabel Daftar Pos Anggaran Lengkap (Full-Width Card) -->
                    <!-- Desktop Table of Categories -->
                    <div class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr
                                        class="bg-slate-50 border-b border-slate-300 text-gov-textMuted uppercase font-bold text-[11px] tracking-wider">
                                        <th wire:click="sortBy('name', 'finance_categories')" class="p-3.5 pl-4 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Pos Anggaran</span>
                                                <x-sort-icon field="name" table="finance_categories" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('group', 'finance_categories')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Kelompok</span>
                                                <x-sort-icon field="group" table="finance_categories" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('finances_count', 'finance_categories')" class="p-3.5 text-center cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center justify-center gap-1">
                                                <span>Transaksi</span>
                                                <x-sort-icon field="finances_count" table="finance_categories" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('finances_sum_amount', 'finance_categories')" class="p-3.5 text-right cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center justify-end gap-1">
                                                <span>Total Akumulasi</span>
                                                <x-sort-icon field="finances_sum_amount" table="finance_categories" />
                                            </div>
                                        </th>
                                        <th class="p-3.5 text-right pr-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-300 font-medium text-slate-700">
                                    @forelse($financeCategories as $fc)
                                        <tr wire:key="fincat-row-{{ $fc->id }}" x-show="matches({{ Js::from($fc->group) }}, {{ Js::from(strtolower($fc->name)) }})"
                                            class="hover:bg-slate-50/80 transition">
                                            <td class="p-3.5 pl-4">
                                                <div class="flex items-center gap-2.5">
                                                    <span
                                                        class="w-2.5 h-2.5 rounded-full shrink-0 {{ $fc->group === 'penerimaan' ? 'bg-emerald-500' : ($fc->group === 'pengeluaran_rutin' ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                                                    <span class="font-bold text-gov-textMain">{{ $fc->name }}</span>
                                                </div>
                                            </td>
                                            <td class="p-3.5">
                                                @if($fc->group === 'penerimaan')
                                                    <span
                                                        class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 leading-none">
                                                        Penerimaan
                                                    </span>
                                                @elseif($fc->group === 'pengeluaran_rutin')
                                                    <span
                                                        class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 leading-none">
                                                        Pengeluaran Rutin
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 leading-none">
                                                        Pengeluaran Non-Rutin
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="p-3.5 text-center">
                                                <span
                                                    class="inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 leading-none">
                                                    {{ $fc->finances_count }} transaksi
                                                </span>
                                            </td>
                                            <td class="p-3.5 text-right font-bold text-gov-textMain">
                                                Rp {{ number_format($fc->finances_sum_amount ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="p-3.5 text-right pr-4">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    @if(Auth::user()->canManage())
                                                        <button type="button"
                                                            @click="openEditFinanceCategory({{ Js::from(['id' => $fc->id, 'name' => $fc->name, 'group' => $fc->group, 'type' => $fc->type, 'color' => $fc->color]) }})"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700 shadow-2xs transition cursor-pointer"
                                                            title="Edit Pos Anggaran">
                                                            <i data-lucide="edit-2" class="w-3.5 h-3.5 text-amber-600"></i>
                                                        </button>
                                                        <button type="button"
                                                            @click="openDeleteModal({{ Js::from([
                                                                'title' => 'Hapus Pos Anggaran',
                                                                'message' => 'Apakah Anda yakin ingin menghapus pos kategori ini? Transaksi yang terhubung akan tetap tersimpan.',
                                                                'itemName' => $fc->name,
                                                                'action' => 'deleteFinanceCategory',
                                                                'id' => $fc->id,
                                                            ]) }})"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 shadow-2xs transition cursor-pointer"
                                                            title="Hapus Pos Anggaran">
                                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-[11px] text-slate-400 italic">Hanya Lihat</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-8 text-center text-slate-400">
                                                <div class="flex flex-col items-center justify-center py-4">
                                                    <i data-lucide="tags" class="w-8 h-8 text-slate-300 mb-2"></i>
                                                    <p class="font-medium text-slate-500">Belum ada pos anggaran terdaftar.
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Standalone Responsive Cards -->
                    <div class="md:hidden space-y-3">
                        @forelse($financeCategories as $fc)
                            <div wire:key="fincat-card-{{ $fc->id }}" x-show="matches({{ Js::from($fc->group) }}, {{ Js::from(strtolower($fc->name)) }})"
                                class="bg-white rounded-xl border border-gov-border p-4 shadow-2xs space-y-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $fc->group === 'penerimaan' ? 'bg-emerald-500' : ($fc->group === 'pengeluaran_rutin' ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
                                    <h5 class="font-bold text-sm text-gov-textMain">{{ $fc->name }}</h5>
                                </div>

                                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                                    <span class="inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 leading-none">
                                        {{ $fc->finances_count }} transaksi
                                    </span>
                                    <span class="font-bold text-gov-textMain text-sm">
                                        Rp {{ number_format($fc->finances_sum_amount ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-end gap-1.5 pt-2 border-t border-slate-100">
                                    @if(Auth::user()->canManage())
                                        <button type="button"
                                            @click="openEditFinanceCategory({{ Js::from(['id' => $fc->id, 'name' => $fc->name, 'group' => $fc->group, 'type' => $fc->type, 'color' => $fc->color]) }})"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700 shadow-2xs transition cursor-pointer"
                                            title="Edit Pos Anggaran">
                                            <i data-lucide="edit-2" class="w-3.5 h-3.5 text-amber-600"></i>
                                        </button>
                                        <button type="button"
                                            @click="openDeleteModal({{ Js::from([
                                                'title' => 'Hapus Pos Anggaran',
                                                'message' => 'Apakah Anda yakin ingin menghapus pos kategori ini? Transaksi yang terhubung akan tetap tersimpan.',
                                                'itemName' => $fc->name,
                                                'action' => 'deleteFinanceCategory',
                                                'id' => $fc->id,
                                            ]) }})"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 shadow-2xs transition cursor-pointer"
                                            title="Hapus Pos Anggaran">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Hanya Lihat</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-xl border border-gov-border p-8 text-center text-slate-400 shadow-2xs">
                                <div class="flex flex-col items-center justify-center py-4">
                                    <i data-lucide="tags" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500">Belum ada pos anggaran terdaftar.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>
