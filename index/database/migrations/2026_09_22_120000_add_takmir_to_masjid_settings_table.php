<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('masjid_settings', function (Blueprint $table) {
            $table->json('takmir_documents')->nullable()->after('bank_accounts');
            $table->json('takmir_structure')->nullable()->after('takmir_documents');
        });
    }

    public function down(): void
    {
        Schema::table('masjid_settings', function (Blueprint $table) {
            $table->dropColumn(['takmir_documents', 'takmir_structure']);
        });
    }
};
