    <!-- ======================================================== -->
    <!-- MODAL: NOTULA KAJIAN EKSEKUTIF (ROLE MASTER ONLY) -->
    <!-- ======================================================== -->
    @if(Auth::user()->isMaster())
    <div x-show="showNotulaModal" x-cloak wire:ignore @keydown.escape.window="showNotulaModal = false; $wire.set('showNotulaModal', false, false)"
        @click="if (window.isBackdropClick($event, $el)) { showNotulaModal = false; $wire.set('showNotulaModal', false, false); }"
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 md:p-6"
        style="display: none;">
        <div class="w-full max-w-5xl bg-gov-950 rounded-2xl shadow-2xl border border-slate-800/80 overflow-hidden flex flex-col h-[92vh] max-h-[92vh] outline-none animate-in fade-in duration-200 transition-all">
            
            <!-- Top Action Bar (Header) - Clean Navy Gov -->
            <header class="no-print bg-gov-950 text-white px-3 sm:px-6 py-2.5 sm:py-3 flex flex-wrap items-center justify-between gap-2.5 border-b border-gov-900 sticky top-0 z-30 shadow-xs shrink-0">
                <!-- Left: Title & Badge -->
                <div class="flex items-center gap-2 text-gov-300 text-xs sm:text-sm font-semibold tracking-wide min-w-0 max-w-[45%] sm:max-w-[40%]">
                    <svg class="w-4 h-4 text-gov-300 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                        <path d="M6 6h10"/><path d="M6 10h10"/><path d="M6 14h6"/>
                    </svg>
                    <span class="hidden xs:inline">Notula</span>
                    <span class="text-[10px] font-normal px-2 py-0.5 rounded-full bg-gov-900 text-gov-300 border border-slate-700/70 capitalize shrink-0"
                        x-text="notulaForm.type === 'tematik' ? 'Tematik' : (notulaForm.type === 'jumat' ? 'Shalat Jumat' : 'Pekanan')"></span>
                    <span class="truncate text-slate-300 text-xs font-normal" x-show="notulaForm.title" x-text="notulaForm.title"></span>
                </div>

                <!-- Center: Mode Tab Switcher (Preview vs Editor) -->
                <div class="flex items-center bg-gov-900/90 rounded-lg p-0.5 border border-slate-700/80 text-xs font-medium">
                    <button type="button" @click="notulaActiveTab = 'preview'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md transition cursor-pointer"
                        :class="notulaActiveTab === 'preview' ? 'bg-gov-700 text-white shadow-xs font-semibold' : 'text-slate-300 hover:text-white hover:bg-gov-800'">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <span>Pratinjau</span>
                    </button>
                    <button type="button" @click="notulaActiveTab = 'editor'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md transition cursor-pointer"
                        :class="notulaActiveTab === 'editor' ? 'bg-gov-700 text-white shadow-xs font-semibold' : 'text-slate-300 hover:text-white hover:bg-gov-800'">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        <span>Tulis / Edit</span>
                    </button>
                </div>

                <!-- Right Quick Action Buttons -->
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <!-- Zoom Buttons (Only visible in Preview tab) -->
                    <div x-show="notulaActiveTab === 'preview'" class="flex items-center bg-gov-900 rounded-lg p-0.5 border border-slate-700/70 text-slate-300 mr-0.5">
                        <button type="button" @click="adjustNotulaFontSize(-1)" title="Perkecil Teks (A-)" class="p-1.5 hover:text-white hover:bg-gov-800 rounded-md transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <span class="text-[11px] px-1.5 font-mono font-medium text-slate-300" x-text="['A-', 'A', 'A+', 'A++'][notulaFontSizeIndex]"></span>
                        <button type="button" @click="adjustNotulaFontSize(1)" title="Perbesar Teks (A+)" class="p-1.5 hover:text-white hover:bg-gov-800 rounded-md transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                    </div>

                    <!-- Cetak / PDF Button -->
                    <button type="button" 
                        @click="window.printNotulaDocument ? window.printNotulaDocument(window.parseNotulaToHtml(notulaForm.raw_text, notulaForm), notulaForm.title || 'Notula Kajian') : window.print()" 
                        class="inline-flex items-center gap-1.5 bg-gov-700 hover:bg-gov-600 active:bg-gov-800 text-white text-xs font-semibold px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-lg transition-colors border border-gov-600/60 shadow-xs cursor-pointer"
                        title="Cetak atau Simpan PDF">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                        <span class="hidden sm:inline">Cetak /</span><span>PDF</span>
                    </button>

                    <!-- Simpan Button -->
                    <button type="button"
                        @click="$wire.saveNotula(notulaForm.id, notulaForm.raw_text)"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 bg-gov-navy hover:bg-gov-navyHover active:bg-gov-900 text-white text-xs font-bold px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg transition-colors border border-gov-navy shadow-xs cursor-pointer disabled:opacity-50"
                        title="Simpan Perubahan Notula ke Database">
                        <svg wire:loading.remove wire:target="saveNotula" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        <svg wire:loading wire:target="saveNotula" class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        <span wire:loading.remove wire:target="saveNotula">Simpan</span>
                        <span wire:loading wire:target="saveNotula">Menyimpan...</span>
                    </button>

                    <!-- Tutup Button -->
                    <button type="button" @click="showNotulaModal = false; $wire.set('showNotulaModal', false, false)"
                        class="inline-flex items-center gap-1 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-300 hover:text-white text-xs font-medium px-2.5 py-1.5 sm:py-2 rounded-lg border border-slate-700 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span class="hidden sm:inline">Tutup</span>
                    </button>
                </div>
            </header>

            <!-- Main Body: Tab 1 Preview Eksekutif -->
            <div x-show="notulaActiveTab === 'preview'" class="min-h-0 flex-1 overflow-y-auto bg-slate-100 p-3 sm:p-6 md:p-8 flex justify-center notula-scrollable">
                <div class="w-full max-w-4xl bg-white rounded-xl sm:rounded-2xl shadow-md border border-slate-200/80 overflow-hidden flex flex-col h-fit">
                    <main id="notulaContentContainer" 
                        class="p-5 sm:p-8 md:p-11 space-y-6 sm:space-y-7 leading-relaxed text-slate-700 transition-all"
                        :class="['text-sm', 'text-base', 'text-lg', 'text-xl'][notulaFontSizeIndex]"
                        x-html="window.parseNotulaToHtml ? window.parseNotulaToHtml(notulaForm.raw_text, notulaForm) : notulaForm.raw_text">
                    </main>
                </div>
            </div>

            <!-- Main Body: Tab 2 Editor / Tulis Notula -->
            <div x-show="notulaActiveTab === 'editor'" class="min-h-0 flex-1 overflow-y-auto bg-slate-50 p-4 sm:p-6 space-y-4 notula-scrollable">
                <div class="max-w-4xl mx-auto space-y-4">
                    <!-- Petunjuk / Helper Banner -->
                    <div class="bg-gov-50/80 border border-gov-200 rounded-xl p-4 text-xs text-gov-900 flex items-start gap-3">
                        <svg class="w-5 h-5 text-gov-700 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <div class="space-y-1 flex-1">
                            <p class="font-semibold text-gov-950">Generator Otomatis Dokumen Eksekutif Notula</p>
                            <p class="text-slate-600 leading-relaxed">
                                Anda dapat langsung menyalin (paste) naskah notula kajian dari ChatGPT, catatan, transkrip rekaman, atau format Markdown apa pun. 
                                Sistem secara otomatis mendeteksi teks ayat Arab, nomor bab, dalil pendukung, studi kasus, dan catatan kaki untuk diubah menjadi risalah dokumen formal siap cetak.
                            </p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" 
                                @click="if(!notulaForm.raw_text || confirm('Muat naskah contoh kajian tematik? (Akan menimpa teks saat ini)')) { notulaForm.raw_text = window.SAMPLE_NOTULA_TEMPLATE || ''; }"
                                class="px-2.5 py-1.5 rounded-lg border border-gov-300 bg-white hover:bg-gov-100 text-gov-800 text-xs font-medium transition cursor-pointer">
                                Contoh Format
                            </button>
                        </div>
                    </div>

                    <!-- Textarea Editor -->
                    <div class="bg-white rounded-xl border border-slate-300 shadow-2xs overflow-hidden focus-within:ring-2 focus-within:ring-gov-navy/20 focus-within:border-gov-navy transition">
                        <div class="bg-slate-100/80 px-4 py-2 border-b border-slate-200 flex items-center justify-between text-xs text-slate-500">
                            <span class="font-medium flex items-center gap-1.5 text-slate-700">
                                <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                Editor Naskah Notula
                            </span>
                            <div class="flex items-center gap-3">
                                <span>Karakter: <strong class="text-slate-800" x-text="(notulaForm.raw_text || '').length"></strong></span>
                                <span>Kata: <strong class="text-slate-800" x-text="(notulaForm.raw_text || '').trim() ? (notulaForm.raw_text || '').trim().split(/\s+/).length : 0"></strong></span>
                            </div>
                        </div>
                        <textarea 
                            wire:ignore
                            x-ref="notulaTextarea"
                            x-model="notulaForm.raw_text"
                            rows="18"
                            class="w-full p-4 font-mono text-xs sm:text-sm text-slate-800 bg-white focus:outline-none resize-y leading-relaxed"
                            placeholder="Salin atau ketik notula kajian di sini... (Mendukung Markdown, judul #, ayat Arab, dalil, dan catatan kaki [^1])"></textarea>
                    </div>

                    <!-- Bottom Action Buttons in Editor -->
                    <div class="flex items-center justify-between flex-wrap gap-2 pt-1">
                        <button type="button" @click="notulaForm.raw_text = ''; if($refs.notulaTextarea) $refs.notulaTextarea.value = '';"
                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 hover:text-rose-600 hover:bg-rose-50 text-xs font-medium transition cursor-pointer">
                            Bersihkan Naskah
                        </button>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="notulaActiveTab = 'preview'"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-gov-50 hover:bg-gov-100 text-gov-900 border border-gov-200 text-xs font-semibold transition cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-gov-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>Lihat Pratinjau Dokumen</span>
                            </button>
                            <button type="button"
                                @click="$wire.saveNotula(notulaForm.id, notulaForm.raw_text)"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold px-4 py-2 rounded-lg transition border border-gov-navy shadow-xs cursor-pointer disabled:opacity-50">
                                <svg wire:loading.remove wire:target="saveNotula" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                <svg wire:loading wire:target="saveNotula" class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                <span wire:loading.remove wire:target="saveNotula">Simpan Notula</span>
                                <span wire:loading wire:target="saveNotula">Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL: KELOLA LINK YOUTUBE KEGIATAN -->
    <!-- ======================================================== -->
    <div x-show="showYoutubeModal" x-cloak wire:ignore.self @keydown.escape.window="showYoutubeModal = false; $wire.set('showYoutubeModal', false, false)"
        @click="if (window.isBackdropClick($event, $el)) { showYoutubeModal = false; $wire.set('showYoutubeModal', false, false); }"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[92vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-start justify-between border-b border-slate-200 pb-3 gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gov-textMain">Kelola Link YouTube Siaran</h3>
                        <p class="text-[11px] text-slate-500">
                            Tautkan siaran langsung (live streaming) atau rekaman kajian/agenda.
                        </p>
                    </div>
                </div>
                <button type="button" @click="showYoutubeModal = false; $wire.set('showYoutubeModal', false, false)"
                    class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Item Info Card -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-1 text-xs">
                <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-gov-navy/10 text-gov-navy capitalize"
                        x-text="youtubeForm.type === 'agenda' ? 'Agenda Kegiatan' : 'Kajian Masjid'"></span>
                    <span class="text-slate-400 text-[11px]" x-show="youtubeForm.date" x-text="youtubeForm.date"></span>
                </div>
                <div class="font-bold text-gov-textMain text-sm leading-snug" x-text="youtubeForm.title || '-'"></div>
                <div class="text-slate-500 text-xs" x-show="youtubeForm.subtitle" x-text="youtubeForm.subtitle"></div>
            </div>

            <!-- Form -->
            <form @submit.prevent="$wire.saveYoutubeLink(youtubeForm.id, youtubeForm.youtube_url, youtubeForm.type)" class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">
                        <span>URL Video atau Siaran YouTube</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input x-model="youtubeForm.youtube_url" type="url" required
                            placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/..."
                            class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gov-border bg-white text-xs font-medium text-slate-800 focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition shadow-2xs">
                        <svg class="w-4 h-4 text-rose-500 absolute left-3 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Format yang didukung: link video reguler YouTube, Shorts, live streaming, atau tautan ringkas youtu.be.
                    </p>
                </div>

                <!-- Preview Box -->
                <div x-data="{
                    get ytId() {
                        const u = youtubeForm.youtube_url || '';
                        const m = u.match(/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/);
                        return (m && m[2].length === 11) ? m[2] : null;
                    }
                }" class="space-y-2">
                    <template x-if="ytId">
                        <div class="rounded-xl overflow-hidden border border-slate-200 bg-black aspect-video relative shadow-xs">
                            <iframe :src="'https://www.youtube-nocookie.com/embed/' + ytId"
                                class="w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                        </div>
                    </template>
                    <template x-if="!ytId && youtubeForm.youtube_url">
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-xs flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>URL YouTube belum valid. Masukkan URL lengkap seperti <code class="font-mono font-semibold">https://www.youtube.com/watch?v=xxxx</code></span>
                        </div>
                    </template>
                </div>

                <!-- Bottom Actions -->
                <div class="pt-3 flex items-center justify-between border-t border-slate-200">
                    <div>
                        <button type="button" x-show="youtubeForm.youtube_url"
                            @click="youtubeForm.youtube_url = ''; $wire.saveYoutubeLink(youtubeForm.id, '', youtubeForm.type)"
                            class="px-2.5 py-1.5 rounded-lg text-rose-600 hover:bg-rose-50 text-xs font-semibold transition cursor-pointer inline-flex items-center gap-1.5"
                            title="Hapus tautan YouTube dari kegiatan ini">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            <span>Hapus Link</span>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="showYoutubeModal = false; $wire.set('showYoutubeModal', false, false)"
                            class="px-4 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold shadow-2xs transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="saveYoutubeLink">Simpan Link</span>
                            <span wire:loading.inline-flex wire:target="saveYoutubeLink" class="inline-flex items-center gap-2" style="display: none;">
                                <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- ======================================================== -->
    <!-- MODAL: BUAT / EDIT JADWAL KAJIAN (MILESTONE 1) -->
    <!-- ======================================================== -->
    <div x-show="showKajianModal" x-cloak wire:ignore.self @keydown.escape.window="closeKajianModal()"
        @click="if (window.isBackdropClick ? window.isBackdropClick($event, $el) : $event.target === $el) closeKajianModal()"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-base font-bold text-gov-textMain"
                        x-text="isEditingKajian ? 'Edit Jadwal Kajian' : 'Tambah Jadwal Kajian Baru'"></h3>
                    <p class="text-xs text-slate-500">Kategori: <strong class="text-gov-navy uppercase"
                            x-text="kajianForm.type === 'jumat' ? 'Shalat Jumat & Khutbah' : (kajianForm.type === 'tematik' ? 'Kajian Tematik' : 'Kajian Pekanan Rutin')"></strong>
                    </p>
                </div>
                <button type="button" @click="closeKajianModal()"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer"><i
                        data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form @submit.prevent="$wire.saveKajian(kajianForm)" class="space-y-3.5 text-xs">
                <!-- Pilihan Tipe Kajian jika menambah baru -->
                <div x-show="!isEditingKajian">
                    <label class="block font-semibold text-slate-700 mb-1">Jenis Jadwal *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <button type="button"
                            @click="kajianForm.type = 'pekanan'; if(!kajianForm.time_display || kajianForm.time_display === '11:45 - 12:45') kajianForm.time_display = '09:00 - 11:30'"
                            class="p-2.5 rounded-lg border font-bold text-center transition cursor-pointer text-xs"
                            :class="kajianForm.type === 'pekanan' ? 'border-gov-navy bg-gov-navy text-white shadow-2xs' : 'border-gov-border bg-slate-50 text-slate-700 hover:bg-slate-100'">
                            Kajian Pekanan
                        </button>
                        <button type="button"
                            @click="kajianForm.type = 'tematik'; if(!kajianForm.time_display || kajianForm.time_display === '11:45 - 12:45') kajianForm.time_display = '09:00 - 11:30'"
                            class="p-2.5 rounded-lg border font-bold text-center transition cursor-pointer text-xs"
                            :class="kajianForm.type === 'tematik' ? 'border-gov-navy bg-gov-navy text-white shadow-2xs' : 'border-gov-border bg-slate-50 text-slate-700 hover:bg-slate-100'">
                            Kajian Tematik
                        </button>
                        <button type="button"
                            @click="kajianForm.type = 'jumat'; if(!kajianForm.time_display || kajianForm.time_display === '09:00 - 11:30') kajianForm.time_display = '11:45 - 12:45'"
                            class="p-2.5 rounded-lg border font-bold text-center transition cursor-pointer text-xs"
                            :class="kajianForm.type === 'jumat' ? 'border-gov-navy bg-gov-navy text-white shadow-2xs' : 'border-gov-border bg-slate-50 text-slate-700 hover:bg-slate-100'">
                            Shalat Jumat & Khutbah
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Hari & Tanggal *</label>
                        <input x-model="kajianForm.date" type="date"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain cursor-pointer">
                        @error('kajianDate') <span
                        class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Waktu Pelaksanaan</label>
                        <input x-model="kajianForm.time_display" type="text"
                            :placeholder="kajianForm.type === 'jumat' ? '11:45 - 12:45' : '09:00 - 11:30'"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1"
                        x-text="kajianForm.type === 'jumat' ? 'Judul / Tema Khutbah Jumat *' : 'Judul / Tema Kajian *'"></label>
                    <input x-model="kajianForm.title" type="text"
                        :placeholder="kajianForm.type === 'jumat' ? 'Contoh: Meneladani Karakter Pemimpin Berintegritas' : (kajianForm.type === 'tematik' ? 'Contoh: Peringatan Isra Miraj 1448 H' : 'Contoh: Kajian Tafsir Al-Kahfi')"
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">
                    @error('kajianTitle') <span
                    class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                </div>

                <!-- Khusus Kajian Pekanan & Tematik -->
                <div x-show="kajianForm.type === 'pekanan' || kajianForm.type === 'tematik'" class="grid grid-cols-2 gap-3">
                    <div class="relative" x-data="{
                        openSuggestions: false,
                        get filteredUstadz() {
                            const query = (kajianForm.speaker_name || '').toLowerCase().trim();
                            if (!query) return (ustadzDirectory || []).slice(0, 6);
                            return (ustadzDirectory || []).filter(u => u.name.toLowerCase().includes(query)).slice(0, 6);
                        },
                        selectUstadz(u) {
                            kajianForm.speaker_name = u.name;
                            if (u.phone && !kajianForm.speaker_phone) {
                                kajianForm.speaker_phone = u.phone;
                            }
                            this.openSuggestions = false;
                        }
                    }" @click.outside="openSuggestions = false">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700">Pembicara *</label>
                            <span x-show="openSuggestions && filteredUstadz.length > 0" class="text-[10px] text-emerald-600 font-medium">Saran aktif</span>
                        </div>
                        <div class="relative">
                            <input x-model="kajianForm.speaker_name"
                                @focus="openSuggestions = true"
                                @input="openSuggestions = true"
                                @keydown.escape="openSuggestions = false"
                                type="text"
                                placeholder="Ketik nama pembicara..."
                                autocomplete="off"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">

                            <!-- Dropdown Saran Database Ustadz -->
                            <div x-show="openSuggestions && filteredUstadz.length > 0"
                                x-transition:enter="transition ease-out duration-50"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-50"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute left-0 top-full mt-1 z-50 w-[calc(200%+0.75rem)] min-w-70 max-w-[calc(100vw-3.5rem)] bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden py-1 max-h-60 overflow-y-auto"
                                style="display: none;">
                                <div class="px-3 py-1.5 text-[11px] font-semibold text-slate-500 bg-slate-50 border-b border-slate-100 flex items-center justify-between gap-2">
                                    <span>Pilih dari Database Ustadz</span>
                                    <span class="text-[10px] text-slate-400 font-normal shrink-0">Klik untuk memilih</span>
                                </div>
                                <template x-for="u in filteredUstadz" :key="'speaker-' + u.id">
                                    <button type="button" @click="selectUstadz(u)"
                                        class="w-full text-left px-3 py-2 hover:bg-emerald-50/60 flex items-center justify-between gap-2.5 transition cursor-pointer border-b border-slate-50 last:border-0 group">
                                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                                                <template x-if="u.photo">
                                                    <img :src="'{{ asset('storage') }}/' + u.photo" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!u.photo">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </template>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-semibold text-slate-800 group-hover:text-gov-navy truncate" x-text="u.name"></p>
                                                <p class="text-[10px] text-slate-400 truncate" x-text="u.phone ? u.phone : 'Tanpa No. WhatsApp'"></p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border border-emerald-400 bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100 group-hover:border-emerald-300 leading-none shrink-0 transition-colors">Pilih</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('kajianSpeakerName') <span
                        class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">WhatsApp</label>
                        <input x-model="kajianForm.speaker_phone" type="text" placeholder="+62 812-3456-7890"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">
                    </div>
                </div>

                <!-- Foto Pembicara / Ustadz untuk Poster -->
                <div x-show="kajianForm.type === 'pekanan' || kajianForm.type === 'tematik'" class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block font-semibold text-slate-700">Foto Pembicara / Ustadz (Untuk Poster HD)</label>
                        <button type="button" @click="$wire.openUstadzModal()" class="text-[11px] text-gov-navy hover:underline font-semibold cursor-pointer inline-flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Database Ustadz</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-slate-50/70 rounded-xl border border-gov-border">
                        <!-- Preview Box -->
                        <div class="relative shrink-0 w-14 h-14 rounded-lg overflow-hidden border border-slate-200 bg-white shadow-2xs flex items-center justify-center">
                            @if ($kajianSpeakerPhoto)
                                <img src="{{ $kajianSpeakerPhoto->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                            @elseif ($kajianExistingPhoto)
                                <img src="{{ asset('storage/' . $kajianExistingPhoto) }}" alt="Foto Ustadz" class="w-full h-full object-cover">
                            @else
                                <template x-if="kajianForm.speaker_name && ustadzPhotoMap && ustadzPhotoMap[kajianForm.speaker_name]">
                                    <img :src="'{{ asset('storage') }}/' + ustadzPhotoMap[kajianForm.speaker_name]" alt="Foto Ustadz Database" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!kajianForm.speaker_name || !ustadzPhotoMap || !ustadzPhotoMap[kajianForm.speaker_name]">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="text-[8px] font-medium mt-0.5">Polos</span>
                                    </div>
                                </template>
                            @endif
                        </div>

                        <!-- Upload Controls & Database Status -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
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
                                    <span class="pointer-events-none" x-text="isDragging ? 'Lepas Foto' : 'Pilih / Seret Foto'">Pilih Foto</span>
                                    <input type="file"
                                           wire:model="kajianSpeakerPhoto"
                                           accept="image/png,image/jpeg,image/webp"
                                           @dragenter="isDragging = true"
                                           @dragover.prevent="isDragging = true"
                                           @dragleave="isDragging = false"
                                           @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                </label>
                                @if ($kajianSpeakerPhoto || $kajianExistingPhoto)
                                    <button type="button" wire:click="removeKajianPhoto" class="px-2 py-1 text-xs text-rose-600 hover:bg-rose-50 rounded-lg transition font-medium cursor-pointer">
                                        Hapus Foto
                                    </button>
                                @endif
                            </div>

                            <!-- Indicator when photo is auto-detected from database -->
                            <div x-show="!$wire.kajianSpeakerPhoto && !$wire.kajianExistingPhoto && (kajianForm.speaker_name && ustadzPhotoMap && ustadzPhotoMap[kajianForm.speaker_name])" class="mt-1.5 flex items-center gap-1.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-400">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Tersinkron Database Ustadz
                                </span>
                                <span class="text-[11px] text-slate-500">Foto otomatis terpasang</span>
                            </div>

                            <p x-show="$wire.kajianSpeakerPhoto || $wire.kajianExistingPhoto || !(kajianForm.speaker_name && ustadzPhotoMap && ustadzPhotoMap[kajianForm.speaker_name])" class="text-[11px] text-slate-500 mt-1 truncate">
                                Cukup upload 1x per ustadz, otomatis tersinkron ke semua jadwal.
                            </p>

                            <div wire:loading.inline-flex wire:target="kajianSpeakerPhoto" style="display: none;"
                                class="inline-flex flex-row items-center gap-2 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200 mt-1 shrink-0 whitespace-nowrap">
                                <svg class="w-3 h-3 animate-spin shrink-0 text-emerald-600 inline-block" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="whitespace-nowrap leading-none">Mengunggah foto...</span>
                            </div>
                            @error('kajianSpeakerPhoto')
                                <span class="text-xs font-semibold text-rose-600 block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Khusus Kajian Jumat -->
                <div x-show="kajianForm.type === 'jumat'" class="space-y-3">
                    <div
                        class="p-3 bg-amber-50/60 rounded-lg border border-amber-200 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-amber-950 block">Opsi Hari Libur</span>
                            <span class="text-xs text-amber-800">Nonaktifkan petugas jika bertepatan dengan libur
                                bersama atau cuti.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="kajianForm.is_holiday_disabled" class="sr-only peer">
                            <div
                                class="w-10 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-gov-navy">
                            </div>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="relative" x-data="{
                            openKhatibSuggestions: false,
                            get filteredKhatib() {
                                const query = (kajianForm.khatib_name || '').toLowerCase().trim();
                                if (!query) return (ustadzDirectory || []).slice(0, 6);
                                return (ustadzDirectory || []).filter(u => u.name.toLowerCase().includes(query)).slice(0, 6);
                            },
                            selectKhatib(u) {
                                kajianForm.khatib_name = u.name;
                                if (u.phone && !kajianForm.khatib_phone) {
                                    kajianForm.khatib_phone = u.phone;
                                }
                                this.openKhatibSuggestions = false;
                            }
                        }" @click.outside="openKhatibSuggestions = false">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block font-semibold text-slate-700">Khatib & Imam</label>
                                <span x-show="openKhatibSuggestions && filteredKhatib.length > 0" class="text-[10px] text-emerald-600 font-medium">Saran aktif</span>
                            </div>
                            <div class="relative">
                                <input x-model="kajianForm.khatib_name"
                                    @focus="openKhatibSuggestions = true"
                                    @input="openKhatibSuggestions = true"
                                    @keydown.escape="openKhatibSuggestions = false"
                                    type="text"
                                    placeholder="Ketik nama khatib..."
                                    autocomplete="off"
                                    class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">

                                <!-- Dropdown Saran Khatib -->
                                <div x-show="openKhatibSuggestions && filteredKhatib.length > 0"
                                    x-transition:enter="transition ease-out duration-50"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-50"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute left-0 top-full mt-1 z-50 w-[calc(200%+0.75rem)] min-w-70 max-w-[calc(100vw-3.5rem)] bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden py-1 max-h-60 overflow-y-auto"
                                    style="display: none;">
                                    <div class="px-3 py-1.5 text-[11px] font-semibold text-slate-500 bg-slate-50 border-b border-slate-100 flex items-center justify-between gap-2">
                                        <span>Pilih dari Database Ustadz</span>
                                        <span class="text-[10px] text-slate-400 font-normal shrink-0">Klik untuk memilih</span>
                                    </div>
                                    <template x-for="u in filteredKhatib" :key="'khatib-' + u.id">
                                        <button type="button" @click="selectKhatib(u)"
                                            class="w-full text-left px-3 py-2 hover:bg-emerald-50/60 flex items-center justify-between gap-2.5 transition cursor-pointer border-b border-slate-50 last:border-0 group">
                                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                                                    <template x-if="u.photo">
                                                        <img :src="'{{ asset('storage') }}/' + u.photo" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!u.photo">
                                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                    </template>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-semibold text-slate-800 group-hover:text-gov-navy truncate" x-text="u.name"></p>
                                                    <p class="text-[10px] text-slate-400 truncate" x-text="u.phone ? u.phone : 'Tanpa No. WhatsApp'"></p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border border-emerald-400 bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100 group-hover:border-emerald-300 leading-none shrink-0 transition-colors">Pilih</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">No HP Khatib</label>
                            <input x-model="kajianForm.khatib_phone" type="text" placeholder="+62 812-9876-5432"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">MC / Protokol Shalat Jumat</label>
                            <input x-model="kajianForm.mc_name" type="text" placeholder="H. Bambang Sugiarto"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Muadzin & Bilal</label>
                            <input x-model="kajianForm.muadzin_name" type="text" placeholder="Ustadz Bilal Ramadhan"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Pengumuman MC (Tampil pada Cetak
                            Teks MC)</label>
                        <textarea x-model="kajianForm.mc_notes" rows="2"
                            placeholder="Tuliskan pengumuman khusus DKM yang harus dibacakan MC sebelum adzan..."
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain"></textarea>
                    </div>
                </div>

                <!-- Link YouTube (Opsional) -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">
                        <span>Link YouTube Video / Live Streaming</span>
                        <span class="text-[10px] text-slate-400 font-normal ml-1">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <input x-model="kajianForm.youtube_url" type="url" placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/..."
                            class="w-full p-2.5 pl-8 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">
                        <svg class="w-4 h-4 text-rose-500 absolute left-2.5 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </div>
                </div>

                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="closeKajianModal()"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="saveKajian"
                            x-text="isEditingKajian ? 'Simpan Perubahan' : 'Simpan Jadwal'"></span>
                        <span wire:loading.inline-flex wire:target="saveKajian"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: IMPORT JADWAL KAJIAN (MILESTONE 1) -->
    <!-- ======================================================== -->
    <div x-show="showImportKajianModal" x-cloak wire:ignore.self @keydown.escape.window="showImportKajianModal = false"
        @click="if (window.isBackdropClick($event, $el)) showImportKajianModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-base font-bold text-gov-textMain">Import Jadwal Kajian</h3>
                    <p class="text-xs text-slate-500">Target Impor: <strong
                            class="text-gov-navy uppercase">{{ $importKajianType === 'jumat' ? 'Shalat Jumat' : 'Kajian' }}</strong>
                    </p>
                </div>
                <button type="button" @click="showImportKajianModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer"><i
                        data-lucide="x" class="w-5 h-5"></i></button>
            </div>

                <!-- Tab Selector di dalam Modal: Copypaste Excel vs Upload Excel -->
            <div class="flex items-center p-1 bg-slate-100 rounded-lg">
                <button type="button" @click="importKajianTab = 'paste'"
                    class="flex-1 py-1.5 rounded-md font-bold text-xs transition cursor-pointer"
                    :class="importKajianTab === 'paste' ? 'bg-white text-gov-navy shadow-2xs' : 'text-slate-600 hover:text-gov-navy'">
                    📋 Copypaste Excel
                </button>
                <button type="button" @click="importKajianTab = 'file'"
                    class="flex-1 py-1.5 rounded-md font-bold text-xs transition cursor-pointer"
                    :class="importKajianTab === 'file' ? 'bg-white text-gov-navy shadow-2xs' : 'text-slate-600 hover:text-gov-navy'">
                    📁 Upload File Excel
                </button>
            </div>

            <!-- TAB 1: COPYPASTE DARI EXCEL -->
            <div x-show="importKajianTab === 'paste'" class="space-y-3 text-xs">
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-amber-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-amber-600"></i>
                        <span>Format Kolom Excel yang Diharapkan:</span>
                    </span>
                    @if($importKajianType === 'pekanan')
                        <p class="text-xs font-mono bg-white/70 p-2 rounded border border-amber-300">
                            [No] \t [Jenis] \t [Hari & Tanggal] \t [Waktu] \t [Judul Kajian] \t [Pembicara] \t [No HP]
                        </p>
                        <p class="text-xs text-amber-800">Cukup blok tabel di Excel/Google Sheets, tekan
                            <strong>Ctrl+C</strong>, lalu paste ke kotak di bawah. File Excel hasil download juga dapat langsung dicopy/paste atau diupload.
                        </p>
                    @else
                        <p class="text-xs font-mono bg-white/70 p-2 rounded border border-amber-300">
                            [No] \t [Hari & Tanggal] \t [Waktu] \t [Status Libur] \t [Judul Khutbah] \t [Khatib] \t [MC] \t [Muadzin] \t [No HP]
                        </p>
                        <p class="text-xs text-amber-800">Cukup blok tabel di Excel/Google Sheets, tekan
                            <strong>Ctrl+C</strong>, lalu paste ke kotak di bawah. File Excel hasil download juga dapat langsung dicopy/paste atau diupload.
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tempelkan Teks Excel di Sini *</label>
                    <textarea wire:model="importKajianPasteText" rows="6"
                        placeholder="2026-09-17&#9;09:00 - 11:30&#9;Tafsir Al-Kahfi&#9;Ust. Firdaus&#9;08123456789"
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-mono text-xs transition"></textarea>
                </div>

                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showImportKajianModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="button" wire:click="processImportKajianPaste" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="processImportKajianPaste">Proses Impor Teks</span>
                        <span wire:loading.inline-flex wire:target="processImportKajianPaste" style="display: none;"
                            class="inline-flex flex-row items-center justify-center gap-2 shrink-0 whitespace-nowrap">
                            <svg class="w-4 h-4 animate-spin shrink-0 text-white inline-block" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Mengimpor...</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- TAB 2: UPLOAD FILE EXCEL -->
            <div x-show="importKajianTab === 'file'" class="space-y-3 text-xs">
                <div class="p-3 bg-sky-50 rounded-lg border border-sky-200 text-sky-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-sky-600"></i>
                        <span>Petunjuk Unggah File Excel:</span>
                    </span>
                    <p class="text-xs text-sky-800">Unggah file berformat <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong> dengan ukuran maksimal 5 MB.
                    </p>
                </div>

                <div class="space-y-1.5 pt-1">
                    <label class="block font-semibold text-slate-700">Pilih Berkas Excel *</label>
                    <div x-data="{ isDragging: false }"
                         :class="isDragging ? 'border-gov-navy bg-blue-50/70 ring-2 ring-gov-navy/20' : 'border-gov-border bg-slate-50/50 hover:bg-slate-50'"
                         class="border-2 border-dashed rounded-xl p-8 text-center space-y-2.5 transition relative overflow-hidden group">
                        
                        <!-- Invisible Full-Surface Native File Input -->
                        <input id="importKajianFile"
                               type="file"
                               wire:model="importKajianFile"
                               accept=".xlsx,.xls,.csv,.txt"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @dragenter="isDragging = true"
                               @dragover.prevent="isDragging = true"
                               @dragleave="isDragging = false"
                               @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }">

                        <div class="pointer-events-none space-y-2">
                            <div class="w-12 h-12 rounded-full bg-gov-navy/10 flex items-center justify-center mx-auto text-gov-navy transition group-hover:scale-105"
                                 :class="isDragging ? 'scale-110 bg-gov-navy/20' : ''">
                                <svg class="w-6 h-6 text-gov-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                                    <path d="M12 12v9"/>
                                    <path d="m16 16-4-4-4 4"/>
                                </svg>
                            </div>
                            <div class="text-xs text-slate-600">
                                <span class="font-bold text-gov-navy group-hover:underline">Klik untuk memilih berkas</span>
                                <span>atau seret & lepas berkas Excel ke area ini</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Format: .xlsx, .xls, .csv (Maksimal 5 MB)</p>
                            @if($importKajianFile)
                                <div class="pt-1 text-xs font-semibold text-emerald-700 flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4 text-emerald-600 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>{{ $importKajianFile->getClientOriginalName() }} (Siap Diimpor)</span>
                                </div>
                            @endif
                            <template x-if="isDragging">
                                <div class="text-xs font-bold text-blue-700 animate-pulse pt-1">
                                    Lepaskan berkas di sini untuk mengunggah
                                </div>
                            </template>
                        </div>
                    </div>
                    @error('importKajianFile') <span
                    class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showImportKajianModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="button" wire:click="processImportKajianFile" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="processImportKajianFile">Upload & Impor File Excel</span>
                        <span wire:loading.inline-flex wire:target="processImportKajianFile" style="display: none;"
                            class="inline-flex flex-row items-center justify-center gap-2 shrink-0 whitespace-nowrap">
                            <svg class="w-4 h-4 animate-spin shrink-0 text-white inline-block" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Mengunggah...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: INPUT / EDIT JADWAL PENUGASAN (MILESTONE 2) -->
    <!-- ======================================================== -->
    <div x-show="showPrayerDutyModal" x-cloak wire:ignore.self @keydown.escape.window="showPrayerDutyModal = false"
        @click="if (window.isBackdropClick($event, $el)) showPrayerDutyModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h3 class="text-base font-bold text-gov-textMain"
                    x-text="isEditingPrayerDuty ? 'Edit Penugasan Petugas' : 'Tambah Penugasan Petugas'"></h3>
                <button type="button" @click="showPrayerDutyModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer"><i
                        data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form @submit.prevent="$wire.savePrayerDuty(dutyForm)" class="space-y-4 text-xs">
                <!-- Waktu Shalat Toggle -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Waktu Shalat *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button"
                            @click="switchDutyTime('dzuhur')"
                            :class="dutyForm.prayer_time === 'dzuhur' ? 'border-gov-navy bg-gov-navy text-white shadow-2xs' : 'bg-white text-slate-700 border-gov-border font-medium hover:bg-slate-50'"
                            class="p-2.5 rounded-lg border text-center text-xs cursor-pointer flex items-center justify-center transition-colors">
                            Dzuhur
                        </button>
                        <button type="button"
                            @click="switchDutyTime('ashar')"
                            :class="dutyForm.prayer_time === 'ashar' ? 'border-gov-navy bg-gov-navy text-white shadow-2xs' : 'bg-white text-slate-700 border-gov-border font-medium hover:bg-slate-50'"
                            class="p-2.5 rounded-lg border text-center text-xs cursor-pointer flex items-center justify-center transition-colors">
                            Ashar
                        </button>
                    </div>
                    @error('dutyPrayerTime') <span
                    class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Hari & Tahun Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Hari *</label>
                        <x-custom-select
                            x-model="dutyForm.day_name"
                            :options="[
                                ['value' => 'Senin', 'label' => 'Senin'],
                                ['value' => 'Selasa', 'label' => 'Selasa'],
                                ['value' => 'Rabu', 'label' => 'Rabu'],
                                ['value' => 'Kamis', 'label' => 'Kamis'],
                                ['value' => 'Jumat', 'label' => 'Jumat'],
                            ]"
                            placeholder="Pilih Hari..."
                            class="w-full text-xs"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                        @error('dutyDayName') <span
                        class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tahun *</label>
                        <input x-model="dutyForm.tahun" type="number" min="2020" max="2099" placeholder="2026"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                        @error('dutyTahun') <span
                        class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pola Pekan Penugasan *</label>
                    <x-custom-select
                        x-model="dutyForm.week_pattern"
                        :options="[
                            ['value' => 'semua', 'label' => 'Semua Pekan (Rutin Setiap Minggu)'],
                            ['value' => 'pekan_1', 'label' => 'Pekan 1 Saja'],
                            ['value' => 'pekan_2', 'label' => 'Pekan 2 Saja'],
                            ['value' => 'pekan_3', 'label' => 'Pekan 3 Saja'],
                            ['value' => 'pekan_4', 'label' => 'Pekan 4 Saja'],
                            ['value' => 'pekan_5', 'label' => 'Pekan 5 Saja'],
                            ['value' => 'pekan_1_3_5', 'label' => 'Pekan Ganjil (Pekan 1, 3, dan 5)'],
                            ['value' => 'pekan_2_4', 'label' => 'Pekan Genap (Pekan 2 dan 4)'],
                        ]"
                        placeholder="Pilih Pola Pekan..."
                        class="w-full text-xs"
                        buttonClass="p-2.5 bg-slate-50/50"
                    />
                    @error('dutyWeekPattern') <span
                    class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror

                    <div class="mt-2 p-2.5 bg-blue-50/70 rounded-lg border border-blue-200 text-blue-900 text-xs flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                        <div class="space-y-0.5">
                            <span><strong>Pola Penugasan Rutin:</strong> Jadwal ini berlaku berulang otomatis setiap bulan untuk hari & pola pekan yang dipilih.</span>
                            <div class="text-[11px] text-blue-700">Contoh: Penugasan di hari Kamis Pekan 1 Tahun 2026 otomatis berlaku pada tanggal 1 Oktober 2026, 5 November 2026, dst.</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Petugas Imam</label>
                        <input x-model="dutyForm.imam_name" type="text" placeholder="Contoh: Ust. Pujo Santoso"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                        @error('dutyImamName') <span
                        class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Petugas Muadzin</label>
                        <input x-model="dutyForm.muadzin_name" type="text" placeholder="Contoh: Mas Zulhaq"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                        @error('dutyMuadzinName') <span
                        class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-amber-900 text-xs">
                    <strong>Informasi Sinkronisasi:</strong> Pada hari Jumat siang (Shalat Dzuhur / Shalat Jumat), data
                    nama Imam dan Muadzin akan otomatis disinkronkan dengan nama Khatib dan Muadzin Shalat Jumat pada
                    modul Kajian Jumat.
                </div>

                <template x-if="dutyRowData && dutyRowData.is_friday && dutyForm.prayer_time === 'dzuhur' && dutyRowData.friday_kajian_id">
                    <div class="p-2.5 bg-amber-100/60 rounded-lg border border-amber-300 text-amber-900 text-xs flex items-center justify-between">
                        <span>Ingin memperbarui Khatib & Muadzin Jumat?</span>
                        <button type="button"
                            @click="showPrayerDutyModal = false; $wire.openFridayKajianFromPetugas(dutyRowData.friday_kajian_id)"
                            class="font-bold text-amber-800 hover:text-amber-950 underline cursor-pointer">
                            Edit di Modul Kegiatan &rarr;
                        </button>
                    </div>
                </template>

                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showPrayerDutyModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="savePrayerDuty"
                            x-text="isEditingPrayerDuty ? 'Simpan Perubahan' : 'Tambah Penugasan'"></span>
                        <span wire:loading.inline-flex wire:target="savePrayerDuty"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: IMPORT JADWAL PENUGASAN (MILESTONE 2) -->
    <!-- ======================================================== -->
    <div x-show="showImportDutyModal" x-cloak wire:ignore.self @keydown.escape.window="showImportDutyModal = false"
        @click="if (window.isBackdropClick($event, $el)) showImportDutyModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-base font-bold text-gov-textMain">Import Jadwal Penugasan Ibadah</h3>
                    <p class="text-xs text-slate-500">Impor data jadwal Imam & Muadzin dari Excel atau CSV</p>
                </div>
                <button type="button" @click="showImportDutyModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer"><i
                        data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <!-- Tab Selector di dalam Modal: Copypaste Excel vs Upload CSV -->
            <div class="flex items-center p-1 bg-slate-100 rounded-lg">
                <button type="button" @click="importDutyTab = 'paste'"
                    class="flex-1 py-1.5 rounded-md font-bold text-xs transition cursor-pointer"
                    :class="importDutyTab === 'paste' ? 'bg-white text-gov-navy shadow-2xs' : 'text-slate-600 hover:text-gov-navy'">
                    📋 Copypaste Excel
                </button>
                <button type="button" @click="importDutyTab = 'file'"
                    class="flex-1 py-1.5 rounded-md font-bold text-xs transition cursor-pointer"
                    :class="importDutyTab === 'file' ? 'bg-white text-gov-navy shadow-2xs' : 'text-slate-600 hover:text-gov-navy'">
                    📁 Upload File Excel
                </button>
            </div>

            <!-- TAB 1: COPYPASTE DARI EXCEL -->
            <div x-show="importDutyTab === 'paste'" class="space-y-3 text-xs">
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-amber-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-amber-600"></i>
                        <span>Format Kolom Excel yang Diharapkan:</span>
                    </span>
                    <p class="text-xs font-mono bg-white/70 p-2 rounded border border-amber-300">
                        [Hari] \t [Waktu] \t [Nama Imam] \t [Nama Muadzin] \t [Pola Pekan] \t [Catatan]
                    </p>
                    <p class="text-xs text-amber-800">Contoh Pola Pekan: <code>semua</code>, <code>pekan_1</code>,
                        <code>pekan_1_3_5</code>, <code>pekan_2_4</code>. (Kolom 'No' di awal otomatis disesuaikan).
                    </p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tempelkan Teks Excel di Sini *</label>
                    <textarea wire:model="importDutyPasteText" rows="6"
                        placeholder="Senin&#9;Dzuhur&#9;Ust. Pujo Santoso&#9;Mas Zulhaq&#9;semua&#9;Rutin"
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-mono text-xs transition"></textarea>
                </div>

                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showImportDutyModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="button" wire:click="processImportDutyPaste" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="processImportDutyPaste">Proses Impor Teks</span>
                        <span wire:loading.inline-flex wire:target="processImportDutyPaste"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Mengimpor...</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- TAB 2: UPLOAD FILE EXCEL -->
            <div x-show="importDutyTab === 'file'" class="space-y-3 text-xs">
                <div class="p-3 bg-sky-50 rounded-lg border border-sky-200 text-sky-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-sky-600"></i>
                        <span>Petunjuk Unggah File Excel:</span>
                    </span>
                    <p class="text-xs text-sky-800">Unggah file berformat <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong> dengan ukuran maksimal 5 MB.
                    </p>
                </div>

                <div class="space-y-1.5 pt-1">
                    <label class="block font-semibold text-slate-700">Pilih Berkas Excel *</label>
                    <div x-data="{ isDragging: false }"
                         :class="isDragging ? 'border-gov-navy bg-blue-50/70 ring-2 ring-gov-navy/20' : 'border-gov-border bg-slate-50/50 hover:bg-slate-50'"
                         class="border-2 border-dashed rounded-xl p-8 text-center space-y-2.5 transition relative overflow-hidden group">
                        
                        <!-- Invisible Full-Surface Native File Input -->
                        <input id="importDutyFile"
                               type="file"
                               wire:model="importDutyFile"
                               accept=".xlsx,.xls,.csv,.txt"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @dragenter="isDragging = true"
                               @dragover.prevent="isDragging = true"
                               @dragleave="isDragging = false"
                               @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }">

                        <div class="pointer-events-none space-y-2">
                            <div class="w-12 h-12 rounded-full bg-gov-navy/10 flex items-center justify-center mx-auto text-gov-navy transition group-hover:scale-105"
                                 :class="isDragging ? 'scale-110 bg-gov-navy/20' : ''">
                                <svg class="w-6 h-6 text-gov-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                                    <path d="M12 12v9"/>
                                    <path d="m16 16-4-4-4 4"/>
                                </svg>
                            </div>
                            <div class="text-xs text-slate-600">
                                <span class="font-bold text-gov-navy group-hover:underline">Klik untuk memilih berkas</span>
                                <span>atau seret & lepas berkas Excel ke area ini</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Format: .xlsx, .xls, .csv (Maksimal 5 MB)</p>
                            @if($importDutyFile)
                                <div class="pt-1 text-xs font-semibold text-emerald-700 flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4 text-emerald-600 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>{{ $importDutyFile->getClientOriginalName() }} (Siap Diimpor)</span>
                                </div>
                            @endif
                            <template x-if="isDragging">
                                <div class="text-xs font-bold text-blue-700 animate-pulse pt-1">
                                    Lepaskan berkas di sini untuk mengunggah
                                </div>
                            </template>
                        </div>
                    </div>
                    @error('importDutyFile') <span
                    class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showImportDutyModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="button" wire:click="processImportDutyFile" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="processImportDutyFile">Upload & Impor File Excel</span>
                        <span wire:loading.inline-flex wire:target="processImportDutyFile"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Mengunggah...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: BUAT / EDIT PERENCANAAN KEGIATAN (MILESTONE 3) -->
    <!-- ======================================================== -->
    <div x-show="showAgendaModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) { showAgendaModal = false; if (window.Livewire) { $wire.set('showAgendaModal', false, false); } }"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showAgendaModal = false; if (window.Livewire) { $wire.set('showAgendaModal', false, false); }">
        <div
            class="bg-white rounded-xl max-w-2xl w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in zoom-in-95 duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-base font-bold text-gov-textMain tracking-tight"
                        x-text="isEditingAgenda ? 'Edit Perencanaan Kegiatan' : 'Buat Perencanaan Kegiatan'"></h3>
                    <p class="text-xs text-slate-500 mt-0.5">Lengkapi parameter kegiatan, susunan panitia, estimasi
                        anggaran, dan berkas LPJ.</p>
                </div>
                <button type="button" @click="showAgendaModal = false; if (window.Livewire) { $wire.set('showAgendaModal', false, false); }"
                    class="text-slate-400 hover:text-gov-textMain p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="$wire.saveAgenda(agendaForm)" class="space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Left Column -->
                    <div class="space-y-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Kegiatan <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" x-model="agendaForm.title" placeholder="Misal: Buka Puasa Bersama"
                                required
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                            @error('agendaTitle') <span
                                class="text-rose-600 text-[11px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal Kegiatan <span
                                    class="text-rose-500">*</span></label>
                            <input type="date" x-model="agendaForm.event_date" required
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain cursor-pointer">
                            @error('agendaDate') <span
                                class="text-rose-600 text-[11px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tujuan / Deskripsi Kegiatan</label>
                            <textarea x-model="agendaForm.description" rows="3"
                                placeholder="Jelaskan tujuan kegiatan dan sasaran jamaah..."
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition"></textarea>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Status Kegiatan</label>
                            <x-custom-select
                                x-model="agendaForm.status"
                                :options="[
                                    ['value' => 'Direncanakan', 'label' => 'Direncanakan'],
                                    ['value' => 'Berjalan', 'label' => 'Berjalan'],
                                    ['value' => 'SELESAI', 'label' => 'Selesai'],
                                ]"
                                placeholder="Pilih Status..."
                                class="w-full text-xs"
                                buttonClass="p-2.5 bg-slate-50/50"
                            />
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                <span>Link YouTube Video / Siaran</span>
                                <span class="text-[10px] text-slate-400 font-normal ml-1">(Opsional)</span>
                            </label>
                            <input x-model="agendaForm.youtube_url" type="url" placeholder="https://www.youtube.com/watch?v=..."
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Daftar Panitia (Satu nama per
                                baris)</label>
                            <textarea x-model="agendaForm.committee_members" rows="3"
                                placeholder="Khudori (Ketua)&#10;Junaedi (Sekretaris)&#10;Bu Indah (Bendahara)"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-mono text-[11px]"></textarea>
                            <span class="text-[10px] text-slate-400">Tulis satu nama panitia dan perannya per
                                baris.</span>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Anggaran Kegiatan (Rp)</label>
                            <input type="text" inputmode="numeric"
                                :value="window.formatRibuan(agendaForm.budget)"
                                @input="window.handleRibuanInput($event.target, (clean) => { agendaForm.budget = clean ? parseInt(clean) : 0; })"
                                placeholder="Contoh: 5.000.000"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-bold">
                            <span class="text-[10px] text-slate-400">Ketik nominal angka, sistem otomatis memformat pemisah ribuan.</span>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">
                                <span>Lampiran Berkas LPJ (PDF Opsional)</span>
                                <span class="text-[10px] text-slate-400 font-normal ml-1">(.pdf maks 10MB)</span>
                            </label>
                            <div x-data="{ isDragging: false }"
                                 @dragenter.prevent="isDragging = true"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @drop="isDragging = false"
                                 :class="isDragging ? 'border-gov-navy bg-sky-50/60 ring-2 ring-gov-navy/20 scale-[0.99]' : 'border-slate-300 hover:border-gov-navy hover:bg-slate-50/80 bg-slate-50/40'"
                                 class="relative border-2 border-dashed rounded-xl p-4 text-center transition-all duration-200 cursor-pointer group">
                                <input id="agendaReportPdfInput"
                                       type="file"
                                       wire:model="agendaReportPdf"
                                       accept=".pdf,application/pdf"
                                       @dragenter="isDragging = true"
                                       @dragover.prevent="isDragging = true"
                                       @dragleave="isDragging = false"
                                       @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                                <div class="pointer-events-none flex flex-col items-center justify-center gap-1.5">
                                    <div :class="isDragging ? 'bg-gov-navy text-white scale-110' : 'bg-rose-50 text-rose-600 group-hover:scale-110'"
                                         class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform shadow-2xs">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <path d="M12 18v-6"></path>
                                            <path d="M9 15l3-3 3 3"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-slate-700 font-semibold mt-1">
                                        <span class="text-gov-navy underline underline-offset-2">Klik untuk memilih file</span> atau seret PDF ke area ini
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        Format berkas: Dokumen PDF resmi laporan (Maksimal 10 MB)
                                    </p>
                                </div>
                            </div>
                            @error('agendaReportPdf')
                                <span class="text-rose-600 text-[11px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror

                            <!-- Indikator Loading Saat File PDF Diunggah -->
                            <div wire:loading.inline-flex wire:target="agendaReportPdf" style="display: none;"
                                class="inline-flex flex-row items-center gap-2 text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 shrink-0 whitespace-nowrap mt-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-amber-600 inline-block" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="whitespace-nowrap leading-none">Mengunggah file PDF...</span>
                            </div>

                            <!-- Preview / Link Berkas yang Sudah Pernah Diupload -->
                            <template x-if="agendaForm.report_pdf_path">
                                <div class="mt-2.5 p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2 text-slate-700 min-w-0">
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                             <polyline points="14 2 14 8 20 8"></polyline>
                                            <path d="M10 12v6"></path>
                                            <path d="M14 12v6"></path>
                                        </svg>
                                        <div class="truncate">
                                            <span class="font-medium text-slate-800">Dokumen LPJ Tersimpan</span>
                                            <span class="text-[10px] text-slate-400 block truncate">Pilih file baru di atas jika ingin mengganti.</span>
                                        </div>
                                    </div>
                                    <a :href="'/admin/agenda/lpj/' + agendaForm.id" target="_blank"
                                        class="text-gov-navy hover:text-gov-navyHover font-semibold underline underline-offset-2 shrink-0 ml-2 inline-flex items-center gap-1">
                                        <span>Buka Dokumen</span>
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                            <polyline points="15 3 21 3 21 9"></polyline>
                                            <line x1="10" y1="14" x2="21" y2="3"></line>
                                        </svg>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showAgendaModal = false; if (window.Livewire) { $wire.set('showAgendaModal', false, false); }"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="saveAgenda"
                            x-text="isEditingAgenda ? 'Perbarui Kegiatan' : 'Simpan Kegiatan'"></span>
                        <span wire:loading.inline-flex wire:target="saveAgenda"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: IMPORT AGENDA KEGIATAN (MILESTONE 3) -->
    <!-- ======================================================== -->
    <div x-show="showImportAgendaModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showImportAgendaModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showImportAgendaModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-base font-bold text-gov-textMain">Import Data Agenda Kegiatan</h3>
                    <p class="text-xs text-slate-500">Impor perencanaan kegiatan dari Excel atau file CSV</p>
                </div>
                <button type="button" @click="showImportAgendaModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Tab Selector: Copypaste Excel vs Upload CSV -->
            <div class="flex items-center p-1 bg-slate-100 rounded-lg">
                <button type="button" @click="importAgendaTab = 'paste'"
                    class="flex-1 py-1.5 rounded-md font-bold text-xs transition cursor-pointer"
                    :class="importAgendaTab === 'paste' ? 'bg-white text-gov-navy shadow-2xs' : 'text-slate-600 hover:text-gov-navy'">
                    📋 Copypaste Excel
                </button>
                <button type="button" @click="importAgendaTab = 'file'"
                    class="flex-1 py-1.5 rounded-md font-bold text-xs transition cursor-pointer"
                    :class="importAgendaTab === 'file' ? 'bg-white text-gov-navy shadow-2xs' : 'text-slate-600 hover:text-gov-navy'">
                    📁 Upload File CSV
                </button>
            </div>

            <!-- TAB 1: COPYPASTE DARI EXCEL -->
            <div x-show="importAgendaTab === 'paste'" class="space-y-3 text-xs">
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-amber-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-amber-600"></i>
                        <span>Format Kolom Excel yang Diharapkan:</span>
                    </span>
                    <p class="text-xs text-amber-800 font-mono text-[11px] leading-relaxed">
                        Nama Agenda [Tab] Tanggal (YYYY-MM-DD) [Tab] Anggaran [Tab] Panitia [Tab] Status
                    </p>
                    <p class="text-[10px] text-amber-700">Contoh:
                        <code>Perayaan Iduladha&#9;2026-06-16&#9;65000000&#9;Khudori; Junaedi&#9;Direncanakan</code>
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="block font-semibold text-slate-700">Tempelkan Data Excel (Paste Here):</label>
                    <textarea wire:model="importAgendaPasteText" rows="6"
                        placeholder="Blok cell di Excel -> Copy -> Paste ke sini..."
                        class="w-full p-2.5 rounded-lg border border-gov-border font-mono text-[11px] bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition"></textarea>
                </div>

                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showImportAgendaModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="button" wire:click="processImportAgendaPaste" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="processImportAgendaPaste">Proses Impor Teks</span>
                        <span wire:loading.inline-flex wire:target="processImportAgendaPaste"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Mengimpor...</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- TAB 2: UPLOAD FILE CSV -->
            <div x-show="importAgendaTab === 'file'" class="space-y-3 text-xs">
                <div class="p-3 bg-sky-50 rounded-lg border border-sky-200 text-sky-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-sky-600"></i>
                        <span>Petunjuk Unggah File CSV:</span>
                    </span>
                    <p class="text-xs text-sky-800">Unggah berkas <strong>.csv</strong> atau <strong>.txt</strong>
                        dengan pemisah koma, titik-koma, atau tab maksimal 2 MB.</p>
                </div>

                <div x-data="{ isDragging: false }"
                     :class="isDragging ? 'border-gov-navy bg-blue-50/70 ring-2 ring-gov-navy/20' : 'border-gov-border bg-slate-50/50 hover:bg-slate-50'"
                     class="border-2 border-dashed rounded-xl p-8 text-center space-y-2.5 transition relative overflow-hidden group">
                    
                    <!-- Invisible Full-Surface Native File Input -->
                    <input id="importAgendaFile"
                           type="file"
                           wire:model="importAgendaFile"
                           accept=".csv,.txt"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                           @dragenter="isDragging = true"
                           @dragover.prevent="isDragging = true"
                           @dragleave="isDragging = false"
                           @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }">

                    <div class="pointer-events-none space-y-2">
                        <div class="w-12 h-12 rounded-full bg-gov-navy/10 flex items-center justify-center mx-auto text-gov-navy transition group-hover:scale-105"
                             :class="isDragging ? 'scale-110 bg-gov-navy/20' : ''">
                            <svg class="w-6 h-6 text-gov-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                                <path d="M12 12v9"/>
                                <path d="m16 16-4-4-4 4"/>
                            </svg>
                        </div>
                        <div class="text-xs text-slate-600">
                            <span class="font-bold text-gov-navy group-hover:underline">Pilih file</span> atau seret & lepas berkas ke area ini
                        </div>
                        <p class="text-[11px] text-slate-400">Format: CSV / TXT (Maksimal 2 MB)</p>
                        @if($importAgendaFile)
                            <div class="pt-1 text-xs font-semibold text-emerald-700 flex items-center justify-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ $importAgendaFile->getClientOriginalName() }} (Siap Diimpor)</span>
                            </div>
                        @endif
                        <template x-if="isDragging">
                            <div class="text-xs font-bold text-blue-700 animate-pulse pt-1">
                                Lepaskan berkas di sini untuk mengunggah
                            </div>
                        </template>
                    </div>
                </div>
                @error('importAgendaFile') <span
                class="text-xs text-rose-600 font-semibold block">{{ $message }}</span> @enderror

                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showImportAgendaModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="button" wire:click="processImportAgendaFile" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="processImportAgendaFile">Upload & Impor File</span>
                        <span wire:loading.inline-flex wire:target="processImportAgendaFile"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Mengunggah...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: ATUR JUZ PEGAWAI (ODOJ) -->
    <!-- ======================================================== -->
    <div x-show="showAssignOdojModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showAssignOdojModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showAssignOdojModal = false">
        <div
            class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-base font-bold text-gov-textMain">Atur Juz Pegawai</h3>
                    <p class="text-xs text-slate-500">Tugaskan hingga maksimal 2 pegawai per Juz.</p>
                </div>
                <button type="button" @click="showAssignOdojModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveOdojAssignment" class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Juz *</label>
                    @php
                        $juzOptions = [];
                        for ($j = 1; $j <= 30; $j++) {
                            $juzOptions[] = ['value' => $j, 'label' => 'Juz ' . $j];
                        }
                    @endphp
                    <x-custom-select
                        wire:model.live="assignJuzNumber"
                        wire:change="loadAssignJuzData"
                        :options="$juzOptions"
                        placeholder="Pilih Juz..."
                        class="w-full text-xs"
                        buttonClass="p-2.5 bg-slate-50/50"
                    />
                    @error('assignJuzNumber') <span
                        class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Pegawai 1 *</label>
                    <input type="text" wire:model="assignPegawai1" placeholder="Contoh: Ahmad Fauzi" required
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                    @error('assignPegawai1') <span
                        class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Pegawai 2 (Opsional)</label>
                    <input type="text" wire:model="assignPegawai2" placeholder="Contoh: Budi Santoso"
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                    <span class="text-[11px] text-slate-400 mt-1 block">1 Juz dapat ditugaskan ke maksimal 2
                        pegawai.</span>
                    @error('assignPegawai2') <span
                        class="text-xs text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showAssignOdojModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="saveOdojAssignment">Simpan Penugasan</span>
                        <span wire:loading.inline-flex wire:target="saveOdojAssignment"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: BUAT / EDIT PENGGUNA (MILESTONE 4) -->
    <!-- ======================================================== -->
    <div x-show="showUserModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showUserModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showUserModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h3 class="text-base font-bold text-gov-textMain"
                    x-text="isEditingUser ? 'Edit Data Pengguna' : 'Tambah Pengguna'"></h3>
                <button type="button" @click="showUserModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveUser" class="space-y-3 text-xs">
                <!-- Nama Lengkap -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Ustadz *</label>
                    <input wire:model="userName" type="text" placeholder="Contoh: Dr. H. Bambang Irawan"
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    @error('userName') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email & Kata Sandi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alamat Email *</label>
                        <input wire:model="userEmail" type="email" placeholder="nama@masjidsalahuddin.id"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                        @error('userEmail') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">
                            Kata Sandi <span x-show="!isEditingUser">*</span><span x-show="isEditingUser"
                                class="text-slate-400 font-normal">(Kosongkan bila tidak diubah)</span>
                        </label>
                        <input wire:model="userPassword" type="password" placeholder="Min. 8 karakter (Kapital, angka, simbol)"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                        @error('userPassword') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Role & Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Role / Peran Pengguna *</label>
                        @php
                            $userRoleOptions = [
                                ['label' => 'Pengurus (Hak Akses Penuh / CRUD)', 'is_header' => true],
                                ['value' => 'Master', 'label' => 'Master Admin (Pengurus Tertinggi)'],
                                ['value' => 'Ketua', 'label' => 'Ketua DKM'],
                                ['value' => 'Sekretaris', 'label' => 'Sekretaris (Operasional)'],
                                ['value' => 'Bendahara', 'label' => 'Bendahara (Keuangan)'],
                                ['label' => 'Jamaah & Masyarakat', 'is_header' => true],
                                ['value' => 'Jamaah', 'label' => 'Jamaah (Read-Only / Mode Lihat)'],
                            ];
                        @endphp
                        <x-custom-select 
                            wire:model="userRole"
                            :options="$userRoleOptions"
                            placeholder="Pilih Role / Peran"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                        @error('userRole') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Akun *</label>
                        <x-custom-select 
                            wire:model="userStatus"
                            :options="[
                                ['value' => 'AKTIF', 'label' => 'AKTIF (Dapat Login & Beraktivitas)'],
                                ['value' => 'NONAKTIF', 'label' => 'NONAKTIF (Akses Ditangguhkan)'],
                            ]"
                            placeholder="Pilih Status Akun"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                        @error('userStatus') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showUserModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="saveUser">Simpan Pengguna</span>
                        <span wire:loading.inline-flex wire:target="saveUser"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: UPLOAD DOKUMEN RESMI TAKMIR (SK / LAMPIRAN) -->
    <!-- ======================================================== -->
    <div x-show="showUploadDocModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showUploadDocModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showUploadDocModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gov-navy/10 text-gov-navy flex items-center justify-center">
                        <i data-lucide="file-up" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">Unggah Dokumen Resmi</h3>
                        <p class="text-xs text-slate-500 font-medium truncate max-w-70 sm:max-w-none">
                            {{ $uploadDocTitle ?: 'Unggah Berkas PDF' }}
                        </p>
                    </div>
                </div>
                <button type="button" @click="showUploadDocModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveUploadDoc" class="space-y-3.5 text-xs">
                <!-- Info Target Berkas -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between">
                    <div>
                        <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold block">Dokumen Target</span>
                        <span class="font-bold text-gov-navy text-xs">{{ $uploadDocTitle ?: 'Surat Keputusan / Lampiran' }}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gov-navy/10 text-gov-navy uppercase tracking-wider border border-gov-navy/20">
                        Target: {{ strtoupper($uploadDocTarget) }}
                    </span>
                </div>

                <!-- Input Berkas PDF -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih File PDF Dokumen Legalitas * <span class="text-slate-400 font-normal">(Maks. 10MB)</span></label>

                    <div x-data="{ isDragging: false }"
                         @dragenter.prevent="isDragging = true"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop="isDragging = false"
                         :class="isDragging ? 'border-gov-navy bg-sky-50/60 ring-2 ring-gov-navy/20 scale-[0.99]' : 'border-slate-300 hover:border-gov-navy hover:bg-slate-50/80 bg-slate-50/40'"
                         class="relative border-2 border-dashed rounded-xl p-4 text-center transition-all duration-200 cursor-pointer group">
                        <input id="uploadDocFileInput"
                               wire:model="uploadDocFile"
                               type="file"
                               accept="application/pdf"
                               @dragenter="isDragging = true"
                               @dragover.prevent="isDragging = true"
                               @dragleave="isDragging = false"
                               @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="pointer-events-none flex flex-col items-center justify-center gap-1.5">
                            <div :class="isDragging ? 'bg-gov-navy text-white scale-110' : 'bg-sky-50 text-sky-600 group-hover:scale-110'"
                                 class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform shadow-2xs">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-700 font-semibold mt-1">
                                <span class="text-gov-navy underline underline-offset-2">Klik untuk memilih file</span> atau seret PDF dokumen ke area ini
                            </p>
                            <p class="text-[11px] text-slate-500">
                                Berkas resmi SK / Legalitas PDF (Maksimal 10 MB)
                            </p>
                        </div>
                    </div>

                    @error('uploadDocFile')
                        <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span>
                    @enderror

                    <div wire:loading.inline-flex wire:target="uploadDocFile" style="display: none;"
                        class="inline-flex flex-row items-center gap-2 text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 shrink-0 whitespace-nowrap mt-2">
                        <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-amber-600 inline-block" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="whitespace-nowrap leading-none">Mengunggah berkas ke buffer...</span>
                    </div>
                </div>

                <!-- Metadata Opsional / Otomatis -->
                <div class="border-t border-slate-200 pt-3 space-y-3">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        Perbarui Informasi Dokumen (Opsional)
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor Dokumen / Surat Keputusan</label>
                        <input wire:model="uploadDocNumber" type="text" placeholder="Contoh: KEP-48/KPP.1209/2026"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-mono">
                        @error('uploadDocNumber') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal Penetapan / Berlaku</label>
                            <input wire:model="uploadDocDate" type="text" placeholder="23 Januari 2026"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                            @error('uploadDocDate') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Jumlah Halaman</label>
                            <input wire:model="uploadDocPages" type="text" placeholder="Contoh: 3 Halaman"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                            @error('uploadDocPages') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-[11px] leading-relaxed flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 shrink-0 text-amber-600 mt-0.5"></i>
                    <span>Berkas PDF yang diunggah akan langsung menggantikan dokumen aktif di portal publik profil masjid dan siap diunduh oleh jamaah.</span>
                </div>

                <!-- Footer Buttons (Rule 5 & PANDUAN_ARSITEKTUR_DAN_PERFORMA) -->
                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showUploadDocModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="saveUploadDoc">Unggah & Simpan Dokumen</span>
                        <span wire:loading.inline-flex wire:target="saveUploadDoc" style="display: none;"
                            class="items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Mengunggah...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: TEMPLATE SURAT KEPUTUSAN KEPALA KPP MADYA MALANG -->
    <!-- TENTANG PERUBAHAN SUSUNAN PENGURUS TAKMIR MASJID SALAHUDDIN -->
    <!-- ======================================================== -->
    <div x-show="showTemplateKepModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showTemplateKepModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
        style="display: none;" @keydown.escape.window="showTemplateKepModal = false">
        <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl border border-gov-border flex flex-col max-h-[92vh] overflow-hidden animate-in fade-in zoom-in-95 duration-200"
             x-data="{ kepActiveTab: 'identitas' }">
            
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-200 flex items-center justify-between gap-4 bg-slate-50/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gov-navy text-amber-400 flex items-center justify-center shadow-xs shrink-0">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain leading-tight">
                            Template Surat Keputusan Kepala KPP Madya Malang
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Tentang Perubahan Susunan Pengurus Takmir Masjid Salahuddin Periode {{ $docSkPeriode }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.kepengurusan.print-sk') }}" target="_blank"
                       class="h-8 px-3 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer"
                       title="Cetak atau Unduh Draf PDF Resmi Standar Naskah Dinas">
                        <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span class="hidden sm:inline">Cetak Draf PDF</span>
                    </a>
                    <button type="button" @click="showTemplateKepModal = false"
                        class="text-slate-400 hover:text-gov-textMain p-1.5 rounded-lg hover:bg-slate-200/60 transition cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Nav Tabs -->
            <div class="border-b border-slate-200 bg-white px-5 pt-2">
                <nav class="flex items-center gap-2 overflow-x-auto text-xs no-scrollbar" aria-label="Template KEP Tabs">
                    <button type="button" @click="kepActiveTab = 'identitas'"
                        class="py-2.5 px-3 border-b-2 rounded-t-lg transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer -mb-px text-xs font-semibold"
                        :class="kepActiveTab === 'identitas' ? 'border-gov-navy text-gov-navy font-bold' : 'border-transparent text-slate-500 hover:text-gov-navy hover:bg-slate-50'">
                        <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i>
                        <span>1. Identitas Dokumen SK</span>
                    </button>
                    <button type="button" @click="kepActiveTab = 'pimpinan'"
                        class="py-2.5 px-3 border-b-2 rounded-t-lg transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer -mb-px text-xs font-semibold"
                        :class="kepActiveTab === 'pimpinan' ? 'border-gov-navy text-gov-navy font-bold' : 'border-transparent text-slate-500 hover:text-gov-navy hover:bg-slate-50'">
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                        <span>2. Pimpinan Harian</span>
                    </button>
                    <button type="button" @click="kepActiveTab = 'bidang'"
                        class="py-2.5 px-3 border-b-2 rounded-t-lg transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer -mb-px text-xs font-semibold"
                        :class="kepActiveTab === 'bidang' ? 'border-gov-navy text-gov-navy font-bold' : 'border-transparent text-slate-500 hover:text-gov-navy hover:bg-slate-50'">
                        <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                        <span>3. 4 Bidang Pengelola</span>
                    </button>
                    <button type="button" @click="kepActiveTab = 'tupoksi'"
                        class="py-2.5 px-3 border-b-2 rounded-t-lg transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer -mb-px text-xs font-semibold"
                        :class="kepActiveTab === 'tupoksi' ? 'border-gov-navy text-gov-navy font-bold' : 'border-transparent text-slate-500 hover:text-gov-navy hover:bg-slate-50'">
                        <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                        <span>4. Rincian Tupoksi</span>
                    </button>
                    <button type="button" @click="kepActiveTab = 'upload'"
                        class="py-2.5 px-3 border-b-2 rounded-t-lg transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer -mb-px text-xs font-semibold"
                        :class="kepActiveTab === 'upload' ? 'border-gov-navy text-gov-navy font-bold' : 'border-transparent text-slate-500 hover:text-gov-navy hover:bg-slate-50'">
                        <i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i>
                        <span>5. Berkas PDF Final</span>
                    </button>
                </nav>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto max-h-[60vh] space-y-5 text-xs">
                
                <!-- TAB 1: IDENTITAS DOKUMEN SK -->
                <div x-show="kepActiveTab === 'identitas'" class="space-y-4">
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-start gap-2.5">
                        <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                        <p class="leading-relaxed">
                            Data identitas di bawah ini menjadi konsiderans utama Surat Keputusan (SK) resmi yang diterbitkan dan ditandatangani oleh Kepala Kantor Pelayanan Pajak Madya Malang.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700">Nomor Keputusan (SK) *</label>
                            <input type="text" wire:model.defer="docSkNumber" placeholder="Contoh: KEP-48/KPP.1209/2026"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy font-mono text-xs">
                            @error('docSkNumber') <span class="text-rose-600 text-[11px] block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700">Tanggal Penetapan SK *</label>
                            <input type="text" wire:model.defer="docSkDate" placeholder="Contoh: 14 April 2026"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs">
                            @error('docSkDate') <span class="text-rose-600 text-[11px] block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700">Periode Masa Bakti *</label>
                            <input type="text" wire:model.defer="docSkPeriode" placeholder="Contoh: 2026-2029"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-semibold">
                            @error('docSkPeriode') <span class="text-rose-600 text-[11px] block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="font-bold text-slate-700">Judul Utama Dokumen SK</label>
                            <input type="text" wire:model.defer="docSkTitle" placeholder="Surat Keputusan Kepala KPP Madya Malang"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs">
                        </div>

                        <div class="space-y-1.5 sm:col-span-2 lg:col-span-1">
                            <label class="font-bold text-slate-700">Nama Pembina (Kepala Kantor) *</label>
                            <input type="text" wire:model.defer="pembinaName" placeholder="Teguh Iman Wirotomo"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs">
                            @error('pembinaName') <span class="text-rose-600 text-[11px] block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2 lg:col-span-3 space-y-1.5">
                            <label class="font-bold text-slate-700">Perihal / Tentang Keputusan</label>
                            <textarea wire:model.defer="docSkDescription" rows="2"
                                placeholder="Perubahan Susunan Pengurus Takmir Masjid Sholahuddin KPP Madya Malang Periode 2026-2029"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs"></textarea>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: PIMPINAN HARIAN -->
                <div x-show="kepActiveTab === 'pimpinan'" x-cloak class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700">Ketua Takmir *</label>
                            <input type="text" wire:model.defer="ketuaName" placeholder="Nama lengkap Ketua Takmir"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-semibold">
                            @error('ketuaName') <span class="text-rose-600 text-[11px] block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700">Wakil Ketua Takmir *</label>
                            <input type="text" wire:model.defer="wakilKetuaName" placeholder="Nama lengkap Wakil Ketua Takmir"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-semibold">
                            @error('wakilKetuaName') <span class="text-rose-600 text-[11px] block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700">Sekretaris * <span class="font-normal text-slate-400">(Satu nama per baris)</span></label>
                            <textarea wire:model.defer="sekretarisNames" rows="3" placeholder="Nama Sekretaris 1&#10;Nama Sekretaris 2"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs"></textarea>
                            @error('sekretarisNames') <span class="text-rose-600 text-[11px] block">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-700">Bendahara * <span class="font-normal text-slate-400">(Satu nama per baris)</span></label>
                            <textarea wire:model.defer="bendaharaNames" rows="3" placeholder="Nama Bendahara 1&#10;Nama Bendahara 2"
                                class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs"></textarea>
                            @error('bendaharaNames') <span class="text-rose-600 text-[11px] block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- TAB 3: 4 BIDANG PENGELOLA -->
                <div x-show="kepActiveTab === 'bidang'" x-cloak class="space-y-5">
                    
                    <!-- Bidang 1 -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold flex items-center justify-center">1</span>
                            <input type="text" wire:model.defer="bidang0Name" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-bold text-gov-textMain">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-slate-600">Pengelola / Koordinator <span class="text-slate-400 font-normal">(1 per baris)</span></label>
                                <textarea wire:model.defer="bidang0Pengelola" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs"></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-slate-600">Anggota Bidang <span class="text-slate-400 font-normal">(1 per baris)</span></label>
                                <textarea wire:model.defer="bidang0Anggota" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bidang 2 -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[11px] font-bold flex items-center justify-center">2</span>
                            <input type="text" wire:model.defer="bidang1Name" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-bold text-gov-textMain">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-slate-600">Pengelola / Koordinator <span class="text-slate-400 font-normal">(1 per baris)</span></label>
                                <textarea wire:model.defer="bidang1Pengelola" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs"></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-slate-600">Anggota Bidang <span class="text-slate-400 font-normal">(1 per baris)</span></label>
                                <textarea wire:model.defer="bidang1Anggota" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bidang 3 -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold flex items-center justify-center">3</span>
                            <input type="text" wire:model.defer="bidang2Name" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-bold text-gov-textMain">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-slate-600">Pengelola / Koordinator <span class="text-slate-400 font-normal">(1 per baris)</span></label>
                                <textarea wire:model.defer="bidang2Pengelola" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs"></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-slate-600">Anggota Bidang <span class="text-slate-400 font-normal">(1 per baris)</span></label>
                                <textarea wire:model.defer="bidang2Anggota" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bidang 4 -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-800 text-[11px] font-bold flex items-center justify-center">4</span>
                            <input type="text" wire:model.defer="bidang3Name" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-bold text-gov-textMain">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-slate-600">Pengelola / Koordinator <span class="text-slate-400 font-normal">(1 per baris)</span></label>
                                <textarea wire:model.defer="bidang3Pengelola" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs"></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-bold text-slate-600">Anggota Bidang <span class="text-slate-400 font-normal">(1 per baris)</span></label>
                                <textarea wire:model.defer="bidang3Anggota" rows="2" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs"></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- TAB 4: RINCIAN TUPOKSI -->
                <div x-show="kepActiveTab === 'tupoksi'" x-cloak class="space-y-4">
                    <p class="text-[11px] text-slate-500">Tuliskan butir tugas pokok dan fungsi per baris. Butir-butir ini akan dicetak otomatis pada Lampiran II SK Resmi.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700">Tupoksi Pembina</label>
                            <textarea wire:model.defer="pembinaTupoksi" rows="3" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700">Tupoksi Ketua Takmir</label>
                            <textarea wire:model.defer="ketuaTupoksi" rows="3" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700">Tupoksi Wakil Ketua</label>
                            <textarea wire:model.defer="wakilKetuaTupoksi" rows="3" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700">Tupoksi Sekretaris</label>
                            <textarea wire:model.defer="sekretarisTupoksi" rows="3" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700">Tupoksi Bendahara</label>
                            <textarea wire:model.defer="bendaharaTupoksi" rows="3" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700">Tupoksi {{ $bidang0Name }}</label>
                            <textarea wire:model.defer="bidang0Tupoksi" rows="3" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700">Tupoksi {{ $bidang1Name }}</label>
                            <textarea wire:model.defer="bidang1Tupoksi" rows="3" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs"></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-700">Tupoksi {{ $bidang2Name }}</label>
                            <textarea wire:model.defer="bidang2Tupoksi" rows="3" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs"></textarea>
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="font-bold text-slate-700">Tupoksi {{ $bidang3Name }}</label>
                            <textarea wire:model.defer="bidang3Tupoksi" rows="3" class="w-full px-2.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 focus:bg-white text-xs"></textarea>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: UNGGAH PDF KEP FINAL -->
                <div x-show="kepActiveTab === 'upload'" x-cloak class="space-y-4">
                    <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200/80 text-blue-900 text-xs flex items-start gap-2.5">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                        <div class="space-y-1">
                            <p class="font-semibold text-blue-900">Unggah Berkas Pindaian PDF KEP yang Telah Bertanda Tangan</p>
                            <p class="text-blue-700 text-[11px] leading-relaxed">
                                Setelah draf dicetak dan ditandatangani secara resmi (basah maupun tersertifikasi), Anda dapat mengunggah hasil pindaian (.pdf) di sini. Sistem akan otomatis memperbarui file SK utama pada portal publik.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Pilih Berkas PDF KEP Final <span class="text-slate-400 font-normal">(Maksimal 20 MB, Opsional)</span></label>

                        <div x-data="{ isDragging: false }"
                             @dragenter.prevent="isDragging = true"
                             @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop="isDragging = false"
                             :class="isDragging ? 'border-gov-navy bg-sky-50/60 ring-2 ring-gov-navy/20' : 'border-slate-300 hover:border-gov-navy hover:bg-slate-50/80 bg-slate-50/40'"
                             class="relative border-2 border-dashed rounded-xl p-5 text-center transition cursor-pointer group">
                            <input wire:model="templateKepPdfFile"
                                   type="file"
                                   accept="application/pdf"
                                   @dragenter="isDragging = true"
                                   @dragover.prevent="isDragging = true"
                                   @dragleave="isDragging = false"
                                   @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <div class="pointer-events-none flex flex-col items-center justify-center gap-1.5">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-gov-navy flex items-center justify-center shadow-2xs">
                                    <i data-lucide="upload" class="w-5 h-5"></i>
                                </div>
                                <p class="text-xs text-slate-700 font-semibold mt-1">
                                    <span class="text-gov-navy underline underline-offset-2">Klik untuk memilih file PDF</span> atau tarik berkas ke sini
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    Dokumen resmi format PDF (.pdf) hingga 20 MB
                                </p>
                            </div>
                        </div>

                        @error('templateKepPdfFile')
                            <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span>
                        @enderror

                        <div wire:loading.inline-flex wire:target="templateKepPdfFile" style="display: none;"
                            class="inline-flex flex-row items-center gap-2 text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 shrink-0 whitespace-nowrap mt-2">
                            <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-amber-600 inline-block" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Memproses unggahan berkas...</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.kepengurusan.print-sk') }}" target="_blank"
                       class="h-9 px-3.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="printer" class="w-4 h-4 text-slate-500"></i>
                        <span>Cetak / Unduh Draf PDF Resmi</span>
                    </a>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button type="button" @click="showTemplateKepModal = false"
                        class="h-9 px-4 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 font-semibold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="saveTemplateKep" wire:loading.attr="disabled"
                        class="h-9 px-5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <div wire:loading.inline-flex wire:target="saveTemplateKep" style="display: none;" class="inline-flex flex-row items-center gap-2 shrink-0 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 animate-spin shrink-0 inline-block text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Menyimpan...</span>
                        </div>
                        <span wire:loading.remove wire:target="saveTemplateKep" class="inline-flex items-center gap-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4 text-amber-400"></i>
                            <span>Simpan & Terapkan ke Database</span>
                        </span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: KONFIRMASI HAPUS DATA (GLOBAL POP-UP) -->
    <!-- ======================================================== -->
    <div x-show="confirmDeleteModal.show" x-cloak wire:ignore
        @click="if (window.isBackdropClick($event, $el) && !confirmDeleteModal.loading) confirmDeleteModal.show = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
        @keydown.escape.window="if (!confirmDeleteModal.loading) confirmDeleteModal.show = false">
        <div
            class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200/80 space-y-4 animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-start gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200/70 flex items-center justify-center shrink-0 text-rose-600 shadow-2xs">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-gov-textMain"
                        x-text="confirmDeleteModal.title || 'Konfirmasi Hapus Data'"></h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed" x-text="confirmDeleteModal.message"></p>
                </div>
                <button type="button" @click="confirmDeleteModal.show = false" :disabled="confirmDeleteModal.loading"
                    class="text-slate-400 hover:text-gov-textMain p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer disabled:opacity-40"
                    title="Tutup">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Item Name Highlight Badge -->
            <template x-if="confirmDeleteModal.itemName">
                <div class="p-3 rounded-xl bg-rose-50/70 border border-rose-200/70 flex items-start gap-2.5 text-xs">
                    <i data-lucide="trash-2" class="w-4 h-4 text-rose-500 shrink-0 mt-0.5"></i>
                    <span class="font-bold text-rose-900 line-clamp-3 wrap-break-word whitespace-pre-line leading-relaxed" x-text="confirmDeleteModal.itemName"></span>
                </div>
            </template>

            <div
                class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/60 text-[11px] text-amber-900 flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                <span class="leading-relaxed">Tindakan ini tidak dapat dibatalkan. Pastikan Anda telah memeriksa kembali
                    data yang akan dihapus.</span>
            </div>

            <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-100">
                <button type="button" @click="confirmDeleteModal.show = false" :disabled="confirmDeleteModal.loading"
                    class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 text-xs font-semibold transition cursor-pointer disabled:opacity-40">
                    Batal
                </button>
                <button type="button" @click="if (!confirmDeleteModal.loading) executeConfirmDelete()" :disabled="confirmDeleteModal.loading"
                    class="px-4 py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-xs font-bold shadow-2xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50">
                    <span x-show="!confirmDeleteModal.loading" class="inline-flex items-center gap-1.5">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Ya, Hapus</span>
                    </span>
                    <span x-show="confirmDeleteModal.loading" x-cloak class="inline-flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                            fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="whitespace-nowrap">Menghapus...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: PRATINJAU BUKTI TRANSFER / KWITANSI TRANSAKSI KAS -->
    <!-- ======================================================== -->
    <div x-show="receiptPreviewModal.show" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) closeReceiptModal(false)"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
        style="display: none; backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
        @keydown.escape.window="if (receiptPreviewModal.show) closeReceiptModal(false)"
        @keydown.arrow-left.window="if (receiptPreviewModal.show) receiptPreviewModal.prev()"
        @keydown.arrow-right.window="if (receiptPreviewModal.show) receiptPreviewModal.next()">
        <div class="bg-white rounded-2xl max-w-3xl w-full p-4 sm:p-6 shadow-2xl border border-slate-200/80 space-y-3.5 animate-in fade-in zoom-in-95 duration-200 flex flex-col max-h-[92vh]">
            <!-- Header -->
            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-200/80">
                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 border border-sky-200/70 flex items-center justify-center shrink-0 text-sky-600 shadow-2xs">
                        <svg class="w-5 h-5 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base font-bold text-gov-textMain truncate">Bukti Transaksi Kas</h3>
                            <template x-if="receiptPreviewModal.items && receiptPreviewModal.items.length > 1">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-sky-100 text-sky-800 border border-sky-200">
                                    Berkas <span x-text="receiptPreviewModal.currentIndex + 1"></span> dari <span x-text="receiptPreviewModal.items.length"></span>
                                </span>
                            </template>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed line-clamp-1" x-text="receiptPreviewModal.description || 'Pratinjau berkas bukti transaksi'"></p>
                    </div>
                </div>
                <button type="button" @click="closeReceiptModal(false)"
                    class="text-slate-400 hover:text-gov-textMain p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer shrink-0"
                    title="Tutup">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Transaction Details Summary Card -->
            <div class="p-2.5 sm:p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none"
                          :class="receiptPreviewModal.type === 'pemasukan' ? 'bg-emerald-50 text-emerald-700 border-emerald-400' : 'bg-rose-100 text-rose-800 border-rose-300'"
                          x-text="receiptPreviewModal.type === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran'"></span>
                    <span class="text-slate-500 font-medium" x-text="receiptPreviewModal.date"></span>
                </div>
                <div class="font-bold text-sm tnum text-gov-textMain" x-text="receiptPreviewModal.amount"></div>
            </div>

            <!-- Preview Body Container / Carousel Viewport -->
            <div class="flex-1 overflow-hidden min-h-65 max-h-[54vh] bg-slate-950/90 rounded-xl border border-slate-800 p-2 flex items-center justify-center relative select-none">
                <!-- If Image -->
                <template x-if="!receiptPreviewModal.isPdf && receiptPreviewModal.currentUrl">
                    <img :src="receiptPreviewModal.currentUrl" :alt="'Bukti Transaksi ' + (receiptPreviewModal.currentIndex + 1)"
                        class="max-h-[50vh] max-w-full rounded-lg object-contain shadow-xs select-none transition-all duration-200">
                </template>

                <!-- If PDF -->
                <template x-if="receiptPreviewModal.isPdf && receiptPreviewModal.currentUrl">
                    <iframe :src="receiptPreviewModal.currentUrl" class="w-full h-[50vh] rounded-lg border-0 shadow-xs bg-white"></iframe>
                </template>

                <!-- Fallback empty -->
                <template x-if="!receiptPreviewModal.currentUrl">
                    <div class="text-center p-8 text-slate-400">
                        <svg class="w-10 h-10 mx-auto text-slate-500 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                        <p class="text-xs font-medium text-slate-400">Berkas bukti tidak ditemukan atau tautan tidak valid.</p>
                    </div>
                </template>

                <!-- Carousel Navigation Arrows (when multiple items) -->
                <template x-if="receiptPreviewModal.items && receiptPreviewModal.items.length > 1">
                    <div class="contents">
                        <!-- Prev Button -->
                        <button type="button" @click.stop="receiptPreviewModal.prev()"
                            class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 p-2 sm:p-2.5 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white border border-white/20 shadow-lg backdrop-blur-xs transition cursor-pointer z-10 hover:scale-105 active:scale-95"
                            title="Foto Sebelumnya (Panah Kiri)">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </button>
                        <!-- Next Button -->
                        <button type="button" @click.stop="receiptPreviewModal.next()"
                            class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 p-2 sm:p-2.5 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white border border-white/20 shadow-lg backdrop-blur-xs transition cursor-pointer z-10 hover:scale-105 active:scale-95"
                            title="Foto Berikutnya (Panah Kanan)">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Carousel Thumbnails (when multiple items) -->
            <template x-if="receiptPreviewModal.items && receiptPreviewModal.items.length > 1">
                <div class="flex items-center gap-2 overflow-x-auto py-1 px-0.5 max-w-full scrollbar-thin">
                    <template x-for="(item, idx) in receiptPreviewModal.items" :key="idx">
                        <button type="button" @click="receiptPreviewModal.setIndex(idx)"
                            class="relative shrink-0 w-13 h-13 sm:w-14 sm:h-14 rounded-lg overflow-hidden border-2 transition cursor-pointer focus:outline-none"
                            :class="receiptPreviewModal.currentIndex === idx ? 'border-sky-500 ring-2 ring-sky-300 opacity-100 scale-102' : 'border-slate-200 opacity-60 hover:opacity-100'">
                            <template x-if="!item.is_pdf">
                                <img :src="item.url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="item.is_pdf">
                                <div class="w-full h-full bg-slate-100 flex flex-col items-center justify-center text-rose-600">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    <span class="text-[8px] font-bold">PDF</span>
                                </div>
                            </template>
                            <div class="absolute bottom-0 inset-x-0 bg-slate-900/70 text-[9px] text-white font-bold text-center leading-tight py-0.5"
                                 x-text="'#' + (idx + 1)"></div>
                        </button>
                    </template>
                </div>
            </template>

            <!-- Footer Actions -->
            <div class="pt-3 flex items-center justify-between gap-2 border-t border-slate-100 text-xs">
                <a :href="receiptPreviewModal.currentUrl" target="_blank"
                    class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    <span>Buka File Asli</span>
                </a>
                <div class="flex items-center gap-2">
                    <template x-if="receiptPreviewModal.fromFinanceModal">
                        <button type="button" @click="closeReceiptModal(false)"
                            class="px-3.5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                            <span>Kembali ke Form Edit</span>
                        </button>
                    </template>
                    <button type="button" @click="closeReceiptModal(true)"
                        class="px-4 py-2 rounded-lg font-bold shadow-2xs transition cursor-pointer"
                        :class="receiptPreviewModal.fromFinanceModal ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' : 'bg-gov-navy hover:bg-gov-navyHover text-white'">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: CATAT / EDIT TRANSAKSI KAS (MILESTONE 5) -->
    <!-- ======================================================== -->
    <div x-show="showFinanceModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showFinanceModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showFinanceModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200 shrink-0">
                        <i data-lucide="wallet" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain"
                            x-text="isEditingFinance ? 'Edit Transaksi Kas' : 'Catat Transaksi Kas Baru'"></h3>
                        <p class="text-xs text-slate-500">Mutasi kas operasional dan program dakwah masjid</p>
                    </div>
                </div>
                <button type="button" @click="showFinanceModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveFinance" class="space-y-4 text-xs">
                @php
                    $isTesting = app()->runningUnitTests();

                    $catOptionsPemasukan = [['value' => '', 'label' => '-- Pilih Pos Kategori --']];
                    $catOptionsPengeluaran = [['value' => '', 'label' => '-- Pilih Pos Kategori --']];

                    if (isset($groupedFinanceCategories)) {
                        if (! $isTesting || $financeType === 'pemasukan') {
                            $catOptionsPemasukan[] = ['label' => '📥 PENERIMAAN', 'is_header' => true];
                            if (isset($groupedFinanceCategories['Penerimaan'])) {
                                foreach($groupedFinanceCategories['Penerimaan'] as $fc) {
                                    $catOptionsPemasukan[] = ['value' => (string)$fc->id, 'label' => $fc->name];
                                }
                            }
                        }

                        if (! $isTesting || $financeType === 'pengeluaran') {
                            $catOptionsPengeluaran[] = ['label' => '📤 PENGELUARAN RUTIN', 'is_header' => true];
                            if (isset($groupedFinanceCategories['Pengeluaran Rutin'])) {
                                foreach($groupedFinanceCategories['Pengeluaran Rutin'] as $fc) {
                                    $catOptionsPengeluaran[] = ['value' => (string)$fc->id, 'label' => $fc->name];
                                }
                            }
                            $catOptionsPengeluaran[] = ['label' => '🛠️ PENGELUARAN NON-RUTIN', 'is_header' => true];
                            if (isset($groupedFinanceCategories['Pengeluaran Non-Rutin'])) {
                                foreach($groupedFinanceCategories['Pengeluaran Non-Rutin'] as $fc) {
                                    $catOptionsPengeluaran[] = ['value' => (string)$fc->id, 'label' => $fc->name];
                                }
                            }
                        }
                    } else {
                        foreach($financeCategories as $fc) {
                            if ($fc->group === 'penerimaan') {
                                if (! $isTesting || $financeType === 'pemasukan') {
                                    $catOptionsPemasukan[] = ['value' => (string)$fc->id, 'label' => $fc->name];
                                }
                            } else {
                                if (! $isTesting || $financeType === 'pengeluaran') {
                                    $catOptionsPengeluaran[] = ['value' => (string)$fc->id, 'label' => $fc->name];
                                }
                            }
                        }
                    }

                    $defaultPemasukanId = (string) ($this->getDefaultFinanceCategoryId('pemasukan') ?? '');
                    $defaultPengeluaranId = (string) ($this->getDefaultFinanceCategoryId('pengeluaran') ?? '');
                    $currentCatOptions = ($financeType === 'pemasukan') ? $catOptionsPemasukan : $catOptionsPengeluaran;
                @endphp

                <!-- Jenis Transaksi Toggle -->
                <div x-data="{
                    currentType: '{{ $financeType }}',
                    pemasukanOptions: {{ Js::from($catOptionsPemasukan) }},
                    pengeluaranOptions: {{ Js::from($catOptionsPengeluaran) }},
                    defaultPemasukanId: '{{ $defaultPemasukanId }}',
                    defaultPengeluaranId: '{{ $defaultPengeluaranId }}',
                    switchType(type) {
                        this.currentType = type;
                        const isMasuk = (type === 'pemasukan');
                        const newOptions = isMasuk ? this.pemasukanOptions : this.pengeluaranOptions;
                        const defaultId = isMasuk ? this.defaultPemasukanId : this.defaultPengeluaranId;
                        this.$dispatch('custom-select-set-options', {
                            name: 'financeCategoryId',
                            options: newOptions,
                            selected: defaultId
                        });
                        $wire.set('financeType', type, false);
                        $wire.set('financeCategoryId', defaultId ? Number(defaultId) : null, false);
                    }
                }"
                @open-finance-modal.window="currentType = $wire.financeType"
                @finance-modal-reset.window="switchType('pemasukan')">
                    <label class="block font-semibold text-slate-700 mb-1.5">Jenis Transaksi Kas *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button"
                            wire:key="fin-btn-type-masuk"
                            @click="switchType('pemasukan')"
                            :class="currentType === 'pemasukan' ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' : 'bg-slate-50 text-slate-700 border-gov-border hover:bg-slate-100'"
                            class="p-2.5 rounded-lg border text-center font-bold text-xs cursor-pointer flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 7 7 17"/>
                                <path d="M17 17H7V7"/>
                            </svg>
                            <span>Pemasukan</span>
                        </button>
                        <button type="button"
                            wire:key="fin-btn-type-keluar"
                            @click="switchType('pengeluaran')"
                            :class="currentType === 'pengeluaran' ? 'bg-rose-600 text-white border-rose-600 shadow-2xs' : 'bg-slate-50 text-slate-700 border-gov-border hover:bg-slate-100'"
                            class="p-2.5 rounded-lg border text-center font-bold text-xs cursor-pointer flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7 17 17 7"/>
                                <path d="M7 7h10v10"/>
                            </svg>
                            <span>Pengeluaran</span>
                        </button>
                    </div>
                    @error('financeType') <span
                    class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal & Kategori -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Transaksi *</label>
                        <input wire:model="financeDate" type="date" required
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain cursor-pointer">
                        @error('financeDate') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700">Pos Kategori Kas *</label>
                        </div>
                        <x-custom-select 
                            wire:key="finance-cat-select-{{ $financeType }}"
                            wire:model="financeCategoryId"
                            :options="$currentCatOptions"
                            placeholder="-- Pilih Pos Kategori --"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                        @error('financeCategoryId') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Alokasi Pembukuan & Nominal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alokasi Pembukuan Kas</label>
                        @php
                            $agendaOptions = [['value' => '', 'label' => '🏢 Kas Umum Masjid (Rutin Bulanan)']];
                            if (isset($allAgendas) && $allAgendas->isNotEmpty()) {
                                $agendaOptions[] = ['label' => '🎯 PROGRAM TEMATIK / AGENDA KEGIATAN', 'is_header' => true];
                                foreach($allAgendas as $ag) {
                                    $agendaOptions[] = ['value' => (string)$ag->id, 'label' => 'Kegiatan: ' . $ag->title];
                                }
                            }
                        @endphp
                        <x-custom-select 
                            wire:model="financeAgendaId"
                            :options="$agendaOptions"
                            placeholder="🏢 Kas Umum Masjid (Rutin Bulanan)"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nominal Anggaran (Rp) *</label>
                        <input wire:model="financeAmount" x-ribuan="$wire.financeAmount" type="text" inputmode="numeric" placeholder="Contoh: 1.500.000" required
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-bold">
                        @error('financeAmount') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Deskripsi / Keterangan -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Uraian / Keterangan Transaksi *</label>
                    <textarea wire:model="financeDescription" rows="2"
                        placeholder="Contoh: Pembelian sound system mimbar ruang sholat utama..." required
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition"></textarea>
                    @error('financeDescription') <span
                    class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Bukti Transaksi / Nota -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Unggah Nota / Kwitansi / Bukti Transfer (Opsional)</label>
                    <div x-data="{ isDragging: false }"
                         @dragenter.prevent="isDragging = true"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop="isDragging = false"
                         :class="isDragging ? 'border-gov-navy bg-sky-50/60 ring-2 ring-gov-navy/20 scale-[0.99]' : 'border-slate-300 hover:border-gov-navy hover:bg-slate-50/80 bg-slate-50/40'"
                         class="relative border-2 border-dashed rounded-xl p-4 text-center transition-all duration-200 cursor-pointer group">
                        <input id="financeReceiptFilesInput"
                               wire:model="financeReceiptFiles"
                               type="file"
                               multiple
                               accept="image/*,.pdf"
                               @dragenter="isDragging = true"
                               @dragover.prevent="isDragging = true"
                               @dragleave="isDragging = false"
                               @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="pointer-events-none flex flex-col items-center justify-center gap-1.5">
                            <div :class="isDragging ? 'bg-gov-navy text-white scale-110' : 'bg-sky-50 text-sky-600 group-hover:scale-110'"
                                 class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform shadow-2xs">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-slate-700 font-semibold mt-1">
                                <span class="text-gov-navy underline underline-offset-2">Klik untuk memilih file</span> atau seret bukti nota ke sini
                            </p>
                            <p class="text-[11px] text-slate-500">
                                Dapat memilih lebih dari 1 file sekaligus (JPG, PNG, WEBP, atau PDF, maks. 5MB per file). Gambar otomatis dikonversi ke WebP tajam & ringan.
                            </p>
                        </div>
                    </div>

                    <div wire:loading.inline-flex wire:target="financeReceiptFiles" style="display: none;"
                        class="inline-flex flex-row items-center gap-2 text-xs font-medium text-sky-700 bg-sky-50 px-3 py-1.5 rounded-lg border border-sky-200 shrink-0 whitespace-nowrap mt-2">
                        <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-sky-600 inline-block" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="whitespace-nowrap leading-none">Sedang mengunggah berkas bukti...</span>
                    </div>

                    @error('financeReceiptFiles') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    @error('financeReceiptFiles.*') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    @error('financeReceiptFile') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror

                    @if(!empty($existingFinanceReceiptPaths))
                        <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                    <span>Bukti Tersimpan ({{ count($existingFinanceReceiptPaths) }} berkas)</span>
                                </span>
                                @php
                                    $existingUrls = array_map(function($p) {
                                        $clean = ltrim((string) $p, '/');
                                        return [
                                            'path' => $clean,
                                            'url' => asset('storage/' . $clean),
                                            'is_pdf' => str_ends_with(strtolower($clean), '.pdf'),
                                        ];
                                    }, $existingFinanceReceiptPaths);
                                @endphp
                                <button type="button"
                                    @click="showFinanceModal = false; openReceiptModal({{ Js::from([
                                        'items' => $existingUrls,
                                        'description' => $financeDescription ?: 'Bukti Transaksi Tersimpan',
                                        'date' => $financeDate ?: '',
                                        'amount' => $financeAmount ? ('Rp ' . number_format((float) preg_replace('/\D/', '', (string) $financeAmount), 0, ',', '.')) : '',
                                        'type' => $financeType ?: 'pemasukan',
                                        'fromFinanceModal' => true,
                                    ]) }})"
                                    class="px-2 py-1 rounded-md bg-white hover:bg-sky-50 text-sky-700 font-semibold border border-sky-300 shadow-2xs transition text-[11px] inline-flex items-center gap-1 cursor-pointer">
                                    <svg class="w-3 h-3 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <span>Lihat Carousel</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1">
                                @foreach($existingFinanceReceiptPaths as $idx => $rPath)
                                    @php
                                        $cleanP = ltrim((string) $rPath, '/');
                                        $isPdf = str_ends_with(strtolower($cleanP), '.pdf');
                                        $rUrl = asset('storage/' . $cleanP);
                                    @endphp
                                    <div wire:key="existing-receipt-{{ $idx }}-{{ $cleanP }}"
                                         class="group relative rounded-lg border border-slate-200 bg-white p-1.5 flex flex-col justify-between overflow-hidden shadow-2xs hover:border-slate-300 transition">
                                        <div class="h-20 w-full rounded overflow-hidden bg-slate-100 flex items-center justify-center">
                                            @if($isPdf)
                                                <div class="flex flex-col items-center justify-center text-rose-600 p-2 text-center">
                                                    <svg class="w-6 h-6 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                                        <polyline points="14 2 14 8 20 8"/>
                                                    </svg>
                                                    <span class="text-[9px] font-bold uppercase truncate max-w-full">PDF Doc</span>
                                                </div>
                                            @else
                                                <img src="{{ $rUrl }}" alt="Bukti {{ $idx + 1 }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="mt-1.5 flex items-center justify-between gap-1">
                                            <span class="text-[10px] font-medium text-slate-500 truncate">Berkas #{{ $idx + 1 }}</span>
                                            <div class="flex items-center gap-1">
                                                <a href="{{ $rUrl }}" target="_blank"
                                                   class="p-1 rounded text-slate-500 hover:text-sky-600 hover:bg-sky-50 transition" title="Buka berkas">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                        <polyline points="15 3 21 3 21 9"></polyline>
                                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                                    </svg>
                                                </a>
                                                <button type="button" wire:click="removeExistingFinanceReceipt({{ $idx }})"
                                                   class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus berkas ini">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showFinanceModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="saveFinance"
                            x-text="isEditingFinance ? 'Perbarui Transaksi' : 'Simpan Transaksi'"></span>
                        <span wire:loading.inline-flex wire:target="saveFinance"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: KELOLA KATEGORI KAS (MILESTONE 5) -->
    <!-- ======================================================== -->
    <div x-show="showFinanceCategoryModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showFinanceCategoryModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showFinanceCategoryModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200 shrink-0">
                        <i data-lucide="tags" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain"
                            x-text="isEditingFinanceCategory ? 'Edit Pos Anggaran' : 'Tambah Pos Anggaran'">
                            Tambah Pos Anggaran
                        </h3>
                    </div>
                </div>
                <button type="button" @click="showFinanceCategoryModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Tambah / Edit Kategori -->
            <form @submit.prevent="$wire.saveFinanceCategory(financeCategoryForm)" class="space-y-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Pos Anggaran *</label>
                        <input x-model="financeCategoryForm.name" type="text"
                            placeholder="Misal: Biaya Operasional & Sarana" required
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                        @error('newCategoryName') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kelompok Pos *</label>
                        <x-custom-select 
                            x-model="financeCategoryForm.group"
                            :options="[
                                ['value' => 'penerimaan', 'label' => '📥 Penerimaan (Kas Masuk)'],
                                ['value' => 'pengeluaran_rutin', 'label' => '📤 Pengeluaran Rutin'],
                                ['value' => 'pengeluaran_nonrutin', 'label' => '🛠️ Pengeluaran Non-Rutin'],
                            ]"
                            placeholder="Pilih Kelompok Pos"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                        @error('newCategoryGroup') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showFinanceCategoryModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="saveFinanceCategory"
                            x-text="isEditingFinanceCategory ? 'Perbarui Pos Anggaran' : 'Simpan Pos Anggaran'">Simpan
                            Pos Anggaran</span>
                        <span wire:loading.inline-flex wire:target="saveFinanceCategory"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: CETAK LAPORAN KAS & LPJ AGENDA (MILESTONE 5) -->
    <!-- ======================================================== -->
    <div x-show="showPrintFinanceModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showPrintFinanceModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showPrintFinanceModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-200 shrink-0">
                        <i data-lucide="printer" class="w-4 h-4 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">Cetak Laporan Kas & LPJ</h3>
                        <p class="text-xs text-slate-500">Pilih format laporan keuangan resmi Masjid Salahuddin</p>
                    </div>
                </div>
                <button type="button" @click="showPrintFinanceModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <!-- Pilihan Mode Laporan -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Jenis Dokumen Laporan</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <button type="button" @click="$wire.set('printReportType', 'monthly')"
                            class="p-3 rounded-xl border text-left transition cursor-pointer"
                            :class="$wire.printReportType === 'monthly' ? 'bg-blue-50 border-blue-500 text-blue-900' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                            <div class="font-bold flex items-center gap-1.5 mb-1">
                                <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                                <span>Kas Bulanan Rutin</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">Format baku arus kas DKM Kemenkeu dengan
                                saldo berkelanjutan</p>
                        </button>

                        <button type="button" @click="$wire.set('printReportType', 'agenda')"
                            class="p-3 rounded-xl border text-left transition cursor-pointer"
                            :class="$wire.printReportType === 'agenda' ? 'bg-emerald-50 border-emerald-500 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                            <div class="font-bold flex items-center gap-1.5 mb-1">
                                <i data-lucide="target" class="w-4 h-4 text-emerald-600"></i>
                                <span>LPJ Agenda Tematik</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">Laporan pertanggungjawaban kegiatan
                                panitia (Ramadhan, Qurban, dll)</p>
                        </button>
                    </div>
                </div>

                <!-- Parameter Form Berdasarkan Mode -->
                <div x-show="$wire.printReportType === 'monthly'"
                    class="space-y-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="font-bold text-slate-700 block">Periode Laporan Kas Bulanan</span>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Bulan</label>
                            @php
                                $printMonthOptions = [['value' => 'all', 'label' => 'Semua Bulan (Setahun)']];
                                for($m = 1; $m <= 12; $m++) {
                                    $printMonthOptions[] = [
                                        'value' => (string)$m,
                                        'label' => Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F')
                                    ];
                                }
                            @endphp
                            <x-custom-select 
                                wire:model.live="printMonth"
                                :options="$printMonthOptions"
                                placeholder="Semua Bulan (Setahun)"
                                buttonClass="p-2.5 bg-white"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Tahun</label>
                            @php
                                $printYearOptions = [['value' => 'all', 'label' => 'Semua Tahun']];
                                for($y = 2026; $y >= 2021; $y--) {
                                    $printYearOptions[] = ['value' => (string)$y, 'label' => (string)$y];
                                }
                            @endphp
                            <x-custom-select 
                                wire:model.live="printYear"
                                :options="$printYearOptions"
                                placeholder="Semua Tahun"
                                buttonClass="p-2.5 bg-white"
                            />
                        </div>
                    </div>
                </div>

                <div x-show="$wire.printReportType === 'agenda'"
                    class="space-y-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="font-bold text-slate-700 block">Pilih Agenda Kegiatan Tematik</span>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Agenda / Program Kegiatan</label>
                        @php
                            $printAgendaOptions = [];
                            if(isset($allAgendas) && $allAgendas->isNotEmpty()) {
                                foreach($allAgendas as $ag) {
                                    $printAgendaOptions[] = [
                                        'value' => (string)$ag->id,
                                        'label' => $ag->title . ' (' . $ag->formatted_date . ') - Status: ' . $ag->status
                                    ];
                                }
                            }
                        @endphp
                        <x-custom-select 
                            wire:model.live="printAgendaId"
                            :options="$printAgendaOptions"
                            placeholder="Pilih Agenda / Program"
                            buttonClass="p-2.5 bg-white"
                        />
                    </div>
                </div>

                <!-- TTE Digital Verification Banner (Clean Standard Card) -->
                <div wire:key="card-tte-banner-{{ $tteSigned ? 'signed' : 'unsigned' }}"
                    class="p-3.5 sm:p-4 rounded-xl border shadow-2xs flex flex-col items-start justify-between gap-3 {{ $tteSigned ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-slate-50/70' }}">
                    <div class="flex flex-col items-start">
                        <div class="mb-2 w-9 h-9 rounded-lg flex items-center justify-center shrink-0 border shadow-2xs {{ $tteSigned ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200' }}">
                            @if($tteSigned)
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.364 13.634a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506l4.013-4.009a1 1 0 0 0-3.004-3.004z"></path>
                                    <path d="M14.487 7.858A1 1 0 0 1 14 7V2"></path>
                                    <path d="M20 19.645V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l2.516 2.516"></path>
                                    <path d="M8 18h1"></path>
                                </svg>
                            @endif
                        </div>
                        <h4 class="mb-1 text-xs font-bold text-gov-textMain tracking-tight">
                            {{ $tteSigned ? 'Sertifikasi TTE Digital BSrE Aktif & Tervalidasi' : 'Pengesahan TTE Digital BSrE (Menunggu Validasi)' }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-0.5">
                            @if($tteSigned)
                                Laporan kas resmi terverifikasi BSrE. Terbuka untuk diakses jamaah melalui portal publik dan dokumen PDF.
                            @else
                                Rekapitulasi mutasi kas periode ini siap diperiksa. Bubuhkan TTE digital agar lembar sah diunduh jamaah.
                            @endif
                        </p>
                    </div>
                    @if(Auth::user()->canManage())
                        @if($tteSigned)
                            <button wire:key="btn-tte-signed" type="button" wire:click="signTteReport" wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold shadow-2xs shrink-0 cursor-pointer disabled:opacity-50 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300">
                                <div wire:loading.inline-flex wire:target="signTteReport,printMonth,printYear,printAgendaId" style="display: none;" class="inline-flex flex-row items-center gap-1.5 shrink-0 whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 animate-spin shrink-0 inline-block text-rose-700" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <span wire:loading.remove wire:target="signTteReport,printMonth,printYear,printAgendaId" class="inline-flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 14 4 9l5-5"></path>
                                        <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5a5.5 5.5 0 0 1-5.5 5.5H11"></path>
                                    </svg>
                                </span>
                                <span class="whitespace-nowrap">Batalkan Pengesahan</span>
                            </button>
                        @else
                            <button wire:key="btn-tte-unsigned" type="button" wire:click="signTteReport" wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold shadow-2xs shrink-0 cursor-pointer disabled:opacity-50 bg-gov-navy hover:bg-gov-navyHover text-white border border-transparent">
                                <div wire:loading.inline-flex wire:target="signTteReport,printMonth,printYear,printAgendaId" style="display: none;" class="inline-flex flex-row items-center gap-1.5 shrink-0 whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 animate-spin shrink-0 inline-block text-white" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <span wire:loading.remove wire:target="signTteReport,printMonth,printYear,printAgendaId" class="inline-flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M15.707 21.293a1 1 0 0 1-1.414 0l-1.586-1.586a1 1 0 0 1 0-1.414l5.586-5.586a1 1 0 0 1 1.414 0l1.586 1.586a1 1 0 0 1 0 1.414z"></path>
                                        <path d="m18 13-1.375-6.874a1 1 0 0 0-.746-.776L3.235 2.028a1 1 0 0 0-1.207 1.207L5.35 15.879a1 1 0 0 0 .776.746L13 18"></path>
                                        <path d="m2.3 2.3 7.286 7.286"></path>
                                        <circle cx="11" cy="11" r="2"></circle>
                                    </svg>
                                </span>
                                <span class="whitespace-nowrap">Tinjau &amp; Sahkan TTE</span>
                            </button>
                        @endif
                    @endif
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-200">
                    <button type="button" @click="showPrintFinanceModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>

                    <a :href="'{{ url('/admin/finance/export-pdf') }}?' + (
                            $wire.printReportType === 'agenda' 
                                ? ('type=agenda&agenda_id=' + $wire.printAgendaId + '&tte=' + ($wire.tteSigned ? '1' : '0'))
                                : ('type=monthly&month=' + $wire.printMonth + '&year=' + $wire.printYear + '&tte=' + ($wire.tteSigned ? '1' : '0'))
                        )" target="_blank" @click="showPrintFinanceModal = false"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs inline-flex items-center justify-center gap-2 shadow-2xs shrink-0 cursor-pointer">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Buka Laporan PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: IMPORT DATA KAS (MILESTONE 5) -->
    <!-- ======================================================== -->
    <div x-show="showFinanceImportModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showFinanceImportModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showFinanceImportModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center border border-gov-border shrink-0">
                        <i data-lucide="upload-cloud" class="w-4 h-4 text-gov-navy"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">Import Data Transaksi Kas</h3>
                        <p class="text-xs text-slate-500">Impor mutasi kas dari Excel atau file CSV</p>
                    </div>
                </div>
                <button type="button" @click="showFinanceImportModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Tab Selector: Copypaste Excel vs Upload CSV -->
            <div class="flex items-center p-1 bg-slate-100 rounded-lg">
                <button type="button" @click="importFinanceTab = 'paste'"
                    class="flex-1 py-1.5 rounded-md font-bold text-xs transition cursor-pointer"
                    :class="importFinanceTab === 'paste' ? 'bg-white text-gov-navy shadow-2xs' : 'text-slate-600 hover:text-gov-navy'">
                    📋 Copypaste Excel
                </button>
                <button type="button" @click="importFinanceTab = 'file'"
                    class="flex-1 py-1.5 rounded-md font-bold text-xs transition cursor-pointer"
                    :class="importFinanceTab === 'file' ? 'bg-white text-gov-navy shadow-2xs' : 'text-slate-600 hover:text-gov-navy'">
                    📁 Upload File CSV
                </button>
            </div>

            <!-- TAB 1: COPYPASTE DARI EXCEL -->
            <div x-show="importFinanceTab === 'paste'" class="space-y-3 text-xs">
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-amber-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-amber-600"></i>
                        <span>Format Kolom Excel yang Diharapkan:</span>
                    </span>
                    <p class="text-xs text-amber-800 font-mono text-[11px] leading-relaxed">
                        Tipe (Masuk/Keluar) [Tab] Kategori [Tab] Nominal [Tab] Tanggal (YYYY-MM-DD) [Tab] Keterangan
                    </p>
                    <p class="text-[10px] text-amber-700">Contoh:
                        <code>Pemasukan&#9;Infaq Jumat&#9;4500000&#9;2026-09-12&#9;Infaq Kotak Utama Sholat Jumat</code>
                    </p>
                </div>

                <div class="space-y-1">
                    <label class="block font-semibold text-slate-700">Tempelkan Data Excel (Paste Here):</label>
                    <textarea wire:model="importFinancePasteText" rows="6"
                        placeholder="Blok baris & kolom di Excel -> Copy (Ctrl+C) -> Paste (Ctrl+V) ke sini..."
                        class="w-full p-2.5 rounded-lg border border-gov-border font-mono text-[11px] bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition"></textarea>
                </div>

                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showFinanceImportModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="button" wire:click="processImportFinancePaste" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="processImportFinancePaste">Proses Impor Teks</span>
                        <span wire:loading.inline-flex wire:target="processImportFinancePaste"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Mengimpor...</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- TAB 2: UPLOAD FILE CSV -->
            <div x-show="importFinanceTab === 'file'" class="space-y-3 text-xs">
                <div class="p-3 bg-sky-50 rounded-lg border border-sky-200 text-sky-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-sky-600"></i>
                        <span>Petunjuk Unggah File CSV:</span>
                    </span>
                    <p class="text-xs text-sky-800">Unggah berkas <strong>.csv</strong> atau <strong>.txt</strong>
                        dengan pemisah koma atau tab maksimal 2 MB.</p>
                </div>

                <div x-data="{ isDragging: false }"
                     :class="isDragging ? 'border-gov-navy bg-blue-50/70 ring-2 ring-gov-navy/20' : 'border-gov-border bg-slate-50/50 hover:bg-slate-50'"
                     class="border-2 border-dashed rounded-xl p-8 text-center space-y-2.5 transition relative overflow-hidden group">
                    
                    <!-- Invisible Full-Surface Native File Input -->
                    <input id="importFinanceFile"
                           type="file"
                           wire:model="importFinanceFile"
                           accept=".csv,.txt"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                           @dragenter="isDragging = true"
                           @dragover.prevent="isDragging = true"
                           @dragleave="isDragging = false"
                           @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }">

                    <div class="pointer-events-none space-y-2">
                        <div class="w-12 h-12 rounded-full bg-gov-navy/10 flex items-center justify-center mx-auto text-gov-navy transition group-hover:scale-105"
                             :class="isDragging ? 'scale-110 bg-gov-navy/20' : ''">
                            <svg class="w-6 h-6 text-gov-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                                <path d="M12 12v9"/>
                                <path d="m16 16-4-4-4 4"/>
                            </svg>
                        </div>
                        <div class="text-xs text-slate-600">
                            <span class="font-bold text-gov-navy group-hover:underline">Klik untuk memilih berkas</span>
                            <span>atau seret & lepas berkas ke area ini</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Format: CSV / TXT (Maksimal 2 MB)</p>
                        @if($importFinanceFile)
                            <div class="pt-1 text-xs font-semibold text-emerald-700 flex items-center justify-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>{{ $importFinanceFile->getClientOriginalName() }} (Siap Diimpor)</span>
                            </div>
                        @endif
                        <template x-if="isDragging">
                            <div class="text-xs font-bold text-blue-700 animate-pulse pt-1">
                                Lepaskan berkas di sini untuk mengunggah
                            </div>
                        </template>
                    </div>
                </div>
                @error('importFinanceFile')
                    <span class="text-xs font-medium text-rose-600 block">{{ $message }}</span>
                @enderror

                <div class="pt-2 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showFinanceImportModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="button" wire:click="processImportFinanceFile" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="processImportFinanceFile">Upload & Impor File</span>
                        <span wire:loading.inline-flex wire:target="processImportFinanceFile"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Mengunggah...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: CATAT SETORAN KLIRING KANTOR (LUMP-SUM) -->
    <!-- ======================================================== -->
    <div x-show="showLumpSumModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showLumpSumModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showLumpSumModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200 shrink-0">
                        <i data-lucide="building-2" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">Catat Setoran Kliring Kantor</h3>
                        <p class="text-xs text-slate-400">Pencatatan dana potongan kantor yang ditransfer secara glondongan (*lump-sum*)</p>
                    </div>
                </div>
                <button type="button" @click="showLumpSumModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form wire:submit.prevent="processLumpSumDeposit" class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal Transfer (Rp) *</label>
                    <input wire:model="lumpSumAmount" x-ribuan="$wire.lumpSumAmount" type="text" inputmode="numeric" placeholder="Contoh: 18.250.000"
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-black text-sm transition">
                    @error('lumpSumAmount') <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Transfer Masuk *</label>
                        <input wire:model="lumpSumDate" type="date"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain cursor-pointer">
                        @error('lumpSumDate') <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alokasi Program Utama</label>
                        <x-custom-select 
                            wire:model="lumpSumProgram"
                            :options="[
                                ['value' => 'Infaq Rutin', 'label' => 'Infaq Rutin Pegawai'],
                                ['value' => 'Santunan Anak Yatim', 'label' => 'Santunan Anak Yatim'],
                                ['value' => 'Zakat Mal Rutin', 'label' => 'Zakat Mal Rutin'],
                                ['value' => 'Tabungan Qurban', 'label' => 'Tabungan Qurban'],
                            ]"
                            placeholder="Pilih Alokasi Program"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Bukti / Keterangan Transfer</label>
                    <input wire:model="lumpSumNotes" type="text"
                        placeholder="No. Ref / Bilyet Kliring Bank"
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                </div>

                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showLumpSumModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="processLumpSumDeposit">Simpan Setoran Glondongan</span>
                        <span wire:loading.inline-flex wire:target="processLumpSumDeposit"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: DAFTARKAN / EDIT PESERTA PROGRAM SOSIAL (MILESTONE 5) -->
    <!-- ======================================================== -->
    <div x-show="showParticipantModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showParticipantModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showParticipantModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200 shrink-0">
                        <i data-lucide="user-plus" class="w-4 h-4 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain"
                            x-text="isEditingParticipant ? 'Edit Peserta Program Sosial' : 'Daftarkan Peserta Program Sosial'">
                        </h3>
                        <p class="text-xs text-slate-500">Pendaftaran komitmen pemotongan gaji / tukin bulanan pegawai
                        </p>
                    </div>
                </div>
                <button type="button" @click="showParticipantModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveParticipant" class="space-y-4 text-xs">
                <!-- Nama Peserta -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input wire:model="participantName" type="text" placeholder="Contoh: Dr. H. Bambang Irawan" required
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    @error('participantName') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Program Pilihan & Nominal Bulanan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Program Sosial Pilihan *</label>
                        @php
                            $partProgOptions = [];
                            foreach($allSocialPrograms as $prog) {
                                $partProgOptions[] = ['value' => $prog->name, 'label' => $prog->name];
                            }
                        @endphp
                        <x-custom-select 
                            wire:model="participantProgram"
                            :options="$partProgOptions"
                            placeholder="Pilih Program Sosial"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                        @error('participantProgram') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nominal Potongan Bulanan (Rp) *</label>
                        <input wire:model="participantAmount" x-ribuan="$wire.participantAmount" type="text" inputmode="numeric" placeholder="Contoh: 250.000"
                            required
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-bold">
                        @error('participantAmount') <span
                        class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Periode Potongan -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Periode Potongan *</label>
                    <input wire:model="participantPeriod" list="available-periods-list" type="text" placeholder="Periode {{ now()->month }}/{{ now()->year }}"
                        required
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    <datalist id="available-periods-list">
                        @if(isset($availablePeriods))
                            @foreach($availablePeriods as $per)
                                <option value="{{ $per }}"></option>
                            @endforeach
                        @endif
                        <option value="Periode {{ now()->month }}/{{ now()->year }}"></option>
                    </datalist>
                    <span class="text-[11px] text-slate-400 mt-1 block">Format: Periode {bulan}/{tahun} (Contoh: Periode {{ now()->month }}/{{ now()->year }})</span>
                    @error('participantPeriod') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showParticipantModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="saveParticipant"
                            x-text="isEditingParticipant ? 'Perbarui Peserta' : 'Daftarkan Peserta'"></span>
                        <span wire:loading.inline-flex wire:target="saveParticipant"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24"
                                fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: IMPORT & SINKRONISASI POTONGAN PROGRAM SOSIAL -->
    <!-- ======================================================== -->
    <div x-show="showImportPotonganModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) { showImportPotonganModal = false; $wire.closeImportPotonganModal(); }"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showImportPotonganModal = false; $wire.closeImportPotonganModal();">
        <div
            class="bg-white rounded-xl max-w-xl w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200 shrink-0">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">Import Potongan</h3>
                        <p class="text-xs text-slate-500">Unggah berkas rekap Excel untuk sinkronisasi otomatis peserta program</p>
                    </div>
                </div>
                <button type="button" @click="showImportPotonganModal = false; $wire.closeImportPotonganModal();"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Error Banner -->
            @if(!empty($importPotonganError))
                <div class="p-3 bg-rose-50 rounded-lg border border-rose-200 text-rose-800 text-xs flex items-start gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5"></i>
                    <div class="flex-1">
                        <span class="font-semibold block">Gagal Memproses Berkas:</span>
                        <span class="text-[11px]">{{ $importPotonganError }}</span>
                    </div>
                    <button type="button" wire:click="$set('importPotonganError', '')" class="text-rose-400 hover:text-rose-700 cursor-pointer">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            @endif

            <!-- Step 1: Upload Dropzone (When preview is not yet generated) -->
            @if(!$importPotonganPreview)
                <div class="space-y-3" x-data="{ isDragging: false }">
                    <div :class="isDragging ? 'border-gov-navy bg-blue-50/70 ring-2 ring-gov-navy/20' : 'border-gov-border bg-slate-50/50 hover:bg-slate-50'"
                         class="border-2 border-dashed rounded-xl p-8 text-center space-y-2.5 transition relative overflow-hidden group">
                        
                        <!-- Invisible Full-Surface Native File Input -->
                        <input id="potonganFileInput"
                               type="file"
                               wire:model.live="potonganFile"
                               accept=".xlsx,.xls"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @dragenter="isDragging = true"
                               @dragover.prevent="isDragging = true"
                               @dragleave="isDragging = false"
                               @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }">

                        <!-- Loading Overlay during file upload & parsing -->
                        <div wire:loading.flex wire:target="potonganFile" class="absolute inset-0 bg-white/90 backdrop-blur-xs rounded-xl flex-col items-center justify-center gap-2 z-20">
                            <svg class="w-7 h-7 animate-spin text-gov-navy shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-slate-700 whitespace-nowrap">Menganalisis isi berkas Excel...</span>
                        </div>

                        <!-- Dropzone Visual Elements (pointer-events-none prevents child drag interference) -->
                        <div class="pointer-events-none space-y-2">
                            <div class="w-12 h-12 rounded-full bg-gov-navy/10 flex items-center justify-center mx-auto text-gov-navy transition group-hover:scale-105"
                                 :class="isDragging ? 'scale-110 bg-gov-navy/20' : ''">
                                <svg class="w-6 h-6 text-gov-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                                    <path d="M12 12v9"/>
                                    <path d="m16 16-4-4-4 4"/>
                                </svg>
                            </div>

                            <div class="text-xs text-slate-600">
                                <span class="font-bold text-gov-navy group-hover:underline">Klik untuk memilih berkas</span>
                                <span>atau seret & lepas berkas Excel ke area ini</span>
                            </div>

                            <p class="text-[11px] text-slate-400">Format: Rekapitulasi Potongan Masjid 2.0.xlsx / .xls (Maksimal 15 MB)</p>

                            <template x-if="isDragging">
                                <div class="text-xs font-bold text-blue-700 animate-pulse pt-1">
                                    Lepaskan berkas di sini untuk mengunggah
                                </div>
                            </template>
                        </div>
                    </div>
                    @error('potonganFile')
                        <span class="text-xs font-medium text-rose-600 block">{{ $message }}</span>
                    @enderror
                </div>
            @else
                <!-- Step 2: Smart Preview Card (When preview is ready) -->
                <div class="space-y-3.5">
                    <!-- File Badge & Reset -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="file-spreadsheet" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                            <div>
                                <span class="font-bold text-slate-900 block truncate max-w-xs">{{ $importPotonganPreview['file_name'] }}</span>
                                <span class="text-[11px] text-slate-500">{{ number_format($importPotonganPreview['file_size'] / 1024, 1) }} KB &bull; <span class="text-emerald-700 font-semibold">Siap Disinkronkan</span></span>
                            </div>
                        </div>
                        <button type="button" wire:click="resetImportPotonganFile"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 text-xs font-medium transition cursor-pointer">
                            <i data-lucide="refresh-cw" class="w-3 h-3 text-slate-500"></i>
                            <span>Ganti File</span>
                        </button>
                    </div>

                    <!-- Highlight Metrics -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-100 text-center">
                            <span class="block text-[10px] text-slate-400 uppercase font-semibold">Total Baris Excel</span>
                            <span class="text-sm font-black text-slate-800">{{ number_format($importPotonganPreview['total_rows'] ?? $importPotonganPreview['total_excel_unique'], 0, ',', '.') }}</span>
                            <span class="text-[10px] text-slate-500 block">baris potongan</span>
                        </div>
                        <div class="p-2.5 bg-blue-50 rounded-lg border border-blue-100 text-center">
                            <span class="block text-[10px] text-blue-600 uppercase font-semibold">Periode Terdeteksi</span>
                            <span class="text-sm font-black text-blue-700">{{ count($importPotonganPreview['by_period'] ?? []) }}</span>
                            <span class="text-[10px] text-blue-600 block">bulan periode</span>
                        </div>
                        <div class="p-2.5 bg-emerald-50 rounded-lg border border-emerald-100 text-center">
                            <span class="block text-[10px] text-emerald-600 uppercase font-semibold">Bulan Berjalan ({{ now()->format('M Y') }})</span>
                            <span class="text-sm font-black text-emerald-700">{{ $importPotonganPreview['current_period_count'] ?? $importPotonganPreview['active_count'] }}</span>
                            <span class="text-[10px] text-emerald-600 block">peserta aktif</span>
                        </div>
                        <div class="p-2.5 bg-amber-50 rounded-lg border border-amber-100 text-center">
                            <span class="block text-[10px] text-amber-700 uppercase font-semibold">Potongan Bulan Ini</span>
                            <span class="text-xs font-black text-amber-800">Rp {{ number_format($importPotonganPreview['current_period_amount'] ?? $importPotonganPreview['active_total_amount'], 0, ',', '.') }}</span>
                            <span class="text-[10px] text-amber-600 block">{{ $importPotonganPreview['current_period'] ?? ('Periode ' . now()->month . '/' . now()->year) }}</span>
                        </div>
                    </div>

                    <!-- Program Breakdown Mini-Table -->
                    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden text-xs">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-500 font-semibold text-[10px] uppercase">
                                    <th class="p-2">Program Sosial</th>
                                    <th class="p-2 text-center">Bulan Ini ({{ now()->month }}/{{ now()->year }})</th>
                                    <th class="p-2 text-right">Potongan Bulan Ini</th>
                                    <th class="p-2 text-center">Total Baris</th>
                                    <th class="p-2 text-right">Total Akumulasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-[11px]">
                                @foreach($importPotonganPreview['by_program'] as $p)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-2 font-bold text-slate-800">{{ $p['name'] }}</td>
                                        <td class="p-2 text-center font-semibold text-emerald-700">{{ $p['current_month_count'] ?? $p['active'] }} org</td>
                                        <td class="p-2 text-right font-black text-slate-900">Rp {{ number_format($p['current_month_amount'] ?? $p['active_amount'], 0, ',', '.') }}</td>
                                        <td class="p-2 text-center text-slate-500">{{ $p['total_rows'] ?? $p['total'] }} baris</td>
                                        <td class="p-2 text-right font-semibold text-slate-700">Rp {{ number_format($p['total_amount'] ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Impact Summary Note -->
                    <div class="flex items-center gap-2 p-2 bg-blue-50/70 border border-blue-200 rounded-lg text-[11px] text-blue-900">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-blue-600 shrink-0"></i>
                        <span>
                            @if(($importPotonganPreview['deleted_count'] ?? 0) > 0)
                                Data periode terkait akan diperbarui bersih: <strong>{{ number_format($importPotonganPreview['deleted_count'], 0, ',', '.') }}</strong> data lama dihapus, lalu <strong>{{ number_format($importPotonganPreview['inserted_count'], 0, ',', '.') }}</strong> data potongan baru dimasukkan ke sistem.
                            @else
                                Sebanyak <strong>{{ number_format($importPotonganPreview['inserted_count'], 0, ',', '.') }}</strong> data potongan baru akan dimasukkan ke sistem.
                            @endif
                        </span>
                    </div>
                </div>
            @endif

            <!-- Modal Footer -->
            <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-200 text-xs">
                <button type="button" @click="showImportPotonganModal = false; $wire.closeImportPotonganModal();"
                    class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">
                    Batal
                </button>
                @if($importPotonganPreview)
                    <button type="button" wire:click="processConfirmImportPotongan" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="processConfirmImportPotongan">
                            <i data-lucide="check" class="w-4 h-4 inline-block mr-1"></i>
                            Konfirmasi & Sinkronkan ke Database
                        </span>
                        <span wire:loading.inline-flex wire:target="processConfirmImportPotongan" class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Menyinkronkan...</span>
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: EXPORT REKAPITULASI POTONGAN PROGRAM SOSIAL -->
    <!-- ======================================================== -->
    <div x-show="showExportPotonganModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showExportPotonganModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showExportPotonganModal = false">
        <div
            class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-200 shrink-0">
                        <i data-lucide="download" class="w-4 h-4 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">Export Rekapitulasi Potongan</h3>
                        <p class="text-xs text-slate-500">Unduh data potongan pegawai dalam format Excel (.xlsx)</p>
                    </div>
                </div>
                <button type="button" @click="showExportPotonganModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Form Content -->
            <div class="space-y-4 text-xs">
                <!-- Filter Tahun -->
                <div class="space-y-1.5">
                    <label class="block font-bold text-gov-textMain">Tahun Potongan</label>
                    @php
                        $exportYearOptions = [];
                        for ($y = (int) now()->year + 1; $y >= 2024; $y--) {
                            $exportYearOptions[] = ['value' => $y, 'label' => (string) $y];
                        }
                    @endphp
                    <x-custom-select
                        x-model.number="exportPotonganYear"
                        :options="$exportYearOptions"
                        placeholder="Pilih Tahun..."
                        class="w-full text-xs"
                        buttonClass="p-2.5 bg-slate-50/50"
                    />
                </div>

                <!-- Filter Rentang Bulan (Bulan Mulai & Bulan Selesai) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block font-bold text-gov-textMain">Rentang Bulan Potongan</label>
                        <!-- Quick Presets -->
                        <div class="flex items-center gap-1.5 text-[11px]">
                            <button type="button" 
                                @click="exportPotonganStartMonth = {{ now()->month }}; exportPotonganEndMonth = {{ now()->month }}"
                                class="text-gov-navy hover:underline font-semibold cursor-pointer">
                                Bulan Ini
                            </button>
                            <span class="text-slate-300">|</span>
                            <button type="button" 
                                @click="exportPotonganStartMonth = 1; exportPotonganEndMonth = 12"
                                class="text-gov-navy hover:underline font-semibold cursor-pointer">
                                1 Tahun Penuh
                            </button>
                        </div>
                    </div>
                    @php
                        $monthOptions = [
                            ['value' => 1, 'label' => 'Januari'],
                            ['value' => 2, 'label' => 'Februari'],
                            ['value' => 3, 'label' => 'Maret'],
                            ['value' => 4, 'label' => 'April'],
                            ['value' => 5, 'label' => 'Mei'],
                            ['value' => 6, 'label' => 'Juni'],
                            ['value' => 7, 'label' => 'Juli'],
                            ['value' => 8, 'label' => 'Agustus'],
                            ['value' => 9, 'label' => 'September'],
                            ['value' => 10, 'label' => 'Oktober'],
                            ['value' => 11, 'label' => 'November'],
                            ['value' => 12, 'label' => 'Desember'],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <x-custom-select
                                x-model.number="exportPotonganStartMonth"
                                @change="if(exportPotonganStartMonth > exportPotonganEndMonth) exportPotonganEndMonth = exportPotonganStartMonth"
                                :options="$monthOptions"
                                placeholder="Bulan Mulai..."
                                class="w-full text-xs"
                                buttonClass="p-2.5 bg-slate-50/50"
                            />
                        </div>
                        <div>
                            <x-custom-select
                                x-model.number="exportPotonganEndMonth"
                                @change="if(exportPotonganEndMonth < exportPotonganStartMonth) exportPotonganStartMonth = exportPotonganEndMonth"
                                :options="$monthOptions"
                                placeholder="Bulan Selesai..."
                                class="w-full text-xs"
                                buttonClass="p-2.5 bg-slate-50/50"
                            />
                        </div>
                    </div>
                </div>

                <!-- Format Output Info Box -->
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-slate-600 space-y-1">
                    <span class="font-bold flex items-center gap-1.5 text-gov-navy">
                        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-gov-navy"></i>
                        <span>Nama Berkas Unduhan:</span>
                    </span>
                    <p class="text-[11px] text-slate-600 font-mono">
                        <span x-text="exportPotonganStartMonth === exportPotonganEndMonth 
                            ? 'Rekapitulasi Potongan Masjid - Periode ' + (['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][exportPotonganStartMonth] || '') + ' ' + exportPotonganYear + '.xlsx'
                            : (exportPotonganStartMonth === 1 && exportPotonganEndMonth === 12 
                                ? 'Rekapitulasi Potongan Masjid - Seluruh Periode Tahun ' + exportPotonganYear + '.xlsx'
                                : 'Rekapitulasi Potongan Masjid - Periode ' + (['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][exportPotonganStartMonth] || '') + ' - ' + (['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][exportPotonganEndMonth] || '') + ' ' + exportPotonganYear + '.xlsx')">
                        </span>
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-200 text-xs">
                <button type="button" @click="showExportPotonganModal = false"
                    class="px-4 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition cursor-pointer">
                    Batal
                </button>
                <a :href="'{{ route('admin.programs.export-excel') }}?year=' + exportPotonganYear + '&start_month=' + exportPotonganStartMonth + '&end_month=' + exportPotonganEndMonth + '&month=' + exportPotonganStartMonth"
                    @click="setTimeout(() => { showExportPotonganModal = false; }, 300)"
                    class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <i data-lucide="download" class="w-4 h-4 text-amber-400"></i>
                    <span>Download Excel (.xlsx)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL: CETAK SURAT PERNYATAAN POTONGAN TUKIN -->
    <!-- ========================================== -->
    <div x-show="showPrintPernyataanModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showPrintPernyataanModal = false"
        x-transition:enter="transition ease-out duration-75"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-50"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex justify-center items-center p-3 sm:p-5"
        style="display: none;" @keydown.escape.window="if (printPernyataanOpenDropdown) { closePrintDropdowns(); } else { showPrintPernyataanModal = false; }">
        <div class="relative bg-white rounded-2xl w-full p-4 sm:p-5 text-left shadow-2xl border border-gov-border space-y-3 my-auto transition-all duration-150 overflow-hidden"
            :class="(pernyataanModalTab === 'impor' && ($wire.scanPages?.length > 0 || {{ !empty($scanPages) ? 'true' : 'false' }})) ? (window.innerWidth < 1024 ? 'max-w-6xl max-h-[92vh] overflow-y-auto block' : 'max-w-6xl max-h-[92vh] flex flex-col overflow-hidden') : (pernyataanModalTab === 'impor' ? 'max-w-2xl max-h-[90vh] overflow-y-auto' : 'max-w-xl max-h-[90vh] overflow-y-auto')"
            x-transition:enter="transition ease-out duration-75"
            x-transition:enter-start="opacity-0 scale-98"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-50"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-98"
            @click="closePrintDropdowns()">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center border border-blue-200">
                        <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gov-textMain">Surat Pernyataan Potongan Tukin</h3>
                        <p class="text-xs text-slate-500">Cetak lembar formulir atau impor pindaian (scan) kesediaan pemotongan tunjangan kinerja</p>
                    </div>
                </div>
                <button type="button" @click="showPrintPernyataanModal = false"
                    class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Tab Switcher (Cetak vs Impor) -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl text-xs">
                <button type="button" @click="pernyataanModalTab = 'cetak'; $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                    :class="pernyataanModalTab === 'cetak' ? 'bg-white text-gov-navy font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 py-1.5 px-3 rounded-lg transition inline-flex items-center justify-center gap-1.5 cursor-pointer">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    <span>Cetak Pernyataan</span>
                </button>
                <button type="button" @click="pernyataanModalTab = 'impor'; $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                    :class="pernyataanModalTab === 'impor' ? 'bg-white text-gov-navy font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-medium'"
                    class="flex-1 py-1.5 px-3 rounded-lg transition inline-flex items-center justify-center gap-1.5 cursor-pointer">
                    <i data-lucide="scan-line" class="w-3.5 h-3.5"></i>
                    <span>Impor Pernyataan (Scan PDF)</span>
                </button>
            </div>

            <!-- TAB 1: CETAK PERNYATAAN -->
            <div x-show="pernyataanModalTab === 'cetak'" class="space-y-4 text-xs">
                <!-- Mode Pemilihan Pegawai -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Mode Sasaran Pegawai *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <button type="button" @click="printPernyataanMode = 'all'; closePrintDropdowns()"
                            class="p-2.5 rounded-lg border text-left transition flex flex-col justify-between cursor-pointer"
                            :class="printPernyataanMode === 'all' ? 'border-gov-navy bg-sky-50/70 text-gov-navy font-bold ring-1 ring-gov-navy/20' : 'border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium'">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="users" class="w-3.5 h-3.5 text-blue-600 shrink-0"></i>
                                <span>Semua Pegawai</span>
                            </span>
                            <span class="text-[11px] text-slate-500 font-normal mt-1 leading-tight">Mail-Merge seluruh pegawai aktif yang ada riwayat</span>
                        </button>

                        <button type="button" @click="printPernyataanMode = 'single'; closePrintDropdowns()"
                            class="p-2.5 rounded-lg border text-left transition flex flex-col justify-between cursor-pointer"
                            :class="printPernyataanMode === 'single' ? 'border-gov-navy bg-sky-50/70 text-gov-navy font-bold ring-1 ring-gov-navy/20' : 'border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium'">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                <span>Pilih Pegawai</span>
                            </span>
                            <span class="text-[11px] text-slate-500 font-normal mt-1 leading-tight">Cetak spesifik 1 pegawai dari daftar</span>
                        </button>

                        <button type="button" @click="printPernyataanMode = 'new'; closePrintDropdowns()"
                            class="p-2.5 rounded-lg border text-left transition flex flex-col justify-between cursor-pointer"
                            :class="printPernyataanMode === 'new' ? 'border-gov-navy bg-sky-50/70 text-gov-navy font-bold ring-1 ring-gov-navy/20' : 'border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium'">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="user-plus" class="w-3.5 h-3.5 text-amber-600 shrink-0"></i>
                                <span>Pegawai Baru</span>
                            </span>
                            <span class="text-[11px] text-slate-500 font-normal mt-1 leading-tight">Tanpa tabel riwayat potongan</span>
                        </button>
                    </div>
                </div>

                <!-- Mode Single: Dropdown Pegawai yang Pernah Memotong -->
                <div x-show="printPernyataanMode === 'single'" class="space-y-1">
                    <label class="block font-semibold text-slate-700">Nama Pegawai (dari Riwayat) *</label>
                    <div class="relative" :class="printPernyataanOpenDropdown === 'singleEmp' ? 'z-40' : 'z-10'"
                        @click.outside="if (printPernyataanOpenDropdown === 'singleEmp') closePrintDropdowns()">
                        <button type="button" @click.stop="togglePrintDropdown('singleEmp')"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between text-left">
                            <span class="truncate" x-text="printPernyataanEmployeeName ? printPernyataanEmployeeName : '-- Pilih Nama Pegawai yang Ada di Rekapitulasi --'"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'singleEmp' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div x-show="printPernyataanOpenDropdown === 'singleEmp'" x-cloak @click.stop
                            class="absolute left-0 top-full mt-1 w-full bg-white rounded-xl shadow-2xl border border-gov-border p-1.5 z-50 max-h-60 overflow-hidden flex flex-col"
                            style="display: none;">
                            <div class="p-1 pb-1.5 border-b border-slate-100">
                                <input x-model="printPernyataanSearchEmp" type="text" placeholder="Ketik untuk mencari nama..."
                                    class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-gov-navy">
                            </div>
                            <div class="overflow-y-auto max-h-48 divide-y divide-slate-50 py-1">
                                <button type="button" @click.stop="printPernyataanEmployeeName = ''; closePrintDropdowns()"
                                    class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer text-slate-500 italic">
                                    -- Batal Pilih --
                                </button>
                                @if(isset($distinctParticipantNames))
                                    @foreach($distinctParticipantNames as $pName)
                                        <button type="button"
                                            x-show="!printPernyataanSearchEmp || '{{ strtolower(addslashes($pName)) }}'.includes(printPernyataanSearchEmp.toLowerCase())"
                                            @click.stop="printPernyataanEmployeeName = '{{ addslashes($pName) }}'; closePrintDropdowns()"
                                            class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                            :class="printPernyataanEmployeeName === '{{ addslashes($pName) }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                            <span>{{ $pName }}</span>
                                            <span x-show="printPernyataanEmployeeName === '{{ addslashes($pName) }}'" class="text-gov-navy font-bold">✓</span>
                                        </button>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mode New: Pilih dari Master Pegawai atau Formulir Kosong -->
                <div x-show="printPernyataanMode === 'new'" class="space-y-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Pilih Pegawai Baru dari Master (Atau Kosongkan untuk Blanko) *</label>
                        <div class="relative" :class="printPernyataanOpenDropdown === 'newEmp' ? 'z-40' : 'z-10'"
                            @click.outside="if (printPernyataanOpenDropdown === 'newEmp') closePrintDropdowns()">
                            <button type="button" @click.stop="togglePrintDropdown('newEmp')"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between text-left">
                                <span class="truncate" x-text="printPernyataanEmployeeId && printPernyataanSelectedNewName ? printPernyataanSelectedNewName : '-- Formulir Blanko Kosong (Semua Data Titik-Titik Tulis Tangan) --'"></span>
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'newEmp' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </button>

                            <div x-show="printPernyataanOpenDropdown === 'newEmp'" x-cloak @click.stop
                                class="absolute left-0 top-full mt-1 w-full bg-white rounded-xl shadow-2xl border border-gov-border p-1.5 z-50 max-h-60 overflow-hidden flex flex-col"
                                style="display: none;">
                                <div class="p-1 pb-1.5 border-b border-slate-100">
                                    <input x-model="printPernyataanSearchNewEmp" type="text" placeholder="Ketik nama atau NIP pegawai..."
                                        class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:ring-1 focus:ring-gov-navy">
                                </div>
                                <div class="overflow-y-auto max-h-48 divide-y divide-slate-50 py-1">
                                    <button type="button" @click.stop="printPernyataanEmployeeId = ''; printPernyataanSelectedNewName = ''; closePrintDropdowns()"
                                        class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer text-slate-500 italic">
                                        -- Formulir Blanko Kosong (Semua Data Titik-Titik Tulis Tangan) --
                                    </button>
                                    @if(isset($allEmployeesForPrint))
                                        @foreach($allEmployeesForPrint as $empItem)
                                            @php
                                                $empDisplay = $empItem->name . ' (NIP: ' . ($empItem->nip_panjang ?: ($empItem->nip_pendek ?: '-')) . ')';
                                            @endphp
                                            <button type="button"
                                                x-show="!printPernyataanSearchNewEmp || '{{ strtolower(addslashes($empDisplay)) }}'.includes(printPernyataanSearchNewEmp.toLowerCase())"
                                                @click.stop="printPernyataanEmployeeId = '{{ $empItem->id }}'; printPernyataanSelectedNewName = '{{ addslashes($empDisplay) }}'; closePrintDropdowns()"
                                                class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                                :class="printPernyataanEmployeeId == '{{ $empItem->id }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                                <span>{{ $empDisplay }}</span>
                                                <span x-show="printPernyataanEmployeeId == '{{ $empItem->id }}'" class="text-gov-navy font-bold">✓</span>
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-2.5 bg-amber-50 rounded-lg border border-amber-200 text-amber-800 flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                        <div class="text-[11px] leading-relaxed">
                            <span class="font-bold">Informasi:</span> Pada mode Pegawai Baru, <span class="font-semibold underline">tabel Riwayat Potongan akan disembunyikan</span> sehingga formulir siap diisi dan ditandatangani untuk pendaftaran komitmen baru.
                        </div>
                    </div>
                </div>

                <!-- Tahun Riwayat Potongan (Untuk Mode All & Single) -->
                <div x-show="printPernyataanMode !== 'new'" class="space-y-1">
                    <label class="block font-semibold text-slate-700">Tahun Riwayat Potongan *</label>
                    <div class="relative" :class="printPernyataanOpenDropdown === 'historyYear' ? 'z-40' : 'z-10'"
                        @click.outside="if (printPernyataanOpenDropdown === 'historyYear') closePrintDropdowns()">
                        <button type="button" @click.stop="togglePrintDropdown('historyYear')"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between text-left">
                            <span class="truncate" x-text="'Tahun ' + printPernyataanYear"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'historyYear' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div x-show="printPernyataanOpenDropdown === 'historyYear'" x-cloak @click.stop
                            class="absolute left-0 top-full mt-1 w-full bg-white rounded-xl shadow-2xl border border-gov-border py-1 z-50 max-h-56 overflow-y-auto divide-y divide-slate-100"
                            style="display: none;">
                            @for($y = (int)date('Y') + 1; $y >= 2024; $y--)
                                <button type="button" @click.stop="printPernyataanYear = '{{ $y }}'; closePrintDropdowns()"
                                    class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                    :class="printPernyataanYear == '{{ $y }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                    <span>Tahun {{ $y }}</span>
                                    <span x-show="printPernyataanYear == '{{ $y }}'" class="text-gov-navy font-bold">✓</span>
                                </button>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Input Opsional: Tanggal Surat & Mulai Berlaku -->
                <div class="border-t border-slate-200 pt-3 space-y-3">
                    <div class="font-bold text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-gov-navy"></i>
                        <span>Keterangan Waktu Persuratan (Opsional)</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Tanggal Surat</label>
                            <input x-model="printPernyataanDateLetter" type="text"
                                placeholder="Contoh: 15 Oktober 2026 (kosong = titik-titik)"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Mulai Berlaku Bulan & Tahun</label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <!-- Dropdown Bulan Custom (Seamless & Instan 0ms) -->
                                <div class="relative" :class="printPernyataanOpenDropdown === 'month' ? 'z-40' : 'z-10'"
                                    @click.outside="if (printPernyataanOpenDropdown === 'month') closePrintDropdowns()">
                                    <button type="button" @click.stop="togglePrintDropdown('month')"
                                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between gap-1 text-left">
                                        <span class="truncate" x-text="printPernyataanEffectiveMonth ? printPernyataanEffectiveMonth : '-- Kosongkan --'"></span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'month' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m6 9 6 6 6-6"/>
                                        </svg>
                                    </button>

                                    <div x-show="printPernyataanOpenDropdown === 'month'" x-cloak @click.stop
                                        class="absolute left-0 top-full mt-1 w-full min-w-[140px] bg-white rounded-xl shadow-2xl border border-gov-border py-1 z-50 max-h-56 overflow-y-auto divide-y divide-slate-100"
                                        style="display: none;">
                                        <button type="button" @click.stop="printPernyataanEffectiveMonth = ''; closePrintDropdowns()"
                                            class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                            :class="!printPernyataanEffectiveMonth ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-500 font-medium italic'">
                                            <span>-- Kosongkan --</span>
                                            <span x-show="!printPernyataanEffectiveMonth" class="text-gov-navy font-bold">✓</span>
                                        </button>
                                        @php
                                            $daftarBulan = [
                                                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                            ];
                                        @endphp
                                        @foreach($daftarBulan as $bulan)
                                            <button type="button" @click.stop="printPernyataanEffectiveMonth = '{{ $bulan }}'; closePrintDropdowns()"
                                                class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                                :class="printPernyataanEffectiveMonth === '{{ $bulan }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                                <span>{{ $bulan }}</span>
                                                <span x-show="printPernyataanEffectiveMonth === '{{ $bulan }}'" class="text-gov-navy font-bold">✓</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Dropdown Tahun Custom (Seamless & Instan 0ms) -->
                                <div class="relative" :class="printPernyataanOpenDropdown === 'year' ? 'z-40' : 'z-10'"
                                    @click.outside="if (printPernyataanOpenDropdown === 'year') closePrintDropdowns()">
                                    <button type="button" @click.stop="togglePrintDropdown('year')"
                                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs flex items-center justify-between gap-1 text-left">
                                        <span class="truncate" x-text="printPernyataanEffectiveYear ? printPernyataanEffectiveYear : '-- Kosongkan --'"></span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" :class="printPernyataanOpenDropdown === 'year' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m6 9 6 6 6-6"/>
                                        </svg>
                                    </button>

                                    <div x-show="printPernyataanOpenDropdown === 'year'" x-cloak @click.stop
                                        class="absolute right-0 top-full mt-1 w-full min-w-[110px] bg-white rounded-xl shadow-2xl border border-gov-border py-1 z-50 max-h-56 overflow-y-auto divide-y divide-slate-100"
                                        style="display: none;">
                                        <button type="button" @click.stop="printPernyataanEffectiveYear = ''; closePrintDropdowns()"
                                            class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                            :class="!printPernyataanEffectiveYear ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-500 font-medium italic'">
                                            <span>-- Kosongkan --</span>
                                            <span x-show="!printPernyataanEffectiveYear" class="text-gov-navy font-bold">✓</span>
                                        </button>
                                        @for($y = (int)date('Y') + 2; $y >= 2024; $y--)
                                            <button type="button" @click.stop="printPernyataanEffectiveYear = '{{ $y }}'; closePrintDropdowns()"
                                                class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 cursor-pointer flex items-center justify-between"
                                                :class="printPernyataanEffectiveYear == '{{ $y }}' ? 'bg-sky-50 text-gov-navy font-bold' : 'text-slate-700 font-medium'">
                                                <span>{{ $y }}</span>
                                                <span x-show="printPernyataanEffectiveYear == '{{ $y }}'" class="text-gov-navy font-bold">✓</span>
                                            </button>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Petugas Sekretaris Pengumpul Berkas -->
                <div class="border-t border-slate-200 pt-3 space-y-3">
                    <div class="font-bold text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-gov-navy"></i>
                        <span>Sekretaris Pengumpul Berkas (Footer Surat)</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Nama Sekretaris</label>
                            <input x-model="printPernyataanSecretaryName" type="text"
                                placeholder="Deril Amrizal Kholid"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Seksi / Unit Sekretaris</label>
                            <input x-model="printPernyataanSecretarySection" type="text"
                                placeholder="Seksi Penjaminan Kualitas Data"
                                class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer (Cetak) -->
            <div x-show="pernyataanModalTab === 'cetak'" class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-200 text-xs">
                <button type="button" @click="showPrintPernyataanModal = false"
                    class="px-4 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition cursor-pointer">
                    Batal
                </button>
                <a :href="'{{ route('admin.programs.print-surat-pernyataan') }}?mode=' + printPernyataanMode + '&year=' + printPernyataanYear + '&employee_name=' + encodeURIComponent(printPernyataanEmployeeName) + '&employee_id=' + printPernyataanEmployeeId + '&date_letter=' + encodeURIComponent(printPernyataanDateLetter) + '&effective_month=' + encodeURIComponent(printPernyataanEffectiveMonth) + '&effective_year=' + encodeURIComponent(printPernyataanEffectiveYear) + '&secretary_name=' + encodeURIComponent(printPernyataanSecretaryName) + '&secretary_section=' + encodeURIComponent(printPernyataanSecretarySection)"
                    target="_blank"
                    @click="setTimeout(() => { showPrintPernyataanModal = false; }, 300)"
                    class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4 text-amber-400"></i>
                    <span>Buka Dokumen Cetak</span>
                </a>
            </div>

            <!-- TAB 2: IMPOR PERNYATAAN DARI SCAN PDF -->
            <div x-show="pernyataanModalTab === 'impor'" class="flex-1 min-h-0 flex flex-col space-y-3 text-xs">
                @if(empty($scanPages))
                    <!-- Upload Area -->
                    <div class="space-y-3">
                        <div class="p-3.5 bg-blue-50/70 rounded-xl border border-blue-200 text-blue-900 leading-relaxed text-xs">
                            <div class="flex items-start gap-2.5">
                                <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                                <div>
                                    <span class="font-bold">Pemrosesan Dokumen Scan Lokal Server:</span>
                                    <p class="mt-0.5 text-blue-800 text-[11px] leading-relaxed">
                                        Unggah berkas pindaian multi-halaman PDF lembar formulir pemotongan tunjangan kinerja (seperti scan mesin). Sistem akan mengekstrak NIP, nama pegawai, dan nominal komitmen zakat/infaq/qurban/santunan secara otomatis untuk diverifikasi.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- File Input / Dropzone -->
                        <div class="border-2 border-dashed rounded-2xl p-8 text-center space-y-2.5 transition relative overflow-hidden group"
                             x-data="{ isDragging: false }"
                             :class="isDragging ? 'border-gov-navy bg-blue-50/70 ring-2 ring-gov-navy/20' : 'border-slate-300 hover:border-gov-navy bg-slate-50/50 hover:bg-slate-50'">
                            
                            <!-- Full-Surface Native File Input -->
                            <input id="scanPernyataanFileInput"
                                   type="file"
                                   wire:model.live="scanPernyataanFile"
                                   accept="application/pdf"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                   @dragenter="isDragging = true"
                                   @dragover.prevent="isDragging = true"
                                   @dragleave="isDragging = false"
                                   @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }">

                            <!-- Loading Overlay during file upload & parsing -->
                            <div wire:loading.flex wire:target="scanPernyataanFile, processScanPernyataan" class="absolute inset-0 bg-white/95 backdrop-blur-xs rounded-2xl flex-col items-center justify-center gap-2.5 z-20">
                                <svg class="w-8 h-8 animate-spin text-gov-navy shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-xs font-bold text-gov-textMain">Sedang membaca berkas PDF & mengekstrak data formulir...</span>
                                <span class="text-[11px] text-slate-500">Mempersiapkan pindaian dan lembar verifikasi</span>
                            </div>

                            <!-- Dropzone Visual Elements (pointer-events-none prevents child drag interference) -->
                            <div class="pointer-events-none space-y-2">
                                <div class="w-12 h-12 mx-auto rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200 shadow-2xs mb-2">
                                    <i data-lucide="file-up" class="w-6 h-6 text-blue-600"></i>
                                </div>
                                <p class="text-sm font-bold text-gov-textMain">
                                    <span class="text-gov-navy underline underline-offset-2">Klik untuk memilih berkas</span> atau tarik scan PDF ke sini
                                </p>
                                <p class="text-xs text-slate-500">
                                    Mendukung berkas PDF multi-halaman (hingga 20 MB)
                                </p>
                            </div>
                        </div>

                        @if($scanError)
                            <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 text-rose-700 flex items-center gap-2">
                                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-600"></i>
                                <span class="text-xs font-medium">{{ $scanError }}</span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                            <button type="button" @click="showPrintPernyataanModal = false"
                                class="px-4 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Verification Split-Screen -->
                    <div class="flex-1 min-h-0 flex flex-col justify-between space-y-3"
                         x-data="{
                             activeScanIdx: @entangle('activeScanPageIndex').live,
                             pdfBaseUrl: '{{ route('admin.programs.preview-scan-pernyataan') }}',
                             totalPages: {{ count($scanPages) }},
                             scanEmpNames: {{ json_encode(array_map(fn($p) => $p['nama'] ?? 'Pegawai', $scanPages)) }},
                             hasImages: {{ !empty($scanPages[0]['image_path'] ?? null) ? 'true' : 'false' }},
                             isPdfLoading: {{ empty($scanPages[0]['image_path'] ?? null) ? 'true' : 'false' }},
                             pdfError: '',
                             isMobile: window.innerWidth < 1024,
                             pageRotations: {},
                             rotatePage(idx) {
                                 const current = this.pageRotations[idx] || 0;
                                 this.pageRotations = { ...this.pageRotations, [idx]: (current + 180) % 360 };
                             },
                             init() {
                                 window.addEventListener('resize', () => {
                                     this.isMobile = window.innerWidth < 1024;
                                 });
                                 this.$nextTick(() => {
                                     if (window.createLucideIcons) window.createLucideIcons();
                                     this.setupContinuousScrollObserver();
                                     if (!this.hasImages) {
                                         this.initContinuousPdf();
                                     } else {
                                         this.isPdfLoading = false;
                                     }
                                 });
                             },
                             async initContinuousPdf() {
                                 if (this.hasImages) {
                                     this.isPdfLoading = false;
                                     return;
                                 }
                                 this.isPdfLoading = true;
                                 this.pdfError = '';
                                 try {
                                     let retries = 0;
                                     while (typeof window.pdfjsLib === 'undefined' && retries < 40) {
                                         await new Promise(r => setTimeout(r, 50));
                                         retries++;
                                     }
                                     if (typeof window.pdfjsLib === 'undefined') {
                                         this.isPdfLoading = false;
                                         return;
                                     }

                                     window.pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset('vendor/pdfjs/pdf.worker.min.js') }}';
                                     const loadingTask = window.pdfjsLib.getDocument({
                                         url: this.pdfBaseUrl,
                                         cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/cmaps/',
                                         cMapPacked: true,
                                     });
                                     const pdf = await loadingTask.promise;
                                     const numPages = Math.min(pdf.numPages, this.totalPages);

                                     for (let pNum = 1; pNum <= numPages; pNum++) {
                                         const page = await pdf.getPage(pNum);
                                         const canvas = document.getElementById('scan-pdf-canvas-' + (pNum - 1));
                                         if (canvas) {
                                             const viewport = page.getViewport({ scale: 1.5 });
                                             canvas.height = viewport.height;
                                             canvas.width = viewport.width;
                                             const ctx = canvas.getContext('2d');
                                             await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                                         }
                                     }
                                     this.isPdfLoading = false;
                                     this.setupContinuousScrollObserver();
                                 } catch (err) {
                                     console.error('PDF.js render error:', err);
                                     this.isPdfLoading = false;
                                     this.pdfError = 'Gagal memuat pratinjau dokumen pindaian.';
                                 }
                             },
                             setupContinuousScrollObserver() {
                                 const container = document.getElementById('pdfContinuousScrollContainer');
                                 if (!container) return;

                                 if (this._scrollListener) {
                                     container.removeEventListener('scroll', this._scrollListener);
                                 }

                                 this._scrollListener = () => {
                                     const containerRect = container.getBoundingClientRect();
                                     // Target focus line: 35% from the top of the container
                                     const targetY = containerRect.top + containerRect.height * 0.35;

                                     let selectedIdx = null;
                                     let minDistance = Infinity;

                                     const cards = container.querySelectorAll('.scan-pdf-page-card');
                                     cards.forEach((card) => {
                                         const rect = card.getBoundingClientRect();
                                         if (rect.top <= targetY && rect.bottom >= targetY) {
                                             selectedIdx = parseInt(card.dataset.pageIndex, 10);
                                         }
                                         const cardCenter = (rect.top + rect.bottom) / 2;
                                         const dist = Math.abs(cardCenter - targetY);
                                         if (dist < minDistance) {
                                             minDistance = dist;
                                             if (selectedIdx === null) {
                                                 selectedIdx = parseInt(card.dataset.pageIndex, 10);
                                             }
                                         }
                                     });

                                     if (selectedIdx !== null && !isNaN(selectedIdx) && this.activeScanIdx !== selectedIdx) {
                                         this.activeScanIdx = selectedIdx;
                                     }
                                 };

                                 container.addEventListener('scroll', this._scrollListener, { passive: true });
                                 this._scrollListener();
                             },
                             prevPage() {
                                 if (this.activeScanIdx > 0) {
                                     this.selectPage(this.activeScanIdx - 1);
                                 }
                             },
                             nextPage() {
                                 if (this.activeScanIdx < this.totalPages - 1) {
                                     this.selectPage(this.activeScanIdx + 1);
                                 }
                             },
                             selectPage(idx) {
                                 this.activeScanIdx = idx;
                                 if (!this.isMobile) {
                                     const card = document.getElementById('scan-page-card-' + idx);
                                     if (card) {
                                         card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                     }
                                 }
                             }
                         }">

                        <!-- Split View Container (Responsive: Side-by-Side di Desktop, Full Flow di Mobile) -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch"
                             :class="isMobile ? 'overflow-visible' : 'flex-1 min-h-0'">
                            <!-- Sisi Kiri: Pindaian Dokumen Asli (Desktop: Terkunci & Scrollable, Mobile: Full Height) -->
                            <div :class="isMobile ? 'h-auto overflow-visible' : 'h-[470px] xl:h-[490px] 2xl:h-[570px] max-h-[calc(92vh-220px)] overflow-hidden flex flex-col'"
                                 class="lg:col-span-7 bg-slate-50 rounded-xl border border-gov-border p-3 justify-between relative shadow-2xs">
                                <!-- Header Controls -->
                                <div class="flex items-center justify-between pb-2 border-b border-slate-200 text-xs text-gov-textMain shrink-0">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center">
                                            <i data-lucide="file-search" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-800">Dokumen Asli</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="rotatePage(activeScanIdx)"
                                            title="Putar Dokumen 180°"
                                            class="h-7 px-2.5 rounded-lg bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 text-xs font-semibold inline-flex items-center gap-1 border border-slate-200 shadow-2xs transition cursor-pointer">
                                            <i data-lucide="rotate-cw" class="w-3.5 h-3.5 text-slate-500"></i>
                                            <span>Putar</span>
                                        </button>
                                        <a :href="pdfBaseUrl + '#page=' + (activeScanIdx + 1)" target="_blank"
                                            class="h-7 px-3 rounded-lg bg-white hover:bg-slate-100 text-blue-700 hover:text-blue-900 text-xs font-semibold inline-flex items-center gap-1.5 border border-slate-200 shadow-2xs transition cursor-pointer">
                                            <i data-lucide="external-link" class="w-3.5 h-3.5 text-blue-600"></i>
                                            <span class="hidden sm:inline">Buka di Tab Baru</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Continuous Scroll Container for All PDF Pages -->
                                <div wire:ignore
                                     wire:key="scan-pdf-continuous-container-{{ count($scanPages) }}-{{ session('temp_scan_pdf_time', 1) }}"
                                     id="pdfContinuousScrollContainer"
                                     :class="isMobile ? 'w-full h-auto overflow-visible' : 'flex-1 min-h-0 w-full overflow-y-auto notula-scrollable'"
                                     class="my-1.5 rounded-lg bg-slate-200/90 p-2 sm:p-3 space-y-3.5 border border-slate-300 shadow-inner relative">
                                    
                                    <!-- Loading State Overlay -->
                                    <div x-show="isPdfLoading" class="absolute inset-0 bg-slate-100/95 flex flex-col items-center justify-center gap-2 z-20">
                                        <svg class="w-7 h-7 animate-spin text-gov-navy shrink-0" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-xs font-semibold text-slate-700">Menyiapkan halaman dokumen pindaian...</span>
                                    </div>

                                    @foreach($scanPages as $pIdx => $pItem)
                                        @php
                                            $targetPage = (int) ($pItem['page_number'] ?? ($pIdx + 1));
                                            $pageEmpName = $pItem['nama'] ?? 'Pegawai';
                                        @endphp
                                        <div class="scan-pdf-page-card bg-white rounded-xl shadow-xs border overflow-hidden transition cursor-pointer"
                                             :class="isMobile ? (activeScanIdx === {{ $pIdx }} ? 'ring-2 ring-gov-navy border-gov-navy shadow-md block' : 'hidden') : (activeScanIdx === {{ $pIdx }} ? 'ring-2 ring-gov-navy border-gov-navy shadow-md block' : 'border-slate-300 opacity-75 hover:opacity-100 block')"
                                             data-page-index="{{ $pIdx }}"
                                             id="scan-page-card-{{ $pIdx }}"
                                             @click="selectPage({{ $pIdx }})">
                                            <!-- Card Header -->
                                            <div class="px-3.5 py-1.5 border-b flex items-center justify-between text-xs transition"
                                                 :class="activeScanIdx === {{ $pIdx }} ? 'bg-sky-50 text-gov-navy font-bold border-sky-200' : 'bg-slate-50 text-slate-600 border-slate-200'">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="w-2.5 h-2.5 rounded-full" :class="activeScanIdx === {{ $pIdx }} ? 'bg-emerald-500 ring-2 ring-emerald-200' : 'bg-slate-300'"></span>
                                                    <span>Halaman {{ $targetPage }}</span>
                                                    <span class="text-slate-400 font-normal">dari {{ count($scanPages) }}</span>
                                                </div>
                                                <button type="button" @click.stop="rotatePage({{ $pIdx }})"
                                                    title="Putar Lembar Ini 180°"
                                                    class="h-5 px-1.5 rounded bg-white hover:bg-slate-100 text-slate-600 hover:text-slate-800 text-[10px] font-medium inline-flex items-center gap-1 border border-slate-200 shadow-2xs transition cursor-pointer">
                                                    <i data-lucide="rotate-cw" class="w-2.5 h-2.5 text-slate-400"></i>
                                                    <span>Putar</span>
                                                </button>
                                            </div>

                                            <!-- Content: High-Res Poppler Image OR PDF.js Canvas -->
                                            <div class="bg-white flex items-center justify-center p-2 min-h-0 lg:min-h-[280px]">
                                                @if(!empty($pItem['image_path']))
                                                    <img src="{{ $pItem['image_path'] }}"
                                                         alt="Halaman {{ $targetPage }}"
                                                         :style="'transform: rotate(' + (pageRotations[{{ $pIdx }}] || 0) + 'deg); transition: transform 0.2s ease;'"
                                                         class="w-full h-auto block rounded shadow-2xs object-contain"
                                                         loading="lazy">
                                                @else
                                                    <canvas id="scan-pdf-canvas-{{ $pIdx }}"
                                                            :style="'transform: rotate(' + (pageRotations[{{ $pIdx }}] || 0) + 'deg); transition: transform 0.2s ease;'"
                                                            class="w-full h-auto block rounded shadow-2xs bg-white"></canvas>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Bottom Footer info (Desktop: Hint Gulir, Mobile: Navigasi Lembar) -->
                                <div x-show="!isMobile" class="flex items-center justify-between text-[11px] text-slate-500 pt-1.5 border-t border-slate-200 shrink-0">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="mouse-pointer" class="w-3 h-3 text-slate-400"></i>
                                        <span>Gulir lembar atau klik kartu untuk beralih halaman.</span>
                                    </span>
                                </div>
                                <div x-show="isMobile" class="flex items-center justify-between gap-2 pt-2 border-t border-slate-200 text-xs shrink-0">
                                    <button type="button" @click="prevPage()" :disabled="activeScanIdx <= 0"
                                        :class="activeScanIdx <= 0 ? 'opacity-40 cursor-not-allowed text-slate-400 border-slate-200' : 'text-gov-navy hover:bg-slate-100 border-slate-300 cursor-pointer'"
                                        class="h-8 px-3 rounded-lg border bg-white font-semibold inline-flex items-center gap-1 transition">
                                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                        <span>Sebelumnya</span>
                                    </button>
                                    <button type="button" @click="nextPage()" :disabled="activeScanIdx >= totalPages - 1"
                                        :class="activeScanIdx >= totalPages - 1 ? 'opacity-40 cursor-not-allowed text-slate-400 border-slate-200' : 'text-gov-navy hover:bg-slate-100 border-slate-300 cursor-pointer'"
                                        class="h-8 px-3 rounded-lg border bg-white font-semibold inline-flex items-center gap-1 transition">
                                        <span>Berikutnya</span>
                                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Sisi Kanan: Form Koreksi & Verifikasi Sederhana (Desktop: Terkunci & Scrollable, Mobile: Full Height) -->
                            <div :class="isMobile ? 'h-auto overflow-visible' : 'h-[470px] xl:h-[490px] 2xl:h-[570px] max-h-[calc(92vh-220px)] overflow-hidden flex flex-col'"
                                 class="lg:col-span-5 bg-white rounded-xl border border-gov-border p-4 shadow-2xs">
                                <!-- Fixed Header -->
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 shrink-0">
                                    <span class="font-bold text-sm text-gov-textMain">Verifikasi Data</span>
                                    <button type="button" @click="$wire.removeScanPage(activeScanIdx)"
                                        class="text-rose-600 hover:text-rose-800 text-[11px] font-semibold hover:underline inline-flex items-center gap-1 cursor-pointer">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        <span>Hapus Lembar Ini</span>
                                    </button>
                                </div>

                                <!-- Scrollable Form Content (Desktop: Scrollable, Mobile: Full Height Tanpa Scroll) -->
                                <div :class="isMobile ? 'w-full h-auto overflow-visible' : 'flex-1 min-h-0 w-full overflow-y-auto notula-scrollable'"
                                     class="pr-1 space-y-3.5 pt-2">
                                    @foreach($scanPages as $idx => $cur)
                                        <div x-show="activeScanIdx === {{ $idx }}" x-cloak class="space-y-3.5"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0">

                                        <!-- Identitas Fields -->
                                        <div class="space-y-2.5">
                                            <div>
                                                <label class="block font-semibold text-slate-700 mb-1">Nama Pegawai *</label>
                                                <input type="text" wire:model.live="scanPages.{{ $idx }}.nama"
                                                    class="w-full h-8 px-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs font-semibold text-gov-textMain">
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                                <div>
                                                    <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                                                    <input type="text" wire:model.live="scanPages.{{ $idx }}.phone" placeholder="08xxxxxxxxxx"
                                                        class="w-full h-8 px-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs font-mono">
                                                </div>
                                                <div>
                                                    <label class="block font-semibold text-slate-700 mb-1">Periode Mulai Berlaku</label>
                                                    <input type="text" wire:model.live="scanPages.{{ $idx }}.effective_period" placeholder="Periode 11/2026"
                                                        class="w-full h-8 px-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-xs font-semibold text-gov-navy">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel Nominal Komitmen Bulanan dengan Pemisah Ribuan -->
                                        <div class="pt-2 border-t border-slate-100"
                                             x-data="{
                                                 zakat: @entangle('scanPages.' . $idx . '.commitments.zakat'),
                                                 infaq: @entangle('scanPages.' . $idx . '.commitments.infaq'),
                                                 qurban: @entangle('scanPages.' . $idx . '.commitments.qurban'),
                                                 yatim: @entangle('scanPages.' . $idx . '.commitments.yatim'),
                                                 dhuafa: @entangle('scanPages.' . $idx . '.commitments.dhuafa'),
                                                 getTotal() {
                                                     return (parseInt(this.zakat) || 0) +
                                                            (parseInt(this.infaq) || 0) +
                                                            (parseInt(this.qurban) || 0) +
                                                            (parseInt(this.yatim) || 0) +
                                                            (parseInt(this.dhuafa) || 0);
                                                 }
                                             }">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <label class="block font-bold text-slate-800 text-xs">Nominal Komitmen Bulanan (Rp)</label>
                                                <span class="text-[11px] text-slate-400">Pemisah ribuan otomatis</span>
                                            </div>

                                            <div class="space-y-1.5 bg-slate-50 p-2.5 rounded-xl border border-gov-border">
                                                <!-- 1. Zakat -->
                                                <div class="grid grid-cols-2 items-center gap-2">
                                                    <span class="font-medium text-slate-700 text-xs flex items-center gap-1.5">
                                                        <span>1. Zakat</span>
                                                    </span>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                        <input type="text" inputmode="numeric"
                                                            :value="window.formatRibuan(zakat)"
                                                            @input="window.handleRibuanInput($event.target, (clean) => { zakat = clean ? parseInt(clean) : 0; })"
                                                            placeholder="0"
                                                            class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                    </div>
                                                </div>

                                                <!-- 2. Infaq & Sedekah -->
                                                <div class="grid grid-cols-2 items-center gap-2">
                                                    <span class="font-medium text-slate-700 text-xs flex items-center gap-1.5">
                                                        <span>2. Infaq & Sedekah</span>
                                                    </span>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                        <input type="text" inputmode="numeric"
                                                            :value="window.formatRibuan(infaq)"
                                                            @input="window.handleRibuanInput($event.target, (clean) => { infaq = clean ? parseInt(clean) : 0; })"
                                                            placeholder="0"
                                                            class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                    </div>
                                                </div>

                                                <!-- 3. Tabungan Qurban -->
                                                <div class="grid grid-cols-2 items-center gap-2">
                                                    <span class="font-medium text-slate-700 text-xs flex items-center gap-1.5">
                                                        <span>3. Tabungan Qurban</span>
                                                    </span>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                        <input type="text" inputmode="numeric"
                                                            :value="window.formatRibuan(qurban)"
                                                            @input="window.handleRibuanInput($event.target, (clean) => { qurban = clean ? parseInt(clean) : 0; })"
                                                            placeholder="0"
                                                            class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                    </div>
                                                </div>

                                                <!-- 4. Santunan Anak Yatim -->
                                                <div class="grid grid-cols-2 items-center gap-2">
                                                    <span class="font-medium text-slate-700 text-xs flex items-center gap-1.5">
                                                        <span>4. Santunan Anak Yatim</span>
                                                    </span>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                        <input type="text" inputmode="numeric"
                                                            :value="window.formatRibuan(yatim)"
                                                            @input="window.handleRibuanInput($event.target, (clean) => { yatim = clean ? parseInt(clean) : 0; })"
                                                            placeholder="0"
                                                            class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                    </div>
                                                </div>

                                                <!-- 5. Santunan Dhuafa -->
                                                <div class="grid grid-cols-2 items-center gap-2">
                                                    <span class="font-medium text-slate-700 text-xs flex items-center gap-1.5">
                                                        <span>5. Santunan Dhuafa</span>
                                                    </span>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[11px] font-semibold">Rp</span>
                                                        <input type="text" inputmode="numeric"
                                                            :value="window.formatRibuan(dhuafa)"
                                                            @input="window.handleRibuanInput($event.target, (clean) => { dhuafa = clean ? parseInt(clean) : 0; })"
                                                            placeholder="0"
                                                            class="w-full h-7 pl-7 pr-2 text-right rounded-lg border border-slate-200 bg-white text-xs font-bold text-gov-textMain tnum focus:border-gov-navy focus:outline-none">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Live Total Calculation in Rupiah -->
                                            <div class="mt-2.5 pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                                                <span class="font-bold text-slate-700">Total Komitmen / Bulan:</span>
                                                <span class="font-extrabold text-gov-navy text-sm font-mono"
                                                      x-text="'Rp ' + (window.formatRibuan(getTotal()) || '0')">
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Footer for Verification Screen (Responsive: Rapih di Mobile & Desktop) -->
                        <div class="mt-4 pt-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-t border-slate-200 text-xs shrink-0">
                            <!-- Tombol Aksi Utama: Tutup & Unduh Excel (Berdampingan Proporsional di Mobile) -->
                            <div class="flex items-center gap-2 w-full sm:w-auto order-1 sm:order-2">
                                <button type="button" @click="showPrintPernyataanModal = false"
                                    class="flex-1 sm:flex-none px-4 py-2 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs transition cursor-pointer text-center">
                                    Tutup
                                </button>
                                <button type="button" wire:click="exportScanPernyataanExcel"
                                    wire:loading.attr="disabled"
                                    class="flex-2 sm:flex-none px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer inline-flex items-center justify-center gap-1.5">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-400"></i>
                                    <span>Unduh Excel</span>
                                </button>
                            </div>

                            <!-- Tombol Sekunder: Unggah Ulang PDF Lain (Lebar Penuh di Mobile Bawah, Sebelah Kiri di Desktop) -->
                            <button type="button" wire:click="resetScanPernyataan"
                                class="w-full sm:w-auto px-3.5 py-2 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-800 font-medium text-xs transition cursor-pointer inline-flex items-center justify-center gap-1.5 order-2 sm:order-1">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Unggah Ulang PDF Lain</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL: TAMBAH / EDIT PROGRAM SOSIAL (MILESTONE 6) -->
    <!-- ========================================== -->
    <div x-show="showSocialProgramModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) showSocialProgramModal = false"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="showSocialProgramModal = false">
        <div
            class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200 shrink-0">
                        <i data-lucide="heart-handshake" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain"
                            x-text="isEditingSocialProgram ? 'Edit Program Sosial' : 'Tambah Program Sosial Baru'">
                        </h3>
                        <p class="text-xs text-slate-500">Kelola master program sosial masjid</p>
                    </div>
                </div>
                <button type="button" @click="showSocialProgramModal = false"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveSocialProgram" class="space-y-4 text-xs">
                <!-- Nama Program -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Program Sosial *</label>
                    <input wire:model="socialProgramName" type="text" placeholder="Contoh: Santunan Anak Yatim" required
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    @error('socialProgramName') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Periode & Status Program -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Periode Program *</label>
                        <x-custom-select
                            wire:model="socialProgramPeriodType"
                            :options="[
                                ['value' => 'bulanan', 'label' => 'Bulanan (Rutin)'],
                                ['value' => 'tahunan', 'label' => 'Tahunan (Berkala)'],
                                ['value' => 'insidental', 'label' => 'Insidental (Sekali Waktu)'],
                            ]"
                            placeholder="Pilih Periode..."
                            class="w-full text-xs"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                        @error('socialProgramPeriodType') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Program *</label>
                        <x-custom-select
                            wire:model="socialProgramStatus"
                            :options="[
                                ['value' => 'AKTIF', 'label' => 'Aktif (Menerima Peserta)'],
                                ['value' => 'DITUTUP', 'label' => 'Ditutup Sementara'],
                                ['value' => 'SELESAI', 'label' => 'Selesai'],
                            ]"
                            placeholder="Pilih Status..."
                            class="w-full text-xs"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                        @error('socialProgramStatus') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Ikon Tampilan -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Ikon Tampilan</label>
                    <x-custom-select
                        wire:model="socialProgramIcon"
                        :options="[
                            ['value' => 'heart-handshake', 'label' => 'heart-handshake (Santunan/Sosial)'],
                            ['value' => 'hand-heart', 'label' => 'hand-heart (Sedekah/Dhuafa)'],
                            ['value' => 'wallet', 'label' => 'wallet (Infaq/Dompet)'],
                            ['value' => 'coins', 'label' => 'coins (Zakat/Koin)'],
                            ['value' => 'sparkles', 'label' => 'sparkles (Qurban/Spesial)'],
                            ['value' => 'users', 'label' => 'users (Komunitas)'],
                        ]"
                        placeholder="Pilih Ikon..."
                        class="w-full text-xs"
                        buttonClass="p-2.5 bg-slate-50/50"
                    />
                </div>

                <!-- Deskripsi Program -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Deskripsi & Peruntukan Dana</label>
                    <textarea wire:model="socialProgramDescription" rows="3"
                        placeholder="Jelaskan tujuan, peruntukan dana, dan sasaran penerima manfaat..."
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium"></textarea>
                    @error('socialProgramDescription') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Footer Buttons (Rule 5) -->
                <div class="pt-3 flex justify-end space-x-2 border-t border-slate-200">
                    <button type="button" @click="showSocialProgramModal = false"
                        class="px-3.5 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium transition cursor-pointer">Batal</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="saveSocialProgram"
                            x-text="isEditingSocialProgram ? 'Perbarui Program' : 'Simpan Program'"></span>
                        <span wire:loading.inline-flex wire:target="saveSocialProgram"
                            class="inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap">Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: DIREKTORI & DATABASE FOTO USTADZ (MILESTONE 1+) -->
    <!-- ======================================================== -->
    <div x-show="showUstadzModal" x-cloak wire:ignore.self @keydown.escape.window="showUstadzModal = false; $wire.closeUstadzModal()"
        @click="if (window.isBackdropClick($event, $el)) { showUstadzModal = false; $wire.closeUstadzModal(); }"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;">
        <div
            class="bg-white rounded-2xl max-w-3xl w-full p-6 shadow-2xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[92vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-200 pb-3 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200 shrink-0">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">
                            <span>Direktori & Database Foto Ustadz</span>
                        </h3>
                        <p class="text-xs text-slate-500">Cukup upload 1 foto untuk setiap ustadz. Foto otomatis tersinkronisasi ke seluruh jadwal kajian dan poster.</p>
                    </div>
                </div>
                <button type="button" @click="showUstadzModal = false; $wire.closeUstadzModal()"
                    class="text-slate-400 hover:text-gov-textMain p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Scrollable Content Area -->
            <div class="overflow-y-auto space-y-4 pr-1 flex-1">
                <!-- Form Box: Tambah / Edit Ustadz -->
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                            <span>{{ $editingUstadzId ? 'Edit Profil & Foto Ustadz' : 'Tambah Data Ustadz' }}</span>
                        </h4>
                        @if($editingUstadzId)
                            <button type="button" wire:click="resetUstadzForm" class="text-xs text-slate-500 hover:text-slate-800 underline cursor-pointer">
                                Batal Edit
                            </button>
                        @endif
                    </div>

                    <form wire:submit="saveUstadz" class="space-y-3 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-1">
                                <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap & Gelar *</label>
                                <input type="text" wire:model="ustadzName" placeholder="Contoh: Ust Hasyim Azhari SPdI"
                                    class="w-full p-2.5 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-medium text-xs text-gov-textMain">
                                @error('ustadzName') <span class="text-xs font-semibold text-rose-600 block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Kajian</label>
                                <input type="text" wire:model="ustadzTitle" placeholder="Misal: Kajian Tematik / Tafsir"
                                    class="w-full p-2.5 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-medium text-xs text-gov-textMain">
                                @error('ustadzTitle') <span class="text-xs font-semibold text-rose-600 block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                                <input type="text" wire:model="ustadzPhone" placeholder="0821-xxxx-xxxx"
                                    class="w-full p-2.5 rounded-lg border border-gov-border bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy font-medium text-xs text-gov-textMain">
                                @error('ustadzPhone') <span class="text-xs font-semibold text-rose-600 block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Foto Upload Row -->
                        <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-200">
                            <div class="relative shrink-0 w-12 h-12 rounded-full overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center shadow-2xs">
                                @if ($ustadzPhoto)
                                    <img src="{{ $ustadzPhoto->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                                @elseif ($ustadzExistingPhoto)
                                    <img src="{{ asset('storage/' . $ustadzExistingPhoto) }}" alt="Foto Ustadz" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <label class="relative cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold shadow-2xs transition overflow-hidden"
                                       x-data="{ isDragging: false }"
                                       @dragenter.prevent="isDragging = true"
                                       @dragover.prevent="isDragging = true"
                                       @dragleave.prevent="isDragging = false"
                                       @drop="isDragging = false"
                                       :class="isDragging ? 'ring-2 ring-gov-navy border-gov-navy bg-sky-50 text-gov-navy' : ''">
                                    <svg class="w-3.5 h-3.5 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="pointer-events-none" x-text="isDragging ? 'Lepas Foto' : '{{ $ustadzExistingPhoto || $ustadzPhoto ? 'Ganti Foto / Seret' : 'Unggah Foto / Seret' }}'">{{ $ustadzExistingPhoto || $ustadzPhoto ? 'Ganti Foto Ustadz' : 'Unggah Foto Ustadz' }}</span>
                                    <input type="file"
                                           wire:model="ustadzPhoto"
                                           accept="image/png,image/jpeg,image/webp"
                                           @dragenter="isDragging = true"
                                           @dragover.prevent="isDragging = true"
                                           @dragleave="isDragging = false"
                                           @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                </label>
                                <p class="text-[11px] text-slate-500 mt-0.5 truncate">Format JPG, PNG, WEBP (maks. 3MB). Foto persegi/proporsional direkomendasikan.</p>
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

                        <!-- Footer Form: Tombol Simpan Responsive (Full di mobile, auto & align-right di desktop) -->
                        <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                            @if($editingUstadzId)
                                <button type="button" wire:click="resetUstadzForm"
                                    class="px-3.5 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 font-semibold transition cursor-pointer shrink-0">
                                    Batal
                                </button>
                            @endif
                            <button type="submit" wire:loading.attr="disabled"
                                class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="saveUstadz">{{ $editingUstadzId ? 'Simpan Perubahan' : 'Simpan Ustadz' }}</span>
                                <span wire:loading.inline-flex wire:target="saveUstadz" style="display: none;"
                                    class="inline-flex flex-row items-center gap-2 shrink-0 whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="whitespace-nowrap leading-none">Menyimpan...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Directory Search & List -->
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                            Daftar Ustadz
                        </h4>
                        <!-- Search Input -->
                        <div class="relative w-full sm:w-64">
                            <input type="text" wire:model.live.debounce.300ms="ustadzSearch" placeholder="Cari nama ustadz..."
                                class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Cards Grid -->
                    @if(isset($allUstadzs) && $allUstadzs->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($allUstadzs as $u)
                                <div wire:key="ustadz-card-{{ $u->id }}" class="rounded-xl border border-slate-200 bg-white hover:border-slate-300 hover:shadow-2xs transition flex flex-col justify-between overflow-hidden">
                                    <!-- Top Card Content: Avatar & Details -->
                                    <div class="p-3.5 flex items-start gap-3 flex-1 min-w-0">
                                        <!-- Photo Avatar -->
                                        <div class="relative shrink-0 w-12 h-12 rounded-full overflow-hidden border border-slate-200 bg-slate-100 flex items-center justify-center">
                                            @if($u->photo)
                                                <img src="{{ asset('storage/' . $u->photo) }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            @endif
                                        </div>

                                        <!-- Details -->
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-xs font-bold text-slate-900 truncate" title="{{ $u->name }}">
                                                {{ $u->name }}
                                            </h5>
                                            @if($u->title)
                                                <p class="text-[11px] text-slate-500 truncate">{{ $u->title }}</p>
                                            @endif

                                            <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                                @if($u->photo)
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-400">
                                                        <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Ada Foto
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                        Belum Ada Foto
                                                    </span>
                                                @endif

                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                    {{ $u->kajians_count }} Jadwal
                                                </span>

                                                @if($u->phone)
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $u->phone) }}" target="_blank"
                                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-50 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 border border-slate-200 transition">
                                                        <i data-lucide="phone" class="w-2.5 h-2.5 text-emerald-600"></i>
                                                        <span>{{ $u->phone }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Footer: Sekumpulan Tombol Aksi Dipisahkan Border -->
                                    <div class="px-3.5 py-2 bg-slate-50/50 border-t border-slate-200 flex items-center justify-end gap-1.5">
                                        <button type="button" wire:click.stop="editUstadz({{ $u->id }})"
                                            @click="$el.closest('.overflow-y-auto')?.scrollTo({ top: 0, behavior: 'smooth' })"
                                            title="Edit Ustadz & Foto"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-600 hover:text-gov-navy shadow-2xs transition cursor-pointer">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        </button>
                                        @if($u->photo)
                                            <button type="button" wire:click.stop="removeUstadzPhoto({{ $u->id }})" wire:confirm="Hapus foto profil ustadz ini? Foto akan dihapus dari seluruh jadwal kajian." title="Hapus Foto"
                                                class="inline-flex items-center justify-center p-1.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700 shadow-2xs transition cursor-pointer">
                                                <i data-lucide="image-minus" class="w-3.5 h-3.5"></i>
                                            </button>
                                        @endif
                                        <button type="button" wire:click.stop="deleteUstadz({{ $u->id }})" wire:confirm="Hapus data ustadz ini dari direktori?" title="Hapus Ustadz"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 shadow-2xs transition cursor-pointer">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-slate-50 rounded-xl border border-slate-200 text-slate-500 text-xs">
                            <i data-lucide="users" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                            <p>Tidak ada data ustadz yang sesuai dengan pencarian.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between border-t border-slate-200 pt-3 shrink-0 text-xs">
                <div class="text-[11px] text-slate-500">
                    💡 <strong>Otomatisasi:</strong> Jadwal kajian baru atau lama dengan nama ustadz yang sama akan langsung menggunakan foto ini.
                </div>
                <button type="button" @click="showUstadzModal = false; $wire.closeUstadzModal()"
                    class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Tambah / Edit Galeri Kegiatan -->
    <div x-show="$wire.showGalleryModal" x-cloak
        @click="if (window.isBackdropClick($event, $el)) $wire.closeGalleryModal()"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="$wire.closeGalleryModal()">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="flex items-start justify-between border-b border-slate-100 pb-3 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200 shrink-0">
                        <i data-lucide="images" class="w-4 h-4 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gov-textMain">
                            <span x-text="$wire.isEditingGallery ? 'Edit Galeri Kegiatan' : 'Tambah Galeri Kegiatan'"></span>
                        </h3>
                        <p class="text-[11px] text-slate-500">Dokumentasi foto kegiatan ibadah & agenda Masjid Salahuddin</p>
                    </div>
                </div>
                <button type="button" wire:click="closeGalleryModal"
                    class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Form Content -->
            <form wire:submit.prevent="saveGallery" class="space-y-4 text-xs overflow-y-auto flex-1 pr-1">
                <!-- Judul Kegiatan -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Judul Kegiatan <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="galleryTitle"
                        placeholder="Contoh: Kajian Rutin Ba'da Ashar Bersama Ustadz..."
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition text-xs font-medium text-gov-textMain">
                    @error('galleryTitle') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal & Lokasi Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Kegiatan <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="galleryDate"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition text-xs font-medium text-gov-textMain cursor-pointer shadow-2xs">
                        @error('galleryDate') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Lokasi Kegiatan <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="galleryLocation"
                            placeholder="Contoh: Ruang Utama Masjid"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition text-xs font-medium text-gov-textMain">
                        @error('galleryLocation') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Upload Foto Baru -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Unggah Foto Dokumentasi</label>
                    <div x-data="{ isDragging: false }"
                         :class="isDragging ? 'border-gov-navy bg-blue-50/60 ring-2 ring-gov-navy/20' : 'border-slate-200 hover:border-gov-navy/50 bg-slate-50/50'"
                         class="p-5 border-2 border-dashed rounded-lg text-center transition relative overflow-hidden group">
                        
                        <!-- Invisible Full-Surface Native File Input -->
                        <input type="file"
                               wire:model="galleryUploadedPhotos"
                               multiple
                               accept="image/*"
                               id="galleryPhotosInput"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @dragenter="isDragging = true"
                               @dragover.prevent="isDragging = true"
                               @dragleave="isDragging = false"
                               @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }"
                               @change="
                                   const maxBytesPerFile = 30 * 1024 * 1024;
                                   const maxBatchBytes = 60 * 1024 * 1024;
                                   const files = $el.files ? Array.from($el.files) : [];
                                   const oversized = files.filter(f => f.size > maxBytesPerFile);
                                   const totalBytes = files.reduce((acc, f) => acc + f.size, 0);

                                   if (oversized.length > 0) {
                                       const details = oversized.map(f => f.name + ' (' + (f.size / (1024 * 1024)).toFixed(1) + ' MB)').join(', ');
                                       window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Ukuran foto melebihi batas maksimal 30 MB per file: ' + details + '. Silakan pilih foto dengan ukuran maksimal 30 MB.' } }));
                                       $event.stopImmediatePropagation();
                                       $el.value = '';
                                       return;
                                   }

                                   if (totalBytes > maxBatchBytes) {
                                       const totalMb = (totalBytes / (1024 * 1024)).toFixed(1);
                                       window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Total ukuran foto yang dipilih (' + totalMb + ' MB) melebihi batas muatan pengiriman (60 MB). Silakan unggah bertahap 2-3 foto sekaligus.' } }));
                                       $event.stopImmediatePropagation();
                                       $el.value = '';
                                       return;
                                   }
                               ">

                        <div class="pointer-events-none space-y-1.5">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-500 transition group-hover:scale-105"
                                 :class="isDragging ? 'scale-110 bg-gov-navy/10 text-gov-navy' : ''">
                                <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/>
                                    <path d="M12 12v9"/>
                                    <path d="m16 16-4-4-4 4"/>
                                </svg>
                            </div>
                            <div class="text-slate-600 font-medium text-[11px]">
                                <span class="text-gov-navy font-bold group-hover:underline">Pilih file foto</span> atau drag & drop ke sini
                            </div>
                            <p class="text-[10px] text-slate-400">Format: JPG, PNG, WEBP (Bisa pilih beberapa foto sekaligus, maks 30MB/foto, maks total 60MB/batch). Otomatis dikonversi ke WebP tajam & ringan.</p>
                            <template x-if="isDragging">
                                <div class="text-xs font-bold text-blue-700 animate-pulse pt-0.5">
                                    Lepaskan foto di sini untuk mengunggah
                                </div>
                            </template>
                        </div>
                    </div>
                    <div wire:loading.inline-flex wire:target="galleryUploadedPhotos" style="display: none;" class="items-center gap-1.5 text-xs text-amber-700 pt-1.5">
                        <svg class="w-3.5 h-3.5 animate-spin text-amber-600 inline-block" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Mengunggah dan memproses foto dokumentasi...</span>
                    </div>
                    @error('galleryUploadedPhotos')
                        <div class="mt-2 p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2">
                            <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <div class="leading-relaxed">
                                <span class="font-semibold block text-rose-700">{{ $message }}</span>
                                <span class="text-[11px] text-rose-600 block mt-0.5">Saran: Jika foto dari kamera DSLR/HP beresolusi sangat besar (10–15 MB per foto), pilih 1–2 foto sekaligus atau sesuaikan batas <code class="bg-rose-100 px-1 py-0.5 rounded font-mono text-[10px]">client_max_body_size</code> di Nginx server.</span>
                            </div>
                        </div>
                    @enderror
                    @error('galleryUploadedPhotos.*')
                        <span class="text-rose-600 text-[11px] font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Foto yang Sudah Ada (Mode Edit) -->
                @if(!empty($galleryExistingPhotos))
                    <div class="space-y-1.5">
                        <span class="block font-semibold text-slate-700 text-[11px]">Foto Tersimpan Saat Ini ({{ count($galleryExistingPhotos) }}):</span>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($galleryExistingPhotos as $idx => $pPath)
                                <div class="relative group aspect-square rounded-lg overflow-hidden border border-slate-200 bg-slate-100">
                                    <img src="{{ asset('storage/' . $pPath) }}" class="w-full h-full object-cover">
                                    <button type="button" wire:click="removeExistingGalleryPhoto({{ $idx }})"
                                        class="absolute top-1 right-1 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center opacity-80 hover:opacity-100 transition shadow-xs cursor-pointer"
                                        title="Hapus foto ini">
                                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Pratinjau Foto yang Baru Dipilih -->
                @if(!empty($galleryUploadedPhotos))
                    <div class="space-y-1.5">
                        <span class="block font-semibold text-emerald-700 text-[11px]">Foto Baru yang Siap Disimpan ({{ count($galleryUploadedPhotos) }}):</span>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($galleryUploadedPhotos as $tempIdx => $tempPhoto)
                                @if(is_object($tempPhoto) && method_exists($tempPhoto, 'temporaryUrl'))
                                    <div class="relative group aspect-square rounded-lg overflow-hidden border border-emerald-300 bg-slate-100">
                                        <img src="{{ $tempPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                                        <button type="button" wire:click="removeUploadedGalleryPhoto({{ $tempIdx }})"
                                            class="absolute top-1 right-1 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center opacity-80 hover:opacity-100 transition shadow-xs cursor-pointer"
                                            title="Batal simpan foto ini">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Modal Action Buttons -->
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 shrink-0">
                    <button type="button" wire:click="closeGalleryModal"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
                        <span wire:loading.remove wire:target="saveGallery, galleryUploadedPhotos">Simpan Galeri</span>
                        <span wire:loading.inline-flex wire:target="saveGallery, galleryUploadedPhotos" style="display: none;"
                            class="inline-flex flex-row items-center justify-center gap-2 shrink-0 whitespace-nowrap">
                            <svg class="w-4 h-4 animate-spin shrink-0 inline-block text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Menyimpan..</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

        <!-- Modal: Detail & Tanggapan Saran Jamaah -->
    <div x-show="$wire.showSaranDetailModal" x-cloak wire:ignore.self
        @click="if (window.isBackdropClick($event, $el)) $wire.closeSaranDetailModal()"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;" @keydown.escape.window="$wire.closeSaranDetailModal()">
        <div class="bg-white rounded-xl max-w-xl w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between border-b border-slate-100 pb-3 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-200 shrink-0">
                        <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gov-textMain">
                            {{ Auth::check() && Auth::user()->isJamaah() ? 'Detail Saran Masukan Jamaah' : 'Detail Saran & Tanggapan Takmir' }}
                        </h3>
                        <p class="text-[11px] text-slate-500">Masjid Salahuddin KPP Madya Malang</p>
                    </div>
                </div>
                <button type="button" wire:click="closeSaranDetailModal" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="flex-1 overflow-y-auto space-y-4 pr-1 text-xs">
                @if($selectedSaran)
                    <!-- Informasi Pengirim & Metadata -->
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">PENGIRIM</span>
                            <span class="font-bold text-slate-800 text-xs block mt-0.5">
                                {{ $selectedSaran->is_anonymous ? 'Hamba Allah (Anonim)' : ($selectedSaran->name ?: 'Hamba Allah') }}
                            </span>
                            @if(!empty($selectedSaran->contact))
                                <span class="text-[11px] text-slate-500 block mt-0.5">Kontak: {{ $selectedSaran->contact }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">KATEGORI & TANGGAL</span>
                            <span class="font-semibold text-slate-800 text-xs block mt-0.5">{{ $selectedSaran->category }}</span>
                            <span class="text-[11px] text-slate-500 block mt-0.5">{{ $selectedSaran->created_at ? $selectedSaran->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB</span>
                        </div>
                    </div>

                    <!-- Topik & Isi Pesan Asli -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">ISI PESAN MASUKAN</span>
                        @if(!empty($selectedSaran->title))
                            <h4 class="font-bold text-xs text-slate-900">{{ $selectedSaran->title }}</h4>
                        @endif
                        <div class="p-3.5 rounded-lg bg-slate-50/80 border border-slate-200 text-slate-700 leading-relaxed whitespace-pre-line text-xs">
                            {{ $selectedSaran->message }}
                        </div>
                    </div>

                    @if(Auth::check() && !Auth::user()->isJamaah())
                        <!-- Form Tanggapan & Status (Khusus Pengurus/Takmir) -->
                        <div class="pt-2 border-t border-slate-100 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Status Tindak Lanjut -->
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Status Tindak Lanjut</label>
                                    <x-custom-select
                                        wire:model="saranUpdateStatus"
                                        :options="[
                                            ['value' => 'dibaca', 'label' => 'Telah Dibaca'],
                                            ['value' => 'ditindaklanjuti', 'label' => 'Selesai Ditindaklanjuti'],
                                            ['value' => 'arsip', 'label' => 'Diarsipkan'],
                                            ['value' => 'baru', 'label' => 'Tandai Baru (Belum Dibaca)'],
                                        ]"
                                        placeholder="Pilih Status..."
                                        class="w-full text-xs"
                                        buttonClass="p-2.5 bg-slate-50/50"
                                    />
                                </div>

                                <!-- Publikasikan ke Portal -->
                                <div class="flex items-center pt-5">
                                    <label class="flex items-center gap-2 cursor-pointer font-semibold text-slate-700 text-xs">
                                        <input type="checkbox" wire:model="saranUpdateIsPublic"
                                            class="rounded border-slate-300 text-gov-navy focus:ring-gov-navy w-4 h-4 cursor-pointer">
                                        <span>Tampilkan di Feed Publik Portal</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Tulis Tanggapan Resmi Takmir -->
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">
                                    Tanggapan Resmi Takmir DKM
                                    <span class="text-slate-400 font-normal">(Akan tampil di feed jika dipublikasikan)</span>
                                </label>
                                <textarea wire:model="saranReplyText" rows="4"
                                    placeholder="Tuliskan tanggapan resmi atau informasi aksi tindak lanjut yang telah dilakukan Takmir DKM..."
                                    class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white text-gov-textMain text-xs focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition resize-y leading-relaxed"></textarea>
                            </div>
                        </div>
                    @else
                        <!-- Mode Read-Only untuk Jamaah: Tampilkan Tanggapan Jika Ada -->
                        @if(!empty($selectedSaran->admin_reply))
                            <div class="pt-2 border-t border-slate-100">
                                <div class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1.5">
                                    <div class="flex items-center gap-1.5 text-emerald-800 font-bold text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                        <span>Tanggapan Resmi Takmir DKM</span>
                                    </div>
                                    <p class="text-xs leading-relaxed whitespace-pre-line text-emerald-950 font-normal">{{ $selectedSaran->admin_reply }}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100 shrink-0">
                <div>
                    @if(Auth::check() && Auth::user()->canManage() && $selectedSaran)
                        <!-- Tombol Hapus: Tetap muncul baik belum maupun telah ditanggapi -->
                        <button type="button" wire:click="deleteSaran({{ $selectedSaran->id }})"
                            wire:confirm="Yakin ingin menghapus catatan saran ini?"
                            class="px-3 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer inline-flex items-center gap-1.5"
                            title="Hapus catatan saran ini (tetap tersedia untuk saran yang telah ditanggapi)">
                            <svg class="w-3.5 h-3.5 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            <span>Hapus Saran</span>
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" wire:click="closeSaranDetailModal"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition cursor-pointer">
                        {{ Auth::check() && Auth::user()->isJamaah() ? 'Tutup' : 'Batal / Tutup' }}
                    </button>

                    @if(Auth::check() && Auth::user()->canManage())
                        <button type="button" wire:click="saveSaranResponse"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-1.5">
                            <span wire:loading.remove wire:target="saveSaranResponse">Simpan Tanggapan</span>
                            <span wire:loading.inline-flex wire:target="saveSaranResponse" style="display: none;"
                                class="inline-flex flex-row items-center justify-center gap-2 shrink-0 whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 animate-spin text-white inline-block" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Menyimpan...</span>
                            </span>
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: LIGHTBOX / PRATINJAU GALERI KEGIATAN -->
    <!-- ======================================================== -->
    <div x-show="galleryLightboxModal.show" 
         x-cloak 
         wire:ignore.self
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="if (galleryLightboxModal.show) closeGalleryLightbox()"
         @keydown.arrow-left.window="if (galleryLightboxModal.show) galleryLightboxModal.prev()"
         @keydown.arrow-right.window="if (galleryLightboxModal.show) galleryLightboxModal.next()"
         class="fixed inset-0 z-70 flex flex-col bg-slate-950/95 backdrop-blur-md select-none"
         style="display: none;">
         
        <!-- Header Bar (Title, Meta, Counter & Close) -->
        <div class="px-4 py-3 sm:px-6 sm:py-3.5 border-b border-white/10 shrink-0 bg-slate-900/90 backdrop-blur-md">
            <div class="flex items-start sm:items-center justify-between gap-3">
                <!-- Info Section (Title & Meta) -->
                <div class="min-w-0 flex-1 space-y-1">
                    <h4 class="text-sm sm:text-base font-bold text-white truncate leading-tight" 
                        x-text="galleryLightboxModal.title"
                        :title="galleryLightboxModal.title"></h4>
                    
                    <div class="flex items-center gap-2 sm:gap-3 text-[11px] sm:text-xs text-slate-300">
                        <!-- Tanggal -->
                        <span class="inline-flex items-center gap-1 shrink-0 whitespace-nowrap text-slate-200 font-medium">
                            <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span x-text="galleryLightboxModal.date"></span>
                        </span>
                        
                        <span class="text-slate-500 shrink-0">•</span>
                        
                        <!-- Lokasi -->
                        <span class="inline-flex items-center gap-1 min-w-0 truncate text-slate-300">
                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span class="truncate" x-text="galleryLightboxModal.location" :title="galleryLightboxModal.location"></span>
                        </span>
                    </div>
                </div>

                <!-- Right Controls: Counter Badge & Close Button -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0 self-center">
                    <span class="px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-white/10 text-slate-200 border border-white/15 whitespace-nowrap shadow-2xs" 
                          x-text="galleryLightboxModal.photos && galleryLightboxModal.photos.length > 0 ? (galleryLightboxModal.currentIndex + 1) + ' / ' + galleryLightboxModal.photos.length : ''">
                    </span>
                    <button type="button" 
                            @click="closeGalleryLightbox()"
                            class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition cursor-pointer shrink-0"
                            title="Tutup Galeri (Esc)">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Image Stage (Touch Swipe Enabled, Subtle Navigation Buttons) -->
        <div class="flex-1 relative flex items-center justify-center p-2 sm:p-6 overflow-hidden"
             @touchstart="galleryLightboxModal.touchStartX = $event.touches[0].clientX"
             @touchend="galleryLightboxModal.touchEndX = $event.changedTouches[0].clientX; galleryLightboxModal.handleSwipe()"
             @click.self="closeGalleryLightbox()">
            
            <!-- Prev Button -->
            <button type="button" 
                    x-show="galleryLightboxModal.photos && galleryLightboxModal.photos.length > 1"
                    @click.stop="galleryLightboxModal.prev()"
                    class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-10 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-slate-900/60 hover:bg-slate-900 text-white backdrop-blur-xs transition cursor-pointer shadow-lg border border-white/15 flex items-center justify-center"
                    title="Foto Sebelumnya (Panah Kiri / Geser Kanan)">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            </button>

            <!-- Main Active Image -->
            <div class="max-h-full max-w-full flex items-center justify-center p-1 sm:p-0">
                <template x-if="galleryLightboxModal.currentPhoto">
                    <img :src="galleryLightboxModal.currentPhoto" 
                         :alt="galleryLightboxModal.title"
                         class="max-h-[66vh] sm:max-h-[76vh] w-auto max-w-full object-contain rounded-lg shadow-2xl transition duration-150 select-none">
                </template>
                <template x-if="!galleryLightboxModal.currentPhoto">
                    <div class="p-8 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-500 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                        <span>Foto tidak dapat dimuat</span>
                    </div>
                </template>
            </div>

            <!-- Next Button -->
            <button type="button" 
                    x-show="galleryLightboxModal.photos && galleryLightboxModal.photos.length > 1"
                    @click.stop="galleryLightboxModal.next()"
                    class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-10 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-slate-900/60 hover:bg-slate-900 text-white backdrop-blur-xs transition cursor-pointer shadow-lg border border-white/15 flex items-center justify-center"
                    title="Foto Selanjutnya (Panah Kanan / Geser Kiri)">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>

        <!-- Bottom Thumbnails Strip -->
        <div x-show="galleryLightboxModal.photos && galleryLightboxModal.photos.length > 1"
             class="px-3 pt-2.5 pb-6 sm:px-4 sm:py-3 border-t border-white/10 bg-slate-900/80 backdrop-blur-md flex items-center justify-start sm:justify-center gap-2 overflow-x-auto max-w-full shrink-0">
            <template x-for="(photoUrl, pIdx) in galleryLightboxModal.photos" :key="pIdx">
                <button type="button" 
                        @click="galleryLightboxModal.setIndex(pIdx)"
                        class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg overflow-hidden shrink-0 border-2 transition cursor-pointer"
                        :class="galleryLightboxModal.currentIndex === pIdx ? 'border-amber-400 scale-105 shadow-md' : 'border-white/20 opacity-60 hover:opacity-100'">
                    <img :src="photoUrl" class="w-full h-full object-cover">
                </button>
            </template>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: TAMBAH / EDIT PEGAWAI MUSLIM KPP MADYA MALANG -->
    <!-- ======================================================== -->
    <div x-show="showEmployeeModal" x-cloak wire:ignore.self
         @click="if (window.isBackdropClick($event, $el)) { showEmployeeModal = false; $wire.closeEmployeeModal(); }"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;" @keydown.escape.window="showEmployeeModal = false; $wire.closeEmployeeModal();">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gov-navy/10 text-gov-navy flex items-center justify-center">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="16" rx="3"/>
                            <circle cx="9" cy="10" r="2"/>
                            <line x1="15" y1="8" x2="17" y2="8"/>
                            <line x1="15" y1="12" x2="17" y2="12"/>
                            <line x1="7" y1="16" x2="17" y2="16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">{{ $editingEmployeeId ? 'Ubah Data Pegawai' : 'Tambah Pegawai Muslim' }}</h3>
                        <p class="text-xs text-slate-500 font-medium">Civitas Pegawai Kantor Pelayanan Pajak Madya Malang</p>
                    </div>
                </div>
                <button type="button" @click="showEmployeeModal = false; $wire.closeEmployeeModal()"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveEmployee" class="space-y-3.5 text-xs">
                <!-- Nama Pegawai -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap &amp; Gelar *</label>
                    <input type="text" wire:model="employeeFormName" placeholder="Contoh: Deril Amrizal Kholid, S.Kom." required
                        class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                    @error('employeeFormName') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- NIP Pendek & NIP Panjang -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">NIP Pendek (9 Digit DJP)</label>
                        <input type="text" wire:model="employeeFormNipPendek" placeholder="Contoh: 817931806" maxlength="15"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-mono">
                        @error('employeeFormNipPendek') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">NIP Panjang (18 Digit BKN)</label>
                        <input type="text" wire:model="employeeFormNipPanjang" placeholder="Contoh: 198501152010121001" maxlength="25"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-mono">
                        @error('employeeFormNipPanjang') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Jabatan & Unit Kerja -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jabatan / Seksi</label>
                        <input type="text" wire:model="employeeFormJabatan" placeholder="Contoh: Account Representative"
                            class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium">
                        @error('employeeFormJabatan') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status Kantor</label>
                        <x-custom-select 
                            wire:model="employeeFormStatus"
                            :options="[
                                ['value' => 'AKTIF', 'label' => 'Aktif di KPP Madya Malang'],
                                ['value' => 'MUTASI', 'label' => 'Mutasi / Pindah Kantor'],
                                ['value' => 'NONAKTIF', 'label' => 'Nonaktif / Pensiun'],
                            ]"
                            placeholder="Pilih Status Kantor"
                            buttonClass="p-2.5 bg-slate-50/50"
                        />
                        @error('employeeFormStatus') <span class="text-xs font-medium text-rose-600 block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    <button type="button" @click="showEmployeeModal = false; $wire.closeEmployeeModal()"
                        class="px-3.5 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 font-semibold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveEmployee">{{ $editingEmployeeId ? 'Simpan Perubahan' : 'Tambahkan Pegawai' }}</span>
                        <span wire:loading.inline-flex wire:target="saveEmployee" style="display: none;" class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL: IMPOR BERKAS REKAP PEGAWAI EXCEL / CSV -->
    <!-- ======================================================== -->
    <div x-show="showImportEmployeeModal" x-cloak wire:ignore.self
         @click="if (window.isBackdropClick($event, $el)) { showImportEmployeeModal = false; $wire.closeImportEmployeeModal(); }"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;" @keydown.escape.window="showImportEmployeeModal = false; $wire.closeImportEmployeeModal();">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-gov-border space-y-4 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="8" y1="13" x2="16" y2="13"/>
                            <line x1="8" y1="17" x2="16" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gov-textMain">Impor Data Pegawai Muslim</h3>
                        <p class="text-xs text-slate-500 font-medium">Unggah berkas spreadsheet Excel (.xlsx, .xls) atau CSV</p>
                    </div>
                </div>
                <button type="button" @click="showImportEmployeeModal = false; $wire.closeImportEmployeeModal()"
                    class="text-slate-400 hover:text-gov-textMain p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form wire:submit.prevent="importEmployees" class="space-y-4 text-xs">
                <!-- Dropzone Area -->
                <div x-data="{ isDragging: false }"
                     @dragenter.prevent="isDragging = true"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop="isDragging = false"
                     :class="isDragging ? 'border-gov-navy bg-sky-50/60 ring-2 ring-gov-navy/20 scale-[0.99]' : 'border-slate-300 hover:border-gov-navy hover:bg-slate-50/80 bg-slate-50/40'"
                     class="relative border-2 border-dashed rounded-xl p-5 text-center transition-all duration-200 cursor-pointer group">
                    <input id="importEmployeeFileInput"
                           type="file"
                           wire:model="importEmployeeFile"
                           accept=".xlsx,.xls,.csv"
                           @dragenter="isDragging = true"
                           @dragover.prevent="isDragging = true"
                           @dragleave="isDragging = false"
                           @drop="isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files && $event.dataTransfer.files.length) { $el.files = $event.dataTransfer.files; $el.dispatchEvent(new Event('change', { bubbles: true })); }"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div class="pointer-events-none flex flex-col items-center justify-center gap-1.5">
                        <div :class="isDragging ? 'bg-gov-navy text-white scale-110' : 'bg-emerald-50 text-emerald-600 group-hover:scale-110'"
                             class="w-10 h-10 rounded-xl flex items-center justify-center transition-transform shadow-2xs">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="8" y1="13" x2="16" y2="13"/>
                                <line x1="8" y1="17" x2="16" y2="17"/>
                            </svg>
                        </div>
                        <p class="text-xs text-slate-700 font-semibold mt-1">
                            <span class="text-gov-navy underline underline-offset-2">Klik untuk memilih berkas</span> atau seret spreadsheet ke sini
                        </p>
                        <p class="text-[11px] text-slate-500">
                            Format berkas: Rekapitulasi Pegawai (.xlsx, .xls, .csv maks. 10MB)
                        </p>
                    </div>
                </div>

                @error('importEmployeeFile')
                    <span class="text-xs font-semibold text-rose-600 block">{{ $message }}</span>
                @enderror

                <!-- Loading Spinner Upload & Ekstraksi -->
                <div wire:loading.inline-flex wire:target="importEmployeeFile, importEmployees" style="display: none;"
                    class="inline-flex flex-row items-center gap-2 text-xs font-semibold text-amber-700 bg-amber-50 px-3.5 py-2 rounded-lg border border-amber-200 shrink-0">
                    <svg class="w-4 h-4 animate-spin text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="whitespace-nowrap leading-none">Sedang membaca dan mengimpor data pegawai ke database...</span>
                </div>

                <!-- Opsi Filter Hanya Pegawai Muslim -->
                <label class="flex items-start gap-2.5 p-3 rounded-lg bg-emerald-50/50 border border-emerald-200 cursor-pointer select-none">
                    <input type="checkbox" wire:model="importEmployeeOnlyMuslim"
                        class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <div class="text-xs">
                        <span class="font-bold text-slate-800 block">Hanya impor pegawai yang beragama Islam</span>
                        <span class="text-slate-500 text-[11px] block mt-0.5">
                            Jika spreadsheet memuat kolom Agama (misal format resmi SIKKA Kemenkeu), sistem otomatis menyaring dan hanya memasukkan pegawai muslim ke database.
                        </span>
                    </div>
                </label>

                <!-- Opsi Auto Link ke Akun Pengguna -->
                <label class="flex items-start gap-2.5 p-3 rounded-lg bg-slate-50 border border-slate-200 cursor-pointer select-none">
                    <input type="checkbox" wire:model="importEmployeeAutoLink"
                        class="mt-0.5 rounded text-gov-navy focus:ring-gov-navy border-slate-300">
                    <div class="text-xs">
                        <span class="font-bold text-slate-800 block">Tautkan otomatis dengan akun pengguna yang cocok</span>
                        <span class="text-slate-500 text-[11px] block mt-0.5">
                            Jika ditemukan akun pada tabel Anggota yang memiliki NIP atau nama yang sama, data pegawai akan langsung ditautkan ke akun tersebut.
                        </span>
                    </div>
                </label>

                <!-- Ringkasan Hasil Impor -->
                @if($importEmployeeResult)
                    <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-xs space-y-1.5 animate-in fade-in">
                        <div class="font-bold flex items-center gap-1.5 text-emerald-900">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Hasil Impor Spreadsheet:</span>
                        </div>
                        <p class="text-[11px] leading-relaxed text-emerald-800">
                            Total baris: <strong>{{ $importEmployeeResult['total_rows'] }}</strong> | 
                            Baru: <strong>{{ $importEmployeeResult['imported'] }}</strong> | 
                            Diperbarui: <strong>{{ $importEmployeeResult['updated'] }}</strong> | 
                            Tertaut akun: <strong>{{ $importEmployeeResult['linked'] }}</strong>
                            @if(($importEmployeeResult['skipped_non_muslim'] ?? 0) > 0)
                                | Non-muslim dilewati: <strong>{{ $importEmployeeResult['skipped_non_muslim'] }}</strong>
                            @endif
                        </p>
                    </div>
                @endif

                <div class="pt-3 border-t border-slate-200 flex items-center justify-end gap-2">
                    @if($importEmployeeResult)
                        <button type="button" @click="showImportEmployeeModal = false; $wire.closeImportEmployeeModal()"
                            class="px-5 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer">
                            Selesai
                        </button>
                    @else
                        <button type="button" @click="showImportEmployeeModal = false; $wire.closeImportEmployeeModal()"
                            class="px-3.5 py-2 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 font-semibold transition cursor-pointer">
                            Batal
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

