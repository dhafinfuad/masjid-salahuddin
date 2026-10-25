<?php

namespace App\Http\Controllers;

use App\Models\MasjidSetting;
use App\Models\User;
use App\Services\QrCodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TakmirDocController extends Controller
{
    /**
     * Siapkan seluruh data resmi Surat Keputusan (KEP) Kepala KPP Madya Malang
     * tentang Perubahan Susunan Pengurus Takmir Masjid Salahuddin lengkap dengan
     * Lampiran I (Susunan Pengurus) dan Lampiran II (Tupoksi) serta QR Code BSrE.
     */
    protected function prepareOfficialSkData(Request $request): array
    {
        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();
        $struct = $settings->getTakmirStructure();

        $skNumber = $request->query('number') ?: ($docs['sk']['number'] ?? 'KEP-48/KPP.1209/2026');
        $skDate = $request->query('date') ?: ($docs['sk']['date'] ?? '14 April 2026');
        $skTitle = $request->query('title') ?: ($docs['sk']['title'] ?? 'Surat Keputusan Kepala KPP Madya Malang');
        $skDescription = $request->query('description') ?: ($docs['sk']['description'] ?? 'Perubahan Susunan Pengurus Takmir Masjid Sholahuddin KPP Madya Malang');
        
        // Ekstrak tahun/periode dari nomor KEP atau teks
        $periode = $request->query('periode') ?: '2026-2029';

        $tteHash = 'TTE-' . strtoupper(substr(md5('SK_TAKMIR_' . $skNumber . '_' . $periode), 0, 10));
        $verifyUrl = url('/admin/kepengurusan/cetak-sk?number=' . urlencode($skNumber) . '&periode=' . urlencode($periode) . '&hash=' . $tteHash);
        $qrCodeBase64 = QrCodeService::generateBase64($verifyUrl, 4);

        return [
            'settings' => $settings,
            'docs' => $docs,
            'struct' => $struct,
            'skNumber' => $skNumber,
            'skDate' => $skDate,
            'skTitle' => $skTitle,
            'skDescription' => $skDescription,
            'periode' => $periode,
            'tteHash' => $tteHash,
            'verifyUrl' => $verifyUrl,
            'qrCodeBase64' => $qrCodeBase64,
            'printDate' => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'),
        ];
    }

    /**
     * Tampilkan draf dokumen resmi Surat Keputusan (KEP) Kepala KPP Madya Malang (HTML View)
     */
    public function printOfficialSk(Request $request)
    {
        $data = $this->prepareOfficialSkData($request);
        return view('admin.print.sk-takmir-official', $data);
    }

    /**
     * Direct Server-Side Stream PDF Download for Official SK Takmir (No blank tab, instant download)
     */
    public function downloadOfficialSkPdf(Request $request): StreamedResponse
    {
        $settings = MasjidSetting::getActive();
        $docs = $settings->getTakmirDocuments();

        // Jika pengurus telah mengunggah berkas PDF resmi lengkap manual, utamakan berkas tersebut
        if (!empty($docs['sk_lengkap']['file'])) {
            $filePath = public_path('resources/' . $docs['sk_lengkap']['file']);
            if (file_exists($filePath)) {
                $cleanFilename = $docs['sk_lengkap']['file'];
                return response()->streamDownload(function () use ($filePath) {
                    readfile($filePath);
                }, $cleanFilename, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $cleanFilename . '"',
                ]);
            }
        }

        $data = $this->prepareOfficialSkData($request);
        $cleanFilename = 'Draf_Dokumen_Resmi_SK_Takmir_' . Str::slug($data['periode']) . '.pdf';

        $pdf = Pdf::loadView('pdf.sk-takmir-official-pdf', $data)
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
}
