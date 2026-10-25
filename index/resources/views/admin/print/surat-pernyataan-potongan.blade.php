<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Surat Pernyataan Kesediaan Pemotongan Tunjangan Kinerja — {{ $settings->name ?? 'Masjid Salahuddin' }}</title>

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- html2pdf.js for Client-Side PDF Generation on Mobile -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'Arial', 'sans-serif'],
          },
          colors: {
            gov: {
              navy: '#06172e',
              gold: '#ffb900',
              border: '#c3c9d4',
              text: '#1d1d1b',
            }
          }
        }
      }
    }
  </script>

  <style>
    @page {
      size: A4 portrait;
      margin: 0;
    }

    *, *::before, *::after {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
      color-adjust: exact !important;
    }

    @media print {
      .no-print {
        display: none !important;
      }
      body {
        background-color: #ffffff !important;
        padding: 0 !important;
      }
      .page-container {
        box-shadow: none !important;
        margin: 0 auto !important;
        page-break-after: always !important;
        break-after: page !important;
      }
    }
  </style>
</head>

<body class="bg-slate-100 text-slate-800 font-['Plus_Jakarta_Sans',Arial,sans-serif] antialiased min-h-screen">

  <!-- Top Action Bar (Header) Sesuai Standar Proyek -->
  <header class="no-print bg-slate-900 text-white px-4 sm:px-6 py-2.5 flex items-center justify-between gap-2 border-b border-slate-800 sticky top-0 z-30 shadow-sm">
    <!-- Tombol Kembali -->
    <a 
      href="{{ route('admin.tab', ['tab' => 'programs']) }}" 
      class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-200 hover:text-white text-xs sm:text-sm font-semibold px-3.5 py-2 rounded-lg border border-slate-700 transition-colors shadow-2xs cursor-pointer"
      title="Kembali ke Manajemen Program Sosial">
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
        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <polyline points="6 9 6 2 18 2 18 9"></polyline>
          <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
          <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        <span>Cetak / Simpan PDF</span>
      </button>

      <!-- Mobile: Tombol Unduh PDF (inline-flex di mobile, hidden di sm+) -->
      <button 
        type="button"
        id="btnDownloadMobile"
        onclick="downloadSuratPernyataanPdf()" 
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

  <div id="printContainer" class="py-4 sm:py-6 print:py-0 overflow-x-auto">
  <!-- Loop Per Lembar Pegawai (A4 = 794px x 1123px) -->
  @foreach($pages as $index => $page)
    <div class="page-container mx-auto flex h-[1123px] w-[794px] flex-col overflow-hidden bg-white text-[#1d1d1b] shadow-lg print:shadow-none mb-8 print:mb-0">

      <!-- Header Surat -->
      <header class="flex-none border-b-[5px] border-[#ffb900] bg-[#06172e] px-10 pb-3.5 pt-5 text-white">
        <div class="text-xs font-semibold uppercase tracking-[0.12em] text-[#ffb900]">
          {{ $settings->name ?? 'Masjid Salahuddin' }} KPP Madya Malang
        </div>
        <h1 class="mt-1 text-xl font-extrabold leading-tight">
          Surat Pernyataan Kesediaan Pemotongan Tunjangan Kinerja
        </h1>
      </header>

      <!-- Main Body Konten Surat -->
      <main class="flex flex-1 flex-col gap-3.5 px-10 pb-4 pt-3.5">

        <!-- 1. Data Pegawai -->
        <section class="grid grid-cols-[140px_1fr] items-end gap-x-4 gap-y-1.5 text-xs sm:text-[13px]">
          <b class="font-semibold text-[#06172e]">Nama</b>
          @if(!empty($page['name']))
            <span class="h-5 border-b border-[#8a8a8a] flex items-center font-bold text-slate-900">{{ $page['name'] }}</span>
          @else
            <span class="h-5 border-b border-[#8a8a8a]"></span>
          @endif

          <b class="font-semibold text-[#06172e]">NIP</b>
          @if(!empty($page['nip']))
            <span class="h-5 border-b border-[#8a8a8a] flex items-center font-medium text-slate-800">{{ $page['nip'] }}</span>
          @else
            <span class="h-5 border-b border-[#8a8a8a]"></span>
          @endif

          <b class="font-semibold text-[#06172e]">Jabatan</b>
          @if(!empty($page['jabatan']))
            <span class="h-5 border-b border-[#8a8a8a] flex items-center font-medium text-slate-800">{{ $page['jabatan'] }}</span>
          @else
            <span class="h-5 border-b border-[#8a8a8a]"></span>
          @endif

          <b class="font-semibold text-[#06172e]">Seksi/Kelompok</b>
          @if(!empty($page['seksi']))
            <span class="h-5 border-b border-[#8a8a8a] flex items-center font-medium text-slate-800">{{ $page['seksi'] }}</span>
          @else
            <span class="h-5 border-b border-[#8a8a8a]"></span>
          @endif

          <b class="font-semibold text-[#06172e]">Nomor WhatsApp</b>
          @if(!empty($page['phone']))
            <span class="h-5 border-b border-[#8a8a8a] flex items-center font-medium text-slate-800">{{ $page['phone'] }}</span>
          @else
            <span class="h-5 border-b border-[#8a8a8a]"></span>
          @endif
        </section>

        <!-- 2. Pernyataan & Rincian Program Pemotongan -->
        <section>
          <p class="mb-2 text-justify text-xs sm:text-[13px] leading-relaxed">
            Saya bersedia dipotong Tunjangan Kinerja setiap bulan oleh Bendahara KPP Madya Malang,
            untuk disalurkan kepada Bendahara Masjid Salahuddin, dengan rincian berikut.
            Pemotongan berlaku mulai bulan <span class="font-semibold">{{ $effectiveMonth ?: '________' }}</span> tahun <span class="font-semibold">{{ $effectiveYear ?: '20____' }}</span>.
          </p>

          <table class="w-full border-collapse text-xs sm:text-[13px] leading-[1.3]
                        [&_th]:border [&_th]:border-[#c3c9d4] [&_th]:bg-[#06172e] [&_th]:px-3 [&_th]:py-1 [&_th]:text-left [&_th]:font-bold [&_th]:text-white
                        [&_td]:border [&_td]:border-[#c3c9d4] [&_td]:px-3 [&_td]:py-1">
            <thead>
              <tr>
                <th class="w-9 !text-center">No</th>
                <th>Program</th>
                <th class="w-[200px]">Nominal per Bulan</th>
              </tr>
            </thead>
            <tbody>
              @foreach($page['programs'] as $pKey => $p)
                <tr>
                  <td class="text-center font-semibold text-slate-700">{{ $p['order'] }}</td>
                  <td class="font-semibold text-slate-900">{{ $p['label'] }}</td>
                  <td></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>

        <!-- 3. Tanda Tangan Yang Menyatakan (Format Garis Lurus Solid Tanpa Tabrakan Teks) -->
        <section class="flex justify-end text-xs sm:text-[13px]">
          <div class="text-center min-w-[240px]">
            <div class="flex items-center justify-center gap-1.5 whitespace-nowrap leading-none mb-2">
              <span class="text-slate-800">Malang,</span>
              @if(!empty($dateLetter))
                <span class="font-medium text-slate-900">{{ $dateLetter }}</span>
              @else
                <span class="inline-block w-28 border-b border-slate-700 h-4"></span>
                <span class="text-slate-800">20</span>
                <span class="inline-block w-12 border-b border-slate-700 h-4"></span>
              @endif
            </div>
            <p class="leading-none mb-2 text-[#06172e]">Yang menyatakan,</p>
            @if(!empty($page['qr_code']))
              <div class="inline-block my-1 p-1 bg-white border border-slate-300 rounded shadow-2xs text-center">
                <img src="{{ $page['qr_code'] }}" class="w-12 h-12 mx-auto block" alt="QR Verifikasi Blangko">
                <div class="text-[6.5px] font-mono font-bold text-slate-600 mt-0.5 tracking-wider uppercase">BLANGKO RESMI KEMENKEU</div>
              </div>
            @else
              <div class="h-10"></div>
            @endif
            <p class="font-bold underline underline-offset-2 text-slate-900 leading-none mt-2">
              ( {{ $page['name'] ?: '______________________' }} )
            </p>
          </div>
        </section>

        <!-- 4. Riwayat Potongan (Kondisional: Disembunyikan jika mode Pegawai Baru / Tanpa Riwayat) -->
        @if($page['show_history'])
          <section>
            <div class="flex items-center justify-between mb-1.5">
              <h2 class="text-xs sm:text-sm font-bold text-[#06172e]">
                Riwayat Potongan Tahun {{ $year }}
              </h2>
              <span class="text-[10.5px] text-slate-500 italic">*Nominal dalam ribuan rupiah</span>
            </div>

            <table class="w-full border-collapse text-[11px] sm:text-xs leading-[1.3]
                          [&_th]:border [&_th]:border-[#c3c9d4] [&_th]:bg-[#06172e] [&_th]:px-0.5 [&_th]:py-1 [&_th]:text-center [&_th]:font-bold [&_th]:text-white
                          [&_td]:h-6 [&_td]:border [&_td]:border-[#c3c9d4] [&_td]:px-0.5 [&_td]:py-0.5 [&_td]:text-center">
              <thead>
                <tr>
                  <th class="w-[155px] !pl-2 !text-left">Program</th>
                  <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th>
                  <th>7</th><th>8</th><th>9</th><th>10</th><th>11</th><th>12</th>
                </tr>
              </thead>
              <tbody>
                @foreach($page['history'] as $pKey => $h)
                  <tr>
                    <td class="!pl-2 !text-left font-semibold text-slate-800">{{ $h['label'] }}</td>
                    @for($m = 1; $m <= 12; $m++)
                      @php
                        $mVal = $h['months'][$m];
                        $hasVal = ($mVal !== '-');
                      @endphp
                      <td class="{{ $hasVal ? 'font-bold text-slate-900 bg-amber-50/50' : 'text-slate-300' }}">
                        {{ $mVal }}
                      </td>
                    @endfor
                  </tr>
                @endforeach
              </tbody>
            </table>
          </section>
        @endif

        <!-- 5. Footer Surat -->
        <footer class="mt-auto flex flex-col gap-1 border-t border-[#c3c9d4] pt-2 pb-1 text-xs leading-normal">
          <p class="text-slate-700">
            Mohon dikumpulkan kepada Sdr. <b class="text-slate-900">{{ $secretaryName }}</b>, Sekretaris Masjid Salahuddin
            ({{ $secretarySection }}).
          </p>
        </footer>

      </main>
    </div>
  @endforeach
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) {
        window.lucide.createIcons();
      }
    });

    async function downloadSuratPernyataanPdf() {
      const url = "{{ route('admin.programs.download-pdf') }}" + window.location.search;
      const filename = 'Surat_Pernyataan_Pemotongan_Tunjangan_Kinerja.pdf';

      if (typeof window.downloadPdfDirect === 'function') {
        await window.downloadPdfDirect(url, filename);
        return;
      }

      const btn = document.getElementById('btnDownloadMobile');
      const label = document.getElementById('labelDownloadMobile');

      if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
      }
      if (label) {
        label.textContent = "Mengunduh...";
      }

      try {
        const response = await fetch(url, {
          headers: { 'Accept': 'application/pdf' }
        });
        if (!response.ok) {
          throw new Error("Gagal mengambil file PDF: " + response.statusText);
        }

        const blob = await response.blob();
        const file = new File([blob], filename, { type: 'application/pdf' });

        if (navigator.canShare && navigator.canShare({ files: [file] })) {
          await navigator.share({
            files: [file],
            title: filename,
            text: 'Dokumen Resmi Surat Pernyataan Potongan Tukin'
          });
          return;
        }

        const blobUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = blobUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(() => window.URL.revokeObjectURL(blobUrl), 3000);
      } catch (err) {
        if (err.name !== 'AbortError') {
          console.error("Gagal generate PDF:", err);
          window.location.href = url;
        }
      } finally {
        if (btn) {
          btn.disabled = false;
          btn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
        if (label) {
          label.textContent = "Unduh PDF";
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
