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

        <!-- Card Container -->
        <div class="w-full bg-white rounded-2xl p-5 sm:p-8 shadow-2xl border border-gov-border relative z-10 space-y-4 sm:space-y-6">

        @if($isSuccess)
            <!-- Success View -->
            <div class="text-center space-y-4 py-2">
                <div class="w-16 h-16 rounded-full bg-emerald-50 border-2 border-emerald-300 flex items-center justify-center text-emerald-600 mx-auto shadow-sm">
                    <i data-lucide="check-circle-2" class="w-8 h-8"></i>
                </div>
                
                <div class="space-y-1.5">
                    <h3 class="text-xl font-bold text-gov-navy">Kata Sandi Berhasil Dibuat!</h3>
                    <p class="text-sm text-slate-600">
                        Akun jamaah Anda telah aktif dan diverifikasi. Anda sekarang dapat masuk menggunakan alamat email dan kata sandi baru.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border text-xs text-center space-y-1">
                    <span class="text-slate-500">Email Akun:</span>
                    <p class="font-bold text-gov-navy text-sm">{{ $email }}</p>
                </div>

                <div class="pt-2">
                    <a wire:navigate.hover href="{{ route('login') }}"
                        class="w-full py-2.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-sm shadow-md transition inline-flex items-center justify-center gap-2 cursor-pointer">
                        <span>Masuk ke Akun Saya</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-amber-400"></i>
                    </a>
                </div>
            </div>

        @elseif(!$isValidToken)
            <!-- Invalid / Expired Token View -->
            <div class="text-center space-y-4 py-2">
                <div class="w-16 h-16 rounded-full bg-rose-50 border-2 border-rose-300 flex items-center justify-center text-rose-600 mx-auto shadow-sm">
                    <i data-lucide="shield-x" class="w-8 h-8"></i>
                </div>
                
                <div class="space-y-1.5">
                    <h3 class="text-xl font-bold text-gov-navy">Tautan Tidak Valid atau Kedaluwarsa</h3>
                    <p class="text-sm text-rose-700 bg-rose-50 p-3 rounded-xl border border-rose-200 leading-relaxed">
                        {{ $errorMessage }}
                    </p>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed">
                    Demi alasan keamanan, setiap tautan aktivasi dibatasi waktu kedaluwarsa 60 menit dan hanya dapat dibuka satu kali.
                </p>

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-2">
                    <a wire:navigate.hover href="{{ route('register') }}"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer">
                        Daftar Ulang Akun
                    </a>
                    <a wire:navigate.hover href="{{ route('login') }}"
                        class="px-4 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition cursor-pointer">
                        Ke Halaman Login
                    </a>
                </div>
            </div>

        @else
            <!-- Valid Token Form View -->
            <div class="text-center space-y-2">
                <img src="{{ asset('resources/Logo Masjid Salahuddin.webp') }}" 
                     alt="Logo Masjid Salahuddin" 
                     class="w-16 h-16 object-contain mx-auto transition-transform duration-200 hover:scale-105"
                     onerror="this.onerror=null; this.src='{{ asset('images/Logo Masjid.png') }}';">
                <h2 class="text-2xl font-bold text-gov-navy tracking-tight">Buat Kata Sandi Baru</h2>
                <p class="text-sm text-gov-textMuted">Tentukan kata sandi untuk mengamankan akun jamaah Anda.</p>
            </div>

            <!-- Account Summary Info -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-300">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-gov-navy truncate">{{ $userName }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ $email }}</p>
                </div>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="setPassword" x-data="{ showPass: false, showConfirmPass: false }" class="space-y-4 text-sm">
                <!-- Kata Sandi Baru -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Kata Sandi Baru *</label>
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 top-0 bottom-0 flex items-center justify-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <input wire:model="password" :type="showPass ? 'text' : 'password'" placeholder="Min. 8 karakter (Kapital, angka, simbol)" required
                            class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-gov-border bg-slate-50/50 text-sm text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
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
                    <p class="text-[11px] text-slate-500 mt-1">Minimal 8 karakter, wajib kombinasi huruf kapital, angka, dan simbol.</p>
                    @error('password') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Konfirmasi Kata Sandi *</label>
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 top-0 bottom-0 flex items-center justify-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <input wire:model="password_confirmation" :type="showConfirmPass ? 'text' : 'password'" placeholder="Ulangi kata sandi baru" required
                            class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-gov-border bg-slate-50/50 text-sm text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                        <div class="absolute right-2 top-0 bottom-0 flex items-center justify-center">
                            <button type="button" @click="showConfirmPass = !showConfirmPass"
                                class="p-1.5 text-slate-400 hover:text-slate-600 transition cursor-pointer flex items-center justify-center rounded"
                                title="Tampilkan / Sembunyikan Kata Sandi">
                                <svg x-show="!showConfirmPass" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg x-show="showConfirmPass" x-cloak class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"></path>
                                    <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"></path>
                                    <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"></path>
                                    <path d="m2 2 20 20"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-2.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-sm shadow-md transition inline-flex items-center justify-center cursor-pointer disabled:opacity-50">
                        <span wire:loading.remove class="inline-flex items-center gap-2">
                            <span>Simpan Kata Sandi & Aktifkan</span>
                            <i data-lucide="check" class="w-4 h-4 text-emerald-400"></i>
                        </span>
                        <span wire:loading.inline-flex style="display: none;" class="inline-flex items-center justify-center gap-2 shrink-0 whitespace-nowrap">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        @endif

    </div>
    </div>
</div>
