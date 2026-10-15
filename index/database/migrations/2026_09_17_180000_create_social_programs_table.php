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
        Schema::create('social_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('sosial'); // yatim, dhuafa, infaq, zakat, qurban, sosial
            $table->text('description')->nullable();
            $table->string('period_type')->default('bulanan'); // bulanan, tahunan, insidental
            $table->string('status')->default('AKTIF'); // AKTIF, SELESAI, DITUTUP
            $table->string('icon')->nullable()->default('heart-handshake');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_programs');
    }
};
