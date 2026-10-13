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
        if (! Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->string('nip_pendek', 15)->nullable()->index(); // 9 digit NIP DJP/Kemenkeu
                $table->string('nip_panjang', 25)->nullable()->index(); // 18 digit NIP BKN
                $table->string('name')->index(); // Nama lengkap pegawai beserta gelar
                $table->string('jabatan')->nullable(); // Jabatan / Fungsional / Seksi
                $table->string('unit_kerja')->default('KPP Madya Malang');
                $table->enum('gender', ['L', 'P'])->nullable();
                $table->unsignedBigInteger('user_id')->nullable()->index(); // Relasi opsional ke akun pengguna
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
