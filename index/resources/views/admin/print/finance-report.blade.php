<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    @if(($reportType ?? 'monthly') === 'agenda')
      LPJ Keuangan — {{ $agenda->title ?? 'Agenda' }} — {{ $settings->name ?? 'Masjid Salahuddin' }}
    @else
      Laporan Arus Kas — {{ $periodLabel ?? 'Bulanan' }} — {{ $settings->name ?? 'Masjid Salahuddin' }}
    @endif
  </title>
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

  @php
    $tteHash = $customTteHash ?? ('TTE-' . strtoupper(substr(md5(($settings->name ?? 'MS') . ($periodLabel ?? date('Y-m'))), 0, 10)));
    $verifyUrl = ($reportType ?? 'monthly') === 'agenda'
      ? url('/admin/finance/export-pdf?type=agenda&agenda_id=' . ($agenda->id ?? '') . '&tte=1&hash=' . $tteHash)
      : url('/admin/finance/export-pdf?type=monthly&month=' . ($month ?? date('n')) . '&year=' . ($year ?? date('Y')) . '&tte=1&hash=' . $tteHash);
  @endphp

  <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden flex flex-col transition-all print-shadow-none">
    
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
          href="{{ url('/admin/finance') }}" 
          class="hidden sm:inline-flex items-center gap-1 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-medium px-3 py-2 rounded-lg border border-slate-700 transition-colors" 
          title="Kembali ke Dashboard Keuangan">
          <span>← Kembali</span>
        </a>
      </div>
    </header>


    <main id="contentContainer" class="p-6 sm:p-8 space-y-4 text-slate-800 text-xs sm:text-[13px]">

      @if(($reportType ?? 'monthly') === 'agenda')
        <!-- ======================================================== -->
        <!-- MODE 2: LPJ KEUANGAN AGENDA KEGIATAN TEMATIK             -->
        <!-- ======================================================== -->
        <div class="text-center pt-0.5">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide inline-block border-b border-slate-800 pb-0.5">
            LAPORAN PERTANGGUNGJAWABAN (LPJ) KEUANGAN KEGIATAN
          </h3>
          <p class="text-xs font-bold text-slate-800 mt-1 uppercase">{{ strtoupper($agenda->title ?? 'PROGRAM KEGIATAN') }}</p>
          <p class="text-[11px] text-slate-500 font-normal mt-0.5">Waktu Pelaksanaan: {{ $agenda->formatted_date ?? '-' }}</p>
        </div>

        <!-- Info Box Metrik Anggaran Agenda -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 p-3 rounded-lg border border-slate-300 bg-slate-50/60">
          <div class="bg-white p-2.5 rounded border border-slate-200">
            <span class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider block">Anggaran Plafon</span>
            <div class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5 tabular-nums">Rp {{ number_format($agenda->budget ?: 0, 0, ',', '.') }}</div>
          </div>
          <div class="bg-white p-2.5 rounded border border-slate-200">
            <span class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider block">Total Dana Diterima</span>
            <div class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5 tabular-nums">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</div>
          </div>
          <div class="bg-white p-2.5 rounded border border-slate-200">
            <span class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider block">Total Realisasi Belanja</span>
            <div class="text-xs sm:text-sm font-bold text-slate-900 mt-0.5 tabular-nums">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
          </div>
          <div class="bg-slate-100 p-2.5 rounded border border-slate-300">
            <span class="text-[10px] uppercase font-semibold text-slate-700 tracking-wider block">Sisa Anggaran (Silpa)</span>
            <div class="text-xs sm:text-sm font-extrabold text-slate-950 mt-0.5 tabular-nums">Rp {{ number_format($silpa, 0, ',', '.') }}</div>
          </div>
        </div>


        <!-- Tabel LPJ Kegiatan -->
        <div class="overflow-x-auto border border-slate-300 rounded-lg">
          <table class="w-full text-left border-collapse text-[11px]">
            <thead>
              <tr class="table-header-blue text-white text-[11px] font-bold uppercase tracking-wider" style="background-color: #06172e !important; color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
                <th class="p-1.5 text-center border-b border-[#030c18] w-10" style="background-color: #06172e !important; color: #ffffff !important;">No</th>
                <th class="p-1.5 border-b border-[#030c18] w-24 text-center" style="background-color: #06172e !important; color: #ffffff !important;">Tanggal</th>
                <th class="p-1.5 border-b border-[#030c18] w-44" style="background-color: #06172e !important; color: #ffffff !important;">Pos / Kategori</th>
                <th class="p-1.5 border-b border-[#030c18]" style="background-color: #06172e !important; color: #ffffff !important;">Uraian / Keterangan Belanja</th>
                <th class="p-1.5 border-b border-[#030c18] w-32 text-right" style="background-color: #06172e !important; color: #ffffff !important;">Penerimaan (Rp)</th>
                <th class="p-1.5 border-b border-[#030c18] w-32 text-right" style="background-color: #06172e !important; color: #ffffff !important;">Pengeluaran (Rp)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-300">
              <!-- Bagian A: Penerimaan Dana Kegiatan -->
              <tr class="bg-slate-100 font-bold text-slate-900 text-[11px] uppercase tracking-wider">
                <td colspan="6" class="p-1.5 border-b border-slate-300">A. PENERIMAAN DANA &amp; DONASI KEGIATAN</td>
              </tr>
              @php $noA = 1; @endphp
              @forelse($penerimaan as $item)
                <tr class="odd:bg-white even:bg-slate-50/50">
                  <td class="p-1.5 text-center text-slate-500 border-r border-slate-200">{{ $noA++ }}</td>
                  <td class="p-1.5 text-center text-slate-600 border-r border-slate-200">{{ $item->transaction_date ? $item->transaction_date->format('d/m/Y') : '-' }}</td>
                  <td class="p-1.5 font-semibold text-slate-800 border-r border-slate-200">{{ $item->category?->name ?? 'Penerimaan Kas' }}</td>
                  <td class="p-1.5 border-r border-slate-200 text-slate-700">{{ $item->description }}</td>
                  <td class="p-1.5 text-right text-slate-900 font-semibold tabular-nums border-r border-slate-200">{{ number_format($item->amount, 0, ',', '.') }}</td>
                  <td class="p-1.5 text-right text-slate-400">-</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="p-1.5 text-center text-slate-400 italic">
                    (Alokasi kas masjid langsung atau tidak ada catatan donasi terpisah)
                  </td>
                </tr>
              @endforelse
              <tr class="bg-slate-50 font-bold text-slate-900 border-t border-slate-300">
                <td colspan="4" class="p-1.5 text-right text-[11px] uppercase">SUBTOTAL PENERIMAAN DANA (A):</td>
                <td class="p-1.5 text-right text-slate-900 font-bold tabular-nums border-r border-slate-200">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                <td class="p-1.5 text-right text-slate-400">-</td>
              </tr>

              <!-- Bagian B: Realisasi Belanja Panitia -->
              <tr class="bg-slate-100 font-bold text-slate-900 text-[11px] uppercase tracking-wider border-t-2 border-slate-300">
                <td colspan="6" class="p-1.5 border-b border-slate-300">B. REALISASI PENGELUARAN &amp; BELANJA KEGIATAN</td>
              </tr>
              @php $noB = 1; @endphp
              @forelse($pengeluaran as $item)
                <tr class="odd:bg-white even:bg-slate-50/50">
                  <td class="p-1.5 text-center text-slate-500 border-r border-slate-200">{{ $noB++ }}</td>
                  <td class="p-1.5 text-center text-slate-600 border-r border-slate-200">{{ $item->transaction_date ? $item->transaction_date->format('d/m/Y') : '-' }}</td>
                  <td class="p-1.5 font-semibold text-slate-800 border-r border-slate-200">{{ $item->category?->name ?? 'Belanja Kegiatan' }}</td>
                  <td class="p-1.5 border-r border-slate-200 text-slate-700">{{ $item->description }}</td>
                  <td class="p-1.5 text-right text-slate-400 border-r border-slate-200">-</td>
                  <td class="p-1.5 text-right text-slate-900 font-semibold tabular-nums">{{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="p-1.5 text-center text-slate-400 italic">
                    Tidak ada catatan rincian belanja pengeluaran kegiatan.
                  </td>
                </tr>
              @endforelse
              <tr class="bg-slate-50 font-bold text-slate-900 border-t border-slate-300">
                <td colspan="4" class="p-1.5 text-right text-[11px] uppercase">SUBTOTAL PENGELUARAN BELANJA (B):</td>
                <td class="p-1.5 text-right text-slate-400 border-r border-slate-200">-</td>
                <td class="p-1.5 text-right text-slate-900 font-bold tabular-nums">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="table-footer-blue text-white font-bold text-xs" style="background-color: #06172e !important; color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
                <td colspan="4" class="p-1.5 text-right uppercase tracking-wider" style="background-color: #06172e !important; color: #ffffff !important;">SISA ANGGARAN KEGIATAN (SILPA):</td>
                <td colspan="2" class="p-1.5 text-center font-bold text-xs tabular-nums" style="background-color: #06172e !important; color: #ffffff !important;">
                  Rp {{ number_format($silpa, 0, ',', '.') }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Tanda Tangan LPJ Kegiatan (3 Pihak) dengan QR Code Verifikasi Kedinasan -->
        <div class="pt-4 flex justify-between items-start page-break-avoid text-xs">
          <div class="w-48 text-center flex flex-col items-center">
            <p class="text-slate-600 mb-0.5 font-medium">Disusun oleh,</p>
            <p class="font-bold text-slate-900 mb-1">Bendahara Panitia</p>

            @if($isTteSigned ?? true)
              <div class="my-1.5 p-1.5 bg-white border border-slate-300 rounded shadow-2xs flex flex-col items-center">
                <div class="qrcode-container" data-qr-text="{{ $verifyUrl }}" title="Scan untuk verifikasi TTE resmi BSrE"></div>
                <span class="text-[8px] font-mono text-slate-500 mt-1 tracking-tight font-semibold uppercase">TTE BSrE DISAHKAN</span>
              </div>
            @else
              <div class="h-16"></div>
            @endif

            <p class="font-bold text-slate-900 underline underline-offset-2 mt-0.5">Bendahara Kegiatan</p>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Panitia Pelaksana</p>
          </div>

          <div class="w-48 text-center flex flex-col items-center">
            <p class="text-slate-600 mb-0.5 font-medium">Diperiksa oleh,</p>
            <p class="font-bold text-slate-900 mb-1">Ketua Panitia Pelaksana</p>

            @if($isTteSigned ?? true)
              <div class="my-1.5 p-1.5 bg-white border border-slate-300 rounded shadow-2xs flex flex-col items-center">
                <div class="qrcode-container" data-qr-text="{{ $verifyUrl }}" title="Scan untuk verifikasi TTE resmi BSrE"></div>
                <span class="text-[8px] font-mono text-slate-500 mt-1 tracking-tight font-semibold uppercase">TTE BSrE DISAHKAN</span>
              </div>
            @else
              <div class="h-16"></div>
            @endif

            <p class="font-bold text-slate-900 underline underline-offset-2 mt-0.5">Ketua Panitia</p>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Panitia Pelaksana</p>
          </div>

          <div class="w-56 text-center flex flex-col items-center">
            <p class="text-slate-600 mb-0.5 font-medium">Mengetahui &amp; Menyetujui,</p>
            <p class="font-bold text-slate-900 mb-1">Ketua DKM Masjid Salahuddin</p>

            @if($isTteSigned ?? true)
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

      @else
        <!-- ======================================================== -->
        <!-- MODE 1: LAPORAN ARUS KAS BULANAN BAKU DKM KEMENKEU        -->
        <!-- ======================================================== -->
        <div class="text-center pt-0.5">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide inline-block border-b border-slate-800 pb-0.5">
            LAPORAN ARUS KAS MASJID SALAHUDDIN KPP MADYA MALANG
          </h3>
          <p class="text-[11px] text-slate-600 font-medium mt-1">
            Periode: <strong class="text-slate-900 font-bold uppercase">{{ $periodLabel ?? 'BULANAN' }}</strong>
          </p>
        </div>

        <!-- Tabel Arus Kas Ringkas & Terpadu (4 Kolom Saja) -->
        <div class="overflow-x-auto border border-slate-300 rounded-lg">
          <table class="w-full text-left border-collapse text-[11px]">
            <thead>
              <tr class="table-header-blue text-white text-[11px] font-bold uppercase tracking-wider" style="background-color: #06172e !important; color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
                <th class="p-1.5 text-center border-b border-[#030c18] w-12" style="background-color: #06172e !important; color: #ffffff !important;">No</th>
                <th class="p-1.5 border-b border-[#030c18]" style="background-color: #06172e !important; color: #ffffff !important;">Pos Anggaran</th>
                <th class="p-1.5 border-b border-[#030c18] w-44 text-right" style="background-color: #06172e !important; color: #ffffff !important;">Penerimaan (Rp)</th>
                <th class="p-1.5 border-b border-[#030c18] w-44 text-right" style="background-color: #06172e !important; color: #ffffff !important;">Pengeluaran (Rp)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-300">
              <!-- BAGIAN I: PENERIMAAN -->
              <tr class="bg-slate-100 font-bold text-slate-900 text-[11px] uppercase tracking-wider">
                <td colspan="4" class="p-1.5 border-b border-slate-300">I. PENERIMAAN KAS DANA INFAQ</td>
              </tr>
              <tr class="odd:bg-white even:bg-slate-50/50">
                <td class="p-1.5 text-center text-slate-400 border-r border-slate-200">-</td>
                <td class="p-1.5 font-semibold text-slate-900 border-r border-slate-200">Saldo Awal Bulan</td>
                <td class="p-1.5 text-right font-semibold text-slate-900 tabular-nums border-r border-slate-200">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                <td class="p-1.5 text-right text-slate-400">-</td>
              </tr>
              @php $noIn = 1; @endphp
              @forelse($penerimaanGrouped as $item)
                <tr class="odd:bg-white even:bg-slate-50/50">
                  <td class="p-1.5 text-center text-slate-500 border-r border-slate-200">{{ $noIn++ }}</td>
                  <td class="p-1.5 font-medium text-slate-800 border-r border-slate-200">{{ $item->name }}</td>
                  <td class="p-1.5 text-right font-semibold text-slate-900 tabular-nums border-r border-slate-200">{{ number_format($item->amount, 0, ',', '.') }}</td>
                  <td class="p-1.5 text-right text-slate-400">-</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="p-1.5 text-center text-slate-400 italic">
                    Tidak ada catatan penerimaan pada periode ini.
                  </td>
                </tr>
              @endforelse
              <tr class="bg-slate-50 font-bold text-slate-900 border-t border-slate-300">
                <td colspan="2" class="p-1.5 text-right text-[11px] uppercase">SUBTOTAL PENERIMAAN</td>
                <td class="p-1.5 text-right font-bold text-slate-900 tabular-nums border-r border-slate-200">Rp {{ number_format($subtotalPenerimaan, 0, ',', '.') }}</td>
                <td class="p-1.5 text-right text-slate-400">-</td>
              </tr>
              <tr class="bg-slate-100 font-bold text-slate-900 border-t border-slate-300">
                <td colspan="2" class="p-1.5 text-right text-[11px] uppercase">TOTAL KAS TERSEDIA</td>
                <td class="p-1.5 text-right font-bold text-slate-900 tabular-nums border-r border-slate-200">Rp {{ number_format($totalKasTersedia, 0, ',', '.') }}</td>
                <td class="p-1.5 text-right text-slate-400">-</td>
              </tr>

              <!-- BAGIAN II: PENGELUARAN RUTIN -->
              <tr class="bg-slate-100 font-bold text-slate-900 text-[11px] uppercase tracking-wider border-t-2 border-slate-300">
                <td colspan="4" class="p-1.5 border-b border-slate-300">II. PENGELUARAN RUTIN</td>
              </tr>
              @php $noRutin = 1; @endphp
              @forelse($pengeluaranRutinGrouped as $item)
                <tr class="odd:bg-white even:bg-slate-50/50">
                  <td class="p-1.5 text-center text-slate-500 border-r border-slate-200">{{ $noRutin++ }}</td>
                  <td class="p-1.5 font-medium text-slate-800 border-r border-slate-200">{{ $item->name }}</td>
                  <td class="p-1.5 text-right text-slate-400 border-r border-slate-200">-</td>
                  <td class="p-1.5 text-right font-semibold text-slate-900 tabular-nums">{{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="p-1.5 text-center text-slate-400 italic">
                    Tidak ada belanja rutin pada periode ini.
                  </td>
                </tr>
              @endforelse
              <tr class="bg-slate-50 font-bold text-slate-900 border-t border-slate-300">
                <td colspan="2" class="p-1.5 text-right text-[11px] uppercase">SUBTOTAL PENGELUARAN RUTIN:</td>
                <td class="p-1.5 text-right text-slate-400 border-r border-slate-200">-</td>
                <td class="p-1.5 text-right font-bold text-slate-900 tabular-nums">Rp {{ number_format($subtotalRutin, 0, ',', '.') }}</td>
              </tr>

              <!-- BAGIAN III: PENGELUARAN NON-RUTIN -->
              <tr class="bg-slate-100 font-bold text-slate-900 text-[11px] uppercase tracking-wider border-t-2 border-slate-300">
                <td colspan="4" class="p-1.5 border-b border-slate-300">III. PENGELUARAN NON-RUTIN</td>
              </tr>
              @php $noNonRutin = 1; @endphp
              @forelse($pengeluaranNonRutinGrouped as $item)
                <tr class="odd:bg-white even:bg-slate-50/50">
                  <td class="p-1.5 text-center text-slate-500 border-r border-slate-200">{{ $noNonRutin++ }}</td>
                  <td class="p-1.5 font-medium text-slate-800 border-r border-slate-200">{{ $item->name }}</td>
                  <td class="p-1.5 text-right text-slate-400 border-r border-slate-200">-</td>
                  <td class="p-1.5 text-right font-semibold text-slate-900 tabular-nums">{{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="p-1.5 text-center text-slate-400 italic">
                    Tidak ada belanja non-rutin pada periode ini.
                  </td>
                </tr>
              @endforelse
              <tr class="bg-slate-50 font-bold text-slate-900 border-t border-slate-300">
                <td colspan="2" class="p-1.5 text-right text-[11px] uppercase">SUBTOTAL PENGELUARAN NON-RUTIN:</td>
                <td class="p-1.5 text-right text-slate-400 border-r border-slate-200">-</td>
                <td class="p-1.5 text-right font-bold text-slate-900 tabular-nums">Rp {{ number_format($subtotalNonRutin, 0, ',', '.') }}</td>
              </tr>

              <!-- REKAPITULASI TOTAL PENGELUARAN -->
              <tr class="bg-slate-100 font-bold text-slate-900 border-t-2 border-slate-300">
                <td colspan="2" class="p-1.5 text-right text-[11px] uppercase">TOTAL PENGELUARAN KAS</td>
                <td class="p-1.5 text-right text-slate-400 border-r border-slate-200">-</td>
                <td class="p-1.5 text-right font-bold text-slate-900 tabular-nums">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="table-footer-blue text-white font-bold text-xs" style="background-color: #06172e !important; color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;">
                <td colspan="2" class="p-1.5 text-right uppercase tracking-wider" style="background-color: #06172e !important; color: #ffffff !important;">SALDO KAS AKHIR</td>
                <td colspan="2" class="p-1.5 text-center font-bold text-xs tabular-nums" style="background-color: #06172e !important; color: #ffffff !important;">
                  Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- Tanda Tangan Resmi dengan QR Code Verifikasi Kedinasan -->
        <div class="pt-4 flex justify-between items-start page-break-avoid text-xs">
          <div class="w-56 text-center flex flex-col items-center">
            <p class="text-slate-600 mb-0.5 font-medium">Disusun oleh,</p>
            <p class="font-bold text-slate-900 mb-1">Bendahara DKM</p>

            @if($isTteSigned ?? true)
              <!-- QR Code TTE BSrE Standar Kedinasan -->
              <div class="my-1.5 p-1.5 bg-white border border-slate-300 rounded shadow-2xs flex flex-col items-center">
                <div class="qrcode-container" data-qr-text="{{ $verifyUrl }}" title="Scan untuk verifikasi TTE resmi BSrE"></div>
                <span class="text-[8px] font-mono text-slate-500 mt-1 tracking-tight font-semibold uppercase">TTE BSrE DISAHKAN</span>
              </div>
            @else
              <div class="h-16"></div>
            @endif

            <p class="font-bold text-slate-900 underline underline-offset-2 mt-0.5">{{ $bendaharaDkm?->name ?? 'Aris Setianto' }}</p>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Bendahara DKM</p>
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
      @endif

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
