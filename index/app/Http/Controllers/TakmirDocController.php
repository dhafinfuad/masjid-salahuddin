<?php

namespace App\Http\Controllers;

use App\Models\MasjidSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TakmirDocController extends Controller
{
    /**
     * Tampilkan draf dokumen resmi Surat Keputusan (KEP) Kepala KPP Madya Malang
     * tentang Perubahan Susunan Pengurus Takmir Masjid Salahuddin lengkap dengan
     * Lampiran I (Susunan Pengurus) dan Lampiran II (Tupoksi).
     */
    public function printOfficialSk(Request $request)
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

        return view('admin.print.sk-takmir-official', [
            'settings' => $settings,
            'docs' => $docs,
            'struct' => $struct,
            'skNumber' => $skNumber,
            'skDate' => $skDate,
            'skTitle' => $skTitle,
            'skDescription' => $skDescription,
            'periode' => $periode,
            'printDate' => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'),
        ]);
    }
}
