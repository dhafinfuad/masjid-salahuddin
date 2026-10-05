<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\Category;
use App\Models\Event;
use App\Models\Finance;
use App\Models\Kajian;
use App\Models\Material;
use App\Models\OdojEntry;
use App\Models\ProgramParticipant;
use App\Models\Registrant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'masjid:clean-dummy {--force : Eksekusi tanpa konfirmasi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan seluruh data dummy bawaan seeder (events, registrants, materials, odoj_entries, agenda dummy, user seed)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== MEMULAI PEMBERSIHAN DATA DUMMY BAWAAN SEEDER ===");

        DB::beginTransaction();

        try {
            // 1. Bersihkan Registrants, Materials, dan Events prototype
            $regCount = Registrant::count();
            Registrant::query()->forceDelete();
            $this->line("✔ Berhasil menghapus {$regCount} data dummy registrants.");

            $matCount = Material::count();
            Material::query()->delete();
            $this->line("✔ Berhasil menghapus {$matCount} data dummy materials.");

            $evCount = Event::count();
            Event::query()->forceDelete();
            $this->line("✔ Berhasil menghapus {$evCount} data dummy events prototype.");

            // 2. Bersihkan ODOJ Entries prototype
            $odojCount = OdojEntry::count();
            OdojEntry::query()->delete();
            $this->line("✔ Berhasil menghapus {$odojCount} data dummy ODOJ entries.");

            // 3. Bersihkan Agenda dummy (Maulid seeder)
            $dummyAgendas = Agenda::where('title', 'like', '%Maulid%')->get();
            $agendaDeleted = 0;
            foreach ($dummyAgendas as $a) {
                $a->delete();
                $agendaDeleted++;
            }
            $this->line("✔ Berhasil menghapus {$agendaDeleted} agenda dummy (hanya menyisakan agenda riil Ramadhan & Iduladha).");

            // 4. Pengalihan relasi keuangan & session sebelum menghapus dummy users
            $realKetua = User::where('email', 'moh.nazil.kusmawan@pajak.go.id')->first();
            $realBendahara = User::where('email', 'aris.setianto@pajak.go.id')->first();
            $realMaster = User::where('email', 'rizki.afandi.fajar@gmail.com')->first();

            $reassignedFinance = Finance::whereIn('recorded_by', [1, 2, 3, 4, 5])
                ->update(['recorded_by' => $realBendahara?->id ?: $realKetua?->id]);
            if ($reassignedFinance > 0) {
                $this->line("✔ Relasi recorded_by pada {$reassignedFinance} transaksi keuangan dialihkan ke Bendahara resmi ({$realBendahara?->name}).");
            }

            // Alihkan session aktif agar user di browser tidak logout
            if ($realKetua) {
                DB::table('sessions')->where('user_id', 1)->update(['user_id' => $realKetua->id]);
            }
            if ($realMaster) {
                DB::table('sessions')->where('user_id', 4)->update(['user_id' => $realMaster->id]);
            }
            DB::table('sessions')->whereIn('user_id', [2, 3, 5])->delete();

            // 5. Hapus 5 user seed dummy
            $dummyUsers = User::where('email', 'like', '%@masjidsalahuddin.id')->get();
            $userDeleted = 0;
            foreach ($dummyUsers as $u) {
                $u->delete();
                $userDeleted++;
            }
            $this->line("✔ Berhasil menghapus {$userDeleted} akun pengguna seed dummy (@masjidsalahuddin.id).");

            // 6. Hapus kategori duplikat jika ada
            $dupCat = Category::where('slug', 'tahsin')->first();
            if ($dupCat && Category::where('slug', 'tahsin-al-quran')->exists()) {
                $dupCat->forceDelete();
                $this->line("✔ Membersihkan 1 duplikat kategori (Tahsin Quran).");
            }

            // 7. Bersihkan jadwal Kajian dummy yang bukan dari file backup riil
            $dummyKajianTitles = [
                'Kajian Rutin Tafsir Ayat Ahkam',
                'Kajian Akbar Peringatan Isra Miraj 1448 H',
                'Kajian Tahun Baru Hijriah 1448 H',
            ];
            $deletedKajian = Kajian::whereIn('title', $dummyKajianTitles)
                ->orWhere('speaker_name', 'Prof. Dr. KH. Nasaruddin Umar')
                ->delete();
            if ($deletedKajian > 0) {
                $this->line("✔ Berhasil menghapus {$deletedKajian} data dummy kajian (Isra Miraj, Tahun Baru Hijriah, Tafsir Ayat Ahkam).");
            }

            // 8. Bersihkan transaksi keuangan uji coba / dummy
            $deletedFinance = Finance::where('description', 'like', '%tes tes%')
                ->orWhere('description', 'like', '%qris qris%')
                ->delete();
            if ($deletedFinance > 0) {
                $this->line("✔ Berhasil menghapus {$deletedFinance} transaksi keuangan dummy/tes.");
            }

            // 9. Bersihkan peserta program sosial uji coba / dummy
            $deletedParticipants = ProgramParticipant::whereIn('name', ['FULAN TES', 'Test Jamaah Participant'])
                ->orWhere('name', 'like', '%TES%')
                ->delete();
            if ($deletedParticipants > 0) {
                $this->line("✔ Berhasil menghapus {$deletedParticipants} peserta program sosial dummy.");
            }

            DB::commit();

            $this->info("\n[SUKSES] Seluruh data dummy telah dibersihkan dari database!");
            $this->comment("Sekarang seluruh data di database adalah 100% data asli Masjid Salahuddin.");
            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Gagal membersihkan data dummy: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
