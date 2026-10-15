<?php

use App\Models\SocialProgram;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        SocialProgram::firstOrCreate(
            ['slug' => 'santunan-dhuafa'],
            [
                'name' => 'Program Santunan Dhuafa',
                'category' => 'dhuafa',
                'description' => 'Penyaluran bantuan santunan bagi kaum dhuafa yang diselenggarakan secara berkala oleh takmir KPP Madya Malang.',
                'period_type' => 'bulanan',
                'status' => 'AKTIF',
                'icon' => 'hand-heart',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        SocialProgram::where('slug', 'santunan-dhuafa')->delete();
    }
};
