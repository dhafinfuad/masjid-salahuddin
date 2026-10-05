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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable(); // Tujuan / deskripsi agenda
            $table->date('event_date');
            $table->text('committee_members')->nullable(); // Daftar panitia (newline-separated or text)
            $table->decimal('budget', 15, 2)->default(0); // Anggaran kegiatan (Rp)
            $table->string('status')->default('Direncanakan'); // Direncanakan, Berjalan, SELESAI
            $table->longText('report_summary')->nullable(); // Draft laporan / ringkasan LPJ
            $table->string('report_pdf_path')->nullable(); // Berkas PDF LPJ jika diunggah
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
