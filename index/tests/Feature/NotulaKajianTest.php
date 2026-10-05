<?php

namespace Tests\Feature;

use App\Livewire\Admin\AdminDashboard;
use App\Models\Kajian;
use App\Models\MasjidSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotulaKajianTest extends TestCase
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
            'title' => 'Karakteristik Keimanan yang Kokoh (Al-Mu\'minun)',
            'speaker_name' => 'Ustadz Pemateri Tematik',
            'speaker_phone' => '081234567890',
        ]);
    }

    /** @test */
    public function test_master_user_can_open_notula_modal_and_save_notula()
    {
        $this->actingAs($this->masterUser);

        $notulaMarkdown = "# Notula Kajian Tematik\n\n**Tema:** Karakteristik Keimanan yang Kokoh\n\n## 1. Landasan Semantik\n\nAyat pendukung...";

        Livewire::test(AdminDashboard::class)
            ->call('openNotulaModal', $this->sampleKajian->id)
            ->assertSet('notulaKajianId', $this->sampleKajian->id)
            ->assertSet('showNotulaModal', true)
            ->assertDispatched('open-notula-modal')
            ->call('saveNotula', $this->sampleKajian->id, $notulaMarkdown)
            ->assertDispatched('toast')
            ->assertDispatched('notula-saved');

        $this->assertDatabaseHas('kajians', [
            'id' => $this->sampleKajian->id,
            'notula' => $notulaMarkdown,
        ]);

        $largeNotula = str_repeat("Ini adalah baris catatan kajian \"dengan tanda petik\" dan 'apostrof'.\n[^10]: Footnote rujukan.\n", 200);
        Livewire::test(AdminDashboard::class)
            ->call('saveNotula', $this->sampleKajian->id, $largeNotula);

        $this->assertEquals($largeNotula, $this->sampleKajian->fresh()->notula);
    }

    /** @test */
    public function test_exact_user_notula_content_save_and_reopen()
    {
        $this->actingAs($this->masterUser);

        $userText = <<<'MARKDOWN'
# Notula Kajian Tematik

**Tema:** Karakteristik Keimanan yang Kokoh (*Al-Mu'minun*) dalam Al-Qur'an

**Fokus Bahasan:** Analisis Semantik Kebahasaan dan Tadabbur 4 Ayat Berawalan *“Innamal-Mu’minun”*

---

## 1. Landasan Semantik: Pembedaan *Alladzina Âmanû* vs *Al-Mu'minûn*

Dalam Al-Qur'an, Allah SWT menggunakan dua bentuk redaksi utama ketika merujuk kepada orang-orang yang beriman, yang memiliki implikasi makna gramatikal (*dilâlah nahwiyyah*)[^1] berbeda:

* **Bentuk Kata Kerja / *Fi'il* (*Alladzina Âmanû* / الَّذِينَ آمَنُوا):**
* Secara kaidah bahasa Arab, kata kerja (*fi'il*) terikat oleh dimensi waktu (*zaman*) dan menunjukkan proses pembaharuan (*tajaddud*) serta perubahan (*huduts*)[^2].
* Implikasi maknanya bersifat dinamis, bertingkat, dan belum sepenuhnya kokoh/stabil (temporer).
* Panggilan *“Yâ ayyuhalladzîna âmanû”* umumnya diiringi dengan perintah, larangan, atau dorongan untuk meningkatkan mutu ibadah, seperti perintah berpuasa (QS. Al-Baqarah: 183)[^3], menjaga keluarga dari api neraka (QS. At-Tahrim: 6)[^3], atau perintah untuk terus memperbarui iman (QS. An-Nisa: 136)[^3], karena keimanan mereka masih dalam proses pembinaan.


* **Bentuk Kata Benda / *Isim* (*Al-Mu'minûn* / الْمُؤْمِنُونَ):**
* Bentuk kata benda (*isim*, khususnya *isim fa'il*) menunjukkan ketetapan (*tsubut*) dan kesinambungan (*dawam*)[^2] yang tidak terikat batas waktu.
* Mengindikasikan keimanan yang telah terhunjam mantap, stabil, kokoh, dan paripurna.
* Penyebutan *Al-Mu'minun* sering diiringi dengan pujian atau jaminan keberuntungan dari Allah SWT, seperti dalam QS. Al-Mu'minun: 1 (*Qad aflahal-mu'minûn* – "Sungguh beruntung orang-orang yang beriman").
MARKDOWN;

        $component = Livewire::test(AdminDashboard::class)
            ->call('openNotulaModal', $this->sampleKajian->id)
            ->call('saveNotula', $this->sampleKajian->id, $userText);

        $savedDb = $this->sampleKajian->fresh()->notula;
        $this->assertEquals($userText, $savedDb);

        // Test reopening
        $component->call('openNotulaModal', $this->sampleKajian->id)
            ->assertDispatched('open-notula-modal', function ($event, $params) use ($userText) {
                // Check if dispatched notula is equal to user text
                $data = $params[0] ?? $params['data'] ?? $params;
                return isset($data['notula']) && $data['notula'] === $userText;
            });
    }

    /** @test */
    public function test_non_master_user_cannot_save_notula()
    {
        $this->actingAs($this->regularUser);

        Livewire::test(AdminDashboard::class)
            ->call('saveNotula', $this->sampleKajian->id, 'Percobaan bypass notula')
            ->assertDispatched('toast', fn ($event, $params) => str_contains($params['message'] ?? '', 'Akses ditolak'));

        $this->assertDatabaseMissing('kajians', [
            'id' => $this->sampleKajian->id,
            'notula' => 'Percobaan bypass notula',
        ]);
    }

    /** @test */
    public function test_notula_button_rendered_for_master_role()
    {
        $this->actingAs($this->masterUser);

        $response = $this->get('/admin/kegiatan');
        $response->assertStatus(200);
        $response->assertSee('Notula');
    }

    /** @test */
    public function test_notula_button_not_rendered_for_non_master_role()
    {
        $this->actingAs($this->regularUser);

        $response = $this->get('/admin/kegiatan');
        $response->assertStatus(200);
        $response->assertDontSee('>Notula</div>', false);
    }

    /** @test */
    public function test_notula_button_rendered_on_portal_page_when_notula_exists()
    {
        $this->sampleKajian->update([
            'notula' => '# Notula Test Portal',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Baca Risalah Notula Kajian');
        $response->assertSee('showPortalNotulaModal');
    }

    /** @test */
    public function test_notula_button_rendered_on_kegiatan_page_when_notula_exists()
    {
        $this->sampleKajian->update([
            'notula' => '# Notula Test Kegiatan',
        ]);

        $response = $this->get('/kegiatan');
        $response->assertStatus(200);
        $response->assertSee('Baca Risalah Notula Kajian');
        $response->assertSee('showPortalNotulaModal');
    }
}
