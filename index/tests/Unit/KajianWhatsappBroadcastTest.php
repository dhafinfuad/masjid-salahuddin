<?php

namespace Tests\Unit;

use App\Models\Kajian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KajianWhatsappBroadcastTest extends TestCase
{
    use RefreshDatabase;
    public function test_pekanan_whatsapp_broadcast_text_format(): void
    {
        $kajian = new Kajian([
            'type' => 'pekanan',
            'date' => '2026-09-07',
            'speaker_name' => 'Ust. Dr. Febrian Taufiq Sholeh MAg',
            'title' => 'Kajian Rutin Tafsir Al-Qur\'an',
        ]);

        $text = $kajian->whatsapp_broadcast_text;

        $this->assertStringContainsString('Assalamualaikum wr wb.', $text);
        $this->assertStringContainsString('Mari hadir dalam kajian Pekanan;', $text);
        $this->assertStringContainsString('🕌 Pemateri InsyaAllah oleh', $text);
        $this->assertStringContainsString('Ust. Dr. Febrian Taufiq Sholeh MAg', $text);
        $this->assertStringContainsString('📅 Hari/Tanggal: Senin, 07 September 2026', $text);
        $this->assertStringContainsString('🕓 Waktu: Setelah Sholat Ashar', $text);
        $this->assertStringContainsString('📍 Tempat: Masjid Sholahuddin, KPP Madya Malang', $text);
    }

    public function test_tematik_whatsapp_broadcast_text_format(): void
    {
        $kajian = new Kajian([
            'type' => 'tematik',
            'date' => '2026-09-15',
            'speaker_name' => 'Ust. Muhammad Yahya Ph.D',
            'title' => 'Tadabbur Surat Al-Kahfi',
        ]);

        $text = $kajian->whatsapp_broadcast_text;

        $this->assertStringContainsString('Mari hadir dalam kajian Tematik;', $text);
        $this->assertStringContainsString('Ust. Muhammad Yahya Ph.D', $text);
        $this->assertStringContainsString('📅 Hari/Tanggal: Selasa, 15 September 2026', $text);
    }

    public function test_jumat_whatsapp_broadcast_text_format(): void
    {
        $kajian = new Kajian([
            'type' => 'jumat',
            'date' => '2026-09-11',
            'khatib_name' => 'Ust. Dr. Febrian Taufiq Sholeh MAg',
            'muadzin_name' => 'Alan Irfansyah',
            'mc_name' => 'Deril Amrizal Kholid',
            'time_display' => '11:45 - 12:45',
        ]);

        $text = $kajian->whatsapp_broadcast_text;

        $this->assertStringContainsString("Assalamu'alaikum wa Rahmatullah wa Barakatuh", $text);
        $this->assertStringContainsString('Semoga keluarga besar KPP Madya Malang senantiasa sehat, sukses, dan berkah selalu.', $text);
        $this->assertStringContainsString('INFO JUMAT', $text);
        $this->assertStringContainsString('Insya Allah Khotib dan Imam Sholat Jumat hari ini :', $text);
        $this->assertStringContainsString('👳🏻 Khatib : Ust. Dr. Febrian Taufiq Sholeh MAg', $text);
        $this->assertStringNotContainsString('♀️', $text);
        $this->assertStringContainsString('🎙️ Muadzin : Alan Irfansyah', $text);
        $this->assertStringContainsString('📄 MC : Deril Amrizal Kholid', $text);
        $this->assertStringContainsString('Waktu Dzuhur hari ini :', $text);
        $this->assertStringNotContainsString('🕦 Waktu Dzuhur hari ini :', $text);
        $this->assertStringContainsString('29 Rabiul Awal 1448 H', $text);
        $this->assertStringContainsString('11 September 2026', $text);
    }
}
