<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>
        @if(($reportType ?? 'monthly') === 'agenda')
            LPJ Keuangan — {{ $agenda->title ?? 'Agenda' }} — {{ $settings->name ?? 'Masjid Salahuddin' }}
        @else
            Laporan Arus Kas — {{ $periodLabel ?? 'Bulanan' }} — {{ $settings->name ?? 'Masjid Salahuddin' }}
        @endif
    </title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 9px;
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
            font-size: 11.5px;
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
            font-size: 9px;
            color: #475569;
            margin: 3px 0 0 0;
            font-weight: 500;
        }

        /* Metrik Anggaran LPJ Agenda */
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .metrics-table td {
            padding: 4px 6px;
            border: 0.5px solid #cbd5e1;
            background-color: #f8fafc;
            text-align: center;
            width: 25%;
        }
        .metrics-label {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            display: block;
        }
        .metrics-value {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 1px;
        }

        /* Tabel Arus Kas & LPJ */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #06172e;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 4.5px 5px;
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
            padding: 4px 5px;
            border: 0.5px solid #cbd5e1;
            font-size: 8px;
            vertical-align: top;
        }
        .data-table tr.even {
            background-color: #f8fafc;
        }
        .data-table tr.odd {
            background-color: #ffffff;
        }
        .section-header td {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 4px 5px;
            border: 0.5px solid #cbd5e1;
        }
        .subtotal-row td {
            background-color: #f8fafc;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #94a3b8;
        }
        .total-row td {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            border-top: 1.5px solid #06172e;
        }

        /* Footer Tabel */
        .table-tfoot td {
            background-color: #06172e;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5px;
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
        }
        .sig-city-date {
            font-size: 8px;
            color: #475569;
            margin-bottom: 2px;
        }
        .sig-role {
            font-size: 8.5px;
            font-weight: bold;
            color: #06172e;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .sig-name {
            font-size: 8.5px;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
            margin-bottom: 1px;
        }
        .sig-meta {
            font-size: 7px;
            color: #64748b;
        }
        .sig-qr-box {
            display: inline-block;
            margin: 2px 0 3px 0;
            padding: 2px;
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

    @if(($reportType ?? 'monthly') === 'agenda')
        <!-- ======================================================== -->
        <!-- MODE 2: LPJ KEUANGAN AGENDA KEGIATAN TEMATIK             -->
        <!-- ======================================================== -->
        <div class="doc-title-box">
            <div class="doc-title">LAPORAN PERTANGGUNGJAWABAN (LPJ) KEUANGAN KEGIATAN</div>
            <div class="doc-subtitle">{{ strtoupper($agenda->title ?? 'PROGRAM KEGIATAN') }}</div>
            <div style="font-size: 8px; color: #64748b; margin-top: 1px;">Waktu Pelaksanaan: {{ $agenda->formatted_date ?? '-' }}</div>
        </div>

        <!-- Info Box Metrik Anggaran Agenda -->
        <table class="metrics-table">
            <tr>
                <td>
                    <span class="metrics-label">Anggaran Plafon</span>
                    <div class="metrics-value">Rp {{ number_format($agenda->budget ?: 0, 0, ',', '.') }}</div>
                </td>
                <td>
                    <span class="metrics-label">Total Dana Diterima</span>
                    <div class="metrics-value">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</div>
                </td>
                <td>
                    <span class="metrics-label">Realisasi Belanja</span>
                    <div class="metrics-value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                </td>
                <td style="background-color: #f1f5f9; border-color: #94a3b8;">
                    <span class="metrics-label" style="color: #0f172a;">Sisa Anggaran (Silpa)</span>
                    <div class="metrics-value" style="color: #06172e;">Rp {{ number_format($silpa, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>

        <!-- Tabel LPJ Kegiatan -->
        <table class="data-table">
            <thead>
                <tr>
                    <th class="center" style="width: 24px;">NO</th>
                    <th class="center" style="width: 55px;">TANGGAL</th>
                    <th style="width: 110px;">POS / KATEGORI</th>
                    <th>URAIAN / KETERANGAN BELANJA</th>
                    <th class="right" style="width: 80px;">PENERIMAAN (RP)</th>
                    <th class="right" style="width: 80px;">PENGELUARAN (RP)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Bagian A: Penerimaan Dana -->
                <tr class="section-header">
                    <td colspan="6">A. PENERIMAAN DANA &amp; DONASI KEGIATAN</td>
                </tr>
                @php $noA = 1; @endphp
                @forelse($penerimaan as $item)
                    <tr class="{{ $loop->even ? 'even' : 'odd' }}">
                        <td class="center" style="color: #64748b;">{{ $noA++ }}</td>
                        <td class="center">{{ $item->transaction_date ? $item->transaction_date->format('d/m/Y') : '-' }}</td>
                        <td style="font-weight: 600;">{{ $item->category?->name ?? 'Penerimaan Kas' }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="right" style="font-weight: 600;">{{ number_format($item->amount, 0, ',', '.') }}</td>
                        <td class="right" style="color: #94a3b8;">-</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; font-style: italic;">
                            (Alokasi kas masjid langsung atau tidak ada catatan donasi terpisah)
                        </td>
                    </tr>
                @endforelse
                <tr class="subtotal-row">
                    <td colspan="4" class="right" style="text-transform: uppercase;">SUBTOTAL PENERIMAAN DANA (A):</td>
                    <td class="right">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                    <td class="right" style="color: #94a3b8;">-</td>
                </tr>

                <!-- Bagian B: Realisasi Belanja Panitia -->
                <tr class="section-header">
                    <td colspan="6">B. REALISASI PENGELUARAN &amp; BELANJA KEGIATAN</td>
                </tr>
                @php $noB = 1; @endphp
                @forelse($pengeluaran as $item)
                    <tr class="{{ $loop->even ? 'even' : 'odd' }}">
                        <td class="center" style="color: #64748b;">{{ $noB++ }}</td>
                        <td class="center">{{ $item->transaction_date ? $item->transaction_date->format('d/m/Y') : '-' }}</td>
                        <td style="font-weight: 600;">{{ $item->category?->name ?? 'Belanja Kegiatan' }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="right" style="color: #94a3b8;">-</td>
                        <td class="right" style="font-weight: 600;">{{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; font-style: italic;">
                            Tidak ada catatan rincian belanja pengeluaran kegiatan.
                        </td>
                    </tr>
                @endforelse
                <tr class="subtotal-row">
                    <td colspan="4" class="right" style="text-transform: uppercase;">SUBTOTAL PENGELUARAN BELANJA (B):</td>
                    <td class="right" style="color: #94a3b8;">-</td>
                    <td class="right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-tfoot">
                    <td colspan="4" class="right" style="text-transform: uppercase; letter-spacing: 0.3px;">
                        SISA ANGGARAN KEGIATAN (SILPA):
                    </td>
                    <td colspan="2" class="center">
                        Rp {{ number_format($silpa, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Tanda Tangan LPJ Kegiatan (3 Pihak) dengan QR Code Verifikasi Kedinasan -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <!-- Pihak 1: Bendahara Panitia -->
                    <td class="sig-box" style="width: 32%;">
                        <div class="sig-city-date">&nbsp;</div>
                        <div class="sig-role">Disusun oleh,<br>Bendahara Panitia</div>
                        @if(!empty($qrCodeBase64))
                            <div class="sig-qr-box">
                                <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Code Verifikasi TTE">
                                <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                            </div>
                        @else
                            <div style="height: 48px;"></div>
                        @endif
                        <div class="sig-name">Bendahara Kegiatan</div>
                        <div class="sig-meta">Panitia Pelaksana</div>
                    </td>

                    <td style="width: 2%;"></td>

                    <!-- Pihak 2: Ketua Panitia Pelaksana -->
                    <td class="sig-box" style="width: 32%;">
                        <div class="sig-city-date">&nbsp;</div>
                        <div class="sig-role">Diperiksa oleh,<br>Ketua Panitia Pelaksana</div>
                        @if(!empty($qrCodeBase64))
                            <div class="sig-qr-box">
                                <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Code Verifikasi TTE">
                                <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                            </div>
                        @else
                            <div style="height: 48px;"></div>
                        @endif
                        <div class="sig-name">Ketua Panitia</div>
                        <div class="sig-meta">Panitia Pelaksana</div>
                    </td>

                    <td style="width: 2%;"></td>

                    <!-- Pihak 3: Ketua DKM -->
                    <td class="sig-box" style="width: 32%;">
                        <div class="sig-city-date">Malang, {{ $printDate }}</div>
                        <div class="sig-role">Menyetujui,<br>Ketua DKM Masjid</div>
                        @if(!empty($qrCodeBase64))
                            <div class="sig-qr-box">
                                <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Code Verifikasi TTE">
                                <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                            </div>
                        @else
                            <div style="height: 48px;"></div>
                        @endif
                        <div class="sig-name">{{ $ketuaDkm?->name ?? 'Moh. Nazil Fuadi Kusmawan' }}</div>
                        <div class="sig-meta">Ketua DKM Masjid Salahuddin</div>
                    </td>
                </tr>
            </table>
        </div>

    @else
        <!-- ======================================================== -->
        <!-- MODE 1: LAPORAN ARUS KAS BULANAN BAKU DKM KEMENKEU        -->
        <!-- ======================================================== -->
        <div class="doc-title-box">
            <div class="doc-title">LAPORAN ARUS KAS MASJID SALAHUDDIN KPP MADYA MALANG</div>
            <div class="doc-subtitle">Periode: <strong>{{ strtoupper($periodLabel ?? 'BULANAN') }}</strong></div>
        </div>

        <!-- Tabel Arus Kas Baku -->
        <table class="data-table">
            <thead>
                <tr>
                    <th class="center" style="width: 30px;">NO</th>
                    <th>POS ANGGARAN</th>
                    <th class="right" style="width: 100px;">PENERIMAAN (RP)</th>
                    <th class="right" style="width: 100px;">PENGELUARAN (RP)</th>
                </tr>
            </thead>
            <tbody>
                <!-- BAGIAN I: PENERIMAAN -->
                <tr class="section-header">
                    <td colspan="4">I. PENERIMAAN KAS DANA INFAQ</td>
                </tr>
                <tr class="odd">
                    <td class="center" style="color: #64748b;">-</td>
                    <td style="font-weight: 600;">Saldo Awal Bulan</td>
                    <td class="right" style="font-weight: 600;">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
                    <td class="right" style="color: #94a3b8;">-</td>
                </tr>
                @php $noIn = 1; @endphp
                @forelse($penerimaanGrouped as $item)
                    <tr class="{{ $loop->even ? 'even' : 'odd' }}">
                        <td class="center" style="color: #64748b;">{{ $noIn++ }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="right" style="font-weight: 600;">{{ number_format($item->amount, 0, ',', '.') }}</td>
                        <td class="right" style="color: #94a3b8;">-</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #94a3b8; font-style: italic;">
                            Tidak ada catatan penerimaan pada periode ini.
                        </td>
                    </tr>
                @endforelse
                <tr class="subtotal-row">
                    <td colspan="2" class="right" style="text-transform: uppercase;">SUBTOTAL PENERIMAAN:</td>
                    <td class="right">Rp {{ number_format($subtotalPenerimaan, 0, ',', '.') }}</td>
                    <td class="right" style="color: #94a3b8;">-</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" class="right" style="text-transform: uppercase;">TOTAL KAS TERSEDIA:</td>
                    <td class="right">Rp {{ number_format($totalKasTersedia, 0, ',', '.') }}</td>
                    <td class="right" style="color: #94a3b8;">-</td>
                </tr>

                <!-- BAGIAN II: PENGELUARAN RUTIN -->
                <tr class="section-header">
                    <td colspan="4">II. PENGELUARAN RUTIN</td>
                </tr>
                @php $noRutin = 1; @endphp
                @forelse($pengeluaranRutinGrouped as $item)
                    <tr class="{{ $loop->even ? 'even' : 'odd' }}">
                        <td class="center" style="color: #64748b;">{{ $noRutin++ }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="right" style="color: #94a3b8;">-</td>
                        <td class="right" style="font-weight: 600;">{{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #94a3b8; font-style: italic;">
                            Tidak ada belanja rutin pada periode ini.
                        </td>
                    </tr>
                @endforelse
                <tr class="subtotal-row">
                    <td colspan="2" class="right" style="text-transform: uppercase;">SUBTOTAL PENGELUARAN RUTIN:</td>
                    <td class="right" style="color: #94a3b8;">-</td>
                    <td class="right">Rp {{ number_format($subtotalRutin, 0, ',', '.') }}</td>
                </tr>

                <!-- BAGIAN III: PENGELUARAN NON-RUTIN -->
                <tr class="section-header">
                    <td colspan="4">III. PENGELUARAN NON-RUTIN</td>
                </tr>
                @php $noNonRutin = 1; @endphp
                @forelse($pengeluaranNonRutinGrouped as $item)
                    <tr class="{{ $loop->even ? 'even' : 'odd' }}">
                        <td class="center" style="color: #64748b;">{{ $noNonRutin++ }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="right" style="color: #94a3b8;">-</td>
                        <td class="right" style="font-weight: 600;">{{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #94a3b8; font-style: italic;">
                            Tidak ada belanja non-rutin pada periode ini.
                        </td>
                    </tr>
                @endforelse
                <tr class="subtotal-row">
                    <td colspan="2" class="right" style="text-transform: uppercase;">SUBTOTAL PENGELUARAN NON-RUTIN:</td>
                    <td class="right" style="color: #94a3b8;">-</td>
                    <td class="right">Rp {{ number_format($subtotalNonRutin, 0, ',', '.') }}</td>
                </tr>

                <!-- REKAPITULASI TOTAL PENGELUARAN -->
                <tr class="total-row">
                    <td colspan="2" class="right" style="text-transform: uppercase;">TOTAL PENGELUARAN KAS:</td>
                    <td class="right" style="color: #94a3b8;">-</td>
                    <td class="right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-tfoot">
                    <td colspan="2" class="right" style="text-transform: uppercase; letter-spacing: 0.3px;">
                        SALDO KAS AKHIR:
                    </td>
                    <td colspan="2" class="center">
                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Tanda Tangan Resmi Arus Kas (Bendahara & Ketua DKM) -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <!-- Sisi Kiri: Bendahara DKM -->
                    <td class="sig-box" style="width: 45%;">
                        <div class="sig-city-date">&nbsp;</div>
                        <div class="sig-role">Disusun oleh,<br>Bendahara DKM Masjid</div>
                        @if(!empty($qrCodeBase64))
                            <div class="sig-qr-box">
                                <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Code Verifikasi TTE">
                                <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                            </div>
                        @else
                            <div style="height: 48px;"></div>
                        @endif
                        <div class="sig-name">{{ $bendaharaDkm?->name ?? 'Dwi Prasetyo' }}</div>
                        <div class="sig-meta">Bendahara DKM Masjid Salahuddin</div>
                    </td>

                    <td style="width: 10%;"></td>

                    <!-- Sisi Kanan: Ketua DKM -->
                    <td class="sig-box" style="width: 45%;">
                        <div class="sig-city-date">Malang, {{ $printDate }}</div>
                        <div class="sig-role">Mengetahui &amp; Menyetujui,<br>Ketua DKM Masjid Salahuddin</div>
                        @if(!empty($qrCodeBase64))
                            <div class="sig-qr-box">
                                <img src="{{ $qrCodeBase64 }}" class="sig-qr-img" alt="QR Code Verifikasi TTE">
                                <div class="sig-qr-label">TTE BSrE DISAHKAN</div>
                            </div>
                        @else
                            <div style="height: 48px;"></div>
                        @endif
                        <div class="sig-name">{{ $ketuaDkm?->name ?? 'Moh. Nazil Fuadi Kusmawan' }}</div>
                        <div class="sig-meta">Ketua DKM Masjid Salahuddin</div>
                    </td>
                </tr>
            </table>
        </div>
    @endif

</body>
</html>
