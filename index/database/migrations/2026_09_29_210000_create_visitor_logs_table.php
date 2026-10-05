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
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('url', 255)->index();
            $table->string('method', 10)->default('GET');
            $table->string('device_type', 20)->default('desktop')->index();
            $table->string('platform', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('visited_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
