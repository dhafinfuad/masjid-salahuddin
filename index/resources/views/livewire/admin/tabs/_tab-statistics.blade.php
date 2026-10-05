<!-- ======================================================== -->
<!-- TAB: STATISTIK PENGUNJUNG (0ms Latency Impact, High-Performance) -->
<!-- ======================================================== -->
<div class="space-y-6">

    <!-- Top Banner with Pattern & Filters -->
    <div class="bg-gov-navy rounded-xl p-4 sm:p-6 text-white relative overflow-hidden shadow-xs bg-gov-pattern border border-gov-border/30">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 relative z-10">
            <div class="space-y-1.5 max-w-xl">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span class="text-xs text-amber-300 font-semibold uppercase tracking-wider">Pemantauan Real-Time</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight">Statistik & Analisis Pengunjung</h2>
                <p class="text-xs sm:text-sm text-slate-200 font-normal leading-relaxed">
                    Data kunjungan jamaah ke portal <span class="font-semibold text-amber-300">masjidsalahuddin.my.id</span> yang dicatat secara asinkron tanpa membebani kecepatan website.
                </p>
            </div>

            <!-- Period Filter Segmented Control (Responsive: Full-width 4-column Grid on Mobile, Inline Flex on Desktop) -->
            <div class="grid grid-cols-4 sm:flex sm:items-center gap-1 bg-[#06172E]/90 backdrop-blur-sm p-1 rounded-xl border border-white/10 w-full lg:w-auto shrink-0 text-xs shadow-inner">
                <button type="button" wire:click="setStatsPeriod('today')"
                    class="py-2 px-2 sm:px-3.5 rounded-lg font-semibold text-center transition cursor-pointer whitespace-nowrap text-[11px] sm:text-xs {{ $statsPeriod === 'today' ? 'bg-amber-400 text-gov-navy shadow-xs font-bold' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                    Hari Ini
                </button>
                <button type="button" wire:click="setStatsPeriod('7_days')"
                    class="py-2 px-2 sm:px-3.5 rounded-lg font-semibold text-center transition cursor-pointer whitespace-nowrap text-[11px] sm:text-xs {{ $statsPeriod === '7_days' ? 'bg-amber-400 text-gov-navy shadow-xs font-bold' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                    7 Hari
                </button>
                <button type="button" wire:click="setStatsPeriod('30_days')"
                    class="py-2 px-2 sm:px-3.5 rounded-lg font-semibold text-center transition cursor-pointer whitespace-nowrap text-[11px] sm:text-xs {{ $statsPeriod === '30_days' ? 'bg-amber-400 text-gov-navy shadow-xs font-bold' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                    30 Hari
                </button>
                <button type="button" wire:click="setStatsPeriod('all')"
                    class="py-2 px-2 sm:px-3.5 rounded-lg font-semibold text-center transition cursor-pointer whitespace-nowrap text-[11px] sm:text-xs {{ $statsPeriod === 'all' ? 'bg-amber-400 text-gov-navy shadow-xs font-bold' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                    Semua
                </button>
            </div>
        </div>
    </div>

    <!-- 4 Key Performance Indicator (KPI) Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Total Kunjungan -->
        <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-gov-textMuted font-medium block">Total Kunjungan (Hits)</span>
                <span class="text-2xl font-bold text-gov-textMain tracking-tight">
                    {{ number_format($statsTotalVisits, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 block">Seluruh tampilan halaman</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                <i data-lucide="eye" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- KPI 2: Pengunjung Unik -->
        <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-gov-textMuted font-medium block">Pengunjung Unik (IP)</span>
                <span class="text-2xl font-bold text-gov-textMain tracking-tight">
                    {{ number_format($statsUniqueVisitors, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 block">Perangkat / jamaah berbeda</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- KPI 3: Kunjungan Hari Ini -->
        <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-gov-textMuted font-medium block">Kunjungan Hari Ini</span>
                <span class="text-2xl font-bold text-gov-textMain tracking-tight">
                    {{ number_format($statsTodayVisits, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Real-time hari ini
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                <i data-lucide="calendar-check" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- KPI 4: Dominasi Perangkat (Dinamis: Perangkat terbesar di atas dengan font bold besar dan icon yang sesuai) -->
        @php
            $isDesktopDominant = ($statsDesktopPercent >= $statsMobilePercent);
            $dominantPercent = $isDesktopDominant ? $statsDesktopPercent : $statsMobilePercent;
            $dominantLabel = $isDesktopDominant ? 'Desktop' : 'HP';
            $secondaryPercent = $isDesktopDominant ? $statsMobilePercent : $statsDesktopPercent;
            $secondaryLabel = $isDesktopDominant ? 'HP / Mobile' : 'Desktop / Laptop';
            $dominantIcon = $isDesktopDominant ? 'monitor' : 'smartphone';
            $iconBg = $isDesktopDominant ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-purple-50 text-purple-600 border-purple-200';
        @endphp
        <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-gov-textMuted font-medium block">Dominasi Perangkat</span>
                <span class="text-2xl font-bold text-gov-textMain tracking-tight">
                    {{ $dominantPercent }}% <span class="text-xs font-semibold text-slate-500">{{ $dominantLabel }}</span>
                </span>
                <span class="text-[11px] text-slate-400 block">{{ $secondaryPercent }}% {{ $secondaryLabel }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl {{ $iconBg }} border flex items-center justify-center shrink-0">
                <i data-lucide="{{ $dominantIcon }}" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Charts & Popular Pages Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left 8 cols: Daily Trend Chart -->
        <div class="lg:col-span-8 bg-white p-5 rounded-xl border border-gov-border shadow-2xs space-y-4 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Tren Kunjungan Harian</h3>
                    <p class="text-xs text-gov-textMuted mt-0.5">Grafik aktivitas pengunjung per hari selama {{ count($statsDailyChart) }} hari terakhir.</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium text-slate-500 self-start sm:self-auto">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-sm bg-gov-navy"></span> Total Kunjungan
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-sm bg-amber-400"></span> Pengunjung Unik
                    </span>
                </div>
            </div>

            <!-- Pure CSS Bar Chart (Headroom & Overflow-Safe Tooltip) -->
            <div class="pt-16 overflow-x-auto pb-2 -mx-2 px-3 scrollbar-none sm:scrollbar-thin">
                @if(count($statsDailyChart) > 0)
                    <div class="h-56 flex items-end gap-2 sm:gap-4 px-2 pb-2 border-b border-slate-100 min-w-[560px] sm:min-w-0">
                        @foreach($statsDailyChart as $bar)
                            @php
                                $isToday = ($bar['date'] === \Carbon\Carbon::today()->format('Y-m-d'));
                                $tooltipAlign = $loop->first ? 'left-0' : ($loop->last ? 'right-0' : 'left-1/2 -translate-x-1/2');
                                $caretAlign = $loop->first ? 'left-4' : ($loop->last ? 'right-4' : 'left-1/2 -translate-x-1/2');
                            @endphp
                            <div class="flex-1 flex flex-col items-center h-full justify-end group relative cursor-pointer min-w-[28px]">
                                <!-- Tooltip on Hover -->
                                <div class="opacity-0 group-hover:opacity-100 pointer-events-none transition-all duration-150 absolute -top-13 {{ $tooltipAlign }} bg-gov-navy text-white text-[11px] py-1.5 px-3 rounded-lg shadow-xl whitespace-nowrap z-30 border border-slate-700/80">
                                    <div class="font-bold text-amber-300 text-center">{{ $bar['date_label'] }}</div>
                                    <div class="text-center font-medium">{{ $bar['count'] }} hits • {{ $bar['unique'] }} unik</div>
                                    <div class="absolute -bottom-1 {{ $caretAlign }} w-2 h-2 bg-gov-navy border-r border-b border-slate-700/80 rotate-45"></div>
                                </div>

                                <!-- Bar Column -->
                                <div class="w-full max-w-[38px] flex items-end justify-center h-full">
                                    <div class="w-full rounded-t-md transition-all duration-300 flex flex-col justify-end {{ $isToday ? 'bg-amber-400 group-hover:bg-amber-500' : 'bg-gov-navy group-hover:bg-[#1C477A]' }}"
                                        style="height: {{ $bar['percent'] }}%;">
                                        @if($bar['count'] > 0)
                                            <span class="text-[10px] font-bold text-center block pb-1 text-white leading-none {{ $bar['percent'] < 20 ? 'hidden sm:block' : '' }}">
                                                {{ $bar['count'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Date Label -->
                                <div class="mt-2 text-center">
                                    <span class="text-[11px] font-semibold block leading-tight {{ $isToday ? 'text-amber-600 font-bold' : 'text-slate-600' }}">
                                        {{ $bar['day_name'] }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block leading-tight">
                                        {{ $bar['date_label'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-48 flex items-center justify-center text-xs text-slate-400">
                        Belum ada data grafik untuk periode ini.
                    </div>
                @endif
            </div>
        </div>

        <!-- Right 4 cols: Top Visited Pages -->
        <div class="lg:col-span-4 bg-white p-5 rounded-xl border border-gov-border shadow-2xs space-y-4">
            <div class="border-b border-slate-200 pb-3">
                <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Halaman Terpopuler</h3>
                <p class="text-xs text-gov-textMuted mt-0.5">Halaman yang paling sering diakses jamaah.</p>
            </div>

            <div class="space-y-3">
                @php
                    $maxPageHits = $statsTopPages->max('total') ?: 1;
                @endphp
                @forelse($statsTopPages as $page)
                    @php
                        $percentage = round(($page->total / $maxPageHits) * 100);
                        $parsedUrl = parse_url($page->url, PHP_URL_PATH) ?? $page->url;
                        $cleanPath = '/' . trim($parsedUrl, '/');
                        $pageLabel = match ($cleanPath) {
                            '/', '' => 'Beranda Utama',
                            '/jadwal-sholat' => 'Jadwal Shalat',
                            '/kegiatan', '/kegiatan-masjid' => 'Kegiatan & Kajian',
                            '/petugas-sholat' => 'Petugas Shalat',
                            '/profil', '/profil-masjid' => 'Profil Masjid',
                            '/kas', '/laporan-kas' => 'Laporan Kas',
                            default => ucwords(trim(str_replace(['-', '_', '/'], ' ', $cleanPath))) ?: 'Beranda Utama',
                        };
                    @endphp
                    <div class="space-y-1 text-xs">
                        <div class="flex items-center justify-between font-medium">
                            <span class="text-gov-textMain font-semibold truncate max-w-[180px]" title="{{ $page->url }}">
                                {{ $pageLabel }}
                            </span>
                            <span class="text-slate-500 font-bold shrink-0">
                                {{ number_format($page->total, 0, ',', '.') }} hits
                            </span>
                        </div>
                        <!-- Progress bar -->
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gov-navy h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%;"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-6 text-center">Belum ada data halaman yang tercatat.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Bottom Card: Real-time Visitor Log Table -->
    <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-gov-textMain">
                    Log Aktivitas Kunjungan
                </h3>
                <p class="text-xs text-gov-textMuted mt-0.5">Daftar riwayat kunjungan terbaru ke portal publik.</p>
            </div>

            <!-- Search Input with Debounce -->
            <div class="relative w-full sm:w-64">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" wire:model.live.debounce.300ms="statsSearch"
                    placeholder="Cari IP atau Halaman..."
                    class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-gov-border rounded-lg focus:outline-none focus:ring-1 focus:ring-gov-navy focus:bg-white transition">
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gov-textMain">
                <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-300">
                    <tr>
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3">Alamat IP</th>
                        <th class="py-2.5 px-3">Halaman yang Dibuka</th>
                        <th class="py-2.5 px-3">Perangkat</th>
                        <th class="py-2.5 px-3">Platform & Browser</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300 font-normal">
                    @forelse($statsRecentLogs as $log)
                        @php
                            $vDate = $log->visited_at ? \Carbon\Carbon::parse($log->visited_at)->timezone('Asia/Jakarta') : null;
                            $devType = strtolower($log->device_type ?? 'desktop');
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Waktu -->
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="font-semibold text-gov-textMain block">
                                    {{ $vDate ? $vDate->format('d M Y, H:i') : '-' }} WIB
                                </span>
                                <span class="text-[10px] text-slate-400 block">
                                    {{ $vDate ? $vDate->diffForHumans() : '-' }}
                                </span>
                            </td>

                            <!-- IP Address -->
                            <td class="py-2.5 px-3 whitespace-nowrap font-mono text-slate-700">
                                <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-[11px]">
                                    {{ $log->ip_address ?: '127.0.0.1' }}
                                </span>
                            </td>

                            <!-- Halaman yang Dibuka -->
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <span class="font-semibold text-gov-navy block">
                                    {{ $log->page_title }}
                                </span>
                            </td>

                            <!-- Perangkat Badge (Complies with UI_GUIDELINES.md: h-[20px] text-[11px] rounded-full) -->
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                @if($devType === 'mobile')
                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border bg-emerald-50 text-emerald-700 border-emerald-200 leading-none gap-1">
                                        <i data-lucide="smartphone" class="w-3 h-3"></i>
                                        <span>Mobile</span>
                                    </span>
                                @elseif($devType === 'tablet')
                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border bg-purple-50 text-purple-700 border-purple-200 leading-none gap-1">
                                        <i data-lucide="tablet" class="w-3 h-3"></i>
                                        <span>Tablet</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border bg-blue-50 text-blue-700 border-blue-200 leading-none gap-1">
                                        <i data-lucide="monitor" class="w-3 h-3"></i>
                                        <span>Desktop</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Platform & Browser -->
                            <td class="py-2.5 px-3 whitespace-nowrap text-slate-600">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-medium text-gov-textMain">{{ $log->browser ?? 'Browser' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-500 text-[11px]">{{ $log->platform ?? 'OS' }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i data-lucide="activity" class="w-8 h-8 text-slate-300"></i>
                                    <span>Belum ada log aktivitas kunjungan yang tercatat.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($statsRecentLogs->hasPages())
            <div class="pt-2 border-t border-slate-100">
                {{ $statsRecentLogs->links() }}
            </div>
        @endif
    </div>

</div>
