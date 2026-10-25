<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan Kesediaan Pemotongan Tunjangan Kinerja</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #1d1d1b;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .page-container {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .page-break {
            page-break-after: always;
        }

        /* Kop Dokumen Surat Pernyataan */
        .header-box {
            background-color: #06172e;
            color: #ffffff;
            padding: 10px 14px 10px 14px;
            border-bottom: 3.5px solid #ffb900;
            margin-bottom: 12px;
        }
        .header-institution {
            font-size: 8pt;
            font-weight: bold;
            color: #ffb900;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }
        .header-title {
            font-size: 13pt;
            font-weight: 800;
            line-height: 1.2;
            color: #ffffff;
            margin: 0;
        }

        /* 1. Data Identitas Pegawai */
        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .identity-table td {
            padding: 3px 0;
            font-size: 9pt;
            vertical-align: middle;
        }
        .identity-label {
            width: 130px;
            font-weight: bold;
            color: #06172e;
        }
        .identity-colon {
            width: 12px;
            text-align: center;
            font-weight: bold;
            color: #06172e;
        }
        .identity-val {
            border-bottom: 0.8px solid #94a3b8;
            color: #0f172a;
            padding-left: 4px;
            font-weight: 600;
        }

        /* 2. Rincian Pemotongan */
        .statement-text {
            font-size: 9pt;
            text-align: justify;
            line-height: 1.4;
            margin: 0 0 8px 0;
            color: #1e293b;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #06172e;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: bold;
            text-align: left;
            padding: 5px 8px;
            border: 0.8px solid #94a3b8;
        }
        .data-table th.center {
            text-align: center;
        }
        .data-table td {
            font-size: 8.5pt;
            padding: 5px 8px;
            border: 0.8px solid #cbd5e1;
            color: #0f172a;
        }
        .data-table td.center {
            text-align: center;
        }

        /* 3. Tanda Tangan */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .signature-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .sig-box {
            text-align: center;
            width: 250px;
        }
        .sig-date {
            font-size: 8.5pt;
            color: #1e293b;
            margin-bottom: 3px;
        }
        .sig-role {
            font-size: 8.5pt;
            color: #06172e;
            margin-bottom: 4px;
        }
        .sig-name {
            font-size: 9pt;
            font-weight: bold;
            text-decoration: underline;
            color: #06172e;
            margin-top: 4px;
        }

        .sig-qr-box {
            display: inline-block;
            margin: 2px auto 3px auto;
            padding: 2.5px;
            background-color: #ffffff;
            border: 0.5px solid #cbd5e1;
            border-radius: 4px;
            text-align: center;
        }
        .sig-qr-img {
            width: 48px;
            height: 48px;
            display: block;
            margin: 0 auto;
        }
        .sig-qr-label {
            font-size: 6px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            margin-top: 1.5px;
            letter-spacing: 0.2px;
        }

        /* 4. Riwayat Potongan Matrix */
        .history-title-box {
            margin-bottom: 4px;
        }
        .history-title {
            font-size: 8.5pt;
            font-weight: bold;
            color: #06172e;
            float: left;
        }
        .history-note {
            font-size: 7.5pt;
            font-style: italic;
            color: #64748b;
            float: right;
        }
        .clearfix {
            clear: both;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .history-table th {
            background-color: #06172e;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
            padding: 3.5px 2px;
            border: 0.8px solid #94a3b8;
        }
        .history-table th.left {
            text-align: left;
            padding-left: 6px;
            width: 130px;
        }
        .history-table td {
            font-size: 7.5pt;
            text-align: center;
            padding: 3px 2px;
            border: 0.8px solid #cbd5e1;
            color: #0f172a;
        }
        .history-table td.left {
            text-align: left;
            padding-left: 6px;
            font-weight: 600;
        }
        .history-table td.has-val {
            font-weight: bold;
            background-color: #fefce8;
            color: #06172e;
        }
        .history-table td.empty-val {
            color: #94a3b8;
        }

        /* 5. Footer Surat */
        .footer-box {
            border-top: 0.8px solid #94a3b8;
            padding-top: 6px;
            margin-top: 8px;
            font-size: 8pt;
            color: #475569;
            line-height: 1.35;
        }
        .footer-box b {
            color: #06172e;
        }
    </style>
</head>
<body>

@foreach($pages as $index => $page)
    <div class="page-container {{ !$loop->last ? 'page-break' : '' }}">

        <!-- Header Surat -->
        <div class="header-box">
            <div class="header-institution">{{ $settings->name ?? 'Masjid Salahuddin' }} KPP Madya Malang</div>
            <h1 class="header-title">Surat Pernyataan Kesediaan Pemotongan Tunjangan Kinerja</h1>
        </div>

        <!-- 1. Data Pegawai -->
        <table class="identity-table">
            <tr>
                <td class="identity-label">Nama</td>
                <td class="identity-colon">:</td>
                <td class="identity-val">{{ $page['name'] ?: '-' }}</td>
            </tr>
            <tr>
                <td class="identity-label">NIP</td>
                <td class="identity-colon">:</td>
                <td class="identity-val">{{ $page['nip'] ?: '-' }}</td>
            </tr>
            <tr>
                <td class="identity-label">Jabatan</td>
                <td class="identity-colon">:</td>
                <td class="identity-val">{{ $page['jabatan'] ?: '-' }}</td>
            </tr>
            <tr>
                <td class="identity-label">Seksi / Kelompok</td>
                <td class="identity-colon">:</td>
                <td class="identity-val">{{ $page['seksi'] ?: '-' }}</td>
            </tr>
            <tr>
                <td class="identity-label">Nomor WhatsApp</td>
                <td class="identity-colon">:</td>
                <td class="identity-val">{{ $page['phone'] ?: '-' }}</td>
            </tr>
        </table>

        <!-- 2. Pernyataan & Rincian Program Pemotongan -->
        <p class="statement-text">
            Saya bersedia dipotong Tunjangan Kinerja setiap bulan oleh Bendahara KPP Madya Malang,
            untuk disalurkan kepada Bendahara Masjid Salahuddin, dengan rincian berikut.
            Pemotongan berlaku mulai bulan <strong>{{ $effectiveMonth ?: '________' }}</strong> tahun <strong>{{ $effectiveYear ?: '20____' }}</strong>.
        </p>

        <table class="data-table">
            <thead>
                <tr>
                    <th class="center" style="width: 32px;">No</th>
                    <th>Program Dakwah / Sosial</th>
                    <th style="width: 200px;">Nominal per Bulan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($page['programs'] as $pKey => $p)
                    <tr>
                        <td class="center font-bold">{{ $p['order'] }}</td>
                        <td style="font-weight: bold; color: #06172e;">{{ $p['label'] }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- 3. Tanda Tangan & QR Code Verifikasi Blangko Resmi -->
        <table class="signature-table">
            <tr>
                <td></td>
                <td class="sig-box">
                    <div class="sig-date">
                        Malang, {{ !empty($dateLetter) ? $dateLetter : '_________________ 20___' }}
                    </div>
                    <div class="sig-role">Yang menyatakan,</div>
                    @if(!empty($page['qr_code']))
                        <div class="sig-qr-box">
                            <img src="{{ $page['qr_code'] }}" class="sig-qr-img" alt="QR Code Verifikasi Resmi">
                            <div class="sig-qr-label">BLANGKO RESMI KEMENKEU</div>
                        </div>
                    @else
                        <div style="height: 48px;"></div>
                    @endif
                    <div class="sig-name">
                        ( {{ $page['name'] ?: '____________________________' }} )
                    </div>
                </td>
            </tr>
        </table>

        <!-- 4. Riwayat Potongan (Kondisional) -->
        @if($page['show_history'])
            <div class="history-title-box">
                <span class="history-title">Riwayat Potongan Tahun {{ $year }}</span>
                <span class="history-note">*Nominal dalam ribuan rupiah</span>
                <div class="clearfix"></div>
            </div>

            <table class="history-table">
                <thead>
                    <tr>
                        <th class="left">Program</th>
                        <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th>
                        <th>7</th><th>8</th><th>9</th><th>10</th><th>11</th><th>12</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($page['history'] as $pKey => $h)
                        <tr>
                            <td class="left">{{ $h['label'] }}</td>
                            @for($m = 1; $m <= 12; $m++)
                                @php
                                    $mVal = $h['months'][$m];
                                    $hasVal = ($mVal !== '-');
                                @endphp
                                <td class="{{ $hasVal ? 'has-val' : 'empty-val' }}">
                                    {{ $mVal }}
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- 5. Footer Surat -->
        <div class="footer-box">
            Mohon dikumpulkan kepada Sdr. <b>{{ $secretaryName }}</b>, Sekretaris Masjid Salahuddin ({{ $secretarySection }}).
        </div>

    </div>
@endforeach

</body>
</html>
