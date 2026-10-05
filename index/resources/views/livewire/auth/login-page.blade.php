@push('styles')
<style>
    html, body {
        background-color: #081D38 !important;
        background: #081D38 !important;
        min-height: 100dvh !important;
    }
</style>
@endpush

<div class="min-h-[100dvh] w-full bg-[#081D38] text-gov-textMain flex flex-col items-center justify-center p-4 sm:p-6 pt-6 pb-4 sm:py-8 relative select-none">
    
    <div class="w-full max-w-md my-auto flex flex-col">
        <!-- Navigation Header (Responsive & Flow Aligned) -->
        <div class="w-full mb-3.5 sm:mb-4 flex items-center justify-start z-10">
            <a wire:navigate.hover href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-slate-300 hover:text-white bg-white/10 hover:bg-white/15 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl transition-all cursor-pointer group">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400 transition-transform group-hover:-translate-x-1"></i>
                <span>Kembali ke Portal</span>
            </a>
        </div>

        <!-- Login Card -->
        <div class="w-full bg-white rounded-2xl p-5 sm:p-8 shadow-2xl border border-gov-border relative z-10 space-y-4 sm:space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <img src="{{ asset('resources/Logo Masjid Salahuddin.webp') }}" 
                 alt="Logo Masjid Salahuddin" 
                 class="w-16 h-16 object-contain mx-auto transition-transform duration-200 hover:scale-105"
                 onerror="this.onerror=null; this.src='{{ asset('images/Logo Masjid.png') }}';">
            <h2 class="text-2xl font-bold text-gov-navy tracking-tight">Login Pengurus DKM</h2>
        </div>

        @if(session('status'))
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0 text-emerald-600"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($isMigrationAccount)
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-sm space-y-3 shadow-xs">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-200/80 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="7.5" cy="15.5" r="5.5"></circle>
                            <path d="m21 2-9.6 9.6"></path>
                            <path d="m15.5 7.5 3 3L22 7l-3-3"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-bold text-amber-950 text-xs uppercase tracking-wider">Aktivasi Akun Sistem Lama</h4>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            {{ $errorMessage }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('password.request', ['email' => $migrationEmail]) }}" wire:navigate.hover class="w-full py-2.5 px-3 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition inline-flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="7.5" cy="15.5" r="5.5"></circle>
                        <path d="m21 2-9.6 9.6"></path>
                        <path d="m15.5 7.5 3 3L22 7l-3-3"></path>
                    </svg>
                    <span>Buat Kata Sandi Baru via Email</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        @elseif($errorMessage)
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium flex items-center gap-2 shadow-xs">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-600"></i>
                <div class="flex-1 text-xs sm:text-sm">
                    <span>{{ $errorMessage }}</span>
                    @if(str_contains($errorMessage, 'belum terdaftar'))
                        <a wire:navigate.hover href="{{ route('register') }}" class="ml-1 underline font-bold text-rose-800 hover:text-rose-950">Daftar Akun Baru</a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form wire:submit.prevent="login" x-data="{ showPass: false }" class="space-y-4 text-sm">
            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">ID Email *</label>
                <div class="relative flex items-center">
                    <div class="absolute left-3.5 top-0 bottom-0 flex items-center justify-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L1 7"></path>
                        </svg>
                    </div>
                    <input wire:model="email" type="text" inputmode="email" autocomplete="username" autocapitalize="none" spellcheck="false"
                        placeholder="Contoh: salahuddin.ayubi"
                        class="w-full pl-10 pr-3.5 py-2.5 rounded-lg border border-gov-border bg-slate-50/50 text-sm text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Cukup ketik ID Email (misal: <span class="font-semibold text-gov-navy">salahuddin.ayubi</span>)</p>
                @error('email') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block font-semibold text-slate-700">Kata Sandi *</label>
                    <a wire:navigate.hover href="{{ route('password.request') }}" class="text-xs text-gov-navy hover:underline font-semibold cursor-pointer">
                        Lupa kata sandi?
                    </a>
                </div>
                <div class="relative flex items-center">
                    <div class="absolute left-3.5 top-0 bottom-0 flex items-center justify-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <input wire:model="password" :type="showPass ? 'text' : 'password'" placeholder="••••••••" class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-gov-border bg-slate-50/50 text-sm text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    <div class="absolute right-2 top-0 bottom-0 flex items-center justify-center">
                        <button type="button" @click="showPass = !showPass"
                            class="p-1.5 text-slate-400 hover:text-slate-600 transition cursor-pointer flex items-center justify-center rounded"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                            <svg x-show="!showPass" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg x-show="showPass" x-cloak class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"></path>
                                <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"></path>
                                <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"></path>
                                <path d="m2 2 20 20"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @error('password') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center text-sm pt-1">
                <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                    <input wire:model="remember" type="checkbox" class="rounded text-gov-navy focus:ring-gov-navy border-gov-border">
                    <span class="text-xs">Ingat saya</span>
                </label>
            </div>

            <button type="submit" wire:loading.attr="disabled" class="w-full py-2.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-sm shadow-md transition inline-flex items-center justify-center cursor-pointer disabled:opacity-50">
                <span wire:loading.remove class="inline-flex items-center gap-2">
                    <span>Masuk</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </span>
                <span wire:loading.inline-flex style="display: none;" class="inline-flex items-center justify-center gap-2 shrink-0 whitespace-nowrap">
                    <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="whitespace-nowrap leading-none">Memverifikasi...</span>
                </span>
            </button>

            <!-- Register Link for Pajak Employees -->
            <div class="pt-2 text-center text-xs text-slate-600">
                <span>Belum memiliki akun pengurus? </span>
                <a wire:navigate.hover href="{{ route('register') }}" class="text-gov-navy font-bold hover:underline cursor-pointer">
                    Daftar dengan email @pajak.go.id
                </a>
            </div>
        </form>

    </div>
    </div>
</div>
