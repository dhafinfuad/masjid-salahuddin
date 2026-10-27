<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\User;
use App\Services\NotulaAiService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotulaTranscriptAiTest extends TestCase
{
    use RefreshDatabase;

    protected User $masterUser;
    protected User $regularUser;
    protected Kajian $sampleKajian;

    protected function setUp(): void
    {
        parent::setUp();

        MasjidSetting::getActive();

        $this->masterUser = User::factory()->create([
            'name' => 'Master DKM',
            'email' => 'master@masjidsalahuddin.id',
            'role' => 'Master',
        ]);

        $this->regularUser = User::factory()->create([
            'name' => 'Jamaah Biasa',
            'email' => 'jamaah@masjidsalahuddin.id',
            'role' => 'Jamaah',
        ]);

        $this->sampleKajian = Kajian::create([
            'type' => 'tematik',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'time_display' => '09:00 - 11:30',
            'title' => 'Memahami Makna Keselamatan dan Zikir',
            'speaker_name' => 'Ustadz Pemateri',
            'speaker_phone' => '081234567890',
        ]);
    }

    /** @test */
    public function test_notula_ai_service_cleans_youtube_headers_correctly()
    {
        $service = new NotulaAiService();

        $rawTranscript = <<<TEXT
Transkrip
Telusuri transkrip
Telusuri transkrip

0.000 detikredaksi zikir wis gelem zikir alhamdulillah wong-wong maka Allah berfirman fkuruni
0.080 detikazkurum ingatlah kalian kepadaku niscaya aku akan mengingat kalian
TEXT;

        $cleaned = $service->cleanRawTranscript($rawTranscript);

        $this->assertStringNotContainsString('Transkrip', $cleaned);
        $this->assertStringNotContainsString('Telusuri transkrip', $cleaned);
        $this->assertStringContainsString('0.000 detikredaksi zikir', $cleaned);
    }

    /** @test */
    public function test_notula_ai_service_rejects_too_short_transcript()
    {
        $service = new NotulaAiService();

        $this->expectException(\InvalidArgumentException::class);
        $service->generateFromTranscript('Teks pendek');
    }

    /** @test */
    public function test_notula_ai_service_throws_exception_when_api_key_missing()
    {
        config(['services.gemini.key' => null]);
        putenv('GEMINI_API_KEY=');

        $service = new NotulaAiService();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Google Gemini API Key belum terkonfigurasi');

        $longTranscript = str_repeat("Ini adalah baris transkrip panjang yang melebihi lima puluh karakter.\n", 5);
        $service->generateFromTranscript($longTranscript);
    }

    /** @test */
    public function test_master_user_can_transform_transcript_with_ai()
    {
        $this->actingAs($this->masterUser);

        $rawTranscript = <<<TEXT
Transkrip
0.000 detikredaksi zikir wis gelem zikir alhamdulillah wong-wong maka Allah berfirman fkuruni
0.080 detikazkurum ingatlah kalian kepadaku niscaya aku akan mengingat kalian sekarang saya
0.160 detikterjemahkan dulu lafaz Allahumma antasalam ya Allah engkau adalah zat
0.230 detikyang maha suci dari segala sifat kekurang bersifat dengan segala sifat kesempurnaan
TEXT;

        $mockOutput = <<<MARKDOWN
KAJIAN MEMAHAMI MAKNA KESELAMATAN DAN ZIKIR
Hari, Tanggal : Senin, 27 Oktober 2026
Tempat : Masjid Salahuddin KPP Madya Malang
Pemateri : Ustadz Pemateri
Tema / Kitab : Memahami Makna Keselamatan dan Zikir
Notulis : Tim Media & Notula Masjid Salahuddin

📌 RINGKASAN EKSEKUTIF (EXECUTIVE SUMMARY)
Kajian ini membahas hakikat keselamatan dalam perspektif syariat serta urgensi zikir dalam kehidupan seorang muslim.

Zikir dan doa keselamatan menjadi sarana mendekatkan diri kepada Allah secara istiqamah dan berkesinambungan.

📑 DAFTAR ISI PEMBAHASAN
Urgensi Zikir Pasca Salat
Makna Doa Keselamatan (As-Salam)

📖 PEMBAHASAN LENGKAP
1. Urgensi Zikir Pasca Salat
Zikir merupakan perintah langsung dari Allah SWT untuk senantiasa mengingat-Nya dalam segala situasi.
> فَاذْكُرُونِي أَذْكُرْكُمْ
> *"Ingatlah kalian kepada-Ku, niscaya Aku akan mengingat kalian."* (QS. Al-Baqarah: 152)

2. Makna Doa Keselamatan (As-Salam)
Keselamatan sejati bermula dari ketundukan hati dan ketakwaan kepada Allah SWT.

💡 KESIMPULAN & PESAN AKSI
Membiasakan Zikir: Mengamalkan wirid dan zikir secara teratur setelah shalat fardhu.
Ketulusan Doa: Menyelaraskan ucapan lisan dengan komitmen perbuatan sehari-hari.
MARKDOWN;

        $mockAiService = $this->createMock(NotulaAiService::class);
        $mockAiService->method('isAvailable')->willReturn(true);
        $mockAiService->method('generateFromTranscript')->willReturn($mockOutput);

        $this->app->instance(NotulaAiService::class, $mockAiService);

        Livewire::test(AdminDashboard::class)
            ->call('transformTranscriptToNotula', $this->sampleKajian->id, $rawTranscript)
            ->assertSet('notulaRawText', $mockOutput)
            ->assertDispatched('toast', fn ($event, $params) => str_contains($params['message'] ?? '', 'berhasil ditransformasikan'))
            ->assertDispatched('notula-transformed', function ($event, $params) use ($mockOutput) {
                $data = $params[0] ?? $params['data'] ?? $params;
                return isset($data['notula']) && $data['notula'] === $mockOutput;
            });
    }

    /** @test */
    public function test_non_master_user_cannot_transform_transcript()
    {
        $this->actingAs($this->regularUser);

        Livewire::test(AdminDashboard::class)
            ->call('transformTranscriptToNotula', $this->sampleKajian->id, 'Contoh transkrip...')
            ->assertDispatched('toast', fn ($event, $params) => str_contains($params['message'] ?? '', 'Akses ditolak'));
    }

    /** @test */
    public function test_empty_transcript_fails_validation()
    {
        $this->actingAs($this->masterUser);

        Livewire::test(AdminDashboard::class)
            ->call('transformTranscriptToNotula', $this->sampleKajian->id, '   ')
            ->assertDispatched('toast', fn ($event, $params) => str_contains($params['message'] ?? '', 'tidak boleh kosong'));
    }

    /** @test */
    public function test_notula_ai_service_rejects_too_short_notula_for_reformat()
    {
        $service = new NotulaAiService();

        $this->expectException(\InvalidArgumentException::class);
        $service->reformatExistingNotula('Teks pendek');
    }

    /** @test */
    public function test_reformat_notulas_artisan_command_updates_database()
    {
        $oldNotula = str_repeat("Ini adalah baris naskah notula lama yang panjang sekali.\n", 5);
        $kajian = Kajian::create([
            'type' => 'tematik',
            'date' => '2026-10-31',
            'title' => 'Kajian Lama',
            'notula' => $oldNotula,
        ]);

        $reformattedText = <<<MARKDOWN
KAJIAN LAMA
Hari, Tanggal : Sabtu, 31 Oktober 2026
Tempat : Masjid Salahuddin KPP Madya Malang
Pemateri : Asatidz
Tema / Kitab : Kajian Lama
Notulis : Tim Media & Notula Masjid Salahuddin

📌 RINGKASAN EKSEKUTIF (EXECUTIVE SUMMARY)
Ringkasan eksekutif notula yang baru direformat.

📑 DAFTAR ISI PEMBAHASAN
Poin Pembahasan

📖 PEMBAHASAN LENGKAP
1. Poin Pembahasan
Uraian pembahasan materi kajian.

💡 KESIMPULAN & PESAN AKSI
Poin Aksi: Uraian pesan aksi nyata.
MARKDOWN;

        $mockAiService = $this->createMock(NotulaAiService::class);
        $mockAiService->method('isAvailable')->willReturn(true);
        $mockAiService->method('reformatExistingNotula')->willReturn($reformattedText);

        $this->app->instance(NotulaAiService::class, $mockAiService);

        $this->artisan('notula:reformat-all', ['--id' => $kajian->id])
            ->assertExitCode(0);

        $this->assertDatabaseHas('kajians', [
            'id' => $kajian->id,
            'notula' => $reformattedText,
        ]);
    }
}
