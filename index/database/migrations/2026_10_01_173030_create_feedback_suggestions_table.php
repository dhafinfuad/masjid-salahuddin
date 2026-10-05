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
        Schema::create('feedback_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('contact')->nullable();
            $table->string('category')->default('Fasilitas & Kebersihan');
            $table->string('title')->nullable();
            $table->text('message');
            $table->string('status', 30)->default('baru'); // baru, dibaca, ditindaklanjuti, arsip
            $table->boolean('is_public')->default(false);
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['is_public', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_suggestions');
    }
};
