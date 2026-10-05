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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('speaker_name');
            $table->string('speaker_role')->nullable();
            $table->date('event_date');
            $table->string('time_display')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->default('Ruang Utama Masjid');
            $table->unsignedInteger('capacity')->default(100);
            $table->unsignedInteger('registered_count')->default(0);
            $table->enum('status', ['DRAF', 'TAYANG', 'BERJALAN', 'PENUH', 'SELESAI'])->default('DRAF');
            $table->string('banner_path')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'event_date', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
