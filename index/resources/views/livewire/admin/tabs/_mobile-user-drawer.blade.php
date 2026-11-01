<!-- ======================================================== -->
<!-- MOBILE BOTTOM DRAWER: PROFIL PENGGUNA & KELUAR          -->
<!-- ======================================================== -->
<div x-show="userProfileDrawerOpen && window.innerWidth < 768" x-cloak
    @click="if (window.isBackdropClick ? window.isBackdropClick($event, $el) : ($event.target === $el)) { userProfileDrawerOpen = false; }"
    x-transition:enter="transition-opacity ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 md:hidden flex flex-col justify-end"
    style="display: none; z-index: 9999; background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
    role="dialog" aria-modal="true" aria-label="Profil Pengguna & Sesi">

    <div @click.stop
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="w-full max-w-lg mx-auto bg-white rounded-t-3xl shadow-2xl border-t border-slate-200 p-5 pb-8 space-y-4 relative"
        style="z-index: 10000; background-color: #ffffff;">

        <!-- Drag Indicator Bar -->
        <div class="w-12 h-1 bg-slate-300 rounded-full mx-auto mb-2"></div>

        <!-- Sheet Header -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 leading-tight">Profil Pengguna</h3>
                <p class="text-[11px] text-slate-500">Masjid Salahuddin DJP</p>
            </div>
            <button type="button" @click="userProfileDrawerOpen = false"
                class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer"
                title="Tutup Menu">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- User Identity Block (Nama & Jabatan Saja) -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-gov-navy text-white font-bold text-lg flex items-center justify-center shadow-xs ring-4 ring-slate-200/60 shrink-0">
                {{ strtoupper(substr(Auth::user()->name ?? 'DKM', 0, 2)) }}
            </div>
            <div class="overflow-hidden min-w-0">
                <div class="text-sm font-bold text-slate-900 truncate" title="{{ Auth::user()->name ?? 'Pengurus' }}">
                    {{ Auth::user()->name ?? 'Pengurus' }}
                </div>
                <div class="text-xs text-slate-600 font-medium truncate mt-0.5">
                    {{ Auth::user()->employee->jabatan ?? (Auth::user()->isReadOnly() ? 'Viewer (Read-Only)' : ucfirst(Auth::user()->role ?? 'Admin')) }}
                </div>
            </div>
        </div>

        <!-- Tombol Aksi: Portal Jamaah & Keluar -->
        <div class="space-y-2.5 pt-1">
            <!-- Tombol Kunjungi Portal Jamaah -->
            <a href="{{ url('/') }}" target="_blank"
                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl border border-slate-300 hover:border-slate-400 bg-slate-50 hover:bg-slate-100 active:bg-slate-200 transition cursor-pointer text-center shadow-2xs group">
                <svg class="w-4 h-4 text-slate-500 group-hover:text-gov-navy transition-colors shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
                <span class="text-xs font-bold text-slate-800">Portal Jamaah</span>
            </a>

            <!-- Tombol Keluar (Langsung Keluar 1-Klik) -->
            <button type="button" wire:click="logout" wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl border border-rose-300 hover:border-rose-400 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 transition cursor-pointer text-center disabled:opacity-50 shadow-2xs">
                <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span class="text-xs font-bold text-rose-700">Keluar dari Akun</span>
            </button>
        </div>

    </div>
</div>

