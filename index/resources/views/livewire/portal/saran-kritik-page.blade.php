<div class="min-h-screen bg-gov-canvas text-gov-textMain flex flex-col selection:bg-gov-navy selection:text-white"
     x-data="{
         charCount: 0,
         updateCount(val) { this.charCount = (val || '').length; }
     }">

    <!-- Top Sticky Header Bar -->
    <header class="bg-white border-b border-gov-border sticky top-0 z-30 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 sm:h-16 flex items-center justify-between gap-4">
            <!-- Left: Back button & Title -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" wire:navigate 
                   class="p-2 rounded-lg border border-gov-border hover:bg-slate-50 text-slate-600 transition shadow-2xs inline-flex items-center justify-center cursor-pointer"
                   title="Kembali ke Beranda">
                    <svg class="w-4 h-4 text-gov-textMain" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                </a>
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-gov-textMain leading-tight">Saran & Kritik</h1>
                    <p class="text-[11px] text-slate-500 leading-none mt-0.5">{{ $settings->name ?? 'Masjid Salahuddin' }} • KPP Madya Malang</p>
                </div>
            </div>

            <!-- Right: Link ke Portal / Dashboard -->
            <div class="flex items-center gap-2">
                <a href="{{ url('/') }}" wire:navigate 
                   class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gov-border hover:bg-slate-50 text-xs font-semibold text-slate-700 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>Beranda Portal</span>
                </a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" wire:navigate 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                        <span>Dashboard</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Header Card Banner -->
        <div class="bg-gov-navy text-white rounded-xl p-5 sm:p-7 relative overflow-hidden shadow-xs bg-gov-pattern border border-gov-navyDark">
            <div class="flex items-center space-x-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-300">ASPIRASI & KEMAKMURAN MASJID</span>
            </div>
            <h2 class="text-xl sm:text-3xl font-bold text-white tracking-tight">Kotak Saran & Aspirasi Jamaah</h2>
            <p class="text-xs sm:text-sm text-slate-200 font-normal leading-relaxed mt-1.5 max-w-2xl">
                Kenyamanan ibadah jamaah adalah amanah utama kami. Sampaikan saran membangun, pengaduan fasilitas, kebersihan, maupun usulan program kajian kepada Pengurus Takmir DKM {{ $settings->name ?? 'Masjid Salahuddin' }}.
            </p>
        </div>

        <!-- 2-Column Responsive Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: Formulir Penyampaian Saran (5 Kolom Desktop) -->
            <div class="lg:col-span-5 bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-4">
                
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">Sampaikan Masukan Anda</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Semua masukan dibaca langsung oleh Takmir DKM</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                </div>

                @if($submitted)
                    <!-- Kartu Sukses Pengiriman -->
                    <div class="p-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-3 animate-in fade-in duration-300">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-emerald-900">Jazakumullahu Khairan!</h4>
                                <p class="text-xs text-emerald-700">Aspirasi Anda berhasil terkirim.</p>
                            </div>
                        </div>

                        <p class="text-xs text-emerald-800 leading-relaxed pt-1">
                            Terima kasih atas kepedulian Anda demi kemakmuran dan kenyamanan bersama di {{ $settings->name ?? 'Masjid Salahuddin' }}. Masukan Anda telah masuk ke sistem pengurus dan akan segera ditindaklanjuti.
                        </p>

                        <div class="pt-2">
                            <button type="button" wire:click="resetForm"
                                class="w-full py-2 px-3 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold transition shadow-2xs cursor-pointer inline-flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                                <span>Tulis Saran Lainnya</span>
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Form Input -->
                    <form wire:submit.prevent="submit" class="space-y-4 text-xs">
                        
                        <!-- Anti-spam Honeypot (hidden from real users) -->
                        <div class="hidden" aria-hidden="true" style="display: none;">
                            <input type="text" wire:model="honey_pot" tabindex="-1" autocomplete="off">
                        </div>

                        <!-- Kategori Masukan -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Kategori Masukan <span class="text-rose-500">*</span>
                            </label>
                            <select wire:model="category"
                                class="w-full px-3 py-2 border border-gov-border rounded-lg bg-slate-50/70 hover:bg-slate-100/70 focus:bg-white text-gov-textMain text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition shadow-2xs cursor-pointer">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category') <span class="text-rose-600 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Opsi Pengirim & Anonim -->
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <label for="is_anonymous_checkbox" class="text-xs font-semibold text-slate-700 cursor-pointer flex items-center gap-2">
                                    <input type="checkbox" id="is_anonymous_checkbox" wire:model.live="is_anonymous"
                                        class="rounded border-slate-300 text-gov-navy focus:ring-gov-navy w-4 h-4 cursor-pointer">
                                    <span>Kirim sebagai Hamba Allah (Anonim)</span>
                                </label>
                            </div>

                            @if(!$is_anonymous)
                                <div class="pt-1">
                                    <label class="block text-[11px] font-medium text-slate-600 mb-1">Nama Pengirim</label>
                                    <input type="text" wire:model="name"
                                        placeholder="Contoh: Ahmad Fauzi / Jamaah Pegawai KPP"
                                        class="w-full px-3 py-2 border border-gov-border rounded-lg bg-white text-gov-textMain text-xs focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition shadow-2xs">
                                    @error('name') <span class="text-rose-600 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                                </div>
                            @else
                                <div class="text-[11px] text-slate-500 italic bg-white/70 p-2 rounded border border-slate-200">
                                    Identitas nama Anda tidak akan dicatat dan akan ditampilkan sebagai "Hamba Allah".
                                </div>
                            @endif
                        </div>

                        <!-- Kontak (Opsional) -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-0.5">
                                Kontak WhatsApp / Email <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <p class="text-[11px] text-slate-500 mb-1">Diisi jika Anda berkenan dihubungi pengurus perihal tindak lanjut.</p>
                            <input type="text" wire:model="contact"
                                placeholder="Contoh: 081234567890 atau email@kemenkeu.go.id"
                                class="w-full px-3 py-2 border border-gov-border rounded-lg bg-white text-gov-textMain text-xs focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition shadow-2xs">
                            @error('contact') <span class="text-rose-600 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Subjek / Topik Ringkas -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                Topik / Subjek Masukan <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" wire:model="title"
                                placeholder="Contoh: Usulan Penambahan Rak Sandal Sisi Timur"
                                class="w-full px-3 py-2 border border-gov-border rounded-lg bg-white text-gov-textMain text-xs focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition shadow-2xs">
                            @error('title') <span class="text-rose-600 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Isi Saran & Kritik (Wajib) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-semibold text-slate-700">
                                    Isi Saran, Kritik, atau Aspirasi <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] text-slate-400" x-text="charCount + ' / 2000'"></span>
                            </div>
                            <textarea wire:model="message" rows="5"
                                @input="updateCount($event.target.value)"
                                placeholder="Tuliskan secara jelas saran, kritik konstruktif, pengaduan fasilitas, atau usulan kegiatan Anda di sini..."
                                class="w-full px-3 py-2.5 border border-gov-border rounded-lg bg-white text-gov-textMain text-xs focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition shadow-2xs leading-relaxed resize-y"></textarea>
                            @error('message') <span class="text-rose-600 text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tombol Submit -->
                        <div class="pt-2">
                            <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full py-2.5 px-4 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold transition shadow-2xs cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    <span>Kirim Saran & Aspirasi</span>
                                </span>
                                <span wire:loading.inline-flex wire:target="submit" style="display: none;"
                                    class="inline-flex flex-row items-center justify-center gap-2 shrink-0 whitespace-nowrap">
                                    <svg class="w-4 h-4 animate-spin text-white inline-block" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>Mengirimkan masukan...</span>
                                </span>
                            </button>
                        </div>

                    </form>
                @endif

            </div>

            <!-- RIGHT COLUMN: Aspirasi & Tindak Lanjut Publik (7 Kolom Desktop) -->
            <div class="lg:col-span-7 space-y-4">
                
                <!-- Header Kolom Kanan -->
                <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-base font-bold text-gov-textMain">Aspirasi & Tindak Lanjut Takmir</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Keterbukaan informasi dan respon resmi Pengurus DKM atas masukan jamaah</p>
                        </div>
                    </div>

                    <!-- Filter Kategori Tabs (Clean Pills without Counter Badges per Rule 2) -->
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <button type="button" wire:click="setPublicCategoryFilter('all')"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer {{ $publicCategoryFilter === 'all' ? 'bg-gov-navy text-white shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-gov-border' }}">
                            Semua Kategori
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" wire:click="setPublicCategoryFilter('{{ $cat }}')"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition cursor-pointer {{ $publicCategoryFilter === $cat ? 'bg-gov-navy text-white shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-gov-border' }}">
                                {{ $cat }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Feed Aspirasi Publik -->
                <div class="space-y-3">
                    @forelse($publicFeedbacks as $fb)
                        <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-3 transition hover:border-slate-300">
                            
                            <!-- Header Item: Sender, Category, Date -->
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs shrink-0">
                                        <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-xs text-gov-textMain block">{{ $fb->display_sender }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $fb->created_at ? $fb->created_at->translatedFormat('d F Y, H:i') : '' }} WIB</span>
                                    </div>
                                </div>

                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $fb->category_color_class }}">
                                    {{ $fb->category }}
                                </span>
                            </div>

                            <!-- Title & Message -->
                            <div>
                                @if(!empty($fb->title))
                                    <h4 class="font-bold text-xs text-slate-800 mb-1">{{ $fb->title }}</h4>
                                @endif
                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line bg-slate-50/70 p-3 rounded-lg border border-slate-100">{{ $fb->message }}</p>
                            </div>

                            <!-- Official Takmir Reply Callout -->
                            @if(!empty($fb->admin_reply))
                                <div class="p-3.5 rounded-lg bg-emerald-50/90 border border-emerald-200 text-emerald-900 space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 font-bold text-xs text-emerald-800">
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                            <span>Tanggapan Resmi Takmir DKM</span>
                                        </div>
                                        @if($fb->replied_at)
                                            <span class="text-[10px] text-emerald-700/80">{{ $fb->replied_at->translatedFormat('d M Y') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-emerald-950 leading-relaxed whitespace-pre-line">{{ $fb->admin_reply }}</p>
                                </div>
                            @endif

                        </div>
                    @empty
                        <div class="bg-white rounded-xl p-8 border border-gov-border shadow-2xs text-center space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 mx-auto">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            </div>
                            <h4 class="font-bold text-xs text-gov-textMain pt-1">Belum Ada Aspirasi Publik</h4>
                            <p class="text-[11px] text-slate-500 max-w-sm mx-auto leading-relaxed">
                                Setiap masukan yang Anda kirimkan melalui formulir di samping akan ditinjau dan ditindaklanjuti secara seksama oleh Pengurus Takmir DKM.
                            </p>
                        </div>
                    @endforelse
                </div>

            </div>

        </div>

        <!-- Footer -->
        <footer class="bg-white rounded-xl border border-gov-border shadow-2xs p-4 sm:p-5 text-center space-y-1">
            <div class="font-bold text-xs text-gov-textMain">{{ $settings->name ?? 'Masjid Salahuddin' }}</div>
            <p class="text-xs text-slate-500">KPP Madya Malang • Kementerian Keuangan RI</p>
        </footer>

    </main>

</div>
