<div class="space-y-6">

    <!-- ======================================================== -->
    <!-- DOKUMEN RESMI SK & LAMPIRAN (3 PDF) -->
    <!-- ======================================================== -->
    <div class="space-y-4">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            <!-- Dokumen 1: SK Takmir -->
            <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2 pb-2 border-b border-slate-100">
                        <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-400 leading-none">
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
                    <a href="{{ route('document.viewer', ['file' => asset('resources/' . ($takmirDocs['sk']['file'] ?? 'Surat_Keputusan_Takmir_KEP-48_KPP.1209_2026.pdf')), 'fallback' => url('/admin/kepengurusan')]) }}" 
                       onclick="if (window.innerWidth >= 768 && !(/iPhone|iPad|iPod|Android/i.test(navigator.userAgent))) { event.preventDefault(); window.open('{{ asset('resources/' . ($takmirDocs['sk']['file'] ?? 'Surat_Keputusan_Takmir_KEP-48_KPP.1209_2026.pdf')) }}', '_blank'); }"
                       class="h-9 px-3.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Lihat PDF</span>
                    </a>

                    @if(Auth::user()->canManage())
                        <button type="button"
                            wire:click="openUploadDoc('sk')"
                            class="h-9 px-3.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
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
                    <a href="{{ route('document.viewer', ['file' => asset('resources/' . ($takmirDocs['lampiran1']['file'] ?? 'Lampiran_SK_Takmir_KEP-48_KPP.1209_2026.pdf')), 'fallback' => url('/admin/kepengurusan')]) }}" 
                       onclick="if (window.innerWidth >= 768 && !(/iPhone|iPad|iPod|Android/i.test(navigator.userAgent))) { event.preventDefault(); window.open('{{ asset('resources/' . ($takmirDocs['lampiran1']['file'] ?? 'Lampiran_SK_Takmir_KEP-48_KPP.1209_2026.pdf')) }}', '_blank'); }"
                       class="h-9 px-3.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Lihat PDF</span>
                    </a>

                    @if(Auth::user()->canManage())
                        <button type="button"
                            wire:click="openUploadDoc('lampiran1')"
                            class="h-9 px-3.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
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
                    <a href="{{ route('document.viewer', ['file' => asset('resources/' . ($takmirDocs['lampiran2']['file'] ?? 'Tupoksi_Takmir_KEP-48_KPP.1209_2026.pdf')), 'fallback' => url('/admin/kepengurusan')]) }}" 
                       onclick="if (window.innerWidth >= 768 && !(/iPhone|iPad|iPod|Android/i.test(navigator.userAgent))) { event.preventDefault(); window.open('{{ asset('resources/' . ($takmirDocs['lampiran2']['file'] ?? 'Tupoksi_Takmir_KEP-48_KPP.1209_2026.pdf')) }}', '_blank'); }"
                       class="h-9 px-3.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>Lihat PDF</span>
                    </a>

                    @if(Auth::user()->canManage())
                        <button type="button"
                            wire:click="openUploadDoc('lampiran2')"
                            class="h-9 px-3.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="upload" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span>Unggah PDF Baru</span>
                        </button>
                    @endif
                </div>
            </div>

        </div>

    </div>

    <!-- ======================================================== -->
    <!-- RINGKASAN STRUKTUR KEPENGURUSAN AKTIF (READ-ONLY) -->
    <!-- ======================================================== -->
    <div class="bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
        
        <!-- Header Ringkasan -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-lg bg-gov-navy/10 text-gov-navy flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gov-textMain">Susunan Personalia Takmir Berjalan</h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Berdasarkan Keputusan Nomor <span class="font-mono font-semibold text-gov-navy">{{ $docSkNumber }}</span> • Periode <span class="font-semibold text-gov-textMain">{{ $docSkPeriode }}</span>
                    </p>
                </div>
            </div>

            @if(Auth::user()->canManage())
                <div class="flex items-center gap-2">
                    <button type="button"
                        wire:click="openTemplateKepModal"
                        class="h-9 px-3.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Buka Form Template KEP</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="p-5 space-y-6">
            
            <!-- Banner Edukasi -->
            <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/80 text-blue-900 text-xs flex items-start gap-3">
                <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                <div class="space-y-1">
                    <p class="font-semibold text-blue-900">Pengelolaan Kepengurusan Terintegrasi Surat KEP</p>
                    <p class="text-blue-700 text-[11px] leading-relaxed">
                        Seluruh nama pimpinan, sekretaris, bendahara, dan 4 bidang di bawah terhubung langsung dengan Surat Keputusan resmi. Untuk melakukan pergantian pengurus atau memperbarui periode, klik tombol <strong>Template Surat KEP Takmir</strong> di atas.
                    </p>
                </div>
            </div>

            <!-- Pimpinan Harian -->
            <div>
                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-gov-navy"></i>
                    <span>Pimpinan & Pengurus Harian</span>
                </h5>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                    <!-- Pembina -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">{{ $pembinaRole }}</span>
                        <div class="font-bold text-gov-textMain text-xs">{{ $pembinaName }}</div>
                        <div class="text-[11px] text-slate-500">{{ $pembinaTitle }}</div>
                    </div>

                    <!-- Ketua -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">{{ $ketuaRole }}</span>
                        <div class="font-bold text-gov-textMain text-xs">{{ $ketuaName }}</div>
                        <div class="text-[11px] text-emerald-600 font-medium">Koordinator Umum</div>
                    </div>

                    <!-- Wakil Ketua -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">{{ $wakilKetuaRole }}</span>
                        <div class="font-bold text-gov-textMain text-xs">{{ $wakilKetuaName }}</div>
                        <div class="text-[11px] text-slate-500">Pendamping Ketua</div>
                    </div>

                    <!-- Sekretaris & Bendahara -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">{{ $sekretarisRole }}</span>
                            <div class="font-semibold text-gov-textMain text-xs">
                                {!! nl2br(e($sekretarisNames)) !!}
                            </div>
                        </div>
                        <div class="pt-2 border-t border-slate-200/60">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">{{ $bendaharaRole }}</span>
                            <div class="font-semibold text-gov-textMain text-xs">
                                {!! nl2br(e($bendaharaNames)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Bidang Pengelola -->
            <div>
                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                    <i data-lucide="layers" class="w-3.5 h-3.5 text-gov-navy"></i>
                    <span>4 Bidang Pengelola Takmir</span>
                </h5>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    
                    <!-- Bidang 1 -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="font-bold text-gov-textMain text-xs">{{ $bidang0Name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Pengelola / Koordinator:</span>
                            <div class="font-semibold text-slate-700 text-xs mt-0.5">{!! nl2br(e($bidang0Pengelola)) !!}</div>
                        </div>
                        @if(trim($bidang0Anggota))
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Anggota:</span>
                                <div class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">{!! nl2br(e($bidang0Anggota)) !!}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Bidang 2 -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <div class="w-6 h-6 rounded-md bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                <i data-lucide="heart-handshake" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="font-bold text-gov-textMain text-xs">{{ $bidang1Name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Pengelola / Koordinator:</span>
                            <div class="font-semibold text-slate-700 text-xs mt-0.5">{!! nl2br(e($bidang1Pengelola)) !!}</div>
                        </div>
                        @if(trim($bidang1Anggota))
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Anggota:</span>
                                <div class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">{!! nl2br(e($bidang1Anggota)) !!}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Bidang 3 -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <div class="w-6 h-6 rounded-md bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="font-bold text-gov-textMain text-xs">{{ $bidang2Name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Pengelola / Koordinator:</span>
                            <div class="font-semibold text-slate-700 text-xs mt-0.5">{!! nl2br(e($bidang2Pengelola)) !!}</div>
                        </div>
                        @if(trim($bidang2Anggota))
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Anggota:</span>
                                <div class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">{!! nl2br(e($bidang2Anggota)) !!}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Bidang 4 -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <div class="w-6 h-6 rounded-md bg-rose-100 text-rose-800 flex items-center justify-center shrink-0">
                                <i data-lucide="heart" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="font-bold text-gov-textMain text-xs">{{ $bidang3Name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Pengelola / Koordinator:</span>
                            <div class="font-semibold text-slate-700 text-xs mt-0.5">{!! nl2br(e($bidang3Pengelola)) !!}</div>
                        </div>
                        @if(trim($bidang3Anggota))
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Anggota:</span>
                                <div class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">{!! nl2br(e($bidang3Anggota)) !!}</div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>
