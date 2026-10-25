<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Petugas Shalat {{ $monthName }} {{ $year }}</title>
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
            margin-bottom: 10px;
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

        /* Tabel Penugasan */
        .roster-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .roster-table th {
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
        .roster-table th.center {
            text-align: center;
        }
        .roster-table td {
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
            font-size: 9px;
            vertical-align: top;
        }
        .roster-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .roster-table tr.row-friday {
            background-color: #ecfdf5;
        }
        .roster-table tr {
            page-break-inside: avoid;
        }
        .col-tgl {
            width: 28px;
            text-align: center;
            font-weight: bold;
            color: #475569;
        }
        .col-date {
            width: 115px;
            white-space: nowrap;
        }
        .col-pola {
            width: 75px;
            text-align: center;
        }
        .col-dzuhur {
            width: 170px;
        }
        .col-ashar {
            width: 145px;
        }

        .duty-item {
            margin-bottom: 1.5px;
        }
        .duty-label {
            color: #64748b;
            font-size: 8.5px;
            display: inline-block;
            width: 44px;
        }
        .duty-name {
            font-weight: 600;
            color: #0f172a;
        }
        .badge-jumat {
            display: inline-block;
            background-color: #d1fae5;
            color: #065f46;
            font-size: 8px;
            font-weight: bold;
            padding: 1.5px 5px;
            border-radius: 3px;
            border: 0.5px solid #a7f3d0;
        }
        .badge-pekan {
            display: inline-block;
            background-color: #e0f2fe;
            color: #0369a1;
            font-size: 8px;
            font-weight: 600;
            padding: 1.5px 5px;
            border-radius: 3px;
            border: 0.5px solid #bae6fd;
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
            margin-bottom: 3px;
        }
        .sig-role {
            font-size: 9.5px;
            font-weight: bold;
            color: #06172e;
            text-transform: uppercase;
            margin-bottom: 35px;
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
        .tte-badge {
            display: inline-block;
            margin: 4px auto;
            padding: 3px 6px;
            border: 0.5px dashed #059669;
            background-color: #f0fdf4;
            color: #065f46;
            font-size: 7.5px;
            border-radius: 3px;
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
        <div class="doc-title">Jadwal Penugasan Imam, Khatib &amp; Muadzin</div>
        <div class="doc-subtitle">Periode: Bulan {{ $monthName }} {{ $year }}</div>
    </div>

    <!-- Tabel Roster Penugasan -->
    <table class="roster-table">
        <thead>
            <tr>
                <th class="col-tgl center">Tgl</th>
                <th class="col-date">Hari &amp; Tanggal</th>
                <th class="col-pola center">Pola</th>
                <th class="col-dzuhur">Shalat Dzuhur</th>
                <th class="col-ashar">Shalat Ashar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roster as $row)
                <tr class="{{ $row['is_friday'] ? 'row-friday' : '' }}">
                    <td class="col-tgl">{{ $row['day_number'] }}</td>
                    <td class="col-date">
                        <strong>{{ $row['day_name'] }}</strong>, {{ $row['formatted_date'] }}
                    </td>
                    <td class="col-pola center">
                        @if($row['is_friday'])
                            <span class="badge-jumat">Jumat (P.{{ $row['week_number'] }})</span>
                        @else
                            <span class="badge-pekan">Pekan {{ $row['week_number'] }}</span>
                        @endif
                    </td>
                    <td class="col-dzuhur">
                        @if($row['is_friday'])
                            <div class="duty-item">
                                <span class="duty-label">Khatib:</span>
                                <span class="duty-name">{{ $row['dzuhur_imam'] }}</span>
                            </div>
                            <div class="duty-item">
                                <span class="duty-label">Muadzin:</span>
                                <span class="duty-name">{{ $row['dzuhur_muadzin'] }}</span>
                            </div>
                            @if(!empty($row['friday_mc']) && $row['friday_mc'] !== '-')
                                <div class="duty-item">
                                    <span class="duty-label">MC:</span>
                                    <span class="duty-name">{{ $row['friday_mc'] }}</span>
                                </div>
                            @endif
                        @else
                            <div class="duty-item">
                                <span class="duty-label">Imam:</span>
                                <span class="duty-name">{{ $row['dzuhur_imam'] }}</span>
                            </div>
                            <div class="duty-item">
                                <span class="duty-label">Muadzin:</span>
                                <span class="duty-name">{{ $row['dzuhur_muadzin'] }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="col-ashar">
                        <div class="duty-item">
                            <span class="duty-label">Imam:</span>
                            <span class="duty-name">{{ $row['ashar_imam'] }}</span>
                        </div>
                        <div class="duty-item">
                            <span class="duty-label">Muadzin:</span>
                            <span class="duty-name">{{ $row['ashar_muadzin'] }}</span>
                        </div>
                        @if(!empty($row['ashar_kajian_title']))
                            <div class="duty-item">
                                <span class="duty-label">Kajian:</span>
                                <span class="duty-name" style="color: #0369a1;">{{ $row['ashar_kajian_title'] }}</span>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 15px; color: #94a3b8; font-style: italic;">
                        Tidak ada data jadwal penugasan untuk bulan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bagian Pengesahan / TTE Resmi -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="sig-box" style="text-align: left; width: 50%;">
                    <div class="sig-city-date">&nbsp;</div>
                    <div class="sig-role" style="margin-bottom: 25px;">Sekretaris DKM</div>
                    <div class="tte-badge">
                        &#10003; Ditandatangani Elektronik (TTE BSrE)<br>
                        Ref Hash: {{ $tteHash ?? 'TTE-SALAHUDDIN' }}
                    </div>
                    <div class="sig-name">{{ $sekretarisDkm->name ?? 'Alan Irfansyah' }}</div>
                    <div class="sig-meta">DKM {{ $settings->name ?? 'Masjid Salahuddin' }}</div>
                </td>
                <td style="width: 10%;"></td>
                <td class="sig-box" style="text-align: right; width: 40%;">
                    <div class="sig-city-date">Malang, {{ $printDate ?? date('d F Y') }}</div>
                    <div class="sig-role" style="margin-bottom: 25px;">Ketua DKM</div>
                    <div class="tte-badge">
                        &#10003; Ditandatangani Elektronik (TTE BSrE)<br>
                        Ref Hash: {{ $tteHash ?? 'TTE-SALAHUDDIN' }}
                    </div>
                    <div class="sig-name">{{ $ketuaDkm->name ?? 'M. Yasak' }}</div>
                    <div class="sig-meta">DKM {{ $settings->name ?? 'Masjid Salahuddin' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
