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
        Schema::table('program_participants', function (Blueprint $table) {
            $table->foreignId('social_program_id')->nullable()->after('id')->constrained('social_programs')->nullOnDelete();
            $table->string('nip')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_participants', function (Blueprint $table) {
            $table->dropForeign(['social_program_id']);
            $table->dropColumn(['social_program_id', 'nip']);
        });
    }
};
