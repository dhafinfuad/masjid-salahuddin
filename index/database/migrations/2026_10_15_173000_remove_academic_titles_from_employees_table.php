<?php

use App\Services\EmployeeStatusSyncService;
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
            $employees = DB::table('employees')->select('id', 'name')->get();
            foreach ($employees as $emp) {
                if (! empty($emp->name)) {
                    $cleaned = EmployeeStatusSyncService::stripAcademicTitles($emp->name);
                    $formatted = Str::title(strtolower(trim($cleaned)));
                    if ($formatted !== $emp->name) {
                        DB::table('employees')->where('id', $emp->id)->update(['name' => $formatted]);
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
