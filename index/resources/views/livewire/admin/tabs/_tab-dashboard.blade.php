            <!-- ======================================================== -->
            <!-- SUBTAB 1: DASHBOARD -->
            <!-- ======================================================== -->
            <div class="space-y-6">
                <!-- Welcome Hero Banner (Navy with Pattern & Warm Gold Accents) -->
                <div
                    class="bg-gov-navy rounded-xl p-6 text-white relative overflow-hidden shadow-xs bg-gov-pattern border border-gov-border/30">
                    <div
                        class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 relative z-10">
                        <div class="space-y-1.5 max-w-xl">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span class="text-sm text-amber-300 font-medium">Ahlan wa Sahlan</span>
                            </div>
                            <h2 class="text-2xl py-1 font-bold text-white tracking-tight capitalize">
                                {{ preg_replace('/\s*\(.*?\)/', '', Auth::user()->name ?? 'Pengurus') }}
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-200 font-normal leading-relaxed">Menebar Manfaat, Meraih Berkah</p>
                        </div>
                    </div>
                </div>

                <!-- Two Columns Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Left 8 cols: Jadwal Kajian Terdekat & Agenda Akbar -->
                    <div class="lg:col-span-8 space-y-6">
                        <!-- Jadwal Kajian & Khutbah Terdekat -->
                        <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-200 pb-3">
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Jadwal Kajian & Khutbah Terdekat</h3>
                                    <p class="text-xs text-gov-textMuted mt-0.5">Jadwal khutbah Jumat dan kajian rutin pekanan terdekat di {{ $settings->name }}.</p>
                                </div>
                                <button type="button" @click="switchTabFast('kajian')"
                                    class="text-xs font-semibold text-gov-navy hover:text-amber-600 inline-flex items-center gap-1 transition cursor-pointer shrink-0 self-start sm:self-auto">
                                    <span>Kelola semua</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>

                            <div class="space-y-2.5">
                                @forelse($upcomingKajians as $kj)
                                    @php
                                        $kjDate = \Carbon\Carbon::parse($kj->date);
                                        $isJumat = $kj->type === 'jumat';
                                        $speaker = $isJumat ? ($kj->khatib_name ?: 'Khatib Shalat Jumat') : ($kj->speaker_name ?: 'Pemateri Kajian');
                                    @endphp
                                    <div
                                        class="p-3 sm:p-3.5 rounded-xl bg-slate-50/70 border border-gov-border hover:bg-slate-100/80 transition flex items-start gap-3 shadow-2xs">
                                        <!-- Date Box -->
                                        <div
                                            class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg bg-white border border-gov-border text-gov-navy flex flex-col items-center justify-center font-mono shrink-0 shadow-2xs">
                                            <span
                                                class="text-xs sm:text-sm font-bold leading-none">{{ $kjDate->format('d') }}</span>
                                            <span
                                                class="text-[10px] uppercase font-bold text-amber-600 mt-0.5">{{ $kjDate->translatedFormat('M') }}</span>
                                        </div>

                                        <!-- Details & Actions -->
                                        <div class="min-w-0 flex-1">
                                            <!-- Top Row: Badge & Action -->
                                            <div class="flex items-center justify-between gap-2">
                                                <span
                                                    class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none shrink-0 whitespace-nowrap {{ $isJumat ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-emerald-50 text-emerald-700 border-emerald-300' }}">
                                                    {{ $isJumat ? 'Shalat Jumat' : 'Kajian Pekanan' }}
                                                </span>

                                                @if($isJumat)
                                                    <a href="{{ route('admin.kajian.teks-mc', $kj->id) }}" target="_blank"
                                                        class="inline-flex items-center justify-center p-1.5 rounded-lg bg-gov-50 hover:bg-gov-100 text-gov-navy border border-gov-border shadow-2xs transition cursor-pointer shrink-0 ml-auto"
                                                        title="Buka & Cetak Teks MC Shalat Jumat">
                                                        <i data-lucide="printer" class="w-3.5 h-3.5 text-gov-navy"></i>
                                                    </a>
                                                @endif
                                            </div>

                                            <!-- Title -->
                                            <h4 class="text-xs sm:text-sm font-bold text-gov-textMain truncate leading-tight mt-1.5">{{ $kj->title }}</h4>

                                            <!-- Speaker -->
                                            <p class="text-xs text-slate-700 font-medium truncate mt-0.5">{{ $speaker }}</p>

                                            <!-- Time -->
                                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1.5">
                                                <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                                <span class="truncate">{{ $kj->time_display ?: ($isJumat ? 'Waktu Shalat Jumat' : '09:00 WIB') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada jadwal kajian terdekat.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Agenda & Perencanaan Masjid -->
                        <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-200 pb-3">
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Agenda Akbar & Perencanaan</h3>
                                    <p class="text-xs text-gov-textMuted mt-0.5">Rencana kegiatan besar, program hari besar Islam, dan monitoring LPJ.</p>
                                </div>
                                <button type="button" @click="switchTabFast('agenda')"
                                    class="text-xs font-semibold text-gov-navy hover:text-amber-600 inline-flex items-center gap-1 transition cursor-pointer shrink-0 self-start sm:self-auto">
                                    <span>Kelola agenda</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @forelse($allAgendas as $ag)
                                    @php
                                        $agDate = \Carbon\Carbon::parse($ag->event_date);
                                        $agStatus = $ag->status ?: 'Direncanakan';
                                        $isDone = strcasecmp($agStatus, 'SELESAI') === 0;
                                        $isRunning = strcasecmp($agStatus, 'Berjalan') === 0;
                                        $statusBadgeClass = $isDone 
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                                            : ($isRunning ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-sky-50 text-sky-700 border-sky-200');
                                        $statusLabel = ucfirst(strtolower($agStatus));
                                    @endphp
                                    <div class="p-3.5 rounded-xl bg-slate-50/70 border border-gov-border hover:bg-slate-100/80 transition flex flex-col justify-between space-y-2.5 shadow-2xs">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-xs font-bold text-gov-textMain truncate leading-tight">{{ $ag->title }}</h4>
                                                <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                                                    <i data-lucide="calendar" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                                    <span>{{ $agDate->translatedFormat('d F Y') }}</span>
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none shrink-0 whitespace-nowrap {{ $statusBadgeClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between pt-2 border-t border-slate-200/80 text-xs">
                                            <span class="text-slate-500 text-[11px]">Anggaran:</span>
                                            <span class="font-bold text-gov-navy text-[11px] tnum">Rp {{ number_format($ag->budget, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 py-4 text-center col-span-2">Belum ada agenda perencanaan aktif.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Right 4 cols: Transaksi Kas Terkini & Petugas Rawatib -->
                    <div class="lg:col-span-4 space-y-6">
                        <!-- Transaksi Kas Terkini -->
                        <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-200 pb-3">
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Kas Terkini</h3>
                                    <p class="text-xs text-gov-textMuted mt-0.5">Mutasi kas masuk & keluar</p>
                                </div>
                                <button type="button" @click="switchTabFast('finance')"
                                    class="text-xs font-semibold text-gov-navy hover:text-amber-600 inline-flex items-center gap-1 transition cursor-pointer shrink-0 self-start sm:self-auto">
                                    <span>Buku Kas</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>

                            <div class="space-y-2 text-xs">
                                @forelse($recentFinances as $fin)
                                    @php
                                        $isMasuk = $fin->type === 'pemasukan';
                                        $fDate = \Carbon\Carbon::parse($fin->transaction_date);
                                    @endphp
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-gov-border flex items-center justify-between">
                                        <div class="space-y-0.5 overflow-hidden pr-2">
                                            <div class="font-bold text-gov-textMain truncate text-xs">{{ $fin->description ?: ($fin->category->name ?? 'Kas') }}</div>
                                            <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                                <span>{{ $fDate->format('d M Y') }}</span>
                                                <span>•</span>
                                                <span class="truncate max-w-25">{{ $fin->category->name ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center font-bold text-xs tnum shrink-0 {{ $isMasuk ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $isMasuk ? '+' : '-' }}Rp {{ number_format($fin->amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada transaksi kas.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Petugas Shalat Rawatib Pekan Ini -->
                        <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-200 pb-3">
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Petugas Shalat</h3>
                                    <p class="text-xs text-gov-textMuted mt-0.5">Pekan ke-{{ $currentWeekNumber }}</p>
                                </div>
                                <button type="button" @click="switchTabFast('petugas')"
                                    class="text-xs font-semibold text-gov-navy hover:text-amber-600 inline-flex items-center gap-1 transition cursor-pointer shrink-0 self-start sm:self-auto">
                                    <span>Lihat Semua</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>

                            <div class="space-y-2 text-xs">
                                @forelse($currentWeekDuties->take(5) as $duty)
                                    @php
                                        $isFridayDzuhurWidget = ($duty->day_name === 'Jumat' && $duty->prayer_time === 'dzuhur');
                                        $fridayKajianWidget = ($isFridayDzuhurWidget && isset($fridayKajiansByWeek[$currentWeekNumber]))
                                            ? $fridayKajiansByWeek[$currentWeekNumber]
                                            : ($isFridayDzuhurWidget ? ($fridayKajians->first() ?? null) : null);

                                        $isUstazKajianWidget = (stripos($duty->imam_name, 'kajian') !== false);
                                        $pekananKeyWidget = "{$duty->day_name}_{$currentWeekNumber}";
                                        $pekananKajianWidget = null;
                                        if ($isUstazKajianWidget) {
                                            if (isset($pekananKajiansByDayWeek[$pekananKeyWidget])) {
                                                $pekananKajianWidget = $pekananKajiansByDayWeek[$pekananKeyWidget];
                                            } elseif (isset($pekananKajiansByDay[$duty->day_name])) {
                                                $pekananKajianWidget = $pekananKajiansByDay[$duty->day_name];
                                            } elseif (isset($pekananKajiansByWeek[$currentWeekNumber])) {
                                                $pekananKajianWidget = $pekananKajiansByWeek[$currentWeekNumber];
                                            } else {
                                                $pekananKajianWidget = $defaultPekananKajian ?? null;
                                            }
                                        }

                                        $wImam = $duty->imam_name;
                                        if ($isFridayDzuhurWidget && $fridayKajianWidget && !empty($fridayKajianWidget->khatib_name)) {
                                            $wImam = $fridayKajianWidget->khatib_name;
                                        } elseif ($isUstazKajianWidget && $pekananKajianWidget && (!empty($pekananKajianWidget->speaker_name) || !empty($pekananKajianWidget->khatib_name))) {
                                            $wImam = $pekananKajianWidget->speaker_name ?: $pekananKajianWidget->khatib_name;
                                        }

                                        $wMuadzin = ($isFridayDzuhurWidget && $fridayKajianWidget && !empty($fridayKajianWidget->muadzin_name)) ? $fridayKajianWidget->muadzin_name : $duty->muadzin_name;
                                        $wMc = ($isFridayDzuhurWidget && $fridayKajianWidget && !empty($fridayKajianWidget->mc_name)) ? $fridayKajianWidget->mc_name : null;
                                    @endphp
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-gov-border flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                                <span class="font-bold text-gov-textMain">{{ $duty->day_name }}</span>
                                                <span class="inline-flex items-center h-4.5 px-1.5 rounded-md text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200 capitalize">{{ $duty->prayer_time }}</span>
                                                @if($isFridayDzuhurWidget && $fridayKajianWidget)
                                                    <span class="inline-flex items-center text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Kajian Jumat</span>
                                                    <span class="inline-flex items-center text-[10px] font-semibold text-gov-navy bg-amber-100/80 px-1.5 py-0.5 rounded border border-amber-300">
                                                        {{ \Carbon\Carbon::parse($fridayKajianWidget->date)->translatedFormat('d M Y') }} • {{ $fridayKajianWidget->time_display ?: '11:27 WIB' }}
                                                    </span>
                                                @elseif($isUstazKajianWidget && $pekananKajianWidget)
                                                    <span class="inline-flex items-center text-[10px] font-semibold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200">Kajian Pekanan</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-500 mt-0.5 flex flex-col items-start gap-1">
                                                <span>{{ $isFridayDzuhurWidget ? 'Khatib:' : 'Imam:' }} <strong class="text-slate-700">{{ $wImam }}</strong></span>
                                                @if($wMc)
                                                    <span>MC: <strong class="text-slate-700">{{ $wMc }}</strong></span>
                                                @endif
                                                <span>{{ $isFridayDzuhurWidget ? 'Bilal:' : 'Muadzin:' }} <strong class="text-slate-700">{{ $wMuadzin }}</strong></span>
                                            </div>
                                        </div>
                                        @if($isFridayDzuhurWidget && $fridayKajianWidget)
                                            <div class="flex items-center gap-1.5 shrink-0 pl-2">
                                                <a href="{{ route('admin.kajian.teks-mc', $fridayKajianWidget->id) }}" target="_blank"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-gov-50 hover:bg-gov-100 text-gov-navy border border-gov-border font-semibold text-xs shadow-2xs transition cursor-pointer shrink-0"
                                                    title="Buka & Cetak Teks MC Shalat Jumat">
                                                    <i data-lucide="printer" class="w-3.5 h-3.5 text-gov-navy shrink-0"></i>
                                                    <span>Teks MC</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada jadwal penugasan pekan ini.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
