<div class="space-y-6">

    <!-- Header Card Banner -->
    <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">ASPIRASI JAMAAH</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-gov-textMain mt-1">Saran & Kritik Jamaah</h2>
                <p class="text-xs text-slate-500 mt-0.5 max-w-2xl leading-relaxed">
                    Kelola masukan, pengaduan fasilitas, kebersihan, dan usulan kegiatan yang disampaikan jamaah melalui portal publik.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('portal.saran') }}" target="_blank"
                    class="px-3.5 py-2 text-xs rounded-lg border border-gov-border hover:bg-slate-50 text-slate-700 font-semibold transition shadow-2xs inline-flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    <span>Lihat Halaman Publik</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-xl p-4 sm:p-5 border border-gov-border shadow-2xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            <!-- Search Input -->
            <div class="sm:col-span-6 relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" wire:model.live.debounce.300ms="saranSearch"
                    placeholder="Cari pengirim, topik, kontak, atau isi saran..."
                    class="w-full pl-9 pr-3 py-2 text-xs border border-gov-border rounded-lg bg-slate-50/70 focus:bg-white text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition">
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-3">
                <select wire:model.live="saranStatusFilter"
                    class="w-full px-3 py-2 text-xs border border-gov-border rounded-lg bg-slate-50/70 hover:bg-slate-100/70 focus:bg-white text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition cursor-pointer">
                    <option value="all">Semua Status</option>
                    <option value="baru">Baru / Belum Dibaca</option>
                    <option value="dibaca">Telah Dibaca</option>
                    <option value="ditindaklanjuti">Ditindaklanjuti</option>
                    <option value="arsip">Diarsipkan</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div class="sm:col-span-3">
                <select wire:model.live="saranCategoryFilter"
                    class="w-full px-3 py-2 text-xs border border-gov-border rounded-lg bg-slate-50/70 hover:bg-slate-100/70 focus:bg-white text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition cursor-pointer">
                    <option value="all">Semua Kategori</option>
                    <option value="Fasilitas & Kebersihan">Fasilitas & Kebersihan</option>
                    <option value="Ibadah & Kajian">Ibadah & Kajian</option>
                    <option value="Pelayanan DKM">Pelayanan DKM</option>
                    <option value="Kas & Sosial">Kas & Sosial</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

        </div>
    </div>

    <!-- ======================================================== -->
    <!-- DESKTOP VIEW: CLEAN TABLE (NO CATEGORY BADGE & VERIFIED ICON IN AKSI) -->
    <!-- ======================================================== -->
    <div class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-gov-border uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4 whitespace-nowrap w-[200px]">Pengirim</th>
                        <th class="py-3 px-4 min-w-[340px]">Topik & Isi Masukan</th>
                        <th class="py-3 px-3 text-center whitespace-nowrap w-20">Publik</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap w-36">Tanggal</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($saranList as $item)
                        @php
                            $isBaru = ($item->status === 'baru');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $isBaru ? 'bg-amber-50/70 border-l-4 border-l-amber-500 font-medium' : '' }}">
                            
                            <!-- Pengirim (Hapus Badge Kategori pada Desktop) -->
                            <td class="py-3.5 px-4 align-top whitespace-nowrap">
                                <div class="font-bold text-gov-textMain leading-snug">
                                    {{ $item->is_anonymous ? 'Hamba Allah (Anonim)' : ($item->name ?: 'Hamba Allah') }}
                                </div>
                                @if(!empty($item->contact))
                                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1 font-normal">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <span>{{ $item->contact }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Topik & Isi Masukan (Bersih Tanpa Badge Status di Atasnya) -->
                            <td class="py-3.5 px-4 align-top">
                                <div class="space-y-1 max-w-2xl">
                                    @if(!empty($item->title))
                                        <div class="font-bold text-slate-800 text-xs leading-snug break-words">{{ $item->title }}</div>
                                    @endif
                                    <p class="text-slate-600 line-clamp-3 leading-relaxed font-normal text-xs break-words">{{ $item->message }}</p>

                                    @if(!empty($item->admin_reply))
                                        <div class="mt-2 p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-normal flex items-start gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                            <div class="min-w-0 flex-1 leading-snug break-words">
                                                <span class="font-semibold text-emerald-900">Tanggapan:</span> {{ $item->admin_reply }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Toggle Publik -->
                            <td class="py-3.5 px-3 align-top text-center whitespace-nowrap">
                                @if(Auth::check() && Auth::user()->canManage())
                                    <button type="button" wire:click="toggleSaranPublic({{ $item->id }})"
                                        class="p-1.5 rounded-lg transition cursor-pointer {{ $item->is_public ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-300 hover:text-slate-500 hover:bg-slate-100' }}"
                                        title="{{ $item->is_public ? 'Ditampilkan di Feed Publik Portal (Klik untuk sembunyikan)' : 'Tersembunyi dari Publik (Klik untuk tampilkan di portal)' }}">
                                        @if($item->is_public)
                                            <svg class="w-4 h-4 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        @else
                                            <svg class="w-4 h-4 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                                        @endif
                                    </button>
                                @else
                                    <span class="inline-block p-1.5 {{ $item->is_public ? 'text-emerald-600' : 'text-slate-300' }}" title="{{ $item->is_public ? 'Publik' : 'Privat' }}">
                                        @if($item->is_public)
                                            <svg class="w-4 h-4 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        @else
                                            <svg class="w-4 h-4 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                                        @endif
                                    </span>
                                @endif
                            </td>

                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 align-top text-center whitespace-nowrap">
                                <div class="font-medium text-slate-700 text-xs">{{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $item->created_at ? $item->created_at->translatedFormat('H:i') : '' }} WIB</div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 align-top text-right whitespace-nowrap w-32">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <!-- Tombol Tanggapi / Detail -->
                                    @if(Auth::check() && !Auth::user()->isJamaah())
                                        <!-- Tombol Tanggapi: Khusus Pengurus/Takmir -->
                                        <button type="button" wire:click="openSaranDetail({{ $item->id }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-[11px] font-bold shadow-2xs transition inline-flex items-center gap-1 cursor-pointer shrink-0"
                                            title="{{ !empty($item->admin_reply) ? 'Ubah tanggapan takmir' : 'Buka detail pesan & tulis tanggapan' }}">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                            <span>Tanggapi</span>
                                        </button>
                                    @else
                                        <!-- Role Jamaah: Hanya tombol Detail -->
                                        <button type="button" wire:click="openSaranDetail({{ $item->id }})"
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 transition inline-flex items-center gap-1 cursor-pointer shrink-0"
                                            title="Lihat rincian pesan">
                                            <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            <span>Detail</span>
                                        </button>
                                    @endif

                                    <!-- Tombol Hapus: Tetap muncul baik belum maupun telah ditanggapi -->
                                    @if(Auth::check() && Auth::user()->canManage())
                                        <button type="button" wire:click="deleteSaran({{ $item->id }})"
                                            wire:confirm="Yakin ingin menghapus catatan saran ini?"
                                            class="p-1.5 rounded-lg border border-slate-200 hover:border-rose-300 hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition cursor-pointer shrink-0"
                                            title="Hapus saran ini (tetap tersedia untuk saran yang telah ditanggapi)">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-4 text-center text-slate-400 space-y-2">
                                <svg class="w-8 h-8 mx-auto text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <div class="font-bold text-xs text-slate-600">Tidak ada data saran & kritik</div>
                                <p class="text-[11px] text-slate-400">Belum ada masukan jamaah untuk filter pencarian ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Desktop Pagination -->
        @if($saranList->hasPages())
            <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                {{ $saranList->links(data: ['scrollTo' => false]) }}
            </div>
        @endif

    </div>

    <!-- ======================================================== -->
    <!-- MOBILE VIEW: RESPONSIVE CARDS (md:hidden)                -->
    <!-- ======================================================== -->
    <div class="md:hidden space-y-3" wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage">
        @forelse($saranList as $item)
            @php
                $isBaru = ($item->status === 'baru');
            @endphp
            <div wire:key="saran-mobile-card-{{ $item->id }}"
                class="bg-white rounded-xl border border-gov-border p-4 shadow-2xs space-y-3 transition {{ $isBaru ? 'bg-amber-50/50 border-l-4 border-l-amber-500' : '' }}">
                
                <!-- Card Header: Kategori, Status & Tanggal -->
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $item->category_color_class }}">
                            {{ $item->category }}
                        </span>

                        @if($item->status === 'baru')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                Baru
                            </span>
                        @elseif($item->status === 'dibaca')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                Dibaca
                            </span>
                        @elseif($item->status === 'ditindaklanjuti')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                Selesai
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                Arsip
                            </span>
                        @endif
                    </div>

                    <div class="text-right shrink-0">
                        <span class="text-[11px] text-slate-400 font-medium">
                            {{ $item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-' }}
                        </span>
                    </div>
                </div>

                <!-- Sender & Public Toggle -->
                <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 text-xs">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0 border border-slate-200">
                            <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-slate-900 block truncate leading-tight">
                                {{ $item->is_anonymous ? 'Hamba Allah (Anonim)' : ($item->name ?: 'Hamba Allah') }}
                            </span>
                            @if(!empty($item->contact))
                                <span class="text-[11px] text-slate-500 block truncate leading-tight mt-0.5">{{ $item->contact }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Toggle Publikasi Mobile -->
                    <div class="shrink-0">
                        @if(Auth::check() && Auth::user()->canManage())
                            <button type="button" wire:click="toggleSaranPublic({{ $item->id }})"
                                class="px-2 py-1 rounded-lg text-[11px] font-semibold border transition cursor-pointer inline-flex items-center gap-1 {{ $item->is_public ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100' }}"
                                title="Ubah status publikasi feed portal">
                                @if($item->is_public)
                                    <svg class="w-3 h-3 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span>Publik</span>
                                @else
                                    <svg class="w-3 h-3 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                                    <span>Privat</span>
                                @endif
                            </button>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $item->is_public ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-500 border-slate-200' }}">
                                {{ $item->is_public ? 'Publik' : 'Privat' }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Message Content -->
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1 text-xs">
                    @if(!empty($item->title))
                        <h4 class="font-bold text-slate-900 leading-snug break-words">{{ $item->title }}</h4>
                    @endif
                    <p class="text-slate-700 leading-relaxed break-words font-normal">{{ $item->message }}</p>
                </div>

                <!-- Takmir Response if available -->
                @if(!empty($item->admin_reply))
                    <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-1">
                        <div class="flex items-center gap-1.5 text-emerald-800 font-bold text-[11px]">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <span>Tanggapan Takmir:</span>
                        </div>
                        <p class="text-xs text-emerald-950 font-normal leading-relaxed break-words">{{ $item->admin_reply }}</p>
                    </div>
                @endif

                <!-- Card Actions -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <div>
                        @if(Auth::check() && Auth::user()->canManage())
                            <button type="button" wire:click="deleteSaran({{ $item->id }})"
                                wire:confirm="Yakin ingin menghapus catatan saran ini?"
                                class="p-2 rounded-lg border border-slate-200 hover:border-rose-300 hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition cursor-pointer"
                                title="Hapus saran ini (tetap tersedia untuk saran yang telah ditanggapi)">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if(Auth::check() && !Auth::user()->isJamaah())
                            <button type="button" wire:click="openSaranDetail({{ $item->id }})"
                                class="px-3.5 py-1.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <span>Tanggapi</span>
                            </button>
                        @else
                            <button type="button" wire:click="openSaranDetail({{ $item->id }})"
                                class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <span>Detail</span>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="p-8 text-center bg-white rounded-xl border border-gov-border shadow-2xs space-y-2 text-slate-400">
                <svg class="w-8 h-8 mx-auto text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <div class="font-bold text-xs text-slate-600">Tidak ada data saran & kritik</div>
                <p class="text-[11px] text-slate-400">Belum ada masukan jamaah untuk filter pencarian ini.</p>
            </div>
        @endforelse

        <!-- Mobile Pagination -->
        @if($saranList->hasPages())
            <div class="p-3 bg-white rounded-xl border border-gov-border shadow-2xs">
                {{ $saranList->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

</div>