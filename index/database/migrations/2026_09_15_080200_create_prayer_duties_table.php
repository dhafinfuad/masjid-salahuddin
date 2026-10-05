<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prayer_duties', function (Blueprint $table) {
            $table->id();
            $table->string('prayer_time', 20); // 'dzuhur' or 'ashar'
            $table->string('day_name', 20);    // 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'
            $table->string('week_pattern', 30)->default('semua'); // 'semua', 'pekan_1', 'pekan_2', 'pekan_3', 'pekan_4', 'pekan_5', 'pekan_1_3_5', 'pekan_2_4'
            $table->string('imam_name')->nullable();
            $table->string('muadzin_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['prayer_time', 'day_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_duties');
    }
};
