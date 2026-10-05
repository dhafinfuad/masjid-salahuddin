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
        Schema::create('registrants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('ticket_code', 50)->unique();
            $table->string('full_name', 150);
            $table->string('whatsapp', 30);
            $table->string('email', 150)->nullable();
            $table->enum('gender', ['ikhwan', 'akhwat'])->default('ikhwan');
            $table->enum('status', ['MENUNGGU', 'TERKONFIRMASI', 'HADIR', 'DIBATALKAN'])->default('TERKONFIRMASI');
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['event_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrants');
    }
};
