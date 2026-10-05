<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kajians MODIFY COLUMN type ENUM('pekanan', 'jumat', 'tematik') NOT NULL DEFAULT 'pekanan'");
        } else {
            Schema::table('kajians', function (Blueprint $table) {
                $table->string('type', 20)->default('pekanan')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kajians MODIFY COLUMN type ENUM('pekanan', 'jumat') NOT NULL DEFAULT 'pekanan'");
        } else {
            Schema::table('kajians', function (Blueprint $table) {
                $table->string('type', 20)->default('pekanan')->change();
            });
        }
    }
};
