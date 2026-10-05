<!-- ======================================================== -->
<!-- TAB: MANAJEMEN GALERI KEGIATAN MASJID -->
<!-- ======================================================== -->
<div class="space-y-4">
    <!-- Top Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-gov-border shadow-2xs">
        <div class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center border border-gov-border shrink-0">
                <i data-lucide="images" class="w-4 h-4 text-gov-navy"></i>
            </div>
            <div>
                <h3 class="font-bold text-base text-gov-textMain">Galeri Kegiatan Masjid</h3>
                <div class="text-xs text-gov-textMuted mt-0.5">
                    Kelola foto dokumentasi kegiatan ibadah, kajian, dan program sosial Masjid Salahuddin.
                </div>
            </div>
        </div>

        @if(Auth::user()->canManage())
            <div class="w-full sm:w-auto">
                <button type="button" wire:click="openCreateGalleryModal"
                    class="w-full sm:w-auto justify-center px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Galeri</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Filter & Toolbar Bar -->
    <div class="bg-white p-4 rounded-xl border border-gov-border shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <!-- Search Bar -->
        <div class="relative flex-1 max-w-md">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" wire:model.live.debounce.300ms="gallerySearch"
                placeholder="Cari judul kegiatan atau lokasi..."
                class="w-full pl-9 pr-3.5 py-1.5 border border-gov-border rounded-lg text-xs bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition">
        </div>

        <!-- Filter Tahun & Counter -->
        <div class="flex items-center gap-2.5">
            <div class="flex items-center gap-1.5 text-xs text-slate-500 shrink-0">
                <select wire:model.live="galleryYearFilter"
                    class="p-1.5 border border-gov-border rounded-lg bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white font-medium text-xs text-gov-textMain outline-none transition">
                    <option value="all">Semua Tahun</option>
                    @foreach($galleryYearsList ?? [date('Y')] as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Gallery Album Cards Grid -->
    @if(count($activityGalleries ?? []) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($activityGalleries as $gallery)
                @php
                    $galleryPayload = [
                        'id' => $gallery->id,
                        'title' => $gallery->title,
                        'date' => $gallery->formatted_date,
                        'location' => $gallery->location,
                        'photos' => $gallery->photo_urls,
                    ];
                @endphp
                <div wire:key="admin-gallery-card-{{ $gallery->id }}"
                    class="bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden flex flex-col justify-between hover:shadow-xs transition group">
                    
                    <!-- Cover Photo & Image Count -->
                    <div class="relative aspect-video bg-slate-100 overflow-hidden {{ $gallery->photos_count > 0 ? 'cursor-pointer group/img' : '' }}"
                        @if($gallery->photos_count > 0)
                            @click="openGalleryLightbox({{ Js::from($galleryPayload) }}, 0)"
                        @endif>
                        @if($gallery->cover_photo_url)
                            <img src="{{ $gallery->cover_photo_url }}" 
                                 alt="{{ $gallery->title }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            
                            <!-- Subtle Click / Zoom Hint Overlay -->
                            <div class="absolute inset-0 bg-slate-900/0 hover:bg-slate-900/20 transition-colors flex items-center justify-center pointer-events-none">
                                <span class="opacity-0 group-hover/img:opacity-100 transition-opacity p-2 rounded-full bg-slate-900/60 backdrop-blur-xs text-white shadow-md">
                                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                                </span>
                            </div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 gap-1 bg-slate-50">
                                <i data-lucide="image" class="w-8 h-8 text-slate-300"></i>
                                <span class="text-[11px]">Belum ada foto</span>
                            </div>
                        @endif

                        <!-- Photo count badge -->
                        @if($gallery->photos_count > 0)
                            <button type="button"
                                @click.stop="openGalleryLightbox({{ Js::from($galleryPayload) }}, 0)"
                                class="absolute top-2.5 right-2.5 inline-flex items-center gap-1 h-[20px] px-2 rounded-full text-[11px] font-semibold bg-slate-900/70 hover:bg-slate-900 backdrop-blur-xs text-white leading-none cursor-pointer transition shadow-xs"
                                title="Buka Galeri Foto">
                                <i data-lucide="camera" class="w-3 h-3"></i>
                                <span>{{ $gallery->photos_count }} Foto</span>
                            </button>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 space-y-2.5 flex-1 flex flex-col justify-between">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600 shrink-0"></i>
                                <span>{{ $gallery->formatted_date }}</span>
                            </div>

                            <h4 class="text-sm font-bold text-gov-textMain line-clamp-2 {{ $gallery->photos_count > 0 ? 'hover:text-gov-navy cursor-pointer transition' : '' }}" 
                                @if($gallery->photos_count > 0)
                                    @click="openGalleryLightbox({{ Js::from($galleryPayload) }}, 0)"
                                @endif
                                title="{{ $gallery->title }}">
                                {{ $gallery->title }}
                            </h4>

                            <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-amber-600 shrink-0"></i>
                                <span class="truncate" title="{{ $gallery->location }}">{{ $gallery->location }}</span>
                            </div>
                        </div>

                        <!-- Mini Thumbnail Strip -->
                        @if(!empty($gallery->photos) && count($gallery->photos) > 1)
                            <div class="pt-2 border-t border-slate-100 flex items-center gap-1.5 overflow-hidden">
                                @foreach(array_slice($gallery->photo_urls, 1, 4) as $subIdx => $subPhoto)
                                    <button type="button" 
                                        @click.stop="openGalleryLightbox({{ Js::from($galleryPayload) }}, {{ $subIdx + 1 }})"
                                        class="w-9 h-9 rounded-md overflow-hidden bg-slate-100 shrink-0 border border-slate-200 hover:border-gov-navy hover:scale-105 transition cursor-pointer"
                                        title="Buka foto ke-{{ $subIdx + 2 }}">
                                        <img src="{{ $subPhoto }}" class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                                @if(count($gallery->photos) > 5)
                                    <button type="button"
                                        @click.stop="openGalleryLightbox({{ Js::from($galleryPayload) }}, 5)"
                                        class="w-9 h-9 rounded-md bg-slate-100 hover:bg-slate-200 shrink-0 border border-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 transition cursor-pointer hover:border-gov-navy"
                                        title="Lihat seluruh foto galeri (+{{ count($gallery->photos) - 5 }})">
                                        +{{ count($gallery->photos) - 5 }}
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="px-4 py-2.5 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400 font-medium">ID #{{ $gallery->id }}</span>

                        <div class="flex items-center space-x-1.5">
                            @if(Auth::user()->canManage())
                                <button type="button" wire:click="openEditGalleryModal({{ $gallery->id }})"
                                    class="inline-flex items-center justify-center p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 shadow-2xs transition cursor-pointer"
                                    title="Edit Galeri Kegiatan">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-amber-600"></i>
                                </button>
                                <button type="button"
                                    @click="openDeleteModal({{ Js::from([
                                        'action' => 'deleteGallery',
                                        'id' => $gallery->id,
                                        'title' => 'Hapus Galeri Kegiatan',
                                        'message' => 'Apakah Anda yakin ingin menghapus album galeri kegiatan ini beserta seluruh fotonya?',
                                        'itemName' => $gallery->title . ' (' . $gallery->formatted_date . ')',
                                    ]) }})"
                                    class="inline-flex items-center justify-center p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 shadow-2xs transition cursor-pointer"
                                    title="Hapus Galeri Kegiatan">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-600"></i>
                                </button>
                            @else
                                <span class="text-slate-400 italic text-xs">Read-only</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gov-border shadow-2xs p-10 text-center text-slate-400 space-y-2">
            <i data-lucide="images" class="w-10 h-10 text-slate-300 mx-auto"></i>
            <p class="text-sm font-semibold text-slate-600">Belum ada galeri kegiatan</p>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Dokumentasi foto kegiatan masjid belum tersedia atau tidak sesuai dengan kata kunci pencarian.
            </p>
            @if(Auth::user()->canManage())
                <div class="pt-2">
                    <button type="button" wire:click="openCreateGalleryModal"
                        class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Dokumentasi Baru</span>
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>
