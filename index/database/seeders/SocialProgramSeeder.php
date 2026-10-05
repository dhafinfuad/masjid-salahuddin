<?php

namespace Database\Seeders;

use App\Models\ProgramParticipant;
use App\Models\SocialProgram;
use Illuminate\Database\Seeder;

class SocialProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Program Santunan Anak Yatim',
                'slug' => 'santunan-anak-yatim',
                'category' => 'yatim',
                'description' => 'Penyaluran Program Santunan Anak yatim ni dilakukan pada saat bulan ramadhan dan waktu lainnya yang akan diinfokan pelaksanaan kegiatannya oleh takmir KPP Madya Malang.',
                'target_amount' => 10000000,
                'period_type' => 'bulanan',
                'status' => 'AKTIF',
                'icon' => 'heart-handshake',
                'color' => 'emerald',
            ],
            [
                'name' => 'Infaq Rutin',
                'slug' => 'infaq-rutin',
                'category' => 'infaq',
                'description' => 'Dana infaq yang Anda salurkan diantaranya digunakan untuk kafalah pemateri kajian, khotib dan konsumsinya, kegiatan Jumat berkah, alat kebersihan, serta berbagai kebutuhan operasional masjid lainnya.',
                'target_amount' => 25000000,
                'period_type' => 'bulanan',
                'status' => 'AKTIF',
                'icon' => 'wallet',
                'color' => 'blue',
            ],
            [
                'name' => 'Program Zakat Mal Rutin',
                'slug' => 'zakat-mal-rutin',
                'category' => 'zakat',
                'description' => 'Program Zakat Mal disalurkan pada bulan Ramadhan dan waktu-waktu tertentu lainnya. Jadwal pelaksanaannya akan diinformasikan lebih lanjut oleh takmir KPP Madya Malang.',
                'target_amount' => 15000000,
                'period_type' => 'bulanan',
                'status' => 'AKTIF',
                'icon' => 'coins',
                'color' => 'amber',
            ],
            [
                'name' => 'Tabungan Qurban',
                'slug' => 'tabungan-qurban',
                'category' => 'qurban',
                'description' => 'Tabungan Qurban membantu Anda mempersiapkan qurban secara fleksibel. Dana yang terkumpul tidak mengikat, sehingga dapat digunakan untuk berqurban di KPP Madya Malang maupun tempat lainnya.',
                'target_amount' => 50000000,
                'period_type' => 'tahunan',
                'status' => 'AKTIF',
                'icon' => 'sparkles',
                'color' => 'teal',
            ],
        ];

        foreach ($programs as $prog) {
            $programModel = SocialProgram::updateOrCreate(
                ['slug' => $prog['slug']],
                $prog
            );

            // Connect existing participants to this program
            ProgramParticipant::where(function ($q) use ($prog, $programModel) {
                $q->where('program_name', $prog['name'])
                  ->orWhere('program_name', str_replace('Program ', '', $prog['name']))
                  ->orWhere('program_name', 'Program ' . $prog['name'])
                  ->orWhere('social_program_id', $programModel->id);
            })->update([
                'social_program_id' => $programModel->id,
                'program_name' => $programModel->name,
            ]);
        }
    }
}
