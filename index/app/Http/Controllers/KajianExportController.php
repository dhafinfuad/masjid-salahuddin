<?php

namespace App\Http\Controllers;

use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\User;
use App\Services\SimpleXlsxService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KajianExportController extends Controller
{
    /**
     * Export Kajian Schedule as Excel (.xlsx) with Text formatted columns
     */
    public function exportExcel(Request $request): Response
    {
        $type = $request->query('type', 'pekanan');
        $month = $request->query('month', 'all');
        $year = $request->query('year', 'all');
        $fileName = 'Jadwal_Kajian_' . ucfirst($type) . '_' . date('Y-m-d') . '.xlsx';

        if ($type === 'jumat') {
            $headers = [
                'No',
                'Hari & Tanggal',
                'Waktu',
                'Status Libur',
                'Judul / Tema Khutbah',
                'Khatib',
                'MC',
                'Muadzin',
                'No HP Khatib'
            ];
            $colWidths = [6, 26, 16, 28, 32, 24, 22, 22, 18];

            $query = Kajian::jumat();
            if ($month !== 'all' && is_numeric($month)) {
                $query->whereMonth('date', (int) $month);
            }
            if ($year !== 'all' && is_numeric($year)) {
                $query->whereYear('date', (int) $year);
            }
            $kajians = $query->orderBy('date')->get();

            $rows = [];
            foreach ($kajians as $index => $k) {
                $rows[] = [
                    (string) ($index + 1),
                    Carbon::parse($k->date)->translatedFormat('l, d F Y'),
                    $k->time_display ?: '11:45 - 12:45',
                    $k->is_holiday_disabled ? 'HARI LIBUR (PETUGAS NONAKTIF)' : 'AKTIF',
                    $k->title,
                    $k->khatib_name ?: '-',
                    $k->mc_name ?: '-',
                    $k->muadzin_name ?: '-',
                    $k->khatib_phone ?: '-',
                ];
            }
        } else {
            $headers = [
                'No',
                'Jenis',
                'Hari & Tanggal',
                'Waktu',
                'Judul / Tema Kajian',
                'Pembicara',
                'No HP Pembicara'
            ];
            $colWidths = [6, 14, 26, 16, 32, 26, 18];

            $query = Kajian::kajianUmum();
            if ($month !== 'all' && is_numeric($month)) {
                $query->whereMonth('date', (int) $month);
            }
            if ($year !== 'all' && is_numeric($year)) {
                $query->whereYear('date', (int) $year);
            }
            $kajians = $query->orderBy('date')->get();

            $rows = [];
            foreach ($kajians as $index => $k) {
                $rows[] = [
                    (string) ($index + 1),
                    $k->type === 'tematik' ? 'Tematik' : 'Pekanan',
                    Carbon::parse($k->date)->translatedFormat('l, d F Y'),
                    $k->time_display ?: '09:00 - 11:30',
                    $k->title,
                    $k->speaker_name ?: '-',
                    $k->speaker_phone ?: '-',
                ];
            }
        }

        $xlsxContent = SimpleXlsxService::createXlsx($headers, $rows, $colWidths, 'Jadwal ' . ucfirst($type));

        return response($xlsxContent, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Content-Length' => strlen($xlsxContent),
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Printable Clean PDF View for Schedule
     */
    public function printPdf(Request $request)
    {
        $type = $request->query('type', 'pekanan');
        $month = $request->query('month', 'all');
        $year = $request->query('year', 'all');
        $settings = MasjidSetting::getActive();

        $query = ($type === 'jumat') ? Kajian::jumat() : Kajian::kajianUmum();
        if ($month !== 'all' && is_numeric($month)) {
            $query->whereMonth('date', (int) $month);
        }
        if ($year !== 'all' && is_numeric($year)) {
            $query->whereYear('date', (int) $year);
        }
        $kajians = $query->orderBy('date')->get();

        // Penandatangan resmi
        $ketuaDkm = User::where('role', 'Ketua')->where('status', 'AKTIF')->first()
            ?? User::where('role', 'like', '%Ketua%')->first();
        $koordinatorDakwah = User::where('role', 'Sekretaris')->where('status', 'AKTIF')->first()
            ?? User::where('role', 'like', '%Sekretaris%')->first();

        $tteHash = 'TTE-' . strtoupper(substr(md5(($settings->name ?? 'MS') . 'KAJIAN' . $type . date('Y-m')), 0, 10));
        $verifyUrl = url('/admin/kajian/export-pdf?type=' . $type . '&tte=1&hash=' . $tteHash);

        return view('admin.print.kajian-schedule', [
            'type' => $type,
            'settings' => $settings,
            'kajians' => $kajians,
            'printDate' => Carbon::now()->translatedFormat('d F Y'),
            'ketuaDkm' => $ketuaDkm,
            'koordinatorDakwah' => $koordinatorDakwah,
            'isTteSigned' => true,
            'tteHash' => $tteHash,
            'verifyUrl' => $verifyUrl,
        ]);
    }

    /**
     * Printable Teks MC Shalat Jumat PDF
     */
    public function printMcText(int $id)
    {
        $kajian = Kajian::findOrFail($id);
        $settings = MasjidSetting::getActive();
        $ketuaDkm = User::where('role', 'Ketua')->where('status', 'AKTIF')->first() 
            ?? User::where('role', 'like', '%Ketua%')->first();

        // Ambil kajian pekanan pada hari Senin minggu depannya
        $jumatCarbon = Carbon::parse($kajian->date);
        $nextMonday = $jumatCarbon->copy()->next(Carbon::MONDAY);
        $nextMondayKajian = Kajian::where('type', 'pekanan')
            ->whereDate('date', $nextMonday->toDateString())
            ->first();

        $formattedJumatDate = $jumatCarbon->locale('id')->isoFormat('D MMMM Y');
        $formattedNextMondayDate = $nextMonday->locale('id')->isoFormat('dddd') . ' tanggal ' . $nextMonday->locale('id')->isoFormat('D MMMM Y');

        $timeDisplay = trim((string) $kajian->time_display);
        if (!empty($timeDisplay) && !str_ends_with(strtoupper($timeDisplay), 'WIB')) {
            $timeDisplay .= ' WIB';
        }

        return view('admin.print.mc-text-jumat', [
            'kajian' => $kajian,
            'settings' => $settings,
            'ketuaDkm' => $ketuaDkm,
            'nextMondayKajian' => $nextMondayKajian,
            'formattedJumatDate' => $formattedJumatDate,
            'formattedNextMondayDate' => $formattedNextMondayDate,
            'timeDisplay' => $timeDisplay ?: '11:30 WIB',
            'formattedDate' => $jumatCarbon->locale('id')->translatedFormat('l, d F Y')
        ]);
    }
}
