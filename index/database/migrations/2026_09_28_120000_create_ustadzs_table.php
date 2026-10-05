<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ustadzs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('title')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        // Retroactive initial sync from kajians table
        // 1. Gather all distinct speaker_name from kajians (excluding null/empty or 'libur')
        $speakers = DB::table('kajians')
            ->whereNotNull('speaker_name')
            ->where('speaker_name', '!=', '')
            ->where('speaker_name', 'not like', '%libur%')
            ->select('speaker_name')
            ->distinct()
            ->get();

        foreach ($speakers as $s) {
            $name = trim($s->speaker_name);
            if (empty($name)) {
                continue;
            }

            // Find non-null photo if any exists in kajians for this speaker
            $photo = DB::table('kajians')
                ->where('speaker_name', $name)
                ->whereNotNull('speaker_photo')
                ->where('speaker_photo', '!=', '')
                ->value('speaker_photo');

            // Find phone if any exists in kajians for this speaker
            $phone = DB::table('kajians')
                ->where('speaker_name', $name)
                ->whereNotNull('speaker_phone')
                ->where('speaker_phone', '!=', '')
                ->value('speaker_phone');

            DB::table('ustadzs')->insert([
                'name' => $name,
                'title' => null,
                'phone' => $phone,
                'photo' => $photo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // If a photo was found for this speaker, synchronize it across ALL kajians with this speaker_name
            if ($photo) {
                DB::table('kajians')
                    ->where('speaker_name', $name)
                    ->update(['speaker_photo' => $photo]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ustadzs');
    }
};
