<!-- ======================================================== -->
<!-- TAB: PENGATURAN & TEMPLATE STUDIO -->
<!-- ======================================================== -->
<div class="space-y-4">
    <!-- Header Card Banner (Navy Banner) -->
    <div class="bg-gov-navy rounded-xl p-5 border border-gov-border/30 shadow-xs bg-gov-pattern">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-10">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-300">Konfigurasi Sistem</span>
                </div>
                <h3 class="font-bold text-base text-white">
                    <span x-show="settingsSubTab === 'masjid'">Pengaturan Masjid</span>
                    <span x-show="settingsSubTab === 'poster'" x-cloak>Template & Studio Poster</span>
                    <span x-show="settingsSubTab === 'ustadz'" x-cloak>Database Ustadz</span>
                </h3>
                <span class="sr-only">Pengaturan Masjid, Media Visual & Database Ustadz</span>
                <div class="text-xs text-slate-300 mt-0.5">
                    <span x-show="settingsSubTab === 'masjid'">Perbarui profil masjid, informasi Sholat Jumat, dan display TV.</span>
                    <span x-show="settingsSubTab === 'poster'" x-cloak>Studio visual generator poster kajian otomatis siap cetak dan bagikan.</span>
                    <span x-show="settingsSubTab === 'ustadz'" x-cloak>Kelola direktori profil, kontak WhatsApp, dan foto narasumber kajian.</span>
                </div>
            </div>
            <div class="shrink-0 hidden sm:flex sm:items-center"></div>
        </div>
    </div>


    <!-- ======================================================== -->
    <!-- KONTEN SUB-TAB 1: PENGATURAN MASJID & LAYAR TV -->
    <!-- ======================================================== -->
    <div x-show="settingsSubTab === 'masjid'" x-cloak :class="{ 'hidden': settingsSubTab !== 'masjid' }" class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-gov-border shadow-2xs space-y-4">
            <div class="border-b border-slate-200 pb-3">
                <h3 class="font-bold text-base text-gov-textMain">Pengaturan Masjid</h3>
                <p class="text-xs text-gov-textMuted">Perbarui identitas profil masjid, informasi Sholat Jumat, parameter jeda iqamah, dan teks berjalan TV.</p>
            </div>

            @if(Auth::user()->isReadOnly())
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-lg text-amber-900 text-xs flex items-center gap-2.5 mb-4">
                    <i data-lucide="shield-alert" class="w-4 h-4 text-amber-700 shrink-0"></i>
                    <span><strong>Perlindungan Data Strategis:</strong> Akun Viewer / Penasihat DKM hanya memiliki hak melihat konfigurasi masjid tanpa izin menyimpan perubahan data strategis.</span>
                </div>
            @endif

            <form wire:submit.prevent="saveSettings" class="space-y-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Masjid</label>
                        <input wire:model="settingsName" type="text" @disabled(Auth::user()->isReadOnly())
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kota / Kabupaten Jadwal Sholat (Kemenag)</label>
                        <div wire:ignore>
                            <select wire:model="settingsCityId" @disabled(Auth::user()->isReadOnly())
                                class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs disabled:opacity-60 disabled:cursor-not-allowed">
                                <optgroup label="Pilihan Populer">
                                    <option value="1634">Kota Malang (Default)</option>
                                    <option value="1614">Kab. Malang</option>
                                    <option value="1638">Kota Surabaya</option>
                                    <option value="1301">Kota Jakarta</option>
                                    <option value="1203">Kota Bandung</option>
                                    <option value="1434">Kota Semarang</option>
                                    <option value="1505">Kota Yogyakarta</option>
                                </optgroup>
                                <optgroup label="Seluruh Kota / Kabupaten">
                                    @foreach($allCities as $c)
                                        <option value="{{ $c['id'] }}">{{ $c['lokasi'] }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea wire:model="settingsAddress" rows="2" @disabled(Auth::user()->isReadOnly())
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Telepon DKM</label>
                        <input wire:model="settingsPhone" type="text" @disabled(Auth::user()->isReadOnly())
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email Resmi</label>
                        <input wire:model="settingsEmail" type="email" @disabled(Auth::user()->isReadOnly())
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Arah Kiblat (&deg; dari Utara)</label>
                        <input wire:model="settingsQiblaAngle" type="number" step="0.01" @disabled(Auth::user()->isReadOnly())
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                    </div>
                </div>

                <!-- Friday prayer -->
                {{-- <div class="p-4 bg-slate-50 rounded-xl border border-gov-border space-y-3">
                    <span class="font-bold text-gov-textMain block">Petugas Sholat Jumat Pekan Ini</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Khatib</label>
                            <input wire:model="settingsFridayKhatib" type="text" @disabled(Auth::user()->isReadOnly())
                                class="w-full p-2 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Imam</label>
                            <input wire:model="settingsFridayImam" type="text" @disabled(Auth::user()->isReadOnly())
                                class="w-full p-2 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Muadzin</label>
                            <input wire:model="settingsFridayMuadzin" type="text" @disabled(Auth::user()->isReadOnly())
                                class="w-full p-2 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                        </div>
                    </div>
                </div> --}}

                <!-- Bank Accounts for Infaq & Delay Iqamah -->
                <div class="p-4 bg-slate-50 rounded-xl border border-gov-border space-y-3">
                    <span class="font-bold text-gov-textMain block">Rekening Infaq Jamaah & Parameter Sholat</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Nama Bank</label>
                            <input wire:model="settingsBankName" type="text" placeholder="BSI (Bank Syariah Indonesia)" @disabled(Auth::user()->isReadOnly())
                                class="w-full p-2 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Nomor Rekening</label>
                            <input wire:model="settingsBankAccount" type="text" placeholder="7123-4567-89" @disabled(Auth::user()->isReadOnly())
                                class="w-full p-2 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-mono disabled:opacity-60 disabled:cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Atas Nama (Pemilik)</label>
                            <input wire:model="settingsBankHolder" type="text" placeholder="DKM Masjid Salahuddin" @disabled(Auth::user()->isReadOnly())
                                class="w-full p-2 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Jeda Iqamah (Menit)</label>
                            <input wire:model="settingsIqamahDelay" type="number" min="1" max="60" @disabled(Auth::user()->isReadOnly())
                                class="w-full p-2 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition disabled:opacity-60 disabled:cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- TV Announcements -->
                {{-- <div>
                    <label class="block font-semibold text-slate-700 mb-1">Teks Berjalan Layar TV (Running Text Ticker)</label>
                    <p class="text-xs text-slate-400 mb-1">Tulis satu pengumuman per baris.</p>
                    <textarea wire:model="settingsTvAnnouncementsText" rows="4" @disabled(Auth::user()->isReadOnly())
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-mono text-xs transition disabled:opacity-60 disabled:cursor-not-allowed"></textarea>
                </div> --}}

                @if(Auth::user()->isAdmin())
                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                            <span wire:loading.remove wire:target="saveSettings">Simpan Perubahan Pengaturan</span>
                            <span wire:loading.inline-flex wire:target="saveSettings" style="display: none;"
                                class="inline-flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="whitespace-nowrap">Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                @else
                    <div class="flex items-center justify-end pt-2">
                        <div class="inline-flex items-center gap-2 text-xs text-slate-500 bg-slate-100 px-3.5 py-2.5 rounded-lg border border-gov-border">
                            <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                            <span>Hanya Admin DKM yang memiliki izin menyimpan perubahan pengaturan strategis masjid.</span>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- KONTEN SUB-TAB 2: TEMPLATE & STUDIO POSTER KAJIAN -->
    <!-- ======================================================== -->
    <div x-show="settingsSubTab === 'poster'" x-cloak :class="{ 'hidden': settingsSubTab !== 'poster' }" class="space-y-6">
        <livewire:admin.poster-setting-manager :isEmbedded="true" :key="'poster-studio-embedded'" />
    </div>

    <!-- ======================================================== -->
    <!-- KONTEN SUB-TAB 3: DATABASE USTADZ (HALAMAN DEDIKASI) -->
    <!-- ======================================================== -->
    <div x-show="settingsSubTab === 'ustadz'" x-cloak :class="{ 'hidden': settingsSubTab !== 'ustadz' }" class="space-y-6">
        <!-- Layout 2 Kolom Responsif Desktop (5:7) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- KOLOM KIRI: Form Tambah / Edit Profil & Foto Ustadz (5 Kolom) -->
            <div class="lg:col-span-5 bg-white p-5 sm:p-6 rounded-xl border border-gov-border shadow-2xs space-y-4 lg:sticky lg:top-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200 shrink-0">
                            <i data-lucide="{{ $editingUstadzId ? 'edit-3' : 'user-plus' }}" class="w-4 h-4 text-emerald-700"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-gov-textMain">
                                {{ $editingUstadzId ? 'Edit Profil Ustadz' : 'Tambah Ustadz Baru' }}
                            </h3>
                            <p class="text-[11px] text-slate-500">
                                {{ $editingUstadzId ? 'Perbarui data profil & foto profil narasumber.' : 'Lengkapi identitas narasumber dan unggah foto profil.' }}
                            </p>
                        </div>
                    </div>

                    @if($editingUstadzId)
                        <button type="button" wire:click="resetUstadzForm"
                            class="text-xs text-slate-500 hover:text-slate-800 underline cursor-pointer shrink-0">
                            Batal Edit
                        </button>
                    @endif
                </div>

                <form wire:submit.prevent="saveUstadz" class="space-y-4 text-xs">
                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                        <input type="text" wire:model="ustadzName" placeholder="Contoh: Ust. Hasyim Azhari, S.Pd.I."
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-medium text-xs text-gov-textMain transition">
                        @error('ustadzName') <span class="text-xs font-semibold text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Bidang Kajian / Gelar -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Bidang Kajian / Spesialisasi</label>
                        <input type="text" wire:model="ustadzTitle" placeholder="Misal: Kajian Tematik / Tafsir / Sirah"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-medium text-xs text-gov-textMain transition">
                        @error('ustadzTitle') <span class="text-xs font-semibold text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nomor WhatsApp -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                        <input type="text" wire:model="ustadzPhone" placeholder="0821-xxxx-xxxx"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-medium text-xs text-gov-textMain transition">
                        @error('ustadzPhone') <span class="text-xs font-semibold text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Foto Profil Ustadz -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Foto Profil Ustadz</label>
                        <div class="flex items-center gap-3.5 p-3.5 bg-slate-50/70 rounded-xl border border-gov-border">
                            <!-- Preview Avatar -->
                            <div class="relative shrink-0 w-14 h-14 rounded-full overflow-hidden border border-slate-200 bg-white flex items-center justify-center shadow-2xs">
                                @if ($ustadzPhoto)
                                    <img src="{{ $ustadzPhoto->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                                @elseif ($ustadzExistingPhoto)
                                    <img src="{{ asset('storage/' . $ustadzExistingPhoto) }}" alt="Foto Ustadz" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Upload Button & File Controls -->
                            <div class="min-w-0 flex-1">
                                <label class="relative cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-2xs transition overflow-hidden"
                                       x-data="{ isDragging: false }"
                                       @dragenter.prevent="isDragging = true"
                                       @dragover.prevent="isDragging = true"
                                       @dragleave.prevent="isDragging = false"
                                       @drop="isDragging = false"
                                       :class="isDragging ? 'ring-2 ring-gov-navy border-gov-navy bg-sky-50 text-gov-navy' : ''">
                                    <svg class="w-3.5 h-3.5 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="pointer-events-none" x-text="isDragging ? 'Lepas Foto' : '{{ $ustadzExistingPhoto || $ustadzPhoto ? 'Ganti Foto / Seret' : 'Unggah Foto / Seret' }}'">{{ $ustadzExistingPhoto || $ustadzPhoto ? 'Ganti Foto' : 'Unggah Foto' }}</span>
                                    <input type="file"
                                           wire:model="ustadzPhoto"
                                           accept="image/png,image/jpeg,image/webp"
                                           @dragenter="isDragging = true"
                                           @dragover.prevent="isDragging = true"
                                           @dragleave="isDragging = false"
                                           @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                </label>
                                <p class="text-[11px] text-slate-500 mt-1 truncate">JPG, PNG, WEBP (maks. 3MB). Proporsional/kotak direkomendasikan.</p>

                                <div wire:loading.inline-flex wire:target="ustadzPhoto" style="display: none;"
                                    class="inline-flex flex-row items-center gap-2 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200 mt-1 shrink-0 whitespace-nowrap">
                                    <svg class="w-3 h-3 animate-spin shrink-0 text-emerald-600 inline-block" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="whitespace-nowrap leading-none">Mengunggah file foto...</span>
                                </div>
                                @error('ustadzPhoto') <span class="text-xs font-semibold text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Simpan Form -->
                    <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                        @if($editingUstadzId)
                            <button type="button" wire:click="resetUstadzForm"
                                class="px-3.5 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 font-semibold transition cursor-pointer">
                                Batal
                            </button>
                        @endif
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="saveUstadz">{{ $editingUstadzId ? 'Simpan Perubahan' : 'Simpan Ustadz' }}</span>
                            <span wire:loading.inline-flex wire:target="saveUstadz" style="display: none;"
                                class="inline-flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Tip Card Otomatisasi -->
                <div class="p-3 bg-amber-50/70 rounded-xl border border-amber-200/80 text-[11px] text-amber-900 flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/>
                        <path d="M9 18h6"/>
                        <path d="M10 22h4"/>
                    </svg>
                    <div class="leading-relaxed">
                        <strong class="font-semibold text-amber-950">Otomatisasi Cerdas:</strong> Foto ustadz yang tersimpan di sini akan otomatis terhubung ke seluruh jadwal kajian lama maupun baru dan generator poster dengan nama narasumber yang cocok.
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: Pencarian Interaktif & Grid Daftar Ustadz (7 Kolom) -->
            <div class="lg:col-span-7 space-y-4">
                <!-- Search & Header Bar -->
                <div class="bg-white p-4 rounded-xl border border-gov-border shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h4 class="font-bold text-sm text-gov-textMain flex items-center gap-2">
                            <span>Direktori Ustadz</span>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                {{ count($allUstadzs) }} Ustadz
                            </span>
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Daftar narasumber kajian yang terdata dalam sistem.</p>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text"
                               wire:model.live.debounce.300ms="ustadzSearch"
                               placeholder="Cari nama ustadz..."
                               class="w-full pl-9 pr-8 py-2 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy text-xs text-gov-textMain transition">
                        @if(!empty($ustadzSearch))
                            <button type="button" wire:click="$set('ustadzSearch', '')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Cards Grid -->
                @if($allUstadzs->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        @foreach($allUstadzs as $u)
                            <div class="bg-white rounded-xl border border-slate-200 hover:border-slate-300 p-4 shadow-2xs transition flex flex-col justify-between gap-3 group">
                                <div class="flex items-start gap-3">
                                    <!-- Foto Avatar -->
                                    <div class="relative shrink-0 w-12 h-12 rounded-full overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center shadow-2xs">
                                        @if($u->photo)
                                            <img src="{{ asset('storage/' . $u->photo) }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-emerald-50 text-emerald-700 font-bold text-xs uppercase">
                                                {{ mb_substr(preg_replace('/^Ust(adz)?\.?\s+/i', '', $u->name), 0, 2) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Nama & Info -->
                                    <div class="min-w-0 flex-1">
                                        <h5 class="text-xs font-bold text-slate-900 group-hover:text-gov-navy transition truncate" title="{{ $u->name }}">
                                            {{ $u->name }}
                                        </h5>
                                        @if($u->title)
                                            <p class="text-[11px] text-slate-500 truncate mt-0.5" title="{{ $u->title }}">
                                                {{ $u->title }}
                                            </p>
                                        @endif
                                        @if($u->phone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $u->phone) }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1 text-[11px] text-emerald-600 hover:text-emerald-700 font-medium mt-1 transition">
                                                <i data-lucide="message-circle" class="w-3 h-3"></i>
                                                <span>{{ $u->phone }}</span>
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-400 block mt-1">Tanpa WhatsApp</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Footer: Stats & Actions -->
                                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                                    <span class="text-[10px] text-slate-500 font-medium">
                                        {{ $u->kajians_count }} Jadwal Kajian
                                    </span>

                                    <div class="flex items-center gap-1">
                                        <!-- Edit Profil Button -->
                                        <button type="button" wire:click="editUstadz({{ $u->id }})"
                                            title="Edit Profil & Foto"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-gov-navy shadow-2xs transition cursor-pointer">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        </button>

                                        <!-- Hapus Foto Button -->
                                        @if($u->photo)
                                            <button type="button"
                                                @click="openDeleteModal({{ Js::from([
                                                    'id' => $u->id,
                                                    'action' => 'removeUstadzPhoto',
                                                    'title' => 'Hapus Foto Profil Ustadz',
                                                    'itemName' => $u->name,
                                                    'message' => 'Hapus foto profil ustadz ini? Foto akan dilepas dari seluruh jadwal kajian.',
                                                    'type' => 'warning',
                                                    'confirmButtonText' => 'Ya, Hapus Foto',
                                                    'noticeText' => 'Foto profil ustadz ini akan dihapus dari server dan tidak akan tampil lagi di poster atau jadwal.',
                                                ]) }})"
                                                title="Hapus Foto Profil"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700 shadow-2xs transition cursor-pointer">
                                                <i data-lucide="image-minus" class="w-3.5 h-3.5"></i>
                                            </button>
                                        @endif

                                        <!-- Hapus Ustadz Button -->
                                        <button type="button"
                                            @click="openDeleteModal({{ Js::from([
                                                'id' => $u->id,
                                                'action' => 'deleteUstadz',
                                                'title' => 'Hapus Data Ustadz',
                                                'itemName' => $u->name,
                                                'message' => 'Hapus data ustadz ini dari direktori? Data profil narasumber akan dihapus dari sistem.',
                                            ]) }})"
                                            title="Hapus Ustadz"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 shadow-2xs transition cursor-pointer">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-xl border border-slate-200 p-8 text-center space-y-2">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <i data-lucide="users" class="w-6 h-6"></i>
                        </div>
                        <h5 class="text-xs font-bold text-slate-800">Tidak ada data ustadz</h5>
                        <p class="text-[11px] text-slate-500 max-w-sm mx-auto">
                            {{ !empty($ustadzSearch) ? 'Tidak ditemukan ustadz yang cocok dengan kata kunci pencarian.' : 'Belum ada data ustadz yang ditambahkan. Silakan gunakan formulir di samping untuk menambah ustadz baru.' }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
