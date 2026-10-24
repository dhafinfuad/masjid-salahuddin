<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jadwal Petugas Shalat {{ $monthName }} {{ $year }} — {{ $settings->name ?? 'Masjid Salahuddin' }}</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Fonts: Plus Jakarta Sans, Inter, Amiri -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- html2pdf.js for Client-Side PDF Generation on Mobile -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
              950: '#0a1726',
              textMain: '#0f172a'
            },
            navy: {
              800: '#111c34',
              900: '#-0d1527',
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

  <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl shadow-slate-300/40 border border-slate-200/90 overflow-hidden flex flex-col transition-all print-shadow-none">
    
    <!-- Top Action Bar (Header) -->
    <header class="no-print bg-slate-900 text-white px-4 sm:px-6 py-2.5 flex items-center justify-between gap-2 border-b border-slate-800 sticky top-0 z-30 shadow-sm">
      <!-- Tombol Kembali -->
      <a 
        href="{{ url('/petugas-sholat') }}" 
        class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-200 hover:text-white text-xs sm:text-sm font-semibold px-3.5 py-2 rounded-lg border border-slate-700 transition-colors shadow-2xs cursor-pointer" 
        title="Kembali ke Jadwal Petugas Shalat">
        <svg class="w-4 h-4 shrink-0 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Kembali</span>
      </a>

      <!-- Action Buttons: Desktop (Cetak) & Mobile (Unduh PDF) -->
      <div class="flex items-center gap-2">
        <!-- Desktop: Tombol Cetak / Simpan PDF (hidden di mobile, inline-flex di sm+) -->
        <button 
          type="button"
          onclick="window.print()" 
          class="hidden sm:inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 active:bg-slate-800 text-white text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-slate-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-slate-400 cursor-pointer"
          title="Cetak atau Simpan PDF (Browser)">
          <i data-lucide="printer" class="w-4 h-4"></i>
          <span>Cetak / Simpan PDF</span>
        </button>

        <!-- Mobile: Tombol Unduh PDF (inline-flex di mobile, hidden di sm+) -->
        <button 
          type="button"
          id="btnDownloadMobile"
          onclick="downloadMonthlyPdf('Jadwal_Penugasan_Imam_Muadzin_{{ $monthName }}_{{ $year }}')" 
          class="inline-flex sm:hidden items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-200 hover:text-white text-xs sm:text-sm font-semibold px-3.5 py-2 rounded-lg border border-slate-700 transition-colors shadow-2xs cursor-pointer"
          title="Unduh File PDF">
          <svg class="w-4 h-4 shrink-0 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span id="labelDownloadMobile">Unduh PDF</span>
        </button>
      </div>
    </header>

    <main id="contentContainer" class="p-6 sm:p-9 md:p-10 space-y-6 leading-relaxed text-slate-700 transition-all text-xs sm:text-sm">
      
      <!-- Judul Dokumen -->
      <div class="text-center pt-1">
        <h3 class="text-base sm:text-lg font-bold text-gov-950 uppercase tracking-wide inline-block border-b-2 border-gov-700 pb-0.5">
          JADWAL PENUGASAN IMAM, KHATIB &amp; MUADZIN
        </h3>
        <p class="text-xs text-slate-500 font-medium mt-1">Bulan: {{ $monthName }} {{ $year }}</p>
      </div>

      <!-- Tabel Penugasan Bulanan -->
      <div class="overflow-x-auto border border-slate-300 rounded-lg">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gov-950 text-white text-[11px] font-bold uppercase tracking-wider">
              <th class="p-2 text-center border-b border-gov-900 w-10">Tgl</th>
              <th class="p-2 border-b border-gov-900 w-32">Hari &amp; Tanggal</th>
              <th class="p-2 border-b border-gov-900 text-center w-28">Pola</th>
              <th class="p-2 border-b border-gov-900">Shalat Dzuhur</th>
              <th class="p-2 border-b border-gov-900">Shalat Ashar</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-300 text-xs">
            @forelse($roster as $row)
              <tr class="{{ $row['is_friday'] ? 'bg-emerald-50/50 hover:bg-emerald-50/80 font-medium' : 'odd:bg-white even:bg-slate-50/50 hover:bg-slate-50' }} transition-colors">
                <td class="p-2 text-center font-bold text-slate-600 border-r border-slate-200">
                  {{ $row['day_number'] }}
                </td>
                <td class="p-2 border-r border-slate-200 whitespace-nowrap">
                  <span class="font-bold text-slate-900">{{ $row['day_name'] }}</span>, 
                  <span class="text-slate-600">{{ $row['formatted_date'] }}</span>
                </td>
                <td class="p-2 border-r border-slate-200 text-center whitespace-nowrap">
                  @if($row['is_friday'])
                    <span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-emerald-50 text-emerald-700 border-emerald-400">
                      Jumat (P.{{ $row['week_number'] }})
                    </span>
                  @else
                    <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">Pekan {{ $row['week_number'] }}</span>
                  @endif
                </td>
                <td class="p-2 border-r border-slate-200 leading-tight">
                  <div class="space-y-0.5 font-medium text-slate-700">
                    @if($row['is_friday'])
                      <div>
                        <span>Khatib:</span>
                        <span class="font-semibold text-gov-textMain ml-1">{{ $row['dzuhur_imam'] }}</span>
                      </div>
                      <div>
                        <span>Muadzin:</span>
                        <span class="font-semibold text-gov-textMain ml-1">{{ $row['dzuhur_muadzin'] }}</span>
                      </div>
                      @if($row['friday_mc'] !== '-')
                        <div>
                          <span>MC:</span>
                          <span class="font-semibold text-gov-textMain ml-1">{{ $row['friday_mc'] }}</span>
                        </div>
                      @endif
                    @else
                      <div>
                        <span>Imam:</span>
                        <span class="font-semibold text-gov-textMain ml-1">{{ $row['dzuhur_imam'] }}</span>
                      </div>
                      <div>
                        <span>Muadzin:</span>
                        <span class="font-semibold text-gov-textMain ml-1">{{ $row['dzuhur_muadzin'] }}</span>
                      </div>
                    @endif
                  </div>
                </td>
                <td class="p-2 leading-tight">
                  <div class="space-y-0.5 font-medium text-slate-700">
                    <div>
                      <span>Imam:</span>
                      <span class="font-semibold text-gov-textMain ml-1">{{ $row['ashar_imam'] }}</span>
                    </div>
                    <div>
                      <span>Muadzin:</span>
                      <span class="font-semibold text-gov-textMain ml-1">{{ $row['ashar_muadzin'] }}</span>
                    </div>
                    @if(!empty($row['ashar_kajian_title']))
                      <div>
                        <span>Kajian:</span>
                        <span class="font-semibold text-gov-textMain ml-1">{{ $row['ashar_kajian_title'] }}</span>
                      </div>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="p-8 text-center text-slate-400 italic">
                  Tidak ada data penugasan untuk bulan ini.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </main>
  </div>

  <!-- Toast Notifikasi Ringan -->
  <div id="toast" class="no-print fixed bottom-5 left-1/2 -translate-x-1/2 bg-slate-900/95 text-white text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-2xl backdrop-blur-sm border border-slate-700/80 transition-opacity duration-300 opacity-0 pointer-events-none z-50 flex items-center gap-2">
    <i data-lucide="info" class="w-4 h-4 text-gov-300"></i>
    <span id="toastMessage">Notifikasi</span>
  </div>

  <script>
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
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

    async function downloadMonthlyPdf(filename) {
      const btn = document.getElementById('btnDownloadMobile');
      const label = document.getElementById('labelDownloadMobile');

      if (typeof html2pdf === 'undefined') {
        showToast("Modul pembuat PDF sedang dimuat, silakan coba sesaat lagi...");
        return;
      }

      if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
      }
      if (label) {
        label.textContent = "Menyiapkan PDF...";
      }
      showToast("Sedang memproses dokumen PDF, mohon tunggu...");

      const element = document.getElementById('contentContainer');
      const cleanFilename = (filename || 'Jadwal_Penugasan_Imam_Muadzin') + '.pdf';

      const opt = {
        margin: [8, 8, 8, 8],
        filename: cleanFilename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
          scale: 2,
          useCORS: true,
          letterRendering: true,
          logging: false
        },
        jsPDF: {
          unit: 'mm',
          format: 'a4',
          orientation: 'portrait'
        },
        pagebreak: {
          mode: ['avoid-all', 'css', 'legacy'],
          avoid: 'tr'
        }
      };

      try {
        await html2pdf().set(opt).from(element).save();
        showToast("Berkas PDF berhasil diunduh!");
      } catch (err) {
        console.error("Gagal generate PDF:", err);
        showToast("Gagal mengunduh PDF: " + (err.message || "Terjadi kesalahan"));
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
        if (label) {
          label.textContent = "Unduh PDF";
        }
        if (typeof lucide !== 'undefined') {
          lucide.createIcons();
        }
      }
    }

    // Otomatis refresh state dokumen setelah dialog cetak / AirPrint ditutup (baik selesai cetak maupun Cancel)
    // agar Safari WebKit me-reset status pencetakan dan tidak memblokir cetak berulang.
    window.addEventListener('afterprint', () => {
      setTimeout(() => {
        window.location.replace(window.location.href);
      }, 150);
    });
  </script>
</body>
</html>
