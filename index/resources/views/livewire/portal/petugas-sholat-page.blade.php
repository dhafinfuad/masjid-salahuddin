<div x-data="{ 
        showQrModal: false,
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
        isDownloadingPdf: false,
        async downloadMonthlyDirectPdf() {
            if (this.isDownloadingPdf) return;
            this.isDownloadingPdf = true;

            const month = (this.$wire && this.$wire.selectedMonth) ? this.$wire.selectedMonth : '{{ $selectedMonth }}';
            const year = (this.$wire && this.$wire.selectedYear) ? this.$wire.selectedYear : '{{ $selectedYear }}';
            const printUrl = `{{ url('/petugas-sholat/cetak') }}?month=${month}&year=${year}`;
            const filename = `Jadwal_Petugas_Shalat_Bulan_${month}_${year}.pdf`;

            try {
                // Buat iframe tersembunyi berukuran 1024px agar hasil layout cetak konsisten dan rapi
                const iframe = document.createElement('iframe');
                iframe.style.position = 'fixed';
                iframe.style.top = '-9999px';
                iframe.style.left = '-9999px';
                iframe.style.width = '1024px';
                iframe.style.height = '1400px';
                iframe.style.opacity = '0';
                iframe.style.pointerEvents = 'none';
                iframe.style.border = 'none';
                iframe.src = printUrl;

                const loadPromise = new Promise((resolve, reject) => {
                    const timeoutId = setTimeout(() => {
                        reject(new Error('Waktu tunggu pemuatan dokumen melebihi batas'));
                    }, 15000);

                    iframe.onload = () => {
                        clearTimeout(timeoutId);
                        resolve();
                    };
                    iframe.onerror = () => {
                        clearTimeout(timeoutId);
                        reject(new Error('Gagal memuat dokumen cetak'));
                    };
                });

                document.body.appendChild(iframe);
                await loadPromise;

                // Beri jeda 400ms agar styling Tailwind dan webfont di dalam iframe selesai dirender
                await new Promise(r => setTimeout(r, 400));

                const iframeWin = iframe.contentWindow;
                const iframeDoc = iframe.contentDocument || iframeWin.document;
                const contentContainer = iframeDoc.getElementById('contentContainer');

                if (!contentContainer) {
                    throw new Error('Konten dokumen tidak ditemukan');
                }

                const pdfWorker = iframeWin.html2pdf || window.html2pdf;
                if (!pdfWorker) {
                    throw new Error('Modul pembuat PDF belum siap');
                }

                const opt = {
                    margin: [8, 8, 8, 8],
                    filename: filename,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        letterRendering: true,
                        logging: false,
                        windowWidth: 1024
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait'
                    },
                    pagebreak: {
                        mode: ['avoid-all', 'css', 'legacy'],
                        avoid: 'tr'
                    }
                };

                const pdfBlob = await pdfWorker().set(opt).from(contentContainer).output('blob');

                if (iframe.parentNode) {
                    iframe.parentNode.removeChild(iframe);
                }

                const pdfFile = new File([pdfBlob], filename, { type: 'application/pdf' });

                // 1. Pada perangkat mobile / iOS PWA: gunakan Web Share API untuk lembar berbagi native
                const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
                if (isMobile && navigator.canShare && navigator.canShare({ files: [pdfFile] })) {
                    try {
                        await navigator.share({
                            files: [pdfFile],
                            title: filename.replace('.pdf', '')
                        });
                        return;
                    } catch (shareErr) {
                        if (shareErr.name === 'AbortError') {
                            return;
                        }
                        console.warn('Share API gagal, beralih ke unduh blob:', shareErr);
                    }
                }

                // 2. Pada Desktop & Android: unduh file PDF langsung tanpa membuka tab baru
                const blobUrl = URL.createObjectURL(pdfBlob);
                const downloadLink = document.createElement('a');
                downloadLink.style.display = 'none';
                downloadLink.href = blobUrl;
                downloadLink.download = filename;
                document.body.appendChild(downloadLink);
                downloadLink.click();

                setTimeout(() => {
                    if (downloadLink.parentNode) {
                        downloadLink.parentNode.removeChild(downloadLink);
                    }
                    URL.revokeObjectURL(blobUrl);
                }, 3000);

            } catch (err) {
                console.error('Direct PDF download error:', err);
                // Fallback aman jika rendering di background gagal: buka halaman cetak di tab baru
                window.open(printUrl, '_blank');
            } finally {
                this.isDownloadingPdf = false;
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                });
            }
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
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 max-w-xl">
                
                <!-- Group Bulan & Tahun: 1 Baris 50% - 50% di Mobile -->
                <div class="grid grid-cols-2 gap-3 w-full sm:flex sm:w-auto sm:items-center">
                    <!-- Filter Bulan -->
                    <div class="w-full sm:w-48 shrink-0">
                        <x-custom-select 
                            id="filter-duty-month"
                            wire:model.live="selectedMonth"
                            :options="$months"
                            placeholder="Pilih Bulan"
                            icon="calendar"
                        />
                    </div>

                    <!-- Filter Tahun -->
                    <div class="w-full sm:w-36 shrink-0">
                        <x-custom-select 
                            id="filter-duty-year"
                            wire:model.live="selectedYear"
                            :options="$years"
                            placeholder="Pilih Tahun"
                            icon="calendar-days"
                        />
                    </div>
                </div>

                <!-- Tombol Unduh PDF Langsung (Uji Coba Direct Download) -->
                <div class="w-full sm:w-auto">
                    <button type="button" 
                            @click="downloadMonthlyDirectPdf()" 
                            :disabled="isDownloadingPdf"
                            class="h-[38px] w-full sm:w-auto px-4 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer whitespace-nowrap disabled:opacity-75 disabled:cursor-wait"
                            title="Unduh Jadwal Petugas Shalat Bulanan (PDF)">
                        <template x-if="!isDownloadingPdf">
                            <span class="inline-flex items-center gap-1.5">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                <span>Unduh PDF</span>
                            </span>
                        </template>
                        <template x-if="isDownloadingPdf">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Menyiapkan PDF...</span>
                            </span>
                        </template>
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
                                subtitle="Belum ada data penugasan yang sesuai untuk bulan yang dipilih." 
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
                        subtitle="Belum ada data penugasan yang sesuai untuk bulan yang dipilih." 
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

    <!-- Library html2pdf vendor preloaded -->
    <script src="{{ asset('vendor/html2pdf.bundle.min.js') }}"></script>
</div>
