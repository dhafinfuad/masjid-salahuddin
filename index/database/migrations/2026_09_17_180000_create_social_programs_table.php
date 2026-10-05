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
            $table->string('category')->default('sosial'); // yatim, infaq, zakat, qurban, sosial
            $table->text('description')->nullable();
            $table->decimal('target_amount', 15, 2)->default(0);
            $table->string('period_type')->default('bulanan'); // bulanan, tahunan, insidental
            $table->string('status')->default('AKTIF'); // AKTIF, SELESAI, DITUTUP
            $table->string('icon')->nullable()->default('heart-handshake');
            $table->string('color')->nullable()->default('emerald');
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
