<!-- ======================================================== -->
<!-- TAB: PENGATURAN & TEMPLATE STUDIO -->
<!-- ======================================================== -->
<div class="space-y-4">
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gov-border shadow-2xs">
        <div class="flex items-center space-x-2.5">
            <div class="hidden sm:flex w-8 h-8 rounded-lg bg-slate-100 text-gov-navy items-center justify-center border border-gov-border shrink-0">
                <i data-lucide="settings-2" class="w-4 h-4 text-gov-navy"></i>
            </div>
            <div>
                <h3 class="font-bold text-base text-gov-textMain">Pengaturan Masjid &amp; Media Visual</h3>
                <p class="text-xs text-gov-textMuted mt-0.5">Konfigurasi profil masjid dan template poster kajian.</p>
            </div>
        </div>
    </div>

    <!-- 2 Sub-tab Navigation Bar -->
    <div class="text-xs flex flex-col sm:flex-row sm:items-center justify-start gap-3 bg-white p-3 rounded-xl border border-gov-border shadow-2xs">
        <button type="button"
            @click="switchSettingsSubTab('masjid')"
            :class="settingsSubTab === 'masjid' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'"
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2">
            <i data-lucide="building-2" class="w-4 h-4"></i>
            <span>Pengaturan Masjid</span>
        </button>
        <button type="button"
            @click="switchSettingsSubTab('poster')"
            :class="settingsSubTab === 'poster' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'"
            class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2">
            <i data-lucide="palette" class="w-4 h-4"></i>
            <span>Template & Studio Poster Kajian</span>
        </button>
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
                        <label class="block font-semibold text-slate-700 mb-1">Arah Kiblat (° dari Utara)</label>
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
                @else
                    <div class="inline-flex items-center gap-2 text-xs text-slate-500 bg-slate-100 px-3.5 py-2.5 rounded-lg border border-gov-border">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                        <span>Hanya Admin DKM yang memiliki izin menyimpan perubahan pengaturan strategis masjid.</span>
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
</div>
