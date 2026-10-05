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
        Schema::table('finance_categories', function (Blueprint $table) {
            $table->string('group')->default('pengeluaran_nonrutin')->after('type'); // penerimaan, pengeluaran_rutin, pengeluaran_nonrutin
        });

        Schema::table('finances', function (Blueprint $table) {
            $table->foreignId('agenda_id')->nullable()->after('category_id')->constrained('agendas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropForeign(['agenda_id']);
            $table->dropColumn('agenda_id');
        });

        Schema::table('finance_categories', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};
