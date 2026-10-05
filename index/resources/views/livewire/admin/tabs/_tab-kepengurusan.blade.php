<div class="space-y-6" x-data="{ takmirSection: 'dokumen' }">



    <!-- Section Switcher Tabs (Ide 3: Minimal Underline Tabs) -->
    <div class="border-b border-slate-300">
        <nav class="grid grid-cols-3 sm:flex sm:items-center sm:gap-2 text-xs" aria-label="Takmir Section Tabs">
            <button type="button"
                @click="takmirSection = 'dokumen'; $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                class="py-2.5 px-1 sm:px-3.5 border-b-2 rounded-t-lg transition flex items-center justify-center cursor-pointer whitespace-nowrap -mb-px text-center"
                :class="takmirSection === 'dokumen' ? 'border-gov-navy text-gov-navy font-bold' : 'border-transparent text-slate-500 hover:text-gov-navy hover:border-slate-300 hover:bg-slate-50 font-medium'">
                <span><span class="sm:hidden">Dokumen SK</span><span class="hidden sm:inline">Dokumen Resmi SK & Lampiran (3 PDF)</span></span>
            </button>

            <button type="button"
                @click="takmirSection = 'struktur'; $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                class="py-2.5 px-1 sm:px-3.5 border-b-2 rounded-t-lg transition flex items-center justify-center cursor-pointer whitespace-nowrap -mb-px text-center"
                :class="takmirSection === 'struktur' ? 'border-gov-navy text-gov-navy font-bold' : 'border-transparent text-slate-500 hover:text-gov-navy hover:border-slate-300 hover:bg-slate-50 font-medium'">
                <span><span class="sm:hidden">Susunan Pengurus</span><span class="hidden sm:inline">Susunan Pengurus Takmir</span></span>
            </button>

            <button type="button"
                @click="takmirSection = 'tupoksi'; $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                class="py-2.5 px-1 sm:px-3.5 border-b-2 rounded-t-lg transition flex items-center justify-center cursor-pointer whitespace-nowrap -mb-px text-center"
                :class="takmirSection === 'tupoksi' ? 'border-gov-navy text-gov-navy font-bold' : 'border-transparent text-slate-500 hover:text-gov-navy hover:border-slate-300 hover:bg-slate-50 font-medium'">
                <span><span class="sm:hidden">Tupoksi</span><span class="hidden sm:inline">Penjabaran Tugas & Wewenang (Tupoksi)</span></span>
            </button>
        </nav>
    </div>

    <!-- ======================================================== -->
    <!-- BAGIAN 1: DOKUMEN RESMI SK & LAMPIRAN (PDF) -->
    <!-- ======================================================== -->
    <div x-show="takmirSection === 'dokumen'" class="space-y-5">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            <!-- Dokumen 1: SK Takmir -->
            <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2 pb-2 border-b border-slate-100">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 leading-none">
                            SK Penetapan Resmi
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono">PDF Dokumen</span>
                    </div>

                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-gov-textMain">{{ $docSkTitle }}</h4>
                        <p class="text-xs text-slate-500">Nomor: <span class="font-semibold text-gov-textMain">{{ $docSkNumber }}</span></p>
                        <p class="text-[11px] text-slate-400">Ditetapkan: {{ $docSkDate }}</p>
                    </div>

                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-1">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-medium text-slate-500">Berkas File:</span>
                            <span class="font-mono text-gov-navy truncate max-w-[150px]">{{ $takmirDocs['sk']['file'] ?? 'Surat_Keputusan_Takmir_KEP-48_KPP.1209_2026.pdf' }}</span>
                        </div>
                        @if(!empty($takmirDocs['sk']['updated_at']))
                            <div class="text-[10px] text-slate-400">Diperbarui: {{ $takmirDocs['sk']['updated_at'] }} ({{ $takmirDocs['sk']['file_size'] ?? '' }})</div>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ asset('resources/' . ($takmirDocs['sk']['file'] ?? 'Surat_Keputusan_Takmir_KEP-48_KPP.1209_2026.pdf')) }}" 
                       target="_blank"
                       class="px-3 py-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Lihat PDF</span>
                    </a>

                    @if(Auth::user()->canManage())
                        <button type="button"
                            wire:click="openUploadDoc('sk')"
                            class="px-3 py-1.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="upload" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>Unggah PDF Baru</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Dokumen 2: Lampiran I -->
            <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2 pb-2 border-b border-slate-100">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 leading-none">
                            Lampiran I
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono">PDF Dokumen</span>
                    </div>

                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-gov-textMain">{{ $docLampiran1Title }}</h4>
                        <p class="text-xs text-slate-500">Daftar nama lengkap susunan pimpinan, sekretaris, bendahara, & 4 bidang takmir.</p>
                        <p class="text-[11px] text-slate-400">Status: Sah Terlampir SK</p>
                    </div>

                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-1">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-medium text-slate-500">Berkas File:</span>
                            <span class="font-mono text-gov-navy truncate max-w-[150px]">{{ $takmirDocs['lampiran1']['file'] ?? 'Lampiran_SK_Takmir_KEP-48_KPP.1209_2026.pdf' }}</span>
                        </div>
                        @if(!empty($takmirDocs['lampiran1']['updated_at']))
                            <div class="text-[10px] text-slate-400">Diperbarui: {{ $takmirDocs['lampiran1']['updated_at'] }} ({{ $takmirDocs['lampiran1']['file_size'] ?? '' }})</div>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ asset('resources/' . ($takmirDocs['lampiran1']['file'] ?? 'Lampiran_SK_Takmir_KEP-48_KPP.1209_2026.pdf')) }}" 
                       target="_blank"
                       class="px-3 py-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Lihat PDF</span>
                    </a>

                    @if(Auth::user()->canManage())
                        <button type="button"
                            wire:click="openUploadDoc('lampiran1')"
                            class="px-3 py-1.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="upload" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>Unggah PDF Baru</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Dokumen 3: Lampiran II Tupoksi -->
            <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2 pb-2 border-b border-slate-100">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 leading-none">
                            Lampiran II (Tupoksi)
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono">PDF Dokumen</span>
                    </div>

                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-gov-textMain">{{ $docLampiran2Title }}</h4>
                        <p class="text-xs text-slate-500">Uraian rincian tugas pokok & wewenang Pembina, Pimpinan, dan 4 Bidang Pengelola.</p>
                        <p class="text-[11px] text-slate-400">Status: Sah Terlampir SK</p>
                    </div>

                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-1">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-medium text-slate-500">Berkas File:</span>
                            <span class="font-mono text-gov-navy truncate max-w-[150px]">{{ $takmirDocs['lampiran2']['file'] ?? 'Tupoksi_Takmir_KEP-48_KPP.1209_2026.pdf' }}</span>
                        </div>
                        @if(!empty($takmirDocs['lampiran2']['updated_at']))
                            <div class="text-[10px] text-slate-400">Diperbarui: {{ $takmirDocs['lampiran2']['updated_at'] }} ({{ $takmirDocs['lampiran2']['file_size'] ?? '' }})</div>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ asset('resources/' . ($takmirDocs['lampiran2']['file'] ?? 'Tupoksi_Takmir_KEP-48_KPP.1209_2026.pdf')) }}" 
                       target="_blank"
                       class="px-3 py-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Lihat PDF</span>
                    </a>

                    @if(Auth::user()->canManage())
                        <button type="button"
                            wire:click="openUploadDoc('lampiran2')"
                            class="px-3 py-1.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="upload" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>Unggah PDF Baru</span>
                        </button>
                    @endif
                </div>
            </div>

        </div>

        <!-- Form Edit Informasi Metadata SK -->
        <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-gov-navy shrink-0">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gov-textMain">Perbarui Metadata & Identitas Dokumen SK</h4>
                    <p class="text-xs text-slate-500">Ubah nomor keputusan resmi, tanggal penetapan, dan perihal dokumen.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700">Nomor Keputusan (SK)</label>
                    <input type="text" wire:model.defer="docSkNumber" 
                        class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium">
                </div>

                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700">Tanggal Penetapan</label>
                    <input type="text" wire:model.defer="docSkDate" 
                        class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium">
                </div>

                <div class="space-y-1.5 sm:col-span-2 lg:col-span-1">
                    <label class="font-bold text-slate-700">Judul Utama SK</label>
                    <input type="text" wire:model.defer="docSkTitle" 
                        class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium">
                </div>

                <div class="sm:col-span-2 lg:col-span-3 space-y-1.5">
                    <label class="font-bold text-slate-700">Perihal / Deskripsi Singkat SK</label>
                    <textarea wire:model.defer="docSkDescription" rows="2"
                        class="w-full px-3 py-2 rounded-lg border border-gov-border bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy text-xs font-medium"></textarea>
                </div>
            </div>

            @if(Auth::user()->canManage())
                <div class="flex justify-end pt-3 border-t border-slate-100">
                    <button type="button"
                        wire:click="saveDocMetadata"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <div wire:loading.inline-flex wire:target="saveDocMetadata" style="display: none;" class="inline-flex flex-row items-center gap-2 shrink-0 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 animate-spin shrink-0 inline-block text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Menyimpan...</span>
                        </div>
                        <span wire:loading.remove wire:target="saveDocMetadata" class="inline-flex items-center gap-1.5">
                            <i data-lucide="save" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>Simpan Metadata Dokumen</span>
                        </span>
                    </button>
                </div>
            @endif
        </div>

    </div>

    <!-- ======================================================== -->
    <!-- BAGIAN 2: SUSUNAN PENGURUS TAKMIR -->
    <!-- ======================================================== -->
    <div x-show="takmirSection === 'struktur'" x-cloak class="space-y-6">

        <form wire:submit.prevent="saveTakmirStructure" class="space-y-6">
            
            <!-- Action Bar -->
            <div class="bg-white p-4 rounded-xl border border-gov-border shadow-2xs">
                <h4 class="text-sm font-bold text-gov-textMain">Perbarui Personalia Pengurus Takmir</h4>
                <p class="text-xs text-slate-500">Ubah nama pimpinan, sekretaris, bendahara, serta pengelola & anggota 4 bidang.</p>
            </div>

            <!-- Card 1: Pembina & Pimpinan Takmir -->
            <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center font-bold text-xs shrink-0">
                        SK
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gov-textMain">Pembina & Pimpinan Utama Takmir</h4>
                        <p class="text-xs text-slate-500">Jabatan struktural pembina institusi serta Ketua dan Wakil Ketua Takmir.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <!-- Pembina -->
                    <div class="space-y-1.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-300 leading-none">
                            Pembina
                        </span>
                        <div>
                            <label class="text-[11px] font-bold text-slate-600">Jabatan Institusi</label>
                            <input type="text" wire:model.defer="pembinaTitle" 
                                class="w-full mt-1 px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-600">Nama Pejabat Pembina</label>
                            <input type="text" wire:model.defer="pembinaName" 
                                class="w-full mt-1 px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-bold text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:outline-none">
                        </div>
                    </div>

                    <!-- Ketua -->
                    <div class="space-y-1.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 leading-none">
                            Ketua Takmir
                        </span>
                        <div>
                            <label class="text-[11px] font-bold text-slate-600">Nama Ketua Takmir</label>
                            <input type="text" wire:model.defer="ketuaName" 
                                class="w-full mt-1 px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-bold text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:outline-none">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">Mengoordinasikan kepengurusan dan arahan kebijakan operasional masjid.</p>
                    </div>

                    <!-- Wakil Ketua -->
                    <div class="space-y-1.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 leading-none">
                            Wakil Ketua Takmir
                        </span>
                        <div>
                            <label class="text-[11px] font-bold text-slate-600">Nama Wakil Ketua Takmir</label>
                            <input type="text" wire:model.defer="wakilKetuaName" 
                                class="w-full mt-1 px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-bold text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:outline-none">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">Mendampingi kepemimpinan harian serta mewakili ketua saat berhalangan.</p>
                    </div>
                </div>
            </div>

            <!-- Card 2: Pengurus Harian (Sekretaris & Bendahara) -->
            <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center shrink-0">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gov-textMain">Sekretariat & Keuangan (Pengurus Harian)</h4>
                        <p class="text-xs text-slate-500">Masukkan nama pengurus (satu nama per baris apabila lebih dari 1 personil).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1.5 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 leading-none">
                                Sekretaris
                            </span>
                            <span class="text-[11px] text-slate-400">1 nama per baris</span>
                        </div>
                        <textarea wire:model.defer="sekretarisNames" rows="3"
                            class="w-full mt-1 px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <div class="space-y-1.5 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 leading-none">
                                Bendahara
                            </span>
                            <span class="text-[11px] text-slate-400">1 nama per baris</span>
                        </div>
                        <textarea wire:model.defer="bendaharaNames" rows="3"
                            class="w-full mt-1 px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>
                </div>
            </div>

            <!-- Card 3: 4 Bidang Pengelola & Anggota -->
            <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center shrink-0">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gov-textMain">Susunan 4 Bidang Pengelola & Anggota</h4>
                        <p class="text-xs text-slate-500">Kelola daftar pengelola (koordinator bidang) serta anggota personil tiap bidang.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                    
                    <!-- Bidang 0: Dakwah & PHBI -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                            <div class="w-7 h-7 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0">
                                <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                            </div>
                            <input type="text" wire:model.defer="bidang0Name"
                                class="w-full px-2.5 py-1 rounded-md border border-gov-border bg-white text-xs font-bold text-gov-textMain">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Pengelola Bidang (1 nama per baris):</label>
                            <textarea wire:model.defer="bidang0Pengelola" rows="3"
                                class="w-full px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none"></textarea>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Anggota Bidang (1 nama per baris):</label>
                            <textarea wire:model.defer="bidang0Anggota" rows="5"
                                class="w-full px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Bidang 1: Humas & Sosial -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                            <div class="w-7 h-7 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0">
                                <i data-lucide="heart-handshake" class="w-3.5 h-3.5"></i>
                            </div>
                            <input type="text" wire:model.defer="bidang1Name"
                                class="w-full px-2.5 py-1 rounded-md border border-gov-border bg-white text-xs font-bold text-gov-textMain">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Pengelola Bidang (1 nama per baris):</label>
                            <textarea wire:model.defer="bidang1Pengelola" rows="3"
                                class="w-full px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none"></textarea>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Anggota Bidang (1 nama per baris):</label>
                            <textarea wire:model.defer="bidang1Anggota" rows="5"
                                class="w-full px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Bidang 2: Rumah Tangga & Sarpras -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                            <div class="w-7 h-7 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0">
                                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                            </div>
                            <input type="text" wire:model.defer="bidang2Name"
                                class="w-full px-2.5 py-1 rounded-md border border-gov-border bg-white text-xs font-bold text-gov-textMain">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Pengelola Bidang (1 nama per baris):</label>
                            <textarea wire:model.defer="bidang2Pengelola" rows="3"
                                class="w-full px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none"></textarea>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Anggota Bidang (1 nama per baris):</label>
                            <textarea wire:model.defer="bidang2Anggota" rows="5"
                                class="w-full px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Bidang 3: Keputrian -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                            <div class="w-7 h-7 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0">
                                <i data-lucide="heart" class="w-3.5 h-3.5"></i>
                            </div>
                            <input type="text" wire:model.defer="bidang3Name"
                                class="w-full px-2.5 py-1 rounded-md border border-gov-border bg-white text-xs font-bold text-gov-textMain">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Pengelola Bidang (1 nama per baris):</label>
                            <textarea wire:model.defer="bidang3Pengelola" rows="3"
                                class="w-full px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none"></textarea>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-600 block mb-1">Anggota Bidang (1 nama per baris):</label>
                            <textarea wire:model.defer="bidang3Anggota" rows="5"
                                class="w-full px-3 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none"></textarea>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bottom Save Action Bar -->
            @if(Auth::user()->canManage())
                <div class="flex justify-end bg-white p-4 rounded-xl border border-gov-border shadow-2xs">
                    <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <div wire:loading.inline-flex wire:target="saveTakmirStructure" style="display: none;" class="inline-flex flex-row items-center gap-2 shrink-0 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 animate-spin shrink-0 inline-block text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Menyimpan...</span>
                        </div>
                        <span wire:loading.remove wire:target="saveTakmirStructure" class="inline-flex items-center gap-1.5">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>Simpan Susunan Pengurus</span>
                        </span>
                    </button>
                </div>
            @endif

        </form>

    </div>

    <!-- ======================================================== -->
    <!-- BAGIAN 3: PENJABARAN TUGAS & WEWENANG (TUPOKSI) -->
    <!-- ======================================================== -->
    <div x-show="takmirSection === 'tupoksi'" x-cloak class="space-y-6">

        <form wire:submit.prevent="saveTakmirTupoksi" class="space-y-6">
            
            <!-- Action Bar -->
            <div class="bg-white p-4 rounded-xl border border-gov-border shadow-2xs">
                <h4 class="text-sm font-bold text-gov-textMain">Penjabaran Tugas Pokok & Wewenang (Tupoksi)</h4>
                <p class="text-xs text-slate-500">Tuliskan rincian butir wewenang dan tugas takmir (1 poin uraian per baris).</p>
            </div>

            <!-- Tupoksi Pembina & Pimpinan Grid -->
            <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-4">
                <h4 class="text-sm font-bold text-gov-textMain pb-2 border-b border-slate-100">Tupoksi Pimpinan & Pengurus Harian</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <!-- Pembina -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-300 leading-none">
                            Tupoksi Pembina
                        </span>
                        <textarea wire:model.defer="pembinaTupoksi" rows="3"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <!-- Ketua -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 leading-none">
                            Tupoksi Ketua Takmir
                        </span>
                        <textarea wire:model.defer="ketuaTupoksi" rows="3"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <!-- Wakil Ketua -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 leading-none">
                            Tupoksi Wakil Ketua Takmir
                        </span>
                        <textarea wire:model.defer="wakilKetuaTupoksi" rows="3"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <!-- Sekretaris -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 leading-none">
                            Tupoksi Sekretaris
                        </span>
                        <textarea wire:model.defer="sekretarisTupoksi" rows="3"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <!-- Bendahara -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 md:col-span-2">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 leading-none">
                            Tupoksi Bendahara
                        </span>
                        <textarea wire:model.defer="bendaharaTupoksi" rows="3"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>
                </div>
            </div>

            <!-- Tupoksi 4 Bidang Grid -->
            <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs space-y-4">
                <h4 class="text-sm font-bold text-gov-textMain pb-2 border-b border-slate-100">Tupoksi 4 Bidang Pengelola</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="font-bold text-gov-textMain text-xs flex items-center gap-1.5">
                            <i data-lucide="book-open" class="w-3.5 h-3.5 text-gov-navy"></i>
                            <span>{{ $bidang0Name }}</span>
                        </div>
                        <textarea wire:model.defer="bidang0Tupoksi" rows="4"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="font-bold text-gov-textMain text-xs flex items-center gap-1.5">
                            <i data-lucide="heart-handshake" class="w-3.5 h-3.5 text-gov-navy"></i>
                            <span>{{ $bidang1Name }}</span>
                        </div>
                        <textarea wire:model.defer="bidang1Tupoksi" rows="4"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="font-bold text-gov-textMain text-xs flex items-center gap-1.5">
                            <i data-lucide="home" class="w-3.5 h-3.5 text-gov-navy"></i>
                            <span>{{ $bidang2Name }}</span>
                        </div>
                        <textarea wire:model.defer="bidang2Tupoksi" rows="4"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="font-bold text-gov-textMain text-xs flex items-center gap-1.5">
                            <i data-lucide="heart" class="w-3.5 h-3.5 text-gov-navy"></i>
                            <span>{{ $bidang3Name }}</span>
                        </div>
                        <textarea wire:model.defer="bidang3Tupoksi" rows="4"
                            class="w-full px-3 py-2 rounded-lg border border-gov-border bg-white text-xs font-medium focus:ring-1 focus:ring-gov-navy focus:outline-none leading-relaxed"></textarea>
                    </div>

                </div>
            </div>

            <!-- Bottom Save Action Bar -->
            @if(Auth::user()->canManage())
                <div class="flex justify-end bg-white p-4 rounded-xl border border-gov-border shadow-2xs">
                    <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center gap-2">
                        <div wire:loading.inline-flex wire:target="saveTakmirTupoksi" style="display: none;" class="inline-flex flex-row items-center gap-2 shrink-0 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 animate-spin shrink-0 inline-block text-white" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="whitespace-nowrap leading-none">Menyimpan...</span>
                        </div>
                        <span wire:loading.remove wire:target="saveTakmirTupoksi" class="inline-flex items-center gap-1.5">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>Simpan Rincian Tupoksi</span>
                        </span>
                    </button>
                </div>
            @endif

        </form>

    </div>

</div>
