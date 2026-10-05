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
        Schema::create('masjid_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Masjid Salahuddin');
            $table->text('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->decimal('latitude', 10, 7)->default(-6.2088000);
            $table->decimal('longitude', 10, 7)->default(106.8456000);
            $table->decimal('qibla_angle', 5, 2)->default(295.12);
            $table->string('calculation_method', 50)->default('KEMENAG');
            $table->tinyInteger('subuh_offset')->default(2);
            $table->tinyInteger('dzuhur_offset')->default(2);
            $table->tinyInteger('ashar_offset')->default(2);
            $table->tinyInteger('maghrib_offset')->default(2);
            $table->tinyInteger('isya_offset')->default(2);
            $table->tinyInteger('iqamah_delay_minutes')->default(10);
            $table->json('tv_announcements')->nullable();
            $table->json('friday_prayer_info')->nullable();
            $table->json('bank_accounts')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masjid_settings');
    }
};
