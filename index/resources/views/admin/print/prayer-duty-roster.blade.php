<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jadwal Penugasan Imam & Muadzin — {{ $settings->name ?? 'Masjid Salahuddin' }}</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts: Plus Jakarta Sans, Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- QRCode.js Library (Local Vendor + CDN Fallback) -->
  <script src="{{ asset('vendor/qrcode.min.js') }}"></script>
  <script>
    if (typeof QRCode === 'undefined') {
      document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
    }
  </script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
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
              950: '#0a1726',
              textMain: '#0f172a'
            }
          }
        }
      }
    }
  </script>

  <style>
    /* 1. Hilangkan Header (URL & Judul) dan Footer bawaan browser saat cetak */
    @page {
      size: A4 portrait;
      margin: 0;
    }

    /* 2. Paksa browser mencetak warna latar belakang (Background Graphics) */
    *, *::before, *::after {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
      color-adjust: exact !important;
    }

    @media print {
      html, body {
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
      .no-print, .screen-toolbar {
        display: none !important;
      }
      .print-shadow-none {
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
      }
      #contentContainer {
        padding: 12mm 15mm !important;
        margin: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
      }
      .page-break-avoid {
        page-break-inside: avoid;
      }
      .qrcode-container {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 72px !important;
        height: 72px !important;
      }
      .qrcode-container img,
      .qrcode-container canvas {
        display: block !important;
        width: 72px !important;
        height: 72px !important;
        max-width: 72px !important;
        max-height: 72px !important;
        image-rendering: -webkit-optimize-contrast !important;
        image-rendering: crisp-edges !important;
        image-rendering: pixelated !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
      .qrcode-container canvas[style*="display: none"] {
        display: none !important;
      }
    }

    /* 3. Warna Biru Kedinasan Khusus Header & Footer Tabel */
    .table-header-blue,
    .table-header-blue th {
      background-color: #06172e !important; /* Blue Navy Kedinasan #06172e */
      color: #ffffff !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    .table-footer-blue,
    .table-footer-blue td {
      background-color: #06172e !important; /* Blue Navy Kedinasan #06172e */
      color: #ffffff !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    .qrcode-container {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 72px;
      height: 72px;
    }

    .qrcode-container canvas,
    .qrcode-container img {
      display: block;
      margin: 0 auto;
      width: 72px !important;
      height: 72px !important;
      max-width: 72px !important;
      max-height: 72px !important;
      image-rendering: -webkit-optimize-contrast;
      image-rendering: crisp-edges;
      image-rendering: pixelated;
    }

    .qrcode-container canvas[style*="display: none"] {
      display: none !important;
    }

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
<body class="bg-slate-100 text-slate-800 font-sans antialiased min-h-screen flex flex-col items-center p-3 sm:p-6 md:p-8">

  <div class="w-full max-w-5xl bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden flex flex-col transition-all print-shadow-none">
    
    <!-- Top Action Bar (Header) -->
    <header class="no-print bg-slate-900 text-white px-4 sm:px-6 py-2.5 flex items-center justify-end gap-2 border-b border-slate-800 sticky top-0 z-30 shadow-sm">
      <div class="flex items-center gap-2">
        <!-- Tombol Cetak / PDF -->
        <button 
          type="button"
          onclick="triggerPrintWithQr()" 
          class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-slate-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-400 cursor-pointer">
          <i data-lucide="printer" class="w-4 h-4"></i>
          <span class="hidden sm:inline">Cetak /</span> Simpan PDF
        </button>

        <!-- Tombol Tutup -->
        <button 
          type="button"
          onclick="handleCloseNotification()" 
          class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-200 hover:text-white text-xs sm:text-sm font-medium px-3.5 py-2 rounded-lg border border-slate-700 transition-colors focus:outline-none cursor-pointer">
          <i data-lucide="x" class="w-4 h-4"></i>
          <span>Tutup</span>
        </button>

        <!-- Tombol Kembali -->
        <a 
          href="{{ url('/admin/petugas') }}" 
          class="hidden sm:inline-flex items-center gap-1 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-medium px-3 py-2 rounded-lg border border-slate-700 transition-colors" 
          title="Kembali ke Manajemen Petugas Shalat">
          <span>← Kembali</span>
        </a>
      </div>
    </header>

    @if(request()->has('tte') || request()->has('hash'))
      <!-- Banner Verifikasi Dokumen TTE saat discan -->
      <div class="no-print mx-4 sm:mx-6 mt-4 p-3 bg-emerald-50 border border-emerald-300 rounded-lg flex items-center gap-3 text-emerald-900 text-xs shadow-xs">
        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 font-bold">
          <i data-lucide="check-circle" class="w-5 h-5"></i>
        </div>
        <div>
          <p class="font-bold text-emerald-950 text-xs sm:text-sm">Dokumen Terverifikasi Sah (TTE BSrE)</p>
          <p class="text-emerald-800 text-[11px] mt-0.5">Dokumen ini telah diverifikasi dan disahkan secara elektronik. Hash: <span class="font-mono font-semibold">{{ $tteHash ?? request('hash') }}</span></p>
        </div>
      </div>
    @endif

    <main id="contentContainer" class="p-6 sm:p-8 space-y-4 text-slate-800 text-xs sm:text-[13px]">
      
      <!-- Judul Dokumen -->
      <div class="text-center pt-0.5">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide inline-block border-b border-slate-800 pb-0.5">
          JADWAL PENUGASAN IMAM &amp; MUADZIN SHALAT FARDHU
        </h3>
        <p class="text-xs text-slate-600 font-medium mt-1">
          {{ $settings->name ?? 'Masjid Salahuddin' }} • Khusus Shalat Dzuhur &amp; Ashar • Status: {{ $week === 'all' ? 'Semua Pekan' : 'Pekan ' . $week }}
        </p>
      </div>


      <!-- Tabel Data Penugasan -->
      @php
        $groupedDuties = [];
        $currentWeekNum = \App\Models\PrayerDuty::getWeekOfMonth(now());
        foreach ($duties as $duty) {
            $dutyWeek = $duty->resolved_week ?? null;
            if (!$dutyWeek) {
                if ($duty->matchesWeek($currentWeekNum)) {
                    $dutyWeek = $currentWeekNum;
                } elseif (preg_match('/pekan_(\d)/', $duty->week_pattern, $m)) {
                    $dutyWeek = (int) $m[1];
                } elseif ($duty->week_pattern === 'semua') {
                    $dutyWeek = $currentWeekNum;
                }
            }
            $resolvedDate = $duty->resolved_date ?? $duty->resolveDate($selectedYearNum ?? now()->year, $selectedMonthNum ?? now()->month, $selectedWeekNum ?? $dutyWeek);
            $dateKey = $resolvedDate ? $resolvedDate->format('Y-m-d') : ($duty->day_name . '_pekan_' . ($dutyWeek ?? $duty->week_pattern));

            if (!isset($groupedDuties[$dateKey])) {
                $groupedDuties[$dateKey] = [
                    'date' => $resolvedDate,
                    'date_key' => $dateKey,
                    'day_name' => $duty->day_name,
                    'formatted_date' => $resolvedDate ? $resolvedDate->translatedFormat('d M Y') : '-',
                    'day_number' => $resolvedDate ? (int) $resolvedDate->format('j') : null,
                    'week_number' => $dutyWeek,
                    'week_label' => $duty->resolved_week_label ?? ($dutyWeek ? ('Pekan ' . $dutyWeek) : $duty->week_pattern_label),
                    'is_friday' => ($duty->day_name === 'Jumat'),
                    'dzuhur' => null,
                    'ashar' => null,
                ];
            }

            if ($duty->prayer_time === 'dzuhur') {
                $groupedDuties[$dateKey]['dzuhur'] = $duty;
            } elseif ($duty->prayer_time === 'ashar') {
                $groupedDuties[$dateKey]['ashar'] = $duty;
            }
        }
      @endphp

      <div class="overflow-x-auto border border-slate-300 rounded-lg">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="table-header-blue text-white text-[11px] font-bold uppercase tracking-wider" style="background-color: #06172e !important; color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
              <th class="p-2 text-center border-b border-[#030c18] w-12" style="background-color: #06172e !important; color: #ffffff !important;">Tgl</th>
              <th class="p-2 border-b border-[#030c18] w-36" style="background-color: #06172e !important; color: #ffffff !important;">Hari &amp; Tanggal</th>
              <th class="p-2 border-b border-[#030c18] text-center w-28" style="background-color: #06172e !important; color: #ffffff !important;">Pola Pekan</th>
              <th class="p-2 border-b border-[#030c18]" style="background-color: #06172e !important; color: #ffffff !important;">Dzuhur</th>
              <th class="p-2 border-b border-[#030c18]" style="background-color: #06172e !important; color: #ffffff !important;">Shalat Ashar</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-300">
            @forelse($groupedDuties as $row)
              @php
                $dateKey = $row['date_key'];
                $matchedKajianOnDate = ($dateKey && isset($kajianUmumByDate[$dateKey]))
                    ? $kajianUmumByDate[$dateKey]
                    : null;

                $asharImam = $row['ashar']?->imam_name ?: '-';
                $asharMuadzin = $row['ashar']?->muadzin_name ?: '-';
                $asharKajianTitle = null;

                if ($matchedKajianOnDate && !empty($matchedKajianOnDate->speaker_name)) {
                    $asharImam = $matchedKajianOnDate->speaker_name;
                    if (!empty($matchedKajianOnDate->muadzin_name)) {
                        $asharMuadzin = $matchedKajianOnDate->muadzin_name;
                    }
                    $asharKajianTitle = $matchedKajianOnDate->title;
                }

                if ($row['is_friday']) {
                    $matchedKajian = ($dateKey && isset($fridayKajiansByDate[$dateKey]))
                        ? $fridayKajiansByDate[$dateKey]
                        : ($row['week_number'] && isset($fridayKajiansByWeek[$row['week_number']]) ? $fridayKajiansByWeek[$row['week_number']] : null);

                    $khatib = $matchedKajian 
                        ? ($matchedKajian->khatib_name ?: ($matchedKajian->speaker_name ?: ($row['dzuhur']?->imam_name ?: 'Ust. M. Yasak Lc MA'))) 
                        : ($row['dzuhur']?->imam_name ?: ($settings->friday_prayer_info['khatib'] ?? 'Ust. M. Yasak Lc MA'));

                    $muadzin = $matchedKajian 
                        ? ($matchedKajian->muadzin_name ?: ($matchedKajian->description ?: ($row['dzuhur']?->muadzin_name ?: 'Khodori'))) 
                        : ($row['dzuhur']?->muadzin_name ?: ($settings->friday_prayer_info['muadzin'] ?? 'Khodori'));

                    $mc = $matchedKajian 
                        ? ($matchedKajian->mc_name ?: ($settings->friday_prayer_info['mc'] ?? '-')) 
                        : ($settings->friday_prayer_info['mc'] ?? '-');
                } else {
                    $dzImam = $row['dzuhur']?->imam_name ?: '-';
                    $dzMuadzin = $row['dzuhur']?->muadzin_name ?: '-';
                }
              @endphp
              <tr class="{{ $row['is_friday'] ? 'bg-emerald-50/50 hover:bg-emerald-50/80 font-medium' : 'odd:bg-white even:bg-slate-50/50 hover:bg-slate-50' }} transition-colors">
                <td class="p-2 text-center font-bold text-slate-600 border-r border-slate-200">
                  {{ $row['day_number'] ?? $loop->iteration }}
                </td>
                <td class="p-2 border-r border-slate-200 whitespace-nowrap">
                  <div class="font-bold text-slate-900">{{ $row['day_name'] }}</div>
                  <div class="text-[11px] text-slate-500 font-normal leading-tight">{{ $row['formatted_date'] }}</div>
                </td>
                <td class="p-2 border-r border-slate-200 text-center whitespace-nowrap">
                  @if($row['is_friday'])
                    <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-emerald-100 text-emerald-800 border-emerald-300">
                      Jumat (P.{{ $row['week_number'] }})
                    </span>
                  @else
                    <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">
                      {{ $row['week_label'] }}
                    </span>
                  @endif
                </td>
                <td class="p-2 border-r border-slate-200 leading-tight">
                  @if($row['is_friday'])
                    <div class="space-y-0.5 font-medium text-slate-700">
                      <div>
                        <span>Khotib :</span>
                        <span class="font-semibold text-slate-900 ml-1">{{ $khatib }}</span>
                      </div>
                      <div>
                        <span>Muadzin :</span>
                        <span class="font-semibold text-slate-900 ml-1">{{ $muadzin }}</span>
                      </div>
                      @if($mc && $mc !== '-')
                        <div>
                          <span>MC :</span>
                          <span class="font-semibold text-slate-900 ml-1">{{ $mc }}</span>
                        </div>
                      @endif
                    </div>
                  @elseif($row['dzuhur'])
                    <div class="space-y-0.5 font-medium text-slate-700">
                      <div>
                        <span>Imam :</span>
                        <span class="font-semibold text-slate-900 ml-1">{{ $dzImam }}</span>
                      </div>
                      <div>
                        <span>Muadzin :</span>
                        <span class="font-semibold text-slate-900 ml-1">{{ $dzMuadzin }}</span>
                      </div>
                    </div>
                  @else
                    <span class="text-slate-400 italic text-[11px]">-</span>
                  @endif
                </td>
                <td class="p-2 leading-tight">
                  @if($row['ashar'] || $asharImam !== '-')
                    <div class="space-y-0.5 font-medium text-slate-700">
                      <div>
                        <span>Imam :</span>
                        <span class="font-semibold text-slate-900 ml-1">{{ $asharImam }}</span>
                      </div>
                      <div>
                        <span>Muadzin :</span>
                        <span class="font-semibold text-slate-900 ml-1">{{ $asharMuadzin }}</span>
                      </div>
                      @if(!empty($asharKajianTitle))
                        <div>
                          <span>Kajian :</span>
                          <span class="font-semibold text-slate-900 ml-1">{{ $asharKajianTitle }}</span>
                        </div>
                      @endif
                    </div>
                  @else
                    <span class="text-slate-400 italic text-[11px]">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="p-6 text-center text-slate-400 italic">
                  Tidak ada data penugasan imam &amp; muadzin yang ditemukan.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Tanda Tangan Resmi dengan QR Code Verifikasi Kedinasan -->
      <div class="pt-4 flex justify-between items-start page-break-avoid text-xs">
        <div class="w-56 text-center flex flex-col items-center">
          <p class="text-slate-600 mb-0.5 font-medium">Disusun oleh,</p>
          <p class="font-bold text-slate-900 mb-1">Sekretaris DKM</p>

          @if($isTteSigned ?? true)
            <!-- QR Code TTE BSrE Standar Kedinasan -->
            <div class="my-1.5 p-1.5 bg-white border border-slate-300 rounded shadow-2xs flex flex-col items-center">
              <div class="qrcode-container" data-qr-text="{{ $verifyUrl }}" title="Scan untuk verifikasi TTE resmi BSrE"></div>
              <span class="text-[8px] font-mono text-slate-500 mt-1 tracking-tight font-semibold uppercase">TTE BSrE DISAHKAN</span>
            </div>
          @else
            <div class="h-16"></div>
          @endif

          <p class="font-bold text-slate-900 underline underline-offset-2 mt-0.5">{{ $sekretarisDkm?->name ?? 'Deril Amrizal Kholid' }}</p>
          <p class="text-[11px] text-slate-500 font-medium mt-0.5">Sekretaris DKM</p>
        </div>

        <div class="w-56 text-center flex flex-col items-center">
          <p class="text-slate-600 mb-0.5 font-medium">Mengetahui &amp; Menyetujui,</p>
          <p class="font-bold text-slate-900 mb-1">Ketua DKM Masjid Salahuddin</p>

          @if($isTteSigned ?? true)
            <!-- QR Code TTE BSrE Standar Kedinasan -->
            <div class="my-1.5 p-1.5 bg-white border border-slate-300 rounded shadow-2xs flex flex-col items-center">
              <div class="qrcode-container" data-qr-text="{{ $verifyUrl }}" title="Scan untuk verifikasi TTE resmi BSrE"></div>
              <span class="text-[8px] font-mono text-slate-500 mt-1 tracking-tight font-semibold uppercase">TTE BSrE DISAHKAN</span>
            </div>
          @else
            <div class="h-16"></div>
          @endif

          <p class="font-bold text-slate-900 underline underline-offset-2 mt-0.5">{{ $ketuaDkm?->name ?? 'Nazil Fuadi' }}</p>
          <p class="text-[11px] text-slate-500 font-medium mt-0.5">Ketua DKM</p>
        </div>
      </div>

    </main>
  </div>

  <!-- Toast Notifikasi Ringan -->
  <div id="toast" class="no-print fixed bottom-5 left-1/2 -translate-x-1/2 bg-slate-900/95 text-white text-xs sm:text-sm px-4 py-2 rounded-xl shadow-2xl backdrop-blur-sm border border-slate-700 transition-opacity duration-300 opacity-0 pointer-events-none z-50 flex items-center gap-2">
    <i data-lucide="info" class="w-4 h-4 text-slate-300"></i>
    <span id="toastMessage">Notifikasi</span>
  </div>

  <script>
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }

    function initQrCodes() {
      if (typeof QRCode === 'undefined') return;
      const containers = document.querySelectorAll('.qrcode-container');
      containers.forEach(function (el) {
        if (el.children.length === 0) {
          const text = el.getAttribute('data-qr-text') || window.location.href;
          new QRCode(el, {
            text: text,
            width: 256,
            height: 256,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
          });
        }
      });
    }

    document.addEventListener('DOMContentLoaded', initQrCodes);
    setTimeout(initQrCodes, 150);

    function triggerPrintWithQr() {
      initQrCodes();
      setTimeout(() => {
        window.print();
      }, 200);
    }

    function handleCloseNotification() {
      showToast("Jendela cetak ditutup");
      setTimeout(() => {
        try {
          window.close();
        } catch (e) {}
      }, 400);
    }

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
