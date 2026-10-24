            <!-- ======================================================== -->
            <!-- TAB: PROGRAM SOSIAL & KEIKUTSERTAAN (MILESTONE 6) -->
            <!-- ======================================================== -->
            <div class="space-y-6">
                <!-- Header Card Banner (Navy) -->
                <div class="bg-gov-navy rounded-xl p-5 border border-gov-border/30 shadow-xs bg-gov-pattern">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-10">
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-300">Program Sosial</span>
                            </div>
                            <h3 class="font-bold text-base text-white">
                                <span x-show="socialSubTab === 'katalog'">Katalog Program</span>
                                <span x-show="socialSubTab === 'peserta'" x-cloak>Rekap Peserta</span>
                                <span x-show="socialSubTab === 'pernyataan'" x-cloak>Surat Pernyataan</span>
                                <span x-show="socialSubTab === 'setoran'" x-cloak>Setoran Kantor</span>
                            </h3>
                            <span class="sr-only">Program Sosial</span>
                            <div class="text-xs text-slate-300 mt-0.5">
                                <span x-show="socialSubTab === 'katalog'">Katalog program pemberdayaan, santunan, dan donasi sosial.</span>
                                <span x-show="socialSubTab === 'peserta'" x-cloak>Rekapitulasi komitmen sukarela dan basis data peserta program.</span>
                                <span x-show="socialSubTab === 'pernyataan'" x-cloak>Cetak dan arsip surat pernyataan komitmen potongan gaji pegawai.</span>
                                <span x-show="socialSubTab === 'setoran'" x-cloak>Penyetoran dan pencatatan komitmen infaq potongan gaji ke kas masjid.</span>
                            </div>
                        </div>

                        @if(Auth::user()->canManage())
                            <div class="shrink-0 hidden sm:flex sm:items-center gap-2">
                                <button type="button" @click="showImportPotonganModal = true; $wire.call('openImportPotonganModal'); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                                    class="justify-center h-9 px-3.5 rounded-lg border border-white/20 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-white/70"></i>
                                    <span>Import</span>
                                </button>
                                <button type="button" @click="showExportPotonganModal = true; $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                                    class="justify-center h-9 px-3.5 rounded-lg border border-white/20 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="download" class="w-3.5 h-3.5 text-emerald-300"></i>
                                    <span>Export</span>
                                </button>
                                <button type="button" @click="openCreateParticipant()"
                                    class="justify-center inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg border border-white/20 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs shadow-2xs transition cursor-pointer">
                                    <i data-lucide="user-plus" class="w-3.5 h-3.5 text-amber-300"></i>
                                    <span>Peserta</span>
                                </button>
                                <button type="button" @click="openCreateSocialProgram()"
                                    class="justify-center inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-amber-400 hover:bg-amber-300 text-gov-navy font-bold text-xs shadow-2xs transition cursor-pointer">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    <span>Program</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- SUB-VIEW 1: KATALOG PROGRAM SOSIAL -->
                <div x-show="socialSubTab === 'katalog'" x-cloak class="space-y-5">
                    <!-- Metric Cards (3 Cards) -->
                    @if(Auth::user()->canManage())
                    <!-- Mobile KPI Carousel Wrapper -->
                    <div>
                        <!-- Mobile: scroll-snap carousel -->
                        <div class="sm:hidden"
                            x-data="{
                                kpiIdx: 0,
                                kpiTotal: 3,
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
                                <!-- Card 0: Dana Terhimpun -->
                                <div class="snap-start shrink-0 w-full bg-white p-4 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
                                    <div>
                                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Dana Terhimpun</p>
                                        <h4 class="text-lg font-bold text-emerald-700 mt-1">Rp {{ number_format($totalSocialCollectedAll, 0, ',', '.') }}</h4>
                                        <p class="text-[11px] text-emerald-600 font-medium mt-0.5">Januari - {{ now()->translatedFormat('F Y') }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center border border-emerald-200">
                                        <i data-lucide="wallet" class="w-5 h-5 text-emerald-600"></i>
                                    </div>
                                </div>
                                <!-- Card 1: Peserta Terdaftar -->
                                <div class="snap-start shrink-0 w-full bg-white p-4 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
                                    <div>
                                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Peserta Terdaftar</p>
                                        <h4 class="text-lg font-bold text-gov-textMain mt-1">{{ $totalActiveSocialParticipants }} Peserta</h4>
                                        <p class="text-[11px] text-slate-400 mt-0.5">per {{ now()->translatedFormat('F Y') }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center border border-blue-200">
                                        <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                                    </div>
                                </div>
                                <!-- Card 2: Potongan -->
                                <div class="snap-start shrink-0 w-full bg-white p-4 rounded-xl border border-gov-border shadow-2xs flex items-center justify-between">
                                    <div>
                                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Potongan</p>
                                        <h4 class="text-lg font-bold text-amber-700 mt-1">Rp {{ number_format($totalSocialCommitment, 0, ',', '.') }}</h4>
                                        <p class="text-[11px] text-amber-600 font-medium mt-0.5">per {{ now()->translatedFormat('F Y') }}</p>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center border border-amber-200">
                                        <i data-lucide="calendar-clock" class="w-5 h-5 text-amber-600"></i>
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


                        <!-- Desktop: normal grid (sm+) -->
                        <div class="hidden sm:grid sm:grid-cols-3 gap-4">
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
                    </div>
                    @else
                    @php
                        $catalogMonthOptions = [];
                        foreach (range(1, 12) as $mNum) {
                            $catalogMonthOptions[] = [
                                'value' => (string) $mNum,
                                'label' => \Carbon\Carbon::create(2000, $mNum, 1)->translatedFormat('F'),
                            ];
                        }
                        $catalogYearOptions = [];
                        foreach (range((int) now('Asia/Jakarta')->year, (int) now('Asia/Jakarta')->year - 3) as $yr) {
                            $catalogYearOptions[] = ['value' => (string) $yr, 'label' => (string) $yr];
                        }
                    @endphp
                    <!-- Filter Toolbar Katalog (Jamaah): Pencarian, Bulan, Tahun -->
                    <div class="flex flex-col md:flex-row md:items-center gap-2">
                        <div class="relative flex-1 min-w-0">
                            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input wire:model.live.debounce.300ms="catalogSearch" type="text"
                                placeholder="Cari nama program..."
                                class="h-9 w-full pl-8 pr-9 text-xs rounded-lg border border-gov-border bg-white font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition shadow-2xs">
                            <button type="button" wire:click="$set('catalogSearch', '')" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 transition cursor-pointer flex items-center justify-center" title="Hapus pencarian">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-2 w-full md:w-auto shrink-0 text-xs">
                            <span class="sr-only">BULAN:</span>
                            <x-custom-select
                                wire:model.live="catalogMonth"
                                :options="$catalogMonthOptions"
                                placeholder="Pilih Bulan"
                                icon="calendar"
                                class="flex-1 min-w-0 md:flex-none w-full md:w-36"
                            />
                            <span class="sr-only">TAHUN:</span>
                            <x-custom-select
                                wire:model.live="catalogYear"
                                :options="$catalogYearOptions"
                                placeholder="Pilih Tahun"
                                class="flex-1 min-w-0 md:flex-none w-full md:w-28"
                            />
                            <button type="button" wire:click="resetCatalogFilters"
                                class="h-9 px-3 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 hover:text-gov-navy text-xs font-semibold shadow-2xs transition cursor-pointer flex items-center gap-1.5 shrink-0"
                                title="Reset filter dan pencarian ke kondisi awal">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-500"></i>
                                <span class="whitespace-nowrap">Reset</span>
                            </button>
                        </div>
                    </div>
                    @endif

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
                                                    Periode {{ ucfirst($prog->period_type) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            @if($prog->status === 'AKTIF')
                                                <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-emerald-50 text-emerald-700 border-emerald-400">Aktif</span>
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
                                    @if(Auth::user()->canManage())
                                    <div class="bg-slate-50 p-3 rounded-lg border border-gov-border mb-4 flex items-center justify-between text-xs">
                                        <span class="text-slate-500 font-medium">Realisasi Dana:</span>
                                        <span class="font-bold text-gov-textMain">
                                            Rp {{ number_format($prog->total_collected, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @endif

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
                                            <span class="text-slate-500 block text-[11px] mb-1">{{ Auth::user()->canManage() ? 'Potongan Bulan Ini' : 'Potongan ' . \Carbon\Carbon::create((int) ($this->catalogYear ?: now()->year), (int) ($this->catalogMonth ?: now()->month), 1)->translatedFormat('F Y') }}</span>
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

                                        <div x-data="{ openMenu: false }" class="relative" @click.outside="openMenu = false" @keydown.escape.window="openMenu = false">
                                            <button type="button" @click="openMenu = !openMenu" 
                                                class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center" 
                                                :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''" 
                                                title="Menu Aksi">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Popover -->
                                            <div x-show="openMenu" 
                                                x-transition:enter="transition ease-out duration-100" 
                                                x-transition:enter-start="opacity-0 scale-95" 
                                                x-transition:enter-end="opacity-100 scale-100" 
                                                x-transition:leave="transition ease-in duration-75" 
                                                x-transition:leave-start="opacity-100 scale-100" 
                                                x-transition:leave-end="opacity-0 scale-95" 
                                                class="absolute right-0 bottom-full mb-1 w-44 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-left focus:outline-none" 
                                                style="display: none;">
                                                <button type="button" 
                                                    @click="openMenu = false; openEditSocialProgram({{ $prog->id }})" 
                                                    class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-gov-navy transition cursor-pointer">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                                    <span>Edit Program</span>
                                                </button>
                                                <div class="my-1 border-t border-slate-100"></div>
                                                <button type="button" 
                                                    @click="openMenu = false; openDeleteModal({{ Js::from([
                                                        'title' => 'Hapus Program Sosial',
                                                        'message' => 'Apakah Anda yakin ingin menghapus program sosial \'' . $prog->name . '\'? Seluruh data terkait program ini akan terdampak.',
                                                        'itemName' => $prog->name,
                                                        'action' => 'deleteSocialProgram',
                                                        'id' => $prog->id,
                                                    ]) }})" 
                                                    class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                    <span>Hapus Program</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full">
                                <x-empty-state 
                                    icon="heart-handshake" 
                                    title="Tidak ada data program sosial" 
                                    subtitle="Belum ada program sosial yang sesuai dengan kriteria atau filter pencarian ini." 
                                />
                            </div>
                        @endforelse
                    </div>
                </div>

                @if(Auth::user()->canManage())
                    <!-- SUB-VIEW 2: REKAPITULASI PESERTA -->
                    <div x-show="socialSubTab === 'peserta'" x-cloak class="space-y-6">
                    <!-- Filter & Search Bar -->
                    <div class="flex flex-col md:flex-row md:items-center gap-2">
                        <!-- Search Input (Selalu di atas pada mobile) -->
                        <div class="relative flex-1 min-w-0" x-data="{
                            clearSearch() {
                                $wire.clearSocialParticipantSearch();
                            }
                        }">
                            <i data-lucide="search"
                                class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input wire:model.live.debounce.300ms="socialParticipantSearch" type="text"
                                placeholder="Cari nama peserta..."
                                class="h-9 w-full pl-8 pr-9 rounded-lg border border-gov-border bg-white text-xs text-gov-textMain focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-medium shadow-2xs transition">
                            <button type="button" @click="clearSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 transition cursor-pointer flex items-center justify-center" title="Reset pencarian">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>

                        <!-- Filter Row -->
                        <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                            <div class="flex-1 min-w-0 md:flex-none md:w-56">
                                @php
                                    $programFilterOptions = array_merge(
                                        [['value' => 'all', 'label' => 'Program Sosial (Semua)']],
                                        $allSocialPrograms->map(fn($sp) => ['value' => (string)$sp->id, 'label' => $sp->name])->values()->all()
                                    );
                                @endphp
                                <x-custom-select 
                                    wire:model.live="socialProgramFilter"
                                    :options="$programFilterOptions"
                                    placeholder="Program Sosial (Semua)"
                                    icon="heart-handshake"
                                    class="w-full"
                                />
                            </div>

                            <div class="relative flex-1 min-w-0 md:flex-none md:w-44" x-data="{
                                open: false
                            }">
                                <button type="button" @click="open = !open"
                                    class="w-full h-9 px-2 sm:px-2.5 rounded-lg border border-gov-border bg-white text-xs font-medium text-gov-textMain cursor-pointer flex items-center justify-between gap-1 shadow-2xs hover:bg-slate-50 transition focus:outline-none focus:border-gov-navy select-none"
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
                                            <span class="text-gov-textMain font-medium truncate">Periode (Semua)</span>
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
                                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform duration-75 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                <!-- Dropdown Checklist Panel -->
                                <div x-show="open" @click.outside="open = false" x-cloak
                                    x-transition:enter="transition ease-out duration-50"
                                    x-transition:enter-start="transform opacity-0 scale-98"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-50"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-98"
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
                                                    <span class="inline-flex items-center h-[18px] px-1.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-400 shrink-0">
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

                            <button type="button" wire:click="resetParticipantFilters"
                                class="h-9 px-3 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 hover:text-gov-navy text-xs font-semibold shadow-2xs transition cursor-pointer flex items-center gap-1.5 shrink-0"
                                title="Reset filter dan pencarian ke kondisi awal">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-500"></i>
                                <span class="whitespace-nowrap">Reset</span>
                            </button>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-visible">
                        <div>
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
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $p->period === ('Periode ' . now()->month . '/' . now()->year) ? 'bg-emerald-50 text-emerald-700 border border-emerald-400 font-semibold' : 'bg-slate-100 text-slate-700' }}">
                                                    {{ $p->period ?: 'Bulanan' }}
                                                </span>
                                            </td>

                                            <!-- Aksi (Rule 3) -->
                                            <td x-data="{ openMenu: false }" class="py-3 px-4 text-center relative" @click.outside="openMenu = false" @keydown.escape.window="openMenu = false">
                                                @php
                                                    $canEditThisParticipant = Auth::user()->canManage() || (Auth::user()->isJamaah() && (
                                                        (strtolower(trim($p->name)) === strtolower(trim(Auth::user()->name)))
                                                    ));
                                                @endphp
                                                @if($canEditThisParticipant || Auth::user()->canManage())
                                                    <div class="inline-flex items-center justify-center">
                                                        <button type="button" @click="openMenu = !openMenu" 
                                                            class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center" 
                                                            :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''" 
                                                            title="Menu Aksi">
                                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle>
                                                            </svg>
                                                        </button>

                                                        <!-- Dropdown Popover -->
                                                        <div x-show="openMenu" 
                                                            x-transition:enter="transition ease-out duration-100" 
                                                            x-transition:enter-start="opacity-0 scale-95" 
                                                            x-transition:enter-end="opacity-100 scale-100" 
                                                            x-transition:leave="transition ease-in duration-75" 
                                                            x-transition:leave-start="opacity-100 scale-100" 
                                                            x-transition:leave-end="opacity-0 scale-95" 
                                                            class="absolute right-4 top-full mt-1 w-44 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-left focus:outline-none" 
                                                            style="display: none;">
                                                            @if($canEditThisParticipant)
                                                                <button type="button" 
                                                                    @click="openMenu = false; openEditParticipant({{ Js::from([
                                                                        'id' => $p->id,
                                                                        'name' => $p->name,
                                                                        'program_name' => $p->program_name,
                                                                        'monthly_amount' => (int) $p->monthly_amount,
                                                                        'period' => $p->period ?? '',
                                                                    ]) }})" 
                                                                    class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-gov-navy transition cursor-pointer">
                                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                                                    <span>Edit Peserta</span>
                                                                </button>
                                                            @endif
                                                            @if(Auth::user()->canManage())
                                                                @if($canEditThisParticipant)
                                                                    <div class="my-1 border-t border-slate-100"></div>
                                                                @endif
                                                                <button type="button" 
                                                                    @click="openMenu = false; openDeleteModal({{ Js::from([
                                                                        'title' => 'Hapus Peserta Program',
                                                                        'message' => 'Apakah Anda yakin ingin menghapus data peserta \'' . $p->name . '\' dari program ini?',
                                                                        'itemName' => $p->name,
                                                                        'action' => 'deleteParticipant',
                                                                        'id' => $p->id,
                                                                    ]) }})" 
                                                                    class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                                    <span>Hapus Peserta</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <x-empty-state 
                                            :isTable="true" 
                                            colspan="5" 
                                            icon="users" 
                                            title="Tidak ada data peserta program" 
                                            subtitle="Belum ada peserta yang sesuai dengan kriteria atau filter pencarian ini." 
                                        />
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $p->period === ('Periode ' . now()->month . '/' . now()->year) ? 'bg-emerald-50 text-emerald-700 border border-emerald-400 font-semibold' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $p->period ?: 'Bulanan' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                                    <span class="font-semibold text-gov-textMain">{{ $p->socialProgram->name ?? $p->program_name }}</span>
                                    <span class="font-mono font-bold text-gov-navy text-sm">Rp {{ number_format($p->monthly_amount, 0, ',', '.') }}</span>
                                </div>

                                <div x-data="{ openMenu: false }" class="flex items-center justify-end pt-2 border-t border-slate-100 relative" @click.outside="openMenu = false" @keydown.escape.window="openMenu = false">
                                    @php
                                        $canEditMobileParticipant = Auth::user()->canManage() || (Auth::user()->isJamaah() && (
                                            (strtolower(trim($p->name)) === strtolower(trim(Auth::user()->name)))
                                        ));
                                    @endphp
                                    @if($canEditMobileParticipant || Auth::user()->canManage())
                                        <button type="button" @click="openMenu = !openMenu" 
                                            class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center" 
                                            :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''" 
                                            title="Menu Aksi">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle>
                                            </svg>
                                        </button>

                                        <!-- Dropdown Popover -->
                                        <div x-show="openMenu" 
                                            x-transition:enter="transition ease-out duration-100" 
                                            x-transition:enter-start="opacity-0 scale-95" 
                                            x-transition:enter-end="opacity-100 scale-100" 
                                            x-transition:leave="transition ease-in duration-75" 
                                            x-transition:leave-start="opacity-100 scale-100" 
                                            x-transition:leave-end="opacity-0 scale-95" 
                                            class="absolute right-0 bottom-full mb-1 w-44 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-left focus:outline-none" 
                                            style="display: none;">
                                            @if($canEditMobileParticipant)
                                                <button type="button" 
                                                    @click="openMenu = false; openEditParticipant({{ Js::from([
                                                        'id' => $p->id,
                                                        'name' => $p->name,
                                                        'program_name' => $p->program_name,
                                                        'monthly_amount' => (int) $p->monthly_amount,
                                                        'period' => $p->period ?? '',
                                                    ]) }})" 
                                                    class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-gov-navy transition cursor-pointer">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                                    <span>Edit Peserta</span>
                                                </button>
                                            @endif
                                            @if(Auth::user()->canManage())
                                                @if($canEditMobileParticipant)
                                                    <div class="my-1 border-t border-slate-100"></div>
                                                @endif
                                                <button type="button" 
                                                    @click="openMenu = false; openDeleteModal({{ Js::from([
                                                        'title' => 'Hapus Peserta Program',
                                                        'message' => 'Apakah Anda yakin ingin menghapus data peserta \'' . $p->name . '\' dari program ini?',
                                                        'itemName' => $p->name,
                                                        'action' => 'deleteParticipant',
                                                        'id' => $p->id,
                                                    ]) }})" 
                                                    class="w-full flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                                    <span>Hapus Peserta</span>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <x-empty-state 
                                icon="users" 
                                title="Tidak ada data peserta program" 
                                subtitle="Belum ada peserta yang sesuai dengan kriteria atau filter pencarian ini." 
                            />
                        @endforelse

                        @if(method_exists($socialParticipantsList, 'hasPages') && $socialParticipantsList->hasPages())
                            <div class="p-3 bg-white rounded-xl border border-gov-border shadow-2xs">
                                {{ $socialParticipantsList->links(data: ['scrollTo' => false]) }}
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- ==================================================== -->
                <!-- SUB-VIEW 3: SURAT PERNYATAAN POTONGAN TUKIN -->
                <!-- ==================================================== -->
                <div x-show="socialSubTab === 'pernyataan'" x-cloak class="space-y-6 max-w-6xl" @click="closePrintDropdowns()">

                    @if(!Auth::user()->canManage())
                        <!-- Inisialisasi State Khusus Jamaah (Selalu Single Pegawai Diri Sendiri) -->
                        <div x-init="
                            printPernyataanMode = 'single';
                            printPernyataanEmployeeName = {{ Js::from(Auth::user()->name) }};
                            printPernyataanEmployeeId = '';
                        "></div>
                    @else
                        <!-- Mode Switcher: Cetak Formulir vs Unggah & Verifikasi Scan PDF -->
                        <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
                            <button type="button"
                                @click="pernyataanModalTab = 'cetak'; $wire.set('pernyataanModalTab', 'cetak', false)"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer"
                                :class="pernyataanModalTab === 'cetak' ? 'bg-gov-navy text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'">
                                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                <span>Cetak Formulir</span>
                            </button>
                            <button type="button"
                                @click="pernyataanModalTab = 'impor'; $wire.set('pernyataanModalTab', 'impor', false)"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer"
                                :class="pernyataanModalTab === 'impor' ? 'bg-gov-navy text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'">
                                <i data-lucide="file-up" class="w-3.5 h-3.5"></i>
                                <span>Unggah Scan</span>
                            </button>
                        </div>
                    @endif

                    <!-- ============================================== -->
                    <!-- PANEL 1: CETAK FORMULIR -->
                    <!-- ============================================== -->
                    <div x-show="pernyataanModalTab === 'cetak' || {{ !Auth::user()->canManage() ? 'true' : 'false' }}" class="space-y-6">

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                            <!-- Left / Main Form Column -->
                            <div class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-xl border border-gov-border shadow-2xs space-y-5 text-xs">

                                @if(Auth::user()->canManage())
                                    <!-- Mode Pemilihan Sasaran Pegawai (Hanya untuk Admin / Pengurus) -->
                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-2 text-xs">Sasaran Pegawai *</label>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                            <button type="button" @click="printPernyataanMode = 'all'; closePrintDropdowns()"
                                                class="p-3 rounded-lg border text-left transition flex flex-col justify-between cursor-pointer"
                                                :class="printPernyataanMode === 'all' ? 'border-gov-navy bg-sky-50/70 text-gov-navy font-bold ring-1 ring-gov-navy/20' : 'border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium'">
                                                <span class="flex items-center gap-1.5">
                                                    <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                                                    <span class="text-xs">Semua Pegawai</span>
                                                </span>
                                                <span class="text-[11px] opacity-75 mt-1 block font-normal leading-tight">Mail-merge seluruh pegawai yang ada di rekapitulasi</span>
                                            </button>

                                            <button type="button" @click="printPernyataanMode = 'single'; closePrintDropdowns()"
                                                class="p-3 rounded-lg border text-left transition flex flex-col justify-between cursor-pointer"
                                                :class="printPernyataanMode === 'single' ? 'border-gov-navy bg-sky-50/70 text-gov-navy font-bold ring-1 ring-gov-navy/20' : 'border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium'">
                                                <span class="flex items-center gap-1.5">
                                                    <i data-lucide="user" class="w-4 h-4 shrink-0"></i>
                                                    <span class="text-xs">Satu Pegawai</span>
                                                </span>
                                                <span class="text-[11px] opacity-75 mt-1 block font-normal leading-tight">Pilih 1 lembar nama spesifik dari rekapitulasi</span>
                                            </button>

                                            <button type="button" @click="printPernyataanMode = 'new'; closePrintDropdowns()"
                                                class="p-3 rounded-lg border text-left transition flex flex-col justify-between cursor-pointer"
                                                :class="printPernyataanMode === 'new' ? 'border-gov-navy bg-sky-50/70 text-gov-navy font-bold ring-1 ring-gov-navy/20' : 'border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium'">
                                                <span class="flex items-center gap-1.5">
                                                    <i data-lucide="user-plus" class="w-4 h-4 shrink-0"></i>
                                                    <span class="text-xs">Pegawai Baru / Blanko</span>
                                                </span>
                                                <span class="text-[11px] opacity-75 mt-1 block font-normal leading-tight">Pegawai baru atau formulir kosong tulis tangan</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Single Mode: Pilih Nama Pegawai dari Rekapitulasi -->
                                    <div x-show="printPernyataanMode === 'single'" class="space-y-1.5">
                                        <label class="block font-semibold text-slate-700">Pilih Nama Pegawai Rekapitulasi *</label>
                                        <div class="relative" :class="printPernyataanOpenDropdown === 'singleEmp' ? 'z-40' : 'z-10'"
                                            @click.outside="if (printPernyataanOpenDropdown === 'singleEmp') closePrintDropdowns()">
                                            <button type="button" @click.stop="togglePrintDropdown('singleEmp')"
                                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between gap-2 text-left">
                                                <span class="truncate" x-text="printPernyataanEmployeeName ? printPernyataanEmployeeName : '-- Pilih Nama Pegawai yang Ada di Rekapitulasi --'"></span>
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'singleEmp' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m6 9 6 6 6-6"/>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div x-show="printPernyataanOpenDropdown === 'singleEmp'" x-cloak @click.stop
                                                class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-2xl border border-gov-border py-2 z-50 max-h-60 overflow-y-auto"
                                                style="display: none;">
                                                <div class="px-2.5 pb-2 border-b border-slate-100 flex items-center gap-1.5">
                                                    <input x-model="printPernyataanSearchEmp" type="text" placeholder="Ketik untuk mencari nama..."
                                                        class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-gov-navy font-medium bg-slate-50">
                                                    <button type="button" @click.stop="printPernyataanEmployeeName = ''; closePrintDropdowns()"
                                                        class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-slate-100 transition cursor-pointer" title="Hapus pilihan">
                                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </div>
                                                <div class="pt-1 divide-y divide-slate-100">
                                                    @if(isset($distinctParticipantNames))
                                                        @foreach($distinctParticipantNames as $pName)
                                                            <button type="button"
                                                                x-show="!printPernyataanSearchEmp || '{{ strtolower(addslashes($pName)) }}'.includes(printPernyataanSearchEmp.toLowerCase())"
                                                                @click.stop="printPernyataanEmployeeName = '{{ addslashes($pName) }}'; closePrintDropdowns()"
                                                                class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between transition"
                                                                :class="printPernyataanEmployeeName === '{{ addslashes($pName) }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                                                <span>{{ $pName }}</span>
                                                                <span x-show="printPernyataanEmployeeName === '{{ addslashes($pName) }}'" class="text-gov-navy font-bold">✓</span>
                                                            </button>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- New Mode: Pilih Pegawai Baru dari Data Induk Pegawai Aktif -->
                                    <div x-show="printPernyataanMode === 'new'" class="space-y-3">
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Pilih Pegawai Muslim (Database Pegawai Aktif)</label>
                                            <div class="relative" :class="printPernyataanOpenDropdown === 'newEmp' ? 'z-40' : 'z-10'"
                                                @click.outside="if (printPernyataanOpenDropdown === 'newEmp') closePrintDropdowns()">
                                                <button type="button" @click.stop="togglePrintDropdown('newEmp')"
                                                    class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between gap-2 text-left">
                                                    <span class="truncate" x-text="printPernyataanEmployeeId && printPernyataanSelectedNewName ? printPernyataanSelectedNewName : '-- Formulir Blanko Kosong (Semua Data Titik-Titik Tulis Tangan) --'"></span>
                                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'newEmp' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m6 9 6 6 6-6"/>
                                                    </svg>
                                                </button>

                                                <!-- Dropdown Menu -->
                                                <div x-show="printPernyataanOpenDropdown === 'newEmp'" x-cloak @click.stop
                                                    class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-2xl border border-gov-border py-2 z-50 max-h-60 overflow-y-auto"
                                                    style="display: none;">
                                                    <div class="px-2.5 pb-2 border-b border-slate-100 flex items-center gap-1.5">
                                                        <input x-model="printPernyataanSearchNewEmp" type="text" placeholder="Ketik nama atau NIP pegawai..."
                                                            class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-gov-navy font-medium bg-slate-50">
                                                        <button type="button" @click.stop="printPernyataanEmployeeId = ''; printPernyataanSelectedNewName = ''; closePrintDropdowns()"
                                                            class="p-1 rounded-md text-slate-400 hover:text-rose-600 hover:bg-slate-100 transition cursor-pointer" title="Reset ke Blanko Kosong">
                                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    </div>
                                                    <div class="pt-1 divide-y divide-slate-100">
                                                        <button type="button" @click.stop="printPernyataanEmployeeId = ''; printPernyataanSelectedNewName = ''; closePrintDropdowns()"
                                                            class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between transition"
                                                            :class="!printPernyataanEmployeeId ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-500 font-medium italic'">
                                                            <span>-- Formulir Blanko Kosong (Titik-Titik Tulis Tangan) --</span>
                                                            <span x-show="!printPernyataanEmployeeId" class="text-gov-navy font-bold">✓</span>
                                                        </button>
                                                        @if(isset($allEmployeesForPrint))
                                                            @foreach($allEmployeesForPrint as $empItem)
                                                                @php
                                                                    $empDisplay = $empItem->name . ($empItem->nip_panjang ? ' (' . $empItem->nip_panjang . ')' : '');
                                                                @endphp
                                                                <button type="button"
                                                                    x-show="!printPernyataanSearchNewEmp || '{{ strtolower(addslashes($empDisplay)) }}'.includes(printPernyataanSearchNewEmp.toLowerCase())"
                                                                    @click.stop="printPernyataanEmployeeId = '{{ $empItem->id }}'; printPernyataanSelectedNewName = '{{ addslashes($empDisplay) }}'; closePrintDropdowns()"
                                                                    class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between transition"
                                                                    :class="printPernyataanEmployeeId == '{{ $empItem->id }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                                                    <span>{{ $empDisplay }}</span>
                                                                    <span x-show="printPernyataanEmployeeId == '{{ $empItem->id }}'" class="text-gov-navy font-bold">✓</span>
                                                                </button>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-slate-500 italic">
                                            *Pilih pegawai muslim yang belum terdaftar untuk mencetak formulir dengan identitas profilnya lengkap, atau biarkan blanko kosong untuk dicetak massal dan diisi tulis tangan.
                                        </p>
                                    </div>
                                @endif

                                <!-- Parameter Riwayat Potongan & Waktu Persuratan -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                                    <!-- Tahun Riwayat Potongan (Kondisional: Muncul jika bukan mode Pegawai Baru) -->
                                    <div x-show="printPernyataanMode !== 'new'" class="space-y-1.5">
                                        <label class="block font-semibold text-slate-700">Tahun Riwayat Potongan *</label>
                                        <div class="relative" :class="printPernyataanOpenDropdown === 'historyYear' ? 'z-40' : 'z-10'"
                                            @click.outside="if (printPernyataanOpenDropdown === 'historyYear') closePrintDropdowns()">
                                            <button type="button" @click.stop="togglePrintDropdown('historyYear')"
                                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between gap-2 text-left">
                                                <span class="truncate" x-text="'Tahun ' + printPernyataanYear"></span>
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'historyYear' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m6 9 6 6 6-6"/>
                                                </svg>
                                            </button>

                                            <div x-show="printPernyataanOpenDropdown === 'historyYear'" x-cloak @click.stop
                                                class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-2xl border border-gov-border py-1 z-50 max-h-56 overflow-y-auto divide-y divide-slate-100"
                                                style="display: none;">
                                                @for($y = (int)date('Y') + 2; $y >= 2024; $y--)
                                                    <button type="button" @click.stop="printPernyataanYear = '{{ $y }}'; closePrintDropdowns()"
                                                        class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between transition"
                                                        :class="printPernyataanYear == '{{ $y }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                                        <span>Tahun {{ $y }}</span>
                                                        <span x-show="printPernyataanYear == '{{ $y }}'" class="text-gov-navy font-bold">✓</span>
                                                    </button>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-tight">
                                            Tabel riwayat potongan akan otomatis tertampil jika pegawai memiliki riwayat potongan pada tahun ini.
                                        </p>
                                    </div>

                                    <!-- Tanggal Surat -->
                                    <div class="space-y-1.5">
                                        <label class="block font-semibold text-slate-700">Tanggal Surat</label>
                                        <input x-model="printPernyataanDateLetter" type="text"
                                            placeholder="Kosongkan untuk garis titik-titik (tulis tangan)"
                                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium">
                                        <p class="text-[11px] text-slate-500">Contoh: <em>{{ now()->translatedFormat('d F Y') }}</em></p>
                                    </div>
                                </div>

                                <!-- Waktu Mulai Berlaku Pemotongan -->
                                <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                    <label class="block font-semibold text-slate-700">Mulai Berlaku Pemotongan</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <!-- Dropdown Bulan Custom -->
                                        <div class="relative" :class="printPernyataanOpenDropdown === 'month' ? 'z-40' : 'z-10'"
                                            @click.outside="if (printPernyataanOpenDropdown === 'month') closePrintDropdowns()">
                                            <button type="button" @click.stop="togglePrintDropdown('month')"
                                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between gap-1 text-left">
                                                <span class="truncate" x-text="printPernyataanEffectiveMonth ? printPernyataanEffectiveMonth : '-- Kosongkan (Titik Tulis Tangan) --'"></span>
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'month' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m6 9 6 6 6-6"/>
                                                </svg>
                                            </button>

                                            <div x-show="printPernyataanOpenDropdown === 'month'" x-cloak @click.stop
                                                class="absolute left-0 top-full mt-1 w-full bg-white rounded-xl shadow-2xl border border-gov-border py-1 z-50 max-h-56 overflow-y-auto divide-y divide-slate-100"
                                                style="display: none;">
                                                <button type="button" @click.stop="printPernyataanEffectiveMonth = ''; closePrintDropdowns()"
                                                    class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                                    :class="!printPernyataanEffectiveMonth ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-500 font-medium italic'">
                                                    <span>-- Kosongkan (Titik Tulis Tangan) --</span>
                                                    <span x-show="!printPernyataanEffectiveMonth" class="text-gov-navy font-bold">✓</span>
                                                </button>
                                                @php
                                                    $daftarBulan = [
                                                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                                    ];
                                                @endphp
                                                @foreach($daftarBulan as $bulan)
                                                    <button type="button" @click.stop="printPernyataanEffectiveMonth = '{{ $bulan }}'; closePrintDropdowns()"
                                                        class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                                        :class="printPernyataanEffectiveMonth === '{{ $bulan }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                                        <span>{{ $bulan }}</span>
                                                        <span x-show="printPernyataanEffectiveMonth === '{{ $bulan }}'" class="text-gov-navy font-bold">✓</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Dropdown Tahun Custom -->
                                        <div class="relative" :class="printPernyataanOpenDropdown === 'year' ? 'z-40' : 'z-10'"
                                            @click.outside="if (printPernyataanOpenDropdown === 'year') closePrintDropdowns()">
                                            <button type="button" @click.stop="togglePrintDropdown('year')"
                                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between gap-1 text-left">
                                                <span class="truncate" x-text="printPernyataanEffectiveYear ? printPernyataanEffectiveYear : '-- Kosongkan --'"></span>
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'year' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="m6 9 6 6 6-6"/>
                                                </svg>
                                            </button>

                                            <div x-show="printPernyataanOpenDropdown === 'year'" x-cloak @click.stop
                                                class="absolute right-0 top-full mt-1 w-full bg-white rounded-xl shadow-2xl border border-gov-border py-1 z-50 max-h-56 overflow-y-auto divide-y divide-slate-100"
                                                style="display: none;">
                                                <button type="button" @click.stop="printPernyataanEffectiveYear = ''; closePrintDropdowns()"
                                                    class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                                    :class="!printPernyataanEffectiveYear ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-500 font-medium italic'">
                                                    <span>-- Kosongkan --</span>
                                                    <span x-show="!printPernyataanEffectiveYear" class="text-gov-navy font-bold">✓</span>
                                                </button>
                                                @for($y = (int)date('Y') + 2; $y >= 2024; $y--)
                                                    <button type="button" @click.stop="printPernyataanEffectiveYear = '{{ $y }}'; closePrintDropdowns()"
                                                        class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                                        :class="printPernyataanEffectiveYear == '{{ $y }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                                        <span>{{ $y }}</span>
                                                        <span x-show="printPernyataanEffectiveYear == '{{ $y }}'" class="text-gov-navy font-bold">✓</span>
                                                    </button>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(Auth::user()->canManage())
                                    <!-- Petugas Sekretaris Pengumpul Berkas (Hanya untuk Admin / Pengurus) -->
                                    <div class="border-t border-slate-100 pt-3 space-y-3">
                                        <div class="font-bold text-slate-700 flex items-center gap-1.5">
                                            <i data-lucide="user" class="w-3.5 h-3.5 text-gov-navy"></i>
                                            <span>Sekretaris</span>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block font-semibold text-slate-600 mb-1">Nama Sekretaris</label>
                                                <input x-model="printPernyataanSecretaryName" type="text"
                                                    placeholder="Deril Amrizal Kholid"
                                                    class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium">
                                            </div>
                                            <div>
                                                <label class="block font-semibold text-slate-600 mb-1">Seksi / Unit Sekretaris</label>
                                                <input x-model="printPernyataanSecretarySection" type="text"
                                                    placeholder="Seksi Penjaminan Kualitas Data"
                                                    class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Submit Action Bar -->
                                <div class="pt-4 flex items-center justify-end border-t border-slate-100">
                                    <a :href="'{{ route('admin.programs.print-surat-pernyataan') }}?mode=' + printPernyataanMode + '&year=' + printPernyataanYear + '&employee_name=' + encodeURIComponent(printPernyataanEmployeeName) + '&employee_id=' + printPernyataanEmployeeId + '&date_letter=' + encodeURIComponent(printPernyataanDateLetter) + '&effective_month=' + encodeURIComponent(printPernyataanEffectiveMonth) + '&effective_year=' + encodeURIComponent(printPernyataanEffectiveYear) + '&secretary_name=' + encodeURIComponent(printPernyataanSecretaryName) + '&secretary_section=' + encodeURIComponent(printPernyataanSecretarySection)"
                                        target="_blank"
                                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                                        <i data-lucide="printer" class="w-4 h-4 text-amber-400"></i>
                                        <span>Cetak Surat</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Right Column: Panduan Pengisian & Format Berkas -->
                            <div class="lg:col-span-4 space-y-4">
                                <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs space-y-3">
                                    <div class="flex items-center gap-2 text-gov-navy font-bold text-xs">
                                        <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                                        <span>Panduan Cetak Surat Pernyataan</span>
                                    </div>
                                    <ul class="text-xs text-slate-600 space-y-2 leading-relaxed">
                                        <li class="flex items-start gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 shrink-0"></span>
                                            <span>Dokumen dicetak di atas kertas <strong>HVS A4</strong> (orientasi Portrait).</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 shrink-0"></span>
                                            <span>Kolom <em>Nominal per Bulan</em> sengaja dikosongkan agar diisi manual oleh pegawai yang bersangkutan.</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 shrink-0"></span>
                                            <span>Tabel riwayat potongan akan otomatis tertampil jika pegawai memiliki riwayat pemotongan di tahun yang dipilih.</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5 shrink-0"></span>
                                            <span>Setelah ditandatangani, serahkan lembar fisik ke Sekretaris Masjid Salahuddin.</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="bg-blue-50/60 p-4 rounded-xl border border-blue-200/70 text-xs text-blue-900 space-y-1.5">
                                    <div class="font-bold flex items-center gap-1.5 text-blue-800">
                                        <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                                        <span>Pemberitahuan Sistem</span>
                                    </div>
                                    <p class="text-[11px] text-blue-800 leading-relaxed">
                                        Setiap lembar pernyataan yang telah ditandatangani dapat dipindai (scan) multi-halaman PDF dan diimpor ke sistem oleh Takmir/Admin untuk verifikasi otomatis.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(Auth::user()->canManage())
                        <!-- ============================================== -->
                        <!-- PANEL 2: IMPOR & VERIFIKASI SCAN PDF (ADMIN) -->
                        <!-- ============================================== -->
                        <div x-show="pernyataanModalTab === 'impor'" class="space-y-6">
                            @if(empty($scanPages))
                                <!-- Upload Area Card -->
                                <div class="bg-white p-6 rounded-xl border border-gov-border shadow-2xs space-y-4">
                                    <div class="p-4 bg-blue-50/70 rounded-xl border border-blue-200 text-blue-900 leading-relaxed text-xs">
                                        <div class="flex items-start gap-2.5">
                                            <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                                            <div>
                                                <span class="font-bold">Pemrosesan Dokumen :</span>
                                                <p class="mt-0.5 text-blue-800 text-[11px] leading-relaxed">
                                                    Unggah scan PDF formulir pemotongan tunjangan kinerja. Sistem akan mengekstrak NIP, nama pegawai, dan nominal komitmen zakat/infaq/qurban/santunan secara otomatis untuk diverifikasi.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- File Input / Dropzone -->
                                    <div class="border-2 border-dashed rounded-2xl p-10 text-center space-y-2.5 transition relative overflow-hidden group"
                                         x-data="{ isDragging: false }"
                                         :class="isDragging ? 'border-gov-navy bg-blue-50/70 ring-2 ring-gov-navy/20' : 'border-slate-300 hover:border-gov-navy bg-slate-50/50 hover:bg-slate-50'">
                                        
                                        <!-- Full-Surface Native File Input -->
                                        <input id="scanPernyataanFileInput"
                                               type="file"
                                               wire:model.live="scanPernyataanFile"
                                               accept="application/pdf"
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                               @dragenter="isDragging = true"
                                               @dragover.prevent="isDragging = true"
                                               @dragleave="isDragging = false"
                                               @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }">

                                        <!-- Loading Overlay during file upload & parsing -->
                                        <div wire:loading.flex wire:target="scanPernyataanFile, processScanPernyataan" class="absolute inset-0 bg-white/95 backdrop-blur-xs rounded-2xl flex-col items-center justify-center gap-2.5 z-20">
                                            <svg class="w-8 h-8 animate-spin text-gov-navy shrink-0" viewBox="0 0 24 24" fill="none">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-xs font-bold text-gov-textMain">Sedang membaca berkas PDF & mengekstrak data formulir...</span>
                                            <span class="text-[11px] text-slate-500">Mempersiapkan pindaian dan lembar verifikasi</span>
                                        </div>

                                        <!-- Dropzone Visual Elements -->
                                        <div class="pointer-events-none space-y-2">
                                            <div class="w-14 h-14 mx-auto rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200 shadow-2xs mb-2">
                                                <i data-lucide="file-up" class="w-7 h-7 text-blue-600"></i>
                                            </div>
                                            <p class="text-sm font-bold text-gov-textMain">
                                                <span class="text-gov-navy underline underline-offset-2">Klik untuk memilih berkas</span> atau tarik scan PDF ke sini
                                            </p>
                                            <p class="text-xs text-slate-500 font-medium">Berkas PDF multi-halaman hasil scanner dokumen fisik (maks. 20 MB)</p>
                                        </div>
                                    </div>

                                    @if($scanError)
                                        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl flex items-center gap-2">
                                            <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-600"></i>
                                            <span class="text-xs font-medium">{{ $scanError }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <!-- Verification Split-Screen Card -->
                                <div class="bg-white p-5 rounded-xl border border-gov-border shadow-2xs space-y-4"
                                     x-data="{
                                         activeScanIdx: @entangle('activeScanPageIndex').live,
                                         pdfBaseUrl: '{{ route('admin.programs.preview-scan-pernyataan') }}',
                                         totalPages: {{ count($scanPages) }},
                                         scanEmpNames: {{ json_encode(array_map(fn($p) => $p['nama'] ?? 'Pegawai', $scanPages)) }},
                                         hasImages: {{ !empty($scanPages[0]['image_path'] ?? null) ? 'true' : 'false' }},
                                         isPdfLoading: {{ empty($scanPages[0]['image_path'] ?? null) ? 'true' : 'false' }},
                                         pdfError: '',
                                         _isManualSelecting: false,
                                         _manualSelectTimer: null,
                                          _pdfRenderGen: 0,
                                          hasRenderedAnyPage: false,
                                         pageRotations: {},
                                         rotatePage(idx) {
                                             const current = this.pageRotations[idx] || 0;
                                             this.pageRotations = { ...this.pageRotations, [idx]: (current + 180) % 360 };
                                         },
                                         init() {
                                             this.$nextTick(() => {
                                                 if (window.createLucideIcons) window.createLucideIcons();
                                                 this.setupContinuousScrollObserver();
                                                 if (!this.hasImages) {
                                                      this.initContinuousPdf();
                                                  } else {
                                                      this.isPdfLoading = false;
                                                      this.pdfError = '';
                                                  }
                                             });
                                         },
                                         async initContinuousPdf() {
                                              if (this.hasImages) {
                                                  this.isPdfLoading = false;
                                                  this.pdfError = '';
                                                  return;
                                              }
                                              this._pdfRenderGen = (this._pdfRenderGen || 0) + 1;
                                              const currentGen = this._pdfRenderGen;
                                              this.isPdfLoading = true;
                                              this.pdfError = '';
                                              try {
                                                  let retries = 0;
                                                  while (typeof window.pdfjsLib === 'undefined' && retries < 40) {
                                                      if (this._pdfRenderGen !== currentGen) return;
                                                      await new Promise(r => setTimeout(r, 50));
                                                      retries++;
                                                  }
                                                  if (this._pdfRenderGen !== currentGen) return;
                                                  if (typeof window.pdfjsLib === 'undefined') {
                                                      this.isPdfLoading = false;
                                                      return;
                                                  }

                                                  window.pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset('vendor/pdfjs/pdf.worker.min.js') }}';
                                                  const loadingTask = window.pdfjsLib.getDocument({
                                                      url: this.pdfBaseUrl + (this.pdfBaseUrl.includes('?') ? '&' : '?') + 't=' + Date.now(),
                                                      cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/cmaps/',
                                                      cMapPacked: true,
                                                  });
                                                  const pdf = await loadingTask.promise;
                                                  if (this._pdfRenderGen !== currentGen) return;
                                                  const numPages = Math.min(pdf.numPages, this.totalPages);

                                                  for (let pNum = 1; pNum <= numPages; pNum++) {
                                                      if (this._pdfRenderGen !== currentGen) return;
                                                      const page = await pdf.getPage(pNum);
                                                      const canvas = document.getElementById('scan-pdf-canvas-' + (pNum - 1));
                                                      if (canvas) {
                                                          if (canvas._renderTask) {
                                                              try { canvas._renderTask.cancel(); } catch(e){}
                                                          }
                                                          const viewport = page.getViewport({ scale: 1.5 });
                                                          canvas.height = viewport.height;
                                                          canvas.width = viewport.width;
                                                          const ctx = canvas.getContext('2d');
                                                          ctx.setTransform(1, 0, 0, 1, 0, 0);
                                                          ctx.clearRect(0, 0, canvas.width, canvas.height);
                                                          canvas._renderTask = page.render({ canvasContext: ctx, viewport: viewport });
                                                          try {
                                                              await canvas._renderTask.promise;
                                                              this.hasRenderedAnyPage = true;
                                                          } catch (renderErr) {
                                                              if (renderErr?.name === 'RenderingCancelledException' || (renderErr?.message && renderErr.message.toLowerCase().includes('cancel'))) {
                                                                  // Normal cancellation between page/task switches, ignore
                                                              } else {
                                                                  throw renderErr;
                                                              }
                                                          }
                                                          canvas._renderTask = null;
                                                      }
                                                  }
                                                  if (this._pdfRenderGen === currentGen) {
                                                      this.pdfError = '';
                                                      this.setupContinuousScrollObserver();
                                                  }
                                              } catch (err) {
                                                  if (err?.name === 'RenderingCancelledException' || (err?.message && err.message.toLowerCase().includes('cancel'))) {
                                                      return;
                                                  }
                                                  console.error('PDF.js render error:', err);
                                                  if (this._pdfRenderGen === currentGen) {
                                                      this.pdfError = 'Gagal memuat pratinjau dokumen pindaian.';
                                                  }
                                              } finally {
                                                  if (this._pdfRenderGen === currentGen) {
                                                      this.isPdfLoading = false;
                                                  }
                                              }
                                          },
                                         setupContinuousScrollObserver() {
                                             const container = document.getElementById('pdfContinuousScrollContainer');
                                             if (!container) return;

                                             if (this._scrollListener) {
                                                 container.removeEventListener('scroll', this._scrollListener);
                                             }

                                             this._scrollListener = () => {
                                                 if (this._isManualSelecting) return;
                                                 const containerRect = container.getBoundingClientRect();
                                                 const targetY = containerRect.top + containerRect.height * 0.35;

                                                 let selectedIdx = null;
                                                 let minDistance = Infinity;

                                                 const cards = container.querySelectorAll('.scan-pdf-page-card');
                                                 cards.forEach((card) => {
                                                     const rect = card.getBoundingClientRect();
                                                     if (rect.top <= targetY && rect.bottom >= targetY) {
                                                         selectedIdx = parseInt(card.dataset.pageIndex, 10);
                                                     }
                                                     const cardCenter = (rect.top + rect.bottom) / 2;
                                                     const dist = Math.abs(cardCenter - targetY);
                                                     if (dist < minDistance) {
                                                         minDistance = dist;
                                                         if (selectedIdx === null) {
                                                             selectedIdx = parseInt(card.dataset.pageIndex, 10);
                                                         }
                                                     }
                                                 });

                                                 if (selectedIdx !== null && !isNaN(selectedIdx) && this.activeScanIdx !== selectedIdx) {
                                                      this.activeScanIdx = selectedIdx;
                                                 }
                                             };

                                             container.addEventListener('scroll', this._scrollListener, { passive: true });
                                             this._scrollListener();
                                         },
                                         prevPage() {
                                             if (this.activeScanIdx > 0) {
                                                 this.selectPage(this.activeScanIdx - 1);
                                             }
                                         },
                                         nextPage() {
                                             if (this.activeScanIdx < this.totalPages - 1) {
                                                 this.selectPage(this.activeScanIdx + 1);
                                             }
                                         },
                                         selectPage(idx) {
                                             if (idx < 0 || idx >= this.totalPages) return;
                                             this.activeScanIdx = idx;
                                             this._isManualSelecting = true;
                                             const card = document.getElementById('scan-page-card-' + idx);
                                             if (card) {
                                                 card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                             }
                                             clearTimeout(this._manualSelectTimer);
                                             this._manualSelectTimer = setTimeout(() => {
                                                 this._isManualSelecting = false;
                                             }, 450);
                                         }
                                     }">

                                    <!-- Split View Container (Side-by-Side di Desktop, Full Flow di Mobile) -->
                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
                                        <!-- Sisi Kiri: Pindaian Dokumen Asli -->
                                        <div class="lg:col-span-7 bg-slate-50 rounded-xl border border-gov-border p-3.5 flex flex-col justify-between relative shadow-2xs overflow-hidden"
                                             style="height: 640px; max-height: calc(100vh - 220px); min-height: 480px;">
                                            <!-- Header Controls -->
                                            <div class="flex items-center justify-between pb-2.5 border-b border-slate-200 text-xs text-gov-textMain shrink-0">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center">
                                                        <i data-lucide="file-search" class="w-3.5 h-3.5"></i>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-slate-800">Dokumen Asli</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button" @click="rotatePage(activeScanIdx)"
                                                        title="Putar Dokumen 180°"
                                                        class="h-7 px-2.5 rounded-lg bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 text-xs font-semibold inline-flex items-center gap-1 border border-slate-200 shadow-2xs transition cursor-pointer">
                                                        <i data-lucide="rotate-cw" class="w-3.5 h-3.5 text-slate-500"></i>
                                                        <span>Putar</span>
                                                    </button>
                                                    <a :href="pdfBaseUrl + '#page=' + (activeScanIdx + 1)" target="_blank"
                                                        class="h-7 px-3 rounded-lg bg-white hover:bg-slate-100 text-blue-700 hover:text-blue-900 text-xs font-semibold inline-flex items-center gap-1.5 border border-slate-200 shadow-2xs transition cursor-pointer">
                                                        <i data-lucide="external-link" class="w-3.5 h-3.5 text-blue-600"></i>
                                                        <span class="hidden sm:inline">Buka di Tab Baru</span>
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Continuous Scroll Container for All PDF Pages -->
                                            <div wire:ignore
                                                 wire:key="scan-pdf-continuous-container-{{ count($scanPages) }}"
                                                 id="pdfContinuousScrollContainer"
                                                 class="flex-1 min-h-0 w-full overflow-y-auto space-y-4 pr-1 scroll-smooth py-2.5 relative">
                                                
                                                <!-- Loading Placeholder -->
                                                <div x-show="isPdfLoading && !hasRenderedAnyPage" class="p-8 text-center text-slate-400 space-y-2">
                                                    <svg class="w-8 h-8 animate-spin mx-auto text-gov-navy" viewBox="0 0 24 24" fill="none">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <p class="text-xs font-semibold">Merender pratinjau seluruh halaman scan...</p>
                                                </div>

                                                <!-- Error Notice -->
                                                <div x-show="!hasImages && pdfError" class="p-4 bg-rose-50 text-rose-700 rounded-xl border border-rose-200 text-xs" x-text="pdfError"></div>

                                                <!-- All Pages Stacked Continuously -->
                                                @foreach($scanPages as $pIdx => $pageData)
                                                <div id="scan-page-card-{{ $pIdx }}"
                                                     data-page-index="{{ $pIdx }}"
                                                     @click="selectPage({{ $pIdx }})"
                                                     class="scan-pdf-page-card bg-white rounded-xl border p-2 shadow-2xs transition-all duration-150 cursor-pointer"
                                                     :class="activeScanIdx === {{ $pIdx }} ? 'border-gov-navy ring-2 ring-gov-navy/30 shadow-md' : 'border-slate-200 hover:border-slate-300 opacity-85 hover:opacity-100'">
                                                    <!-- Mini Card Header with Page Number & Name -->
                                                    <div class="flex items-center justify-between px-1.5 pb-1.5 mb-1.5 border-b border-slate-100 text-[11px]">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="w-5 h-5 rounded-full flex items-center justify-center font-bold text-[10px]"
                                                                  :class="activeScanIdx === {{ $pIdx }} ? 'bg-gov-navy text-white' : 'bg-slate-100 text-slate-700'">
                                                                {{ $pIdx + 1 }}
                                                            </span>
                                                            <span class="font-bold text-slate-800 truncate max-w-[200px]">
                                                                {{ $pageData['nama'] ?? 'Pegawai ' . ($pIdx + 1) }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" @click.stop="$wire.removeScanPage({{ $pIdx }})"
                                                                    title="Hapus Lembar Halaman Ini"
                                                                    class="text-[10px] font-semibold text-rose-500 hover:text-rose-700 hover:underline cursor-pointer">
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Canvas or Server-rendered Image Preview -->
                                                    <div class="overflow-hidden flex items-center justify-center bg-slate-100/50 rounded-lg min-h-[300px]">
                                                        @if(!empty($pageData['image_path']))
                                                            <img src="{{ asset('storage/' . $pageData['image_path']) }}"
                                                                 alt="Halaman {{ $pIdx + 1 }}"
                                                                 @load="setupContinuousScrollObserver()"
                                                                 class="max-w-full h-auto object-contain rounded shadow-xs transition-transform duration-200"
                                                                 :style="'transform: rotate(' + (pageRotations[{{ $pIdx }}] || 0) + 'deg);'">
                                                        @else
                                                            <canvas id="scan-pdf-canvas-{{ $pIdx }}"
                                                                    class="max-w-full h-auto shadow-xs rounded bg-white transition-transform duration-200"
                                                                    :style="'transform: rotate(' + (pageRotations[{{ $pIdx }}] || 0) + 'deg);'"></canvas>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <!-- Bottom Mini Navigation Pill -->
                                            <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs text-slate-600 shrink-0">
                                                <button type="button" @click="prevPage()"
                                                    :disabled="activeScanIdx === 0"
                                                    class="px-2.5 py-1 rounded-md border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 transition font-medium cursor-pointer">
                                                    Sebelumnya
                                                </button>
                                                <button type="button" @click="nextPage()"
                                                    :disabled="activeScanIdx >= totalPages - 1"
                                                    class="px-2.5 py-1 rounded-md border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 transition font-medium cursor-pointer">
                                                    Berikutnya
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Sisi Kanan: Form Hasil Ekstraksi & Verifikasi -->
                                        <div class="lg:col-span-5 bg-white rounded-xl border border-gov-border p-4 shadow-2xs flex flex-col justify-between overflow-hidden"
                                             style="height: 640px; max-height: calc(100vh - 220px); min-height: 480px;">
                                            <!-- Header Form -->
                                            <div class="flex items-center justify-between pb-2 border-b border-slate-200 text-xs shrink-0">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                                        <i data-lucide="check-square" class="w-3.5 h-3.5"></i>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-slate-800">Verifikasi Data</span>
                                                    </div>
                                                </div>
                                                <span class="text-[11px] font-semibold text-slate-500">
                                                    Lembar <span x-text="activeScanIdx + 1"></span> / {{ count($scanPages) }}
                                                </span>
                                            </div>

                                            <!-- Dynamic Form Stack -->
                                            <div class="flex-1 min-h-0 overflow-y-auto space-y-4 pr-1 pt-1">
                                                @foreach($scanPages as $index => $page)
                                                <div x-show="activeScanIdx === {{ $index }}"
                                                     x-cloak
                                                     wire:key="scan-page-form-{{ $index }}"
                                                     x-data="{
                                                         nip: '{{ $page['nip'] ?? '' }}',
                                                         nama: '{{ addslashes($page['nama'] ?? '') }}',
                                                         zakat: {{ (int)($page['commitments']['zakat'] ?? $page['programs']['zakat'] ?? 0) }},
                                                         infaq: {{ (int)($page['commitments']['infaq'] ?? $page['programs']['infaq'] ?? 0) }},
                                                         qurban: {{ (int)($page['commitments']['qurban'] ?? $page['programs']['qurban'] ?? 0) }},
                                                         yatim: {{ (int)($page['commitments']['yatim'] ?? $page['programs']['yatim'] ?? 0) }},
                                                         dhuafa: {{ (int)($page['commitments']['dhuafa'] ?? $page['programs']['dhuafa'] ?? 0) }},
                                                         getTotal() {
                                                             return (this.zakat || 0) + (this.infaq || 0) + (this.qurban || 0) + (this.yatim || 0) + (this.dhuafa || 0);
                                                         }
                                                     }"
                                                     class="space-y-3.5">

                                                    <!-- Profile Data Box -->
                                                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2.5">
                                                        <div>
                                                            <label class="block font-semibold text-slate-600 text-xs mb-1">NIP Pegawai (18 digit)</label>
                                                            <input type="text"
                                                                wire:model="scanPages.{{ $index }}.nip"
                                                                x-model="nip"
                                                                placeholder="19xxxxxxxxxxxxxxxx"
                                                                class="w-full h-8 px-2.5 rounded-lg border border-slate-300 bg-white text-xs font-mono font-bold text-gov-textMain focus:border-gov-navy focus:outline-none">
                                                        </div>

                                                        <div>
                                                            <label class="block font-semibold text-slate-600 text-xs mb-1">Nama Lengkap Pegawai</label>
                                                            <input type="text"
                                                                wire:model="scanPages.{{ $index }}.nama"
                                                                x-model="nama"
                                                                placeholder="Nama Pegawai Sesuai Lembar Formulir"
                                                                class="w-full h-8 px-2.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-gov-textMain focus:border-gov-navy focus:outline-none">
                                                        </div>
                                                    </div>

                                                    <!-- 5 Canonical Programs Input Grid -->
                                                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2.5">
                                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-200">
                                                            <span class="font-bold text-xs text-gov-textMain">Nominal / Bulan</span>
                                                            <span class="text-[11px] text-slate-400">Ketik angka murni</span>
                                                        </div>

                                                        <div class="space-y-2">
                                                            <!-- 1. Zakat -->
                                                            <div class="grid grid-cols-2 items-center gap-2">
                                                                <span class="font-medium text-slate-700 text-xs">1. Zakat</span>
                                                                <div class="relative">
                                                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                                    <input type="text" inputmode="numeric"
                                                                        :value="window.formatRibuan(zakat)"
                                                                        @input="window.handleRibuanInput($event.target, (clean) => { zakat = clean ? parseInt(clean) : 0; $wire.set('scanPages.{{ $index }}.commitments.zakat', zakat, false); $wire.set('scanPages.{{ $index }}.programs.zakat', zakat, false); })"
                                                                        placeholder="0"
                                                                        class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                                </div>
                                                            </div>

                                                            <!-- 2. Infaq & Sedekah -->
                                                            <div class="grid grid-cols-2 items-center gap-2">
                                                                <span class="font-medium text-slate-700 text-xs">2. Infaq & Sedekah</span>
                                                                <div class="relative">
                                                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                                    <input type="text" inputmode="numeric"
                                                                        :value="window.formatRibuan(infaq)"
                                                                        @input="window.handleRibuanInput($event.target, (clean) => { infaq = clean ? parseInt(clean) : 0; $wire.set('scanPages.{{ $index }}.commitments.infaq', infaq, false); $wire.set('scanPages.{{ $index }}.programs.infaq', infaq, false); })"
                                                                        placeholder="0"
                                                                        class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                                </div>
                                                            </div>

                                                            <!-- 3. Tabungan Qurban -->
                                                            <div class="grid grid-cols-2 items-center gap-2">
                                                                <span class="font-medium text-slate-700 text-xs">3. Tabungan Qurban</span>
                                                                <div class="relative">
                                                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                                    <input type="text" inputmode="numeric"
                                                                        :value="window.formatRibuan(qurban)"
                                                                        @input="window.handleRibuanInput($event.target, (clean) => { qurban = clean ? parseInt(clean) : 0; $wire.set('scanPages.{{ $index }}.commitments.qurban', qurban, false); $wire.set('scanPages.{{ $index }}.programs.qurban', qurban, false); })"
                                                                        placeholder="0"
                                                                        class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                                </div>
                                                            </div>

                                                            <!-- 4. Santunan Anak Yatim -->
                                                            <div class="grid grid-cols-2 items-center gap-2">
                                                                <span class="font-medium text-slate-700 text-xs">4. Santunan Anak Yatim</span>
                                                                <div class="relative">
                                                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                                    <input type="text" inputmode="numeric"
                                                                        :value="window.formatRibuan(yatim)"
                                                                        @input="window.handleRibuanInput($event.target, (clean) => { yatim = clean ? parseInt(clean) : 0; $wire.set('scanPages.{{ $index }}.commitments.yatim', yatim, false); $wire.set('scanPages.{{ $index }}.programs.yatim', yatim, false); })"
                                                                        placeholder="0"
                                                                        class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                                </div>
                                                            </div>

                                                            <!-- 5. Santunan Dhuafa -->
                                                            <div class="grid grid-cols-2 items-center gap-2">
                                                                <span class="font-medium text-slate-700 text-xs">5. Santunan Dhuafa</span>
                                                                <div class="relative">
                                                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                                    <input type="text" inputmode="numeric"
                                                                        :value="window.formatRibuan(dhuafa)"
                                                                        @input="window.handleRibuanInput($event.target, (clean) => { dhuafa = clean ? parseInt(clean) : 0; $wire.set('scanPages.{{ $index }}.commitments.dhuafa', dhuafa, false); $wire.set('scanPages.{{ $index }}.programs.dhuafa', dhuafa, false); })"
                                                                        placeholder="0"
                                                                        class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Live Total Calculation in Rupiah -->
                                                        <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                                                            <span class="font-bold text-slate-700">Total:</span>
                                                            <span class="font-extrabold text-gov-navy text-sm font-mono"
                                                                  x-text="'Rp ' + (window.formatRibuan(getTotal()) || '0')">
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <!-- Bottom Action Bar -->
                                            <div class="pt-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs shrink-0">
                                                <button type="button" wire:click="resetScanPernyataan"
                                                    class="w-full sm:w-auto px-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-800 font-medium text-xs transition cursor-pointer inline-flex items-center justify-center gap-1.5">
                                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-400"></i>
                                                    <span>Unggah Ulang PDF Lain</span>
                                                </button>

                                                <button type="button" wire:click="exportScanPernyataanExcel"
                                                    wire:loading.attr="disabled"
                                                    class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer inline-flex items-center justify-center gap-1.5">
                                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-400"></i>
                                                    <span>Unduh Excel (.xlsx)</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                @if(Auth::user()->canManage())
                <!-- ==================================================== -->
                <!-- SUB-VIEW 4: SETORAN KANTOR (KLIRING TRANSFER KANTOR) -->
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
                                    class="h-9 px-3.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer inline-flex items-center gap-1.5 self-start sm:self-auto">
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
                                        <x-empty-state 
                                            :isTable="true" 
                                            colspan="4" 
                                            icon="landmark" 
                                            title="Tidak ada data transfer kliring" 
                                            subtitle="Belum ada riwayat penerimaan transfer yang sesuai dengan filter pencarian ini." 
                                        />
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
                            <x-empty-state 
                                icon="landmark" 
                                title="Tidak ada data transfer kliring" 
                                subtitle="Belum ada riwayat penerimaan transfer yang sesuai dengan filter pencarian ini." 
                            />
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
