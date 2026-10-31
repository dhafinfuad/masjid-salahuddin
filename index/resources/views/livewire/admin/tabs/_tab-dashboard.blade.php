<!-- ======================================================== -->
<!-- SUBTAB 1: DASHBOARD (HYBRID COMMAND HUB / MODEL B) -->
<!-- ======================================================== -->
<div class="space-y-6">
    <!-- Welcome Hero Banner (Navy with Pattern & Warm Gold Accents) -->
    <div
        class="bg-gov-navy rounded-2xl p-5 sm:p-6 text-white relative overflow-hidden shadow-xs bg-gov-pattern border border-gov-border/30">
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 relative z-10">
            <div class="space-y-1 max-w-xl">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span class="text-xs sm:text-sm text-amber-300 font-medium">Ahlan wa Sahlan</span>
                </div>
                <h2 class="text-xl sm:text-2xl py-0.5 font-bold text-white tracking-tight capitalize">
                    {{ preg_replace('/\s*\(.*?\)/', '', Auth::user()->name ?? 'Pengurus') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-200 font-normal leading-relaxed">Menebar Manfaat, Meraih Berkah &bull; {{ $settings->name }}</p>
            </div>
            <div class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-white/10 text-white border border-white/15 self-start sm:self-auto flex items-center gap-2 shrink-0">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Top Quick Status Cards (4 Kartu Ringkasan Cepat) -->
    @php
        $nextFridayKajian = $upcomingKajians->where('type', 'jumat')->first() 
            ?? \App\Models\Kajian::jumat()->where('date', '>=', \Carbon\Carbon::now('Asia/Jakarta')->subDays(1))->orderBy('date', 'asc')->first()
            ?? \App\Models\Kajian::jumat()->orderBy('date', 'desc')->first();
        $nextKajianDate = $nextFridayKajian ? \Carbon\Carbon::parse($nextFridayKajian->date) : null;
        $nextKhatib = $nextFridayKajian ? ($nextFridayKajian->khatib_name ?: 'Khatib Shalat Jumat') : 'Belum ada jadwal';

        // Petugas Dzuhur & Ashar
        $todayDzuhur = $currentWeekDuties->where('day_name', $todayDayName)->where('prayer_time', 'dzuhur')->first();
        $todayAshar = $currentWeekDuties->where('day_name', $todayDayName)->where('prayer_time', 'ashar')->first();

        // Fallback hari kerja terdekat jika akhir pekan (Sabtu/Minggu) atau belum diisi
        $fallbackDzuhur = $currentWeekDuties->where('prayer_time', 'dzuhur')->first();
        $fallbackAshar = $currentWeekDuties->where('prayer_time', 'ashar')->first();

        $dutyDzuhur = $todayDzuhur ?: $fallbackDzuhur;
        $dutyAshar = $todayAshar ?: $fallbackAshar;

        // Khusus Jumat Dzuhur
        $isFriDzuhur = $dutyDzuhur && ($dutyDzuhur->day_name === 'Jumat');
        $friKajDzuhur = ($isFriDzuhur && isset($fridayKajiansByWeek[$currentWeekNumber])) 
            ? $fridayKajiansByWeek[$currentWeekNumber] 
            : ($isFriDzuhur ? ($fridayKajians->first() ?? null) : null);

        $imamDzuhur = $dutyDzuhur ? $dutyDzuhur->imam_name : 'Belum diatur';
        $muadzinDzuhur = $dutyDzuhur ? $dutyDzuhur->muadzin_name : 'Belum diatur';
        $mcDzuhur = null;

        if ($isFriDzuhur && $friKajDzuhur) {
            if (!empty($friKajDzuhur->khatib_name)) $imamDzuhur = $friKajDzuhur->khatib_name;
            if (!empty($friKajDzuhur->muadzin_name)) $muadzinDzuhur = $friKajDzuhur->muadzin_name;
            if (!empty($friKajDzuhur->mc_name)) $mcDzuhur = $friKajDzuhur->mc_name;
        } elseif ($dutyDzuhur && stripos($dutyDzuhur->imam_name, 'kajian') !== false) {
            $pekKey = "{$dutyDzuhur->day_name}_{$currentWeekNumber}";
            $pekKaj = $pekananKajiansByDayWeek[$pekKey] ?? ($pekananKajiansByDay[$dutyDzuhur->day_name] ?? ($pekananKajiansByWeek[$currentWeekNumber] ?? ($defaultPekananKajian ?? null)));
            if ($pekKaj && (!empty($pekKaj->speaker_name) || !empty($pekKaj->khatib_name))) {
                $imamDzuhur = $pekKaj->speaker_name ?: $pekKaj->khatib_name;
            }
        }

        $tagDzuhur = $isFriDzuhur ? 'Khutbah Jumat' : ('Petugas ' . ($dutyDzuhur ? $dutyDzuhur->day_name : 'Senin') . ' Dzuhur');
        $tagAshar = 'Petugas ' . ($dutyAshar ? $dutyAshar->day_name : 'Senin') . ' Ashar';
        $imamAshar = $dutyAshar ? $dutyAshar->imam_name : 'Belum diatur';
        if ($dutyAshar && stripos($dutyAshar->imam_name, 'kajian') !== false) {
            $pekKey = "{$dutyAshar->day_name}_{$currentWeekNumber}";
            $pekKaj = $pekananKajiansByDayWeek[$pekKey] ?? ($pekananKajiansByDay[$dutyAshar->day_name] ?? ($pekananKajiansByWeek[$currentWeekNumber] ?? ($defaultPekananKajian ?? null)));
            if ($pekKaj && (!empty($pekKaj->speaker_name) || !empty($pekKaj->khatib_name))) {
                $imamAshar = $pekKaj->speaker_name ?: $pekKaj->khatib_name;
            }
        }
        $muadzinAshar = $dutyAshar ? $dutyAshar->muadzin_name : 'Belum diatur';
    @endphp

    <!-- Mobile: scroll-snap carousel (sm:hidden) -->
    <div class="sm:hidden"
        x-data="{
            kpiIdx: 0,
            kpiTotal: 4,
            scrollTo(idx) {
                this.kpiIdx = Math.max(0, Math.min(idx, this.kpiTotal - 1));
                const el = this.$refs.kpiTrack;
                const cardW = el.scrollWidth / this.kpiTotal;
                el.scrollTo({ left: cardW * this.kpiIdx, behavior: 'smooth' });
            },
            onScroll() {
                const el = this.$refs.kpiTrack;
                const cardW = el.scrollWidth / this.kpiTotal;
                this.kpiIdx = Math.round(el.scrollLeft / cardW);
            }
        }"
    >
        <!-- Scroll track -->
        <div
            x-ref="kpiTrack"
            @scroll.passive="onScroll()"
            class="flex overflow-x-auto snap-x snap-mandatory gap-3 pb-1 scrollbar-none"
            style="-ms-overflow-style:none; scrollbar-width:none;"
        >
            <!-- Card 1: Saldo Tersisa -->
            <div @click="switchTabFast('finance')"
                class="snap-start shrink-0 w-full bg-white p-4 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0 shadow-2xs group-hover:bg-emerald-100 group-hover:border-emerald-300 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Saldo Tersisa</span>
                        <h4 class="text-xs font-bold text-gov-textMain truncate leading-tight mt-0.5 tnum">
                            Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}
                        </h4>
                        <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                            Penerimaan: <span class="text-emerald-700 font-semibold tnum">+Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</span>
                        </p>
                    </div>
                </div>
                <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-gov-50 text-slate-400 group-hover:text-gov-navy flex items-center justify-center shrink-0 transition">
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            <!-- Card 2: Petugas Dzuhur -->
            <div @click="switchTabFast('petugas')"
                class="snap-start shrink-0 w-full bg-white p-4 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 shadow-2xs group-hover:bg-amber-100 group-hover:border-amber-300 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">
                            {{ $tagDzuhur }}
                        </span>
                        <h4 class="text-xs font-bold text-gov-textMain truncate leading-tight mt-0.5">
                            Imam : {{ $imamDzuhur }}
                        </h4>
                        <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                            Muadzin : {{ $muadzinDzuhur }}@if($mcDzuhur) • MC : {{ $mcDzuhur }}@endif
                        </p>
                    </div>
                </div>
                <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-gov-50 text-slate-400 group-hover:text-gov-navy flex items-center justify-center shrink-0 transition">
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            <!-- Card 3: Petugas Ashar -->
            <div @click="switchTabFast('petugas')"
                class="snap-start shrink-0 w-full bg-white p-4 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 shadow-2xs group-hover:bg-amber-100 group-hover:border-amber-300 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">
                            {{ $tagAshar }}
                        </span>
                        <h4 class="text-xs font-bold text-gov-textMain truncate leading-tight mt-0.5">
                            Imam : {{ $imamAshar }}
                        </h4>
                        <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                            Muadzin : {{ $muadzinAshar }}
                        </p>
                    </div>
                </div>
                <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-gov-50 text-slate-400 group-hover:text-gov-navy flex items-center justify-center shrink-0 transition">
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>

            <!-- Card 4: Khutbah Jumat Terdekat -->
            <div @click="switchTabFast('kegiatan')"
                class="snap-start shrink-0 w-full bg-white p-4 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0 shadow-2xs group-hover:bg-blue-100 group-hover:border-blue-300 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">
                            Khutbah Jumat Terdekat
                        </span>
                        <h4 class="text-xs font-bold text-gov-textMain truncate leading-tight mt-0.5">
                            {{ $nextKhatib }}
                        </h4>
                        <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                            @if($nextFridayKajian)
                                {{ $nextKajianDate ? $nextKajianDate->translatedFormat('d M') : '' }} • {{ $nextFridayKajian->time_display ?: '11:45 WIB' }}
                            @else
                                Belum ada jadwal terdekat
                            @endif
                        </p>
                    </div>
                </div>
                <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-gov-50 text-slate-400 group-hover:text-gov-navy flex items-center justify-center shrink-0 transition">
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>
        </div>

        <!-- Bottom nav: dots indicator -->
        <div class="flex items-center justify-center gap-1.5 mt-2.5">
            <template x-for="i in kpiTotal" :key="i">
                <button type="button" @click="scrollTo(i - 1)"
                    class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                    :class="kpiIdx === i - 1 ? 'bg-gov-navy w-4' : 'bg-slate-300 w-1.5'"
                ></button>
            </template>
        </div>
    </div>

    <!-- Desktop: normal grid (hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4) -->
    <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <!-- Card 1: Saldo Tersisa -->
        <div @click="switchTabFast('finance')"
            class="bg-white p-4 sm:p-4.5 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0 shadow-2xs group-hover:bg-emerald-100 group-hover:border-emerald-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-700">Saldo Tersisa</span>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain truncate leading-tight mt-0.5 tnum">
                        Rp {{ number_format($saldoSaatIni, 0, ',', '.') }}
                    </h4>
                    <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                        Penerimaan: <span class="text-emerald-700 font-semibold tnum">+Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</span>
                    </p>
                </div>
            </div>
            <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-gov-50 text-slate-400 group-hover:text-gov-navy flex items-center justify-center shrink-0 transition">
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- Card 2: Petugas Dzuhur -->
        <div @click="switchTabFast('petugas')"
            class="bg-white p-4 sm:p-4.5 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 shadow-2xs group-hover:bg-amber-100 group-hover:border-amber-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-amber-700">
                        {{ $tagDzuhur }}
                    </span>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain truncate leading-tight mt-0.5">
                        Imam : {{ $imamDzuhur }}
                    </h4>
                    <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                        Muadzin : {{ $muadzinDzuhur }}@if($mcDzuhur) • MC : {{ $mcDzuhur }}@endif
                    </p>
                </div>
            </div>
            <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-gov-50 text-slate-400 group-hover:text-gov-navy flex items-center justify-center shrink-0 transition">
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- Card 3: Petugas Ashar -->
        <div @click="switchTabFast('petugas')"
            class="bg-white p-4 sm:p-4.5 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 shadow-2xs group-hover:bg-amber-100 group-hover:border-amber-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-amber-700">
                        {{ $tagAshar }}
                    </span>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain truncate leading-tight mt-0.5">
                        Imam : {{ $imamAshar }}
                    </h4>
                    <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                        Muadzin : {{ $muadzinAshar }}
                    </p>
                </div>
            </div>
            <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-gov-50 text-slate-400 group-hover:text-gov-navy flex items-center justify-center shrink-0 transition">
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- Card 4: Khutbah Jumat Terdekat -->
        <div @click="switchTabFast('kegiatan')"
            class="bg-white p-4 sm:p-4.5 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0 shadow-2xs group-hover:bg-blue-100 group-hover:border-blue-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-blue-700">
                        Khutbah Jumat Terdekat
                    </span>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain truncate leading-tight mt-0.5">
                        {{ $nextKhatib }}
                    </h4>
                    <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                        @if($nextFridayKajian)
                            {{ $nextKajianDate ? $nextKajianDate->translatedFormat('d M') : '' }} • {{ $nextFridayKajian->time_display ?: '11:45 WIB' }}
                        @else
                            Belum ada jadwal terdekat
                        @endif
                    </p>
                </div>
            </div>
            <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-gov-50 text-slate-400 group-hover:text-gov-navy flex items-center justify-center shrink-0 transition">
                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>
    </div>

    <!-- Section Title: Menu Manajemen Utama -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3 pt-2">
        <div>
            <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Menu Manajemen Utama</h3>
            <p class="text-xs text-gov-textMuted mt-0.5">Pilih modul untuk mengelola aktivitas dan administrasi masjid secara langsung.</p>
        </div>
        <span class="text-[11px] font-semibold text-slate-400 hidden sm:inline-block">1-Tap Quick Access</span>
    </div>

    <style>
        @media (min-width: 640px) and (max-width: 1023px) {
            .dashboard-menu-grid {
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            }
        }
        @media (min-width: 1024px) {
            .dashboard-menu-grid {
                display: grid !important;
                grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            }
        }
    </style>

    <!-- Grid Menu Launcher Cards (10 Cards: 2 Kolom Mobile, 4 Kolom Tablet, 5 Kolom Desktop) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3.5 sm:gap-4 dashboard-menu-grid">
        <!-- 1. Jadwal Kegiatan -->
        <div @click="switchTabFast('kegiatan')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0 group-hover:bg-blue-100/80 group-hover:border-blue-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="M17 14h-6"/><path d="M13 18H7"/><path d="M7 14h.01"/><path d="M17 18h.01"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Jadwal Kegiatan</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Kajian Pekanan, Khutbah Jumat & Agenda Kegiatan Akbar</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-blue-700 group-hover:text-blue-800 transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-blue-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 2. Imam & Muadzin (Petugas Shalat) -->
        <div @click="switchTabFast('petugas')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-teal-50 text-teal-700 border border-teal-200 flex items-center justify-center shrink-0 group-hover:bg-teal-100/80 group-hover:border-teal-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Petugas Shalat</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Imam Rawatib, Muadzin & Petugas Shalat Jumat</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-teal-700 group-hover:text-teal-800 transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-teal-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 3. Kas & Keuangan -->
        <div @click="switchTabFast('finance')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0 group-hover:bg-emerald-100/80 group-hover:border-emerald-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Kas & Keuangan</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Buku Kas, Mutasi Masuk/Keluar & Rekapitulasi</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-emerald-700 group-hover:text-emerald-800 transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-emerald-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 4. Program Sosial -->
        <div @click="switchTabFast('programs')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 group-hover:bg-amber-100/80 group-hover:border-amber-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/><path d="M12 5 9.04 7.96a2.17 2.17 0 0 0 0 3.08v0c.82.82 2.13.85 3 .07l2.07-1.9a2.82 2.82 0 0 1 3.79 0l2.96 2.66"/><path d="m18 15-2-2"/><path d="m15 18-2-2"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Program Sosial</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Infaq Tukin Pegawai, Santunan & Donasi Jamaah</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-amber-700 group-hover:text-amber-800 transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-amber-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 5. Galeri Kegiatan -->
        <div @click="switchTabFast('galeri')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center shrink-0 group-hover:bg-rose-100/80 group-hover:border-rose-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 22H4a2 2 0 0 1-2-2V6"/><path d="m22 13-1.296-1.296a2.41 2.41 0 0 0-3.408 0L11 18"/><circle cx="12" cy="8" r="2"/><rect width="16" height="16" x="6" y="2" rx="2"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Galeri Foto</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Dokumentasi Acara & Arsip Kegiatan Masjid</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-rose-700 group-hover:text-rose-800 transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-rose-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        @unless(Auth::user()->isJamaah())
        <!-- 6. Saran & Kritik -->
        <div @click="switchTabFast('saran')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center shrink-0 group-hover:bg-purple-100/80 group-hover:border-purple-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 12a2 2 0 0 0 2-2V8H8"/><path d="M14 12a2 2 0 0 0 2-2V8h-2"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Saran & Kritik</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Aspirasi, Kotak Saran & Evaluasi Jamaah</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-purple-700 group-hover:text-purple-800 transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-purple-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 7. Pengaturan -->
        <div @click="switchTabFast('settings')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center shrink-0 group-hover:bg-slate-200/80 group-hover:border-slate-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Pengaturan</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Profil Masjid, Poster Studio & Template Jarkoman</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-700 group-hover:text-slate-800 transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-slate-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 8. Pengguna & DKM -->
        <div @click="switchTabFast('users')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center shrink-0 group-hover:bg-indigo-100/80 group-hover:border-indigo-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><circle cx="19" cy="11" r="2"/><path d="M19 8v1"/><path d="M19 13v1"/><path d="m21.6 9.5-.87.5"/><path d="m17.27 12-.87.5"/><path d="m21.6 12.5-.87-.5"/><path d="m17.27 10-.87-.5"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Pengguna & DKM</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Anggota Jamaah, Pegawai & SK Takmir</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-indigo-700 group-hover:text-indigo-800 transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-indigo-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 9. Statistik Pengunjung -->
        <div @click="switchTabFast('statistics')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-cyan-50 text-cyan-700 border border-cyan-200 flex items-center justify-center shrink-0 group-hover:bg-cyan-100/80 group-hover:border-cyan-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Statistik</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Kunjungan Portal Jamaah & Analisis Jam Sibuk</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-cyan-700 group-hover:text-cyan-800 transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-cyan-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>
        @endunless

        <!-- 10. Portal Jamaah -->
        <a href="{{ url('/') }}" target="_blank"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-50 text-orange-700 border border-orange-200 flex items-center justify-center shrink-0 group-hover:bg-orange-100/80 group-hover:border-orange-300 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Portal Jamaah</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Halaman Muka Publik Terbuka untuk Seluruh Jamaah</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-orange-700 group-hover:text-orange-800 transition-colors">
                <span>Kunjungi</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-orange-700 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </div>
        </a>
    </div>
</div>
