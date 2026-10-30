<?php

namespace App\Http\Controllers;

use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\User;
use App\Services\QrCodeService;
use App\Services\SimpleXlsxService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * Prepare schedule data for Kajian / Jumat
     */
    protected function prepareKajianScheduleData(Request $request): array
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
        $qrCodeBase64 = QrCodeService::generateBase64($verifyUrl, 4);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthNum = (is_numeric($month) && (int) $month >= 1 && (int) $month <= 12) ? (int) $month : (int) date('n');
        $monthName = $months[$monthNum] ?? 'Januari';
        $activeYear = (is_numeric($year) && (int) $year >= 2020) ? (int) $year : (int) date('Y');

        return [
            'type' => $type,
            'settings' => $settings,
            'kajians' => $kajians,
            'month' => $month,
            'monthName' => $monthName,
            'year' => $activeYear,
            'printDate' => Carbon::now()->translatedFormat('d F Y'),
            'ketuaDkm' => $ketuaDkm,
            'koordinatorDakwah' => $koordinatorDakwah,
            'isTteSigned' => true,
            'tteHash' => $tteHash,
            'verifyUrl' => $verifyUrl,
            'qrCodeBase64' => $qrCodeBase64,
        ];
    }

    /**
     * Printable Clean PDF View for Schedule (HTML preview / browser print)
     */
    public function printPdf(Request $request)
    {
        $data = $this->prepareKajianScheduleData($request);
        return view('admin.print.kajian-schedule', $data);
    }

    /**
     * Direct Server-Side Stream PDF Download (No blank tab, instant download)
     */
    public function downloadPdf(Request $request): StreamedResponse
    {
        $data = $this->prepareKajianScheduleData($request);
        $typeLabel = ($data['type'] === 'jumat') ? 'Shalat_Jumat' : 'Kajian_Pekanan';
        $cleanFilename = "Jadwal_{$typeLabel}_{$data['monthName']}_{$data['year']}.pdf";

        $pdf = Pdf::loadView('pdf.kajian-schedule-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $cleanFilename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $cleanFilename . '"',
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

        $from = request()->query('from');
        $referer = request()->header('referer');

        if ($from === 'admin') {
            $backUrl = url('/admin/kegiatan');
        } elseif ($from === 'portal' || !auth()->check() || (auth()->check() && !auth()->user()->canManage())) {
            $backUrl = route('portal.kegiatan');
        } elseif ($referer && (str_contains($referer, '/kegiatan') || str_contains($referer, '/portal') || $referer === url('/') || $referer === url('/kegiatan'))) {
            $backUrl = $referer;
        } else {
            $backUrl = auth()->check() ? url('/admin/kegiatan') : route('portal.kegiatan');
        }

        return view('admin.print.mc-text-jumat', [
            'kajian' => $kajian,
            'settings' => $settings,
            'ketuaDkm' => $ketuaDkm,
            'nextMondayKajian' => $nextMondayKajian,
            'formattedJumatDate' => $formattedJumatDate,
            'formattedNextMondayDate' => $formattedNextMondayDate,
            'timeDisplay' => $timeDisplay ?: '11:30 WIB',
            'formattedDate' => $jumatCarbon->locale('id')->translatedFormat('l, d F Y'),
            'backUrl' => $backUrl
        ]);
    }
}
