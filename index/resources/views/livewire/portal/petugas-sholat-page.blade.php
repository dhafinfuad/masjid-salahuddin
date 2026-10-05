<div x-data="{ 
        showQrModal: false,
        openQr() {
            this.showQrModal = true;
            this.$nextTick(() => {
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
            });
        }
    }" 
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
            <div class="flex flex-col sm:flex-row sm:items-end gap-3 max-w-xl">
                
                <!-- Filter Bulan -->
                <div class="w-full sm:w-48">
                    <label for="filter-duty-month" class="block text-xs font-bold uppercase text-slate-500 mb-1.5 text-left">
                        Bulan
                    </label>
                    <div class="relative">
                        <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center" wire:ignore>
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600 shrink-0 inline-block"></i>
                        </div>
                        <select id="filter-duty-month" wire:model.live="selectedMonth"
                            class="w-full pl-8 pr-8 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filter Tahun -->
                <div class="w-full sm:w-36">
                    <label for="filter-duty-year" class="block text-xs font-bold uppercase text-slate-500 mb-1.5 text-left">
                        Tahun
                    </label>
                    <div class="relative">
                        <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center" wire:ignore>
                            <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-indigo-600 shrink-0 inline-block"></i>
                        </div>
                        <select id="filter-duty-year" wire:model.live="selectedYear"
                            class="w-full pl-8 pr-8 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs">
                            @foreach($years as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tombol Cetak PDF Resmi & QR Code -->
                <div class="w-full sm:w-auto flex items-center gap-2">
                    <a href="{{ route('portal.petugas-sholat.print', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank"
                       class="w-full sm:w-auto px-4 py-1.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap"
                       title="Cetak Jadwal Petugas Shalat Bulanan (PDF)">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span>Cetak PDF</span>
                    </a>

                    <button type="button" @click="openQr()"
                       class="w-full sm:w-auto px-3.5 py-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap"
                       title="Lihat Kode QR Jadwal Petugas Shalat">
                        <i data-lucide="qr-code" class="w-3.5 h-3.5 text-gov-navy"></i>
                        <span>QR Code</span>
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
                                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-slate-100 text-slate-500 border-slate-200">
                                            Libur
                                        </span>
                                    @elseif($isFriday)
                                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-emerald-100 text-emerald-800 border-emerald-300">
                                            Pekan {{ $row['week_number'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">
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
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">
                                    Tidak ada data penugasan untuk bulan yang dipilih.
                                </td>
                            </tr>
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
                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-slate-100 text-slate-500 border-slate-200">
                                        Libur
                                    </span>
                                @elseif($isFriday)
                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-emerald-100 text-emerald-800 border-emerald-300">
                                        Pekan {{ $row['week_number'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">
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
                    <div class="p-8 text-center text-slate-400">
                        Tidak ada data penugasan untuk bulan yang dipilih.
                    </div>
                @endforelse
            </div>

            <!-- Table Footer Legend -->
            <div class="p-4 bg-slate-50/70 border-t border-slate-200 text-xs text-slate-500 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-amber-200 border border-amber-300 inline-block shrink-0"></span>
                    <span>Baris kuning paling atas menandakan penugasan hari ini. Sholat Jumat disinkronkan otomatis dengan data Khutbah Jumat.</span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="openQr()"
                       class="text-slate-600 hover:text-gov-navy font-semibold hover:underline inline-flex items-center gap-1 cursor-pointer">
                        <i data-lucide="qr-code" class="w-3 h-3"></i>
                        <span>Scan QR</span>
                    </button>
                    <span class="text-slate-300">•</span>
                    <a href="{{ route('portal.petugas-sholat.print', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank"
                       class="text-gov-navy font-bold hover:underline inline-flex items-center gap-1">
                        <i data-lucide="printer" class="w-3 h-3"></i>
                        <span>Cetak Dokumen Lengkap</span>
                    </a>
                </div>
            </div>
        </div>

    </main>

    <!-- Modal Dialog QR Code Jadwal Petugas Sholat -->
    <div x-show="showQrModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        
        <div @click.away="showQrModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-sm w-full p-6 text-center space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gov-50 text-gov-navy flex items-center justify-center font-bold">
                        <i data-lucide="qr-code" class="w-4 h-4"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="text-sm font-bold text-gov-textMain leading-tight">QR Code Jadwal</h3>
                        <p class="text-[11px] text-slate-500">{{ $months[$selectedMonth] }} {{ $selectedYear }}</p>
                    </div>
                </div>
                <button type="button" @click="showQrModal = false" class="p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- QR Code Container -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col items-center justify-center">
                <div id="portalQrcodeContainer" class="w-48 h-48 flex items-center justify-center bg-white p-2 rounded-lg border border-slate-200 shadow-2xs"></div>
                <p class="text-[11px] text-slate-500 mt-2 font-medium">Scan menggunakan kamera HP untuk langsung membuka atau mencetak jadwal resmi ini</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 pt-1">
                <a href="{{ route('portal.petugas-sholat.print', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" target="_blank"
                   class="flex-1 py-2 px-3 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold transition inline-flex items-center justify-center gap-1.5 shadow-2xs">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span>Buka Jadwal</span>
                </a>
                <button type="button" @click="showQrModal = false"
                        class="px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Script Library QR Code -->
    <script src="{{ asset('vendor/qrcode.min.js') }}"></script>
    <script>
        if (typeof QRCode === 'undefined') {
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
        }
    </script>
</div>
