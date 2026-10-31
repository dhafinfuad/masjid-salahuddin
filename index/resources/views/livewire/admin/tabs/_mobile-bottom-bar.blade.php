<!-- ======================================================== -->
<!-- MOBILE BOTTOM NAVIGATION BAR (5 SLOTS)                     -->
<!-- ======================================================== -->
<nav data-html2canvas-ignore="true"
    class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-slate-200/90 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] md:hidden select-none"
    style="padding-bottom: env(safe-area-inset-bottom, 0px);"
    aria-label="Navigasi Bawah Mobile">

    <div class="flex items-stretch justify-between px-2 pt-0 pb-1.5 h-15 relative">

        <!-- 1. Slot: Kegiatan -->
        <a href="{{ url('/admin/kegiatan') }}" wire:navigate
            class="flex-1 flex flex-col items-center justify-center pt-1.5 pb-1 gap-1 transition-all duration-150 cursor-pointer -mt-[1px] {{ in_array($currentTab, ['kegiatan', 'kajian', 'agenda']) ? 'border-t-2 border-amber-500 text-gov-navy font-bold' : 'border-t-2 border-transparent text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <svg class="w-5 h-5 {{ in_array($currentTab, ['kegiatan', 'kajian', 'agenda']) ? 'text-gov-navy stroke-[2.3]' : 'text-slate-400 stroke-[1.8]' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <span class="text-[10px] leading-tight tracking-tight">Kegiatan</span>
        </a>

        <!-- 2. Slot: Petugas (Microphone Icon) -->
        <a href="{{ url('/admin/petugas') }}" wire:navigate
            class="flex-1 flex flex-col items-center justify-center pt-1.5 pb-1 gap-1 transition-all duration-150 cursor-pointer -mt-[1px] {{ $currentTab === 'petugas' ? 'border-t-2 border-amber-500 text-gov-navy font-bold' : 'border-t-2 border-transparent text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <svg class="w-5 h-5 {{ $currentTab === 'petugas' ? 'text-gov-navy stroke-[2.3]' : 'text-slate-400 stroke-[1.8]' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                    <line x1="12" y1="19" x2="12" y2="23"></line>
                    <line x1="8" y1="23" x2="16" y2="23"></line>
                </svg>
            </div>
            <span class="text-[10px] leading-tight tracking-tight">Petugas</span>
        </a>

        <!-- 3. Slot: Flush Minimalist Menu Hub (Tanpa Tonjolan, Single Focal Point FAB) -->
        <div class="flex-1 flex items-center justify-center px-1">
            <button type="button" @click="mobileNavDrawerOpen = true"
                class="w-11 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-gov-navy flex items-center justify-center transition-all duration-150 cursor-pointer border border-slate-200/80 shadow-2xs group"
                title="Buka Menu Lengkap Seluruh Modul"
                aria-label="Buka Menu Lengkap Seluruh Modul">
                <!-- 9-Dot Bento App Drawer Grid Icon -->
                <svg class="w-5 h-5 text-gov-navy group-hover:scale-105 transition-transform duration-150" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="5" cy="5" r="2"></circle>
                    <circle cx="12" cy="5" r="2"></circle>
                    <circle cx="19" cy="5" r="2"></circle>
                    <circle cx="5" cy="12" r="2"></circle>
                    <circle cx="12" cy="12" r="2"></circle>
                    <circle cx="19" cy="12" r="2"></circle>
                    <circle cx="5" cy="19" r="2"></circle>
                    <circle cx="12" cy="19" r="2"></circle>
                    <circle cx="19" cy="19" r="2"></circle>
                </svg>
            </button>
        </div>

        <!-- 4. Slot: Kas -->
        <a href="{{ url('/admin/finance') }}" wire:navigate
            class="flex-1 flex flex-col items-center justify-center pt-1.5 pb-1 gap-1 transition-all duration-150 cursor-pointer -mt-[1px] {{ $currentTab === 'finance' ? 'border-t-2 border-amber-500 text-gov-navy font-bold' : 'border-t-2 border-transparent text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <svg class="w-5 h-5 {{ $currentTab === 'finance' ? 'text-gov-navy stroke-[2.3]' : 'text-slate-400 stroke-[1.8]' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path>
                    <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path>
                    <circle cx="18" cy="15" r="1"></circle>
                </svg>
            </div>
            <span class="text-[10px] leading-tight tracking-tight">Kas</span>
        </a>

        @if(!Auth::user()->isJamaah())
        <!-- 5. Slot: Pengguna -->
        <a href="{{ url('/admin/users') }}" wire:navigate
            class="flex-1 flex flex-col items-center justify-center pt-1.5 pb-1 gap-1 transition-all duration-150 cursor-pointer -mt-[1px] {{ $currentTab === 'users' ? 'border-t-2 border-amber-500 text-gov-navy font-bold' : 'border-t-2 border-transparent text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <svg class="w-5 h-5 {{ $currentTab === 'users' ? 'text-gov-navy stroke-[2.3]' : 'text-slate-400 stroke-[1.8]' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <span class="text-[10px] leading-tight tracking-tight">Pengguna</span>
        </a>
        @else
        <!-- 5. Slot: Galeri (Khusus Jamaah) -->
        <a href="{{ url('/admin/galeri') }}" wire:navigate
            class="flex-1 flex flex-col items-center justify-center pt-1.5 pb-1 gap-1 transition-all duration-150 cursor-pointer -mt-[1px] {{ in_array($currentTab, ['galeri', 'gallery']) ? 'border-t-2 border-amber-500 text-gov-navy font-bold' : 'border-t-2 border-transparent text-slate-400 hover:text-slate-600 font-medium' }}">
            <div class="relative">
                <svg class="w-5 h-5 {{ in_array($currentTab, ['galeri', 'gallery']) ? 'text-gov-navy stroke-[2.3]' : 'text-slate-400 stroke-[1.8]' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <path d="m21 15-5-5L5 21"></path>
                </svg>
            </div>
            <span class="text-[10px] leading-tight tracking-tight">Galeri</span>
        </a>
        @endif

    </div>
</nav>

