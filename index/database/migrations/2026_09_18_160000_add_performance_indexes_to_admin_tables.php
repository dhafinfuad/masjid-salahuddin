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
        if (!Schema::hasIndex('kajians', ['type', 'date'])) {
            Schema::table('kajians', function (Blueprint $table) {
                $table->index(['type', 'date']);
            });
        }
        if (Schema::hasColumn('kajians', 'status') && !Schema::hasIndex('kajians', ['status'])) {
            Schema::table('kajians', function (Blueprint $table) {
                $table->index('status');
            });
        }

        if (!Schema::hasIndex('finances', ['type', 'transaction_date'])) {
            Schema::table('finances', function (Blueprint $table) {
                $table->index(['type', 'transaction_date']);
            });
        }
        if (!Schema::hasIndex('finances', ['category_id'])) {
            Schema::table('finances', function (Blueprint $table) {
                $table->index('category_id');
            });
        }

        if (!Schema::hasIndex('agendas', ['status', 'event_date'])) {
            Schema::table('agendas', function (Blueprint $table) {
                $table->index(['status', 'event_date']);
            });
        }

        if (!Schema::hasIndex('odoj_entries', ['target_date', 'status'])) {
            Schema::table('odoj_entries', function (Blueprint $table) {
                $table->index(['target_date', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('kajians', ['type', 'date'])) {
            Schema::table('kajians', function (Blueprint $table) {
                $table->dropIndex(['type', 'date']);
            });
        }
        if (Schema::hasColumn('kajians', 'status') && Schema::hasIndex('kajians', ['status'])) {
            Schema::table('kajians', function (Blueprint $table) {
                $table->dropIndex(['status']);
            });
        }

        if (Schema::hasIndex('finances', ['type', 'transaction_date'])) {
            Schema::table('finances', function (Blueprint $table) {
                $table->dropIndex(['type', 'transaction_date']);
            });
        }
        if (Schema::hasIndex('finances', ['category_id'])) {
            Schema::table('finances', function (Blueprint $table) {
                $table->dropIndex(['category_id']);
            });
        }

        if (Schema::hasIndex('agendas', ['status', 'event_date'])) {
            Schema::table('agendas', function (Blueprint $table) {
                $table->dropIndex(['status', 'event_date']);
            });
        }

        if (Schema::hasIndex('odoj_entries', ['target_date', 'status'])) {
            Schema::table('odoj_entries', function (Blueprint $table) {
                $table->dropIndex(['target_date', 'status']);
            });
        }
    }
};
