<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Finance;
use App\Models\FinanceCategory;
use App\Models\MasjidSetting;
use App\Models\User;
use App\Services\TteSignatureService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceExportController extends Controller
{
    /**
     * Export Finance records as Excel / CSV
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $fileName = 'Laporan_Kas_Masjid_Salahuddin_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it with proper formatting
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'No',
                'Tanggal',
                'Tipe Transaksi',
                'Kelompok Pos',
                'Kategori Kas',
                'Agenda Terkait',
                'Uraian / Keterangan',
                'Pemasukan (Rp)',
                'Pengeluaran (Rp)',
            ]);

            $finances = Finance::with(['category', 'agenda'])->orderBy('transaction_date', 'asc')->get();
            foreach ($finances as $index => $f) {
                fputcsv($handle, [
                    $index + 1,
                    $f->transaction_date ? $f->transaction_date->format('Y-m-d') : '-',
                    ucfirst($f->type),
                    $f->category?->group_label ?? 'Kas Umum',
                    $f->category?->name ?? 'Kas Umum',
                    $f->agenda?->title ?? $f->program_name ?? 'Kas Umum',
                    $f->description,
                    $f->type === 'pemasukan' ? $f->amount : 0,
                    $f->type === 'pengeluaran' ? $f->amount : 0,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Printable Clean PDF View for Financial Report
     * Supports both Monthly Routine Cash Flow and Thematic Agenda LPJ
     */
    public function printPdf(Request $request)
    {
        $settings = MasjidSetting::getActive();
        $reportType = $request->query('type', 'monthly'); // 'monthly' | 'agenda'

        // Ambil data penandatangan resmi dari data users
        $ketuaDkm = User::where('role', 'Ketua')->where('status', 'AKTIF')->first()
            ?? User::where('role', 'like', '%Ketua%')->first();
        $bendaharaDkm = User::where('role', 'Bendahara')->where('status', 'AKTIF')->first()
            ?? User::where('role', 'like', '%Bendahara%')->first();

        if ($reportType === 'agenda') {
            $agendaId = $request->query('agenda_id');
            $agenda = Agenda::findOrFail($agendaId);
            $periodKey = TteSignatureService::normalizeKey('agenda', $agendaId);
            $isTteSigned = $request->has('tte')
                ? ($request->query('tte') === '1')
                : TteSignatureService::isSigned($periodKey);
            $tteRecord = TteSignatureService::get($periodKey);
            $customTteHash = $tteRecord['tte_hash'] ?? $request->query('hash');

            $finances = Finance::with('category')
                ->where('agenda_id', $agenda->id)
                ->orderBy('transaction_date', 'asc')
                ->get();

            $penerimaan = $finances->where('type', 'pemasukan');
            $pengeluaran = $finances->where('type', 'pengeluaran');

            $totalPenerimaan = (float) $penerimaan->sum('amount');
            $totalPengeluaran = (float) $pengeluaran->sum('amount');
            $saldoKegiatan = $totalPenerimaan - $totalPengeluaran;
            $silpa = (float) $agenda->budget - $totalPengeluaran;

            return view('admin.print.finance-report', [
                'settings' => $settings,
                'reportType' => 'agenda',
                'agenda' => $agenda,
                'finances' => $finances,
                'penerimaan' => $penerimaan,
                'pengeluaran' => $pengeluaran,
                'totalPenerimaan' => $totalPenerimaan,
                'totalPengeluaran' => $totalPengeluaran,
                'saldoKegiatan' => $saldoKegiatan,
                'silpa' => $silpa,
                'isTteSigned' => $isTteSigned,
                'customTteHash' => $customTteHash,
                'ketuaDkm' => $ketuaDkm,
                'bendaharaDkm' => $bendaharaDkm,
            ]);
        }

        // Mode 1: Monthly Cash Flow (Continuous Carry-Over Balance)
        $month = $request->query('month', date('n'));
        $year = $request->query('year', date('Y'));
        $periodKey = TteSignatureService::normalizeKey('monthly', $year, $month);
        $isTteSigned = $request->has('tte')
            ? ($request->query('tte') === '1')
            : TteSignatureService::isSigned($periodKey);
        $tteRecord = TteSignatureService::get($periodKey);
        $customTteHash = $tteRecord['tte_hash'] ?? $request->query('hash');

        $monthName = Carbon::createFromDate((int)($year === 'all' ? date('Y') : $year), (int)($month === 'all' ? 1 : $month), 1)->translatedFormat('F');
        $periodLabel = ($month !== 'all' && $year !== 'all')
            ? "Bulan {$monthName} {$year}"
            : (($year !== 'all') ? "Tahun {$year}" : "Keseluruhan Periode");

        // Calculate continuous carry-over balance (Saldo Awal)
        if ($month !== 'all' && $year !== 'all' && is_numeric($month) && is_numeric($year)) {
            $startDate = Carbon::createFromDate((int)$year, (int)$month, 1)->startOfDay();
            $priorIncome = (float) Finance::pemasukan()->where('transaction_date', '<', $startDate)->sum('amount');
            $priorExpense = (float) Finance::pengeluaran()->where('transaction_date', '<', $startDate)->sum('amount');
            $saldoAwal = $priorIncome - $priorExpense;
        } else {
            $saldoAwal = 0;
        }

        $query = Finance::with(['category', 'agenda'])->orderBy('transaction_date', 'asc');
        if ($month && $month !== 'all') {
            $query->whereMonth('transaction_date', (int)$month);
        }
        if ($year && $year !== 'all') {
            $query->whereYear('transaction_date', (int)$year);
        }
        $finances = $query->get();

        $penerimaan = $finances->where('type', 'pemasukan');
        $pengeluaranRutin = $finances->where('type', 'pengeluaran')->filter(function ($f) {
            return $f->category?->group === 'pengeluaran_rutin';
        });
        $pengeluaranNonRutin = $finances->where('type', 'pengeluaran')->filter(function ($f) {
            return $f->category?->group !== 'pengeluaran_rutin';
        });

        // Grouping & SUM Pos Anggaran yang sama agar laporan rapi & tidak memanjang
        $penerimaanGrouped = $penerimaan->groupBy(function ($item) {
            return $item->category?->name ?? 'Infaq Umum';
        })->map(function ($items, $name) {
            return (object) [
                'name' => $name,
                'amount' => (float) $items->sum('amount'),
            ];
        })->values();

        $pengeluaranRutinGrouped = $pengeluaranRutin->groupBy(function ($item) {
            return $item->category?->name ?? 'Belanja Rutin';
        })->map(function ($items, $name) {
            return (object) [
                'name' => $name,
                'amount' => (float) $items->sum('amount'),
            ];
        })->values();

        $pengeluaranNonRutinGrouped = $pengeluaranNonRutin->groupBy(function ($item) {
            return $item->category?->name ?? 'Belanja Non-Rutin';
        })->map(function ($items, $name) {
            return (object) [
                'name' => $name,
                'amount' => (float) $items->sum('amount'),
            ];
        })->values();

        $subtotalPenerimaan = (float) $penerimaan->sum('amount');
        $totalKasTersedia = $saldoAwal + $subtotalPenerimaan;
        $subtotalRutin = (float) $pengeluaranRutin->sum('amount');
        $subtotalNonRutin = (float) $pengeluaranNonRutin->sum('amount');
        $totalPengeluaran = $subtotalRutin + $subtotalNonRutin;
        $saldoAkhir = $totalKasTersedia - $totalPengeluaran;

        return view('admin.print.finance-report', [
            'settings' => $settings,
            'reportType' => 'monthly',
            'periodLabel' => $periodLabel,
            'month' => $month,
            'year' => $year,
            'finances' => $finances,
            'penerimaan' => $penerimaan,
            'pengeluaranRutin' => $pengeluaranRutin,
            'pengeluaranNonRutin' => $pengeluaranNonRutin,
            'penerimaanGrouped' => $penerimaanGrouped,
            'pengeluaranRutinGrouped' => $pengeluaranRutinGrouped,
            'pengeluaranNonRutinGrouped' => $pengeluaranNonRutinGrouped,
            'saldoAwal' => $saldoAwal,
            'subtotalPenerimaan' => $subtotalPenerimaan,
            'totalKasTersedia' => $totalKasTersedia,
            'subtotalRutin' => $subtotalRutin,
            'subtotalNonRutin' => $subtotalNonRutin,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoAkhir' => $saldoAkhir,
            'isTteSigned' => $isTteSigned,
            'customTteHash' => $customTteHash,
            'ketuaDkm' => $ketuaDkm,
            'bendaharaDkm' => $bendaharaDkm,
        ]);
    }
}

