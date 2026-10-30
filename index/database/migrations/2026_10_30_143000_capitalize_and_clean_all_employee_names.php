<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Artisan::call('masjid:clean-employee-names');
        } catch (\Throwable $e) {
            // Fail-safe jika context migration terisolasi
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way data standardization, no reversal needed
    }
};
