<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\MasjidSetting;
use App\Models\User;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AgendaExportController extends Controller
{
    /**
     * Export Agenda as Excel / CSV
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $fileName = 'Agenda_Kegiatan_Masjid_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it with proper encoding
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'No',
                'Nama Agenda Kegiatan',
                'Tanggal Kegiatan',
                'Anggaran (Rp)',
                'Status',
                'Tujuan / Deskripsi',
                'Susunan Panitia',
                'Ringkasan Laporan (LPJ)'
            ]);

            $agendas = Agenda::orderBy('event_date', 'desc')->get();
            foreach ($agendas as $index => $a) {
                fputcsv($handle, [
                    $index + 1,
                    $a->title,
                    Carbon::parse($a->event_date)->translatedFormat('l, d F Y'),
                    $a->budget,
                    $a->status,
                    $a->description ?: '-',
                    $a->committee_members ? str_replace(["\r\n", "\n", "\r"], '; ', $a->committee_members) : '-',
                    $a->report_summary ? str_replace(["\r\n", "\n", "\r"], ' ', $a->report_summary) : '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Prepare data for Agenda schedule export / PDF
     */
    protected function prepareAgendaScheduleData(Request $request): array
    {
        $agendas = Agenda::orderBy('event_date', 'desc')->get();
        $settings = MasjidSetting::getActive() ?? MasjidSetting::first();
        $printDate = Carbon::now()->translatedFormat('d F Y');

        // Penandatangan resmi
        $ketuaDkm = User::where('role', 'Ketua')->where('status', 'AKTIF')->first()
            ?? User::where('role', 'like', '%Ketua%')->first();

        // Cari Ketua Panitia / Acara dari agenda yang ada, atau panitia/sekretaris
        $ketuaAcara = null;
        foreach ($agendas as $agenda) {
            if ($agenda->ketua_panitia) {
                $ketuaAcara = $agenda->ketua_panitia;
                break;
            }
        }
        if (!$ketuaAcara) {
            $sekretaris = User::where('role', 'Sekretaris')->where('status', 'AKTIF')->first();
            $ketuaAcara = $sekretaris?->name ?? 'Ichtiar Rachmatullah';
        }

        $tteHash = 'TTE-' . strtoupper(substr(md5(($settings->name ?? 'MS') . 'AGENDA' . date('Y-m')), 0, 10));
        $verifyUrl = url('/admin/agenda/export-pdf?tte=1&hash=' . $tteHash);
        $qrCodeBase64 = QrCodeService::generateBase64($verifyUrl, 4);

        return [
            'agendas' => $agendas,
            'totalBudget' => $agendas->sum('budget'),
            'settings' => $settings,
            'printDate' => $printDate,
            'ketuaDkm' => $ketuaDkm,
            'ketuaAcara' => $ketuaAcara,
            'isTteSigned' => true,
            'tteHash' => $tteHash,
            'verifyUrl' => $verifyUrl,
            'qrCodeBase64' => $qrCodeBase64,
        ];
    }

    /**
     * Print all agendas as formal printable schedule/report (HTML View)
     */
    public function printPdf(Request $request)
    {
        $data = $this->prepareAgendaScheduleData($request);
        return view('admin.print.agenda-schedule', $data);
    }

    /**
     * Direct Server-Side Stream PDF Download for Agenda Kegiatan (No blank tab, instant download)
     */
    public function downloadPdf(Request $request): StreamedResponse
    {
        $data = $this->prepareAgendaScheduleData($request);
        $cleanFilename = 'Agenda_Kegiatan_Masjid_' . date('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('pdf.agenda-schedule-pdf', $data)
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
     * Print single Agenda LPJ / Direct Download uploaded PDF document
     */
    public function printLpj(int $id)
    {
        $agenda = Agenda::findOrFail($id);

        if (!empty($agenda->report_pdf_path)) {
            $safeName = 'LPJ_' . Str::slug($agenda->title) . '.pdf';

            // Cek di disk public Storage
            if (Storage::disk('public')->exists($agenda->report_pdf_path)) {
                $filePath = Storage::disk('public')->path($agenda->report_pdf_path);
                return response()->download($filePath, $safeName, [
                    'Content-Type' => 'application/pdf',
                ]);
            }

            // Cek jika path tersimpan langsung di public/storage
            $directPath = public_path('storage/' . $agenda->report_pdf_path);
            if (file_exists($directPath)) {
                return response()->download($directPath, $safeName, [
                    'Content-Type' => 'application/pdf',
                ]);
            }

            // Cek jika path tersimpan langsung di root public/
            $publicPath = public_path($agenda->report_pdf_path);
            if (file_exists($publicPath)) {
                return response()->download($publicPath, $safeName, [
                    'Content-Type' => 'application/pdf',
                ]);
            }
        }

        $settings = MasjidSetting::first();
        $printDate = Carbon::now()->translatedFormat('d F Y');

        return view('admin.print.agenda-lpj', [
            'agenda' => $agenda,
            'settings' => $settings,
            'printDate' => $printDate,
        ]);
    }
}
