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

    <!-- Top Quick Status Cards (3 Kartu Ringkasan Cepat) -->
    @php
        $nextKajian = $upcomingKajians->first();
        $isNextJumat = $nextKajian && $nextKajian->type === 'jumat';
        $nextKajianDate = $nextKajian ? \Carbon\Carbon::parse($nextKajian->date) : null;
        $nextSpeaker = $nextKajian ? ($isNextJumat ? ($nextKajian->khatib_name ?: 'Khatib Shalat Jumat') : ($nextKajian->speaker_name ?: 'Pemateri Kajian')) : null;
        $nextTypeLabel = $isNextJumat ? 'Shalat Jumat' : 'Kajian Pekanan';
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
        <!-- Status 1: Jadwal Ibadah / Kajian Terdekat -->
        <div @click="switchTabFast('kegiatan')"
            class="bg-white p-4 sm:p-4.5 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl {{ $isNextJumat ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-teal-50 text-teal-700 border-teal-200' }} border flex items-center justify-center shrink-0 font-bold text-xs shadow-2xs group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    {{ $isNextJumat ? 'JUM' : 'KAJ' }}
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider {{ $isNextJumat ? 'text-blue-700' : 'text-teal-700' }}">
                        {{ $nextTypeLabel }} Terdekat
                    </span>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain truncate leading-tight mt-0.5">
                        {{ $nextSpeaker ?: 'Belum ada jadwal terdekat' }}
                    </h4>
                    <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                        @if($nextKajian)
                            {{ $nextKajianDate ? $nextKajianDate->translatedFormat('d M') : '' }} &bull; {{ $nextKajian->time_display ?: ($isNextJumat ? '11:45 WIB' : '09:00 WIB') }}
                            @if($nextKajian->muadzin_name)
                                &bull; Bilal: {{ $nextKajian->muadzin_name }}
                            @endif
                        @else
                            Jadwal kajian atau Jumat belum diatur
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($isNextJumat && $nextKajian)
                    <a href="{{ route('admin.kajian.teks-mc', ['id' => $nextKajian->id, 'from' => 'admin']) }}" target="_blank"
                        @click.stop
                        class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 text-xs font-semibold shadow-2xs transition"
                        title="Buka & Cetak Teks MC Shalat Jumat">
                        <svg class="w-3.5 h-3.5 text-blue-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                        <span>Teks MC</span>
                    </a>
                @endif
                <div class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-gov-50 text-slate-400 group-hover:text-gov-navy flex items-center justify-center transition">
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </div>
        </div>

        <!-- Status 2: Saldo Kas Utama -->
        <div @click="switchTabFast('finance')"
            class="bg-white p-4 sm:p-4.5 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0 font-bold text-xs shadow-2xs group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-700">Saldo Kas Utama</span>
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

        <!-- Status 3: Status Operasional / DKM & Petugas -->
        @php
            $hasDuties = $currentWeekDuties->isNotEmpty();
            $resolvedDutyImam = null;
            $resolvedDutyLabel = null;
            if ($hasDuties) {
                $dutyFirst = $currentWeekDuties->first();
                $isFri = ($dutyFirst->day_name === 'Jumat' && $dutyFirst->prayer_time === 'dzuhur');
                $friKaj = ($isFri && isset($fridayKajiansByWeek[$currentWeekNumber])) ? $fridayKajiansByWeek[$currentWeekNumber] : ($isFri ? ($fridayKajians->first() ?? null) : null);
                $isUst = (stripos($dutyFirst->imam_name, 'kajian') !== false);
                $pekKey = "{$dutyFirst->day_name}_{$currentWeekNumber}";
                $pekKaj = $isUst ? ($pekananKajiansByDayWeek[$pekKey] ?? ($pekananKajiansByDay[$dutyFirst->day_name] ?? ($pekananKajiansByWeek[$currentWeekNumber] ?? ($defaultPekananKajian ?? null)))) : null;
                $resolvedDutyImam = $dutyFirst->imam_name;
                if ($isFri && $friKaj && !empty($friKaj->khatib_name)) {
                    $resolvedDutyImam = $friKaj->khatib_name;
                } elseif ($isUst && $pekKaj && (!empty($pekKaj->speaker_name) || !empty($pekKaj->khatib_name))) {
                    $resolvedDutyImam = $pekKaj->speaker_name ?: $pekKaj->khatib_name;
                }
                $resolvedDutyLabel = "{$dutyFirst->day_name} " . ucfirst($dutyFirst->prayer_time);
            }
        @endphp
        <div @click="switchTabFast('{{ Auth::user()->isJamaah() ? 'petugas' : 'users' }}')"
            class="bg-white p-4 sm:p-4.5 rounded-2xl border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer flex items-center justify-between gap-3 group">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0 font-bold text-xs shadow-2xs group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-amber-700">
                        {{ $hasDuties ? 'Petugas Shalat Pekan Ini' : 'Status Operasional DKM' }}
                    </span>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain truncate leading-tight mt-0.5">
                        @if($hasDuties)
                            {{ $resolvedDutyLabel }}: {{ $resolvedDutyImam }}
                        @else
                            DKM Periode 2026-2029
                        @endif
                    </h4>
                    <p class="text-[11px] text-gov-textMuted truncate mt-0.5">
                        Pekan ke-{{ $currentWeekNumber }} &bull; {{ $totalJamaah > 0 ? $totalJamaah . ' Jamaah' : 'Pengurus Aktif' }}
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

    <!-- Grid Menu Launcher Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <!-- 1. Jadwal Kegiatan -->
        <div @click="switchTabFast('kegiatan')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Jadwal Kegiatan</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Kajian Pekanan, Khutbah Jumat & Agenda Kegiatan Akbar</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-blue-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 2. Imam & Muadzin (Petugas Shalat) -->
        <div @click="switchTabFast('petugas')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-teal-50 text-teal-700 border border-teal-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Petugas Shalat</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Imam Rawatib, Muadzin & Petugas Shalat Jumat</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-teal-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 3. Kas & Keuangan -->
        <div @click="switchTabFast('finance')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Kas & Keuangan</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Buku Kas, Mutasi Masuk/Keluar & Rekapitulasi</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-emerald-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 4. Program Sosial -->
        <div @click="switchTabFast('programs')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-700 border border-amber-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Program Sosial</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Infaq Tukin Pegawai, Santunan & Donasi Jamaah</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-amber-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 5. Galeri Kegiatan -->
        <div @click="switchTabFast('galeri')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-rose-50 text-rose-700 border border-rose-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Galeri Foto</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Dokumentasi Acara & Arsip Kegiatan Masjid</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-rose-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        @unless(Auth::user()->isJamaah())
        <!-- 6. Saran & Kritik -->
        <div @click="switchTabFast('saran')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-50 text-purple-700 border border-purple-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Saran & Kritik</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Aspirasi, Kotak Saran & Evaluasi Jamaah</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-purple-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 7. Pengaturan -->
        <div @click="switchTabFast('settings')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Pengaturan</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Profil Masjid, Poster Studio & Template Jarkoman</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-slate-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 8. Pengguna & DKM -->
        <div @click="switchTabFast('users')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Pengguna & DKM</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Anggota Jamaah, Pegawai & SK Takmir</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-indigo-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>

        <!-- 9. Statistik Pengunjung -->
        <div @click="switchTabFast('statistics')"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-cyan-50 text-cyan-700 border border-cyan-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Statistik</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Kunjungan Portal Jamaah & Analisis Jam Sibuk</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-cyan-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Menu</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </div>
        @endunless

        <!-- 10. TV Display -->
        <a href="{{ url('/display') }}" target="_blank"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-sky-50 text-sky-700 border border-sky-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="15" x="2" y="3" rx="2"/><polyline points="17 21 12 18 7 21"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">TV Display</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Layar Informasi Digital Masjid & Jam Sholat Digital</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-sky-700 group-hover:text-gov-navy transition-colors">
                <span>Buka Layar</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </div>
        </a>

        <!-- 11. Portal Jamaah -->
        <a href="{{ url('/') }}" target="_blank"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-gov-border shadow-2xs hover:shadow-md hover:border-gov-navy/40 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-50 text-orange-700 border border-orange-100 flex items-center justify-center shrink-0 group-hover:bg-gov-navy group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain group-hover:text-gov-navy transition-colors truncate">Portal Jamaah</h4>
                    <p class="text-[11px] text-gov-textMuted mt-0.5 leading-snug line-clamp-2">Halaman Muka Publik Terbuka untuk Seluruh Jamaah</p>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-semibold text-orange-700 group-hover:text-gov-navy transition-colors">
                <span>Kunjungi</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:translate-x-1 group-hover:text-gov-navy transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </div>
        </a>
    </div>
</div>
