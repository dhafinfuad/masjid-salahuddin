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
            @if(Auth::user()->canManage())
                <div class="shrink-0 hidden sm:flex sm:items-center">
                    <div x-show="settingsSubTab === 'ustadz'" x-cloak>
                        <button type="button" @click="openCreateUstadzModal()"
                            class="justify-center inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-amber-400 hover:bg-amber-300 text-gov-navy font-bold text-xs shadow-2xs transition cursor-pointer">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Tambah Ustadz</span>
                        </button>
                    </div>
                </div>
            @else
                <div class="shrink-0 hidden sm:flex sm:items-center"></div>
            @endif
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

            <form wire:submit.prevent="saveSettings" novalidate class="space-y-4 text-xs">
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
        <!-- Direktori & Daftar Ustadz -->
        <div class="space-y-4">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
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

                                @if(Auth::user()->canManage())
                                    <!-- Titik Tiga Menu Aksi -->
                                    <div x-data="{ openMenu: false }" class="relative inline-block text-left" @click.outside="openMenu = false" @keydown.escape.window="openMenu = false">
                                        <button type="button" @click="openMenu = !openMenu" 
                                            class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center" 
                                            :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''" 
                                            title="Menu Aksi">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu Aksi -->
                                        <div x-show="openMenu" 
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-50"
                                            x-transition:enter-start="transform opacity-0 scale-98"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-50"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-98"
                                            class="absolute right-0 bottom-full mb-1 w-48 bg-white rounded-xl shadow-xl border border-gov-border py-1 z-40 text-xs text-left divide-y divide-slate-100">
                                            
                                            <div class="py-1">
                                                <button type="button" 
                                                    @click="openMenu = false; openEditUstadzModal({{ $u->id }})"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                                    <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                                    <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Edit Profil &amp; Foto</div>
                                                </button>

                                                @if($u->photo)
                                                    <button type="button"
                                                        @click="openMenu = false; openDeleteModal({{ Js::from([
                                                            'id' => $u->id,
                                                            'action' => 'removeUstadzPhoto',
                                                            'title' => 'Hapus Foto Profil Ustadz',
                                                            'itemName' => $u->name,
                                                            'message' => 'Hapus foto profil ustadz ini? Foto akan dilepas dari seluruh jadwal kajian.',
                                                            'type' => 'warning',
                                                            'confirmButtonText' => 'Ya, Hapus Foto',
                                                            'noticeText' => 'Foto profil ustadz ini akan dihapus dari server dan tidak akan tampil lagi di poster atau jadwal.',
                                                        ]) }})"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-amber-700 hover:bg-amber-50 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Foto Profil</div>
                                                    </button>
                                                @endif
                                            </div>

                                            <div class="py-1">
                                                <button type="button"
                                                    @click="openMenu = false; openDeleteModal({{ Js::from([
                                                        'id' => $u->id,
                                                        'action' => 'deleteUstadz',
                                                        'title' => 'Hapus Data Ustadz',
                                                        'itemName' => $u->name,
                                                        'message' => 'Hapus data ustadz ini dari direktori? Data profil narasumber akan dihapus dari sistem.',
                                                    ]) }})"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                    <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                    <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Data Ustadz</div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                        {{ !empty($ustadzSearch) ? 'Tidak ditemukan ustadz yang cocok dengan kata kunci pencarian.' : 'Belum ada data ustadz yang ditambahkan. Silakan klik tombol Tambah Ustadz untuk menambah ustadz baru.' }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
