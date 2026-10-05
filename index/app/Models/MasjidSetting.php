<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasjidSetting extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'latitude',
        'longitude',
        'qibla_angle',
        'calculation_method',
        'city_id',
        'city_name',
        'subuh_offset',
        'dzuhur_offset',
        'ashar_offset',
        'maghrib_offset',
        'isya_offset',
        'iqamah_delay_minutes',
        'tv_announcements',
        'friday_prayer_info',
        'bank_accounts',
        'takmir_documents',
        'takmir_structure',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'qibla_angle' => 'decimal:2',
            'subuh_offset' => 'integer',
            'dzuhur_offset' => 'integer',
            'ashar_offset' => 'integer',
            'maghrib_offset' => 'integer',
            'isya_offset' => 'integer',
            'iqamah_delay_minutes' => 'integer',
            'tv_announcements' => 'array',
            'friday_prayer_info' => 'array',
            'bank_accounts' => 'array',
            'takmir_documents' => 'array',
            'takmir_structure' => 'array',
        ];
    }

    /**
     * Singleton instance helper
     */
    public static function getActive(): self
    {
        return static::firstOrCreate([], [
            'name' => 'Masjid Salahuddin',
            'address' => 'Jl. Jenderal Sudirman No. 45, Jakarta Pusat',
            'phone' => '+62 21 555-1234',
            'email' => 'kontak@masjidsalahuddin.id',
            'latitude' => -6.2088000,
            'longitude' => 106.8456000,
            'qibla_angle' => 295.12,
            'city_id' => '1634',
            'city_name' => 'KOTA MALANG',
            'bank_accounts' => [
                [
                    'bank' => 'BSI (Bank Syariah Indonesia)',
                    'holder' => 'DKM Masjid Salahuddin',
                    'account_number' => '7123-4567-89',
                ],
            ],
            'subuh_offset' => 2,
            'dzuhur_offset' => 2,
            'ashar_offset' => 2,
            'maghrib_offset' => 2,
            'isya_offset' => 2,
            'iqamah_delay_minutes' => 10,
        ]);
    }

    /**
     * Default Takmir Documents
     */
    public static function getDefaultTakmirDocuments(): array
    {
        return [
            'sk' => [
                'key' => 'sk',
                'title' => 'Surat Keputusan Kepala KPP Madya Malang',
                'number' => 'KEP-48/KPP.1209/2026',
                'date' => '14 April 2026',
                'description' => 'Keputusan tentang Perubahan Susunan Pengurus Takmir Masjid Sholahuddin KPP Madya Malang Periode 2026-2029.',
                'file' => 'Surat_Keputusan_Takmir_KEP-48_KPP.1209_2026.pdf',
                'badge' => 'SK Resmi',
                'icon' => 'file-text',
                'badge_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            ],
            'lampiran1' => [
                'key' => 'lampiran1',
                'title' => 'Lampiran I: Susunan Pengurus Takmir',
                'number' => 'Lampiran I SK KEP-48/KPP.1209/2026',
                'date' => '14 April 2026',
                'description' => 'Daftar nama lengkap susunan pimpinan, sekretaris, bendahara, pengelola bidang, dan seluruh anggota pengurus Takmir periode 2026-2029.',
                'file' => 'Lampiran_SK_Takmir_KEP-48_KPP.1209_2026.pdf',
                'badge' => 'Struktur Pengurus',
                'icon' => 'users',
                'badge_color' => 'bg-blue-50 text-blue-700 border-blue-200',
            ],
            'lampiran2' => [
                'key' => 'lampiran2',
                'title' => 'Lampiran II: Penjabaran Tugas & Wewenang (Tupoksi)',
                'number' => 'Lampiran II SK KEP-48/KPP.1209/2026',
                'date' => '14 April 2026',
                'description' => 'Rincian tugas pokok dan fungsi (Tupoksi) Pembina, Ketua, Wakil Ketua, Sekretaris, Bendahara, serta 4 Bidang Pengelola Takmir.',
                'file' => 'Tupoksi_Takmir_KEP-48_KPP.1209_2026.pdf',
                'badge' => 'Tupoksi Kerja',
                'icon' => 'clipboard-check',
                'badge_color' => 'bg-amber-50 text-amber-700 border-amber-200',
            ],
        ];
    }

    /**
     * Get Takmir Documents (with fallback)
     */
    public function getTakmirDocuments(): array
    {
        $defaults = self::getDefaultTakmirDocuments();
        $stored = $this->takmir_documents ?? [];

        foreach ($defaults as $k => $def) {
            if (isset($stored[$k]) && is_array($stored[$k])) {
                $defaults[$k] = array_merge($def, $stored[$k]);
            }
        }

        return $defaults;
    }

    /**
     * Default Takmir Structure & Tupoksi
     */
    public static function getDefaultTakmirStructure(): array
    {
        return [
            'pembina' => [
                'role' => 'Pembina',
                'title' => 'Kepala Kantor Pelayanan Pajak Madya Malang',
                'name' => 'Teguh Iman Wirotomo',
                'tugas' => [
                    'Memberikan arahan umum dan pembinaan keagamaan masjid agar tetap lurus di atas syariat Islam.',
                    'Mengawasi keselarasan program kerja Takmir dengan kebijakan institusi/lingkungan.',
                ],
            ],
            'ketua' => [
                'role' => 'Ketua Takmir',
                'name' => 'Moh. Nazil Fuadi Kusmawan',
                'tugas' => [
                    'Memimpin, mengoordinasikan, dan memantau seluruh aktivitas pelaksanaan program Takmir Masjid.',
                    'Menjalin hubungan internal dengan pimpinan kantor serta jaringan dakwah eksternal demi kesejahteraan umat.',
                ],
            ],
            'wakil_ketua' => [
                'role' => 'Wakil Ketua Takmir',
                'name' => 'Mujiburrokhman',
                'tugas' => [
                    'Mendampingi Ketua Takmir dalam mengarahkan operasional kepemimpinan harian.',
                    'Mewakili penandatanganan dokumen atau pertemuan takmir apabila Ketua berhalangan hadir.',
                ],
            ],
            'sekretaris' => [
                'role' => 'Sekretaris',
                'names' => ['Deril Amrizal Kholid', 'Alan Irfansyah'],
                'tugas' => [
                    'Mengelola administrasi persuratan resmi, proposal, notulensi rapat, database kepengurusan, dan pengarsipan.',
                    'Mempersiapkan draf laporan berkala s.d. Laporan Pertanggungjawaban (LPJ) kepanitiaan.',
                ],
            ],
            'bendahara' => [
                'role' => 'Bendahara',
                'names' => ['Aris Setianto', 'Santi Rukmala'],
                'tugas' => [
                    'Mengelola dan mengontrol seluruh kas penerimaan, pengeluaran riil, dan mutasi saldo infaq/zakat.',
                    'Menyusun laporan keuangan berkala untuk dipublikasikan secara transparan kepada jamaah.',
                ],
            ],
            'bidang' => [
                [
                    'name' => 'Bidang Dakwah dan Perayaan Hari Besar Islam (PHBI)',
                    'icon' => 'book-open',
                    'pengelola' => ['Bimo Heriyanto', 'Sukirman', 'Khamid Masduki'],
                    'anggota' => [
                        'Wempi Maron', 'Mahmud Hidayat', 'Junaedi', 'Moh. Lukman Hakim',
                        'Dhafin Fuad Mahathir', 'Rizki Afandi Fajar', 'Dian Awida Kohar',
                        'Sol Djoni Risandy', 'Teguh Irvanto',
                    ],
                    'tupoksi' => [
                        'Mengatur jadwal khotib, imam rawatib, kajian rutin harian/mingguan, serta perayaan hari besar Islam (PHBI).',
                        'Mengembangkan sarana dakwah digital berkala, buletin jumat, dan pembinaan mualaf/jamaah baru.',
                    ],
                ],
                [
                    'name' => 'Bidang Humas dan Sosial',
                    'icon' => 'heart-handshake',
                    'pengelola' => ['Ichtiar Rachmatullah', 'Adim Kadimulloh', 'Iwan Darmawan'],
                    'anggota' => [
                        'Rahman Hakim', 'Erfan Nur Faizin', 'Kusuma Fadli Wijaya', 'Yoni Ramdhani',
                        'Dani Sulistiono', 'Krisna Amalia Maharani', 'Ugik Endrar Viana',
                        'Yogi Kusuma Wahyudhiana', 'Hari Sulistiyo',
                    ],
                    'tupoksi' => [
                        'Menghimpun jalinan kerja sama sosial, penyaluran santunan anak yatim, pengelolaan zakat fitrah, dan kurban.',
                        'Membina sistem informasi publikasi mading, broadcast kajian WhatsApp, dokumentasi kegiatan, dan publikasi media sosial.',
                    ],
                ],
                [
                    'name' => 'Bidang Rumah Tangga dan Sarana Prasarana',
                    'icon' => 'home',
                    'pengelola' => ['Darma Setiawan', 'Oky Fardiano', 'Dody Ferdianto'],
                    'anggota' => [
                        'Muhammad Fahmi Hidayat', 'Fahmi Fahdian Aziz', 'Taufik Ismail',
                        'Riesqi Devana Mungki', 'Dedi Dwi Setiawan', 'Agiel Noer Yahya',
                        'Dimas Irfan Wijanarko', 'Mochammad Dzulfikri Yul Zamzami', 'Khudori',
                    ],
                    'tupoksi' => [
                        'Bertanggung jawab atas pengelolaan kebersihan, kesucian, ketertiban, keamanan, parkir, dan kenyamanan bangunan masjid.',
                        'Merawat sarana prasarana fisik meliputi sound system, mesin penyejuk udara (AC), karpet, genset, dan kelistrikan masjid.',
                    ],
                ],
                [
                    'name' => 'Bidang Keputrian',
                    'icon' => 'heart',
                    'pengelola' => ['Ida Heryanie', 'Miswati', 'Endang Karyawati'],
                    'anggota' => [
                        'Anik Isnaini', 'Retno Prastyowati', 'Rina Dasa Sari',
                        'Maya Mahiyatul Zulaikha', 'Ike Jayanti', 'Ilva Mardotin',
                        'Shanti Wulandari', 'Desti Nurul Tri Hapsari', 'Wulandari Fadhilah',
                    ],
                    'tupoksi' => [
                        'Menyelenggarakan kegiatan kajian fiqih wanita, keterampilan kreatif keputrian, pembinaan akhlak anak, serta majelis taklim ibu-ibu.',
                        'Menjaga ketertiban, kesucian shaf ibadah jamaah wanita, kelengkapan mukena, dan kenyamanan area khusus keputrian.',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get Takmir Structure (with fallback)
     */
    public function getTakmirStructure(): array
    {
        $defaults = self::getDefaultTakmirStructure();
        $stored = $this->takmir_structure ?? [];

        if (empty($stored)) {
            return $defaults;
        }

        return array_replace_recursive($defaults, $stored);
    }
}
