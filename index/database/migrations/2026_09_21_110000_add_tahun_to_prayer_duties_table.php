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
        Schema::table('prayer_duties', function (Blueprint $table) {
            $table->unsignedSmallInteger('tahun')->nullable()->default(2026)->after('week_pattern');
            $table->index('tahun');
        });

        // Set existing records to 2026
        DB::table('prayer_duties')->whereNull('tahun')->update(['tahun' => 2026]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prayer_duties', function (Blueprint $table) {
            $table->dropIndex(['tahun']);
            $table->dropColumn('tahun');
        });
    }
};
