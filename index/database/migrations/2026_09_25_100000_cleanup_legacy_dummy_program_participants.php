<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('program_participants')) {
            // Delete any dummy records with 'Bulanan' or invalid period format
            DB::table('program_participants')
                ->where('period', 'Bulanan')
                ->orWhere('period', 'like', '% - %')
                ->orWhere('period', 'not like', 'Periode %/%')
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for cleaning up dummy rows
    }
};
