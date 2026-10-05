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
        if (!Schema::hasColumn('finances', 'receipt_paths')) {
            Schema::table('finances', function (Blueprint $table) {
                $table->json('receipt_paths')->nullable()->after('receipt_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('finances', 'receipt_paths')) {
            Schema::table('finances', function (Blueprint $table) {
                $table->dropColumn('receipt_paths');
            });
        }
    }
};
