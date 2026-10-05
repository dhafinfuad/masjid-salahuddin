<div class="min-h-screen bg-gov-canvas text-gov-textMain flex flex-col selection:bg-gov-navy selection:text-white"
     x-data="{ 
         activeTab: @entangle('activeTab'),
         lightboxOpen: false,
         currentGallery: null,
         currentPhotoIndex: 0,
         touchStartX: 0,
         touchEndX: 0,
         openLightbox(gallery, index = 0) {
             this.currentGallery = gallery;
             this.currentPhotoIndex = index;
             this.lightboxOpen = true;
             document.body.style.overflow = 'hidden';
             this.$nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); });
         },
         closeLightbox() {
             this.lightboxOpen = false;
             this.currentGallery = null;
             this.currentPhotoIndex = 0;
             document.body.style.overflow = '';
         },
         nextPhoto() {
             if (!this.currentGallery || !this.currentGallery.photos || this.currentGallery.photos.length <= 1) return;
             this.currentPhotoIndex = (this.currentPhotoIndex + 1) % this.currentGallery.photos.length;
         },
         prevPhoto() {
             if (!this.currentGallery || !this.currentGallery.photos || this.currentGallery.photos.length <= 1) return;
             this.currentPhotoIndex = (this.currentPhotoIndex - 1 + this.currentGallery.photos.length) % this.currentGallery.photos.length;
         },
         handleSwipe() {
             const diff = this.touchStartX - this.touchEndX;
             if (Math.abs(diff) > 40) {
                 if (diff > 0) {
                     this.nextPhoto();
                 } else {
                     this.prevPhoto();
                 }
             }
         }
     }"
     @keydown.escape.window="if (lightboxOpen) closeLightbox()"
     @keydown.arrow-right.window="if (lightboxOpen) nextPhoto()"
     @keydown.arrow-left.window="if (lightboxOpen) prevPhoto()">

    <!-- Top Sticky Header Bar -->
    <header class="bg-white border-b border-gov-border sticky top-0 z-30 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 sm:h-16 flex items-center justify-between gap-4">
            
            <!-- Left: Back button & Title -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" wire:navigate 
                   class="p-2 rounded-lg border border-gov-border hover:bg-slate-50 text-slate-600 transition shadow-2xs inline-flex items-center justify-center cursor-pointer"
                   title="Kembali ke Beranda">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gov-textMain"></i>
                </a>
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-gov-textMain leading-tight">Profil Masjid</h1>
                    <p class="text-[11px] text-slate-500 leading-none mt-0.5">{{ $settings->name ?? 'Masjid Salahuddin' }} • KPP Madya Malang</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Header Card Banner -->
        <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">PROFIL & KEPENGURUSAN</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gov-textMain mt-1">
                        Profil Masjid, Kepengurusan & Galeri Kegiatan
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        Informasi lengkap identitas, sejarah, sarana prasarana, Surat Keputusan dan Struktur Pengurus Takmir, serta dokumentasi kegiatan {{ $settings->name ?? 'Masjid Salahuddin' }}.
                    </p>
                </div>
            </div>
        </div>

        <!-- 3 Tab Menu Navigation Card -->
        <div class="bg-white rounded-xl p-3 sm:p-4 border border-gov-border shadow-2xs flex flex-wrap items-center gap-2.5">
            <button type="button" 
                    @click="activeTab = 'profil'; $wire.set('activeTab', 'profil', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                    class="px-4 py-2 text-xs rounded-lg transition cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'profil' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-slate-50 border border-gov-border text-slate-700 hover:bg-slate-100 font-medium'">
                <i data-lucide="building" class="w-4 h-4"></i>
                <span>Profil Masjid</span>
            </button>
            <button type="button" 
                    @click="activeTab = 'pengurus'; $wire.set('activeTab', 'pengurus', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                    class="px-4 py-2 text-xs rounded-lg transition cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'pengurus' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-slate-50 border border-gov-border text-slate-700 hover:bg-slate-100 font-medium'">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Kepengurusan</span>
            </button>
            <button type="button" 
                    @click="activeTab = 'galeri'; $wire.set('activeTab', 'galeri', false); $nextTick(() => { if (window.createLucideIcons) window.createLucideIcons(); })"
                    class="px-4 py-2 text-xs rounded-lg transition cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'galeri' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-slate-50 border border-gov-border text-slate-700 hover:bg-slate-100 font-medium'">
                <i data-lucide="images" class="w-4 h-4"></i>
                <span>Galeri Kegiatan</span>
            </button>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 1: PROFIL MASJID -->
        <!-- ======================================================== -->
        <div x-show="activeTab === 'profil'" class="space-y-6">

            <!-- Card 1: Identitas & Informasi Utama -->
            <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-5">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pb-4 border-b border-slate-100">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-50 border border-gov-border flex items-center justify-center p-2 shrink-0 shadow-2xs">
                        <img src="{{ asset('resources/Logo Masjid Salahuddin.webp') }}" 
                             alt="Logo Masjid Salahuddin" 
                             class="w-full h-full object-contain"
                             onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-lg sm:text-xl font-bold text-gov-textMain">{{ $settings->name ?? 'Masjid Salahuddin' }} KPP Madya Malang</h3>
                        <p class="text-xs text-slate-500 leading-relaxed max-w-3xl">
                            Pusat peribadatan, kajian keislaman, dan pembinaan ukhuwah islamiyah bagi para pegawai di lingkungan Kantor Pelayanan Pajak Madya Malang serta masyarakat muslim di sekitarnya.
                        </p>
                    </div>
                </div>

                <!-- 4 Highlights Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-[11px] uppercase font-bold text-slate-500 block">KAPASITAS</span>
                            <span class="text-sm font-bold text-gov-textMain">100 <span class="text-xs font-normal text-slate-500">Jamaah</span></span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-[11px] uppercase font-bold text-slate-500 block">BERDIRI SEJAK</span>
                            <span class="text-sm font-bold text-gov-textMain">Tahun 2010</span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                            <i data-lucide="compass" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-[11px] uppercase font-bold text-slate-500 block">ARAH KIBLAT</span>
                            <span class="text-sm font-bold text-gov-textMain">{{ $settings->qibla_angle ?? '295.12' }}° <span class="text-xs font-normal text-slate-500">Utara</span></span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-[11px] uppercase font-bold text-slate-500 block">OPERASIONAL</span>
                            <span class="text-sm font-bold text-gov-textMain">04:00 — 22:00 <span class="text-xs font-normal text-slate-500">WIB</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Visi & Misi -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Visi -->
                <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-3">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Visi Masjid</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed bg-slate-50/70 p-4 rounded-xl border border-gov-border italic">
                        "Terwujudnya Masjid Salahuddin sebagai pusat pembinaan keimanan, ketakwaan, dan peribadatan yang makmur, berkeadaban, serta memperkokoh integritas, ukhuwah islamiyah, dan kepedulian sosial di lingkungan KPP Madya Malang dan sekitarnya."
                    </p>
                </div>

                <!-- Misi -->
                <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-3">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 shrink-0">
                            <i data-lucide="target" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Misi Utama</h3>
                    </div>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0 text-[10px] mt-0.5">1</span>
                            <span>Menyelenggarakan ibadah shalat berjamaah 5 waktu dan Shalat Jumat secara khusyuk, tertib, dan berkesinambungan.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0 text-[10px] mt-0.5">2</span>
                            <span>Mengembangkan kajian dakwah rutin, pembinaan Al-Qur'an (One Day One Juz), dan peringatan hari besar Islam (PHBI).</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0 text-[10px] mt-0.5">3</span>
                            <span>Mengelola dan menyalurkan amanah infaq rutin, santunan anak yatim, dan zakat secara transparan dan akuntabel.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0 text-[10px] mt-0.5">4</span>
                            <span>Menjaga kesucian, kebersihan, kenyamanan, serta kelayakan sarana dan prasarana ibadah bagi seluruh jamaah.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card 3: Sarana & Fasilitas Masjid -->
            <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shrink-0">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Fasilitas & Layanan Jamaah</h3>
                        <p class="text-xs text-slate-500">Sarana prasarana yang tersedia untuk menunjang kenyamanan ibadah sehari-hari</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                            <i data-lucide="wind" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gov-textMain">Ruang Shalat Sejuk Ber-AC</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Ruang utama dilengkapi penyejuk udara dan karpet tebal berkualitas untuk kenyamanan ibadah.</p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                            <i data-lucide="droplet" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gov-textMain">Tempat Wudhu & Toilet Higienis</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Fasilitas wudhu bersih terpisah untuk ikhwan dan akhwat dengan pasokan air lancar.</p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                            <i data-lucide="heart" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gov-textMain">Area Khusus Keputrian</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Shaf khusus jamaah wanita yang terjaga privasinya, bersih, serta dilengkapi mukena siap pakai.</p>
                        </div>
                    </div>


                    <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                            <i data-lucide="coffee" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gov-textMain">Kantin & Jumat Berkah</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Penyediaan hidangan Jumat Berkah untuk jamaah setelah shalat Jumat di area kantin.</p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-gov-border flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gov-textMain">Infaq Digital QRIS & Transfer</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Kemudahan penyaluran infaq dan shodaqoh secara non-tunai melalui QRIS dan rekening BSI resmi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Lokasi & Kontak DKM -->
            <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-700 shrink-0">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Alamat & Kontak DKM</h3>
                        <p class="text-xs text-slate-500">Informasi alamat sekretariat dan saluran komunikasi resmi pengurus masjid</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="p-4 rounded-xl bg-slate-50 border border-gov-border space-y-1.5">
                        <span class="text-[11px] font-bold uppercase text-slate-500 block">ALAMAT LENGKAP</span>
                        <p class="text-gov-textMain font-medium leading-relaxed">
                            {{ $settings->address ?? 'Komplek Araya Business Center, Jl. Raden Panji Suroso Kav. 1, Purwodadi, Kec. Blimbing, Kota Malang, Jawa Timur 65126' }}
                        </p>
                        <a href="https://maps.app.goo.gl/BrN7JLVPRbqhiN1r5" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gov-navy hover:underline pt-1">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            <span>Buka di Google Maps</span>
                        </a>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-gov-border space-y-1.5">
                        <span class="text-[11px] font-bold uppercase text-slate-500 block">TELEPON / WHATSAPP DKM</span>
                        <p class="text-gov-textMain font-bold text-sm">
                            {{ $settings->phone ?? '+62 877 1552 4369' }}
                        </p>
                        <span class="text-[11px] text-slate-500 block">Sekretariat Pengurus Takmir Masjid</span>
                        @if(!empty($settings->phone))
                            <a href="{{ wa_link($settings->phone) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 hover:underline pt-1">
                                <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                <span>Hubungi via WhatsApp</span>
                            </a>
                        @endif
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-gov-border space-y-1.5">
                        <span class="text-[11px] font-bold uppercase text-slate-500 block">EMAIL RESMI</span>
                        <p class="text-gov-textMain font-medium text-xs truncate">
                            {{ $settings->email ?? 'kontak@masjidsalahuddin.my.id' }}
                        </p>
                        <span class="text-[11px] text-slate-500 block">Pertanyaan, konfirmasi infaq, & administrasi</span>
                        <a href="mailto:{{ $settings->email ?? 'kontak@masjidsalahuddin.my.id' }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gov-navy hover:underline pt-1">
                            <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                            <span>Kirim Pesan Email</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- TAB 2: SUSUNAN PENGURUS & DOKUMEN SK -->
        <!-- ======================================================== -->
        <div x-show="activeTab === 'pengurus'" class="space-y-6" style="display: none;">

            <!-- Section 1: 3 Dokumen PDF Resmi (Unduh & Buka) -->
            <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                            <i data-lucide="file-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Dokumen Keputusan Resmi (PDF)</h3>
                            <p class="text-xs text-slate-500">Dasar hukum pengangkatan dan penetapan kepengurusan Takmir Masjid Salahuddin Periode 2026-2029</p>
                        </div>
                    </div>
                </div>

                <!-- 3 PDF Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($documents as $doc)
                        <div class="border border-gov-border rounded-xl p-4 bg-white hover:border-slate-400 transition flex flex-col justify-between space-y-3">
                            <div class="space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="w-9 h-9 rounded-xl bg-slate-50 border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                                        <i data-lucide="{{ $doc['icon'] }}" class="w-4 h-4"></i>
                                    </div>
                                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none {{ $doc['badge_color'] }}">
                                        {{ $doc['badge'] }}
                                    </span>
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-gov-textMain leading-snug">{{ $doc['title'] }}</h4>
                                    <p class="text-[11px] font-mono text-slate-500 mt-0.5">{{ $doc['number'] }}</p>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-3">
                                    {{ $doc['description'] }}
                                </p>
                            </div>

                            <!-- PDF Actions: Buka & Unduh -->
                            <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                                <a href="{{ asset('resources/' . $doc['file']) }}" target="_blank" rel="noopener noreferrer"
                                   class="flex-1 py-2 px-3 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold transition shadow-2xs inline-flex items-center justify-center gap-1.5 cursor-pointer text-center">
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                    <span>Buka Dokumen</span>
                                </a>
                                <a href="{{ asset('resources/' . $doc['file']) }}" download="{{ $doc['file'] }}"
                                   class="p-2 rounded-lg border border-gov-border bg-slate-50 hover:bg-slate-100 text-slate-700 transition shadow-2xs inline-flex items-center justify-center cursor-pointer shrink-0"
                                   title="Unduh Berkas PDF">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Section 2: Struktur Organisasi Takmir Periode 2026-2029 -->
            <div class="space-y-4">

                <!-- Pembina & Pimpinan Utama -->
                <div class="bg-white rounded-xl p-5 sm:p-6 border border-gov-border shadow-2xs space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <i data-lucide="crown" class="w-4 h-4 text-amber-500"></i>
                        <h3 class="text-sm sm:text-base font-bold text-gov-textMain">Pembina & Pimpinan Takmir</h3>
                    </div>

                    <!-- Pembina Card -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100/70 border border-amber-300 flex items-center justify-center text-gov-navy font-bold text-xs shrink-0">
                                SK
                            </div>
                            <div>
                                <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-300 leading-none">
                                    {{ $pengurus['pembina']['role'] }}
                                </span>
                                <h4 class="text-sm font-bold text-gov-textMain mt-1">{{ $pengurus['pembina']['name'] }}</h4>
                                <p class="text-xs text-slate-500">{{ $pengurus['pembina']['title'] }}</p>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 max-w-md sm:text-right">
                            Memberikan arahan umum, pembinaan keagamaan, serta pengawasan keselarasan program masjid.
                        </div>
                    </div>

                    <!-- Ketua, Wakil, Sekretaris, Bendahara Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 pt-1">
                        <!-- Ketua -->
                        <div class="p-3.5 rounded-xl border border-gov-border bg-white shadow-2xs space-y-1.5">
                            <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 leading-none">
                                {{ $pengurus['ketua']['role'] }}
                            </span>
                            <h4 class="text-sm font-bold text-gov-textMain">{{ $pengurus['ketua']['name'] }}</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Memimpin & mengoordinasikan seluruh aktivitas pelaksanaan program Takmir Masjid.
                            </p>
                        </div>

                        <!-- Wakil Ketua -->
                        <div class="p-3.5 rounded-xl border border-gov-border bg-white shadow-2xs space-y-1.5">
                            <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 leading-none">
                                {{ $pengurus['wakil_ketua']['role'] }}
                            </span>
                            <h4 class="text-sm font-bold text-gov-textMain">{{ $pengurus['wakil_ketua']['name'] }}</h4>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Mendampingi Ketua Takmir dan mewakili operasional kepemimpinan harian.
                            </p>
                        </div>

                        <!-- Sekretaris -->
                        <div class="p-3.5 rounded-xl border border-gov-border bg-white shadow-2xs space-y-1.5">
                            <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 leading-none">
                                {{ $pengurus['sekretaris']['role'] }}
                            </span>
                            <div class="space-y-0.5">
                                @foreach($pengurus['sekretaris']['names'] as $sName)
                                    <div class="text-xs font-bold text-gov-textMain">{{ $sName }}</div>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Mengelola administrasi persuratan, notulensi rapat, database, dan pengarsipan LPJ.
                            </p>
                        </div>

                        <!-- Bendahara -->
                        <div class="p-3.5 rounded-xl border border-gov-border bg-white shadow-2xs space-y-1.5">
                            <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 leading-none">
                                {{ $pengurus['bendahara']['role'] }}
                            </span>
                            <div class="space-y-0.5">
                                @foreach($pengurus['bendahara']['names'] as $bName)
                                    <div class="text-xs font-bold text-gov-textMain">{{ $bName }}</div>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Mengontrol seluruh kas penerimaan, pengeluaran, saldo infaq/zakat, & transparansi laporan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 4 Bidang Pengelola & Anggota -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($pengurus['bidang'] as $bid)
                        <div class="bg-white rounded-xl p-5 border border-gov-border shadow-2xs flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                <!-- Header Bidang -->
                                <div class="flex items-start gap-3 pb-3 border-b border-slate-100">
                                    <div class="w-9 h-9 rounded-xl bg-slate-50 border border-gov-border flex items-center justify-center text-gov-navy shrink-0 shadow-2xs">
                                        <i data-lucide="{{ $bid['icon'] }}" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gov-textMain leading-snug">{{ $bid['name'] }}</h4>
                                        <span class="text-[11px] text-slate-400">Pengelola Bidang & Tim Anggota</span>
                                    </div>
                                </div>

                                <!-- Pengelola (Koordinator) -->
                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5">
                                    <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider block">PENGELOLA BIDANG :</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($bid['pengelola'] as $pName)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-white border border-slate-200 text-gov-textMain shadow-2xs">
                                                {{ $pName }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Anggota -->
                                <div class="space-y-1.5">
                                    <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider block">ANGGOTA BIDANG :</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($bid['anggota'] as $aName)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-slate-50 border border-slate-100 text-slate-600">
                                                {{ $aName }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Tupoksi Ringkas -->
                            <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-500 space-y-1 bg-slate-50/50 p-3 rounded-lg">
                                <span class="font-bold text-slate-700 block text-[10px] uppercase">Tugas Pokok & Fungsi (Tupoksi):</span>
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($bid['tupoksi'] as $tp)
                                        <li class="leading-relaxed">{{ $tp }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

        </div>

        <!-- ======================================================== -->
        <!-- TAB 3: GALERI KEGIATAN -->
        <!-- ======================================================== -->
        <div x-show="activeTab === 'galeri'" class="space-y-6">

            <!-- Filter Toolbar (Search & Filter Tahun) -->
            <div class="bg-white rounded-xl p-4 sm:p-5 border border-gov-border shadow-2xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <!-- Search Input -->
                <div class="relative flex-1 max-w-md">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input type="text" 
                           wire:model.live.debounce.300ms="gallerySearch"
                           placeholder="Cari judul kegiatan atau lokasi..." 
                           class="w-full pl-9 pr-8 py-2 border border-gov-border rounded-lg text-xs bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white text-gov-textMain focus:ring-1 focus:ring-gov-navy focus:border-gov-navy outline-none transition">
                    @if($gallerySearch)
                        <button type="button" wire:click="$set('gallerySearch', '')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    @endif
                </div>

                <!-- Year Filter & Count text -->
                <div class="flex items-center justify-between sm:justify-end gap-3">
                    <div class="flex items-center gap-2 text-xs text-slate-600">
                        <select wire:model.live="galleryYear"
                                class="px-3 py-1.5 border border-gov-border rounded-lg bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white font-medium text-xs text-gov-textMain outline-none transition cursor-pointer">
                            <option value="all">Semua Tahun</option>
                            @foreach($galleryYears as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Galleries Grid -->
            @if(count($galleries) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($galleries as $gallery)
                        @php
                            $galleryPayload = [
                                'id' => $gallery->id,
                                'title' => $gallery->title,
                                'date' => $gallery->formatted_date,
                                'location' => $gallery->location,
                                'photos' => $gallery->photo_urls,
                            ];
                        @endphp
                        <div wire:key="portal-gallery-card-{{ $gallery->id }}"
                             class="bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden flex flex-col justify-between hover:shadow-md transition duration-200 group">
                            
                            <!-- Cover Image Container -->
                            <div class="relative aspect-video bg-slate-100 overflow-hidden cursor-pointer"
                                 @click="openLightbox({{ Js::from($galleryPayload) }}, 0)">
                                @if($gallery->cover_photo_url)
                                    <img src="{{ $gallery->cover_photo_url }}" 
                                         alt="{{ $gallery->title }}"
                                         loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 gap-1 bg-slate-50">
                                        <i data-lucide="image" class="w-8 h-8 text-slate-300"></i>
                                        <span class="text-[11px]">Belum ada foto</span>
                                    </div>
                                @endif

                                <!-- Subtle Photo count overlay on image corner -->
                                @if($gallery->photos_count > 0)
                                    <div class="absolute bottom-2.5 right-2.5 px-2 py-1 rounded-md text-[11px] font-medium bg-slate-900/75 backdrop-blur-xs text-white flex items-center gap-1.5 shadow-xs pointer-events-none">
                                        <i data-lucide="camera" class="w-3.5 h-3.5 text-slate-200"></i>
                                        <span>{{ $gallery->photos_count }} Foto</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Content Area: Judul, Tanggal, Lokasi -->
                            <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <!-- Tanggal Kegiatan -->
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600 shrink-0"></i>
                                        <span>{{ $gallery->formatted_date }}</span>
                                    </div>

                                    <!-- Judul Kegiatan -->
                                    <h3 class="text-sm sm:text-base font-bold text-gov-textMain line-clamp-2 leading-snug cursor-pointer hover:text-blue-700 transition"
                                        @click="openLightbox({{ Js::from($galleryPayload) }}, 0)"
                                        title="{{ $gallery->title }}">
                                        {{ $gallery->title }}
                                    </h3>

                                    <!-- Lokasi Kegiatan -->
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-amber-600 shrink-0"></i>
                                        <span class="truncate" title="{{ $gallery->location }}">{{ $gallery->location }}</span>
                                    </div>
                                </div>

                                <!-- Thumbnail Strip & Action Button -->
                                <div class="space-y-3 pt-3 border-t border-slate-100">
                                    @if(count($gallery->photo_urls) > 1)
                                        <div class="flex items-center gap-2 overflow-hidden">
                                            @foreach(array_slice($gallery->photo_urls, 1, 3) as $idx => $photoUrl)
                                                <button type="button" 
                                                        @click.stop="openLightbox({{ Js::from($galleryPayload) }}, {{ $idx + 1 }})"
                                                        class="w-11 h-11 rounded-lg overflow-hidden border border-slate-200 hover:border-gov-navy transition shrink-0 bg-slate-100 cursor-pointer">
                                                    <img src="{{ $photoUrl }}" class="w-full h-full object-cover">
                                                </button>
                                            @endforeach

                                            @if(count($gallery->photo_urls) > 4)
                                                <button type="button"
                                                        @click.stop="openLightbox({{ Js::from($galleryPayload) }}, 4)"
                                                        class="w-11 h-11 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 shrink-0 transition cursor-pointer">
                                                    +{{ count($gallery->photo_urls) - 4 }}
                                                </button>
                                            @endif
                                        </div>
                                    @endif

                                    <button type="button" 
                                            @click="openLightbox({{ Js::from($galleryPayload) }}, 0)"
                                            class="w-full py-2 px-3 rounded-lg border border-gov-border bg-gov-navy hover:bg-gov-navyHover text-xs font-semibold text-white transition inline-flex items-center justify-center gap-2 cursor-pointer shadow-2xs">
                                        <i data-lucide="images" class="w-3.5 h-3.5"></i>
                                        <span>Lihat Album Foto</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl border border-gov-border shadow-2xs p-12 text-center text-slate-400 space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto text-slate-400">
                        <i data-lucide="images" class="w-6 h-6"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-700">Tidak ada galeri kegiatan ditemukan</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            @if($gallerySearch || $galleryYear !== 'all')
                                Tidak ada foto kegiatan yang sesuai dengan kata kunci pencarian atau filter tahun yang dipilih.
                            @else
                                Belum ada dokumentasi foto kegiatan yang dipublikasikan.
                            @endif
                        </p>
                    </div>
                    @if($gallerySearch || $galleryYear !== 'all')
                        <div class="pt-2">
                            <button type="button" 
                                    wire:click="$set('gallerySearch', ''); $set('galleryYear', 'all')"
                                    class="px-3.5 py-1.5 rounded-lg border border-gov-border bg-slate-50 hover:bg-slate-100 text-xs font-semibold text-slate-700 transition cursor-pointer">
                                Reset Filter
                            </button>
                        </div>
                    @endif
                </div>
            @endif

        </div>

        <!-- Footer -->
        <footer class="bg-white rounded-xl border border-gov-border shadow-2xs p-4 sm:p-5 text-center space-y-1">
            <div class="font-bold text-xs text-gov-textMain">{{ $settings->name ?? 'Masjid Salahuddin' }} KPP Madya Malang</div>
            <p class="text-[11px] text-slate-500">
                {{ $settings->address ?? 'Komplek Araya Business Center, Jl. Raden Panji Suroso Kav. 1, Purwodadi, Kec. Blimbing, Kota Malang, Jawa Timur 65126' }}
            </p>
        </footer>

    </main>

    <!-- ======================================================== -->
    <!-- LIGHTBOX / ALBUM VIEWER MODAL -->
    <!-- ======================================================== -->
    <div x-show="lightboxOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex flex-col bg-slate-950/95 backdrop-blur-md select-none">
         
        <!-- Lightbox Header Bar (Mobile Optimized: Spacious Title, Single Line Date & Location, Compact Counter) -->
        <div class="px-4 py-3 sm:px-6 sm:py-3.5 border-b border-white/10 shrink-0 bg-slate-900/90 backdrop-blur-md">
            <div class="flex items-start sm:items-center justify-between gap-3">
                
                <!-- Info Section (Title & Meta) -->
                <div class="min-w-0 flex-1 space-y-1">
                    <h4 class="text-sm sm:text-base font-bold text-white truncate leading-tight" 
                        x-text="currentGallery ? currentGallery.title : ''"></h4>
                    
                    <div class="flex items-center gap-2 sm:gap-3 text-[11px] sm:text-xs text-slate-300">
                        <!-- Tanggal (Prevent ugly wrapping with whitespace-nowrap) -->
                        <span class="inline-flex items-center gap-1 shrink-0 whitespace-nowrap text-slate-200 font-medium">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-400 shrink-0"></i>
                            <span x-text="currentGallery ? currentGallery.date : ''"></span>
                        </span>
                        
                        <span class="text-slate-500 shrink-0">•</span>
                        
                        <!-- Lokasi (Safely truncates without pushing the date) -->
                        <span class="inline-flex items-center gap-1 min-w-0 truncate text-slate-300">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-amber-400 shrink-0"></i>
                            <span class="truncate" x-text="currentGallery ? currentGallery.location : ''"></span>
                        </span>
                    </div>
                </div>

                <!-- Right Controls: Counter Badge & Close Button -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0 self-center">
                    <span class="px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-semibold bg-white/10 text-slate-200 border border-white/15 whitespace-nowrap shadow-2xs" 
                          x-text="currentGallery && currentGallery.photos ? (currentPhotoIndex + 1) + ' / ' + currentGallery.photos.length : ''">
                    </span>
                    <button type="button" 
                            @click="closeLightbox()"
                            class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition cursor-pointer shrink-0"
                            title="Tutup (Esc)">
                        <i data-lucide="x" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Lightbox Main Image Stage (Touch Swipe Enabled, Subtle Navigation Buttons) -->
        <div class="flex-1 relative flex items-center justify-center p-2 sm:p-6 overflow-hidden"
             @touchstart="touchStartX = $event.touches[0].clientX"
             @touchend="touchEndX = $event.changedTouches[0].clientX; handleSwipe()"
             @click.self="closeLightbox()">
            
            <!-- Prev Button -->
            <button type="button" 
                    x-show="currentGallery && currentGallery.photos && currentGallery.photos.length > 1"
                    @click.stop="prevPhoto()"
                    class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-10 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-slate-900/60 hover:bg-slate-900 text-white backdrop-blur-xs transition cursor-pointer shadow-lg border border-white/15 flex items-center justify-center"
                    title="Foto Sebelumnya (Panah Kiri / Geser Kanan)">
                <i data-lucide="chevron-left" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </button>

            <!-- Main Active Image -->
            <div class="max-h-full max-w-full flex items-center justify-center p-1 sm:p-0">
                <img :src="currentGallery && currentGallery.photos ? currentGallery.photos[currentPhotoIndex] : ''" 
                     :alt="currentGallery ? currentGallery.title : ''"
                     class="max-h-[66vh] sm:max-h-[78vh] w-auto max-w-full object-contain rounded-lg shadow-2xl transition duration-150 select-none">
            </div>

            <!-- Next Button -->
            <button type="button" 
                    x-show="currentGallery && currentGallery.photos && currentGallery.photos.length > 1"
                    @click.stop="nextPhoto()"
                    class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-10 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-slate-900/60 hover:bg-slate-900 text-white backdrop-blur-xs transition cursor-pointer shadow-lg border border-white/15 flex items-center justify-center"
                    title="Foto Selanjutnya (Panah Kanan / Geser Kiri)">
                <i data-lucide="chevron-right" class="w-5 h-5 sm:w-6 sm:h-6"></i>
            </button>
        </div>

        <!-- Lightbox Bottom Thumbnails Strip (With Mobile Safe Area Bottom Padding) -->
        <div x-show="currentGallery && currentGallery.photos && currentGallery.photos.length > 1"
             class="px-3 pt-2.5 pb-6 sm:px-4 sm:py-3 border-t border-white/10 bg-slate-900/80 backdrop-blur-md flex items-center justify-start sm:justify-center gap-2 overflow-x-auto max-w-full shrink-0">
            <template x-for="(photoUrl, pIdx) in (currentGallery ? currentGallery.photos : [])" :key="pIdx">
                <button type="button" 
                        @click="currentPhotoIndex = pIdx"
                        class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg overflow-hidden shrink-0 border-2 transition cursor-pointer"
                        :class="currentPhotoIndex === pIdx ? 'border-amber-400 scale-105 shadow-md' : 'border-white/20 opacity-60 hover:opacity-100'">
                    <img :src="photoUrl" class="w-full h-full object-cover">
                </button>
            </template>
        </div>

    </div>

</div>
