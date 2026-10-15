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
        Schema::table('social_programs', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('social_programs', 'target_amount')) {
                $columnsToDrop[] = 'target_amount';
            }
            if (Schema::hasColumn('social_programs', 'color')) {
                $columnsToDrop[] = 'color';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_programs', function (Blueprint $table) {
            if (!Schema::hasColumn('social_programs', 'target_amount')) {
                $table->decimal('target_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('social_programs', 'color')) {
                $table->string('color')->nullable()->default('emerald');
            }
        });
    }
};
