<!-- ======================================================== -->
<!-- BOTTOM DRAWER: MENU LENGKAP SELURUH MODUL (MOBILE)         -->
<!-- ======================================================== -->
<div x-show="mobileNavDrawerOpen" x-cloak
    @click="if (window.isBackdropClick ? window.isBackdropClick($event, $el) : ($event.target === $el)) { mobileNavDrawerOpen = false; }"
    x-transition:enter="transition-opacity ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 md:hidden flex flex-col justify-end"
    style="display: none; z-index: 9999; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
    role="dialog" aria-modal="true" aria-label="Menu Lengkap Seluruh Modul">

    <div @click.stop
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="w-full max-w-lg mx-auto bg-white rounded-t-3xl shadow-2xl border-t border-slate-200 p-5 pb-8 space-y-4 max-h-[85vh] overflow-y-auto notula-scrollable relative"
        style="z-index: 10000; background-color: #ffffff;">

        <!-- Drag Indicator Bar -->
        <div class="w-12 h-1 bg-slate-300 rounded-full mx-auto mb-1"></div>

        <!-- Sheet Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gov-navy text-amber-400 font-extrabold flex items-center justify-center text-xs shadow-xs shrink-0">
                    MS
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 leading-tight">Menu Navigasi Modul</h3>
                    <p class="text-[11px] text-slate-500">Masjid Salahuddin DJP</p>
                </div>
            </div>
            <button type="button" @click="mobileNavDrawerOpen = false"
                class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer"
                title="Tutup Menu">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- 2-Column Grid of All Modules -->
        <div class="grid grid-cols-2 gap-2.5 pt-1 text-left">

            <!-- 1. Dashboard (Row #1) -->
            <a href="{{ url('/admin/dashboard') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ $currentTab === 'dashboard' ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ $currentTab === 'dashboard' ? 'bg-amber-400 text-gov-navy' : 'bg-blue-50 text-blue-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Dashboard</div>
                    <div class="text-[10px] text-slate-500 truncate">Panel Utama</div>
                </div>
            </a>

            <!-- 2. Jadwal Kegiatan -->
            <a href="{{ url('/admin/kegiatan') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ in_array($currentTab, ['kegiatan', 'kajian', 'agenda']) ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ in_array($currentTab, ['kegiatan', 'kajian', 'agenda']) ? 'bg-amber-400 text-gov-navy' : 'bg-emerald-50 text-emerald-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Jadwal Kegiatan</div>
                    <div class="text-[10px] text-slate-500 truncate">Kajian & Akbar</div>
                </div>
            </a>

            <!-- 3. Petugas Shalat -->
            <a href="{{ url('/admin/petugas') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ $currentTab === 'petugas' ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ $currentTab === 'petugas' ? 'bg-amber-400 text-gov-navy' : 'bg-indigo-50 text-indigo-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                        <line x1="12" y1="19" x2="12" y2="23"></line>
                        <line x1="8" y1="23" x2="16" y2="23"></line>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Petugas Shalat</div>
                    <div class="text-[10px] text-slate-500 truncate">Imam & Muadzin</div>
                </div>
            </a>

            <!-- 4. Kas & Keuangan -->
            <a href="{{ url('/admin/finance') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ $currentTab === 'finance' ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ $currentTab === 'finance' ? 'bg-amber-400 text-gov-navy' : 'bg-teal-50 text-teal-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path>
                        <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path>
                        <circle cx="18" cy="15" r="1"></circle>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Kas & Keuangan</div>
                    <div class="text-[10px] text-slate-500 truncate">Infaq & Saldo</div>
                </div>
            </a>

            <!-- 5. Program Sosial -->
            <a href="{{ url('/admin/programs') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ $currentTab === 'programs' ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ $currentTab === 'programs' ? 'bg-amber-400 text-gov-navy' : 'bg-rose-50 text-rose-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Program Sosial</div>
                    <div class="text-[10px] text-slate-500 truncate">ZISWAF & Peserta</div>
                </div>
            </a>

            <!-- 6. Galeri Kegiatan -->
            <a href="{{ url('/admin/galeri') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ in_array($currentTab, ['galeri', 'gallery']) ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ in_array($currentTab, ['galeri', 'gallery']) ? 'bg-amber-400 text-gov-navy' : 'bg-purple-50 text-purple-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <path d="m21 15-5-5L5 21"></path>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Galeri Kegiatan</div>
                    <div class="text-[10px] text-slate-500 truncate">Dokumentasi Foto</div>
                </div>
            </a>

            <!-- 7. Saran & Kritik -->
            @unless(Auth::user()->isJamaah())
            <a href="{{ url('/admin/saran') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ $currentTab === 'saran' ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ $currentTab === 'saran' ? 'bg-amber-400 text-gov-navy' : 'bg-cyan-50 text-cyan-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Saran & Kritik</div>
                    <div class="text-[10px] text-slate-500 truncate">Suara Jamaah</div>
                </div>
            </a>
            @endunless

            <!-- 8. Pengaturan & Layar TV -->
            @unless(Auth::user()->isJamaah())
            <a href="{{ url('/admin/settings') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ $currentTab === 'settings' ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ $currentTab === 'settings' ? 'bg-amber-400 text-gov-navy' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Pengaturan</div>
                    <div class="text-[10px] text-slate-500 truncate">Layar TV & Masjid</div>
                </div>
            </a>
            @endunless

            <!-- 9. Manajemen Pengguna & Kepengurusan -->
            @unless(Auth::user()->isJamaah())
            <a href="{{ url('/admin/users') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ $currentTab === 'users' ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ $currentTab === 'users' ? 'bg-amber-400 text-gov-navy' : 'bg-sky-50 text-sky-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Pengguna & SK</div>
                    <div class="text-[10px] text-slate-500 truncate">Takmir & Pegawai</div>
                </div>
            </a>
            @endunless

            <!-- 10. Statistik Pengunjung -->
            @unless(Auth::user()->isJamaah())
            <a href="{{ url('/admin/statistics') }}" wire:navigate @click="mobileNavDrawerOpen = false"
                class="flex items-center gap-2.5 p-2.5 rounded-xl border transition cursor-pointer {{ in_array($currentTab, ['statistics', 'statistik']) ? 'border-amber-400 bg-amber-50/40 text-gov-navy' : 'border-slate-200/90 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-800' }}">
                <div class="w-8 h-8 rounded-lg {{ in_array($currentTab, ['statistics', 'statistik']) ? 'bg-amber-400 text-gov-navy' : 'bg-violet-50 text-violet-700' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                </div>
                <div class="leading-tight overflow-hidden">
                    <div class="text-xs font-bold truncate">Statistik</div>
                    <div class="text-[10px] text-slate-500 truncate">Trafik Pengunjung</div>
                </div>
            </a>
            @endunless

        </div>

        <!-- Footer Actions (Portal Jamaah & Logout) -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3 text-xs">
            <a href="{{ url('/') }}" target="_blank"
                class="inline-flex items-center gap-1.5 py-2 px-3 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50 hover:bg-slate-100 font-semibold text-slate-700 transition">
                <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                    <polyline points="15 3 21 3 21 9"></polyline>
                    <line x1="10" y1="14" x2="21" y2="3"></line>
                </svg>
                <span>Portal Jamaah</span>
            </a>

            <button type="button" wire:click="logout" wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 py-2 px-3 rounded-xl border border-rose-200 hover:border-rose-300 bg-rose-50 hover:bg-rose-100 font-bold text-rose-700 transition cursor-pointer disabled:opacity-50">
                <svg class="w-3.5 h-3.5 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Keluar</span>
            </button>
        </div>

    </div>
</div>

