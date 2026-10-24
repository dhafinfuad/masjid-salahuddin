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
        \App\Models\Kajian::syncDynamicTimesAndTitles(true);
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
