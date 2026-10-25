<!-- ======================================================== -->
<!-- FLOATING ACTION BUTTON (FAB) & BOTTOM ACTION SHEET (MOBILE) -->
<!-- ======================================================== -->

<!-- 1. Circular (+) Floating Action Button (Mobile Only) -->
<div class="fixed bottom-6 right-5 z-40 sm:hidden"
    x-show="!['settings', 'statistics', 'statistik', 'saran'].includes(activeTab) && !(activeTab === 'programs' && socialSubTab === 'pernyataan')"
    x-cloak>
    <button type="button"
        @click="openMobileActionSheet()"
        class="w-14 h-14 rounded-full bg-gov-navy text-white flex items-center justify-center shadow-xl shadow-slate-900/35 hover:bg-gov-navyHover active:scale-95 transition-all duration-200 cursor-pointer"
        title="Menu Tindakan Cepat"
        aria-label="Menu Tindakan Cepat">
        <svg class="w-7 h-7 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
    </button>
</div>

<!-- 2. Dark Backdrop Overlay -->
<div x-show="mobileActionSheetOpen" x-cloak
    @click="closeMobileActionSheet()"
    x-transition:enter="transition-opacity ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 sm:hidden"
    style="display: none;">
</div>

<!-- 3. Bottom Sheet Drawer Panel -->
<div x-show="mobileActionSheetOpen" x-cloak
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="translate-y-full"
    x-transition:enter-end="translate-y-0"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="translate-y-0"
    x-transition:leave-end="translate-y-full"
    class="fixed bottom-0 inset-x-0 z-50 sm:hidden flex flex-col justify-end"
    style="display: none;">

    <div class="w-full max-w-lg mx-auto bg-white rounded-t-3xl shadow-2xl border-t border-slate-200 p-5 pb-8 space-y-4 max-h-[85vh] overflow-y-auto">
        <!-- Center Drag Indicator Handle -->
        <div class="w-12 h-1 bg-slate-300 rounded-full mx-auto mb-1"></div>

        <!-- Sheet Header with Title & Dismiss Button -->
        <div class="flex items-center justify-between pb-1">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Menu Tindakan Cepat</h3>
                <p class="text-xs text-slate-500 mt-0.5" x-text="getMobileActionSubtitle()"></p>
            </div>
            <button type="button" @click="closeMobileActionSheet()" 
                class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer"
                title="Tutup Menu">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- ============================================== -->
        <!-- TAB 1: KEGIATAN (Kajian, Jumat, Agenda, ODOJ) -->
        <!-- ============================================== -->
        <template x-if="activeTab === 'kegiatan' || activeTab === 'kajian' || activeTab === 'agenda'">
            <div class="space-y-3">
                <!-- Subtab: Pekanan -->
                <div x-show="kegiatanSubTab === 'pekanan'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); openCreateKajian('pekanan')"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Jadwal Baru</span>
                        </button>
                    @endif

                    <div class="grid grid-cols-2 gap-2.5">
                        @if(Auth::user()->canManage())
                            <button type="button" @click="closeMobileActionSheet(); showImportKajianModal = true; $wire.openImportKajian('pekanan')"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                                <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-800 leading-tight">Import File</span>
                            </button>
                        @endif

                        <a :href="'{{ route('admin.kajian.export-excel') }}?type=pekanan&month=' + $wire.kajianMonthFilter + '&year=' + $wire.kajianYearFilter"
                            @click="closeMobileActionSheet()"
                            class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-800 leading-tight">Download Excel</span>
                        </a>

                        <button type="button" @click="closeMobileActionSheet(); showUstadzModal = true; $wire.set('showUstadzModal', true, false)"
                            class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-800 leading-tight">Direktori Asatidz</span>
                        </button>

                        <a :href="'{{ route('admin.kajian.download-pdf') }}?type=pekanan&month=' + $wire.kajianMonthFilter + '&year=' + $wire.kajianYearFilter"
                            @click="closeMobileActionSheet()"
                            class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-800 leading-tight">Unduh PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Subtab: Jumat -->
                <div x-show="kegiatanSubTab === 'jumat'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); openCreateKajian('jumat')"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Petugas Jumat</span>
                        </button>
                    @endif

                    <div class="grid grid-cols-2 gap-2.5">
                        @if(Auth::user()->canManage())
                            <button type="button" @click="closeMobileActionSheet(); showImportKajianModal = true; $wire.openImportKajian('jumat')"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                                <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-800 leading-tight">Import File</span>
                            </button>
                        @endif

                        <a :href="'{{ route('admin.kajian.export-excel') }}?type=jumat&month=' + $wire.kajianMonthFilter + '&year=' + $wire.kajianYearFilter"
                            @click="closeMobileActionSheet()"
                            class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-800 leading-tight">Download Excel</span>
                        </a>

                        <button type="button" @click="closeMobileActionSheet(); showUstadzModal = true; $wire.set('showUstadzModal', true, false)"
                            class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-800 leading-tight">Data Ustadz</span>
                        </button>

                        <a :href="'{{ route('admin.kajian.download-pdf') }}?type=jumat&month=' + $wire.kajianMonthFilter + '&year=' + $wire.kajianYearFilter"
                            @click="closeMobileActionSheet()"
                            class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-slate-800 leading-tight">Unduh PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Subtab: Agenda Akbar -->
                <div x-show="kegiatanSubTab === 'agenda'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); openCreateAgenda()"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Agenda Akbar</span>
                        </button>
                    @endif

                    <div class="grid grid-cols-3 gap-2">
                        @if(Auth::user()->canManage())
                            <button type="button" @click="closeMobileActionSheet(); showImportAgendaModal = true"
                                class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0 mb-1">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                </div>
                                <span class="text-[11px] font-semibold text-slate-800 leading-tight">Import File</span>
                            </button>
                        @endif

                        <a href="{{ route('admin.agenda.export-excel') }}" @click="closeMobileActionSheet()"
                            class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0 mb-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-800 leading-tight">Download Excel</span>
                        </a>

                        <a href="{{ route('admin.agenda.export-pdf') }}" target="_blank" @click="closeMobileActionSheet()"
                            class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0 mb-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-800 leading-tight">Cetak PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Subtab: ODOJ -->
                <div x-show="kegiatanSubTab === 'odoj'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); openAssignOdoj(1, '')"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Atur Juz Harian</span>
                        </button>

                        <div class="grid grid-cols-2 gap-2.5">
                            <button type="button" @click="closeMobileActionSheet(); $wire.setAllOdojStatus('Selesai')"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                        <polyline points="24 6 13 17 8 12"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-slate-800 leading-tight block">Semua Selesai</span>
                                    <span class="text-[10px] text-slate-400 leading-tight block">Tandai 30 Juz</span>
                                </div>
                            </button>

                            <button type="button" @click="closeMobileActionSheet(); $wire.setAllOdojStatus('Belum')"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                        <polyline points="3 3 3 8 8 8"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-slate-800 leading-tight block">Reset Belum</span>
                                    <span class="text-[10px] text-slate-400 leading-tight block">Kembalikan status</span>
                                </div>
                            </button>
                        </div>
                    @endif

                    <button type="button" @click="closeMobileActionSheet(); copyOdojToClipboard({{ Js::from($odojWhatsappText ?? '') }})"
                        class="w-full flex items-center gap-3 p-3 rounded-xl border border-emerald-200 bg-emerald-50/60 hover:bg-emerald-50 active:bg-emerald-100 transition cursor-pointer text-left">
                        <div class="w-9 h-9 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-emerald-900 block">Salin Rekap WA</span>
                            <span class="text-[11px] text-emerald-700">Salin daftar tilawah juz ke clipboard</span>
                        </div>
                    </button>
                </div>
            </div>
        </template>

        <!-- ============================================== -->
        <!-- TAB 2: PETUGAS (Imam & Muadzin) -->
        <!-- ============================================== -->
        <template x-if="activeTab === 'petugas'">
            <div class="space-y-3">
                @if(Auth::user()->canManage())
                    <button type="button" @click="closeMobileActionSheet(); openCreatePrayerDuty()"
                        class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Tambah Petugas Shalat</span>
                    </button>
                @endif

                <div class="grid grid-cols-3 gap-2">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); showImportDutyModal = true; $wire.openImportDuty()"
                            class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0 mb-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-800 leading-tight">Import File</span>
                        </button>
                    @endif

                    <a :href="'{{ route('admin.petugas.export-excel') }}?week=' + $wire.prayerDutyFilterWeek + '&month=' + $wire.prayerDutyFilterMonth + '&year=' + $wire.prayerDutyFilterYear"
                        @click="closeMobileActionSheet()"
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0 mb-1">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-800 leading-tight">Download Excel</span>
                    </a>

                    <a :href="'{{ route('admin.petugas.download-pdf') }}?week=' + $wire.prayerDutyFilterWeek + '&month=' + $wire.prayerDutyFilterMonth + '&year=' + $wire.prayerDutyFilterYear"
                        @click="closeMobileActionSheet()"
                        class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0 mb-1">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-800 leading-tight">Unduh PDF</span>
                    </a>
                </div>
            </div>
        </template>

        <!-- ============================================== -->
        <!-- TAB 3: FINANCE (Kas Utama & Kategori) -->
        <!-- ============================================== -->
        <template x-if="activeTab === 'finance'">
            <div class="space-y-3">
                <!-- Subtab: Kas Utama -->
                <div x-show="financeSubTab === 'utama'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); openCreateFinance()"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Catat Transaksi Kas</span>
                        </button>
                    @endif

                    <div class="grid grid-cols-3 gap-2">
                        @if(Auth::user()->canManage())
                            <button type="button" @click="closeMobileActionSheet(); showFinanceImportModal = true"
                                class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0 mb-1">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                </div>
                                <span class="text-[11px] font-semibold text-slate-800 leading-tight">Import File</span>
                            </button>
                        @endif

                        <a href="{{ route('admin.finance.export-excel') }}" @click="closeMobileActionSheet()"
                            class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0 mb-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-800 leading-tight">Download Excel</span>
                        </a>

                        <button type="button" @click="closeMobileActionSheet(); showPrintFinanceModal = true; $wire.openPrintFinanceModal()"
                            class="flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0 mb-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-800 leading-tight">Cetak PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Subtab: Kategori -->
                <div x-show="financeSubTab === 'kategori'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); openCreateFinanceCategory()"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Pos Anggaran</span>
                        </button>
                    @endif
                </div>
            </div>
        </template>

        <!-- ============================================== -->
        <!-- TAB 4: PROGRAMS (Program Sosial & Infaq) -->
        <!-- ============================================== -->
        <template x-if="activeTab === 'programs'">
            <div class="space-y-3">
                <!-- Subtab: Katalog -->
                <div x-show="socialSubTab === 'katalog'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); openCreateSocialProgram()"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Program Sosial</span>
                        </button>
                    @endif

                    <div class="grid grid-cols-2 gap-2.5">
                        @if(Auth::user()->canManage())
                            <button type="button" @click="closeMobileActionSheet(); showImportPotonganModal = true; openImportPotonganModal()"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                                <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-800 leading-tight">Import File</span>
                            </button>

                            <button type="button" @click="closeMobileActionSheet(); showExportPotonganModal = true"
                                class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-800 leading-tight">Export Data</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Subtab: Peserta -->
                <div x-show="socialSubTab === 'peserta'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); showExportPotonganModal = true"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            <span>Download Rekap Peserta</span>
                        </button>
                    @endif
                </div>


                <!-- Subtab: Setoran Kantor -->
                <div x-show="socialSubTab === 'setoran'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); showImportPotonganModal = true"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            <span>Impor Potongan Payroll</span>
                        </button>
                    @endif

                    <button type="button" @click="closeMobileActionSheet(); showExportPotonganModal = true"
                        class="w-full flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl border border-slate-400 hover:border-slate-500 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                        <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                        <span class="text-xs font-semibold text-slate-800">Export Setoran</span>
                    </button>
                </div>
            </div>
        </template>

        <!-- ============================================== -->
        <!-- TAB 5: USERS (Pengguna, Pegawai, Takmir) -->
        <!-- ============================================== -->
        <template x-if="activeTab === 'users'">
            <div class="space-y-3">
                <!-- Subtab: Pengguna -->
                <div x-show="userSubTab === 'pengguna'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); openCreateUser()"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Pengguna</span>
                        </button>
                    @endif
                </div>

                <!-- Subtab: Pegawai -->
                <div x-show="userSubTab === 'pegawai'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); showEmployeeModal = true; $wire.openCreateEmployeeModal()"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Tambah Pegawai Baru</span>
                        </button>
                    @endif

                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); showImportEmployeeModal = true; $wire.openImportEmployeeModal()"
                            class="w-full flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl border border-slate-400 hover:border-slate-500 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-center">
                            <svg class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            <span class="text-xs font-semibold text-slate-800">Impor Spreadsheet</span>
                        </button>
                    @endif
                </div>

                <!-- Subtab: Kepengurusan -->
                <div x-show="userSubTab === 'kepengurusan'" class="space-y-3">
                    @if(Auth::user()->canManage())
                        <button type="button" @click="closeMobileActionSheet(); $wire.openTemplateKepModal()"
                            class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            <span>Template Surat KEP Takmir</span>
                        </button>
                    @endif
                </div>
            </div>
        </template>

        <!-- ============================================== -->
        <!-- TAB 6: GALERI KEGIATAN -->
        <!-- ============================================== -->
        <template x-if="activeTab === 'galeri' || activeTab === 'gallery'">
            <div class="space-y-3">
                @if(Auth::user()->canManage())
                    <button type="button" @click="closeMobileActionSheet(); $wire.openCreateGalleryModal()"
                        class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Tambah Foto Galeri</span>
                    </button>
                @endif
            </div>
        </template>

        <!-- ============================================== -->
        <!-- TAB 8: DASHBOARD (Shortcuts) -->
        <!-- ============================================== -->
        <template x-if="activeTab === 'dashboard'">
            <div class="space-y-3">
                <button type="button" @click="closeMobileActionSheet(); activeTab = 'kegiatan'; kegiatanSubTab = 'pekanan'; openCreateKajian('pekanan')"
                    class="w-full py-3 px-4 rounded-xl bg-gov-navy hover:bg-gov-navyHover active:scale-[0.99] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>+ Tambah Jadwal Kajian</span>
                </button>

                <div class="grid grid-cols-2 gap-2.5">
                    <button type="button" @click="closeMobileActionSheet(); activeTab = 'finance'; openCreateFinance()"
                        class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-800 leading-tight">Catat Transaksi</span>
                    </button>

                    <button type="button" @click="closeMobileActionSheet(); activeTab = 'petugas'; openCreatePrayerDuty()"
                        class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                        <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-800 leading-tight">Petugas Shalat</span>
                    </button>

                    <button type="button" @click="closeMobileActionSheet(); activeTab = 'programs'; openCreateSocialProgram()"
                        class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                        <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-800 leading-tight">Program Sosial</span>
                    </button>

                    <button type="button" @click="closeMobileActionSheet(); activeTab = 'users'; userSubTab = 'pegawai'; showEmployeeModal = true; $wire.openCreateEmployeeModal()"
                        class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:bg-slate-100 transition cursor-pointer text-left">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="2"/><line x1="15" y1="8" x2="17" y2="8"/><line x1="15" y1="12" x2="17" y2="12"/><line x1="7" y1="16" x2="17" y2="16"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-800 leading-tight">Tambah Pegawai</span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
