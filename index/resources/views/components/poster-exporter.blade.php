@props(['config' => []])

@php
    $activeConfig = array_merge(\App\Models\PosterSetting::getAppConfig(), (array) $config);
@endphp

<!-- ============================================================================== -->
<!-- ON-DEMAND POSTER EXPORTER COMPONENT (OFF-SCREEN ISOLATED CONTAINER)            -->
<!-- ZERO DOM BLOAT: RENDERED ONCE OUTSIDE DATA TABLES FOR MAXIMUM SPEED            -->
<script>
    window.posterSettingConfig = {!! json_encode($activeConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
</script>

<div x-data="posterExporter(window.posterSettingConfig)"
     @generate-poster.window="generate($event.detail)">

    <!-- ON-DEMAND POSTER EXPORTER (OFF-SCREEN ISOLATED CONTAINER) -->
    <div id="posterExporterWrapper"
         style="position: fixed; right: 0; bottom: 0; width: 500px; height: 500px; opacity: 0; pointer-events: none; z-index: -9999; overflow: hidden;">

        <!-- POSTER CONTAINER: STRICT 1:1 SQUARE (500px x 500px, ZERO BORDER/OUTLINE) -->
        <div id="posterExportCard"
             :style="'width: 500px; height: 500px; border: none; outline: none; box-shadow: none; background: ' + (state.template === 1 ? '#fbfcf9' : 'linear-gradient(155deg, #0264d6 0%, #0042a5 100%)') + ';'"
             class="relative overflow-hidden select-none flex flex-col justify-between">

            <!-- BACKGROUND WATERMARK PATTERN -->
            <div id="exporterBgPatternLayer"
                 class="absolute inset-0 z-0 pointer-events-none"
                 :style="'opacity: ' + (state.template === 1 ? '0.14' : '0.15') + ';'"
                 x-show="state.show_pattern">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
                    <defs>
                        <!-- Pattern Template 1 (Hijau Halus Lembut) -->
                        <pattern id="exporterIslamicPatternTpl1" width="90" height="90" patternUnits="userSpaceOnUse">
                            <path d="M45,0 L57,33 L90,45 L57,57 L45,90 L33,57 L0,45 L33,33 Z" fill="none" stroke="#256e3b" stroke-width="0.85"/>
                            <circle cx="45" cy="45" r="28" fill="none" stroke="#256e3b" stroke-width="0.7"/>
                            <circle cx="0" cy="0" r="18" fill="none" stroke="#256e3b" stroke-width="0.6"/>
                            <circle cx="90" cy="0" r="18" fill="none" stroke="#256e3b" stroke-width="0.6"/>
                            <circle cx="0" cy="90" r="18" fill="none" stroke="#256e3b" stroke-width="0.6"/>
                            <circle cx="90" cy="90" r="18" fill="none" stroke="#256e3b" stroke-width="0.6"/>
                        </pattern>

                        <!-- Pattern Template 2 (Putih Bersih 15% di Atas Biru) -->
                        <pattern id="exporterIslamicPatternTpl2" width="90" height="90" patternUnits="userSpaceOnUse">
                            <path d="M45,0 L57,33 L90,45 L57,57 L45,90 L33,57 L0,45 L33,33 Z" fill="none" stroke="#ffffff" stroke-width="0.95"/>
                            <circle cx="45" cy="45" r="28" fill="none" stroke="#ffffff" stroke-width="0.8"/>
                            <circle cx="0" cy="0" r="18" fill="none" stroke="#ffffff" stroke-width="0.7"/>
                            <circle cx="90" cy="0" r="18" fill="none" stroke="#ffffff" stroke-width="0.7"/>
                            <circle cx="0" cy="90" r="18" fill="none" stroke="#ffffff" stroke-width="0.7"/>
                            <circle cx="90" cy="90" r="18" fill="none" stroke="#ffffff" stroke-width="0.7"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" :fill="state.template === 1 ? 'url(#exporterIslamicPatternTpl1)' : 'url(#exporterIslamicPatternTpl2)'"/>
                </svg>
            </div>

            <!-- ORNAMENTS: NATURAL LEAVES & ACCENTS -->
            <div class="absolute inset-0 z-10 pointer-events-none" x-show="state.show_leaves">
                <!-- Ornamen Template 1 (Botani Hijau) -->
                <div x-show="state.template === 1">
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
                    <div class="absolute top-[280px] -left-4 w-12 h-12 drop-shadow-md">
                        <svg viewBox="0 0 100 100" class="w-full h-full fill-[#2e6822] transform -rotate-45">
                            <path d="M15,85 C20,40 50,15 90,10 C80,55 55,80 15,85 Z"/>
                            <path d="M15,85 Q50,50 90,10" stroke="#1d4415" stroke-width="2.5" fill="none"/>
                        </svg>
                    </div>
                </div>

                <!-- Ornamen Template 2 (Dedaunan Sisi Kanan & Kiri) -->
                <div x-show="state.template === 2">
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
            <div class="relative z-20 pt-4 px-6 flex flex-col items-center">

                <!-- LOGO MASJID & IDENTITY -->
                <div class="flex items-center gap-2.5 justify-center mb-1">
                    <div class="flex items-center justify-center shrink-0">
                        <template x-if="state.logo_url">
                            <img :src="state.logo_url" alt="Logo" x-on:error="state.logo_url = '/resources/Logo Masjid Salahuddin.svg'" :style="'height: ' + state.logo_size + 'px;'" class="object-contain max-h-14">
                        </template>
                        <template x-if="!state.logo_url">
                            <img src="/resources/Logo Masjid Salahuddin.svg" alt="Logo Resmi" :style="'height: ' + state.logo_size + 'px;'" class="object-contain max-h-14">
                        </template>
                    </div>
                    <div class="text-left leading-tight">
                        <div :class="state.template === 1 ? 'text-[#334155]' : 'text-[#dbeafe]'"
                             class="text-[11px] font-semibold tracking-wide"
                             x-text="state.masjid_line1"></div>
                        <div :class="state.template === 1 ? 'text-[#16532b]' : 'text-[#ffffff]'"
                             class="text-[13px] font-extrabold tracking-tight"
                             x-text="state.masjid_line2"></div>
                    </div>
                </div>

                <!-- MAIN TITLE & SUBTITLE -->
                <div class="mt-1 text-center w-full flex flex-col items-center">
                    <!-- Judul Template 1: Serif Playfair Display #16532b -->
                    <template x-if="state.template === 1">
                        <div class="font-serifTitle leading-[1.1] tracking-tight font-extrabold text-[#16532b]"
                             :style="'font-size: ' + state.title_font_size + 'px; transform: translateY(' + state.title_y + 'px);'">
                            <span x-text="titleP1"></span>
                            <span x-text="titleP2"></span>
                        </div>
                    </template>

                    <!-- Judul Template 2: Sans-serif Bold Neon Green #51e91f -->
                    <template x-if="state.template === 2">
                        <div class="font-sans uppercase leading-[0.98] font-black tracking-tight text-center"
                             :style="'font-size: ' + state.title_font_size + 'px; transform: translateY(' + state.title_y + 'px);'">
                            <span class="block text-[#51e91f]" x-text="titleP2 || 'KAJIAN PEKANAN'"></span>
                        </div>
                    </template>

                    <!-- Deskripsi / Sub-Judul Kajian -->
                    <div :class="state.template === 1 ? 'font-sans italic font-semibold text-[#1e6b36]' : 'font-sans italic font-semibold text-[#dbeafe]'"
                         class="leading-snug max-w-[430px] mx-auto whitespace-pre-line tracking-normal"
                         :style="'font-size: ' + state.desc_font_size + 'px; margin-top: ' + state.title_desc_gap + 'px; transform: translateY(' + state.desc_y + 'px);'"
                         x-text="state.subtitle">
                    </div>
                </div>

            </div>

            <!-- MIDDLE SECTION: JADWAL & FOTO POLAROID -->
            <div id="exporterMiddleWrap"
                 class="relative z-20 px-6 flex items-center justify-between my-auto"
                 :style="'transform: translateY(' + state.content_y + 'px);'">

                <!-- INFO SCHEDULE CARD WRAPPER WITH SOFT RASTER SHADOW -->
                <div class="relative shrink-0">
                    <img id="exporterScheduleShadowImg" class="absolute pointer-events-none -z-10 select-none max-w-none" alt="" />

                    <div id="exporterScheduleCard"
                         :class="state.template === 1 ? 'bg-gradient-to-br from-[#1b6531] to-[#257d3d] text-[#ffffff]' : 'bg-[#ffffff] text-[#1e293b]'"
                         class="w-[218px] rounded-2xl py-5 px-3.5 relative z-10 shrink-0">

                        <div class="flex flex-col" :style="'gap: ' + state.schedule_gap + 'px;'">

                            <!-- Item 1: Tanggal -->
                            <div class="flex items-center gap-2.5">
                                <div :class="state.template === 1 ? 'bg-[#85ce38] text-[#16532b]' : 'bg-[#0052cc] text-[#ffffff]'"
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
                                    <div :class="state.template === 1 ? 'text-[10px] font-semibold text-[#d1fae5] leading-none' : 'text-[10px] font-bold text-[#64748b] uppercase tracking-wide leading-none'">Tanggal</div>
                                    <div :class="state.template === 1 ? 'text-[#ffffff]' : 'text-[#0f172a]'"
                                         class="text-[12px] font-extrabold tracking-tight leading-tight mt-0.5"
                                         x-text="state.date"></div>
                                </div>
                            </div>

                            <!-- Item 2: Pukul -->
                            <div class="flex items-center gap-2.5">
                                <div :class="state.template === 1 ? 'bg-[#85ce38] text-[#16532b]' : 'bg-[#0052cc] text-[#ffffff]'"
                                     class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9"></circle>
                                        <polyline points="12 7 12 12 15.5 14"></polyline>
                                    </svg>
                                </div>
                                <div class="flex flex-col justify-center -translate-y-[2px]">
                                    <div :class="state.template === 1 ? 'text-[10px] font-semibold text-[#d1fae5] leading-none' : 'text-[10px] font-bold text-[#64748b] uppercase tracking-wide leading-none'">Pukul</div>
                                    <div :class="state.template === 1 ? 'text-[#ffffff]' : 'text-[#0f172a]'"
                                         class="text-[12px] font-extrabold tracking-tight leading-tight mt-0.5"
                                         x-text="state.time"></div>
                                </div>
                            </div>

                            <!-- Item 3: Lokasi -->
                            <div class="flex items-center gap-2.5">
                                <div :class="state.template === 1 ? 'bg-[#85ce38] text-[#16532b]' : 'bg-[#0052cc] text-[#ffffff]'"
                                     class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                        <circle cx="12" cy="10" r="2.8" fill="currentColor"></circle>
                                    </svg>
                                </div>
                                <div class="flex flex-col justify-center -translate-y-[2px]">
                                    <div :class="state.template === 1 ? 'text-[10px] font-semibold text-[#d1fae5] leading-none' : 'text-[10px] font-bold text-[#64748b] uppercase tracking-wide leading-none'">Lokasi</div>
                                    <div :class="state.template === 1 ? 'text-[#ffffff]' : 'text-[#0f172a]'"
                                         class="text-[12px] font-extrabold tracking-tight leading-tight mt-0.5"
                                         x-text="state.location"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- POLAROID PHOTO FRAME -->
                <div class="relative shrink-0 transform -rotate-[2.5deg] origin-bottom-left">
                    <img id="exporterPolaroidShadowImg" class="absolute pointer-events-none -z-10 select-none max-w-none" alt="" />

                    <div id="exporterPolaroidCard" class="w-[195px] bg-[#ffffff] rounded-lg p-2 pb-3 border border-[#e2e8f0]/50 relative z-10">
                        <div class="w-full aspect-square bg-[#cfe7f7] rounded overflow-hidden relative border border-[#e2e8f0]/40">
                            <canvas id="exporterSpeakerCanvas" width="400" height="400" class="w-full h-full block"></canvas>
                        </div>
                        <div class="mt-2 text-[11px] font-bold text-[#1e293b] leading-snug whitespace-pre-line tracking-tight"
                             x-text="state.speaker">
                        </div>
                    </div>
                </div>

            </div>

            <!-- BOTTOM SECTION: FOOTER RIBBON (Teks diturunkan ke tengah) -->
            <div id="exporterFooterRibbon"
                 :class="state.template === 1 ? 'bg-[#17532a] text-[#ffffff]' : 'bg-[#ffffff] text-[#1e293b]'"
                 class="relative z-20 w-full h-10 px-6 flex items-center">
                <div class="flex items-center gap-1.5 text-[11.5px] tracking-tight leading-none -translate-y-[1px] my-auto">
                    <span class="font-bold" x-text="state.footer_label">Informasi Kajian :</span>
                    <span class="font-medium opacity-95" x-text="state.footer_url">masjidsalahuddin.my.id</span>
                </div>
            </div>

        </div>

    </div>

    <!-- MODAL KHUSUS IPHONE / IPAD & MOBILE PREVIEW -->
    <div x-show="showIosModal"
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs"
         @keydown.escape.window="closeModal()">
        
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border border-slate-100 flex flex-col max-h-[92vh] animate-in fade-in zoom-in-95 duration-200"
             @click.outside="closeModal()">
            
            <!-- Modal Header -->
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="font-bold text-slate-800 text-sm">Poster Berhasil Dibuat</h3>
                </div>
                <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Modal Body (Preview & Petunjuk iPhone) -->
            <div class="p-4 bg-slate-100/60 flex flex-col items-center justify-center overflow-y-auto">
                <div class="relative w-64 h-64 sm:w-72 sm:h-72 rounded-xl overflow-hidden shadow-lg border border-slate-200 bg-white">
                    <img :src="currentBlobUrl" class="w-full h-full object-contain select-none" alt="Poster Kajian" />
                </div>
                
                <!-- Box Petunjuk Galeri iOS -->
                <div class="mt-3.5 w-full bg-emerald-50 border border-emerald-200/80 rounded-xl p-3 text-xs text-emerald-950 leading-relaxed shadow-xs">
                    <div class="font-bold flex items-center gap-1.5 text-emerald-800 mb-1">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Cara Simpan ke Galeri Foto (iPhone):</span>
                    </div>
                    <ul class="text-slate-700 list-disc list-inside space-y-1 pl-0.5">
                        <li>Tekan tombol hijau <strong>"Simpan ke Galeri / Bagikan"</strong> di bawah, lalu pilih <strong>"Simpan Gambar"</strong> (Save Image).</li>
                        <li>Atau <strong>tekan & tahan (tahan lama)</strong> gambar di atas, lalu pilih <strong>"Simpan ke Foto"</strong>.</li>
                    </ul>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="p-3.5 bg-white border-t border-slate-100 flex flex-col gap-2">
                <button type="button"
                        @click="sharePoster()"
                        class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm rounded-xl shadow-md flex items-center justify-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    <span>Simpan ke Galeri / Bagikan</span>
                </button>
                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="openInNewTab()"
                            class="flex-1 py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition text-center cursor-pointer">
                        Buka Tab Baru
                    </button>
                    <button type="button"
                            @click="closeModal()"
                            class="flex-1 py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition text-center cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function posterExporter(baseConfig) {
    if (typeof window.posterExporter === 'function' && window.posterExporter !== posterExporter) {
        return window.posterExporter(baseConfig || window.posterSettingConfig);
    }
    const config = Object.assign({}, window.posterSettingConfig || {}, baseConfig || {});
    return {
        state: {
            template: config.template || 1,
            masjid_line1: config.masjid_line1 || 'KPP Madya Malang',
            masjid_line2: config.masjid_line2 || 'Masjid Salahuddin',
            footer_label: config.footer_label || 'Informasi Kajian :',
            footer_url: config.footer_url || 'masjidsalahuddin.my.id',
            logo_url: config.logo_url || '/resources/Logo Masjid Salahuddin.svg',
            logo_size: config.logo_size || 32,
            title_font_size: config.title_font_size || 42,
            desc_font_size: config.desc_font_size || 15,
            title_y: config.title_y || 10,
            desc_y: config.desc_y || 8,
            title_desc_gap: config.title_desc_gap || 14,
            schedule_gap: config.schedule_gap || 20,
            content_y: config.content_y || 0,
            show_pattern: config.show_pattern ?? true,
            show_leaves: config.show_leaves ?? true,
            title: 'Kajian Tematik',
            subtitle: '',
            speaker: 'Nama Ustadz',
            date: 'Hari, DD MMMM YYYY',
            time: '08.30–10.00 WIB',
            location: 'Masjid Salahuddin',
            photo: null,
            posX: 0,
            posY: 0,
            zoom: 100
        },
        titleP1: 'Kajian',
        titleP2: 'Tematik',
        isGenerating: false,
        showIosModal: false,
        currentBlobUrl: null,
        currentFile: null,
        currentFileName: '',
        _shadowCache: {},

        async ensureHtml2Canvas() {
            if (typeof window.html2canvas === 'function') return window.html2canvas;
            if (window.getHtml2Canvas) {
                return await window.getHtml2Canvas();
            }
            return window.html2canvas;
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

        createSoftRasterShadow(width, height, radius, shadowLayers, scale = 2) {
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

                // Punch-out bagian tengah
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
            const tpl = this.state.template;
            if (!this._shadowCache[tpl]) {
                const scheduleCard = document.getElementById('exporterScheduleCard');
                const polaroidCard = document.getElementById('exporterPolaroidCard');
                const sW = (scheduleCard && scheduleCard.offsetWidth) || 218;
                const sH = (scheduleCard && scheduleCard.offsetHeight) || 180;
                const pW = (polaroidCard && polaroidCard.offsetWidth) || 195;
                const pH = (polaroidCard && polaroidCard.offsetHeight) || 250;
                const isTpl1 = tpl === 1;

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

                this._shadowCache[tpl] = {
                    schedule: this.createSoftRasterShadow(sW, sH, 16, sShadows, 2),
                    polaroid: this.createSoftRasterShadow(pW, pH, 8, pShadows, 2)
                };
            }

            const scheduleImg = document.getElementById('exporterScheduleShadowImg');
            const polaroidImg = document.getElementById('exporterPolaroidShadowImg');
            if (scheduleImg && polaroidImg) {
                const cached = this._shadowCache[tpl];
                scheduleImg.src = cached.schedule.dataUrl;
                scheduleImg.style.width = `${cached.schedule.width}px`;
                scheduleImg.style.height = `${cached.schedule.height}px`;
                scheduleImg.style.left = `${-cached.schedule.offset}px`;
                scheduleImg.style.top = `${-cached.schedule.offset}px`;

                polaroidImg.src = cached.polaroid.dataUrl;
                polaroidImg.style.width = `${cached.polaroid.width}px`;
                polaroidImg.style.height = `${cached.polaroid.height}px`;
                polaroidImg.style.left = `${-cached.polaroid.offset}px`;
                polaroidImg.style.top = `${-cached.polaroid.offset}px`;
            }
        },

        drawSpeakerPhoto(img) {
            const canvas = document.getElementById('exporterSpeakerCanvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const cw = canvas.width;
            const ch = canvas.height;

            ctx.clearRect(0, 0, cw, ch);

            if (img && img.naturalWidth) {
                const scale = Math.max(cw / img.naturalWidth, ch / img.naturalHeight) * ((this.state.zoom || 100) / 100);
                const w = img.naturalWidth * scale;
                const h = img.naturalHeight * scale;
                const x = (cw - w) / 2 + (this.state.posX || 0);
                const y = (ch - h) / 2 + (this.state.posY || 0);
                ctx.drawImage(img, x, y, w, h);
            } else {
                const skyGrad = ctx.createLinearGradient(0, 0, 0, ch);
                skyGrad.addColorStop(0, '#b8ddf5');
                skyGrad.addColorStop(0.65, '#e4f1fb');
                ctx.fillStyle = skyGrad;
                ctx.fillRect(0, 0, cw, ch);

                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.arc(cw * 0.52, ch * 0.28, 38, 0, Math.PI * 2);
                ctx.arc(cw * 0.43, ch * 0.32, 28, 0, Math.PI * 2);
                ctx.arc(cw * 0.62, ch * 0.33, 26, 0, Math.PI * 2);
                ctx.fill();
                ctx.fillRect(cw * 0.40, ch * 0.31, 95, 20);

                ctx.fillStyle = '#9cb84e';
                ctx.beginPath();
                ctx.moveTo(0, ch * 0.64);
                ctx.bezierCurveTo(cw * 0.35, ch * 0.55, cw * 0.65, ch * 0.72, cw, ch * 0.60);
                ctx.lineTo(cw, ch);
                ctx.lineTo(0, ch);
                ctx.closePath();
                ctx.fill();

                ctx.fillStyle = '#7a961f';
                ctx.beginPath();
                ctx.moveTo(0, ch * 0.74);
                ctx.bezierCurveTo(cw * 0.4, ch * 0.85, cw * 0.7, ch * 0.68, cw, ch * 0.76);
                ctx.lineTo(cw, ch);
                ctx.lineTo(0, ch);
                ctx.closePath();
                ctx.fill();
            }
        },

        closeModal() {
            this.showIosModal = false;
        },

        openInNewTab() {
            if (this.currentBlobUrl) {
                window.open(this.currentBlobUrl, '_blank');
            }
        },

        async sharePoster() {
            if (this.currentFile && navigator.canShare && navigator.canShare({ files: [this.currentFile] })) {
                try {
                    await navigator.share({
                        files: [this.currentFile],
                        title: this.currentFileName,
                        text: `Poster ${this.state.title}`
                    });
                    return;
                } catch (e) {
                    if (e.name === 'AbortError') return;
                }
            }
            if (this.currentBlobUrl) {
                window.open(this.currentBlobUrl, '_blank');
            }
        },

        async generate(detail) {
            if (this.isGenerating) return;
            this.isGenerating = true;

            window.dispatchEvent(new CustomEvent('toast', {
                detail: { message: 'Menyiapkan pembuatan poster (720×720 px)...' }
            }));

            try {
                const h2c = await this.ensureHtml2Canvas();

                // Merge data dari event
                if (detail.config && typeof detail.config === 'object') {
                    Object.assign(this.state, detail.config);
                }
                const tpl = (detail.template !== undefined && detail.template !== null) ? Number(detail.template) : Number(this.state.template || 1);
                this.state.template = tpl;
                this.state.title = detail.title || 'Kajian';
                this.state.subtitle = detail.subtitle || '';
                this.state.speaker = detail.speaker || 'Asatidz';
                this.state.date = detail.date || '';
                this.state.time = detail.time || '';
                this.state.location = detail.location || 'Masjid Salahuddin';

                if (tpl === 1) {
                    if (detail.title1 || detail.title2) {
                        this.titleP1 = detail.title1 || 'Kajian';
                        const cleanT2 = (detail.title2 || 'Tematik').replace(/^kajian\s+/i, '');
                        this.titleP2 = ' ' + cleanT2;
                    } else {
                        const words = (this.state.title || '').trim().split(/\s+/);
                        if (words.length > 1) {
                            this.titleP1 = words[0];
                            this.titleP2 = ' ' + words.slice(1).join(' ');
                        } else {
                            this.titleP1 = words[0] || 'Kajian';
                            this.titleP2 = '';
                        }
                    }
                } else {
                    this.titleP1 = '';
                    const rawT2 = detail.title || detail.title2 || this.state.title || 'KAJIAN PEKANAN';
                    this.titleP2 = rawT2.toUpperCase();
                }

                // Load foto pemateri jika ada (prioritas cache, timeout 400ms)
                let loadedImg = null;
                if (detail.photo && typeof detail.photo === 'string' && detail.photo.trim() !== '') {
                    try {
                        loadedImg = await new Promise((resolve) => {
                            const timer = setTimeout(() => resolve(null), 400);
                            const img = new Image();
                            img.crossOrigin = 'anonymous';
                            img.onload = () => {
                                clearTimeout(timer);
                                resolve(img);
                            };
                            img.onerror = () => {
                                clearTimeout(timer);
                                resolve(null);
                            };
                            img.src = detail.photo;
                            if (img.complete && img.naturalWidth) {
                                clearTimeout(timer);
                                resolve(img);
                            }
                        });
                    } catch (e) {
                        loadedImg = null;
                    }
                }

                this.drawSpeakerPhoto(loadedImg);

                // Sinkronisasi DOM & shadows
                await this.$nextTick();
                this.updateRasterShadows();
                await new Promise(r => requestAnimationFrame(r));

                const posterElement = document.getElementById('posterExportCard');
                posterElement.style.border = 'none';
                posterElement.style.outline = 'none';
                posterElement.style.boxShadow = 'none';

                // Pastikan gambar logo ter-render sebelum dicapture
                const logoImg = posterElement.querySelector('img[alt="Logo"], img[alt="Logo Resmi"]');
                if (logoImg && !logoImg.complete) {
                    await new Promise((resolve) => {
                        logoImg.onload = resolve;
                        logoImg.onerror = resolve;
                        setTimeout(resolve, 300);
                    });
                }

                // Render dengan scale 1.44 (500px * 1.44 = 720px tepat)
                const rawCanvas = await h2c(posterElement, {
                    scale: 1.44,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: tpl === 1 ? '#fbfcf9' : '#0264d6',
                    logging: false,
                    width: 500,
                    height: 500,
                    x: 0,
                    y: 0,
                    scrollX: 0,
                    scrollY: 0,
                    imageTimeout: 300,
                    ignoreElements: (node) => {
                        if (node.hasAttribute && node.hasAttribute('data-html2canvas-ignore')) {
                            return true;
                        }
                        if (node.tagName === 'LINK' && node.href && node.href.includes('fonts.googleapis.com')) {
                            return true;
                        }
                        return false;
                    },
                    onclone: (clonedDoc) => {
                        try {
                            if (document.fonts) {
                                for (const font of document.fonts) {
                                    if (font.status === 'loaded') {
                                        clonedDoc.fonts.add(font);
                                    }
                                }
                            }
                        } catch (e) {}
                    }
                });

                // Normalisasi akhir ke kanvas 720×720 px
                const finalCanvas = document.createElement('canvas');
                finalCanvas.width = 720;
                finalCanvas.height = 720;
                const fCtx = finalCanvas.getContext('2d');
                fCtx.imageSmoothingEnabled = true;
                fCtx.imageSmoothingQuality = 'high';
                fCtx.drawImage(rawCanvas, 0, 0, 720, 720);

                const safeTitle = ((this.state.subtitle || this.state.title || 'Kajian')).replace(/[^a-zA-Z0-9_-]/g, '-').replace(/-+/g, '-').slice(0, 45);
                const safeDate = (this.state.date || '').replace(/[^a-zA-Z0-9_-]/g, '-').replace(/-+/g, '-');
                const fileName = `Poster-${safeTitle}-${safeDate}.png`;

                // Buat binary Blob
                const blob = await new Promise(resolve => finalCanvas.toBlob(resolve, 'image/png'));
                const file = new File([blob], fileName, { type: 'image/png' });
                const blobUrl = URL.createObjectURL(blob);

                this.currentBlobUrl = blobUrl;
                this.currentFile = file;
                this.currentFileName = fileName;

                // Deteksi iOS Safari / iPad / iPhone
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || 
                              (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

                if (isIOS) {
                    let shared = false;
                    if (navigator.canShare && navigator.canShare({ files: [file] })) {
                        try {
                            await navigator.share({
                                files: [file],
                                title: fileName,
                                text: `Poster ${this.state.title}`
                            });
                            shared = true;
                            window.dispatchEvent(new CustomEvent('toast', {
                                detail: { message: `Poster ${fileName} siap disimpan!` }
                            }));
                        } catch (shareErr) {
                            if (shareErr.name === 'AbortError') {
                                return;
                            }
                        }
                    }

                    // Tampilkan modal simpan untuk iPhone dengan petunjuk Simpan ke Foto
                    if (!shared) {
                        this.showIosModal = true;
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: 'Poster siap disimpan! Pilih "Simpan ke Galeri" atau tekan lama gambar.' }
                        }));
                    }
                } else {
                    // Desktop / Android: Unduh langsung via anchor Blob URL
                    const link = document.createElement('a');
                    link.download = fileName;
                    link.href = blobUrl;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: `Poster ${fileName} berhasil diunduh (720×720 px)!` }
                    }));
                }
            } catch (err) {
                console.error('Poster export error:', err);
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { message: 'Gagal membuat poster: ' + err.message }
                }));
            } finally {
                this.isGenerating = false;
            }
        }
    };
}
</script>
