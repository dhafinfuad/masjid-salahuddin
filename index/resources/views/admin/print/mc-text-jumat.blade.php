<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengumuman Sholat Jumat - {{ $settings->name ?? 'Masjid Salahuddin' }} KPP Madya Malang</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts: Plus Jakarta Sans & Amiri -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
            arabic: ['"Amiri"', 'serif']
          },
          colors: {
            gov: {
              50: '#f0f4f9',
              100: '#dde6f2',
              200: '#c0d0e6',
              300: '#94b2d5',
              600: '#1f4e79',
              700: '#173d61',
              800: '#133250',
              900: '#0f243a',
              950: '#0a1726'
            },
            navy: {
              800: '#111c34',
              900: '#0d1527',
              950: '#080d19'
            }
          }
        }
      }
    }
  </script>

  <style>
    @page {
      size: A4 portrait;
      margin: 12mm 15mm 15mm 15mm;
    }

    /* Styling khusus ketika dicetak (Ctrl+P / Simpan PDF) */
    @media print {
      .no-print {
        display: none !important;
      }
      body {
        background: white !important;
        color: #0f172a !important;
        padding: 0 !important;
      }
      .print-shadow-none {
        box-shadow: none !important;
        border: 1px solid #cbd5e1 !important;
      }
      .page-break-avoid {
        page-break-inside: avoid;
      }
    }

    /* Kustomisasi scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
    }
    ::-webkit-scrollbar-track {
      background: #f1f5f9;
    }
    ::-webkit-scrollbar-thumb {
      background: #94a3b8;
      border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #64748b;
    }
  </style>
</head>
<body class="bg-slate-100/90 text-slate-800 font-sans antialiased min-h-screen flex flex-col items-center p-3 sm:p-6 md:p-8 selection:bg-gov-100 selection:text-gov-950">

  <div class="w-full max-w-3xl bg-white rounded-2xl shadow-xl shadow-slate-300/40 border border-slate-200/90 overflow-hidden flex flex-col transition-all print-shadow-none">
    
    <!-- Top Action Bar (Header) - Clean Navy Gov tanpa logo & judul teks -->
    <header class="no-print bg-gov-950 text-white px-4 sm:px-6 py-3 flex items-center justify-end gap-2 border-b border-gov-900 sticky top-0 z-30 shadow-sm">
      <!-- Quick Action Buttons -->
      <div class="flex items-center gap-2">
        <!-- Ukuran Teks Button untuk kenyamanan membaca -->
        <div class="flex items-center bg-gov-900 rounded-lg p-0.5 border border-slate-700/70 text-slate-300 mr-1">
          <button type="button" onclick="adjustFontSize(-1)" title="Perkecil Teks" class="p-1.5 hover:text-white hover:bg-gov-800 rounded-md transition-colors cursor-pointer">
            <i data-lucide="minus" class="w-3.5 h-3.5"></i>
          </button>
          <span class="text-[11px] px-2 font-mono font-medium text-slate-300">A</span>
          <button type="button" onclick="adjustFontSize(1)" title="Perbesar Teks" class="p-1.5 hover:text-white hover:bg-gov-800 rounded-md transition-colors cursor-pointer">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
          </button>
        </div>

        <!-- Tombol Cetak / PDF -->
        <button 
          type="button"
          onclick="window.print()" 
          class="inline-flex items-center gap-2 bg-gov-700 hover:bg-gov-600 active:bg-gov-800 text-white text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-gov-600/60 shadow-sm focus:outline-none focus:ring-2 focus:ring-gov-300/40 cursor-pointer">
          <i data-lucide="printer" class="w-4 h-4"></i>
          <span class="hidden sm:inline">Cetak /</span> Simpan PDF
        </button>

        <!-- Tombol Tutup -->
        <button 
          type="button"
          onclick="handleCloseNotification()" 
          class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-200 hover:text-white text-xs sm:text-sm font-medium px-3.5 py-2 rounded-lg border border-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400/40 cursor-pointer">
          <i data-lucide="x" class="w-4 h-4"></i>
          <span>Tutup</span>
        </button>
      </div>
    </header>

    <main id="contentContainer" class="p-6 sm:p-9 md:p-12 space-y-6 sm:space-y-7 leading-relaxed text-slate-700 transition-all text-base">
      
      <!-- Dokumen Header / Kop Identitas Bersih & Formal -->
      <div class="border-b border-slate-200 pb-5 mb-2">
        <div>
          <h2 class="text-xl sm:text-2xl font-bold text-gov-950 tracking-tight">
            Pemberitahuan Sebelum Khutbah
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
            {{ $settings->name ?? 'Masjid Salahuddin' }} KPP Madya Malang
          </p>
        </div>
      </div>

      <!-- Salam Pembuka & Basmalah -->
      <p class="font-bold text-slate-900 text-base sm:text-lg tracking-tight">
        Assalamu’alaikum warohmatullohi wabarokatuh
      </p>
      
      <p class="text-justify leading-relaxed">
        Bismillahirrohmanirrohim
      </p>
      
      <p class="text-justify leading-relaxed">
        “Alhamdulillahi robbil ‘alamiin, Washolaatu Wassalaamu ‘Alaa Asyrofil Anbiya wal mursalin wa ‘ala aalihi washohbihi ajma’in. Amma Ba’du”
      </p>
      <!-- Paragraf Sambutan & Syukur -->
      <p class="text-justify leading-relaxed">
        Kaum Muslimin Jama’ah Sholat Jumat <strong class="font-bold text-gov-950">{{ $settings->name ?? 'Masjid Salahuddin' }} KPP Madya Malang</strong> yang dirahmati Allah SWT. Syukur Alhamdulillah, segala puja dan puji kita panjatkan kehadirat Allah SWT atas limpahan Taufiq Rahmat dan Nikmat-Nya, hari ini kita dapat berkumpul di tempat yang Mulia ini untuk menjalankan kewajiban sholat Jumat secara berjamaah dalam keadaan sehat wal afiat.
      </p>

      <!-- Paragraf Sholawat -->
      <div class="space-y-2 text-justify leading-relaxed">
        <p>
          Sholawat teriring salam semoga tetap tercurah limpahkan kepada junjungan kita, Nabi Besar Muhammad SAW beserta Keluarga, Sahabat dan para pengikutnya yang istiqomah mengikuti ajarannya, dan semoga kita semua kelak mendapat Syafaat Beliau di Yaumil Qiyamah.
        </p>
        <p class="font-semibold text-slate-800 text-center sm:text-left text-sm sm:text-base italic pt-1">
          Aamiin Ya Robbal Alamin.
        </p>
      </div>

      <!-- Pengantar Pengumuman -->
      <div class="pt-2">
        <p class="text-slate-800 font-medium leading-relaxed">
          Jamaah sidang sholat Jumat yang dirahmati Allah,
        </p>
        <p class="text-slate-700 leading-relaxed mt-1">
          Sebelum Khotib naik ke atas mimbar, perkenankan kami dari Takmir <strong class="font-bold text-gov-950">{{ $settings->name ?? 'Masjid Salahuddin' }}</strong> menyampaikan beberapa buah pengumuman sebagai berikut:
        </p>
      </div>

      <!-- Daftar Poin Pengumuman -->
      <ol class="space-y-3 pt-1 list-none pl-0">
        <!-- Poin 1: Waktu Sholat -->
        <li class="flex items-start gap-3.5 bg-slate-50/70 hover:bg-slate-50 p-4 rounded-xl border border-slate-200 transition-all">
          <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gov-800 text-white font-bold text-xs flex items-center justify-center shadow-xs">
            1
          </span>
          <div class="text-slate-700 text-sm sm:text-base leading-relaxed">
            Waktu sholat Jum’at untuk wilayah kota Malang pada hari ini tanggal 
            <strong>
              {{ $formattedJumatDate }}
            </strong> 
            jatuh pada pukul 
            <strong>
              {{ $timeDisplay }}
            </strong>. 
            Untuk meraih kesempurnaan dan keutamaan ibadah sholat Jumat kita, mari kita hadir ke Masjid sebelum Khotib naik ke atas mimbar.
          </div>
        </li>

        <!-- Poin 2: Khotib, Muadzin & MC -->
        <li class="flex items-start gap-3.5 bg-slate-50/70 hover:bg-slate-50 p-4 rounded-xl border border-slate-200 transition-all">
          <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gov-800 text-white font-bold text-xs flex items-center justify-center shadow-xs">
            2
          </span>
          <div class="text-slate-700 text-sm sm:text-base leading-relaxed">
            Bertindak sebagai Khotib sekaligus Imam pelaksanaan ibadah Sholat Jumat hari ini adalah <strong>Yth. {{ $kajian->khatib_name ?: 'Ust. Dwi Triono SH.' }}</strong>.<br>
            @if(!empty($kajian->mc_name))
              Serta dipandu oleh MC / Pembawa Acara <strong class="font-semibold text-slate-900">Yth. {{ $kajian->mc_name }}</strong>.<br>
            @endif
            Adzan dan Bilal akan dikumandangkan oleh <strong class="font-semibold text-slate-900">{{ $kajian->muadzin_name ?: '-' }}</strong>.
          </div>
        </li>

        <!-- Poin 3: Undangan Kajian Pekanan -->
        <li class="flex items-start gap-3.5 bg-slate-50/70 hover:bg-slate-50 p-4 rounded-xl border border-slate-200 transition-all">
          <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gov-800 text-white font-bold text-xs flex items-center justify-center shadow-xs">
            3
          </span>
          <div class="text-slate-700 text-sm sm:text-base leading-relaxed">
            Kami mengundang Jama’ah untuk turut hadir dalam kegiatan <strong>Kajian Pekanan, Bakda Sholat Ashar</strong>. Kajian pekan depan <span class="font-semibold text-slate-900">{{ $formattedNextMondayDate }}</span>, Insya Allah akan disampaikan oleh <strong class="font-bold text-gov-950">{{ $nextMondayKajian?->speaker_name ?: 'Alvin Shohih' }}</strong>.
          </div>
        </li>

        <!-- Poin 4: Jumat Berkah -->
        <li class="flex items-start gap-3.5 bg-slate-50/70 hover:bg-slate-50 p-4 rounded-xl border border-slate-200 transition-all">
          <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gov-800 text-white font-bold text-xs flex items-center justify-center shadow-xs">
            4
          </span>
          <div class="text-slate-700 text-sm sm:text-base leading-relaxed">
            Takmir <strong>{{ $settings->name ?? 'Masjid Salahuddin' }}</strong>, hari ini menyediakan <strong>Jumat Berkah</strong> yang dapat dinikmati Jamaah setelah kegiatan Shalat Jumat, dan sudah disediakan di area <strong class="font-semibold text-slate-900">Kantin</strong>. Atas nama Takmir kami menghaturkan terima kasih atas Infaq Shodaqoh yang telah diberikan jama’ah, dengan ucapan <em class="italic text-slate-800 font-medium">jazakumullohu khoiron katsiron</em>.
          </div>
        </li>

        <!-- Poin 5: Tata Tertib Jamaah (Mode Hening & Shaf Depan) -->
        <li class="flex items-start gap-3.5 bg-slate-50/70 hover:bg-slate-50 p-4 rounded-xl border border-slate-200 transition-all">
          <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gov-800 text-white font-bold text-xs flex items-center justify-center shadow-xs">
            5
          </span>
          <div class="text-slate-700 text-sm sm:text-base leading-relaxed">
            Demi menjaga kekhusyukan jalannya khutbah dan ibadah sholat, dimohon kepada seluruh jamaah untuk 
            <strong class="font-semibold text-slate-900">menonaktifkan atau mengatur ke mode hening (silent)</strong> alat komunikasi / handphone masing-masing, serta mengisi shaf bagian terdepan yang masih kosong terlebih dahulu.
          </div>
        </li>

        @if(!empty($kajian->mc_notes))
        <!-- Poin 6: Pengumuman Khusus Takmir (Opsional) -->
        <li class="flex items-start gap-3.5 bg-amber-50/70 hover:bg-amber-50 p-4 rounded-xl border border-amber-200 transition-all">
          <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-amber-800 text-white font-bold text-xs flex items-center justify-center shadow-xs">
            6
          </span>
          <div class="text-slate-800 text-sm sm:text-base leading-relaxed">
            <strong class="font-bold text-gov-950 block mb-1">Pengumuman Khusus Takmir:</strong>
            {!! nl2br(e($kajian->mc_notes)) !!}
          </div>
        </li>
        @endif
      </ol>

      <!-- Penutup & Himbauan Khotib -->
      <div class="pt-2 space-y-4">
        <p class="text-slate-700 leading-relaxed text-justify">
          Demikian beberapa informasi dan himbauan yang dapat kami sampaikan. Atas perhatian dan kerja samanya diucapkan terima kasih.
        </p>

        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/90 text-slate-800">
          Kepada Yth. <strong>{{ $kajian->khatib_name ?: 'Ust. Dwi Triono SH.' }}</strong> kami persilakan.
        </div>

        <p class="font-bold text-slate-900 text-base sm:text-lg tracking-tight">
          Wassalamu’alaikum warohmatullohi wabarokatuh
        </p>
      </div>
    </main>

  </div>

  <!-- Toast Notifikasi (Pengganti alert) -->
  <div id="toast" class="fixed bottom-5 right-5 bg-gov-950 text-white text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-lg border border-gov-800 flex items-center gap-2 opacity-0 pointer-events-none transition-opacity duration-300 z-50">
    <i data-lucide="info" class="w-4 h-4 text-gov-300"></i>
    <span id="toastMessage">Tindakan berhasil</span>
  </div>

  <script>
    // Inisialisasi ikon Lucide
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }

    // Fungsi pengatur ukuran teks untuk kemudahan membaca di HP/Desktop
    let currentScale = 0;
    const fontClasses = ['text-sm', 'text-base', 'text-lg', 'text-xl'];
    let currentIndex = 1; // Default: text-base

    function adjustFontSize(delta) {
      const container = document.getElementById('contentContainer');
      const newIndex = currentIndex + delta;

      if (newIndex >= 0 && newIndex < fontClasses.length) {
        container.classList.remove(fontClasses[currentIndex]);
        currentIndex = newIndex;
        container.classList.add(fontClasses[currentIndex]);
        showToast(`Ukuran teks diubah ke level ${currentIndex + 1}`);
      }
    }

    // Penanganan tombol tutup
    function handleCloseNotification() {
      showToast("Jendela pengumuman ditutup");
      const card = document.querySelector('.max-w-3xl');
      if (card) {
        card.classList.add('opacity-40', 'scale-[0.99]');
        setTimeout(() => {
          card.classList.remove('opacity-40', 'scale-[0.99]');
        }, 800);
      }
      setTimeout(() => {
        try {
          window.close();
        } catch (e) {}
      }, 400);
    }

    // Toast notifikasi ramah pengguna tanpa popup browser
    function showToast(message) {
      const toast = document.getElementById('toast');
      const toastMessage = document.getElementById('toastMessage');
      if (!toast || !toastMessage) return;
      toastMessage.textContent = message;
      toast.classList.remove('opacity-0', 'pointer-events-none');
      toast.classList.add('opacity-100');

      setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0', 'pointer-events-none');
      }, 2400);
    }
  </script>
</body>
</html>
