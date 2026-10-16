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
                        @php
                            $dutyWeekOptions = [
                                ['value' => 'all', 'label' => 'Semua Pekan'],
                                ['value' => '1', 'label' => 'Pekan 1' . ($currentWeekNumber === 1 ? ' ⭐' : '')],
                                ['value' => '2', 'label' => 'Pekan 2' . ($currentWeekNumber === 2 ? ' ⭐' : '')],
                                ['value' => '3', 'label' => 'Pekan 3' . ($currentWeekNumber === 3 ? ' ⭐' : '')],
                                ['value' => '4', 'label' => 'Pekan 4' . ($currentWeekNumber === 4 ? ' ⭐' : '')],
                                ['value' => '5', 'label' => 'Pekan 5' . ($currentWeekNumber === 5 ? ' ⭐' : '')],
                            ];
                            $dutyMonthOptions = [];
                            foreach($dutyMonths as $mNum => $mName) {
                                $isThisMonth = (int) $mNum === (int) Carbon\Carbon::now('Asia/Jakarta')->month;
                                $dutyMonthOptions[] = [
                                    'value' => (string)$mNum, 
                                    'label' => $mName . ($isThisMonth ? ' ⭐' : ''),
                                ];
                            }
                            $dutyYearOptions = [['value' => 'all', 'label' => 'Semua Tahun']];
                            foreach($availableDutyYears as $yr) {
                                $isThisYear = (string) $yr === (string) Carbon\Carbon::now('Asia/Jakarta')->year;
                                $dutyYearOptions[] = [
                                    'value' => (string)$yr, 
                                    'label' => $yr . ($isThisYear ? ' (Tahun Ini)' : ''),
                                ];
                            }
                        @endphp

                        <x-custom-select 
                            wire:model.live="prayerDutyFilterWeek" 
                            :options="$dutyWeekOptions"
                            placeholder="Semua Pekan"
                            class="w-full sm:w-36"
                        />

                        <span class="sr-only">BULAN:</span>
                        <x-custom-select 
                            wire:model.live="prayerDutyFilterMonth" 
                            :options="$dutyMonthOptions"
                            placeholder="Pilih Bulan"
                            icon="calendar"
                            class="w-full sm:w-36"
                        />

                        <span class="sr-only">TAHUN:</span>
                        <x-custom-select 
                            wire:model.live="prayerDutyFilterYear" 
                            :options="$dutyYearOptions"
                            placeholder="Semua Tahun"
                            class="col-span-2 sm:col-span-1 w-full sm:w-32"
                        />
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
                                    class="bg-slate-50/80 border-b border-slate-300 text-gov-textMuted uppercase font-bold text-[11px] tracking-wider">
                                    <th wire:click="sortBy('date', 'petugas')" class="p-3.5 pl-4 text-center w-12 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center justify-center gap-1">
                                            <span>Tgl</span>
                                            <x-sort-icon field="date" table="petugas" />
                                        </div>
                                    </th>
                                    <th wire:click="sortBy('day_name', 'petugas')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Hari & Tanggal</span>
                                            <x-sort-icon field="day_name" table="petugas" />
                                        </div>
                                    </th>
                                    <th class="p-3.5 text-center">Pola Pekan</th>
                                    <th wire:click="sortBy('dzuhur', 'petugas')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                        <div class="flex items-center gap-1">
                                            <span>Dzuhur</span>
                                            <x-sort-icon field="dzuhur" table="petugas" />
                                        </div>
                                    </th>
                                    <th class="p-3.5">Ashar</th>
                                    <th class="p-3.5 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 font-medium text-slate-700 transition-opacity duration-150">
                                @forelse($allPrayerDuties as $idx => $row)
                                    @php
                                        $isToday = $row['is_today'];
                                        $isWeekend = $row['is_weekend'];
                                        $isFriday = $row['is_friday'];
                                        $dateStr = $row['date'];
                                    @endphp
                                    <tr wire:key="duty-day-row-{{ $dateStr }}"
                                        class="transition {{ $isToday ? 'bg-amber-50/90 hover:bg-amber-100/80 border-l-4 border-l-amber-500 font-semibold' : ($isWeekend ? 'bg-slate-50/40 text-slate-400' : 'hover:bg-slate-50/80') }}">
                                        
                                        <!-- 1. TGL -->
                                        <td class="p-3 pl-4 text-center font-bold {{ $isToday ? 'text-amber-700' : ($isWeekend ? 'text-slate-400' : 'text-slate-600') }}">
                                            {{ $row['day_number'] }}
                                        </td>

                                        <!-- 2. HARI & TANGGAL -->
                                        <td class="p-3">
                                            <div class="space-y-0.5">
                                                <div class="font-bold {{ $isToday ? 'text-gov-navy' : ($isWeekend ? 'text-slate-500' : 'text-gov-textMain') }}">
                                                    {{ $row['day_name'] }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-normal leading-tight">
                                                    {{ $row['formatted_date'] }}
                                                </div>
                                            </div>
                                        </td>

                                        <!-- 3. POLA PEKAN -->
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

                                        <!-- 4. DZUHUR -->
                                        <td class="p-3">
                                            @if($isWeekend)
                                                <span class="text-slate-400 italic text-[11px]">-</span>
                                            @elseif($isFriday)
                                                <div class="space-y-0.5">
                                                    <div>
                                                        <span>Khatib:</span>
                                                        <span class="font-semibold text-gov-textMain ml-1" title="Tersinkron Khatib Shalat Jumat">{{ $row['friday_khatib'] }}</span>
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
                                                        <span>Muadzin:</span>
                                                        <span class="font-semibold text-gov-textMain ml-1">{{ $row['dzuhur_muadzin'] }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        <!-- 5. SHALAT ASHAR -->
                                        <td class="p-3">
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
                                                        <span>Muadzin:</span>
                                                        <span class="font-semibold text-gov-textMain ml-1">{{ $row['ashar_muadzin'] }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        <!-- 6. AKSI -->
                                        <td class="p-3 text-center whitespace-nowrap">
                                            @if(Auth::user()->canManage())
                                                @if($isWeekend)
                                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                                @else
                                                    @php
                                                        $hasDzuhurDuty = (bool) $row['dzuhur_duty'];
                                                        $hasAsharDuty = (bool) $row['ashar_duty'];
                                                        $hasAnyDuty = $hasDzuhurDuty || $hasAsharDuty || ($isFriday && $row['friday_kajian']);

                                                        $deleteIds = [];
                                                        if ($hasDzuhurDuty) {
                                                            $deleteIds[] = $row['dzuhur_duty']->id;
                                                        }
                                                        if ($hasAsharDuty) {
                                                            $deleteIds[] = $row['ashar_duty']->id;
                                                        }

                                                        $deleteItemName = $row['day_name'] . ', ' . $row['formatted_date'];
                                                        $deleteMessage = 'Apakah Anda yakin ingin menghapus jadwal penugasan petugas ibadah hari ' . $row['day_name'] . ' ini' . (($hasDzuhurDuty && $hasAsharDuty) ? ' (Dzuhur & Ashar)?' : '?');
                                                    @endphp

                                                    @if($hasAnyDuty)
                                                        <div class="inline-flex items-center justify-center gap-1.5">
                                                            <button type="button"
                                                                @click="openEditPrayerDutyRow({{ Js::from([
                                                                    'day_name' => $row['day_name'],
                                                                    'week_pattern' => $row['dzuhur_duty']?->week_pattern ?? $row['ashar_duty']?->week_pattern ?? ('pekan_' . $row['week_number']),
                                                                    'tahun' => $row['dzuhur_duty']?->tahun ?? $row['ashar_duty']?->tahun ?? ((int) Carbon\Carbon::parse($row['date'])->year),
                                                                    'dzuhur' => $row['dzuhur_duty'] ? [
                                                                        'id' => $row['dzuhur_duty']->id,
                                                                        'imam_name' => $row['dzuhur_duty']->imam_name,
                                                                        'muadzin_name' => $row['dzuhur_duty']->muadzin_name,
                                                                        'week_pattern' => $row['dzuhur_duty']->week_pattern,
                                                                        'tahun' => $row['dzuhur_duty']->tahun,
                                                                    ] : null,
                                                                    'ashar' => $row['ashar_duty'] ? [
                                                                        'id' => $row['ashar_duty']->id,
                                                                        'imam_name' => (!empty($row['ashar_kajian_title']) && !empty($row['ashar_imam']) && $row['ashar_imam'] !== '-') ? $row['ashar_imam'] : $row['ashar_duty']->imam_name,
                                                                        'muadzin_name' => $row['ashar_duty']->muadzin_name,
                                                                        'week_pattern' => $row['ashar_duty']->week_pattern,
                                                                        'tahun' => $row['ashar_duty']->tahun,
                                                                    ] : null,
                                                                    'is_friday' => $isFriday,
                                                                    'friday_kajian_id' => $row['friday_kajian']?->id,
                                                                ]) }})"
                                                                class="inline-flex items-center justify-center p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 shadow-2xs transition cursor-pointer"
                                                                title="Edit Penugasan Petugas">
                                                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                                            </button>

                                                            @if(!empty($deleteIds))
                                                                <button type="button"
                                                                    @click="openDeleteModal({{ Js::from([
                                                                        'action' => 'deletePrayerDuty',
                                                                        'id' => count($deleteIds) === 1 ? $deleteIds[0] : $deleteIds,
                                                                        'title' => 'Hapus Penugasan Petugas',
                                                                        'message' => $deleteMessage,
                                                                        'itemName' => $deleteItemName,
                                                                    ]) }})"
                                                                    class="inline-flex items-center justify-center p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 shadow-2xs transition cursor-pointer disabled:opacity-50"
                                                                    title="Hapus Penugasan Petugas">
                                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <button type="button"
                                                            @click="openCreatePrayerDuty('{{ $row['day_name'] }}', 'dzuhur')"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs transition cursor-pointer"
                                                            title="Tambah Petugas">
                                                            <i data-lucide="plus" class="w-3.5 h-3.5 text-slate-500"></i>
                                                        </button>
                                                    @endif
                                                @endif
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
                        @forelse($allPrayerDuties as $idx => $row)
                            @php
                                $isToday = $row['is_today'];
                                $isWeekend = $row['is_weekend'];
                                $isFriday = $row['is_friday'];
                                $dateStr = $row['date'];
                            @endphp
                            <div wire:key="duty-card-{{ $dateStr }}"
                                class="rounded-xl border p-4 shadow-2xs space-y-3 transition {{ $isToday ? 'bg-amber-50/90 border-l-4 border-l-amber-500 font-semibold' : ($isWeekend ? 'bg-slate-50/50 border-gov-border text-slate-400' : 'bg-white border-gov-border') }}">
                                
                                <!-- Header: Hari, Tanggal, dan Pola Pekan -->
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="font-bold text-sm {{ $isToday ? 'text-gov-navy' : ($isWeekend ? 'text-slate-500' : 'text-gov-textMain') }}">
                                            {{ $row['day_name'] }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-normal">
                                            {{ $row['formatted_date'] }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        @if($isWeekend)
                                            <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-slate-100 text-slate-500 border-slate-200">
                                                Libur
                                            </span>
                                        @else
                                            @if($isFriday)
                                                <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-emerald-50 text-emerald-700 border-emerald-400">
                                                    Pekan {{ $row['week_number'] }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">
                                                    Pekan {{ $row['week_number'] }}
                                                </span>
                                            @endif

                                            @if(Auth::user()->canManage())
                                                @php
                                                    $mHasDzuhurDuty = (bool) $row['dzuhur_duty'];
                                                    $mHasAsharDuty = (bool) $row['ashar_duty'];
                                                    $mHasAnyDuty = $mHasDzuhurDuty || $mHasAsharDuty || ($isFriday && $row['friday_kajian']);

                                                    $mDeleteIds = [];
                                                    if ($mHasDzuhurDuty) {
                                                        $mDeleteIds[] = $row['dzuhur_duty']->id;
                                                    }
                                                    if ($mHasAsharDuty) {
                                                        $mDeleteIds[] = $row['ashar_duty']->id;
                                                    }

                                                    $mDeleteItemName = $row['day_name'] . ', ' . $row['formatted_date'];
                                                    $mDeleteMessage = 'Apakah Anda yakin ingin menghapus jadwal penugasan petugas ibadah hari ' . $row['day_name'] . ' ini' . (($mHasDzuhurDuty && $mHasAsharDuty) ? ' (Dzuhur & Ashar)?' : '?');
                                                @endphp

                                                @if($mHasAnyDuty)
                                                    <button type="button"
                                                        @click="openEditPrayerDutyRow({{ Js::from([
                                                            'day_name' => $row['day_name'],
                                                            'week_pattern' => $row['dzuhur_duty']?->week_pattern ?? $row['ashar_duty']?->week_pattern ?? ('pekan_' . $row['week_number']),
                                                            'tahun' => $row['dzuhur_duty']?->tahun ?? $row['ashar_duty']?->tahun ?? ((int) Carbon\Carbon::parse($row['date'])->year),
                                                            'dzuhur' => $row['dzuhur_duty'] ? [
                                                                'id' => $row['dzuhur_duty']->id,
                                                                'imam_name' => $row['dzuhur_duty']->imam_name,
                                                                'muadzin_name' => $row['dzuhur_duty']->muadzin_name,
                                                                'week_pattern' => $row['dzuhur_duty']->week_pattern,
                                                                'tahun' => $row['dzuhur_duty']->tahun,
                                                            ] : null,
                                                            'ashar' => $row['ashar_duty'] ? [
                                                                'id' => $row['ashar_duty']->id,
                                                                'imam_name' => (!empty($row['ashar_kajian_title']) && !empty($row['ashar_imam']) && $row['ashar_imam'] !== '-') ? $row['ashar_imam'] : $row['ashar_duty']->imam_name,
                                                                'muadzin_name' => $row['ashar_duty']->muadzin_name,
                                                                'week_pattern' => $row['ashar_duty']->week_pattern,
                                                                'tahun' => $row['ashar_duty']->tahun,
                                                            ] : null,
                                                            'is_friday' => $isFriday,
                                                            'friday_kajian_id' => $row['friday_kajian']?->id,
                                                        ]) }})"
                                                        class="inline-flex items-center justify-center p-1 rounded-md bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 shadow-2xs transition cursor-pointer"
                                                        title="Edit Penugasan Petugas">
                                                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                                    </button>

                                                    @if(!empty($mDeleteIds))
                                                        <button type="button"
                                                            @click="openDeleteModal({{ Js::from([
                                                                'action' => 'deletePrayerDuty',
                                                                'id' => count($mDeleteIds) === 1 ? $mDeleteIds[0] : $mDeleteIds,
                                                                'title' => 'Hapus Penugasan Petugas',
                                                                'message' => $mDeleteMessage,
                                                                'itemName' => $mDeleteItemName,
                                                            ]) }})"
                                                            class="inline-flex items-center justify-center p-1 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 shadow-2xs transition cursor-pointer disabled:opacity-50"
                                                            title="Hapus Penugasan Petugas">
                                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    <button type="button"
                                                        @click="openCreatePrayerDuty('{{ $row['day_name'] }}', 'dzuhur')"
                                                        class="inline-flex items-center justify-center p-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-300 shadow-2xs transition cursor-pointer"
                                                        title="Tambah Petugas">
                                                        <i data-lucide="plus" class="w-3.5 h-3.5 text-slate-500"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                @if($isWeekend)
                                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/60 text-center text-xs text-slate-400 italic">
                                        Libur Operasional Kantor
                                    </div>
                                @else
                                    <!-- Detail Petugas Dzuhur & Ashar -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                        <!-- Kolom Dzuhur -->
                                        <div class="p-2.5 rounded-lg bg-slate-50/80 border border-slate-200/70 space-y-1.5">
                                            <div class="pb-1 border-b border-slate-200/60">
                                                <span class="font-bold text-gov-navy text-[11px]">
                                                    {{ $isFriday ? 'SHALAT JUMAT' : 'DZUHUR' }}
                                                </span>

                                            </div>
                                            @if($isFriday)
                                                <div class="space-y-0.5 text-[11px]">
                                                    <div><span class="text-slate-400">Khatib:</span> <span class="font-semibold text-slate-800" title="Tersinkron Khatib Shalat Jumat">{{ $row['friday_khatib'] }}</span></div>
                                                    <div><span class="text-slate-400">Muadzin:</span> <span class="font-semibold text-slate-800">{{ $row['friday_muadzin'] }}</span></div>
                                                    @if($row['friday_mc'] !== '-')
                                                        <div><span class="text-slate-400">MC:</span> <span class="font-semibold text-slate-800">{{ $row['friday_mc'] }}</span></div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="space-y-0.5 text-[11px]">
                                                    <div><span class="text-slate-400">Imam:</span> <span class="font-semibold text-slate-800">{{ $row['dzuhur_imam'] }}</span></div>
                                                    <div><span class="text-slate-400">Muadzin:</span> <span class="font-semibold text-slate-800">{{ $row['dzuhur_muadzin'] }}</span></div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Kolom Ashar -->
                                        <div class="p-2.5 rounded-lg bg-slate-50/80 border border-slate-200/70 space-y-1.5">
                                            <div class="pb-1 border-b border-slate-200/60">
                                                <span class="font-bold text-gov-navy text-[11px]">ASHAR</span>
                                            </div>
                                            <div class="space-y-0.5 text-[11px]">
                                                <div>
                                                    <span class="text-slate-400">Imam:</span>
                                                    <span class="font-semibold text-slate-800">{{ $row['ashar_imam'] }}</span>
                                                    @if(!empty($row['ashar_kajian_title']))
                                                        <span class="text-[10px] text-emerald-700 font-normal ml-0.5">({{ $row['ashar_kajian_title'] }})</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="text-slate-400">Muadzin:</span>
                                                    <span class="font-semibold text-slate-800">{{ $row['ashar_muadzin'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
