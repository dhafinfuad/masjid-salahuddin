<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal {{ $type === 'jumat' ? 'Shalat Jumat' : 'Kajian Pekanan' }} {{ $monthName }} {{ $year }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 10px;
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

        /* Tabel Jadwal */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #06172e;
            color: #ffffff;
            font-size: 9px;
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
        .data-table td {
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .data-table tr {
            page-break-inside: avoid;
        }
        .col-num {
            width: 24px;
            text-align: center;
            color: #64748b;
        }
        .col-date {
            width: 95px;
            white-space: nowrap;
        }
        .col-time {
            width: 65px;
            text-align: center;
            white-space: nowrap;
        }
        .col-title {
            font-weight: 600;
            color: #0f172a;
        }
        .col-person {
            font-weight: normal;
            color: #0f172a;
        }
        .badge-holiday {
            display: inline-block;
            background-color: #ffe4e6;
            color: #9f1239;
            font-size: 7.5px;
            font-weight: 600;
            padding: 1px 4px;
            border-radius: 2px;
            border: 0.5px solid #fecdd3;
            margin-top: 2px;
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
            font-size: 9px;
            color: #475569;
            margin-bottom: 2px;
        }
        .sig-role {
            font-size: 9.5px;
            font-weight: bold;
            color: #06172e;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .sig-name {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
            margin-bottom: 1px;
        }
        .sig-meta {
            font-size: 8px;
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

    <!-- Kop Dokumen -->
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
        <div class="doc-title">
            Jadwal {{ $type === 'jumat' ? 'Petugas Shalat Jumat' : 'Kajian Pekanan & Tematik' }}
        </div>
        <div class="doc-subtitle">
            Periode: Bulan {{ $monthName }} {{ $year }}
        </div>
    </div>

    <!-- Tabel Jadwal -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="col-num">No</th>
                <th class="col-date">Hari &amp; Tanggal</th>
                <th class="col-time center">Waktu</th>
                <th>{{ $type === 'jumat' ? 'Judul / Tema Khutbah' : 'Judul / Tema Kajian' }}</th>
                @if($type === 'jumat')
                    <th style="width: 120px;">Khatib &amp; Imam</th>
                    <th style="width: 95px;">MC</th>
                    <th style="width: 95px;">Muadzin</th>
                @else
                    <th style="width: 130px;">Pembicara</th>
                    <th style="width: 95px;">Kontak / HP</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($kajians as $idx => $k)
                <tr>
                    <td class="col-num">{{ $idx + 1 }}</td>
                    <td class="col-date">
                        <strong>{{ \Carbon\Carbon::parse($k->date)->translatedFormat('l') }}</strong><br>
                        <span style="color: #64748b; font-size: 8px;">{{ \Carbon\Carbon::parse($k->date)->translatedFormat('d/m/Y') }}</span>
                    </td>
                    <td class="col-time center">
                        {{ $k->time_display ?: ($type === 'jumat' ? '11:45 WIB' : '15:30 WIB') }}
                    </td>
                    <td>
                        <span class="col-title">{{ $k->title }}</span>
                        @if($k->is_holiday_disabled)
                            <br><span class="badge-holiday">Hari Libur (Petugas Nonaktif)</span>
                        @endif
                    </td>
                    @if($type === 'jumat')
                        <td class="col-person">{{ $k->khatib_name ?: '-' }}</td>
                        <td class="col-person">{{ $k->mc_name ?: '-' }}</td>
                        <td class="col-person">{{ $k->muadzin_name ?: '-' }}</td>
                    @else
                        <td class="col-person">{{ $k->speaker_name ?: '-' }}</td>
                        <td class="col-person" style="font-family: monospace;">{{ $k->speaker_phone ?: '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $type === 'jumat' ? 7 : 6 }}" style="text-align: center; padding: 15px; color: #94a3b8; font-style: italic;">
                        Belum ada jadwal kegiatan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="table-tfoot">
                <td colspan="{{ $type === 'jumat' ? 4 : 3 }}" style="text-align: right; text-transform: uppercase;">
                    Total Jadwal Terdaftar
                </td>
                <td colspan="{{ $type === 'jumat' ? 3 : 3 }}" style="text-align: center;">
                    {{ count($kajians) }} Kegiatan Terjadwal
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Bagian Pengesahan / TTE Resmi -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="sig-box" style="text-align: left; width: 45%;">
                    <div class="sig-city-date">&nbsp;</div>
                    <div class="sig-role">Koordinator Bidang Dakwah</div>
                    <div class="sig-qr-box">
                        @if(!empty($qrCodeBase64))
                            <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Verifikasi" />
                        @endif
                        <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                    </div>
                    <div class="sig-name">{{ $koordinatorDakwah->name ?? 'Deril Amrizal Kholid' }}</div>
                    <div class="sig-meta">Bidang Dakwah &amp; Kajian DKM</div>
                </td>
                <td style="width: 10%;"></td>
                <td class="sig-box" style="text-align: right; width: 45%;">
                    <div class="sig-city-date">Malang, {{ $printDate ?? date('d F Y') }}</div>
                    <div class="sig-role">Ketua DKM</div>
                    <div class="sig-qr-box">
                        @if(!empty($qrCodeBase64))
                            <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Verifikasi" />
                        @endif
                        <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                    </div>
                    <div class="sig-name">{{ $ketuaDkm->name ?? 'Moh. Nazil Fuadi Kusmawan' }}</div>
                    <div class="sig-meta">DKM {{ $settings->name ?? 'Masjid Salahuddin' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
