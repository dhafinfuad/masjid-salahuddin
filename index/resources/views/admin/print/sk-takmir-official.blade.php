<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Keputusan Kepala KPP Madya Malang — Susunan Pengurus Takmir Masjid Salahuddin</title>

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
      margin: 1.5cm 1.5cm 1.5cm 1.5cm;
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
        border: none !important;
      }
      .page-break {
        page-break-before: always !important;
      }
    }

    body {
      background-color: #f1f5f9;
      color: #0f172a;
      font-size: 11pt;
      line-height: 1.5;
    }

    .page-container {
      width: 210mm;
      min-height: 297mm;
      background: #ffffff;
      margin: 20px auto;
      padding: 1.8cm 1.8cm 1.8cm 1.8cm;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      position: relative;
      box-sizing: border-box;
    }
  </style>
</head>
<body class="font-sans antialiased">

  <!-- Floating Print Action Header (Screen Only) -->
  <div class="no-print sticky top-0 z-50 bg-[#06172e] text-white border-b border-slate-700 py-3 px-4 shadow-md">
    <div class="max-w-4xl mx-auto flex items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <i data-lucide="file-text" class="w-5 h-5 text-amber-400"></i>
        <div>
          <span class="font-bold text-sm block leading-tight">Draf Dokumen Resmi Surat KEP Takmir</span>
          <span class="text-xs text-slate-300">Nomor: {{ $skNumber }} • Periode {{ $periode }}</span>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ url('/admin/kepengurusan') }}" class="px-3.5 py-1.5 rounded-lg border border-slate-600 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-xs font-semibold text-slate-200 transition cursor-pointer inline-flex items-center gap-1.5" title="Kembali ke Kepengurusan">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          <span>Kembali</span>
        </a>

        <!-- Action Buttons: Desktop (Cetak) & Mobile (Unduh PDF) -->
        <!-- Desktop: Tombol Cetak / Simpan PDF (hidden di mobile, inline-flex di sm+) -->
        <button 
          type="button" 
          onclick="window.print()" 
          class="hidden sm:inline-flex px-4 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 active:bg-slate-800 border border-slate-600 text-white font-semibold text-xs shadow-sm transition items-center gap-1.5 cursor-pointer"
          title="Cetak atau Simpan PDF (Browser)">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
          onclick="downloadOfficialSkPdf('Draf_Dokumen_Resmi_SK_Takmir_{{ $periode ?? '2026-2029' }}')" 
          class="inline-flex sm:hidden items-center gap-1.5 bg-slate-800 hover:bg-slate-700 active:bg-slate-900 text-slate-200 hover:text-white text-xs font-semibold px-3.5 py-1.5 rounded-lg border border-slate-700 transition-colors shadow-2xs cursor-pointer"
          title="Unduh File PDF">
          <svg class="w-4 h-4 shrink-0 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          <span id="labelDownloadMobile">Unduh PDF</span>
        </button>
      </div>
    </div>
  </div>

  <div id="printContainer">

  <!-- ========================================================================= -->
  <!-- HALAMAN 1: SURAT KEPUTUSAN KEPALA KPP MADYA MALANG -->
  <!-- ========================================================================= -->
  <div class="page-container">
    
    <!-- Kop Surat Resmi -->
    <div class="text-center pb-4 border-b-2 border-slate-900 mb-6 space-y-0.5">
      <h2 class="text-[12pt] font-extrabold uppercase tracking-wider text-slate-900">Kementerian Keuangan Republik Indonesia</h2>
      <h3 class="text-[11pt] font-bold uppercase tracking-wide text-slate-900">Direktorat Jenderal Pajak</h3>
      <h4 class="text-[10pt] font-semibold uppercase tracking-wide text-slate-800">Kantor Wilayah DJP Jawa Timur III</h4>
      <h4 class="text-[11pt] font-extrabold uppercase tracking-wide text-slate-950">Kantor Pelayanan Pajak Madya Malang</h4>
      <p class="text-[8.5pt] text-slate-600 mt-1">Jalan Danau Toba No. 1, Sawojajar, Kedungkandang, Kota Malang, Jawa Timur 65139</p>
    </div>

    <!-- Judul Dokumen KEP -->
    <div class="text-center my-6 space-y-1">
      <h1 class="text-[11.5pt] font-extrabold uppercase text-slate-950 tracking-wide">
        KEPUTUSAN KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG
      </h1>
      <p class="text-[10.5pt] font-bold text-slate-900">
        NOMOR: {{ $skNumber }}
      </p>
      <div class="pt-2">
        <p class="text-[10pt] font-bold uppercase text-slate-800 tracking-wide">TENTANG</p>
        <p class="text-[11pt] font-extrabold uppercase text-slate-950 mt-1 max-w-lg mx-auto leading-snug">
          PERUBAHAN SUSUNAN PENGURUS TAKMIR MASJID SALAHUDDIN KPP MADYA MALANG PERIODE {{ $periode }}
        </p>
      </div>
      <div class="pt-2">
        <p class="text-[10.5pt] font-extrabold uppercase text-slate-900">
          KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG,
        </p>
      </div>
    </div>

    <!-- Konsiderans Menimbang, Mengingat, Memutuskan -->
    <div class="space-y-3.5 text-[10pt] text-justify">
      
      <!-- Menimbang -->
      <table class="w-full align-top">
        <tr>
          <td class="w-24 font-bold align-top">Menimbang</td>
          <td class="w-4 align-top">:</td>
          <td class="align-top space-y-1.5">
            <div class="flex items-start gap-2">
              <span class="w-4 font-semibold">a.</span>
              <p>bahwa dalam rangka meningkatkan syiar Islam, pembinaan keimanan, dan ketakwaan pegawai di lingkungan Kantor Pelayanan Pajak Madya Malang, perlu didukung oleh kepengurusan Takmir Masjid yang berkesinambungan;</p>
            </div>
            <div class="flex items-start gap-2">
              <span class="w-4 font-semibold">b.</span>
              <p>bahwa sehubungan dengan adanya mutasi dan alih tugas pegawai di lingkungan Kantor Pelayanan Pajak Madya Malang, perlu dilakukan penyesuaian dan perubahan terhadap susunan kepengurusan Takmir Masjid Salahuddin;</p>
            </div>
            <div class="flex items-start gap-2">
              <span class="w-4 font-semibold">c.</span>
              <p>bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a dan huruf b, perlu menetapkan Keputusan Kepala Kantor Pelayanan Pajak Madya Malang tentang Perubahan Susunan Pengurus Takmir Masjid Salahuddin KPP Madya Malang Periode {{ $periode }};</p>
            </div>
          </td>
        </tr>
      </table>

      <!-- Mengingat -->
      <table class="w-full align-top mt-2">
        <tr>
          <td class="w-24 font-bold align-top">Mengingat</td>
          <td class="w-4 align-top">:</td>
          <td class="align-top space-y-1.5">
            <div class="flex items-start gap-2">
              <span class="w-4 font-semibold">1.</span>
              <p>Undang-Undang Nomor 20 Tahun 2023 tentang Aparatur Sipil Negara;</p>
            </div>
            <div class="flex items-start gap-2">
              <span class="w-4 font-semibold">2.</span>
              <p>Peraturan Menteri Keuangan Nomor 118/PMK.01/2021 tentang Organisasi dan Tata Kerja Kementerian Keuangan sebagaimana telah diubah beberapa kali terakhir dengan PMK Nomor 135 Tahun 2023;</p>
            </div>
            <div class="flex items-start gap-2">
              <span class="w-4 font-semibold">3.</span>
              <p>Anggaran Dasar dan Anggaran Rumah Tangga Pembinaan Kerohanian Islam di lingkungan Direktorat Jenderal Pajak;</p>
            </div>
          </td>
        </tr>
      </table>

      <!-- Memutuskan -->
      <div class="text-center font-extrabold uppercase tracking-wider text-[10.5pt] py-2">
        MEMUTUSKAN:
      </div>

      <table class="w-full align-top">
        <tr>
          <td class="w-24 font-bold align-top">Menetapkan</td>
          <td class="w-4 align-top">:</td>
          <td class="align-top font-bold uppercase">
            KEPUTUSAN KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG TENTANG PERUBAHAN SUSUNAN PENGURUS TAKMIR MASJID SALAHUDDIN KPP MADYA MALANG PERIODE {{ $periode }}.
          </td>
        </tr>
      </table>

      <!-- Diktum KESATU s.d. KELIMA -->
      <div class="space-y-2 pt-1 pl-7">
        <div class="flex items-start gap-2">
          <span class="font-bold uppercase w-20 shrink-0">KESATU</span>
          <span class="w-2 shrink-0">:</span>
          <p>Memberhentikan dengan hormat susunan pengurus Takmir Masjid Salahuddin periode sebelumnya dan menyampaikan ucapan terima kasih atas segala dedikasi dan pengabdian yang telah diberikan.</p>
        </div>
        <div class="flex items-start gap-2">
          <span class="font-bold uppercase w-20 shrink-0">KEDUA</span>
          <span class="w-2 shrink-0">:</span>
          <p>Menetapkan susunan personalia pengurus Takmir Masjid Salahuddin Kantor Pelayanan Pajak Madya Malang Periode {{ $periode }} dengan rincian sebagaimana tercantum dalam <strong>Lampiran I</strong> yang merupakan bagian tidak terpisahkan dari Keputusan ini.</p>
        </div>
        <div class="flex items-start gap-2">
          <span class="font-bold uppercase w-20 shrink-0">KETIGA</span>
          <span class="w-2 shrink-0">:</span>
          <p>Menetapkan rincian penjabaran tugas pokok dan fungsi (Tupoksi) Pengurus Takmir Masjid Salahuddin Kantor Pelayanan Pajak Madya Malang Periode {{ $periode }} sebagaimana tercantum dalam <strong>Lampiran II</strong> yang merupakan bagian tidak terpisahkan dari Keputusan ini.</p>
        </div>
        <div class="flex items-start gap-2">
          <span class="font-bold uppercase w-20 shrink-0">KEEMPAT</span>
          <span class="w-2 shrink-0">:</span>
          <p>Segala biaya yang timbul sebagai akibat ditetapkannya Keputusan ini dibebankan pada dana infaq dan kas Takmir Masjid Salahuddin KPP Madya Malang secara akuntabel.</p>
        </div>
        <div class="flex items-start gap-2">
          <span class="font-bold uppercase w-20 shrink-0">KELIMA</span>
          <span class="w-2 shrink-0">:</span>
          <p>Keputusan ini mulai berlaku sejak tanggal ditetapkan, dengan ketentuan apabila di kemudian hari terdapat kekeliruan dalam penetapannya akan diadakan perbaikan sebagaimana mestinya.</p>
        </div>
      </div>

    </div>

    <!-- Tanda Tangan Pembina / Kepala Kantor -->
    <div class="mt-8 flex justify-end text-[10pt]">
      <div class="w-72 text-left space-y-1">
        <p>Ditetapkan di Malang</p>
        <p>pada tanggal {{ $skDate }}</p>
        <p class="font-bold uppercase mt-2">
          KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG,
        </p>
        @if(!empty($qrCodeBase64))
          <div class="my-1.5 p-1 bg-white border border-slate-300 rounded shadow-2xs inline-block text-center">
            <img src="{{ $qrCodeBase64 }}" class="w-12 h-12 mx-auto block" alt="QR Code TTE BSrE">
            <div class="text-[6.5px] font-mono font-bold text-slate-600 mt-0.5 tracking-wider uppercase">TTE BSrE DISAHKAN</div>
          </div>
        @else
          <div class="h-20"></div>
        @endif
        <p class="font-extrabold text-[10.5pt] underline text-slate-900 uppercase">
          {{ $struct['pembina']['name'] ?? 'Teguh Iman Wirotomo' }}
        </p>
        <p class="text-[9pt] text-slate-600">
          Pembina Takmir Masjid Salahuddin
        </p>
      </div>
    </div>

  </div>


  <!-- ========================================================================= -->
  <!-- HALAMAN 2: LAMPIRAN I (SUSUNAN PENGURUS TAKMIR) -->
  <!-- ========================================================================= -->
  <div class="page-container page-break">
    
    <!-- Kop Lampiran I -->
    <div class="pb-3 border-b border-slate-400 mb-6 flex items-start justify-between text-[9pt]">
      <div class="font-semibold text-slate-600">
        KANTOR PELAYANAN PAJAK MADYA MALANG
      </div>
      <div class="text-right text-slate-800 space-y-0.5">
        <p class="font-bold">LAMPIRAN I KEPUTUSAN KEPALA KPP MADYA MALANG</p>
        <p>NOMOR : {{ $skNumber }}</p>
        <p>TANGGAL : {{ $skDate }}</p>
      </div>
    </div>

    <div class="text-center my-4 space-y-1">
      <h2 class="text-[11pt] font-extrabold uppercase text-slate-950">
        SUSUNAN PENGURUS TAKMIR MASJID SALAHUDDIN
      </h2>
      <p class="text-[10pt] font-bold text-slate-800 uppercase">
        KANTOR PELAYANAN PAJAK MADYA MALANG PERIODE {{ $periode }}
      </p>
    </div>

    <!-- Tabel Susunan Pengurus -->
    <div class="mt-5 text-[9.5pt]">
      <table class="w-full border-collapse border border-slate-400">
        <thead>
          <tr class="bg-slate-100 text-slate-900 font-bold uppercase text-center border-b border-slate-400">
            <th class="w-10 border border-slate-400 py-2 px-2">No</th>
            <th class="w-56 border border-slate-400 py-2 px-3 text-left">Jabatan dalam Takmir</th>
            <th class="border border-slate-400 py-2 px-3 text-left">Nama Pengurus</th>
            <th class="w-36 border border-slate-400 py-2 px-2 text-center">Kedudukan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-300">
          <!-- Pembina -->
          <tr>
            <td class="border border-slate-400 py-2 px-2 text-center font-bold">1</td>
            <td class="border border-slate-400 py-2 px-3 font-bold text-slate-900">Pembina</td>
            <td class="border border-slate-400 py-2 px-3 font-bold text-slate-900">{{ $struct['pembina']['name'] ?? 'Teguh Iman Wirotomo' }}</td>
            <td class="border border-slate-400 py-2 px-2 text-center text-xs">Ex-Officio Kepala Kantor</td>
          </tr>

          <!-- Ketua -->
          <tr>
            <td class="border border-slate-400 py-2 px-2 text-center font-bold">2</td>
            <td class="border border-slate-400 py-2 px-3 font-semibold text-slate-900">Ketua Takmir</td>
            <td class="border border-slate-400 py-2 px-3 font-semibold text-slate-900">{{ $struct['ketua']['name'] ?? 'Moh. Nazil Fuadi Kusmawan' }}</td>
            <td class="border border-slate-400 py-2 px-2 text-center text-xs">Ketua Harian</td>
          </tr>

          <!-- Wakil Ketua -->
          <tr>
            <td class="border border-slate-400 py-2 px-2 text-center font-bold">3</td>
            <td class="border border-slate-400 py-2 px-3 font-semibold text-slate-900">Wakil Ketua Takmir</td>
            <td class="border border-slate-400 py-2 px-3 font-semibold text-slate-900">{{ $struct['wakil_ketua']['name'] ?? 'Mujiburrokhman' }}</td>
            <td class="border border-slate-400 py-2 px-2 text-center text-xs">Wakil Ketua</td>
          </tr>

          <!-- Sekretaris -->
          <tr>
            <td class="border border-slate-400 py-2 px-2 text-center font-bold">4</td>
            <td class="border border-slate-400 py-2 px-3 font-semibold text-slate-900">Sekretaris</td>
            <td class="border border-slate-400 py-2 px-3">
              <ol class="list-decimal list-inside space-y-0.5">
                @foreach($struct['sekretaris']['names'] ?? [] as $sName)
                  <li class="font-medium text-slate-900">{{ $sName }}</li>
                @endforeach
              </ol>
            </td>
            <td class="border border-slate-400 py-2 px-2 text-center text-xs">Pengurus Harian</td>
          </tr>

          <!-- Bendahara -->
          <tr>
            <td class="border border-slate-400 py-2 px-2 text-center font-bold">5</td>
            <td class="border border-slate-400 py-2 px-3 font-semibold text-slate-900">Bendahara</td>
            <td class="border border-slate-400 py-2 px-3">
              <ol class="list-decimal list-inside space-y-0.5">
                @foreach($struct['bendahara']['names'] ?? [] as $bName)
                  <li class="font-medium text-slate-900">{{ $bName }}</li>
                @endforeach
              </ol>
            </td>
            <td class="border border-slate-400 py-2 px-2 text-center text-xs">Pengurus Harian</td>
          </tr>

          <!-- 4 Bidang -->
          @foreach($struct['bidang'] ?? [] as $idx => $b)
            <tr>
              <td class="border border-slate-400 py-2 px-2 text-center font-bold">{{ $idx + 6 }}</td>
              <td class="border border-slate-400 py-2 px-3 font-bold text-slate-900">
                {{ $b['name'] }}
              </td>
              <td class="border border-slate-400 py-2 px-3 space-y-1.5">
                <!-- Pengelola / Koordinator -->
                <div>
                  <span class="text-[8.5pt] font-bold text-slate-600 uppercase block">Koordinator / Pengelola:</span>
                  <div class="flex flex-wrap gap-x-3 gap-y-0.5 font-semibold text-slate-900">
                    {{ implode(', ', $b['pengelola'] ?? []) ?: '-' }}
                  </div>
                </div>

                <!-- Anggota Bidang -->
                @if(!empty($b['anggota']))
                  <div class="pt-1 border-t border-slate-200">
                    <span class="text-[8.5pt] font-bold text-slate-600 uppercase block">Anggota Bidang:</span>
                    <div class="text-slate-800 text-[9pt] leading-relaxed">
                      {{ implode(', ', $b['anggota']) }}
                    </div>
                  </div>
                @endif
              </td>
              <td class="border border-slate-400 py-2 px-2 text-center text-xs">Pengelola Bidang</td>
            </tr>
          @endforeach

        </tbody>
      </table>
    </div>

    <!-- Tanda Tangan Pembina -->
    <div class="mt-8 flex justify-end text-[10pt]">
      <div class="w-72 text-left space-y-1">
        <p class="font-bold uppercase">
          KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG,
        </p>
        @if(!empty($qrCodeBase64))
          <div class="my-1.5 p-1 bg-white border border-slate-300 rounded shadow-2xs inline-block text-center">
            <img src="{{ $qrCodeBase64 }}" class="w-12 h-12 mx-auto block" alt="QR Code TTE BSrE">
            <div class="text-[6.5px] font-mono font-bold text-slate-600 mt-0.5 tracking-wider uppercase">TTE BSrE DISAHKAN</div>
          </div>
        @else
          <div class="h-16"></div>
        @endif
        <p class="font-extrabold text-[10.5pt] underline text-slate-900 uppercase">
          {{ $struct['pembina']['name'] ?? 'Teguh Iman Wirotomo' }}
        </p>
      </div>
    </div>

  </div>


  <!-- ========================================================================= -->
  <!-- HALAMAN 3: LAMPIRAN II (PENJABARAN TUPOKSI) -->
  <!-- ========================================================================= -->
  <div class="page-container page-break">
    
    <!-- Kop Lampiran II -->
    <div class="pb-3 border-b border-slate-400 mb-6 flex items-start justify-between text-[9pt]">
      <div class="font-semibold text-slate-600">
        KANTOR PELAYANAN PAJAK MADYA MALANG
      </div>
      <div class="text-right text-slate-800 space-y-0.5">
        <p class="font-bold">LAMPIRAN II KEPUTUSAN KEPALA KPP MADYA MALANG</p>
        <p>NOMOR : {{ $skNumber }}</p>
        <p>TANGGAL : {{ $skDate }}</p>
      </div>
    </div>

    <div class="text-center my-4 space-y-1">
      <h2 class="text-[11pt] font-extrabold uppercase text-slate-950">
        PENJABARAN TUGAS POKOK DAN FUNGSI (TUPOKSI)
      </h2>
      <p class="text-[10pt] font-bold text-slate-800 uppercase">
        PENGURUS TAKMIR MASJID SALAHUDDIN KPP MADYA MALANG PERIODE {{ $periode }}
      </p>
    </div>

    <!-- Rincian Tupoksi per Jabatan -->
    <div class="mt-5 space-y-4 text-[9.5pt]">
      
      <!-- 1. Pembina -->
      <div class="p-3.5 rounded-lg border border-slate-300 bg-slate-50/50 space-y-1">
        <h3 class="font-bold text-[10pt] text-slate-900 uppercase">1. Pembina Takmir</h3>
        <ul class="list-disc list-inside space-y-1 text-slate-800 pl-2">
          @forelse($struct['pembina']['tugas'] ?? [] as $tugas)
            <li>{{ $tugas }}</li>
          @empty
            <li>Memberikan arahan umum dan pembinaan keagamaan masjid agar tetap lurus di atas syariat Islam.</li>
            <li>Mengawasi keselarasan program kerja Takmir dengan kebijakan institusi/lingkungan.</li>
          @endforelse
        </ul>
      </div>

      <!-- 2. Ketua Takmir -->
      <div class="p-3.5 rounded-lg border border-slate-300 bg-slate-50/50 space-y-1">
        <h3 class="font-bold text-[10pt] text-slate-900 uppercase">2. Ketua Takmir</h3>
        <ul class="list-disc list-inside space-y-1 text-slate-800 pl-2">
          @forelse($struct['ketua']['tugas'] ?? [] as $tugas)
            <li>{{ $tugas }}</li>
          @empty
            <li>Memimpin, mengoordinasikan, dan memantau seluruh aktivitas pelaksanaan program Takmir Masjid.</li>
            <li>Menjalin hubungan internal dengan pimpinan kantor serta jaringan dakwah eksternal.</li>
          @endforelse
        </ul>
      </div>

      <!-- 3. Wakil Ketua Takmir -->
      <div class="p-3.5 rounded-lg border border-slate-300 bg-slate-50/50 space-y-1">
        <h3 class="font-bold text-[10pt] text-slate-900 uppercase">3. Wakil Ketua Takmir</h3>
        <ul class="list-disc list-inside space-y-1 text-slate-800 pl-2">
          @forelse($struct['wakil_ketua']['tugas'] ?? [] as $tugas)
            <li>{{ $tugas }}</li>
          @empty
            <li>Mendampingi Ketua Takmir dalam mengarahkan operasional kepemimpinan harian.</li>
            <li>Mewakili penandatanganan dokumen atau pertemuan takmir apabila Ketua berhalangan.</li>
          @endforelse
        </ul>
      </div>

      <!-- 4. Sekretaris -->
      <div class="p-3.5 rounded-lg border border-slate-300 bg-slate-50/50 space-y-1">
        <h3 class="font-bold text-[10pt] text-slate-900 uppercase">4. Sekretaris</h3>
        <ul class="list-disc list-inside space-y-1 text-slate-800 pl-2">
          @forelse($struct['sekretaris']['tugas'] ?? [] as $tugas)
            <li>{{ $tugas }}</li>
          @empty
            <li>Mengelola administrasi persuratan resmi, proposal, notulensi rapat, database kepengurusan, dan pengarsipan.</li>
            <li>Mempersiapkan draf laporan berkala s.d. Laporan Pertanggungjawaban (LPJ).</li>
          @endforelse
        </ul>
      </div>

      <!-- 5. Bendahara -->
      <div class="p-3.5 rounded-lg border border-slate-300 bg-slate-50/50 space-y-1">
        <h3 class="font-bold text-[10pt] text-slate-900 uppercase">5. Bendahara</h3>
        <ul class="list-disc list-inside space-y-1 text-slate-800 pl-2">
          @forelse($struct['bendahara']['tugas'] ?? [] as $tugas)
            <li>{{ $tugas }}</li>
          @empty
            <li>Mengelola dan mengontrol seluruh kas penerimaan, pengeluaran riil, dan mutasi saldo infaq/zakat.</li>
            <li>Menyusun laporan keuangan berkala untuk dipublikasikan secara transparan kepada jamaah.</li>
          @endforelse
        </ul>
      </div>

      <!-- 6. 4 Bidang Pengelola -->
      @foreach($struct['bidang'] ?? [] as $idx => $b)
        <div class="p-3.5 rounded-lg border border-slate-300 bg-slate-50/50 space-y-1">
          <h3 class="font-bold text-[10pt] text-slate-900 uppercase">{{ $idx + 6 }}. {{ $b['name'] }}</h3>
          <ul class="list-disc list-inside space-y-1 text-slate-800 pl-2">
            @forelse($b['tupoksi'] ?? [] as $tup)
              <li>{{ $tup }}</li>
            @empty
              <li>Melaksanakan seluruh agenda operasional dan program kerja yang berada dalam lingkup bidang.</li>
            @endforelse
          </ul>
        </div>
      @endforeach

    </div>

    <!-- Tanda Tangan Pembina -->
    <div class="mt-8 flex justify-end text-[10pt]">
      <div class="w-72 text-left space-y-1">
        <p class="font-bold uppercase">
          KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG,
        </p>
        @if(!empty($qrCodeBase64))
          <div class="my-1.5 p-1 bg-white border border-slate-300 rounded shadow-2xs inline-block text-center">
            <img src="{{ $qrCodeBase64 }}" class="w-12 h-12 mx-auto block" alt="QR Code TTE BSrE">
            <div class="text-[6.5px] font-mono font-bold text-slate-600 mt-0.5 tracking-wider uppercase">TTE BSrE DISAHKAN</div>
          </div>
        @else
          <div class="h-16"></div>
        @endif
        <p class="font-extrabold text-[10.5pt] underline text-slate-900 uppercase">
          {{ $struct['pembina']['name'] ?? 'Teguh Iman Wirotomo' }}
        </p>
      </div>
    </div>

  </div>
  </div>

  <script>
    if (window.lucide) {
      window.lucide.createIcons();
    }

    async function downloadOfficialSkPdf(filename) {
      const url = "{{ route('admin.kepengurusan.download-sk-pdf') }}" + window.location.search;
      const cleanFilename = (filename || 'Draf_Dokumen_Resmi_SK_Takmir') + '.pdf';

      if (typeof window.downloadPdfDirect === 'function') {
        await window.downloadPdfDirect(url, cleanFilename);
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
        const file = new File([blob], cleanFilename, { type: 'application/pdf' });

        if (navigator.canShare && navigator.canShare({ files: [file] })) {
          await navigator.share({
            files: [file],
            title: cleanFilename,
            text: 'Draf Dokumen Resmi Surat KEP Takmir'
          });
          return;
        }

        const blobUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = blobUrl;
        a.download = cleanFilename;
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
