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
        if (Schema::hasTable('kajians') && ! Schema::hasColumn('kajians', 'youtube_url')) {
            Schema::table('kajians', function (Blueprint $table) {
                $table->string('youtube_url', 500)->nullable()->after('notula');
            });
        }

        if (Schema::hasTable('agendas') && ! Schema::hasColumn('agendas', 'youtube_url')) {
            Schema::table('agendas', function (Blueprint $table) {
                $table->string('youtube_url', 500)->nullable()->after('report_summary');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kajians') && Schema::hasColumn('kajians', 'youtube_url')) {
            Schema::table('kajians', function (Blueprint $table) {
                $table->dropColumn('youtube_url');
            });
        }

        if (Schema::hasTable('agendas') && Schema::hasColumn('agendas', 'youtube_url')) {
            Schema::table('agendas', function (Blueprint $table) {
                $table->dropColumn('youtube_url');
            });
        }
    }
};
