            <!-- ======================================================== -->
            <!-- SUBTAB: PENUGASAN IMAM & ADZAN (MILESTONE 2) -->
            <!-- ======================================================== -->
            <div class="space-y-4">
                <!-- Top Header & Action Bar -->
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gov-border shadow-2xs">
                    <div class="flex items-center space-x-2.5">
                        <div
                            class="hidden sm:flex w-8 h-8 rounded-lg bg-slate-100 text-gov-navy items-center justify-center border border-gov-border shrink-0">
                            <i data-lucide="user-check" class="w-4 h-4 text-gov-navy"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gov-textMain">Jadwal Penugasan Petugas Ibadah</h3>
                            <p class="text-xs text-gov-textMuted">Kelola jadwal Imam dan Muadzin rutin khusus Shalat
                                Dzuhur & Ashar.</p>
                        </div>
                    </div>

                    <!-- Right Buttons: Import, Export, Tambah Petugas -->
                    <div class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto">
                        @if(Auth::user()->canManage())
                            <!-- Tombol Import Jadwal -->
                            <button type="button" @click="showImportDutyModal = true; $wire.openImportDuty()"
                                class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="file-up" class="w-4 h-4 text-gov-navy"></i>
                                <span>Import</span>
                            </button>
                        @endif

                        <!-- Dropdown Export Jadwal -->
                        <div class="relative w-full sm:w-auto {{ !Auth::user()->canManage() ? 'col-span-2 sm:col-span-1' : '' }}" x-data="{ openDutyExport: false }"
                            @click.outside="openDutyExport = false">
                            <button type="button" @click="openDutyExport = !openDutyExport"
                                class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="file-down" class="w-4 h-4 text-emerald-600"></i>
                                <span>Export</span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                            </button>
                            <div x-show="openDutyExport" x-cloak
                                class="absolute right-0 mt-1 w-52 bg-white rounded-lg shadow-xl border border-gov-border py-1.5 z-20 text-xs animate-in fade-in zoom-in-95 duration-100">
                                <a :href="'{{ route('admin.petugas.export-excel') }}?week=' + $wire.prayerDutyFilterWeek + '&month=' + $wire.prayerDutyFilterMonth + '&year=' + $wire.prayerDutyFilterYear"
                                    class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition font-medium">
                                    <i data-lucide="sheet" class="w-4 h-4 text-emerald-600"></i>
                                    <span>Download Excel (.xlsx)</span>
                                </a>
                                <a :href="'{{ route('admin.petugas.export-pdf') }}?week=' + $wire.prayerDutyFilterWeek + '&month=' + $wire.prayerDutyFilterMonth + '&year=' + $wire.prayerDutyFilterYear"
                                    target="_blank"
                                    class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-rose-50 hover:text-rose-800 transition font-medium">
                                    <i data-lucide="printer" class="w-4 h-4 text-rose-600"></i>
                                    <span>Cetak Jadwal (PDF)</span>
                                </a>
                            </div>
                        </div>

                        @if(Auth::user()->canManage())
                            <!-- Tombol Tambah Petugas -->
                            <button type="button" @click="openCreatePrayerDuty()"
                                class="col-span-2 sm:col-span-1 w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer">
                                <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
                                <span>Tambah Petugas</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Filter Toolbar: Filter Pekan, Filter Bulan, Filter Tahun, & Search -->
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-3 rounded-xl border border-gov-border shadow-2xs">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 w-full md:flex md:items-center md:w-auto text-xs">
                        <select wire:model.live="prayerDutyFilterWeek"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 text-xs font-medium text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy cursor-pointer">
                            <option value="all">Semua Pekan</option>
                            <option value="1">Pekan 1 {{ $currentWeekNumber === 1 ? '⭐ (Hari Ini)' : '' }}</option>
                            <option value="2">Pekan 2 {{ $currentWeekNumber === 2 ? '⭐ (Hari Ini)' : '' }}</option>
                            <option value="3">Pekan 3 {{ $currentWeekNumber === 3 ? '⭐ (Hari Ini)' : '' }}</option>
                            <option value="4">Pekan 4 {{ $currentWeekNumber === 4 ? '⭐ (Hari Ini)' : '' }}</option>
                            <option value="5">Pekan 5 {{ $currentWeekNumber === 5 ? '⭐ (Hari Ini)' : '' }}</option>
                        </select>

                        <span class="sr-only">BULAN:</span>
                        <select wire:model.live="prayerDutyFilterMonth"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 text-xs font-medium text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy cursor-pointer">
                            @foreach($dutyMonths as $mNum => $mName)
                                <option value="{{ $mNum }}">{{ $mName }}{{ (int) $mNum === (int) Carbon\Carbon::now('Asia/Jakarta')->month ? ' ⭐ (Bulan Ini)' : '' }}</option>
                            @endforeach
                        </select>

                        <span class="sr-only">TAHUN:</span>
                        <select wire:model.live="prayerDutyFilterYear"
                            class="col-span-2 sm:col-span-1 w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 text-xs font-medium text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy cursor-pointer">
                            <option value="all">Semua Tahun</option>
                            @foreach($availableDutyYears as $yr)
                                <option value="{{ $yr }}">{{ $yr }}{{ (string) $yr === (string) Carbon\Carbon::now('Asia/Jakarta')->year ? ' (Tahun Ini)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full md:w-64" x-data="{
                        clearSearch() {
                            $wire.clearPetugasSearch();
                        }
                    }">
                        <i data-lucide="search"
                            class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Cari nama petugas / hari..."
                            class="h-[32px] w-full pl-8 pr-9 py-1 rounded-lg border border-gov-border bg-slate-50 text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                        <button type="button" @click="clearSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 transition cursor-pointer flex items-center justify-center" title="Reset pencarian ke data seharusnya">
                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Info Box Sinkronisasi Otomatis -->
                <div
                    class="p-3 bg-emerald-50/80 rounded-xl border border-emerald-200/80 text-emerald-900 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                        <span><strong>Sinkronisasi Otomatis:</strong> Petugas Shalat Dzuhur Jumat otomatis diselaraskan dengan Khatib Jumat, dan Imam "Ustaz kajian" otomatis diselaraskan dengan Narasumber Kajian Pekanan terkait.</span>
                    </div>
                    <button type="button" @click="switchTabFast('kajian'); kajianSubTab = 'pekanan'"
                        class="text-emerald-700 hover:text-emerald-900 font-bold underline whitespace-nowrap text-left sm:text-right cursor-pointer">
                        Lihat Jadwal Kajian &rarr;
                    </button>
                </div>

                <!-- Tabel Jadwal Petugas Ibadah (Smart Hybrid Architecture) -->
                <div class="space-y-3">
                    <!-- Desktop Executive Table (Merged Cohesive Columns) -->
                    <div
                        class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-300 text-slate-500 uppercase tracking-wider text-xs font-semibold">
                                    <th class="p-3.5 text-center w-12">No</th>
                                    <th wire:click="sortBy('day_name', 'petugas')" class="p-3.5 w-48 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Hari & Waktu Shalat</span>
                                            <x-sort-icon field="day_name" table="petugas" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('date', 'petugas')" class="p-3.5 w-44 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600"></i>
                                            <span>Tanggal</span>
                                            <x-sort-icon field="date" table="petugas" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('imam_name', 'petugas')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Imam</span>
                                            <x-sort-icon field="imam_name" table="petugas" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('muadzin_name', 'petugas')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Muadzin / MC</span>
                                            <x-sort-icon field="muadzin_name" table="petugas" />
                                        </div>
                                    </th>
                                    <th class="p-3.5 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 transition-opacity duration-150">
                                @forelse($allPrayerDuties as $idx => $duty)
                                    @php
                                        $isToday = ($duty->day_name === $todayDayName);
                                        $isFridayDzuhur = ($duty->day_name === 'Jumat' && $duty->prayer_time === 'dzuhur');
                                        
                                        $dutyTargetWeek = $duty->resolved_week ?? $dutyFilterWeekNum;
                                        if (!$dutyTargetWeek) {
                                            if ($duty->matchesWeek($currentWeekNumber)) {
                                                $dutyTargetWeek = $currentWeekNumber;
                                            } elseif (preg_match('/pekan_(\d)/', $duty->week_pattern, $m)) {
                                                $dutyTargetWeek = (int) $m[1];
                                            } elseif ($duty->week_pattern === 'semua') {
                                                $dutyTargetWeek = $currentWeekNumber;
                                            }
                                        }

                                        $dutyWeek = $duty->resolved_week ?? $dutyTargetWeek;
                                        $dutyWeekLabel = $duty->resolved_week_label ?? ($dutyWeek ? ('Pekan ' . $dutyWeek) : $duty->week_pattern_label);

                                        $resolvedDate = $duty->resolved_date ?? $duty->resolveDate($dutyFilterYearNum, $dutyFilterMonthNum, $dutyTargetWeek);
                                        $dateKey = $resolvedDate ? $resolvedDate->format('Y-m-d') : null;
                                        $isDateToday = $resolvedDate ? $resolvedDate->isToday() : false;

                                        $fridayKajian = null;
                                        if ($isFridayDzuhur) {
                                            $fridayKajian = ($dateKey && isset($fridayKajiansByDate[$dateKey]))
                                                ? $fridayKajiansByDate[$dateKey]
                                                : ($dutyWeek && isset($fridayKajiansByWeek[$dutyWeek]) ? $fridayKajiansByWeek[$dutyWeek] : ($fridayKajians->first() ?? null));
                                        }

                                        $kajianOnDate = ($duty->prayer_time === 'ashar' && $dateKey && isset($kajianUmumByDate[$dateKey]))
                                            ? $kajianUmumByDate[$dateKey]
                                            : null;

                                        $displayImam = $duty->imam_name ?: '-';
                                        $displayMuadzin = $duty->muadzin_name ?: '-';
                                        $displayMc = null;

                                        if ($isFridayDzuhur && $fridayKajian && !empty($fridayKajian->khatib_name)) {
                                            $displayImam = $fridayKajian->khatib_name;
                                            if (!empty($fridayKajian->muadzin_name)) {
                                                $displayMuadzin = $fridayKajian->muadzin_name;
                                            }
                                            $displayMc = $fridayKajian->mc_name ?: null;
                                        } elseif ($duty->prayer_time === 'ashar' && $kajianOnDate && !empty($kajianOnDate->speaker_name)) {
                                            $displayImam = $kajianOnDate->speaker_name;
                                            if (!empty($kajianOnDate->muadzin_name)) {
                                                $displayMuadzin = $kajianOnDate->muadzin_name;
                                            }
                                        }
                                    @endphp
                                    <tr wire:key="duty-row-{{ $duty->id ?? $idx }}"
                                        class="hover:bg-amber-100/70 transition {{ $isDateToday ? 'bg-amber-100/40 border-l-4 border-l-amber-500' : '' }}">
                                        <td class="p-3.5 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>

                                        <!-- HARI & WAKTU SHALAT (MERGED) -->
                                        <td class="p-3.5">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span
                                                    class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">
                                                    {{ $dutyWeekLabel ?? $duty->week_pattern_label }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold border leading-none bg-slate-100 text-slate-600 border-slate-300">
                                                    {{ $duty->tahun ?: $dutyFilterYearNum }}
                                                </span>
                                            </div>
                                            <div class="text-sm font-bold text-gov-textMain mt-1.5">
                                                {{ $duty->day_name }} •
                                                {{ $duty->prayer_time === 'dzuhur' ? 'Dzuhur' : 'Ashar' }}
                                            </div>
                                        </td>

                                        <!-- TANGGAL PENUGASAN -->
                                        <td class="p-3.5">
                                            @if($resolvedDate)
                                                <div class="text-xs font-bold text-gov-navy">
                                                    {{ $resolvedDate->translatedFormat('d F Y') }}
                                                </div>
                                            @else
                                                <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-slate-100 text-slate-500 border-slate-200" title="Bulan {{ $dutyMonths[(int) $dutyFilterMonthNum] ?? '' }} {{ $dutyFilterYearNum }} tidak memiliki hari {{ $duty->day_name }} pada pekan ke-{{ $dutyTargetWeek ?? 5 }}">
                                                    Tidak ada di bln ini
                                                </span>
                                            @endif
                                        </td>


                                        <!-- IMAM -->
                                        <td class="p-3.5">
                                            @if($isFridayDzuhur)
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-gov-navy text-xs truncate" title="{{ $displayImam }}">{{ $displayImam }}</span>
                                                </div>
                                            @elseif($duty->prayer_time === 'ashar' && $kajianOnDate)
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-bold text-gov-navy text-xs truncate" title="Narasumber Kajian: {{ $displayImam }} ({{ $kajianOnDate->title }})">{{ $displayImam }}</span>
                                                </div>
                                            @else
                                                <div class="font-bold text-gov-navy text-xs truncate" title="{{ $displayImam }}">
                                                    {{ $displayImam }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- MUADZIN -->
                                        <td class="p-3.5">
                                            @if($isFridayDzuhur)
                                                <div class="space-y-0.5">
                                                    <div class="flex items-center gap-1.5 text-xs">
                                                        <span class="text-slate-400 text-[11px] shrink-0">Muadzin :</span>
                                                        <span class="font-semibold text-slate-800 truncate" title="{{ $displayMuadzin }}">{{ $displayMuadzin }}</span>
                                                    </div>
                                                    @if($displayMc)
                                                        <div class="flex items-center gap-1.5 text-xs">
                                                            <span class="text-slate-400 text-[11px] shrink-0">MC :</span>
                                                            <span class="font-semibold text-slate-800 truncate" title="{{ $displayMc }}">{{ $displayMc }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="font-semibold text-slate-800 text-xs truncate">
                                                    {{ $displayMuadzin }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- AKSI -->
                                        <td class="p-3.5 text-center whitespace-nowrap">
                                            @if(Auth::user()->canManage())
                                                <div class="flex items-center justify-center space-x-1.5">
                                                    @if($isFridayDzuhur)
                                                        <!-- Tombol Edit Khusus Jumat Dzuhur: Arahkan ke Kegiatan -> Khutbah Jumat & buka modal Edit Jadwal Kajian -->
                                                        <button type="button"
                                                            wire:click="openFridayKajianFromPetugas({{ $fridayKajian?->id ? $fridayKajian->id : 'null' }})"
                                                            wire:loading.attr="disabled"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 shadow-2xs transition cursor-pointer disabled:opacity-50"
                                                            title="Edit Khutbah Jumat (Halaman Kegiatan)">
                                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" @click="openEditPrayerDuty({{ Js::from([
                                                            'id' => $duty->id,
                                                            'day_name' => $duty->day_name,
                                                            'prayer_time' => $duty->prayer_time,
                                                            'week_pattern' => $duty->week_pattern,
                                                            'tahun' => $duty->tahun,
                                                            'imam_name' => ($duty->prayer_time === 'ashar' && $kajianOnDate && !empty($kajianOnDate->speaker_name)) ? $kajianOnDate->speaker_name : $duty->imam_name,
                                                            'muadzin_name' => $duty->muadzin_name,
                                                        ]) }})"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 shadow-2xs transition cursor-pointer"
                                                            title="Edit Penugasan">
                                                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                                        </button>
                                                    @endif
                                                        <button type="button"
                                                            @click="openDeleteModal({{ Js::from([
                                                                'action' => 'deletePrayerDuty',
                                                                'id' => $duty->id,
                                                                'title' => 'Hapus Penugasan Shalat',
                                                                'message' => 'Apakah Anda yakin ingin menghapus jadwal penugasan imam & muadzin ini?',
                                                                'itemName' => strtoupper($duty->prayer_time) . ' - ' . $duty->day_name . ' (Imam: ' . ($duty->imam_name ?: '-') . ')',
                                                            ]) }})"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 shadow-2xs transition cursor-pointer disabled:opacity-50"
                                                            title="Hapus Penugasan">
                                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                        </button>
                                                                                </div>
                                            @else
                                                <span class="text-slate-400 italic text-xs">Read-only</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300"></i>
                                                <p>Belum ada jadwal penugasan petugas ibadah yang sesuai filter.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Responsive Cards -->
                    <div class="md:hidden space-y-3">
                        @forelse($allPrayerDuties as $idx => $duty)
                            @php
                                $isToday = ($duty->day_name === $todayDayName);
                                $isFridayDzuhur = ($duty->day_name === 'Jumat' && $duty->prayer_time === 'dzuhur');
                                
                                $dutyTargetWeek = $duty->resolved_week ?? $dutyFilterWeekNum;
                                if (!$dutyTargetWeek) {
                                    if ($duty->matchesWeek($currentWeekNumber)) {
                                        $dutyTargetWeek = $currentWeekNumber;
                                    } elseif (preg_match('/pekan_(\d)/', $duty->week_pattern, $m)) {
                                        $dutyTargetWeek = (int) $m[1];
                                    } elseif ($duty->week_pattern === 'semua') {
                                        $dutyTargetWeek = $currentWeekNumber;
                                    }
                                }

                                $dutyWeek = $duty->resolved_week ?? $dutyTargetWeek;
                                $dutyWeekLabel = $duty->resolved_week_label ?? ($dutyWeek ? ('Pekan ' . $dutyWeek) : $duty->week_pattern_label);

                                $resolvedDate = $duty->resolved_date ?? $duty->resolveDate($dutyFilterYearNum, $dutyFilterMonthNum, $dutyTargetWeek);
                                $dateKey = $resolvedDate ? $resolvedDate->format('Y-m-d') : null;
                                $isDateToday = $resolvedDate ? $resolvedDate->isToday() : false;

                                $fridayKajian = null;
                                if ($isFridayDzuhur) {
                                    $fridayKajian = ($dateKey && isset($fridayKajiansByDate[$dateKey]))
                                        ? $fridayKajiansByDate[$dateKey]
                                        : ($dutyWeek && isset($fridayKajiansByWeek[$dutyWeek]) ? $fridayKajiansByWeek[$dutyWeek] : ($fridayKajians->first() ?? null));
                                }

                                $kajianOnDate = ($duty->prayer_time === 'ashar' && $dateKey && isset($kajianUmumByDate[$dateKey]))
                                    ? $kajianUmumByDate[$dateKey]
                                    : null;

                                $displayImam = $duty->imam_name ?: '-';
                                $displayMuadzin = $duty->muadzin_name ?: '-';
                                $displayMc = null;

                                if ($isFridayDzuhur && $fridayKajian && !empty($fridayKajian->khatib_name)) {
                                    $displayImam = $fridayKajian->khatib_name;
                                    if (!empty($fridayKajian->muadzin_name)) {
                                        $displayMuadzin = $fridayKajian->muadzin_name;
                                    }
                                    $displayMc = $fridayKajian->mc_name ?: null;
                                } elseif ($duty->prayer_time === 'ashar' && $kajianOnDate && !empty($kajianOnDate->speaker_name)) {
                                    $displayImam = $kajianOnDate->speaker_name;
                                    if (!empty($kajianOnDate->muadzin_name)) {
                                        $displayMuadzin = $kajianOnDate->muadzin_name;
                                    }
                                }
                            @endphp
                            <div wire:key="duty-card-{{ $duty->id ?? $idx }}"
                                class="rounded-xl border p-4 shadow-2xs space-y-3 transition {{ $isDateToday ? 'bg-amber-100/40 border-l-[3px] border-amber-300' : 'bg-white border-gov-border' }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="font-bold text-gov-textMain text-sm">
                                            {{ $duty->day_name }} • {{ $duty->prayer_time === 'dzuhur' ? 'Dzuhur' : 'Ashar' }}
                                        </div>
                                    </div>
                                    <div class="flex items-start justify-between gap-2">
                                        <span
                                            class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">
                                            {{ $dutyWeekLabel ?? $duty->week_pattern_label }}
                                        </span>
                                        <span
                                            class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-slate-100 text-slate-700 border-slate-300">
                                            {{ $duty->tahun ?: $dutyFilterYearNum }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Tanggal Penugasan (Mobile) -->
                                <div class="flex items-center justify-between text-xs bg-slate-50/80 px-2.5 py-1.5 rounded-lg border border-slate-200/60">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600 shrink-0"></i>
                                        <span class="text-slate-500 text-[11px] font-medium">Tanggal:</span>
                                        @if($resolvedDate)
                                            <span class="font-bold text-gov-navy">{{ $resolvedDate->translatedFormat('d F Y') }}</span>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">Tidak ada di bln ini</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 space-y-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-gov-navy/10 text-gov-navy flex items-center justify-center font-bold text-[10px] shrink-0"
                                            title="{{ $isFridayDzuhur ? 'Tersinkron Khatib Shalat Jumat' : ($duty->prayer_time === 'ashar' && $kajianOnDate ? 'Tersinkron Narasumber Kajian' : 'Imam Rawatib') }}">
                                            I</div>
                                        <div class="min-w-0 flex-1">
                                            <span class="text-slate-400 text-[11px] mr-1">{{ $isFridayDzuhur ? 'Khatib & Imam:' : ($duty->prayer_time === 'ashar' && $kajianOnDate ? 'Imam (Kajian):' : 'Imam:') }}</span>
                                            <span class="font-bold text-gov-navy">{{ $displayImam }}</span>
                                        </div>
                                    </div>
                                    @if($isFridayDzuhur && $displayMc)
                                        <div class="flex items-center gap-2 pt-1.5 border-t border-slate-200/60">
                                            <div
                                                class="w-6 h-6 rounded-full bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-[9px] shrink-0"
                                                title="MC / Protokol Shalat Jumat">
                                                MC</div>
                                            <div class="min-w-0 flex-1">
                                                <span class="text-slate-400 text-[11px] mr-1">MC:</span>
                                                <span class="font-semibold text-slate-800">{{ $displayMc }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2 pt-1.5 border-t border-slate-200/60">
                                        <div
                                            class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-[10px] shrink-0"
                                            title="{{ $isFridayDzuhur ? 'Bilal / Muadzin Shalat Jumat' : 'Muadzin' }}">
                                            {{ $isFridayDzuhur ? 'B' : 'M' }}</div>
                                        <div class="min-w-0 flex-1">
                                            <span class="text-slate-400 text-[11px] mr-1">{{ $isFridayDzuhur ? 'Bilal / Muadzin:' : 'Muadzin:' }}</span>
                                            <span class="font-semibold text-slate-800">{{ $displayMuadzin }}</span>
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $dutyPhone = null;
                                    if ($isFridayDzuhur && $fridayKajian && !empty($fridayKajian->khatib_phone)) {
                                        $dutyPhone = $fridayKajian->khatib_phone;
                                    } elseif ($duty->prayer_time === 'ashar' && $kajianOnDate && !empty($kajianOnDate->speaker_phone)) {
                                        $dutyPhone = $kajianOnDate->speaker_phone;
                                    }
                                @endphp
                                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                    @if($dutyPhone)
                                        <a href="{{ wa_link($dutyPhone) }}"
                                            target="_blank"
                                            class="inline-flex items-center justify-center text-emerald-600 hover:text-emerald-700 font-semibold text-xs bg-emerald-50 p-1.5 rounded-lg border border-emerald-200 transition shrink-0"
                                            title="WhatsApp">
                                            <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                        </a>
                                    @endif
                                    @if(Auth::user()->canManage())
                                        @if($isFridayDzuhur)
                                            <!-- Tombol Edit Khusus Jumat Dzuhur (Mobile) -->
                                            <button type="button"
                                                wire:click="openFridayKajianFromPetugas({{ $fridayKajian?->id ? $fridayKajian->id : 'null' }})"
                                                wire:loading.attr="disabled"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 shadow-2xs transition cursor-pointer disabled:opacity-50"
                                                title="Edit Khutbah Jumat (Halaman Kegiatan)">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                            </button>
                                        @else
                                            <button type="button" @click="openEditPrayerDuty({{ Js::from([
                                                'id' => $duty->id,
                                                'day_name' => $duty->day_name,
                                                'prayer_time' => $duty->prayer_time,
                                                'week_pattern' => $duty->week_pattern,
                                                'tahun' => $duty->tahun,
                                                'imam_name' => ($duty->prayer_time === 'ashar' && $kajianOnDate && !empty($kajianOnDate->speaker_name)) ? $kajianOnDate->speaker_name : $duty->imam_name,
                                                'muadzin_name' => $duty->muadzin_name,
                                            ]) }})"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 shadow-2xs transition cursor-pointer"
                                                title="Edit">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                            </button>
                                        @endif
                                        <button type="button"
                                            @click="openDeleteModal({{ Js::from([
                                                'action' => 'deletePrayerDuty',
                                                'id' => $duty->id,
                                                'title' => 'Hapus Penugasan Shalat',
                                                'message' => 'Apakah Anda yakin ingin menghapus jadwal penugasan imam & muadzin ini?',
                                                'itemName' => strtoupper($duty->prayer_time) . ' - ' . $duty->day_name . ' (Imam: ' . ($displayImam ?: '-') . ')',
                                            ]) }})"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 shadow-2xs transition cursor-pointer disabled:opacity-50"
                                            title="Hapus">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-xl border border-gov-border p-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i data-lucide="calendar-x" class="w-8 h-8 text-slate-300"></i>
                                    <p>Belum ada jadwal penugasan petugas ibadah yang sesuai filter.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($allPrayerDuties, 'hasPages') && $allPrayerDuties->hasPages())
                        <div class="pt-2">
                            {{ $allPrayerDuties->links(data: ['scrollTo' => false]) }}
                        </div>
                    @endif
                </div>
            </div>
