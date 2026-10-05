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
        Schema::table('kajians', function (Blueprint $table) {
            $table->longText('notula')->nullable()->after('speaker_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kajians', function (Blueprint $table) {
            $table->dropColumn('notula');
        });
    }
};
