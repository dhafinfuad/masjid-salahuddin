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
        Schema::create('odoj_entries', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->default('Laporan Madya Malang Bertilawah');
            $table->string('jamaah_name');
            $table->unsignedTinyInteger('juz_number'); // 1 - 30
            $table->date('target_date');
            $table->string('status')->default('Belum'); // Belum, Selesai
            $table->timestamp('completed_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['target_date', 'juz_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odoj_entries');
    }
};
