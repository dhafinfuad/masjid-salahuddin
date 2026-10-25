<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Roster Petugas Ibadah {{ $settings->name ?? 'Masjid Salahuddin' }}</title>
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

        /* Tabel Roster */
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
            width: 26px;
            text-align: center;
            font-weight: bold;
            color: #475569;
        }
        .col-date {
            width: 110px;
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
            font-weight: normal;
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
        <div class="doc-title">Roster Penugasan Imam &amp; Muadzin</div>
        <div class="doc-subtitle">
            Periode: 
            @if($week !== 'all' && is_numeric($week))
                Pekan Ke-{{ $week }}
            @endif
            @if(isset($monthName))
                Bulan {{ $monthName }}
            @endif
            {{ $selectedYearNum ?? date('Y') }}
        </div>
    </div>

    @php
        $groupedDuties = [];
        foreach ($duties as $duty) {
            $dateKey = $duty->resolved_date ? $duty->resolved_date->format('Y-m-d') : ($duty->day_name . '-' . ($duty->resolved_week ?: $duty->week_pattern));
            if (!isset($groupedDuties[$dateKey])) {
                $isFriday = ($duty->day_name === 'Jumat');
                $weekNum = $duty->resolved_week ?: ($duty->resolved_date ? \App\Models\PrayerDuty::getWeekOfMonth($duty->resolved_date) : 1);
                $dayNum = $duty->resolved_date ? $duty->resolved_date->format('j') : '-';
                $formattedDate = $duty->resolved_date ? $duty->resolved_date->translatedFormat('d F Y') : '-';

                $groupedDuties[$dateKey] = [
                    'date_key' => $duty->resolved_date ? $duty->resolved_date->format('Y-m-d') : null,
                    'day_name' => $duty->day_name,
                    'day_number' => $dayNum,
                    'formatted_date' => $formattedDate,
                    'is_friday' => $isFriday,
                    'week_number' => $weekNum,
                    'week_label' => $duty->week_label,
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
                <tr class="{{ $row['is_friday'] ? 'row-friday' : '' }}">
                    <td class="col-tgl">{{ $row['day_number'] }}</td>
                    <td class="col-date">
                        <strong>{{ $row['day_name'] }}</strong>
                        @if($row['formatted_date'] !== '-')
                            <br><span style="color: #64748b; font-size: 8px;">{{ $row['formatted_date'] }}</span>
                        @endif
                    </td>
                    <td class="col-pola center">
                        @if($row['is_friday'])
                            <span class="badge-jumat">Jumat (P.{{ $row['week_number'] }})</span>
                        @else
                            <span class="badge-pekan">{{ $row['week_label'] }}</span>
                        @endif
                    </td>
                    <td class="col-dzuhur">
                        @if($row['is_friday'])
                            <div class="duty-item">
                                <span class="duty-label">Khatib:</span>
                                <span class="duty-name">{{ $khatib }}</span>
                            </div>
                            <div class="duty-item">
                                <span class="duty-label">Muadzin:</span>
                                <span class="duty-name">{{ $muadzin }}</span>
                            </div>
                            @if(!empty($mc) && $mc !== '-')
                                <div class="duty-item">
                                    <span class="duty-label">MC:</span>
                                    <span class="duty-name">{{ $mc }}</span>
                                </div>
                            @endif
                        @else
                            <div class="duty-item">
                                <span class="duty-label">Imam:</span>
                                <span class="duty-name">{{ $dzImam }}</span>
                            </div>
                            <div class="duty-item">
                                <span class="duty-label">Muadzin:</span>
                                <span class="duty-name">{{ $dzMuadzin }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="col-ashar">
                        <div class="duty-item">
                            <span class="duty-label">Imam:</span>
                            <span class="duty-name">{{ $asharImam }}</span>
                        </div>
                        <div class="duty-item">
                            <span class="duty-label">Muadzin:</span>
                            <span class="duty-name">{{ $asharMuadzin }}</span>
                        </div>
                        @if(!empty($asharKajianTitle))
                            <div class="duty-item">
                                <span class="duty-label">Kajian:</span>
                                <span class="duty-name" style="color: #0369a1;">{{ $asharKajianTitle }}</span>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 15px; color: #94a3b8; font-style: italic;">
                        Tidak ada data jadwal penugasan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bagian Pengesahan / TTE Resmi -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="sig-box" style="text-align: left; width: 45%;">
                    <div class="sig-city-date">&nbsp;</div>
                    <div class="sig-role">Sekretaris DKM</div>
                    <div class="sig-qr-box">
                        @if(!empty($qrCodeBase64))
                            <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Verifikasi" />
                        @endif
                        <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                    </div>
                    <div class="sig-name">{{ $sekretarisDkm->name ?? 'Deril Amrizal Kholid' }}</div>
                    <div class="sig-meta">DKM {{ $settings->name ?? 'Masjid Salahuddin' }}</div>
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
