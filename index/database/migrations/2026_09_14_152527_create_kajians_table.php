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
        Schema::create('kajians', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pekanan', 'jumat', 'tematik'])->default('pekanan');
            $table->date('date');
            $table->string('time_display')->default('09:00 - 11:30');
            $table->string('title');
            
            // Kolom Kajian Pekanan
            $table->string('speaker_name')->nullable();
            $table->string('speaker_phone')->nullable();
            
            // Kolom Kajian Jumat
            $table->boolean('is_holiday_disabled')->default(false);
            $table->string('khatib_name')->nullable();
            $table->string('mc_name')->nullable();
            $table->string('muadzin_name')->nullable();
            $table->string('khatib_phone')->nullable();
            $table->text('mc_notes')->nullable();
            
            $table->string('status')->default('AKTIF');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kajians');
    }
};
