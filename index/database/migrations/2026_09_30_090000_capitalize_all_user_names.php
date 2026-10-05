<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            $users = DB::table('users')->select('id', 'name')->get();
            foreach ($users as $u) {
                if (!empty($u->name)) {
                    $capitalized = Str::title(trim($u->name));
                    if ($capitalized !== $u->name) {
                        DB::table('users')->where('id', $u->id)->update(['name' => $capitalized]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fail-safe jika tabel belum terbentuk atau migration dijalankan pada context terisolasi
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu reversal
    }
};
