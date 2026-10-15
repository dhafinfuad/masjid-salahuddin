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

<body class="bg-neutral-200 py-8 print:bg-white print:py-0 font-['Plus_Jakarta_Sans',Arial,sans-serif]">

  <!-- Top Floating Action Bar (Hanya tampil di browser, otomatis disembunyikan saat cetak) -->
  <header class="no-print fixed top-0 inset-x-0 z-50 bg-[#06172e] text-white px-4 sm:px-8 py-3 flex items-center justify-between gap-4 shadow-md border-b border-slate-800">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-lg bg-amber-400 text-slate-900 flex items-center justify-center font-bold text-sm">
        <i data-lucide="file-text" class="w-4 h-4"></i>
      </div>
      <div>
        <h1 class="text-sm font-bold text-white leading-tight">Surat Pernyataan Pemotongan Tukin</h1>
        <p class="text-xs text-amber-300 font-medium">
          Total: <span class="font-bold">{{ count($pages) }} Lembar Surat</span> (Tahun {{ $year }})
        </p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.tab', ['tab' => 'programs']) }}"
         class="px-3.5 py-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition inline-flex items-center gap-1.5 cursor-pointer">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
        <span>Kembali</span>
      </a>
      <button type="button" onclick="window.print()"
              class="px-4 py-1.5 rounded-lg bg-[#ffb900] hover:bg-amber-400 text-[#06172e] font-extrabold text-xs shadow-md transition inline-flex items-center gap-1.5 cursor-pointer">
        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
        <span>Cetak / Simpan PDF</span>
      </button>
    </div>
  </header>

  <!-- Spacer untuk fixed toolbar saat di layar browser -->
  <div class="no-print h-14"></div>

  <!-- Loop Per Lembar Pegawai (A4 = 794px x 1123px) -->
  @foreach($pages as $index => $page)
    <div class="page-container mx-auto flex h-[1123px] w-[794px] flex-col overflow-hidden bg-white text-[#1d1d1b] shadow-lg print:shadow-none mb-8 print:mb-0">

      <!-- Header Surat -->
      <header class="flex-none border-b-[6px] border-[#ffb900] bg-[#06172e] px-12 pb-5 pt-7 text-white">
        <div class="text-sm font-semibold uppercase tracking-[0.12em] text-[#ffb900]">
          {{ $settings->name ?? 'Masjid Salahuddin' }} KPP Madya Malang
        </div>
        <h1 class="mt-2 text-2xl font-extrabold leading-tight">
          Surat Pernyataan Kesediaan Pemotongan Tunjangan Kinerja
        </h1>
      </header>

      <!-- Main Body Konten Surat -->
      <main class="flex flex-1 flex-col gap-6 px-12 pb-6 pt-6">

        <!-- 1. Data Pegawai -->
        <section class="grid grid-cols-[150px_1fr] items-end gap-x-4 gap-y-2.5 text-sm">
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
          <p class="mb-3.5 text-justify text-sm leading-relaxed">
            Saya bersedia dipotong Tunjangan Kinerja setiap bulan oleh Bendahara KPP Madya Malang,
            untuk disalurkan kepada Bendahara Masjid Salahuddin, dengan rincian berikut.
            Pemotongan berlaku mulai bulan <span class="font-semibold">{{ $effectiveMonth ?: '________' }}</span> tahun <span class="font-semibold">{{ $effectiveYear ?: '20____' }}</span>.
          </p>

          <table class="w-full border-collapse text-sm leading-[1.4]
                        [&_th]:border [&_th]:border-[#c3c9d4] [&_th]:bg-[#06172e] [&_th]:px-3 [&_th]:py-[5px] [&_th]:text-left [&_th]:font-bold [&_th]:text-white
                        [&_td]:border [&_td]:border-[#c3c9d4] [&_td]:px-3 [&_td]:py-[5px]">
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
                  <td class="font-bold text-slate-900">
                    @if($p['amount'] > 0)
                      Rp {{ number_format($p['amount'], 0, ',', '.') }}
                    @else
                      Rp
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </section>

        <!-- 3. Tanda Tangan Yang Menyatakan -->
        <section class="flex justify-end text-center text-sm leading-[1.55]">
          <div>
            Malang, {{ $dateLetter ?: '____________ 20____' }}<br>
            Yang menyatakan,
            <div class="h-12"></div>
            ( <span class="font-bold underline underline-offset-2">{{ $page['name'] ?: '______________________' }}</span> )
          </div>
        </section>

        <!-- 4. Riwayat Potongan (Kondisional: Disembunyikan jika mode Pegawai Baru / Tanpa Riwayat) -->
        @if($page['show_history'])
          <section>
            <div class="flex items-center justify-between mb-2">
              <h2 class="text-sm font-bold uppercase tracking-[0.1em] text-[#06172e]">
                Riwayat Potongan Tahun {{ $year }}
              </h2>
              <span class="text-[11px] text-slate-500 italic">*Nominal dalam ribuan rupiah</span>
            </div>

            <table class="w-full border-collapse text-[13px] leading-[1.4]
                          [&_th]:border [&_th]:border-[#c3c9d4] [&_th]:bg-[#06172e] [&_th]:px-0.5 [&_th]:py-[5px] [&_th]:text-center [&_th]:font-bold [&_th]:text-white
                          [&_td]:h-7 [&_td]:border [&_td]:border-[#c3c9d4] [&_td]:px-0.5 [&_td]:py-[5px] [&_td]:text-center">
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
        @else
          <!-- Spacer pengganti saat Riwayat Potongan disembunyikan agar tata letak tetap proporsional -->
          <div class="flex-1 border-t border-dashed border-slate-200 pt-4 flex flex-col justify-center text-center">
            <p class="text-xs text-slate-400 italic">
              Formulir Pernyataan Pemotongan Tunjangan Kinerja Baru — Belum Tercatat Riwayat Pemotongan Sebelumnya
            </p>
          </div>
        @endif

        <!-- 5. Footer Surat -->
        <footer class="mt-auto flex flex-col gap-1.5 border-t border-[#c3c9d4] pt-3 text-[13px] leading-normal">
          <p>
            Mohon dikumpulkan kepada Sdr. <b>{{ $secretaryName }}</b>, Sekretaris Masjid Salahuddin
            ({{ $secretarySection }}).
          </p>
          <p class="italic text-[#06172e]">
            "...dengan zakat itu kamu membersihkan dan menyucikan mereka..." (QS. At-Taubah: 103)<br>
            "Siapakah yang memberi pinjaman kepada Allah, pinjaman yang baik..." (QS. Al-Baqarah: 245)
          </p>
        </footer>

      </main>
    </div>
  @endforeach

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) {
        window.lucide.createIcons();
      }
    });
  </script>
</body>
</html>
