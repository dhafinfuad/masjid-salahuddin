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
        Schema::create('finance_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('umum'); // pemasukan, pengeluaran, umum
            $table->string('color')->nullable()->default('emerald');
            $table->timestamps();
        });

        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('type'); // pemasukan, pengeluaran
            $table->foreignId('category_id')->nullable()->constrained('finance_categories')->nullOnDelete();
            $table->string('program_name')->nullable()->default('Kas Umum'); // Kas Umum, Santunan Anak Yatim, Infaq Rutin, Zakat Mal Rutin, Tabungan Qurban
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->string('receipt_path')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('program_participants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('program_name'); // Santunan Anak Yatim, Infaq Rutin, Zakat Mal Rutin, Tabungan Qurban
            $table->decimal('monthly_amount', 15, 2)->default(0);
            $table->string('period')->default('Bulanan'); // Bulanan, 2026-03, dll
            $table->string('status')->default('AKTIF'); // AKTIF, NONAKTIF
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_participants');
        Schema::dropIfExists('finances');
        Schema::dropIfExists('finance_categories');
    }
};
