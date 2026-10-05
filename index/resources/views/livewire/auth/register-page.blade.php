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
    
    <div class="w-full max-w-lg my-auto flex flex-col">
        <!-- Navigation Header (Responsive & Flow Aligned) -->
        <div class="w-full mb-3.5 sm:mb-4 flex items-center justify-start z-10">
            <a wire:navigate.hover href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-slate-300 hover:text-white bg-white/10 hover:bg-white/15 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl transition-all cursor-pointer group">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400 transition-transform group-hover:-translate-x-1"></i>
                <span>Kembali ke Portal</span>
            </a>
        </div>

        <!-- Register Card -->
        <div class="w-full bg-white rounded-2xl p-5 sm:p-8 shadow-2xl border border-gov-border relative z-10 space-y-4 sm:space-y-6">
        
        @if(!$isSubmitted)
            <!-- Header -->
            <div class="text-center space-y-2">
                <img src="{{ asset('resources/Logo Masjid Salahuddin.webp') }}" 
                     alt="Logo Masjid Salahuddin" 
                     class="w-16 h-16 object-contain mx-auto transition-transform duration-200 hover:scale-105"
                     onerror="this.onerror=null; this.src='{{ asset('images/Logo Masjid.png') }}';">
                <h2 class="text-2xl font-bold text-gov-navy tracking-tight">Pendaftaran Akun Pengurus</h2>
                <p class="text-sm text-gov-textMuted">Khusus pegawai Direktorat Jenderal Pajak dengan alamat email resmi <strong class="text-gov-navy font-semibold">@pajak.go.id</strong>.</p>
            </div>

            <!-- Notice Domain Box -->
            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs flex items-start gap-2.5">
                <i data-lucide="shield-check" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    <p class="font-semibold text-amber-900">Verifikasi Resmi Pegawai DJP</p>
                    <p class="text-amber-800/90 leading-relaxed">Gunakan alamat email dinas resmi. Sistem akan mengirimkan surat elektronik verifikasi akun setelah data berhasil didaftarkan.</p>
                </div>
            </div>

            <!-- Registration Form -->
            <form wire:submit.prevent="register" 
                x-data="{ 
                    showPass: false,
                    email: '{{ addslashes($email) }}',
                    hasInteracted: false,
                    get isPajakDomain() {
                        if (!this.email) return false;
                        const val = this.email.trim().toLowerCase();
                        return val.endsWith('@pajak.go.id') && val.indexOf('@') > 0;
                    },
                    get isInvalidDomain() {
                        if (!this.email) return false;
                        const val = this.email.trim().toLowerCase();
                        const atIdx = val.indexOf('@');
                        if (atIdx === -1) return false;
                        const domain = val.slice(atIdx + 1);
                        if (domain.length === 0) return false;
                        if (!'pajak.go.id'.startsWith(domain)) return true;
                        if (domain.length >= 'pajak.go.id'.length && domain !== 'pajak.go.id') return true;
                        return false;
                    },
                    get showDomainSuggestion() {
                        if (!this.email) return false;
                        const val = this.email.trim().toLowerCase();
                        return !val.includes('@') && val.length >= 3;
                    },
                    appendDomain() {
                        if (this.showDomainSuggestion) {
                            this.email = this.email.trim() + '@pajak.go.id';
                            $wire.set('email', this.email);
                        }
                    }
                }" 
                class="space-y-4 text-sm">
                <!-- Nama Lengkap -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Nama Lengkap Pegawai *</label>
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 top-0 bottom-0 flex items-center justify-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <input wire:model="name" type="text" placeholder="Contoh: Ahmad Dahlan, S.E., M.Ak." required
                            class="w-full pl-10 pr-3.5 py-2.5 rounded-lg border border-gov-border bg-slate-50/50 text-sm text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    </div>
                    @error('name') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Email Resmi @pajak.go.id -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Alamat Email Kedinasan *</label>
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 top-0 bottom-0 flex items-center justify-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L1 7"></path>
                            </svg>
                        </div>
                        <input wire:model="email" 
                            x-model="email"
                            @input="hasInteracted = true"
                            type="email" 
                            placeholder="namapegawai@pajak.go.id" 
                            required
                            :class="isInvalidDomain ? 'border-rose-400 bg-rose-50/30 text-rose-900 focus:ring-rose-500 focus:border-rose-500' : (isPajakDomain ? 'border-emerald-400 bg-emerald-50/20 text-gov-textMain focus:ring-emerald-500 focus:border-emerald-500' : 'border-gov-border bg-slate-50/50 text-gov-textMain focus:ring-gov-navy focus:border-gov-navy')"
                            class="w-full pl-10 pr-3.5 py-2.5 rounded-lg border text-sm focus:bg-white focus:outline-none focus:ring-1 transition font-medium">
                    </div>

                    <!-- Peringatan Real-Time Jika Mengetikkan Domain Selain @pajak.go.id -->
                    <div x-show="isInvalidDomain" x-cloak class="mt-2 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2.5 animate-in fade-in duration-150">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            <p class="font-bold text-rose-800">Domain Email Tidak Diizinkan</p>
                            <p class="text-rose-700 mt-0.5 leading-relaxed">
                                Pendaftaran akun pengurus hanya diperkenankan menggunakan alamat email dinas resmi dengan domain <strong class="font-bold text-rose-900">@pajak.go.id</strong>. Email selain domain tersebut (Gmail, Yahoo, dsb) tidak dapat diproses.
                            </p>
                        </div>
                    </div>

                    <!-- Bantuan Cepat Tambah @pajak.go.id Jika Mengetikkan ID Saja -->
                    <div x-show="showDomainSuggestion" x-cloak class="mt-2 flex items-center justify-between text-[11px] text-slate-500 bg-slate-100/90 px-3 py-1.5 rounded-lg border border-slate-200">
                        <span class="truncate">Ketik alamat lengkap atau klik tombol:</span>
                        <button type="button" @click="appendDomain()" class="font-semibold text-gov-navy hover:text-gov-navyHover bg-white px-2 py-0.5 rounded border border-slate-300 shadow-2xs hover:bg-slate-50 transition cursor-pointer shrink-0 ml-2">
                            + @pajak.go.id
                        </button>
                    </div>

                    <!-- Indikator Berhasil: Domain Terverifikasi @pajak.go.id -->
                    <div x-show="isPajakDomain" x-cloak class="mt-2 flex items-center gap-1.5 text-emerald-700 text-xs font-semibold animate-in fade-in duration-150">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <span>Email dinas resmi DJP valid</span>
                    </div>

                    <p x-show="!isInvalidDomain && !isPajakDomain && !showDomainSuggestion" class="text-[11px] text-slate-500 mt-1">Wajib menggunakan domain resmi <span class="font-semibold text-gov-navy">@pajak.go.id</span></p>
                    @error('email') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Password & Konfirmasi Password (2 Kolom) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block font-semibold text-slate-700">Kata Sandi *</label>
                            <button type="button" @click="showPass = !showPass" class="text-xs text-slate-500 hover:text-gov-navy transition">
                                <span x-show="!showPass">Lihat</span>
                                <span x-show="showPass" x-cloak>Sembunyikan</span>
                            </button>
                        </div>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 top-0 bottom-0 flex items-center justify-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </div>
                            <input wire:model="password" :type="showPass ? 'text' : 'password'" placeholder="Min. 8 karakter (Kapital, angka, simbol)" required
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-lg border border-gov-border bg-slate-50/50 text-sm text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Minimal 8 karakter, wajib kombinasi huruf kapital, angka, dan simbol.</p>
                        @error('password') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Konfirmasi Sandi *</label>
                        <div class="relative flex items-center">
                            <div class="absolute left-3.5 top-0 bottom-0 flex items-center justify-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 12 2 2 4-4"></path>
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </div>
                            <input wire:model="password_confirmation" :type="showPass ? 'text' : 'password'" placeholder="Ulangi kata sandi" required
                                class="w-full pl-10 pr-3.5 py-2.5 rounded-lg border border-gov-border bg-slate-50/50 text-sm text-gov-textMain focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                        </div>
                        @error('password_confirmation') <span class="text-xs text-rose-600 mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        :disabled="!isPajakDomain"
                        wire:loading.attr="disabled"
                        class="w-full py-2.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-sm shadow-md transition inline-flex items-center justify-center cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:hover:bg-slate-400">
                        <span wire:loading.remove class="inline-flex items-center gap-2">
                            <span>Daftarkan Akun & Kirim Verifikasi</span>
                            <i data-lucide="send" class="w-4 h-4 text-amber-400"></i>
                        </span>
                        <span wire:loading.inline-flex style="display: none;" class="inline-flex items-center justify-center gap-2 shrink-0 whitespace-nowrap">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Mendaftarkan Akun...</span>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Back to Login Link -->
            <div class="pt-4 border-t border-gov-border text-center text-xs text-slate-600">
                <span>Sudah memiliki akun terverifikasi? </span>
                <a wire:navigate.hover href="{{ route('login') }}" class="text-gov-navy font-bold hover:underline cursor-pointer">
                    Masuk ke Dashboard
                </a>
            </div>

        @else
            <!-- Success Confirmation Screen -->
            <div class="text-center space-y-4 py-2">
                <div class="w-16 h-16 rounded-full bg-emerald-50 border-2 border-emerald-300 flex items-center justify-center text-emerald-600 mx-auto shadow-sm">
                    <i data-lucide="mail-check" class="w-8 h-8"></i>
                </div>
                
                <div class="space-y-1.5">
                    <h3 class="text-xl font-bold text-gov-navy">Pendaftaran Berhasil! Verifikasi Terkirim</h3>
                    <p class="text-sm text-slate-600">
                        Kami telah mengirimkan tautan verifikasi email dinas ke alamat:
                    </p>
                    <p class="text-sm font-bold text-gov-navy bg-slate-100 py-1.5 px-3 rounded-lg inline-block border border-slate-200">
                        {{ $registeredEmail }}
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-gov-border text-xs text-left text-slate-600 space-y-2">
                    <p class="font-semibold text-slate-800 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-gov-navy"></i>
                        <span>Langkah Selanjutnya:</span>
                    </p>
                    <ol class="list-decimal list-inside space-y-1 pl-1 text-slate-600">
                        <li>Buka inbox email dinas Anda di <strong class="text-gov-navy">webmail / Outlook</strong>.</li>
                        <li>Cari email dari <strong class="text-gov-navy">DKM Masjid Salahuddin</strong> dengan subjek <em>"Verifikasi Alamat Email Dinas"</em>.</li>
                        <li>Klik tombol <strong>"Verifikasi Alamat Email"</strong> pada email tersebut.</li>
                        <li>Setelah diklik, akun Anda akan langsung aktif dan dapat digunakan untuk masuk ke Backoffice.</li>
                    </ol>
                    <p class="text-[11px] text-slate-500 italic pt-1 border-t border-slate-200">
                        *Periksa folder Spam/Junk bila email tidak muncul dalam beberapa saat. Tautan berlaku selama 60 menit.
                    </p>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-2">
                    <button type="button" wire:click="resetForm"
                        class="px-4 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition cursor-pointer">
                        Daftarkan Email Lain
                    </button>
                    <a wire:navigate.hover href="{{ route('login') }}"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer">
                        Ke Halaman Login
                    </a>
                </div>
            </div>
        @endif
    </div>
    </div>
</div>
