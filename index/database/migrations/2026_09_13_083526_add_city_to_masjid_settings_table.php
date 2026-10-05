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
        Schema::table('masjid_settings', function (Blueprint $table) {
            $table->string('city_id', 20)->default('1634')->after('calculation_method');
            $table->string('city_name', 100)->default('KOTA MALANG')->after('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('masjid_settings', function (Blueprint $table) {
            $table->dropColumn(['city_id', 'city_name']);
        });
    }
};
