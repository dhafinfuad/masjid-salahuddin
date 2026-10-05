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
        Schema::create('poster_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('default');
            $table->tinyInteger('template_type')->default(1)->comment('1: Kajian Tematik, 2: Kajian Rutin Pekanan');
            $table->json('config');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poster_settings');
    }
};
