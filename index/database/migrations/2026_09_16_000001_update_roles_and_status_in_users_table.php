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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('email');
            $table->string('status')->default('AKTIF')->after('phone');
        });

        // Ubah tipe role menjadi varchar fleksibel untuk menampung Master, Ketua, Sekretaris, Bendahara, Jamaah, serta legacy
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'Jamaah'");
        } else {
            // SQLite in-memory testing
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 50)->default('Jamaah')->change();
            });
        }

        // Migrasikan role legacy yang ada
        DB::table('users')->where('role', 'admin')->update(['role' => 'Master']);
        DB::table('users')->where('role', 'operator')->update(['role' => 'Sekretaris']);
        DB::table('users')->where('role', 'viewer')->update(['role' => 'Jamaah']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'operator', 'viewer') NOT NULL DEFAULT 'operator'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'status']);
        });
    }
};
