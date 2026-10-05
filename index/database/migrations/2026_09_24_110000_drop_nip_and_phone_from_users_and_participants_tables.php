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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'phone']);
        });

        Schema::table('program_participants', function (Blueprint $table) {
            $table->dropColumn(['nip', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('email');
            $table->string('phone')->nullable()->after('role');
        });

        Schema::table('program_participants', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('name');
            $table->string('phone')->nullable()->after('nip');
        });
    }
};
