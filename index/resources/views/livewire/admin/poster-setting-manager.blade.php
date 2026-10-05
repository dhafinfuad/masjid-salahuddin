<div class="{{ $isEmbedded ? 'space-y-6' : 'min-h-screen bg-gov-canvas py-6 px-4 sm:px-6 lg:px-8' }}"
     x-data="posterSettingStudio({
        template: @entangle('template'),
        masjid_line1: @entangle('masjid_line1'),
        masjid_line2: @entangle('masjid_line2'),
        footer_label: @entangle('footer_label'),
        footer_url: @entangle('footer_url'),
        logo_url: @entangle('logo_url'),
        logo_size: @entangle('logo_size'),
        title_font_size: @entangle('title_font_size'),
        desc_font_size: @entangle('desc_font_size'),
        title_y: @entangle('title_y'),
        desc_y: @entangle('desc_y'),
        title_desc_gap: @entangle('title_desc_gap'),
        schedule_gap: @entangle('schedule_gap'),
        content_y: @entangle('content_y'),
        show_pattern: @entangle('show_pattern'),
        show_leaves: @entangle('show_leaves'),
        preview_title1: @entangle('preview_title1'),
        preview_title2: @entangle('preview_title2'),
        preview_subtitle: @entangle('preview_subtitle'),
        preview_date: @entangle('preview_date'),
        preview_time: @entangle('preview_time'),
        preview_location: @entangle('preview_location'),
        preview_speaker: @entangle('preview_speaker'),
        jarkom_kajian_template: @entangle('jarkom_kajian_template'),
        jarkom_jumat_template: @entangle('jarkom_jumat_template')
     })"
     x-init="initStudio()"
     @poster-template-changed.window="onTemplateChanged($event.detail)"
     @open-jarkom-modal.window="openJarkomModal()"
     @close-jarkom-modal.window="closeJarkomModal()">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="w-full mb-5 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gov-border shadow-2xs">
        <div class="flex items-center gap-3">
            @if(!$isEmbedded)
                <a href="{{ url('/admin/kegiatan') }}" wire:navigate
                   class="inline-flex items-center justify-center p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                   title="Kembali ke Manajemen Kegiatan">
                    <svg class="w-4 h-4 text-gov-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
            @endif
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-bold text-gov-textMain leading-tight">Template &amp; Studio Poster Kajian</h1>
                </div>
                <p class="text-xs text-gov-textMuted mt-0.5">Generator Poster Persegi 1:1 (500×500 px &bull; Ekspor HD 720×720 px)</p>
            </div>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2">
            <!-- Tombol Database Ustadz -->
            <button type="button" @click="$dispatch('open-ustadz-modal')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition cursor-pointer shrink-0 whitespace-nowrap"
                    title="Buka Direktori & Database Foto Ustadz">
                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Data Ustadz</span>
            </button>

            <!-- Tombol Edit Jarkom -->
            <button type="button" @click="openJarkomModal()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-gov-textMain text-xs font-semibold shadow-2xs transition cursor-pointer shrink-0 whitespace-nowrap"
                    title="Atur Format Jarkoman WhatsApp Kajian & Khutbah Jumat">
                <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <span>Edit Jarkom</span>
            </button>

            <!-- Tombol Simpan Konfigurasi -->
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-1.5 shrink-0 whitespace-nowrap">
                <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    <span>Simpan Konfigurasi</span>
                </span>
                <span wire:loading.inline-flex wire:target="save" style="display: none;"
                      class="inline-flex flex-row items-center gap-1.5 shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-white inline-block" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="whitespace-nowrap leading-none">Menyimpan...</span>
                </span>
            </button>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN BALANCED STUDIO WORKSPACE -->
    <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- ======================================================== -->
        <!-- LEFT COLUMN: DESAIN, IDENTITAS, TIPOGRAFI -->
        <!-- ======================================================== -->
        <div class="space-y-6 min-w-0">

            <!-- PRESET & TEMPLATE SELECTION CARD -->
            <div class="bg-white rounded-2xl border border-gov-border shadow-2xs p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center text-xs border border-gov-border">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Pilihan Desain &amp; Preset Baku</h2>
                    </div>
                </div>

                <!-- Template Selector -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Desain Template Aktif (Rasio 1:1)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" wire:click="selectTemplate(1)"
                                :class="template === 1 ? 'border-2 border-emerald-600 bg-emerald-50/70 text-emerald-900 font-bold' : 'border border-gov-border hover:bg-slate-50 text-slate-700 font-medium'"
                                class="py-2.5 px-3 rounded-xl text-xs flex items-center justify-center gap-2 transition cursor-pointer shadow-2xs">
                            <span class="w-3 h-3 rounded-full bg-emerald-600 inline-block shrink-0"></span>
                            <span>1. Kajian Tematik</span>
                        </button>
                        <button type="button" wire:click="selectTemplate(2)"
                                :class="template === 2 ? 'border-2 border-blue-600 bg-blue-50/70 text-blue-900 font-bold' : 'border border-gov-border hover:bg-slate-50 text-slate-700 font-medium'"
                                class="py-2.5 px-3 rounded-xl text-xs flex items-center justify-center gap-2 transition cursor-pointer shadow-2xs">
                            <span class="w-3 h-3 rounded-full bg-blue-600 inline-block shrink-0"></span>
                            <span>2. Rutin Pekanan</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- IDENTITY & LOGO SETTINGS -->
            <div class="bg-white rounded-2xl border border-gov-border shadow-2xs p-5 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center text-xs border border-gov-border">
                        <svg class="w-4 h-4 text-gov-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Identitas Masjid &amp; Logo</h2>
                </div>

                <!-- Logo Uploader & Size Slider -->
                <div class="bg-slate-50/70 p-3.5 rounded-xl border border-gov-border space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-700">File Logo (PNG/SVG/WebP):</span>
                        @if($logo_url || $logo_file)
                            <button type="button" wire:click="resetLogo" class="text-xs text-rose-600 hover:underline font-semibold cursor-pointer">
                                Reset ke Default
                            </button>
                        @endif
                    </div>

                    <input type="file" wire:model="logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                           @change="
                               const file = $event.target.files[0];
                               if (file) {
                                   const reader = new FileReader();
                                   reader.onload = (e) => {
                                       logo_url = e.target.result;
                                   };
                                   reader.readAsDataURL(file);
                               }
                           "
                           class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gov-navy file:text-white hover:file:bg-gov-navyHover cursor-pointer">

                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <button type="button" wire:click="useDefaultMosqueLogo"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[11px] font-semibold transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Gunakan Logo Resmi Masjid Salahuddin</span>
                        </button>
                    </div>

                    <div wire:loading wire:target="logo_file" class="text-[11px] text-amber-600 font-medium flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengunggah pratinjau logo...</span>
                    </div>

                    <!-- Logo Size Slider (20px - 68px) -->
                    <div class="pt-2 border-t border-slate-200">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">Ukuran Logo:</span>
                            <span class="font-bold text-gov-navy" x-text="logo_size + 'px'"></span>
                        </div>
                        <input type="range" min="20" max="68" x-model.number="logo_size"
                               class="w-full accent-gov-navy cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400 font-medium mt-0.5">
                            <span>20px (Kecil)</span>
                            <span>32px (Standar)</span>
                            <span>68px (Besar)</span>
                        </div>
                    </div>
                </div>

                <!-- Masjid Text Inputs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Teks Baris 1 (Instansi/Atas)</label>
                        <input type="text" x-model="masjid_line1"
                               class="w-full text-xs px-3 py-2 border border-gov-border rounded-lg bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Teks Baris 2 (Nama Masjid)</label>
                        <input type="text" x-model="masjid_line2"
                               class="w-full text-xs px-3 py-2 border border-gov-border rounded-lg bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    </div>
                </div>
            </div>

            <!-- TITLE & DESCRIPTION TUNING SLIDERS -->
            <div class="bg-white rounded-2xl border border-gov-border shadow-2xs p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center text-xs border border-gov-border">
                            <svg class="w-4 h-4 text-gov-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Isi &amp; Tipografi Judul / Deskripsi</h2>
                    </div>
                    <button type="button" @click="resetTitleSliders()" class="text-xs text-rose-600 hover:underline font-semibold cursor-pointer">
                        Reset Slider
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- EDIT ISI TEKS JUDUL & DESKRIPSI -->
                    <div class="bg-slate-50/70 p-3.5 rounded-xl border border-gov-border space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                            <span class="text-xs font-bold text-slate-700">Isi Teks Judul &amp; Deskripsi</span>
                            <span class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Pratinjau Langsung</span>
                        </div>

                        <!-- Input Judul Sesuai Template -->
                        <template x-if="template === 1">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Kata 1 (Atas/Kiri)</label>
                                    <input type="text" x-model="preview_title1" placeholder="Contoh: Kajian"
                                           class="w-full text-xs px-3 py-2 border border-gov-border rounded-lg bg-white focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Judul Kata 2 (Bawah/Kanan)</label>
                                    <input type="text" x-model="preview_title2" placeholder="Contoh: Tematik"
                                           class="w-full text-xs px-3 py-2 border border-gov-border rounded-lg bg-white focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                                </div>
                            </div>
                        </template>

                        <template x-if="template === 2">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Teks Judul Utama (Kapital Hijau)</label>
                                <input type="text" x-model="preview_title2" placeholder="Contoh: KAJIAN PEKANAN"
                                       class="w-full text-xs px-3 py-2 border border-gov-border rounded-lg bg-white focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-bold uppercase tracking-wide">
                            </div>
                        </template>

                        <!-- Input Deskripsi / Sub-Judul -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-600">Teks Deskripsi / Tema Kajian</label>
                                <span class="text-[10px] text-slate-400">Bisa multi-baris (tekan Enter)</span>
                            </div>
                            <textarea x-model="preview_subtitle" rows="3" placeholder="Tuliskan tema kajian atau keterangan poster..."
                                      class="w-full text-xs px-3 py-2 border border-gov-border rounded-lg bg-white focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium leading-relaxed resize-y"></textarea>
                        </div>
                    </div>
                    <!-- Slider Ukuran Font Judul (26px - 54px) -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">Ukuran Font Judul:</span>
                            <span class="font-bold text-gov-navy" x-text="title_font_size + 'px'"></span>
                        </div>
                        <input type="range" min="26" max="54" x-model.number="title_font_size"
                               class="w-full accent-gov-navy cursor-pointer">
                    </div>

                    <!-- Slider Ukuran Font Deskripsi (10px - 22px) -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">Ukuran Font Deskripsi:</span>
                            <span class="font-bold text-gov-navy" x-text="desc_font_size + 'px'"></span>
                        </div>
                        <input type="range" min="10" max="22" x-model.number="desc_font_size"
                               class="w-full accent-gov-navy cursor-pointer">
                    </div>

                    <!-- Slider Jarak Judul & Deskripsi (4px - 32px) -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">Jarak Judul &amp; Deskripsi:</span>
                            <span class="font-bold text-gov-navy" x-text="title_desc_gap + 'px'"></span>
                        </div>
                        <input type="range" min="4" max="32" x-model.number="title_desc_gap"
                               class="w-full accent-gov-navy cursor-pointer">
                    </div>

                    <!-- Slider Posisi Vertikal Judul (-20px s.d. 30px) -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">Posisi Vertikal Judul (Offset Y):</span>
                            <span class="font-bold text-gov-navy" x-text="title_y + 'px'"></span>
                        </div>
                        <input type="range" min="-20" max="30" x-model.number="title_y"
                               class="w-full accent-gov-navy cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400 font-medium mt-0.5">
                            <span>▲ Naik (-20px)</span>
                            <span>Netral (0px)</span>
                            <span>▼ Turun (+30px)</span>
                        </div>
                    </div>

                    <!-- Slider Posisi Vertikal Deskripsi (-20px s.d. 30px) -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">Posisi Vertikal Deskripsi (Offset Y):</span>
                            <span class="font-bold text-gov-navy" x-text="desc_y + 'px'"></span>
                        </div>
                        <input type="range" min="-20" max="30" x-model.number="desc_y"
                               class="w-full accent-gov-navy cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400 font-medium mt-0.5">
                            <span>▲ Naik (-20px)</span>
                            <span>Netral (0px)</span>
                            <span>▼ Turun (+30px)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- RIGHT COLUMN: LIVE CANVAS PREVIEW, JADWAL, FOOTER -->
        <!-- ======================================================== -->
        <div class="flex flex-col gap-6 min-w-0">

            <!-- POSTER CANVAS WRAPPER (Auto-Scale to Fit Any Screen, di mobile dipindah ke paling bawah) -->
            <div x-ref="posterBox"
                 class="order-last lg:order-first p-3 sm:p-4 bg-slate-300/60 rounded-3xl shadow-inner w-full flex flex-col items-center justify-center overflow-hidden">

                <!-- Scaling Container Box: Menyesuaikan dimensi fisik wrapper ke ukuran scaled -->
                <div :style="`width: ${Math.round(500 * canvasScale)}px; height: ${Math.round(500 * canvasScale)}px;`"
                     class="relative overflow-hidden shadow-2xl rounded-2xl shrink-0 transition-[width,height] duration-150">

                    <!-- Scaled Inner Wrapper: Ukuran asli 500x500 di-scale secara proporsional -->
                    <div :style="`transform: scale(${canvasScale}); transform-origin: top left; width: 500px; height: 500px;`"
                         class="absolute top-0 left-0 leading-none shrink-0 select-none">

                        <!-- POSTER CONTAINER: STRICT 1:1 SQUARE (500px x 500px, ZERO BORDER/OUTLINE) -->
                        <div id="posterPreviewCard"
                         :style="'width: 500px; height: 500px; border: none; outline: none; box-shadow: none; background: ' + (template === 1 ? '#fbfcf9' : 'linear-gradient(155deg, #0264d6 0%, #0042a5 100%)') + ';'"
                         class="relative overflow-hidden select-none flex flex-col justify-between transition-colors duration-300">

                        <!-- BACKGROUND WATERMARK PATTERN -->
                        <div id="previewBgPatternLayer"
                             class="absolute inset-0 z-0 pointer-events-none transition-opacity duration-300"
                             :style="'opacity: ' + (template === 1 ? '0.14' : '0.15') + ';'"
                             x-show="show_pattern">
                            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                                <defs>
                                    <!-- Pattern Template 1 (Hijau Halus Lembut) -->
                                    <pattern id="studioIslamicPatternTpl1" width="90" height="90" patternUnits="userSpaceOnUse">
                                        <path d="M45,0 L57,33 L90,45 L57,57 L45,90 L33,57 L0,45 L33,33 Z" fill="none" stroke="#256e3b" stroke-width="0.85"/>
                                        <circle cx="45" cy="45" r="28" fill="none" stroke="#256e3b" stroke-width="0.7"/>
                                        <circle cx="0" cy="0" r="18" fill="none" stroke="#256e3b" stroke-width="0.6"/>
                                        <circle cx="90" cy="0" r="18" fill="none" stroke="#256e3b" stroke-width="0.6"/>
                                        <circle cx="0" cy="90" r="18" fill="none" stroke="#256e3b" stroke-width="0.6"/>
                                        <circle cx="90" cy="90" r="18" fill="none" stroke="#256e3b" stroke-width="0.6"/>
                                    </pattern>

                                    <!-- Pattern Template 2 (Putih Bersih 15% di Atas Biru) -->
                                    <pattern id="studioIslamicPatternTpl2" width="90" height="90" patternUnits="userSpaceOnUse">
                                        <path d="M45,0 L57,33 L90,45 L57,57 L45,90 L33,57 L0,45 L33,33 Z" fill="none" stroke="#ffffff" stroke-width="0.95"/>
                                        <circle cx="45" cy="45" r="28" fill="none" stroke="#ffffff" stroke-width="0.8"/>
                                        <circle cx="0" cy="0" r="18" fill="none" stroke="#ffffff" stroke-width="0.7"/>
                                        <circle cx="90" cy="0" r="18" fill="none" stroke="#ffffff" stroke-width="0.7"/>
                                        <circle cx="0" cy="90" r="18" fill="none" stroke="#ffffff" stroke-width="0.7"/>
                                        <circle cx="90" cy="90" r="18" fill="none" stroke="#ffffff" stroke-width="0.7"/>
                                    </pattern>
                                </defs>
                                <rect width="100%" height="100%" :fill="template === 1 ? 'url(#studioIslamicPatternTpl1)' : 'url(#studioIslamicPatternTpl2)'"/>
                            </svg>
                        </div>

                        <!-- ORNAMENTS: NATURAL LEAVES & ACCENTS -->
                        <div class="absolute inset-0 z-10 pointer-events-none" x-show="show_leaves">
                            <!-- Ornamen Template 1 (Botani Hijau) -->
                            <div x-show="template === 1">
                                <!-- Leaf 1: Kiri Atas -->
                                <div class="absolute -top-3 left-6 w-14 h-14 drop-shadow-md">
                                    <svg viewBox="0 0 100 100" class="w-full h-full fill-[#3a7528]">
                                        <path d="M15,85 C20,40 50,15 90,10 C80,55 55,80 15,85 Z"/>
                                        <path d="M15,85 Q50,50 90,10" stroke="#244b17" stroke-width="2.5" fill="none"/>
                                    </svg>
                                </div>
                                <!-- Leaf 2: Kanan Atas dengan Soft Blur -->
                                <div class="absolute top-5 -right-5 w-16 h-16 filter blur-[2px] opacity-90 drop-shadow-sm">
                                    <svg viewBox="0 0 100 100" class="w-full h-full fill-[#45852e] transform rotate-[135deg]">
                                        <path d="M15,85 C20,40 50,15 90,10 C80,55 55,80 15,85 Z"/>
                                    </svg>
                                </div>
                                <!-- Leaf 3: Sisi Kiri Tengah -->
                                <div class="absolute top-[280px] -left-4 w-12 h-12 drop-shadow-md transition-all">
                                    <svg viewBox="0 0 100 100" class="w-full h-full fill-[#2e6822] transform -rotate-45">
                                        <path d="M15,85 C20,40 50,15 90,10 C80,55 55,80 15,85 Z"/>
                                        <path d="M15,85 Q50,50 90,10" stroke="#1d4415" stroke-width="2.5" fill="none"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Ornamen Template 2 (Dedaunan Sisi Kanan & Kiri) -->
                            <div x-show="template === 2">
                                <!-- Daun Kiri Atas -->
                                <div class="absolute -top-2 left-5 w-14 h-14 drop-shadow-md transform -rotate-12">
                                    <svg viewBox="0 0 100 100" class="w-full h-full fill-[#4ba824]">
                                        <path d="M15,85 C20,40 50,15 90,10 C80,55 55,80 15,85 Z"/>
                                        <path d="M15,85 Q50,50 90,10" stroke="#256411" stroke-width="2.5" fill="none"/>
                                    </svg>
                                </div>
                                <!-- Daun Kiri Tengah -->
                                <div class="absolute top-[270px] -left-4 w-12 h-12 drop-shadow-md transform -rotate-45">
                                    <svg viewBox="0 0 100 100" class="w-full h-full fill-[#3a8e1e]">
                                        <path d="M15,85 C20,40 50,15 90,10 C80,55 55,80 15,85 Z"/>
                                        <path d="M15,85 Q50,50 90,10" stroke="#1f5210" stroke-width="2.5" fill="none"/>
                                    </svg>
                                </div>
                                <!-- Daun Kanan Atas -->
                                <div class="absolute -top-3 -right-2 w-16 h-16 drop-shadow-md transform rotate-[130deg]">
                                    <svg viewBox="0 0 100 100" class="w-full h-full fill-[#52b826]">
                                        <path d="M15,85 C20,40 50,15 90,10 C80,55 55,80 15,85 Z"/>
                                        <path d="M15,85 Q50,50 90,10" stroke="#2a6e14" stroke-width="2.5" fill="none"/>
                                    </svg>
                                </div>
                                <!-- Daun Kanan Tengah (Soft Depth Blur) -->
                                <div class="absolute top-[215px] -right-5 w-13 h-13 filter blur-[1.5px] opacity-90 drop-shadow-sm transform rotate-[65deg]">
                                    <svg viewBox="0 0 100 100" class="w-full h-full fill-[#429c20]">
                                        <path d="M15,85 C20,40 50,15 90,10 C80,55 55,80 15,85 Z"/>
                                        <path d="M15,85 Q50,50 90,10" stroke="#225910" stroke-width="2.5" fill="none"/>
                                    </svg>
                                </div>
                                <!-- Daun Kanan Bawah -->
                                <div class="absolute bottom-8 -right-3 w-14 h-14 drop-shadow-md transform rotate-[210deg]">
                                    <svg viewBox="0 0 100 100" class="w-full h-full fill-[#4ba824]">
                                        <path d="M15,85 C20,40 50,15 90,10 C80,55 55,80 15,85 Z"/>
                                        <path d="M15,85 Q50,50 90,10" stroke="#2d6e13" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- TOP SECTION: LOGO & JUDUL -->
                        <div class="relative z-20 pt-4 px-6 flex flex-col items-center transition-transform duration-150">

                            <!-- LOGO MASJID & IDENTITY -->
                            <div class="flex items-center gap-2.5 justify-center mb-1">
                                <div class="flex items-center justify-center shrink-0">
                                    <template x-if="logo_url">
                                        <img :src="logo_url" alt="Logo" :style="'height: ' + logo_size + 'px;'" class="object-contain max-h-14">
                                    </template>
                                    <template x-if="!logo_url">
                                        <img src="/resources/Logo Masjid Salahuddin.svg" alt="Logo Resmi" :style="'height: ' + logo_size + 'px;'" class="object-contain max-h-14">
                                    </template>
                                </div>
                                <div class="text-left leading-tight">
                                    <div :class="template === 1 ? 'text-[#334155]' : 'text-[#dbeafe]'"
                                         class="text-[11px] font-semibold tracking-wide"
                                         x-text="masjid_line1"></div>
                                    <div :class="template === 1 ? 'text-[#16532b]' : 'text-[#ffffff]'"
                                         class="text-[13px] font-extrabold tracking-tight"
                                         x-text="masjid_line2"></div>
                                </div>
                            </div>

                            <!-- MAIN TITLE & SUBTITLE -->
                            <div class="mt-1 text-center w-full flex flex-col items-center">
                                <!-- Judul Template 1: Serif Playfair Display #16532b -->
                                <template x-if="template === 1">
                                    <div class="font-serifTitle leading-[1.1] tracking-tight font-extrabold text-[#16532b] transition-transform duration-75"
                                         :style="'font-size: ' + title_font_size + 'px; transform: translateY(' + title_y + 'px);'">
                                        <span x-text="preview_title1">Kajian</span>
                                        <span x-text="preview_title2">Tematik</span>
                                    </div>
                                </template>

                                <!-- Judul Template 2: Sans-serif Bold Neon Green #51e91f (Kata 1 Hidden) -->
                                <template x-if="template === 2">
                                    <div class="font-sans uppercase leading-[0.98] font-black tracking-tight text-center transition-transform duration-75"
                                         :style="'font-size: ' + title_font_size + 'px; transform: translateY(' + title_y + 'px);'">
                                        <span class="block text-[#51e91f]" x-text="preview_title2 || 'KAJIAN PEKANAN'"></span>
                                    </div>
                                </template>

                                <!-- Deskripsi / Sub-Judul Kajian -->
                                <div :class="template === 1 ? 'font-sans italic font-semibold text-[#1e6b36]' : 'font-sans italic font-semibold text-[#dbeafe]'"
                                     class="leading-snug max-w-[430px] mx-auto whitespace-pre-line tracking-normal transition-transform duration-75"
                                     :style="'font-size: ' + desc_font_size + 'px; margin-top: ' + title_desc_gap + 'px; transform: translateY(' + desc_y + 'px);'"
                                     x-text="preview_subtitle">
                                </div>
                            </div>

                        </div>

                        <!-- MIDDLE SECTION: JADWAL & FOTO POLAROID -->
                        <div id="previewMiddleWrap"
                             class="relative z-20 px-6 flex items-center justify-between my-auto transition-transform duration-150"
                             :style="'transform: translateY(' + content_y + 'px);'">

                            <!-- INFO SCHEDULE CARD WRAPPER WITH SOFT RASTER SHADOW -->
                            <div class="relative shrink-0">
                                <img id="previewScheduleShadowImg" class="absolute pointer-events-none -z-10 select-none max-w-none" alt="" />

                                <div id="previewScheduleCard"
                                     :class="template === 1 ? 'bg-gradient-to-br from-[#1b6531] to-[#257d3d] text-[#ffffff]' : 'bg-[#ffffff] text-[#1e293b]'"
                                     class="w-[218px] rounded-2xl py-5 px-3.5 relative z-10 shrink-0 transition-all duration-300">

                                     <div class="flex flex-col" :style="'gap: ' + schedule_gap + 'px;'">

                                        <!-- Item 1: Tanggal -->
                                        <div class="flex items-center gap-2.5">
                                            <div :class="template === 1 ? 'bg-[#85ce38] text-[#16532b]' : 'bg-[#0052cc] text-[#ffffff]'"
                                                 class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                                    <circle cx="8" cy="14" r="0.9" fill="currentColor"></circle>
                                                    <circle cx="12" cy="14" r="0.9" fill="currentColor"></circle>
                                                    <circle cx="16" cy="14" r="0.9" fill="currentColor"></circle>
                                                    <circle cx="8" cy="17" r="0.9" fill="currentColor"></circle>
                                                    <circle cx="12" cy="17" r="0.9" fill="currentColor"></circle>
                                                </svg>
                                            </div>
                                            <div class="flex flex-col justify-center -translate-y-[2px]">
                                                <div :class="template === 1 ? 'text-[10px] font-semibold text-[#d1fae5] leading-none' : 'text-[10px] font-bold text-[#64748b] uppercase tracking-wide leading-none'">Tanggal</div>
                                                <div :class="template === 1 ? 'text-[#ffffff]' : 'text-[#0f172a]'"
                                                     class="text-[12px] font-extrabold tracking-tight leading-tight mt-0.5"
                                                     x-text="preview_date"></div>
                                            </div>
                                        </div>

                                        <!-- Item 2: Pukul -->
                                        <div class="flex items-center gap-2.5">
                                            <div :class="template === 1 ? 'bg-[#85ce38] text-[#16532b]' : 'bg-[#0052cc] text-[#ffffff]'"
                                                 class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="9"></circle>
                                                    <polyline points="12 7 12 12 15.5 14"></polyline>
                                                </svg>
                                            </div>
                                            <div class="flex flex-col justify-center -translate-y-[2px]">
                                                <div :class="template === 1 ? 'text-[10px] font-semibold text-[#d1fae5] leading-none' : 'text-[10px] font-bold text-[#64748b] uppercase tracking-wide leading-none'">Pukul</div>
                                                <div :class="template === 1 ? 'text-[#ffffff]' : 'text-[#0f172a]'"
                                                     class="text-[12px] font-extrabold tracking-tight leading-tight mt-0.5"
                                                     x-text="preview_time"></div>
                                            </div>
                                        </div>

                                        <!-- Item 3: Lokasi -->
                                        <div class="flex items-center gap-2.5">
                                            <div :class="template === 1 ? 'bg-[#85ce38] text-[#16532b]' : 'bg-[#0052cc] text-[#ffffff]'"
                                                 class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                                    <circle cx="12" cy="10" r="2.8" fill="currentColor"></circle>
                                                </svg>
                                            </div>
                                            <div class="flex flex-col justify-center -translate-y-[2px]">
                                                <div :class="template === 1 ? 'text-[10px] font-semibold text-[#d1fae5] leading-none' : 'text-[10px] font-bold text-[#64748b] uppercase tracking-wide leading-none'">Lokasi</div>
                                                <div :class="template === 1 ? 'text-[#ffffff]' : 'text-[#0f172a]'"
                                                     class="text-[12px] font-extrabold tracking-tight leading-tight mt-0.5"
                                                     x-text="preview_location"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- POLAROID PHOTO FRAME -->
                            <div class="relative shrink-0 transform -rotate-[2.5deg] origin-bottom-left transition-all duration-300">
                                <img id="previewPolaroidShadowImg" class="absolute pointer-events-none -z-10 select-none max-w-none" alt="" />

                                <div id="previewPolaroidCard" class="w-[195px] bg-[#ffffff] rounded-lg p-2 pb-3 border border-[#e2e8f0]/50 relative z-10">
                                    <div class="w-full aspect-square bg-[#cfe7f7] rounded overflow-hidden relative border border-[#e2e8f0]/40">
                                        <canvas id="previewSpeakerCanvas" width="400" height="400" class="w-full h-full block"></canvas>
                                    </div>
                                    <div class="mt-2 text-[11px] font-bold text-[#1e293b] leading-snug whitespace-pre-line tracking-tight"
                                         x-text="preview_speaker">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- BOTTOM SECTION: FOOTER RIBBON (Teks diturunkan ke tengah) -->
                        <div id="previewFooterRibbon"
                             :class="template === 1 ? 'bg-[#17532a] text-[#ffffff]' : 'bg-[#ffffff] text-[#1e293b]'"
                             class="relative z-20 w-full h-10 px-6 flex items-center transition-all duration-300">
                            <div class="flex items-center gap-1.5 text-[11.5px] tracking-tight leading-none -translate-y-[1px] my-auto">
                                <span class="font-bold" x-text="footer_label">Informasi Kajian :</span>
                                <span class="font-medium opacity-95" x-text="footer_url">masjidsalahuddin.my.id</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

            <!-- SCHEDULE & CONTENT POSITION SLIDERS -->
            <div class="bg-white rounded-2xl border border-gov-border shadow-2xs p-5 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center text-xs border border-gov-border">
                        <svg class="w-4 h-4 text-gov-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Jadwal &amp; Offset Konten Tengah</h2>
                </div>

                <div class="space-y-3.5">
                    <!-- Slider Jarak Antar Baris Jadwal (8px - 26px) -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">Jarak Antar Baris Jadwal:</span>
                            <span class="font-bold text-gov-navy" x-text="schedule_gap + 'px'"></span>
                        </div>
                        <input type="range" min="8" max="26" x-model.number="schedule_gap"
                               @input="$nextTick(() => updateRasterShadows())"
                               class="w-full accent-gov-navy cursor-pointer">
                    </div>

                    <!-- Slider Offset Vertikal Konten Utama (-50px s.d. 40px) -->
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">Offset Vertikal Konten Utama (Y):</span>
                            <span class="font-bold text-gov-navy" x-text="content_y + 'px'"></span>
                        </div>
                        <input type="range" min="-50" max="40" x-model.number="content_y"
                               class="w-full accent-gov-navy cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400 font-medium mt-0.5">
                            <span>▲ Naik (-50px)</span>
                            <span>Tengah (0px)</span>
                            <span>▼ Turun (+40px)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER SETTINGS -->
            <div class="bg-white rounded-2xl border border-gov-border shadow-2xs p-5 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center text-xs border border-gov-border">
                        <svg class="w-4 h-4 text-gov-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Teks Footer Pita Bawah</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Label Kiri Footer</label>
                        <input type="text" x-model="footer_label"
                               class="w-full text-xs px-3 py-2 border border-gov-border rounded-lg bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">URL / Link Web</label>
                        <input type="text" x-model="footer_url"
                               class="w-full text-xs px-3 py-2 border border-gov-border rounded-lg bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ======================================================== -->
    <!-- MODAL: PENGATURAN FORMAT JARKOMAN WHATSAPP -->
    <!-- ======================================================== -->
    <div x-show="showJarkomModal" x-cloak wire:ignore.self
         @keydown.escape.window="closeJarkomModal()"
         @click="if (window.isBackdropClick ? window.isBackdropClick($event, $el) : $event.target === $el) { closeJarkomModal(); }"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-5xl w-full p-5 sm:p-6 shadow-2xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[92vh] flex flex-col"
             @click.stop>
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-200 pb-3 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center border border-indigo-200 shrink-0">
                        <svg class="w-5 h-5 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">Format Jarkoman WhatsApp</h3>
                        <p class="text-xs text-slate-500">Kustomisasi draf pesan broadcast WhatsApp untuk Kajian dan Khutbah Jumat.</p>
                    </div>
                </div>
                <button type="button" @click="closeJarkomModal()"
                        class="text-slate-400 hover:text-gov-textMain p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer"
                        title="Tutup Modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Sub-tab Navigation (0ms Alpine Switching) -->
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2.5 shrink-0 w-full">
                <button type="button" @click="jarkomSubTab = 'kajian'"
                        :class="jarkomSubTab === 'kajian' ? 'bg-gov-navy text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2 rounded-lg text-xs font-bold transition cursor-pointer flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Kajian</span>
                </button>
                <button type="button" @click="jarkomSubTab = 'jumat'"
                        :class="jarkomSubTab === 'jumat' ? 'bg-gov-navy text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2 rounded-lg text-xs font-bold transition cursor-pointer flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Khutbah Jumat</span>
                </button>
            </div>

            <!-- Scrollable Content Area: 2 Column Editor + Preview -->
            <div class="w-full flex-1 overflow-y-auto space-y-4 pr-1 min-w-0">
                <div class="w-full min-w-0 grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                    <!-- Left Column: Template Editor -->
                    <div class="w-full min-w-0 flex flex-col space-y-3">
                        <!-- Kajian Tab Editor -->
                        <div x-show="jarkomSubTab === 'kajian'" class="w-full min-w-0 space-y-3">
                            <div class="flex items-center justify-between pb-0.5">
                                <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gov-navy shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Format Template Teks Kajian</span>
                                </label>
                                <button type="button" wire:click="resetJarkomToDefault('kajian')"
                                        class="text-[11px] text-gov-navy hover:underline font-semibold cursor-pointer">
                                    Reset ke Default
                                </button>
                            </div>
                            <textarea id="jarkomKajianTextarea"
                                      x-model="jarkom_kajian_template"
                                      rows="11"
                                      class="w-full min-w-0 font-mono text-xs p-3.5 rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition leading-relaxed text-slate-800 shadow-2xs resize-y"
                                      style="min-height: 220px;"
                                      placeholder="Tuliskan format jarkoman kajian..."></textarea>
                            @error('jarkom_kajian_template')
                                <span class="text-xs font-semibold text-rose-600 block">{{ $message }}</span>
                            @enderror

                            <!-- Variable Tags (Minimalist Pill Buttons, No Excessive Badges) -->
                            <div class="w-full min-w-0 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                                <span class="text-[11px] font-semibold text-slate-600 block">Sisipkan Variabel (klik untuk menyisipkan ke kursor):</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" @click="insertJarkomTag('{jenis}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan jenis kajian (Pekanan / Tematik)">
                                        {jenis}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{pemateri}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan nama pemateri / ustadz">
                                        {pemateri}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{hari_tanggal}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan hari dan tanggal">
                                        {hari_tanggal}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{tanggal}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan tanggal masehi saja">
                                        {tanggal}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{waktu}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan waktu pelaksanaan kajian">
                                        {waktu}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{tempat}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan lokasi tempat kajian">
                                        {tempat}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{judul}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan judul / tema kajian">
                                        {judul}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{masjid}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan nama masjid">
                                        {masjid}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Khutbah Jumat Tab Editor -->
                        <div x-show="jarkomSubTab === 'jumat'" class="w-full min-w-0 space-y-3">
                            <div class="flex items-center justify-between pb-0.5">
                                <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gov-navy shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Format Template Teks Khutbah Jumat</span>
                                </label>
                                <button type="button" wire:click="resetJarkomToDefault('jumat')"
                                        class="text-[11px] text-gov-navy hover:underline font-semibold cursor-pointer">
                                    Reset ke Default
                                </button>
                            </div>
                            <textarea id="jarkomJumatTextarea"
                                      x-model="jarkom_jumat_template"
                                      rows="11"
                                      class="w-full min-w-0 font-mono text-xs p-3.5 rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition leading-relaxed text-slate-800 shadow-2xs resize-y"
                                      style="min-height: 220px;"
                                      placeholder="Tuliskan format jarkoman sholat jumat..."></textarea>
                            @error('jarkom_jumat_template')
                                <span class="text-xs font-semibold text-rose-600 block">{{ $message }}</span>
                            @enderror

                            <!-- Variable Tags (Minimalist Pill Buttons, No Excessive Badges) -->
                            <div class="w-full min-w-0 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                                <span class="text-[11px] font-semibold text-slate-600 block">Sisipkan Variabel (klik untuk menyisipkan ke kursor):</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" @click="insertJarkomTag('{khatib}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan nama khatib">
                                        {khatib}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{muadzin}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan nama muadzin">
                                        {muadzin}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{mc}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan nama MC">
                                        {mc}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{waktu_dzuhur}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan waktu adzan dzuhur">
                                        {waktu_dzuhur}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{tanggal_hijri}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan tanggal Hijriah">
                                        {tanggal_hijri}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{tanggal_masehi}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan tanggal Masehi">
                                        {tanggal_masehi}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{hari_tanggal}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan hari dan tanggal lengkap">
                                        {hari_tanggal}
                                    </button>
                                    <button type="button" @click="insertJarkomTag('{masjid}')"
                                            class="px-2.5 py-1 rounded-md text-[11px] font-mono font-medium bg-white hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-300 shadow-2xs transition cursor-pointer shrink-0"
                                            title="Sisipkan nama masjid">
                                        {masjid}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Realtime WhatsApp Preview -->
                    <div class="w-full min-w-0 flex flex-col space-y-2">
                        <div class="flex items-center justify-between pb-0.5">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                                <span>Pratinjau Pesan WhatsApp</span>
                            </span>
                            <span class="text-[11px] text-slate-500 font-medium">Simulasi Live</span>
                        </div>

                        <!-- WhatsApp Mockup Window Container -->
                        <div class="w-full min-w-0 rounded-xl border border-slate-300 overflow-hidden shadow-2xs bg-[#efeae2] flex flex-col">
                            <!-- WhatsApp Header Bar -->
                            <div class="bg-[#075e54] text-white px-3.5 py-2.5 flex items-center justify-between shrink-0">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-emerald-800 flex items-center justify-center text-white text-sm font-bold shrink-0 shadow-inner">
                                        🕌
                                    </div>
                                    <div class="leading-tight min-w-0">
                                        <p class="text-xs font-bold text-white truncate" x-text="masjid_line2 || 'Masjid Salahuddin'"></p>
                                        <p class="text-[10px] text-emerald-200">Info Broadcast DKM</p>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-700/60 text-emerald-100 font-medium border border-emerald-600/40">WhatsApp</span>
                            </div>

                            <!-- Chat Area -->
                            <div class="p-3.5 sm:p-4 flex-1 overflow-y-auto max-h-[380px] space-y-2">
                                <div class="w-full flex justify-start">
                                    <div class="w-full bg-[#dcf8c6] text-slate-900 p-3.5 rounded-xl rounded-tl-xs shadow-xs border border-emerald-300/50 text-xs whitespace-pre-wrap font-sans leading-relaxed break-words">
                                        <div x-text="jarkomSubTab === 'kajian' ? previewJarkomKajian : previewJarkomJumat"></div>
                                        <div class="mt-2.5 flex items-center justify-end gap-1 text-[10px] text-slate-500 select-none">
                                            <span>10:15</span>
                                            <svg class="w-3.5 h-3.5 text-blue-500 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7m-5 0l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-3 border-t border-slate-200 pt-3 shrink-0">
                <p class="text-[11px] text-slate-400">
                    Tag di dalam kurung <span class="font-mono">{...}</span> akan digantikan data jadwal riil saat tombol Salin Jarkom diklik.
                </p>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" @click="closeJarkomModal()"
                            class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs shadow-2xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="saveJarkom" wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-1.5">
                        <span wire:loading.remove wire:target="saveJarkom" class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan Format</span>
                        </span>
                        <span wire:loading.inline-flex wire:target="saveJarkom" style="display: none;"
                              class="inline-flex flex-row items-center gap-1.5 shrink-0 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function posterSettingStudio(params) {
    return {
        template: params.template || 1,
        masjid_line1: params.masjid_line1 || 'KPP Madya Malang',
        masjid_line2: params.masjid_line2 || 'Masjid Salahuddin',
        footer_label: params.footer_label || 'Informasi Kajian :',
        footer_url: params.footer_url || 'masjidsalahuddin.my.id',
        logo_url: params.logo_url || null,
        logo_size: params.logo_size || 32,
        title_font_size: params.title_font_size || 42,
        desc_font_size: params.desc_font_size || 15,
        title_y: params.title_y || 10,
        desc_y: params.desc_y || 8,
        title_desc_gap: params.title_desc_gap || 14,
        schedule_gap: params.schedule_gap || 20,
        content_y: params.content_y || 0,
        show_pattern: params.show_pattern ?? true,
        show_leaves: params.show_leaves ?? true,
        preview_title1: params.preview_title1 || 'Kajian',
        preview_title2: params.preview_title2 || 'Tematik',
        preview_subtitle: params.preview_subtitle || "Judul Kajian Judul Kajian Judul Kajian\nJudul Kajian Judul Kajian",
        preview_date: params.preview_date || '22 Juni 2027',
        preview_time: params.preview_time || '08.30–10.00 WIB',
        preview_location: params.preview_location || 'Masjid Salahuddin',
        preview_speaker: params.preview_speaker || "Nama Ustad Nama Ustad\nNama Ustad",
        jarkom_kajian_template: params.jarkom_kajian_template || '',
        jarkom_jumat_template: params.jarkom_jumat_template || '',
        showJarkomModal: false,
        jarkomSubTab: 'kajian',

        openJarkomModal() {
            this.showJarkomModal = true;
        },

        closeJarkomModal() {
            this.showJarkomModal = false;
        },

        insertJarkomTag(tag) {
            const textareaId = this.jarkomSubTab === 'kajian' ? 'jarkomKajianTextarea' : 'jarkomJumatTextarea';
            const textarea = document.getElementById(textareaId);
            if (!textarea) {
                if (this.jarkomSubTab === 'kajian') {
                    this.jarkom_kajian_template = (this.jarkom_kajian_template || '') + tag;
                } else {
                    this.jarkom_jumat_template = (this.jarkom_jumat_template || '') + tag;
                }
                return;
            }

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const currentVal = textarea.value;
            const newVal = currentVal.substring(0, start) + tag + currentVal.substring(end);

            if (this.jarkomSubTab === 'kajian') {
                this.jarkom_kajian_template = newVal;
            } else {
                this.jarkom_jumat_template = newVal;
            }

            this.$nextTick(() => {
                textarea.focus();
                textarea.setSelectionRange(start + tag.length, start + tag.length);
            });
        },

        get previewJarkomKajian() {
            let text = this.jarkom_kajian_template || '';
            const replacements = {
                '{jenis}': 'Pekanan',
                '{pemateri}': 'Ust. Dr. Muhammad Syafiq, M.A.',
                '{hari_tanggal}': 'Senin, 12 Oktober 2026',
                '{tanggal}': '12 Oktober 2026',
                '{waktu}': 'Setelah Sholat Ashar (15.30 WIB)',
                '{tempat}': 'Masjid Salahuddin, KPP Madya Malang',
                '{judul}': 'Tafsir Tematik Ayat-Ayat Tazkiyatun Nafs',
                '{tema}': 'Tafsir Tematik Ayat-Ayat Tazkiyatun Nafs',
                '{masjid}': this.masjid_line2 || 'Masjid Salahuddin'
            };
            for (const [k, v] of Object.entries(replacements)) {
                text = text.replaceAll(k, v);
            }
            return text;
        },

        get previewJarkomJumat() {
            let text = this.jarkom_jumat_template || '';
            const replacements = {
                '{khatib}': 'Ust. H. Ahmad Dahlan, Lc., M.H.I.',
                '{muadzin}': 'Bilal Ramadhan Al-Farizi',
                '{mc}': 'Ahmad Fauzi (DKM)',
                '{waktu_dzuhur}': '11.35 WIB',
                '{tanggal_hijri}': '24 Rabiul Awal 1448 H',
                '{tanggal_masehi}': '16 Oktober 2026',
                '{hari_tanggal}': 'Jumat, 16 Oktober 2026',
                '{tanggal}': '16 Oktober 2026',
                '{masjid}': this.masjid_line2 || 'Masjid Salahuddin'
            };
            for (const [k, v] of Object.entries(replacements)) {
                text = text.replaceAll(k, v);
            }
            return text;
        },
        isExporting: false,
        canvasScale: 1,
        resizeObserver: null,

        updateCanvasScale() {
            const box = this.$refs.posterBox;
            if (!box) return;
            // clientWidth dikurangi padding horizontal wrapper + safety buffer
            const avail = box.clientWidth - 28;
            if (avail > 0 && avail < 500) {
                this.canvasScale = Math.round((avail / 500) * 1000) / 1000;
            } else {
                this.canvasScale = 1;
            }
        },

        initStudio() {
            this.drawSpeakerDefault();
            this.updateCanvasScale();

            this.$nextTick(() => {
                this.updateCanvasScale();
                this.updateRasterShadows();

                if (window.ResizeObserver && this.$refs.posterBox) {
                    this.resizeObserver = new ResizeObserver(() => {
                        this.updateCanvasScale();
                    });
                    this.resizeObserver.observe(this.$refs.posterBox);
                }
            });

            window.addEventListener('resize', () => {
                this.updateCanvasScale();
            });

            window.addEventListener('tab-changed', () => {
                setTimeout(() => this.updateCanvasScale(), 60);
            });

            this.$watch('template', () => {
                this.$nextTick(() => {
                    this.updateCanvasScale();
                    this.updateRasterShadows();
                });
            });
            this.$watch('schedule_gap', () => {
                this.$nextTick(() => this.updateRasterShadows());
            });
        },

        onTemplateChanged(data) {
            if (data.template) {
                this.template = data.template;
                if (data.template === 2) {
                    if (!this.preview_title2 || this.preview_title2 === 'Tematik' || this.preview_title2 === 'PEKANAN') {
                        this.preview_title1 = '';
                        this.preview_title2 = 'KAJIAN PEKANAN';
                    }
                } else {
                    if (!this.preview_title1) {
                        this.preview_title1 = 'Kajian';
                    }
                    if (!this.preview_title2 || this.preview_title2 === 'PEKANAN' || this.preview_title2 === 'KAJIAN PEKANAN') {
                        this.preview_title2 = 'Tematik';
                    }
                }
            }
            this.$nextTick(() => this.updateRasterShadows());
        },

        resetTitleSliders() {
            if (this.template === 1) {
                this.title_font_size = 42;
                this.desc_font_size = 15;
                this.title_desc_gap = 14;
                this.title_y = 10;
                this.desc_y = 8;
            } else {
                this.title_font_size = 42;
                this.desc_font_size = 15;
                this.title_desc_gap = 14;
                this.title_y = 6;
                this.desc_y = 2;
            }
            this.$nextTick(() => this.updateRasterShadows());
        },

        drawSpeakerDefault() {
            const canvas = document.getElementById('previewSpeakerCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const w = canvas.width;
            const h = canvas.height;

            const skyGrad = ctx.createLinearGradient(0, 0, 0, h);
            skyGrad.addColorStop(0, '#b8ddf5');
            skyGrad.addColorStop(0.65, '#e4f1fb');
            ctx.fillStyle = skyGrad;
            ctx.fillRect(0, 0, w, h);

            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(w * 0.52, h * 0.28, 38, 0, Math.PI * 2);
            ctx.arc(w * 0.43, h * 0.32, 28, 0, Math.PI * 2);
            ctx.arc(w * 0.62, h * 0.33, 26, 0, Math.PI * 2);
            ctx.fill();
            ctx.fillRect(w * 0.40, h * 0.31, 95, 20);

            ctx.fillStyle = '#9cb84e';
            ctx.beginPath();
            ctx.moveTo(0, h * 0.64);
            ctx.bezierCurveTo(w * 0.35, h * 0.55, w * 0.65, h * 0.72, w, h * 0.60);
            ctx.lineTo(w, h);
            ctx.lineTo(0, h);
            ctx.closePath();
            ctx.fill();

            ctx.fillStyle = '#7a961f';
            ctx.beginPath();
            ctx.moveTo(0, h * 0.74);
            ctx.bezierCurveTo(w * 0.4, h * 0.85, w * 0.7, h * 0.68, w, h * 0.76);
            ctx.lineTo(w, h);
            ctx.lineTo(0, h);
            ctx.closePath();
            ctx.fill();
        },

        roundRectPath(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
        },

        createSoftRasterShadow(width, height, radius, shadowLayers, scale = 3) {
            const pad = 50;
            const canvas = document.createElement('canvas');
            canvas.width = Math.ceil((width + pad * 2) * scale);
            canvas.height = Math.ceil((height + pad * 2) * scale);
            const ctx = canvas.getContext('2d');
            ctx.scale(scale, scale);

            shadowLayers.forEach(s => {
                ctx.save();
                ctx.shadowColor = s.color;
                ctx.shadowBlur = s.blur;
                ctx.shadowOffsetX = s.offsetX;
                ctx.shadowOffsetY = s.offsetY;

                ctx.fillStyle = '#000000';
                this.roundRectPath(ctx, pad, pad, width, height, radius);
                ctx.fill();
                ctx.restore();

                // Punch-out bagian tengah agar tidak ada kotak gelap pekat
                ctx.save();
                ctx.globalCompositeOperation = 'destination-out';
                ctx.fillStyle = '#000000';
                this.roundRectPath(ctx, pad, pad, width, height, radius);
                ctx.fill();
                ctx.restore();
            });

            return {
                dataUrl: canvas.toDataURL('image/png'),
                width: width + pad * 2,
                height: height + pad * 2,
                offset: pad
            };
        },

        updateRasterShadows() {
            const scheduleCard = document.getElementById('previewScheduleCard');
            const scheduleImg = document.getElementById('previewScheduleShadowImg');
            const polaroidCard = document.getElementById('previewPolaroidCard');
            const polaroidImg = document.getElementById('previewPolaroidShadowImg');

            if (!scheduleCard || !scheduleImg || !polaroidCard || !polaroidImg) return;

            const sW = scheduleCard.offsetWidth || 218;
            const sH = scheduleCard.offsetHeight || 180;
            const sRadius = 16;

            const pW = polaroidCard.offsetWidth || 195;
            const pH = polaroidCard.offsetHeight || 250;
            const pRadius = 8;

            const isTpl1 = this.template === 1;

            const sShadows = isTpl1 ? [
                { color: 'rgba(20, 60, 30, 0.11)', blur: 24, offsetX: 0, offsetY: 7 },
                { color: 'rgba(0, 0, 0, 0.06)', blur: 8, offsetX: 0, offsetY: 3 }
            ] : [
                { color: 'rgba(0, 15, 55, 0.28)', blur: 24, offsetX: 0, offsetY: 7 },
                { color: 'rgba(0, 10, 35, 0.16)', blur: 8, offsetX: 0, offsetY: 3 }
            ];

            const pShadows = isTpl1 ? [
                { color: 'rgba(25, 45, 25, 0.12)', blur: 26, offsetX: 1, offsetY: 8 },
                { color: 'rgba(0, 0, 0, 0.07)', blur: 8, offsetX: 1, offsetY: 3 }
            ] : [
                { color: 'rgba(0, 15, 55, 0.28)', blur: 26, offsetX: 1, offsetY: 8 },
                { color: 'rgba(0, 10, 35, 0.16)', blur: 8, offsetX: 1, offsetY: 3 }
            ];

            const sRes = this.createSoftRasterShadow(sW, sH, sRadius, sShadows, 2);
            scheduleImg.src = sRes.dataUrl;
            scheduleImg.style.width = `${sRes.width}px`;
            scheduleImg.style.height = `${sRes.height}px`;
            scheduleImg.style.left = `${-sRes.offset}px`;
            scheduleImg.style.top = `${-sRes.offset}px`;

            const pRes = this.createSoftRasterShadow(pW, pH, pRadius, pShadows, 2);
            polaroidImg.src = pRes.dataUrl;
            polaroidImg.style.width = `${pRes.width}px`;
            polaroidImg.style.height = `${pRes.height}px`;
            polaroidImg.style.left = `${-pRes.offset}px`;
            polaroidImg.style.top = `${-pRes.offset}px`;
        },

        async ensureHtml2Canvas() {
            if (typeof window.html2canvas === 'function') return window.html2canvas;
            if (window.getHtml2Canvas) {
                return await window.getHtml2Canvas();
            }
            return window.html2canvas;
        },

        async downloadPreviewHD() {
            if (this.isExporting) return;
            this.isExporting = true;
            try {
                window.dispatchEvent(new CustomEvent('generate-poster', {
                    detail: {
                        template: this.template,
                        title: this.template === 1 ? (this.preview_title1 + ' ' + this.preview_title2) : this.preview_title2,
                        title1: this.preview_title1,
                        title2: this.preview_title2,
                        subtitle: this.preview_subtitle,
                        speaker: this.preview_speaker || 'Ustadz Fulan',
                        date: this.preview_date || 'Senin, 28 September 2026',
                        time: this.preview_time || '09:00 - 11:30',
                        location: this.preview_location || 'Masjid Salahuddin',
                        photo: null,
                        config: {
                            template: this.template,
                            masjid_line1: this.masjid_line1,
                            masjid_line2: this.masjid_line2,
                            footer_label: this.footer_label,
                            footer_url: this.footer_url,
                            logo_url: this.logo_url || '/resources/Logo Masjid Salahuddin.svg',
                            logo_size: this.logo_size,
                            title_font_size: this.title_font_size,
                            desc_font_size: this.desc_font_size,
                            title_y: this.title_y,
                            desc_y: this.desc_y,
                            title_desc_gap: this.title_desc_gap,
                            schedule_gap: this.schedule_gap,
                            content_y: this.content_y,
                            show_pattern: this.show_pattern,
                            show_leaves: this.show_leaves
                        }
                    }
                }));
            } catch (err) {
                console.error('Download error:', err);
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Gagal mengunduh poster: ' + err.message }
                }));
            } finally {
                setTimeout(() => {
                    this.isExporting = false;
                }, 1200);
            }
        }
    };
}
</script>
@endpush
