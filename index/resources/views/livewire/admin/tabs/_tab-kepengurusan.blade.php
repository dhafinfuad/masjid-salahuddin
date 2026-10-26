<div class="space-y-6">

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
                        Berdasarkan Keputusan Nomor <span class="font-mono font-semibold text-gov-navy">{{ $docSkNumber }}</span> &bull; Periode <span class="font-semibold text-gov-textMain">{{ $docSkPeriode }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="p-5 space-y-6">

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
