# Masjid Salahuddin - Website & Portal Informasi

Sistem Informasi dan Portal Resmi Masjid Salahuddin, mencakup portal jamaah, jadwal sholat, pengelolaan kajian & kegiatan, manajemen petugas sholat, TV display masjid, pelaporan keuangan infaq, dan panel administrasi takmir masjid.

---

## 🌟 Fitur Utama

- **Portal Jamaah**: Informasi profil masjid, agenda kegiatan, jadwal kajian, dan artikel/berita.
- **Jadwal Sholat & Waktu Imsak**: Penghitungan waktu sholat otomatis dan akurat.
- **Roster & Petugas Sholat**: Manajemen jadwal imam, muadzin, dan penceramah/khotib Jumat.
- **TV Display (Digital Signage)**: Tampilan khusus layar monitor / TV masjid dengan jam digital, countdown adzan/iqomah, dan slide kegiatan.
- **Laporan Keuangan & Transparansi Infaq**: Pencatatan kas masuk dan kas keluar dengan export PDF/laporan.
- **PWA (Progressive Web App)**: Akses cepat dan instalasi aplikasi di smartphone atau tablet tanpa unduh dari app store.
- **Kotak Saran & Aspirasi**: Sarana jamaah menyampaikan masukan untuk kemakmuran masjid.
- **Dashboard Takmir (Admin)**: Manajemen terpusat untuk seluruh konten, pengguna, dan pengaturan sistem.

---

## 🛠️ Teknologi & Stack

- **Backend**: [Laravel](https://laravel.com/) (PHP)
- **Frontend / Reactivity**: [Livewire](https://livewire.laravel.com/) & [Alpine.js](https://alpinejs.dev/)
- **Build Tool**: [Vite](https://vitejs.dev/)
- **Web Server & Reverse Proxy**: OpenResty / Nginx (1Panel)
- **Database**: MySQL / MariaDB

---

## 📁 Struktur Direktori

```text
1panel-masjidsalahuddin/
├── index/              # Aplikasi utama Laravel (Source code)
│   ├── app/            # Controllers, Models, Livewire Components, Services
│   ├── config/         # Konfigurasi aplikasi
│   ├── database/       # Migrasi, seeders, dan factories
│   ├── public/         # Public web root, assets, manifest PWA
│   ├── resources/      # Blade views, CSS, dan JavaScript
│   ├── routes/         # Definisi routing web dan console
│   └── tests/          # Feature & Unit tests
├── rewrite/            # Konfigurasi URL rewrite Nginx/OpenResty
├── log/                # Folder log web server (diabaikan oleh git)
├── ssl/                # Folder sertifikat SSL (diabaikan oleh git)
└── README.md
```

---

## 🚀 Panduan Instalasi Lokal

### 1. Prasyarat
- PHP >= 8.2 (ekstensi: pdo_mysql, mbstring, openssl, gd/imagick, fileinfo)
- Composer
- Node.js & NPM
- MySQL / MariaDB (misalnya via Laragon atau Docker)

### 2. Langkah Setup
1. Clone repositori:
   ```bash
   git clone https://github.com/dhafinfuad/masjid-salahuddin.git
   cd masjid-salahuddin/index
   ```

2. Salin environment file dan instal dependensi PHP:
   ```bash
   cp .env.example .env
   composer install
   ```

3. Generate application key:
   ```bash
   php artisan key:generate
   ```

4. Konfigurasi database di file `.env`, lalu jalankan migrasi & seeder:
   ```bash
   php artisan migrate --seed
   ```

5. Instal dependensi frontend & compile assets:
   ```bash
   npm install
   npm run build
   ```

6. Jalankan server lokal:
   ```bash
   php artisan serve
   ```

---

## 📄 Lisensi
Hak Cipta © 2026 Masjid Salahuddin.
