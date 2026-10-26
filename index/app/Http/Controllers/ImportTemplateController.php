<?php

namespace App\Http\Controllers;

use App\Services\SimpleXlsxService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportTemplateController extends Controller
{
    /**
     * Download Excel (.xlsx) template for import features.
     */
    public function download(Request $request, string $type): StreamedResponse
    {
        if (! auth()->user()?->canManage()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk mengunduh template import.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $currentYear = (int) $now->year;
        $currentMonthName = $now->translatedFormat('F');

        switch ($type) {
            case 'kajian_pekanan':
                $filename = 'Template_Import_Kajian_Pekanan.xlsx';
                $sheetName = 'Kajian Pekanan';
                $headers = ['Tanggal', 'Judul Kajian', 'Pemateri', 'No HP Pemateri'];
                $rows = [
                    [$now->format('Y-m-d'), 'Kajian Hadits Riyadhus Shalihin', 'Ustadz Ahmad Fauzi, Lc.', '081234567890'],
                    [$now->copy()->addDays(7)->format('Y-m-d'), 'Kajian Fiqih Ibadah Praktis', 'Ustadz Muhammad Yahya, Ph.D', '081298765432'],
                    [$now->copy()->addDays(14)->format('Y-m-d'), 'Kajian Sirah Nabawiyah', 'Ustadz Alvin Shohih', '085712345678'],
                ];
                $colWidths = [16, 38, 30, 20];
                break;

            case 'kajian_jumat':
                $filename = 'Template_Import_Shalat_Jumat.xlsx';
                $sheetName = 'Shalat Jumat';
                $headers = ['Tanggal', 'Pemateri', 'No HP Pemateri', 'MC', 'Muadzin'];
                // Next Friday
                $nextFriday = $now->copy()->next(Carbon::FRIDAY);
                $rows = [
                    [$nextFriday->format('Y-m-d'), 'Ust. Farid Hamidi Lc.', '081234567890', 'Agung Wiyono', 'Deril Amrizal Kholid'],
                    [$nextFriday->copy()->addWeeks(1)->format('Y-m-d'), 'Ust. Deden Ferry Yuliantoro S.Si', '081398765432', 'Lukman Hakim', 'Deril Amrizal Kholid'],
                    [$nextFriday->copy()->addWeeks(2)->format('Y-m-d'), 'Ust. Hafidz Maulana S.Pd.I', '085712345678', 'Dhafin Fuad Mahathir', 'Agung Wiyono'],
                ];
                $colWidths = [16, 32, 20, 24, 24];
                break;

            case 'petugas_sholat':
                $filename = 'Template_Import_Petugas_Shalat.xlsx';
                $sheetName = 'Petugas Shalat';
                $headers = ['Hari', 'Waktu Shalat', 'Pola Pekan', 'Nama Imam', 'Nama Muadzin', 'Catatan'];
                $rows = [
                    ['Senin', 'Dzuhur', 'Semua Pekan', 'Eka Candra Wijaya', 'Ugik Endrar Viana', 'Petugas Reguler'],
                    ['Senin', 'Ashar', 'Semua Pekan', 'Erfan Nur Faizin', 'Agung Wiyono', 'Petugas Reguler'],
                    ['Selasa', 'Dzuhur', 'Pekan 1', 'Dhafin Fuad Mahathir', 'Moh. Lukman Hakim', 'Rotasi Pekan Ganjil'],
                    ['Rabu', 'Dzuhur', 'Semua Pekan', 'Muhammad Basuki S.Pd', 'Khudori', 'Petugas Reguler'],
                    ['Kamis', 'Ashar', 'Semua Pekan', 'Ustadz Nafis Marzuki Lc.', 'Alan Irfansyah', 'Kajian Ba\'da Ashar'],
                    ['Jumat', 'Ashar', 'Semua Pekan', 'Ugik Endrar Viana', 'Khudori', 'Petugas Ashar Jumat'],
                ];
                $colWidths = [14, 16, 16, 26, 26, 24];
                break;

            case 'agenda_akbar':
                $filename = 'Template_Import_Agenda_Akbar.xlsx';
                $sheetName = 'Agenda Akbar';
                $headers = ['Nama Agenda', 'Tanggal Kegiatan', 'Anggaran', 'Kepanitiaan', 'Status'];
                $rows = [
                    ['Peringatan Nuzulul Qur\'an 1448 H', $currentYear . '-03-25', '15000000', 'Ketua: Ahmad Dani; Anggota: Budi, Agus', 'Direncanakan'],
                    ['Kajian Akbar Tabligh Ramadhan', $currentYear . '-04-02', '25000000', 'Ketua: Dhafin Fuad; Anggota: Lukman, Ugik', 'Berjalan'],
                    ['Penyembelihan & Distribusi Hewan Qurban 1447 H', $currentYear . '-06-16', '65000000', 'Ketua: Eka Candra; Anggota: Tim DKM', 'SELESAI'],
                ];
                $colWidths = [38, 18, 16, 40, 16];
                break;

            case 'kas':
            case 'keuangan':
                $filename = 'Template_Import_Transaksi_Keuangan.xlsx';
                $sheetName = 'Transaksi Keuangan';
                $headers = ['Tipe (Pemasukan/Pengeluaran)', 'Kategori', 'Jumlah / Nominal', 'Tanggal Transaksi', 'Keterangan'];
                $rows = [
                    ['Pemasukan', 'Kotak Infaq Jumat', '3500000', $now->format('Y-m-d'), 'Perolehan infaq shalat jumat berjamaah'],
                    ['Pemasukan', 'Infaq Transfer QRIS', '1250000', $now->format('Y-m-d'), 'Infaq digital jamaah via QRIS'],
                    ['Pengeluaran', 'Operasional & Kebersihan', '750000', $now->format('Y-m-d'), 'Pembelian perlengkapan kebersihan masjid'],
                    ['Pengeluaran', 'Kelistrikan & Air', '1500000', $now->format('Y-m-d'), 'Pembayaran tagihan listrik operasional'],
                ];
                $colWidths = [28, 26, 20, 18, 42];
                break;

            case 'potongan_sosial':
                $filename = 'Template_Import_Potongan_Sosial.xlsx';
                $sheetName = 'Rekap Potongan';
                $headers = ['TAHUN', 'BULAN', 'NAMA PEGAWAI', 'JENIS PROGRAM', 'JUMLAH POTONGAN'];
                $rows = [
                    [(string) $currentYear, strtoupper($currentMonthName), 'Ahmad Fauzi', 'Program Zakat Mal Rutin', '250000'],
                    [(string) $currentYear, strtoupper($currentMonthName), 'Budi Santoso', 'Infaq Rutin', '100000'],
                    [(string) $currentYear, strtoupper($currentMonthName), 'Citra Dewi', 'Program Santunan Anak Yatim', '150000'],
                    [(string) $currentYear, strtoupper($currentMonthName), 'Dedi Pratama', 'Program Santunan Dhuafa', '100000'],
                    [(string) $currentYear, strtoupper($currentMonthName), 'Eka Candra', 'Tabungan Qurban', '300000'],
                ];
                $colWidths = [12, 16, 28, 32, 20];
                break;

            case 'pegawai':
                $filename = 'Template_Import_Data_Pegawai.xlsx';
                $sheetName = 'Data Pegawai';
                $headers = ['NIP (18 Digit)', 'NIP Pendek (9 Digit)', 'Nama Lengkap', 'Jenis Kelamin (L/P)', 'Jabatan', 'Unit Kerja', 'Agama'];
                $rows = [
                    ['198501152010121001', '060085112', 'Ahmad Fauzi, S.E.', 'L', 'Pelaksana', 'Subbagian Umum', 'Islam'],
                    ['198807202014022002', '060088720', 'Siti Rahmawati, S.Kom.', 'P', 'Pemeriksa Pajak', 'Seksi Pengawasan II', 'Islam'],
                    ['199203102015031003', '060092310', 'Budi Santoso', 'L', 'Account Representative', 'Seksi Pengawasan I', 'Islam'],
                ];
                $colWidths = [24, 20, 30, 20, 26, 26, 14];
                break;

            default:
                abort(404, 'Tipe template tidak ditemukan.');
        }

        $xlsxContent = SimpleXlsxService::createXlsx($headers, $rows, $colWidths, $sheetName);

        return response()->streamDownload(function () use ($xlsxContent) {
            echo $xlsxContent;
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
