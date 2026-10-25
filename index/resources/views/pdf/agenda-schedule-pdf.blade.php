<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Perencanaan & Realisasi Agenda Kegiatan — {{ $settings->name ?? 'Masjid Salahuddin' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 9.5px;
            line-height: 1.35;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        
        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .kop-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .kop-text {
            text-align: center;
        }
        .kop-title {
            font-size: 14px;
            font-weight: bold;
            color: #06172e;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-subtitle {
            font-size: 10px;
            color: #475569;
            margin: 2px 0 0 0;
            font-weight: 600;
        }
        .kop-divider-thick {
            border-bottom: 2px solid #06172e;
            margin-top: 6px;
            margin-bottom: 1.5px;
        }
        .kop-divider-thin {
            border-bottom: 0.75px solid #06172e;
            margin-bottom: 10px;
        }

        /* Judul Dokumen */
        .doc-title-box {
            text-align: center;
            margin-bottom: 12px;
        }
        .doc-title {
            font-size: 12px;
            font-weight: bold;
            color: #06172e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            padding-bottom: 2px;
            display: inline-block;
            border-bottom: 1.5px solid #06172e;
        }
        .doc-subtitle {
            font-size: 9.5px;
            color: #475569;
            margin: 3px 0 0 0;
            font-weight: 500;
        }

        /* Tabel Agenda */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #06172e;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5px 6px;
            border: 1px solid #06172e;
            text-align: left;
        }
        .data-table th.center {
            text-align: center;
        }
        .data-table th.right {
            text-align: right;
        }
        .data-table td {
            padding: 5px 6px;
            border: 0.5px solid #cbd5e1;
            font-size: 8.5px;
            vertical-align: top;
        }
        .data-table tr.even {
            background-color: #f8fafc;
        }
        .data-table tr.odd {
            background-color: #ffffff;
        }

        .col-no {
            width: 24px;
            text-align: center;
            color: #64748b;
        }
        .col-title {
            font-weight: bold;
            color: #0f172a;
        }
        .col-desc {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.25;
        }
        .col-date {
            width: 75px;
            white-space: nowrap;
        }
        .col-budget {
            width: 80px;
            text-align: right;
            font-weight: 600;
            white-space: nowrap;
        }
        .col-committee {
            width: 130px;
            color: #334155;
            font-size: 8px;
        }
        .col-status {
            width: 65px;
            text-align: center;
        }

        /* Status Badges */
        .badge-status {
            display: inline-block;
            font-size: 7.5px;
            font-weight: 600;
            padding: 1.5px 5px;
            border-radius: 9999px;
            text-align: center;
        }
        .badge-direncanakan {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 0.5px solid #bfdbfe;
        }
        .badge-berjalan {
            background-color: #fffbeb;
            color: #b45309;
            border: 0.5px solid #fde68a;
        }
        .badge-selesai {
            background-color: #f0fdf4;
            color: #15803d;
            border: 0.5px solid #bbf7d0;
        }

        /* Footer Tabel */
        .table-tfoot td {
            background-color: #06172e;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            padding: 5px 6px;
            border: 1px solid #06172e;
        }

        /* Tanda Tangan & Pengesahan */
        .signature-section {
            width: 100%;
            margin-top: 8px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
        .sig-box {
            text-align: center;
            width: 45%;
        }
        .sig-city-date {
            font-size: 8.5px;
            color: #475569;
            margin-bottom: 2px;
        }
        .sig-role {
            font-size: 9px;
            font-weight: bold;
            color: #06172e;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .sig-name {
            font-size: 9px;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
            margin-bottom: 1px;
        }
        .sig-meta {
            font-size: 7.5px;
            color: #64748b;
        }
        .sig-qr-box {
            display: inline-block;
            margin: 2px 0 4px 0;
            padding: 2.5px;
            background-color: #ffffff;
            border: 0.5px solid #cbd5e1;
            border-radius: 4px;
            text-align: center;
        }
        .sig-qr-img {
            width: 52px;
            height: 52px;
            display: block;
            margin: 0 auto;
        }
        .sig-qr-label {
            font-size: 6.5px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            margin-top: 2px;
            letter-spacing: 0.2px;
        }
    </style>
</head>
<body>

    <!-- Kop Dokumen Resmi -->
    <table class="kop-table">
        <tr>
            <td class="kop-text">
                <div class="kop-title">{{ $settings->name ?? 'Masjid Salahuddin' }}</div>
                <div class="kop-subtitle">KPP Madya Malang • Kementerian Keuangan RI</div>
            </td>
        </tr>
    </table>
    <div class="kop-divider-thick"></div>
    <div class="kop-divider-thin"></div>

    <!-- Judul Dokumen -->
    <div class="doc-title-box">
        <div class="doc-title">DAFTAR PERENCANAAN &amp; REALISASI AGENDA KEGIATAN</div>
        <div class="doc-subtitle">{{ $settings->name ?? 'Masjid Salahuddin' }}</div>
    </div>

    <!-- Tabel Agenda Kegiatan -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="center col-no">NO</th>
                <th>NAMA AGENDA KEGIATAN</th>
                <th class="col-date">TANGGAL</th>
                <th class="right col-budget">ANGGARAN (RP)</th>
                <th class="col-committee">SUSUNAN PANITIA</th>
                <th class="center col-status">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agendas as $index => $agenda)
                <tr class="{{ $index % 2 === 0 ? 'odd' : 'even' }}">
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td>
                        <div class="col-title">{{ $agenda->title }}</div>
                        @if($agenda->description)
                            <div class="col-desc">{{ $agenda->description }}</div>
                        @endif
                    </td>
                    <td class="col-date">
                        <span style="font-weight: 600;">{{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('l') }}</span><br>
                        <span style="color: #64748b; font-size: 8px;">{{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d/m/Y') }}</span>
                    </td>
                    <td class="col-budget">
                        {{ number_format($agenda->budget ?: 0, 0, ',', '.') }}
                    </td>
                    <td class="col-committee">
                        @if($agenda->committee_list)
                            <ul style="margin: 0; padding-left: 10px;">
                                @foreach(array_slice($agenda->committee_list, 0, 3) as $member)
                                    <li>{{ $member }}</li>
                                @endforeach
                                @if(count($agenda->committee_list) > 3)
                                    <li style="color: #94a3b8; font-style: italic;">+{{ count($agenda->committee_list) - 3 }} lainnya</li>
                                @endif
                            </ul>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td class="col-status">
                        @if($agenda->status === 'Direncanakan')
                            <span class="badge-status badge-direncanakan">Direncanakan</span>
                        @elseif($agenda->status === 'Berjalan')
                            <span class="badge-status badge-berjalan">Berjalan</span>
                        @else
                            <span class="badge-status badge-selesai">Selesai</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 12px; color: #64748b; font-style: italic;">
                        Belum ada agenda kegiatan yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="table-tfoot">
                <td colspan="3" style="text-align: right; text-transform: uppercase; letter-spacing: 0.3px;">
                    TOTAL ANGGARAN
                </td>
                <td style="text-align: right; font-weight: bold; white-space: nowrap;">
                    Rp {{ number_format($totalBudget ?? 0, 0, ',', '.') }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan & QR Code Verifikasi -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <!-- Sisi Kiri: Ketua Acara / Panitia -->
                <td class="sig-box">
                    <div class="sig-city-date">&nbsp;</div>
                    <div class="sig-role">Disusun oleh,<br>Ketua Acara / Panitia</div>
                    <div class="sig-qr-box">
                        <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Code Verifikasi TTE">
                        <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                    </div>
                    <div class="sig-name">{{ $ketuaAcara ?? 'Ichtiar Rachmatullah' }}</div>
                    <div class="sig-meta">Ketua Panitia Pelaksana</div>
                </td>

                <td style="width: 10%;"></td>

                <!-- Sisi Kanan: Ketua DKM -->
                <td class="sig-box">
                    <div class="sig-city-date">Malang, {{ $printDate }}</div>
                    <div class="sig-role">Mengetahui &amp; Menyetujui,<br>Ketua DKM Masjid Salahuddin</div>
                    <div class="sig-qr-box">
                        <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Code Verifikasi TTE">
                        <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                    </div>
                    <div class="sig-name">{{ $ketuaDkm?->name ?? 'Moh. Nazil Fuadi Kusmawan' }}</div>
                    <div class="sig-meta">Ketua DKM Masjid Salahuddin</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
