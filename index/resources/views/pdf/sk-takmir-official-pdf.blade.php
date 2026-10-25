<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Draf Dokumen Resmi Surat Keputusan Takmir Masjid Salahuddin</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 15mm 18mm 15mm 18mm;
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
            color: #0f172a;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .page-container {
            width: 100%;
            position: relative;
        }
        .page-break {
            page-break-after: always;
        }

        /* Kop Naskah Dinas Kemenkeu RI */
        .kop-box {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-dept {
            font-size: 10.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .kop-dirjen {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
        }
        .kop-kanwil {
            font-size: 8.5pt;
            font-weight: 600;
            text-transform: uppercase;
            color: #1e293b;
        }
        .kop-kpp {
            font-size: 10pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #06172e;
        }
        .kop-address {
            font-size: 7pt;
            color: #475569;
            margin-top: 2px;
        }

        /* Judul KEP */
        .doc-title-box {
            text-align: center;
            margin-bottom: 10px;
        }
        .doc-title {
            font-size: 10.5pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .doc-number {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .doc-about {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .doc-subject {
            font-size: 10pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #06172e;
            max-width: 90%;
            margin: 0 auto 6px auto;
            line-height: 1.25;
        }
        .doc-official {
            font-size: 9.5pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
        }

        /* Konsiderans */
        .table-legal {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .table-legal td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 8.5pt;
            text-align: justify;
            line-height: 1.35;
        }
        .legal-label {
            width: 80px;
            font-weight: bold;
            color: #0f172a;
        }
        .legal-colon {
            width: 12px;
            font-weight: bold;
            text-align: center;
        }
        .legal-bullet {
            width: 16px;
            font-weight: bold;
        }

        .memutuskan-box {
            text-align: center;
            font-size: 9.5pt;
            font-weight: 800;
            letter-spacing: 0.8px;
            padding: 4px 0;
            color: #0f172a;
        }

        /* Diktum */
        .diktum-table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 15px;
            margin-bottom: 8px;
        }
        .diktum-table td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 8.5pt;
            text-align: justify;
            line-height: 1.35;
        }
        .diktum-title {
            width: 75px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .diktum-colon {
            width: 10px;
            font-weight: bold;
            text-align: center;
        }

        /* Tanda Tangan Naskah Dinas */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: avoid;
        }
        .signature-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .sig-box {
            text-align: left;
            width: 250px;
            font-size: 8.5pt;
        }
        .sig-date {
            color: #1e293b;
            margin-bottom: 2px;
        }
        .sig-official-role {
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 3px;
            line-height: 1.25;
        }
        .sig-name {
            font-size: 9pt;
            font-weight: 800;
            text-decoration: underline;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .sig-meta {
            font-size: 7.5pt;
            color: #475569;
        }

        .sig-tte-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            background: transparent;
            margin: 2px 0 1px 0;
        }
        .sig-tte-table td {
            border: none;
            background: transparent;
            padding: 0;
            height: 75px;
            vertical-align: bottom;
            text-align: left;
        }
        .sig-tte-note {
            font-size: 7.5pt;
            color: #94a3b8;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        /* Lampiran Header */
        .lampiran-kop {
            border-bottom: 1px solid #94a3b8;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .lampiran-kop-left {
            float: left;
            font-size: 8pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }
        .lampiran-kop-right {
            float: right;
            text-align: right;
            font-size: 7.5pt;
            color: #1e293b;
            line-height: 1.25;
        }
        .clearfix {
            clear: both;
        }

        .lampiran-title {
            text-align: center;
            font-size: 10pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #06172e;
            margin-bottom: 2px;
        }
        .lampiran-subtitle {
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e293b;
            margin-bottom: 10px;
        }

        /* Tabel Data Lampiran I */
        .struct-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .struct-table th {
            background-color: #f1f5f9;
            border: 0.8px solid #94a3b8;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 6px;
            color: #0f172a;
        }
        .struct-table td {
            border: 0.8px solid #cbd5e1;
            font-size: 7.5pt;
            padding: 4px 6px;
            vertical-align: top;
            color: #0f172a;
        }
        .struct-table td.center {
            text-align: center;
        }

        /* Tupoksi Box Lampiran II */
        .tupoksi-card {
            border: 0.8px solid #cbd5e1;
            border-radius: 4px;
            padding: 5px 8px;
            margin-bottom: 6px;
            background-color: #fafafa;
        }
        .tupoksi-card-title {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #06172e;
            margin-bottom: 3px;
        }
        .tupoksi-list {
            margin: 0;
            padding-left: 14px;
            font-size: 7.5pt;
            line-height: 1.35;
            color: #1e293b;
        }
        .tupoksi-list li {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

    <!-- ========================================================================= -->
    <!-- HALAMAN 1: SURAT KEPUTUSAN KEPALA KPP MADYA MALANG                        -->
    <!-- ========================================================================= -->
    <div class="page-container page-break">
        <!-- Kop Surat Resmi -->
        <div class="kop-box">
            <div class="kop-dept">Kementerian Keuangan Republik Indonesia</div>
            <div class="kop-dirjen">Direktorat Jenderal Pajak</div>
            <div class="kop-kanwil">Kantor Wilayah DJP Jawa Timur III</div>
            <div class="kop-kpp">Kantor Pelayanan Pajak Madya Malang</div>
            <div class="kop-address">Jalan Danau Toba No. 1, Sawojajar, Kedungkandang, Kota Malang, Jawa Timur 65139</div>
        </div>

        <!-- Judul KEP -->
        <div class="doc-title-box">
            <div class="doc-title">KEPUTUSAN KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG</div>
            <div class="doc-number">NOMOR: {{ $skNumber }}</div>
            <div class="doc-about">TENTANG</div>
            <div class="doc-subject">PERUBAHAN SUSUNAN PENGURUS TAKMIR MASJID SALAHUDDIN KPP MADYA MALANG PERIODE {{ $periode }}</div>
            <div class="doc-official">KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG,</div>
        </div>

        <!-- Konsiderans Menimbang -->
        <table class="table-legal">
            <tr>
                <td class="legal-label">Menimbang</td>
                <td class="legal-colon">:</td>
                <td>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td class="legal-bullet">a.</td>
                            <td>bahwa dalam rangka meningkatkan syiar Islam, pembinaan keimanan, dan ketakwaan pegawai di lingkungan Kantor Pelayanan Pajak Madya Malang, perlu didukung oleh kepengurusan Takmir Masjid yang berkesinambungan;</td>
                        </tr>
                        <tr>
                            <td class="legal-bullet">b.</td>
                            <td>bahwa sehubungan dengan adanya mutasi dan alih tugas pegawai di lingkungan Kantor Pelayanan Pajak Madya Malang, perlu dilakukan penyesuaian dan perubahan terhadap susunan kepengurusan Takmir Masjid Salahuddin;</td>
                        </tr>
                        <tr>
                            <td class="legal-bullet">c.</td>
                            <td>bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a dan huruf b, perlu menetapkan Keputusan Kepala Kantor Pelayanan Pajak Madya Malang tentang Perubahan Susunan Pengurus Takmir Masjid Salahuddin KPP Madya Malang Periode {{ $periode }};</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Konsiderans Mengingat -->
        <table class="table-legal">
            <tr>
                <td class="legal-label">Mengingat</td>
                <td class="legal-colon">:</td>
                <td>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td class="legal-bullet">1.</td>
                            <td>Undang-Undang Nomor 20 Tahun 2023 tentang Aparatur Sipil Negara;</td>
                        </tr>
                        <tr>
                            <td class="legal-bullet">2.</td>
                            <td>Peraturan Menteri Keuangan Nomor 118/PMK.01/2021 tentang Organisasi dan Tata Kerja Kementerian Keuangan sebagaimana telah diubah beberapa kali terakhir dengan PMK Nomor 135 Tahun 2023;</td>
                        </tr>
                        <tr>
                            <td class="legal-bullet">3.</td>
                            <td>Anggaran Dasar dan Anggaran Rumah Tangga Pembinaan Kerohanian Islam di lingkungan Direktorat Jenderal Pajak;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Memutuskan -->
        <div class="memutuskan-box">MEMUTUSKAN:</div>

        <table class="table-legal">
            <tr>
                <td class="legal-label">Menetapkan</td>
                <td class="legal-colon">:</td>
                <td style="font-weight: bold; text-transform: uppercase;">
                    KEPUTUSAN KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG TENTANG PERUBAHAN SUSUNAN PENGURUS TAKMIR MASJID SALAHUDDIN KPP MADYA MALANG PERIODE {{ $periode }}.
                </td>
            </tr>
        </table>

        <!-- Diktum KESATU - KELIMA -->
        <table class="diktum-table">
            <tr>
                <td class="diktum-title">KESATU</td>
                <td class="diktum-colon">:</td>
                <td>Memberhentikan dengan hormat susunan pengurus Takmir Masjid Salahuddin periode sebelumnya dan menyampaikan ucapan terima kasih atas segala dedikasi dan pengabdian yang telah diberikan.</td>
            </tr>
            <tr>
                <td class="diktum-title">KEDUA</td>
                <td class="diktum-colon">:</td>
                <td>Menetapkan susunan personalia pengurus Takmir Masjid Salahuddin Kantor Pelayanan Pajak Madya Malang Periode {{ $periode }} dengan rincian sebagaimana tercantum dalam <strong>Lampiran I</strong> yang merupakan bagian tidak terpisahkan dari Keputusan ini.</td>
            </tr>
            <tr>
                <td class="diktum-title">KETIGA</td>
                <td class="diktum-colon">:</td>
                <td>Menetapkan rincian penjabaran tugas pokok dan fungsi (Tupoksi) Pengurus Takmir Masjid Salahuddin Kantor Pelayanan Pajak Madya Malang Periode {{ $periode }} sebagaimana tercantum dalam <strong>Lampiran II</strong> yang merupakan bagian tidak terpisahkan dari Keputusan ini.</td>
            </tr>
            <tr>
                <td class="diktum-title">KEEMPAT</td>
                <td class="diktum-colon">:</td>
                <td>Segala biaya yang timbul sebagai akibat ditetapkannya Keputusan ini dibebankan pada dana infaq dan kas Takmir Masjid Salahuddin KPP Madya Malang secara akuntabel.</td>
            </tr>
            <tr>
                <td class="diktum-title">KELIMA</td>
                <td class="diktum-colon">:</td>
                <td>Keputusan ini mulai berlaku sejak tanggal ditetapkan, dengan ketentuan apabila di kemudian hari terdapat kekeliruan dalam penetapannya akan diadakan perbaikan sebagaimana mestinya.</td>
            </tr>
        </table>

        <!-- Tanda Tangan Pembina -->
        <table class="signature-table">
            <tr>
                <td></td>
                <td class="sig-box">
                    <div class="sig-date">Ditetapkan di Malang</div>
                    <div class="sig-date">pada tanggal {{ $skDate }}</div>
                    <div class="sig-official-role">KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG,</div>
                    <table class="sig-tte-table">
                        <tr>
                            <td>
                                <div class="sig-tte-note">Ditandatangani secara elektronik</div>
                            </td>
                        </tr>
                    </table>
                    <div class="sig-name">{{ $struct['pembina']['name'] ?? 'Teguh Iman Wirotomo' }}</div>
                    <div class="sig-meta">Pembina Takmir Masjid Salahuddin</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 2: LAMPIRAN I (SUSUNAN PENGURUS TAKMIR)                           -->
    <!-- ========================================================================= -->
    <div class="page-container page-break">
        <!-- Kop Lampiran I -->
        <div class="lampiran-kop">
            <div class="lampiran-kop-right">
                <strong>LAMPIRAN I KEPUTUSAN KEPALA KPP MADYA MALANG</strong><br>
                NOMOR : {{ $skNumber }}<br>
                TANGGAL : {{ $skDate }}
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="lampiran-title">SUSUNAN PENGURUS TAKMIR MASJID SALAHUDDIN</div>
        <div class="lampiran-subtitle">KANTOR PELAYANAN PAJAK MADYA MALANG PERIODE {{ $periode }}</div>

        <!-- Tabel Susunan Pengurus -->
        <table class="struct-table">
            <thead>
                <tr>
                    <th style="width: 26px;" class="center">NO</th>
                    <th style="width: 140px;">JABATAN DALAM TAKMIR</th>
                    <th>NAMA PENGURUS</th>
                    <th style="width: 110px;" class="center">KEDUDUKAN</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. Pembina -->
                <tr>
                    <td class="center font-bold">1</td>
                    <td style="font-weight: bold;">Pembina</td>
                    <td style="font-weight: bold;">{{ $struct['pembina']['name'] ?? 'Teguh Iman Wirotomo' }}</td>
                    <td class="center">Ex-Officio Kepala Kantor</td>
                </tr>

                <!-- 2. Ketua -->
                <tr>
                    <td class="center font-bold">2</td>
                    <td style="font-weight: bold;">Ketua Takmir</td>
                    <td style="font-weight: bold;">{{ $struct['ketua']['name'] ?? 'Moh. Nazil Fuadi Kusmawan' }}</td>
                    <td class="center">Ketua Harian</td>
                </tr>

                <!-- 3. Wakil Ketua -->
                <tr>
                    <td class="center font-bold">3</td>
                    <td style="font-weight: bold;">Wakil Ketua Takmir</td>
                    <td style="font-weight: bold;">{{ $struct['wakil_ketua']['name'] ?? 'Mujiburrokhman' }}</td>
                    <td class="center">Wakil Ketua</td>
                </tr>

                <!-- 4. Sekretaris -->
                <tr>
                    <td class="center font-bold">4</td>
                    <td style="font-weight: bold;">Sekretaris</td>
                    <td>
                        <ol style="margin: 0; padding-left: 14px;">
                            @foreach($struct['sekretaris']['names'] ?? [] as $sName)
                                <li>{{ $sName }}</li>
                            @endforeach
                        </ol>
                    </td>
                    <td class="center">Pengurus Harian</td>
                </tr>

                <!-- 5. Bendahara -->
                <tr>
                    <td class="center font-bold">5</td>
                    <td style="font-weight: bold;">Bendahara</td>
                    <td>
                        <ol style="margin: 0; padding-left: 14px;">
                            @foreach($struct['bendahara']['names'] ?? [] as $bName)
                                <li>{{ $bName }}</li>
                            @endforeach
                        </ol>
                    </td>
                    <td class="center">Pengurus Harian</td>
                </tr>

                <!-- 6..9 4 Bidang -->
                @foreach($struct['bidang'] ?? [] as $idx => $b)
                    <tr>
                        <td class="center font-bold">{{ $idx + 6 }}</td>
                        <td style="font-weight: bold;">{{ $b['name'] }}</td>
                        <td>
                            <div>
                                <strong style="font-size: 7pt; color: #475569; text-transform: uppercase;">Koordinator / Pengelola:</strong>
                                <div>{{ implode(', ', $b['pengelola'] ?? []) ?: '-' }}</div>
                            </div>
                            @if(!empty($b['anggota']))
                                <div style="margin-top: 3px; padding-top: 3px; border-top: 0.5px solid #e2e8f0;">
                                    <strong style="font-size: 7pt; color: #475569; text-transform: uppercase;">Anggota Bidang:</strong>
                                    <div style="color: #334155;">{{ implode(', ', $b['anggota']) }}</div>
                                </div>
                            @endif
                        </td>
                        <td class="center">Pengelola Bidang</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tanda Tangan Pembina -->
        <table class="signature-table">
            <tr>
                <td></td>
                <td class="sig-box">
                    <div class="sig-official-role">KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG,</div>
                    <table class="sig-tte-table">
                        <tr>
                            <td>
                                <div class="sig-tte-note">Ditandatangani secara elektronik</div>
                            </td>
                        </tr>
                    </table>
                    <div class="sig-name">{{ $struct['pembina']['name'] ?? 'Teguh Iman Wirotomo' }}</div>
                    <div class="sig-meta">Pembina Takmir Masjid Salahuddin</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 3: LAMPIRAN II (PENJABARAN TUPOKSI)                               -->
    <!-- ========================================================================= -->
    <div class="page-container">
        <!-- Kop Lampiran II -->
        <div class="lampiran-kop">
            <div class="lampiran-kop-right">
                <strong>LAMPIRAN II KEPUTUSAN KEPALA KPP MADYA MALANG</strong><br>
                NOMOR : {{ $skNumber }}<br>
                TANGGAL : {{ $skDate }}
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="lampiran-title">PENJABARAN TUGAS POKOK DAN FUNGSI (TUPOKSI)</div>
        <div class="lampiran-subtitle">PENGURUS TAKMIR MASJID SALAHUDDIN KPP MADYA MALANG PERIODE {{ $periode }}</div>

        <!-- 1. Pembina -->
        <div class="tupoksi-card">
            <div class="tupoksi-card-title">1. Pembina Takmir</div>
            <ul class="tupoksi-list">
                @forelse($struct['pembina']['tugas'] ?? [] as $tugas)
                    <li>{{ $tugas }}</li>
                @empty
                    <li>Memberikan arahan umum dan pembinaan keagamaan masjid agar tetap lurus di atas syariat Islam.</li>
                    <li>Mengawasi keselarasan program kerja Takmir dengan kebijakan institusi/lingkungan.</li>
                @endforelse
            </ul>
        </div>

        <!-- 2. Ketua -->
        <div class="tupoksi-card">
            <div class="tupoksi-card-title">2. Ketua Takmir</div>
            <ul class="tupoksi-list">
                @forelse($struct['ketua']['tugas'] ?? [] as $tugas)
                    <li>{{ $tugas }}</li>
                @empty
                    <li>Memimpin, mengoordinasikan, dan memantau seluruh aktivitas pelaksanaan program Takmir Masjid.</li>
                    <li>Menjalin hubungan internal dengan pimpinan kantor serta jaringan dakwah eksternal.</li>
                @endforelse
            </ul>
        </div>

        <!-- 3. Wakil Ketua -->
        <div class="tupoksi-card">
            <div class="tupoksi-card-title">3. Wakil Ketua Takmir</div>
            <ul class="tupoksi-list">
                @forelse($struct['wakil_ketua']['tugas'] ?? [] as $tugas)
                    <li>{{ $tugas }}</li>
                @empty
                    <li>Mendampingi Ketua Takmir dalam mengarahkan operasional kepemimpinan harian.</li>
                    <li>Mewakili penandatanganan dokumen atau pertemuan takmir apabila Ketua berhalangan.</li>
                @endforelse
            </ul>
        </div>

        <!-- 4. Sekretaris -->
        <div class="tupoksi-card">
            <div class="tupoksi-card-title">4. Sekretaris</div>
            <ul class="tupoksi-list">
                @forelse($struct['sekretaris']['tugas'] ?? [] as $tugas)
                    <li>{{ $tugas }}</li>
                @empty
                    <li>Mengelola administrasi persuratan resmi, proposal, notulensi rapat, database kepengurusan, dan pengarsipan.</li>
                    <li>Mempersiapkan draf laporan berkala s.d. Laporan Pertanggungjawaban (LPJ).</li>
                @endforelse
            </ul>
        </div>

        <!-- 5. Bendahara -->
        <div class="tupoksi-card">
            <div class="tupoksi-card-title">5. Bendahara</div>
            <ul class="tupoksi-list">
                @forelse($struct['bendahara']['tugas'] ?? [] as $tugas)
                    <li>{{ $tugas }}</li>
                @empty
                    <li>Mengelola dan mengontrol seluruh kas penerimaan, pengeluaran riil, dan mutasi saldo infaq/zakat.</li>
                    <li>Menyusun laporan keuangan berkala untuk dipublikasikan secara transparan kepada jamaah.</li>
                @endforelse
            </ul>
        </div>

        <!-- 6..9 4 Bidang -->
        @foreach($struct['bidang'] ?? [] as $idx => $b)
            <div class="tupoksi-card">
                <div class="tupoksi-card-title">{{ $idx + 6 }}. {{ $b['name'] }}</div>
                <ul class="tupoksi-list">
                    @forelse($b['tupoksi'] ?? [] as $tup)
                        <li>{{ $tup }}</li>
                    @empty
                        <li>Melaksanakan seluruh agenda operasional dan program kerja yang berada dalam lingkup bidang.</li>
                    @endforelse
                </ul>
            </div>
        @endforeach

        <!-- Tanda Tangan Pembina -->
        <table class="signature-table">
            <tr>
                <td></td>
                <td class="sig-box">
                    <div class="sig-official-role">KEPALA KANTOR PELAYANAN PAJAK MADYA MALANG,</div>
                    <table class="sig-tte-table">
                        <tr>
                            <td>
                                <div class="sig-tte-note">Ditandatangani secara elektronik</div>
                            </td>
                        </tr>
                    </table>
                    <div class="sig-name">{{ $struct['pembina']['name'] ?? 'Teguh Iman Wirotomo' }}</div>
                    <div class="sig-meta">Pembina Takmir Masjid Salahuddin</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
