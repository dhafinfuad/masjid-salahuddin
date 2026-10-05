            <!-- ======================================================== -->
            <!-- TAB: PROGRAM SOSIAL & KEIKUTSERTAAN (MILESTONE 6) -->
            <!-- ======================================================== -->
            <div class="space-y-6">
                <!-- Top Header Card -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gov-border shadow-2xs">
                    <div class="flex items-center space-x-2.5">
                        <div class="hidden sm:flex w-8 h-8 rounded-lg bg-amber-50 text-amber-700 items-center justify-center border border-amber-200 shrink-0">
                            <i data-lucide="heart-handshake" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gov-textMain">Program Sosial</h3>
                            <p class="text-xs text-slate-500">Realisasi dana, komitmen sukarela, dan basis data peserta</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto">
                        @if(Auth::user()->canManage())
                            <button type="button" @click="showImportPotonganModal = true; $wire.call('openImportPotonganModal'); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                                class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span>Import</span>
                            </button>
                            <button type="button" @click="showExportPotonganModal = true; $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                                class="w-full sm:w-auto justify-center px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="download" class="w-3.5 h-3.5 text-blue-600"></i>
                                <span>Export</span>
                            </button>
                        @endif
                        <button type="button" @click="openCreateParticipant()"
                            class="{{ !Auth::user()->canManage() ? 'col-span-2 sm:col-span-1' : '' }} w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer">
                            <i data-lucide="user-plus" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>Peserta</span>
                        </button>
                        @if(Auth::user()->canManage())
                            <button type="button" @click="openCreateSocialProgram()"
                                class="w-full sm:w-auto justify-center inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer">
                                <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
                                <span>Program</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Sub-tab Navigation Bar (Strict Mandate Class) -->
                <div class="text-xs flex flex-col sm:flex-row sm:items-center justify-start gap-3 bg-white p-3 rounded-xl border border-gov-border shadow-2xs">
                    <button type="button" @click="socialSubTab = 'katalog'; $wire.set('socialSubTab', 'katalog', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                        class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2"
                        :class="socialSubTab === 'katalog' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'">
                        <i data-lucide="heart-handshake" class="w-4 h-4"></i>
                        <span>Katalog Program Sosial</span>
                    </button>

                    @if(Auth::user()->canManage())
                        <button type="button" @click="socialSubTab = 'peserta'; $wire.set('socialSubTab', 'peserta', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2"
                            :class="socialSubTab === 'peserta' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            <span>Rekapitulasi Peserta</span>
                        </button>

                        <button type="button" @click="socialSubTab = 'setoran'; $wire.set('socialSubTab', 'setoran', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2"
                            :class="socialSubTab === 'setoran' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'">
                            <i data-lucide="landmark" class="w-4 h-4"></i>
                            <span>Setoran Kantor</span>
                        </button>
                    @endif
                </div>

                <!-- SUB-VIEW 1: KATALOG PROGRAM SOSIAL -->
                <div x-show="socialSubTab === 'katalog'" x-cloak class="space-y-6">
                    <!-- Metric Cards (3 Cards / 2 Cards for Jamaah) -->
                    <div class="grid grid-cols-1 {{ Auth::user()->canManage() ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }} gap-4">
                        <!-- Realisasi Dana Terhimpun -->
                        <div class="bg-white p-4 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Dana Terhimpun</p>
                                <h4 class="text-lg font-bold text-emerald-700 mt-1">Rp {{ number_format($totalSocialCollectedAll, 0, ',', '.') }}</h4>
                                <p class="text-[11px] text-emerald-600 font-medium mt-0.5">Januari - {{ now()->translatedFormat('F Y') }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200">
                                <i data-lucide="wallet" class="w-5 h-5"></i>
                            </div>
                        </div>

                        <!-- Total Peserta Aktif -->
                        @if(Auth::user()->canManage())
                            <div class="bg-white p-4 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Peserta Terdaftar</p>
                                    <h4 class="text-lg font-bold text-gov-textMain mt-1">{{ $totalActiveSocialParticipants }} Peserta</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">per {{ now()->translatedFormat('F Y') }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200">
                                    <i data-lucide="users" class="w-5 h-5"></i>
                                </div>
                            </div>
                        @endif

                        <!-- Komitmen Rutin/Bulan -->
                        <div class="bg-white p-4 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Potongan</p>
                                <h4 class="text-lg font-bold text-amber-700 mt-1">Rp {{ number_format($totalSocialCommitment, 0, ',', '.') }}</h4>
                                <p class="text-[11px] text-amber-600 font-medium mt-0.5">per {{ now()->translatedFormat('F Y') }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200">
                                <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Program Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @forelse($allSocialPrograms as $prog)
                            @php
                                $iconName = $prog->icon ?: 'heart-handshake';
                            @endphp
                            <div wire:key="socprog-card-{{ $prog->id }}" class="bg-white rounded-xl border border-gov-border shadow-2xs p-5 flex flex-col justify-between hover:shadow-md transition">
                                <div>
                                    <!-- Header Card -->
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-gov-navy flex items-center justify-center border border-gov-border shrink-0">
                                                <i data-lucide="{{ $iconName }}" class="w-5 h-5 text-gov-navy"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-gov-textMain">{{ $prog->name }}</h4>
                                                <span class="inline-flex items-center text-[11px] font-medium text-slate-500 capitalize">
                                                    Kategori: {{ $prog->category }} • {{ ucfirst($prog->period_type) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            @if($prog->status === 'AKTIF')
                                                <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-emerald-50 text-emerald-700 border-emerald-200">Aktif</span>
                                            @else
                                                <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-slate-100 text-slate-600 border-slate-200">{{ ucfirst(strtolower($prog->status)) }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                                        {{ $prog->description ?: 'Belum ada deskripsi untuk program sosial ini.' }}
                                    </p>

                                    <!-- Realisasi Dana -->
                                    <div class="bg-slate-50 p-3 rounded-lg border border-gov-border mb-4 flex items-center justify-between text-xs">
                                        <span class="text-slate-500 font-medium">Realisasi Dana:</span>
                                        <span class="font-bold text-gov-textMain">
                                            Rp {{ number_format($prog->total_collected, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <!-- Stats Highlights -->
                                    <div class="grid {{ Auth::user()->canManage() ? 'grid-cols-2' : 'grid-cols-1' }} gap-2 text-xs mb-4">
                                        @if(Auth::user()->canManage())
                                            <div class="p-2 rounded-lg bg-slate-50/50 border border-slate-100">
                                                <span class="text-slate-500 block text-[11px]">Peserta Terdaftar</span>
                                                <span class="font-bold text-gov-textMain text-sm">{{ $prog->active_participants_count }}</span>
                                                <span class="text-[11px] text-slate-400"> pegawai</span>
                                            </div>
                                        @endif
                                        <div class="p-2 rounded-lg bg-slate-50/50 border border-slate-100">
                                            <span class="text-slate-500 block text-[11px]">Potongan Bulan Ini</span>
                                            <span class="font-bold text-gov-textMain text-sm">Rp {{ number_format($prog->monthly_commitment_total, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Actions -->
                                @if(Auth::user()->canManage())
                                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-2">
                                        <button type="button"
                                            @click="socialSubTab = 'peserta'; $wire.set('socialSubTab', 'peserta', false); $wire.set('socialProgramFilter', '{{ $prog->id }}'); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                                            class="px-3 py-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                            <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>Lihat Peserta ({{ $prog->active_participants_count }})</span>
                                        </button>

                                        <div class="flex items-center space-x-1.5">
                                            <button type="button" @click="openEditSocialProgram({{ $prog->id }})"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-600 shadow-2xs transition cursor-pointer"
                                                title="Edit Program Sosial">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <button type="button"
                                                @click="openDeleteModal({{ Js::from([
                                                    'title' => 'Hapus Program Sosial',
                                                    'message' => 'Apakah Anda yakin ingin menghapus program sosial \'' . $prog->name . '\'? Seluruh data terkait program ini akan terdampak.',
                                                    'itemName' => $prog->name,
                                                    'action' => 'deleteSocialProgram',
                                                    'id' => $prog->id,
                                                ]) }})"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 shadow-2xs transition cursor-pointer"
                                                title="Hapus Program">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full p-12 bg-white rounded-xl border border-gov-border text-center text-slate-400">
                                <i data-lucide="heart-handshake" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-medium text-slate-600 text-sm">Belum ada program sosial yang terdaftar.</p>
                                <p class="text-xs text-slate-400 mt-1">Klik tombol 'Tambah Program Baru' untuk membuat program sosial pertama.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if(Auth::user()->canManage())
                    <!-- SUB-VIEW 2: REKAPITULASI PESERTA -->
                    <div x-show="socialSubTab === 'peserta'" x-cloak class="space-y-6">
                    <!-- Filter & Search Bar (Complies with Rule 1 for select font-medium) -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-gov-border text-xs shadow-2xs">
                        <div class="grid grid-cols-2 gap-2 w-full sm:flex sm:items-center sm:w-auto">
                            <div class="w-full sm:w-56">
                                <select wire:model.live="socialProgramFilter"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium text-gov-textMain cursor-pointer focus:outline-none focus:border-gov-navy truncate">
                                    <option value="all">Semua Program Sosial</option>
                                    @foreach($allSocialPrograms as $sp)
                                        <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative w-full sm:w-56" x-data="{
                                open: false
                            }">
                                <button type="button" @click="open = !open"
                                    class="w-full px-2 sm:px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium text-gov-textMain cursor-pointer flex items-center justify-between gap-1 shadow-2xs hover:bg-slate-50 transition focus:outline-none focus:border-gov-navy select-none"
                                    title="Pilih satu atau beberapa periode">
                                    <div class="flex items-center gap-1 min-w-0">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                        @php
                                            $activePeriodsList = is_array($socialParticipantPeriodFilter) 
                                                ? array_values(array_filter($socialParticipantPeriodFilter, fn($p) => $p !== 'all' && !empty($p)))
                                                : ($socialParticipantPeriodFilter !== 'all' && !empty($socialParticipantPeriodFilter) ? [$socialParticipantPeriodFilter] : []);
                                            $activeCount = count($activePeriodsList);
                                        @endphp
                                        @if($activeCount === 0)
                                            <span class="text-gov-textMain font-medium truncate">Semua Periode</span>
                                        @elseif($activeCount === 1)
                                            <span class="text-gov-navy font-bold truncate">
                                                {{ $activePeriodsList[0] }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 font-bold text-gov-navy truncate">
                                                <span>{{ $activeCount }} Periode</span>
                                            </span>
                                        @endif
                                    </div>
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                <!-- Dropdown Checklist Panel -->
                                <div x-show="open" @click.outside="open = false" x-cloak
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 sm:right-auto sm:left-0 mt-1.5 w-64 max-w-[calc(100vw-3rem)] bg-white rounded-xl shadow-xl border border-gov-border p-2.5 z-40 space-y-2 text-xs">
                                    
                                    <!-- Header Actions -->
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 px-1">
                                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pilih Periode</span>
                                        <div class="flex items-center gap-2">
                                            <button type="button" 
                                                wire:click="toggleAllParticipantPeriods"
                                                class="text-[11px] font-bold text-gov-navy hover:underline cursor-pointer">
                                                <span>{{ (count($availablePeriods) > 0 && $activeCount === count($availablePeriods)) ? 'Batal Semua' : 'Pilih Semua' }}</span>
                                            </button>
                                            <span class="text-slate-200">|</span>
                                            <button type="button" 
                                                wire:click="resetParticipantPeriods"
                                                class="text-[11px] font-medium text-rose-600 hover:underline cursor-pointer">
                                                Reset
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Checklist Items -->
                                    <div class="max-h-56 overflow-y-auto space-y-0.5 pr-1">
                                        @foreach($availablePeriods as $per)
                                            @php
                                                $isCurrentMonth = $per === ('Periode ' . now()->month . '/' . now()->year);
                                            @endphp
                                            <label class="flex items-center justify-between gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-50 transition cursor-pointer select-none group">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <input type="checkbox" wire:model.live="socialParticipantPeriodFilter" value="{{ $per }}"
                                                        class="w-4 h-4 rounded border-gov-border text-gov-navy focus:ring-gov-navy focus:ring-offset-0 cursor-pointer">
                                                    <span class="text-xs font-medium text-gov-textMain group-hover:text-gov-navy truncate">
                                                        {{ $per }}
                                                    </span>
                                                </div>
                                                @if($isCurrentMonth)
                                                    <span class="inline-flex items-center h-[18px] px-1.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                                        Bulan Ini
                                                    </span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>

                                    <!-- Footer Info -->
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between px-1 text-[11px] text-slate-400">
                                        <span>{{ count($availablePeriods) }} total periode</span>
                                        <button type="button" @click="open = false" class="text-xs font-bold text-gov-navy hover:underline cursor-pointer">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative w-full sm:w-64" x-data="{
                            clearSearch() {
                                $wire.clearSocialParticipantSearch();
                            }
                        }">
                            <i data-lucide="search"
                                class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input wire:model.live.debounce.300ms="socialParticipantSearch" type="text"
                                placeholder="Cari nama peserta..."
                                class="w-full pl-8 pr-9 py-1.5 rounded-lg border border-gov-border bg-white text-xs text-gov-textMain focus:outline-none focus:border-gov-navy font-medium">
                            <button type="button" @click="clearSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 transition cursor-pointer flex items-center justify-center" title="Reset pencarian ke data seharusnya">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50/80 text-slate-600 font-semibold border-b border-slate-300">
                                    <tr>
                                        <th wire:click="sortBy('name', 'social_participants')" class="py-3 px-4 text-left cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Nama Peserta</span>
                                                <x-sort-icon field="name" table="social_participants" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('program_name', 'social_participants')" class="py-3 px-4 text-left cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Program Sosial</span>
                                                <x-sort-icon field="program_name" table="social_participants" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('monthly_amount', 'social_participants')" class="py-3 px-4 text-right cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center justify-end gap-1">
                                                <span>Komitmen / Bulan</span>
                                                <x-sort-icon field="monthly_amount" table="social_participants" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('period', 'social_participants')" class="py-3 px-4 text-center cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center justify-center gap-1">
                                                <span>Periode</span>
                                                <x-sort-icon field="period" table="social_participants" />
                                            </div>
                                        </th>
                                        <th class="py-3 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 transition-opacity duration-150">
                                    @forelse($socialParticipantsList as $p)
                                        <tr wire:key="socpart-row-{{ $p->id }}" class="hover:bg-slate-50/50 transition">
                                            <!-- Nama Peserta -->
                                            <td class="py-3 px-4 text-left">
                                                <div class="font-bold text-gov-textMain text-xs">{{ $p->name }}</div>
                                            </td>

                                            <!-- Program Sosial -->
                                            <td class="py-3 px-4 text-left">
                                                <span class="font-semibold text-gov-textMain">{{ $p->socialProgram->name ?? $p->program_name }}</span>
                                            </td>

                                            <!-- Komitmen / Bulan -->
                                            <td class="py-3 px-4 text-right font-mono font-bold text-gov-navy">
                                                Rp {{ number_format($p->monthly_amount, 0, ',', '.') }}
                                            </td>

                                            <!-- Periode -->
                                            <td class="py-3 px-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $p->period === ('Periode ' . now()->month . '/' . now()->year) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold' : 'bg-slate-100 text-slate-700' }}">
                                                    {{ $p->period ?: 'Bulanan' }}
                                                </span>
                                            </td>

                                            <!-- Aksi (Rule 3) -->
                                            <td class="py-3 px-4 text-center">
                                                <div class="flex items-center justify-center space-x-1.5">
                                                    @php
                                                        $canEditThisParticipant = Auth::user()->canManage() || (Auth::user()->isJamaah() && (
                                                            (strtolower(trim($p->name)) === strtolower(trim(Auth::user()->name)))
                                                        ));
                                                    @endphp
                                                    @if($canEditThisParticipant)
                                                        <button type="button"
                                                            @click="openEditParticipant({{ Js::from([
                                                                'id' => $p->id,
                                                                'name' => $p->name,
                                                                'program_name' => $p->program_name,
                                                                'monthly_amount' => (int) $p->monthly_amount,
                                                                'period' => $p->period ?? '',
                                                            ]) }})"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-600 shadow-2xs transition cursor-pointer"
                                                            title="Edit Data Peserta">
                                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    @endif

                                                    @if(Auth::user()->canManage())
                                                        <button type="button"
                                                            @click="openDeleteModal({{ Js::from([
                                                                'title' => 'Hapus Peserta Program',
                                                                'message' => 'Apakah Anda yakin ingin menghapus data peserta \'' . $p->name . '\' dari program ini?',
                                                                'itemName' => $p->name,
                                                                'action' => 'deleteParticipant',
                                                                'id' => $p->id,
                                                            ]) }})"
                                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 shadow-2xs transition cursor-pointer"
                                                            title="Hapus Peserta">
                                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-8 text-center text-slate-400">
                                                <div class="flex flex-col items-center justify-center py-4">
                                                    <i data-lucide="users" class="w-8 h-8 text-slate-300 mb-2"></i>
                                                    <p class="font-medium text-slate-500">Tidak ada data peserta ditemukan.</p>
                                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian atau klik 'Daftarkan Peserta'.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($socialParticipantsList, 'hasPages') && $socialParticipantsList->hasPages())
                            <div class="p-3 bg-white border-t border-gov-border">
                                {{ $socialParticipantsList->links(data: ['scrollTo' => false]) }}
                            </div>
                        @endif
                    </div>

                    <!-- Mobile Standalone Responsive Cards -->
                    <div class="md:hidden space-y-3" wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage">
                        @forelse($socialParticipantsList as $p)
                            <div wire:key="socpart-card-{{ $p->id }}" class="bg-white rounded-xl border border-gov-border p-4 shadow-2xs space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <h5 class="font-bold text-sm text-gov-textMain">{{ $p->name }}</h5>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $p->period === ('Periode ' . now()->month . '/' . now()->year) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $p->period ?: 'Bulanan' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                                    <span class="font-semibold text-gov-textMain">{{ $p->socialProgram->name ?? $p->program_name }}</span>
                                    <span class="font-mono font-bold text-gov-navy text-sm">Rp {{ number_format($p->monthly_amount, 0, ',', '.') }}</span>
                                </div>

                                <div class="flex items-center justify-end space-x-1.5 pt-2 border-t border-slate-100">
                                    @php
                                        $canEditMobileParticipant = Auth::user()->canManage() || (Auth::user()->isJamaah() && (
                                            (strtolower(trim($p->name)) === strtolower(trim(Auth::user()->name)))
                                        ));
                                    @endphp
                                    @if($canEditMobileParticipant)
                                        <button type="button"
                                            @click="openEditParticipant({{ Js::from([
                                                'id' => $p->id,
                                                'name' => $p->name,
                                                'program_name' => $p->program_name,
                                                'monthly_amount' => (int) $p->monthly_amount,
                                                'period' => $p->period ?? '',
                                            ]) }})"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-600 shadow-2xs transition cursor-pointer"
                                            title="Edit Data Peserta">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        </button>
                                    @endif

                                    @if(Auth::user()->canManage())
                                        <button type="button"
                                            @click="openDeleteModal({{ Js::from([
                                                'title' => 'Hapus Peserta Program',
                                                'message' => 'Apakah Anda yakin ingin menghapus data peserta \'' . $p->name . '\' dari program ini?',
                                                'itemName' => $p->name,
                                                'action' => 'deleteParticipant',
                                                'id' => $p->id,
                                            ]) }})"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 shadow-2xs transition cursor-pointer"
                                            title="Hapus Peserta">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-xl border border-gov-border p-8 text-center text-slate-400 shadow-2xs">
                                <div class="flex flex-col items-center justify-center py-4">
                                    <i data-lucide="users" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500">Tidak ada data peserta ditemukan.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian atau klik 'Daftarkan Peserta'.</p>
                                </div>
                            </div>
                        @endforelse

                        @if(method_exists($socialParticipantsList, 'hasPages') && $socialParticipantsList->hasPages())
                            <div class="p-3 bg-white rounded-xl border border-gov-border shadow-2xs">
                                {{ $socialParticipantsList->links(data: ['scrollTo' => false]) }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ==================================================== -->
                <!-- SUB-VIEW 3: SETORAN KANTOR (KLIRING TRANSFER KANTOR) -->
                <!-- ==================================================== -->
                <div x-show="socialSubTab === 'setoran'" x-cloak class="space-y-6">

                    <!-- Header Card -->
                    <div class="w-full bg-white p-5 rounded-xl border border-gov-border shadow-2xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h4 class="font-bold text-sm text-gov-textMain">Riwayat Penerimaan Transfer Kliring</h4>
                                <p class="text-xs text-slate-400">Catatan setoran dana potongan kantor yang telah diterima di rekening kas masjid</p>
                            </div>
                            @if(Auth::user()->canManage())
                                <button type="button" @click="showLumpSumModal = true; $wire.openLumpSumModal()"
                                    class="px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer inline-flex items-center gap-1.5 self-start sm:self-auto">
                                    <i data-lucide="plus" class="w-4 h-4 text-amber-400"></i>
                                    <span>Catat Setoran Kliring</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-300 bg-slate-50/80 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                                        <th wire:click="sortBy('transaction_date', 'lump_sum')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Tanggal</span>
                                                <x-sort-icon field="transaction_date" table="lump_sum" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('program_name', 'lump_sum')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Program</span>
                                                <x-sort-icon field="program_name" table="lump_sum" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('description', 'lump_sum')" class="p-3.5 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center gap-1">
                                                <span>Keterangan</span>
                                                <x-sort-icon field="description" table="lump_sum" />
                                            </div>
                                        </th>
                                        <th wire:click="sortBy('amount', 'lump_sum')" class="p-3.5 text-right cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                            <div class="flex items-center justify-end gap-1">
                                                <span>Nominal Masuk (Rp)</span>
                                                <x-sort-icon field="amount" table="lump_sum" />
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-300">
                                    @forelse($lumpSumHistory as $lh)
                                        <tr wire:key="lump-row-{{ $lh->id }}" class="hover:bg-slate-50/80 transition">
                                            <td class="p-3.5 text-slate-700 whitespace-nowrap font-medium">
                                                {{ $lh->transaction_date ? $lh->transaction_date->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="p-3.5 text-slate-600 font-medium">{{ $lh->program_name }}</td>
                                            <td class="p-3.5 text-slate-600">{{ $lh->description }}</td>
                                            <td class="p-3.5 text-right font-black text-emerald-700 whitespace-nowrap">
                                                Rp {{ number_format($lh->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-6 text-center text-slate-400">
                                                <div class="flex flex-col items-center justify-center py-6">
                                                    <i data-lucide="landmark" class="w-8 h-8 text-slate-300 mb-2"></i>
                                                    <p class="font-medium text-slate-500">Belum ada riwayat penerimaan transfer kliring.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($lumpSumHistory, 'hasPages') && $lumpSumHistory->hasPages())
                            <div class="p-3 bg-white border-t border-gov-border">
                                {{ $lumpSumHistory->links(data: ['scrollTo' => false]) }}
                            </div>
                        @endif
                    </div>

                    <!-- Mobile Standalone Responsive Cards -->
                    <div class="md:hidden space-y-3">
                        @forelse($lumpSumHistory as $lh)
                            <div wire:key="lump-card-{{ $lh->id }}" class="bg-white rounded-xl border border-gov-border p-4 shadow-2xs space-y-2.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <span class="text-xs font-semibold text-slate-500">{{ $lh->transaction_date ? $lh->transaction_date->format('d/m/Y') : '-' }}</span>
                                        <h5 class="font-bold text-sm text-slate-800 mt-0.5">{{ $lh->program_name }}</h5>
                                    </div>
                                    <span class="font-black text-emerald-700 text-sm whitespace-nowrap">
                                        Rp {{ number_format($lh->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                @if($lh->description)
                                    <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100">{{ $lh->description }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="bg-white rounded-xl border border-gov-border p-6 text-center text-slate-400 shadow-2xs">
                                <div class="flex flex-col items-center justify-center py-6">
                                    <i data-lucide="landmark" class="w-8 h-8 text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500">Belum ada riwayat penerimaan transfer kliring.</p>
                                </div>
                            </div>
                        @endforelse

                        @if(method_exists($lumpSumHistory, 'hasPages') && $lumpSumHistory->hasPages())
                            <div class="p-3 bg-white rounded-xl border border-gov-border shadow-2xs">
                                {{ $lumpSumHistory->links(data: ['scrollTo' => false]) }}
                            </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>
