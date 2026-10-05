<div class="min-h-screen flex flex-col bg-gov-canvas text-gov-textMain">
    
    <!-- Top Sticky Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gov-border shadow-2xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" wire:navigate class="inline-flex items-center justify-center p-2 rounded-lg border border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-600 transition shadow-2xs cursor-pointer" title="Kembali ke Portal">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>

                <div>
                    <h1 class="text-sm sm:text-base font-bold text-gov-textMain leading-tight">Jadwal Waktu Shalat Bulanan</h1>
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
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">JADWAL BULANAN HISAB</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-gov-textMain mt-0.5">
                        {{ $months[$selectedMonth] }} {{ $selectedYear }} — {{ $cityName }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Menampilkan jadwal waktu shalat lengkap untuk wilayah {{ $cityName }} dan sekitarnya.
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
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                
                <!-- Filter Kota / Kabupaten -->
                <div class="relative"
                     wire:ignore
                     x-data="{
                         open: false,
                         search: '',
                         selectedCityId: '{{ $cityId }}',
                         selectedName: '{{ addslashes($cityName) }}',
                         cities: {{ Js::from($cities) }},
                         popularCities: [
                             { id: '1634', lokasi: 'KOTA MALANG' },
                             { id: '1638', lokasi: 'KOTA SURABAYA' },
                             { id: '1301', lokasi: 'KOTA JAKARTA' },
                             { id: '1203', lokasi: 'KOTA BANDUNG' },
                             { id: '1505', lokasi: 'KOTA YOGYAKARTA' }
                         ],
                         init() {
                             this.$watch('$wire.cityId', (val) => {
                                 this.selectedCityId = val;
                                 const match = this.cities.find(c => c.id == val);
                                 if (match) this.selectedName = this.formatCity(match.lokasi);
                             });
                         },
                         get filteredCities() {
                             if (!this.search.trim()) return this.cities;
                             const q = this.search.toLowerCase();
                             return this.cities.filter(c => c.lokasi && c.lokasi.toLowerCase().includes(q));
                         },
                         selectCity(id, lokasi) {
                             this.selectedCityId = id;
                             this.selectedName = this.formatCity(lokasi);
                             this.open = false;
                             this.search = '';
                             $wire.selectCity(id);
                         },
                         formatCity(str) {
                             if (!str) return '';
                             return str.toLowerCase().replace(/(?:^|\s)\S/g, a => a.toUpperCase());
                         }
                     }"
                     @click.outside="open = false"
                     @keydown.escape.window="open = false">

                    <label for="filter-city" class="block text-xs font-bold uppercase text-slate-500 mb-1.5 text-left">
                        Kota / Kabupaten (Kemenag)
                    </label>

                    <!-- Dropdown Trigger Button -->
                    <button type="button" 
                            id="filter-city"
                            @click="open = !open; if (open) $nextTick(() => { $refs.citySearchInput.focus(); if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition cursor-pointer font-medium text-xs text-gov-textMain flex items-center justify-between gap-2 shadow-2xs text-left">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-amber-600 shrink-0 inline-block"></i>
                            <span x-text="selectedName" class="truncate font-medium text-gov-textMain leading-tight">{{ $cityName }}</span>
                        </div>
                        <div class="flex items-center gap-1 shrink-0 text-slate-400">
                            <i data-lucide="search" class="w-3 h-3 text-slate-400 shrink-0 inline-block"></i>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200 shrink-0 inline-block" :class="{ 'rotate-180': open }"></i>
                        </div>
                    </button>

                    <!-- Searchable Dropdown Panel -->
                    <div x-show="open"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                         class="absolute left-0 top-full mt-1.5 w-full sm:w-[360px] bg-white rounded-xl shadow-2xl border border-gov-border z-50 overflow-hidden flex flex-col"
                         style="display: none;">

                        <!-- Search Input Header -->
                        <div class="p-2.5 border-b border-slate-100 bg-slate-50/80 space-y-2">
                            <div class="relative">
                                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 shrink-0 inline-block"></i>
                                <input x-ref="citySearchInput"
                                       x-model="search"
                                       @keydown.enter.prevent="if (filteredCities.length > 0) selectCity(filteredCities[0].id, filteredCities[0].lokasi)"
                                       type="text"
                                       placeholder="Ketik nama kota / kabupaten..."
                                       class="w-full pl-8 pr-7 py-1.5 text-xs bg-white border border-gov-border rounded-lg text-gov-textMain placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition shadow-2xs">
                                <button x-show="search.length > 0"
                                        @click.stop="search = ''; $refs.citySearchInput.focus()"
                                        type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                                    <i data-lucide="x" class="w-3 h-3 shrink-0 inline-block"></i>
                                </button>
                            </div>

                            <!-- Quick Select: Kota Populer -->
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Kota Populer</span>
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="p in popularCities" :key="p.id">
                                        <button type="button"
                                                @click="selectCity(p.id, p.lokasi)"
                                                :class="selectedCityId == p.id ? 'bg-gov-navy text-white font-bold border-gov-navy' : 'bg-white hover:bg-slate-100 text-slate-600 font-medium border-slate-200'"
                                                class="px-2 py-0.5 rounded text-[11px] border transition cursor-pointer shrink-0">
                                            <span x-text="formatCity(p.lokasi)"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Scrollable Cities List (Instant In-Memory Filter) -->
                        <div class="max-h-56 overflow-y-auto p-1.5 space-y-0.5 scrollbar-thin text-xs">
                            <template x-for="city in filteredCities" :key="city.id">
                                <button type="button"
                                        @click="selectCity(city.id, city.lokasi)"
                                        :class="selectedCityId == city.id ? 'bg-blue-50 text-gov-navy font-bold border-blue-200' : 'text-slate-700 hover:bg-slate-50 font-medium border-transparent'"
                                        class="w-full text-left px-3 py-2 rounded-lg border flex items-center justify-between transition cursor-pointer">
                                    <span x-text="formatCity(city.lokasi)"></span>
                                    <span x-show="selectedCityId == city.id" class="inline-flex items-center h-[18px] px-2 rounded-full text-[10px] font-semibold bg-gov-navy text-white leading-none shrink-0">
                                        Pilihan
                                    </span>
                                </button>
                            </template>
                            <div x-show="filteredCities.length === 0" class="py-6 text-center text-slate-400 text-xs" style="display: none;">
                                Kota atau kabupaten "<span x-text="search" class="font-semibold text-slate-600"></span>" tidak ditemukan.
                            </div>
                        </div>

                        <!-- Dropdown Footer -->
                        <div class="px-3 py-2 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span x-text="filteredCities.length + ' dari 518 Kota / Kab'"></span>
                            <button type="button" @click="open = false" class="text-gov-navy font-semibold hover:underline cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>

                    <!-- Hidden native select for accessibility & form inspection -->
                    <select id="filter-city-native" wire:model.live="cityId" class="sr-only" tabindex="-1" aria-hidden="true">
                        @foreach($cities as $city)
                            <option value="{{ $city['id'] }}">{{ ucwords(strtolower($city['lokasi'])) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Bulan -->
                <div>
                    <label for="filter-month" class="block text-xs font-bold uppercase text-slate-500 mb-1.5 text-left">
                        Bulan
                    </label>
                    <div class="relative">
                        <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center" wire:ignore>
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600 shrink-0 inline-block"></i>
                        </div>
                        <select id="filter-month" wire:model.live="selectedMonth"
                            class="w-full pl-8 pr-8 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filter Tahun -->
                <div>
                    <label for="filter-year" class="block text-xs font-bold uppercase text-slate-500 mb-1.5 text-left">
                        Tahun
                    </label>
                    <div class="relative">
                        <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center" wire:ignore>
                            <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-indigo-600 shrink-0 inline-block"></i>
                        </div>
                        <select id="filter-year" wire:model.live="selectedYear"
                            class="w-full pl-8 pr-8 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs">
                            @foreach($years as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

        </div>

        <!-- Monthly Prayer Schedule Table -->
        <div class="bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-300 text-gov-textMuted uppercase font-bold text-[11px] tracking-wider">
                            <th class="p-3.5 pl-4 text-center w-12">No</th>
                            <th class="p-3.5">Hari & Tanggal</th>
                            <th class="p-3.5 text-center">Imsak</th>
                            <th class="p-3.5 text-center">Subuh</th>
                            <th class="p-3.5 text-center">Terbit</th>
                            <th class="p-3.5 text-center">Dhuha</th>
                            <th class="p-3.5 text-center">Dzuhur</th>
                            <th class="p-3.5 text-center">Ashar</th>
                            <th class="p-3.5 text-center">Maghrib</th>
                            <th class="p-3.5 text-center pr-4">Isya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-300 font-medium text-slate-700">
                        @forelse($schedule as $index => $row)
                            @php
                                $isToday = ($row['date'] === $todayDate);
                            @endphp
                            <tr class="transition {{ $isToday ? 'bg-amber-50/90 hover:bg-amber-100/80 border-l-4 border-l-amber-500 font-semibold text-gov-navy' : 'hover:bg-slate-50/80' }}">
                                <td class="p-3 pl-4 text-center {{ $isToday ? 'text-amber-700 font-bold' : 'text-slate-400' }}">
                                    {{ $row['day_number'] }}
                                </td>
                                <td class="p-3">
                                    <div class="space-y-0.5">
                                        <div class="font-bold {{ $isToday ? 'text-gov-navy' : 'text-gov-textMain' }}">{{ $row['day_name'] }}</div>
                                        <div class="text-[11px] text-slate-500 font-normal leading-tight">
                                            {{ $row['formatted_date'] }}
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 text-center tnum text-slate-500">{{ $row['imsak'] }}</td>
                                <td class="p-3 text-center tnum font-bold {{ $isToday ? 'text-gov-navy' : 'text-slate-800' }}">{{ $row['subuh'] }}</td>
                                <td class="p-3 text-center tnum text-slate-500">{{ $row['terbit'] }}</td>
                                <td class="p-3 text-center tnum text-slate-500">{{ $row['dhuha'] }}</td>
                                <td class="p-3 text-center tnum font-bold {{ $isToday ? 'text-gov-navy' : 'text-slate-800' }}">{{ $row['dzuhur'] }}</td>
                                <td class="p-3 text-center tnum font-bold {{ $isToday ? 'text-gov-navy' : 'text-slate-800' }}">{{ $row['ashar'] }}</td>
                                <td class="p-3 text-center tnum font-bold {{ $isToday ? 'text-gov-navy' : 'text-slate-800' }}">{{ $row['maghrib'] }}</td>
                                <td class="p-3 text-center pr-4 tnum font-bold {{ $isToday ? 'text-gov-navy' : 'text-slate-800' }}">{{ $row['isya'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-8 text-center text-slate-400">
                                    Tidak ada data jadwal sholat untuk periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-slate-200 bg-white">
                @forelse($schedule as $index => $row)
                    @php
                        $isToday = ($row['date'] === $todayDate);
                    @endphp
                    <div class="p-4 space-y-2.5 transition {{ $isToday ? 'bg-amber-50/90 border-l-4 border-l-amber-500' : 'bg-white' }}">
                        <!-- Header: Day number, Day name, Date -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ $isToday ? 'bg-amber-200 text-amber-900 border border-amber-300' : 'bg-slate-100 text-gov-navy border border-slate-200' }}">
                                    {{ $row['day_number'] }}
                                </span>
                                <div>
                                    <div class="font-bold text-sm {{ $isToday ? 'text-gov-navy' : 'text-gov-textMain' }}">{{ $row['day_name'] }}</div>
                                    <p class="text-[11px] text-slate-500 font-normal leading-tight">{{ $row['formatted_date'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Prayer times grid: 4 columns x 2 rows -->
                        <div class="grid grid-cols-4 gap-1.5 pt-1.5 border-t border-slate-100 text-center">
                            <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Imsak</span>
                                <span class="text-xs font-semibold text-slate-600 tnum">{{ $row['imsak'] }}</span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-emerald-50/70 border border-emerald-100">
                                <span class="text-[10px] text-emerald-700 block font-bold">Subuh</span>
                                <span class="text-xs font-bold text-emerald-800 tnum">{{ $row['subuh'] }}</span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Terbit</span>
                                <span class="text-xs font-semibold text-slate-600 tnum">{{ $row['terbit'] }}</span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 block font-medium">Dhuha</span>
                                <span class="text-xs font-semibold text-slate-600 tnum">{{ $row['dhuha'] }}</span>
                            </div>

                            <div class="p-1.5 rounded-lg bg-emerald-50/70 border border-emerald-100">
                                <span class="text-[10px] text-emerald-700 block font-bold">Dzuhur</span>
                                <span class="text-xs font-bold text-emerald-800 tnum">{{ $row['dzuhur'] }}</span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-emerald-50/70 border border-emerald-100">
                                <span class="text-[10px] text-emerald-700 block font-bold">Ashar</span>
                                <span class="text-xs font-bold text-emerald-800 tnum">{{ $row['ashar'] }}</span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-emerald-50/70 border border-emerald-100">
                                <span class="text-[10px] text-emerald-700 block font-bold">Maghrib</span>
                                <span class="text-xs font-bold text-emerald-800 tnum">{{ $row['maghrib'] }}</span>
                            </div>
                            <div class="p-1.5 rounded-lg bg-emerald-50/70 border border-emerald-100">
                                <span class="text-[10px] text-emerald-700 block font-bold">Isya</span>
                                <span class="text-xs font-bold text-emerald-800 tnum">{{ $row['isya'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        Tidak ada data jadwal sholat untuk periode yang dipilih.
                    </div>
                @endforelse
            </div>

            <!-- Table Footer Legend -->
            <div class="p-4 bg-slate-50/70 border-t border-slate-200 text-xs text-slate-500 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-amber-200 border border-amber-300 inline-block shrink-0"></span>
                    <span>Baris kuning paling atas menandakan waktu shalat hari ini.</span>
                </div>
                <div class="text-[11px] text-slate-400">
                    Sumber data hisab terintegrasi Bimas Islam Kementerian Agama RI.
                </div>
            </div>
        </div>

    </main>

</div>
