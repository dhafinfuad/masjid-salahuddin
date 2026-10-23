<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\PrayerTimeService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kajians', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('time_display')->nullable()->change();
        });

        // Retroactive synchronization for existing kajians records
        $prayerService = app(PrayerTimeService::class);
        $kajians = DB::table('kajians')->get();

        foreach ($kajians as $k) {
            if ($k->type === 'jumat') {
                // Untuk Jumat: kosongkan title, sinkronkan time_display dengan Waktu Dzuhur + 20 menit
                $dzuhurTime = $prayerService->getDefaultKajianTime('jumat', $k->date);
                DB::table('kajians')->where('id', $k->id)->update([
                    'title' => null,
                    'time_display' => $dzuhurTime,
                ]);
            } elseif ($k->type === 'pekanan') {
                // Untuk Pekanan: sinkronkan time_display dengan Waktu Ashar + 20 menit
                $asharTime = $prayerService->getDefaultKajianTime('pekanan', $k->date);
                DB::table('kajians')->where('id', $k->id)->update([
                    'time_display' => $asharTime,
                ]);
            } elseif ($k->type === 'tematik') {
                // Untuk Tematik: kosongkan time_display
                DB::table('kajians')->where('id', $k->id)->update([
                    'time_display' => null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kajians', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->string('time_display')->default('09:00 - 11:30')->nullable(false)->change();
        });
    }
};
