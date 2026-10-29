<div x-data="petugasSholatComponent()" 
    class="min-h-screen flex flex-col bg-gov-canvas text-gov-textMain">
    
    <!-- Top Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gov-border shadow-2xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" wire:navigate class="inline-flex items-center justify-center p-2 rounded-lg border border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-600 transition shadow-2xs cursor-pointer" title="Kembali ke Portal">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>

                <div>
                    <h1 class="text-sm sm:text-base font-bold text-gov-textMain leading-tight">Jadwal Petugas Shalat Bulanan</h1>
                    <p class="text-[11px] text-slate-500 leading-none mt-0.5">{{ $settings->name ?? 'Masjid Salahuddin' }} • KPP Madya Malang</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Filter & Header Card -->
        <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-2">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">JADWAL PENUGASAN BULANAN</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-gov-textMain mt-0.5">
                        Jadwal Imam & Muadzin — {{ $months[$selectedMonth] }} {{ $selectedYear }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Daftar penugasan peribadatan harian (Dzuhur & Ashar) serta Sholat Jumat di {{ $settings->name ?? 'Masjid Salahuddin' }}.
                    </p>
                </div>

                <!-- Livewire Loading Indicator -->
                <div wire:loading.inline-flex style="display: none;" class="inline-flex flex-row items-center gap-2 text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-amber-600 inline-block" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="whitespace-nowrap leading-none">Memperbarui jadwal...</span>
                </div>
            </div>

            <!-- Filters Toolbar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-1">
                
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 min-w-0">
                    <!-- Searchbar Cari Nama Petugas (Kiri di Desktop, Bagian Atas di Mobile) -->
                    <div class="relative w-full sm:w-64">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search"
                            id="search-petugas-input"
                            placeholder="Cari nama petugas..." 
                            class="h-[38px] w-full pl-8 pr-8 py-1.5 text-xs bg-slate-50 border border-gov-border rounded-lg text-gov-textMain placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition shadow-2xs"
                        >
                        @if(!empty($search))
                            <button wire:click="clearSearch" 
                                    type="button" 
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 rounded transition cursor-pointer"
                                    title="Hapus pencarian">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Group Bulan & Tahun: 1 Baris 50% - 50% di Mobile, di kanan searchbar di Desktop -->
                    <div class="grid grid-cols-2 gap-3 w-full sm:flex sm:w-auto sm:items-center">
                        <!-- Filter Bulan -->
                        <div class="w-full sm:w-44 shrink-0">
                            <x-custom-select 
                                id="filter-duty-month"
                                wire:model.live="selectedMonth"
                                :options="$months"
                                placeholder="Pilih Bulan"
                                icon="calendar"
                            />
                        </div>

                        <!-- Filter Tahun -->
                        <div class="w-full sm:w-32 shrink-0">
                            <x-custom-select 
                                id="filter-duty-year"
                                wire:model.live="selectedYear"
                                :options="$years"
                                placeholder="Pilih Tahun"
                                icon="calendar-days"
                            />
                        </div>
                    </div>
                </div>

                <!-- Tombol Unduh PDF Langsung (Uji Coba Direct Download) -->
                <div class="w-full sm:w-auto shrink-0">
                    <button type="button" 
                            @click="downloadMonthlyDirectPdf()" 
                            :disabled="isDownloadingPdf"
                            class="h-[38px] w-full sm:w-auto px-4 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap disabled:opacity-75 disabled:cursor-wait"
                            title="Unduh Jadwal Petugas Shalat Bulanan (PDF)">
                        <span x-show="!isDownloadingPdf" class="inline-flex items-center gap-1.5">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Unduh PDF</span>
                        </span>
                        <span x-show="isDownloadingPdf" style="display: none;" class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menyiapkan PDF...</span>
                        </span>
                    </button>
                </div>

            </div>

        </div>

        <!-- Monthly Duty Schedule Table -->
        <div class="bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-300 text-gov-textMuted uppercase font-bold text-[11px] tracking-wider">
                            <th class="p-3.5 pl-4 text-center w-12">Tgl</th>
                            <th class="p-3.5">Hari & Tanggal</th>
                            <th class="p-3.5 text-center">Pola Pekan</th>
                            <th class="p-3.5">Dzuhur</th>
                            <th class="p-3.5 pr-4">Shalat Ashar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-300 font-medium text-slate-700">
                        @forelse($roster as $row)
                            @php
                                $isToday = $row['is_today'];
                                $isWeekend = $row['is_weekend'];
                                $isFriday = $row['is_friday'];
                            @endphp
                            <tr class="transition {{ $isToday ? 'bg-amber-50/90 hover:bg-amber-100/80 border-l-4 border-l-amber-500 font-semibold' : ($isWeekend ? 'bg-slate-50/40 text-slate-400' : 'hover:bg-slate-50/80') }}">
                                <td class="p-3 pl-4 text-center font-bold {{ $isToday ? 'text-amber-700' : ($isWeekend ? 'text-slate-400' : 'text-slate-600') }}">
                                    {{ $row['day_number'] }}
                                </td>
                                <td class="p-3">
                                    <div class="space-y-0.5">
                                        <div class="font-bold {{ $isToday ? 'text-gov-navy' : ($isWeekend ? 'text-slate-500' : 'text-gov-textMain') }}">{{ $row['day_name'] }}</div>
                                        <div class="text-[11px] text-slate-500 font-normal leading-tight">
                                            {{ $row['formatted_date'] }}
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    @if($isWeekend)
                                        <span class="w-[72px] justify-center inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold border leading-none shrink-0 bg-slate-100 text-slate-500 border-slate-200"
                                              style="width: 72px; min-width: 72px; max-width: 72px;">
                                            Libur
                                        </span>
                                    @elseif($isFriday)
                                        <span class="w-[72px] justify-center inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold border leading-none shrink-0 bg-emerald-50 text-emerald-700 border-emerald-400"
                                              style="width: 72px; min-width: 72px; max-width: 72px;">
                                            Pekan {{ $row['week_number'] }}
                                        </span>
                                    @else
                                        <span class="w-[72px] justify-center inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold border leading-none shrink-0 bg-blue-50 text-blue-700 border-blue-300"
                                              style="width: 72px; min-width: 72px; max-width: 72px;">
                                            Pekan {{ $row['week_number'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    @if($isWeekend)
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @elseif($isFriday)
                                        <div class="space-y-0.5">
                                            <div>
                                                <span>Khatib:</span>
                                                <span class="font-semibold text-gov-textMain ml-1">{{ $row['friday_khatib'] }}</span>
                                            </div>
                                            <div>
                                                <span>Muadzin:</span>
                                                <span class="font-semibold text-gov-textMain ml-1">{{ $row['friday_muadzin'] }}</span>
                                            </div>
                                            @if($row['friday_mc'] !== '-')
                                                <div>
                                                    <span>MC:</span>
                                                    <span class="font-semibold text-gov-textMain ml-1">{{ $row['friday_mc'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="space-y-0.5">
                                            <div>
                                                <span>Imam:</span>
                                                <span class="font-semibold text-gov-textMain ml-1">{{ $row['dzuhur_imam'] }}</span>
                                            </div>
                                            <div>
                                                <span>Muadzin: </span>
                                                <span class="font-semibold text-gov-textMain ml-1">{{ $row['dzuhur_muadzin'] }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3 pr-4">
                                    @if($isWeekend)
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @else
                                        <div class="space-y-0.5">
                                            <div>
                                                <span>Imam:</span>
                                                <span class="font-semibold text-gov-textMain ml-1">{{ $row['ashar_imam'] }}</span>
                                                @if(!empty($row['ashar_kajian_title']))
                                                    <span class="text-[10px] text-emerald-700 font-normal ml-1">({{ $row['ashar_kajian_title'] }})</span>
                                                @endif
                                            </div>
                                            <div>
                                                <span>Muadzin: </span>
                                                <span class="font-semibold text-gov-textMain ml-1">{{ $row['ashar_muadzin'] }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-empty-state 
                                :isTable="true" 
                                colspan="5" 
                                icon="calendar" 
                                title="Tidak ada data petugas sholat" 
                                :subtitle="!empty($search) ? ('Tidak ditemukan jadwal penugasan yang cocok dengan nama petugas \'' . $search . '\'.') : 'Belum ada data penugasan yang sesuai untuk bulan yang dipilih.'" 
                            />
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-slate-200 bg-white">
                @forelse($roster as $row)
                    @php
                        $isToday = $row['is_today'];
                        $isWeekend = $row['is_weekend'];
                        $isFriday = $row['is_friday'];
                    @endphp
                    <div class="p-4 space-y-2.5 transition {{ $isToday ? 'bg-amber-50/90 border-l-4 border-l-amber-500' : ($isWeekend ? 'bg-slate-50/40 text-slate-400' : 'bg-white') }}">
                        <!-- Header: Date & Status Badge -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ $isToday ? 'bg-amber-200 text-amber-900 border border-amber-300' : ($isWeekend ? 'bg-slate-200 text-slate-500' : 'bg-slate-100 text-gov-navy border border-slate-200') }}">
                                    {{ $row['day_number'] }}
                                </span>
                                <div>
                                    <div class="font-bold text-sm {{ $isToday ? 'text-gov-navy' : ($isWeekend ? 'text-slate-500' : 'text-gov-textMain') }}">{{ $row['day_name'] }}</div>
                                    <p class="text-[11px] text-slate-500 font-normal leading-tight">{{ $row['formatted_date'] }}</p>
                                </div>
                            </div>
                            <div>
                                @if($isWeekend)
                                    <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-slate-100 text-slate-500 border-slate-200">
                                        Libur
                                    </span>
                                @elseif($isFriday)
                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-emerald-50 text-emerald-700 border-emerald-400">
                                        Pekan {{ $row['week_number'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">
                                        Pekan {{ $row['week_number'] }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Content: Shalat Dzuhur & Ashar -->
                        @if(!$isWeekend)
                            <div class="grid grid-cols-1 gap-2 pt-2 border-t border-slate-100 text-xs">
                                <!-- Dzuhur / Jumat -->
                                <div class="p-2.5 rounded-lg bg-slate-50/80 border border-slate-100 space-y-1">
                                    <div class="font-bold text-[11px] uppercase tracking-wide {{ $isFriday ? 'text-emerald-700' : 'text-gov-navy' }}">
                                        {{ $isFriday ? 'Shalat Jumat' : 'Shalat Dzuhur' }}
                                    </div>
                                    @if($isFriday)
                                        <div class="space-y-0.5">
                                            <div class="text-[11px]"><span class="text-slate-400 font-medium">Khatib:</span> <span class="text-gov-textMain font-medium">{{ $row['friday_khatib'] }}</span></div>
                                            <div class="text-[11px]"><span class="text-slate-400 font-medium">Muadzin:</span> <span class="text-slate-700 font-medium">{{ $row['friday_muadzin'] }}</span></div>
                                            @if($row['friday_mc'] !== '-')
                                                <div class="text-[11px]"><span class="text-slate-400 font-medium">MC:</span> <span class="text-slate-700 font-medium">{{ $row['friday_mc'] }}</span></div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="space-y-0.5">
                                            <div class="text-[11px]"><span class="text-slate-400 font-medium">Imam:</span> <span class="text-gov-textMain font-medium">{{ $row['dzuhur_imam'] }}</span></div>
                                            <div class="text-[11px]"><span class="text-slate-400 font-medium">Muadzin:</span> <span class="text-slate-700 font-medium">{{ $row['dzuhur_muadzin'] }}</span></div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Ashar -->
                                <div class="p-2.5 rounded-lg bg-slate-50/80 border border-slate-100 space-y-1">
                                    <div class="font-bold text-[11px] uppercase tracking-wide text-gov-navy">
                                        Shalat Ashar
                                    </div>
                                    <div class="space-y-0.5">
                                        <div class="text-[11px]">
                                            <span class="text-slate-400 font-medium">Imam:</span> 
                                            <span class="text-gov-textMain font-medium">{{ $row['ashar_imam'] }}</span>
                                            @if(!empty($row['ashar_kajian_title']))
                                                <span class="text-[10px] text-emerald-700 font-normal ml-0.5">({{ $row['ashar_kajian_title'] }})</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px]"><span class="text-slate-400 font-medium">Muadzin:</span> <span class="text-slate-700 font-medium">{{ $row['ashar_muadzin'] }}</span></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <x-empty-state 
                        icon="calendar" 
                        title="Tidak ada data petugas sholat" 
                        :subtitle="!empty($search) ? ('Tidak ditemukan jadwal penugasan yang cocok dengan nama petugas \'' . $search . '\'.') : 'Belum ada data penugasan yang sesuai untuk bulan yang dipilih.'" 
                    />
                @endforelse
            </div>

            <!-- Table Footer Legend -->
            <div class="p-4 bg-slate-50/70 border-t border-slate-200 text-xs text-slate-500 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-amber-200 border border-amber-300 inline-block shrink-0"></span>
                    <span>Baris kuning paling atas menandakan penugasan hari ini. Sholat Jumat disinkronkan otomatis dengan data Khutbah Jumat.</span>
                </div>
            </div>
        </div>

    </main>

    <!-- Modal Dialog QR Code Jadwal Petugas Sholat -->
    <div x-show="showQrModal" x-cloak
         @keydown.escape.window="showQrModal = false"
         @click="if (window.isBackdropClick ? window.isBackdropClick($event, $el) : $event.target === $el) showQrModal = false"
         class="fixed inset-0 flex items-center justify-center p-3 sm:p-4"
         style="display: none; z-index: 9999; background-color: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
         role="dialog" aria-modal="true" aria-labelledby="qr-modal-title">
        
        <div @click.stop
             class="bg-[#0b2b54] rounded-2xl w-full max-w-sm shadow-2xl border-0 overflow-hidden flex flex-col my-auto relative animate-in fade-in zoom-in-95 duration-200"
             style="z-index: 10000; background-color: #0b2b54; border: none; height: auto;">
            
            <!-- Modal Header - Solid Navy (#0b2b54) -->
            <header class="px-5 py-3.5 bg-gov-navy text-white flex items-center justify-between border-b border-white/10 shrink-0 rounded-t-2xl"
                style="background-color: #0b2b54; color: #ffffff; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                <div class="min-w-0 pr-2">
                    <h3 id="qr-modal-title" class="text-sm font-bold text-white truncate">QR Code Jadwal</h3>
                    <p class="text-[11px] text-slate-200 truncate">{{ $months[$selectedMonth] }} {{ $selectedYear }}</p>
                </div>
                <button type="button" @click="showQrModal = false"
                    class="text-white/80 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition cursor-pointer shrink-0"
                    title="Tutup Modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </header>

            <!-- Modal Body Container (White Solid, Rounded Bottom) -->
            <div class="flex flex-col flex-1 min-h-0 bg-white rounded-b-2xl"
                style="background-color: #ffffff; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                
                <!-- QR Code Body -->
                <div class="p-5 flex flex-col items-center justify-center space-y-3 bg-white">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-col items-center justify-center w-full">
                        <div id="portalQrcodeContainer" class="w-48 h-48 flex items-center justify-center bg-white p-2 rounded-lg border border-slate-200 shadow-2xs"></div>
                        <p class="text-[11px] text-slate-500 mt-2.5 text-center font-medium leading-relaxed">Scan menggunakan kamera HP untuk langsung membuka atau mencetak jadwal resmi ini</p>
                    </div>
                </div>

                <!-- Sticky Footer Actions -->
                <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-2.5 shrink-0 rounded-b-2xl"
                    style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                    <a href="{{ route('portal.petugas-sholat.print', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank"
                       class="flex-1 py-2 px-3 rounded-lg bg-gov-navy text-white text-xs font-bold transition inline-flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                       style="background-color: #0b2b54; color: #ffffff; border: 1px solid #0b2b54;">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        <span>Buka Jadwal</span>
                    </a>
                    <button type="button" @click="showQrModal = false"
                            class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs border border-slate-300 transition cursor-pointer">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        function petugasSholatComponent() {
            return {
                showQrModal: false,
                isDownloadingPdf: false,
                renderQr() {
                    const container = document.getElementById('portalQrcodeContainer');
                    if (!container) return;
                    container.innerHTML = '';
                    const url = '{{ route('portal.petugas-sholat.print', ['month' => $selectedMonth, 'year' => $selectedYear]) }}';
                    if (typeof QRCode !== 'undefined') {
                        new QRCode(container, {
                            text: url,
                            width: 176,
                            height: 176,
                            colorDark: '#06172e',
                            colorLight: '#ffffff',
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    }
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                },
                openQr() {
                    this.showQrModal = true;
                    this.$nextTick(() => {
                        if (typeof QRCode === 'undefined') {
                            const script = document.createElement('script');
                            script.src = '{{ asset('vendor/qrcode.min.js') }}';
                            script.onload = () => this.renderQr();
                            script.onerror = () => {
                                const cdnScript = document.createElement('script');
                                cdnScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
                                cdnScript.onload = () => this.renderQr();
                                document.head.appendChild(cdnScript);
                            };
                            document.head.appendChild(script);
                        } else {
                            this.renderQr();
                        }
                    });
                },
                async downloadMonthlyDirectPdf() {
                    if (this.isDownloadingPdf) return;
                    this.isDownloadingPdf = true;

                    const month = (this.$wire && this.$wire.selectedMonth) ? this.$wire.selectedMonth : '{{ $selectedMonth }}';
                    const year = (this.$wire && this.$wire.selectedYear) ? this.$wire.selectedYear : '{{ $selectedYear }}';
                    const downloadUrl = `{{ url('/petugas-sholat/unduh-pdf') }}?month=${month}&year=${year}`;
                    const filename = `Jadwal_Petugas_Shalat_Bulan_${month}_${year}.pdf`;

                    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

                    // 1. Pada perangkat seluler / iOS PWA: gunakan Web Share API untuk lembar berbagi native jika didukung
                    if (isMobile && navigator.canShare) {
                        try {
                            const res = await fetch(downloadUrl);
                            if (res.ok) {
                                const blob = await res.blob();
                                const file = new File([blob], filename, { type: 'application/pdf' });
                                if (navigator.canShare({ files: [file] })) {
                                    await navigator.share({
                                        files: [file],
                                        title: filename.replace('.pdf', '')
                                    });
                                    this.isDownloadingPdf = false;
                                    return;
                                }
                            }
                        } catch (e) {
                            if (e.name === 'AbortError') {
                                this.isDownloadingPdf = false;
                                return;
                            }
                            console.warn('Share API gagal, beralih ke direct download:', e);
                        }
                    }

                    // 2. Pada Desktop, Android, dan browser biasa:
                    // Memicu unduhan via tag <a> tersembunyi
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = filename;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();

                    setTimeout(() => {
                        if (link.parentNode) {
                            link.parentNode.removeChild(link);
                        }
                        this.isDownloadingPdf = false;
                    }, 1500);
                }
            };
        }
        window.petugasSholatComponent = petugasSholatComponent;
    </script>
</div>
