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
# Dokumen Kajian Tafsir Tematik Al-Qur'an
**Tema:** Memahami Makna Keselamatan dan Zikir
**Fokus Bahasan:** Hakikat Keselamatan dan Zikir
**Pemateri:** Ustadz Pemateri
---

## 1. Intisari dan Latar Belakang Urgensi
Keselamatan adalah dambaan setiap hamba.

## 2. Analisis Dalil Al-Qur'an dan Sunnah
### Zikir dan Ingat kepada Allah (QS. Al-Baqarah: 152)
> فَاذْكُرُونِي أَذْكُرْكُمْ
> *"Ingatlah kalian kepada-Ku, niscaya Aku akan mengingat kalian."*

## 3. Hikmah, Konteks Sosial, dan Budaya
Tradisi selamatan di Indonesia mencerminkan doa keselamatan.

## 4. Sesi Diskusi / Tanya Jawab Interaktif
Adab zikir dan ketundukan hati setelah salat.

## 5. Ringkasan Poin Aksi dan Karakter Pribadi Muslim
1. **Memperbanyak Zikir:** Membiasakan zikir setelah shalat.

---

### Catatan Kaki (Footnotes): Informasi Tambahan di Luar Transkrip

[^1]: **Takhrij Hadits:** Doa Allahumma Antas Salam diriwayatkan oleh Imam Muslim no. 591.
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
}
