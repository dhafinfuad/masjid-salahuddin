<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\MasjidSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
     * Print all agendas as formal printable schedule/report
     */
    public function printPdf(Request $request)
    {
        $agendas = Agenda::orderBy('event_date', 'desc')->get();
        $settings = MasjidSetting::getActive() ?? MasjidSetting::first();
        $printDate = Carbon::now()->translatedFormat('d F Y');

        // Penandatangan resmi
        $ketuaDkm = \App\Models\User::where('role', 'Ketua')->where('status', 'AKTIF')->first()
            ?? \App\Models\User::where('role', 'like', '%Ketua%')->first();

        // Cari Ketua Panitia / Acara dari agenda yang ada, atau panitia/sekretaris
        $ketuaAcara = null;
        foreach ($agendas as $agenda) {
            if ($agenda->ketua_panitia) {
                $ketuaAcara = $agenda->ketua_panitia;
                break;
            }
        }
        if (!$ketuaAcara) {
            $sekretaris = \App\Models\User::where('role', 'Sekretaris')->where('status', 'AKTIF')->first();
            $ketuaAcara = $sekretaris?->name ?? 'Ichtiar Rachmatullah';
        }

        $tteHash = 'TTE-' . strtoupper(substr(md5(($settings->name ?? 'MS') . 'AGENDA' . date('Y-m')), 0, 10));
        $verifyUrl = url('/admin/agenda/export-pdf?tte=1&hash=' . $tteHash);

        return view('admin.print.agenda-schedule', [
            'agendas' => $agendas,
            'settings' => $settings,
            'printDate' => $printDate,
            'ketuaDkm' => $ketuaDkm,
            'ketuaAcara' => $ketuaAcara,
            'isTteSigned' => true,
            'tteHash' => $tteHash,
            'verifyUrl' => $verifyUrl,
        ]);
    }

    /**
     * Print single Agenda LPJ
     */
    public function printLpj(int $id)
    {
        $agenda = Agenda::findOrFail($id);
        $settings = MasjidSetting::first();
        $printDate = Carbon::now()->translatedFormat('d F Y');

        return view('admin.print.agenda-lpj', [
            'agenda' => $agenda,
            'settings' => $settings,
            'printDate' => $printDate,
        ]);
    }
}
