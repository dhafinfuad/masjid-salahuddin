# 🕌 Masjid Salahuddin — Sistem Informasi Terpadu

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![Livewire](https://img.shields.io/badge/Livewire-3-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
[![License](https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge)](#lisensi)
[![Status](https://img.shields.io/badge/Status-Active-brightgreen?style=for-the-badge)](#)

Sistem informasi terpadu yang dirancang khusus untuk mendukung pengelolaan operasional, administrasi, dan layanan digital di **Masjid Salahuddin**. Aplikasi ini mengintegrasikan berbagai aspek manajemen jamaah, jadwal sholat, petugas ibadah, keuangan, hingga komunikasi dua arah dengan jamaah.

**[📱 Mulai Setup](#-panduan-setup) • [✨ Fitur](#-fitur-utama) • [🛠️ Tech Stack](#️-tech-stack) • [📸 Screenshots](#-screenshots) • [📖 Dokumentasi](#-dokumentasi)**

---

## 📋 Daftar Isi

- [📌 Tentang Proyek](#-tentang-proyek)
- [📸 Screenshots](#-screenshots)
- [✨ Fitur Utama](#-fitur-utama)
- [🛠️ Tech Stack](#️-tech-stack)
- [📁 Struktur Proyek](#-struktur-proyek)
- [🚀 Panduan Setup](#-panduan-setup)
- [🧪 Testing](#-testing)
- [🔐 Keamanan](#-keamanan)
- [📦 Deployment](#-deployment)
- [📖 Dokumentasi](#-dokumentasi)
- [🤝 Kontribusi](#-kontribusi)
- [📄 Lisensi](#-lisensi)

---

## 📌 Tentang Proyek

**Masjid Salahuddin** adalah aplikasi web enterprise yang menggabungkan **portal publik untuk jamaah** dan **dashboard administratif untuk takmir**. Dengan teknologi **Progressive Web App (PWA)**, aplikasi ini dapat:

- ✅ Diakses dari desktop & mobile (responsive design)
- ✅ Diinstal langsung ke homescreen smartphone
- ✅ Berfungsi offline dengan service worker
- ✅ Memberikan notifikasi push untuk reminder penting

Sistem ini membantu Masjid Salahuddin dalam:
1. **Manajemen Jadwal**: Perhitungan waktu sholat otomatis (metode Kemenag RI)
2. **Penugasan Petugas**: Roster imam, muadzin, khotib berbasis sistem roster
3. **Layanan Jamaah**: Informasi profil masjid, agenda kegiatan, kotak saran dua arah
4. **Laporan Keuangan**: Pencatatan infaq & transparansi kas masjid
5. **Digital Signage**: Tampilan khusus untuk monitor TV masjid

---

## 📸 Screenshots

### 🌐 Portal Jamaah & PWA Responsif
Halaman publik yang dapat diakses jamaah dari desktop dan mobile dengan instalasi PWA.

| Portal Beranda (Desktop) | Tampilan Mobile & PWA |
| :---: | :---: |
| ![Portal Beranda Jamaah](index/docs/screenshots/01-portal-beranda.png) | ![Portal Mobile & PWA](index/docs/screenshots/09-portal-mobile.png) |

### ⏰ Jadwal Sholat & Roster Petugas
Perhitungan otomatis waktu sholat dan penugasan petugas imam, muadzin, dan khotib harian.

| Jadwal Waktu Sholat | Roster Petugas |
| :---: | :---: |
| ![Jadwal Sholat](index/docs/screenshots/02-jadwal-sholat.png) | ![Roster Petugas](index/docs/screenshots/03-petugas-sholat.png) |

### 📅 Kalender Kegiatan & Kotak Saran
Daftar kegiatan, kajian, dan sistem aspirasi dengan transparansi tindak lanjut.

| Kalender Kegiatan | Kotak Saran & Aspirasi |
| :---: | :---: |
| ![Kalender Kegiatan](index/docs/screenshots/04-kegiatan-masjid.png) | ![Kotak Saran](index/docs/screenshots/06-kotak-saran.png) |

### 🏛️ Profil Masjid & Struktur Kepengurusan
Informasi identitas masjid, dokumen SK resmi, dan galeri kegiatan.

| Profil Masjid | Struktur Pengurus |
| :---: | :---: |
| ![Profil Masjid](index/docs/screenshots/05-profil-masjid.png) | ![Struktur Kepengurusan](index/docs/screenshots/05c-struktur-pengurus.png) |

| Galeri Kegiatan |
| :---: |
| ![Galeri Kegiatan](index/docs/screenshots/05b-galeri-kegiatan.png) |

### 📺 Digital Signage — TV Display Masjid
Tampilan khusus monitor TV (Full HD 1080p) dengan jam hisab, countdown adzan, dan running text.

![TV Display Digital Signage](index/docs/screenshots/07-tv-display.png)

### 🔐 Autentikasi & Registrasi Pengurus
Keamanan akses modul admin dengan verifikasi email kedinasan.

| Login Pengurus | Pendaftaran |
| :---: | :---: |
| ![Login Pengurus](index/docs/screenshots/08-login-pengurus.png) | ![Pendaftaran](index/docs/screenshots/08b-register.png) |

---

## ✨ Fitur Utama

### 👥 Portal Jamaah
- Informasi profil dan identitas masjid
- Jadwal dan agenda kegiatan terbaru
- Katalog kajian dan penceramah
- Berita dan artikel informatif

### ⏰ Manajemen Jadwal Sholat
- Perhitungan waktu sholat otomatis (metode Kemenag RI)
- Waktu imsak untuk puasa
- Pemberitahuan adzan terintegrasi
- Countdown iqomah real-time

### 👔 Manajemen Petugas Sholat
- Roster jadwal imam, muadzin, dan khotib
- Pengaturan giliran petugas sholat
- Riwayat dan notifikasi tugas
- Manajemen kontak petugas

### 📺 TV Display (Digital Signage)
- Tampilan khusus untuk monitor/TV masjid
- Jam digital dengan desain modern
- Countdown adzan dan iqomah
- Slide otomatis untuk kegiatan dan pengumuman

### 💰 Pelaporan Keuangan & Infaq
- Pencatatan transaksi kas masuk dan keluar
- Laporan keuangan terstruktur
- Export laporan dalam format PDF
- Dashboard transparansi keuangan

### 📱 Progressive Web App (PWA)
- Instalasi langsung di smartphone tanpa app store
- Akses cepat dan offline-capable
- Interface native-like experience
- Notifikasi push untuk reminder penting

### 💭 Kotak Saran & Aspirasi
- Saluran komunikasi jamaah dengan takmir
- Manajemen feedback dan pengaduan
- Prioritas dan status follow-up
- Transparansi respon takmir

### 🔐 Dashboard Admin Takmir
- Panel kontrol manajemen terpusat
- Kelola semua konten dan data
- Manajemen pengguna dan akses
- Pengaturan sistem dan konfigurasi

---

## 🛠️ Tech Stack

| Layer | Teknologi | Versi | Deskripsi |
| :--- | :--- | :--- | :--- |
| **Backend Framework** | [Laravel](https://laravel.com/) | 11.x | Framework PHP modern dengan Eloquent ORM |
| **Language** | PHP | 8.2+ | Bahasa server-side dengan type hints |
| **Frontend Reactivity** | [Livewire](https://livewire.laravel.com/) | 3.x | Komponen reaktif tanpa reload halaman |
| **Frontend Helper** | [Alpine.js](https://alpinejs.dev/) | v3 | Reaktivitas sisi klien untuk modal & animasi |
| **Styling** | [Tailwind CSS](https://tailwindcss.com/) | v3 | Utility-first CSS framework |
| **Build Tool** | [Vite](https://vitejs.dev/) | 8.x | Bundler & dev server super cepat |
| **Database** | MySQL / MariaDB | 8.0+ / 10.5+ | Penyimpanan data terstruktur |
| **Web Server** | Nginx + OpenResty | Latest | High-performance web server |
| **PWA & Offline** | Service Worker | Standard | Offline support & installable app |

---

## 📁 Struktur Proyek

```text
masjid-salahuddin/
├── index/                              # 📂 Aplikasi Laravel Utama
│   ├── app/
│   │   ├── Http/Controllers/          # Controller untuk setiap fitur
│   │   ├── Livewire/                  # Komponen Livewire reaktif
│   │   ├── Models/                    # Model database (Eloquent ORM)
│   │   └── Services/                  # Business logic & services
│   ├── config/                         # Konfigurasi aplikasi
│   ├── database/
│   │   ├── migrations/                # Schema database
│   │   ├── seeders/                   # Data awal
│   │   └── factories/                 # Test data factory
│   ├── docs/                           # 📚 Dokumentasi & Screenshots
│   │   ├── ARCHITECTURE.md            # Panduan arsitektur sistem
│   │   ├── SETUP.md                   # Panduan setup & instalasi
│   │   └── screenshots/               # 📸 Tangkapan layar
│   ├── public/                         # Public web root
│   │   ├── manifest.json              # PWA manifest
│   │   ├── sw.js                      # Service worker
│   │   └── assets/                    # CSS, JS, images
│   ├── resources/
│   │   ├── views/                     # Blade template views
│   │   ├── css/                       # Stylesheet
│   │   └── js/                        # JavaScript
│   ├── routes/
│   │   ├── web.php                    # Web routes
│   │   └── api.php                    # API routes
│   ├── tests/                         # Feature & Unit tests
│   ├── .env.example                   # Environment template
│   ├── composer.json                  # PHP dependencies
│   └── package.json                   # Node dependencies
├── .gitignore
└── README.md                          # Dokumentasi utama
```

---

## 🚀 Panduan Setup

### 📋 Prasyarat

Pastikan sudah terinstal:
- **PHP** >= 8.2 dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd`/`imagick`, `fileinfo`
- **Composer** (PHP dependency manager)
- **Node.js** >= 18 & **NPM**
- **MySQL** atau **MariaDB** (versi 8.0+ / 10.5+)
- **Git** untuk version control

**Rekomendasi Setup Lokal:**
- [Laragon](https://laragon.org/) (Windows)
- [Herd](https://herd.laravel.com/) (macOS)
- [Docker](https://www.docker.com/) (Cross-platform)

---

### ⚡ Langkah Instalasi

#### 1️⃣ Clone Repository
```bash
git clone https://github.com/dhafinfuad/masjid-salahuddin.git
cd masjid-salahuddin/index
```

#### 2️⃣ Instalasi Backend Dependencies
```bash
cp .env.example .env
composer install
```

#### 3️⃣ Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=masjid_salahuddin
DB_USERNAME=root
DB_PASSWORD=
```

#### 4️⃣ Setup Application
```bash
php artisan key:generate
php artisan migrate --seed
```

#### 5️⃣ Instalasi Frontend Dependencies
```bash
npm install
npm run dev
```

#### 6️⃣ Jalankan Development Server
```bash
php artisan serve
```

Akses aplikasi di: **`http://localhost:8000`**

---

### 🔑 Kredensial Default

| Role | Email | Password |
| :--- | :--- | :--- |
| Admin Takmir | `admin@masjid-salahuddin.local` | `password` |

⚠️ **Penting**: Ubah password setelah login pertama kali!

---

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Test dengan coverage report
php artisan test --coverage

# Test file spesifik
php artisan test tests/Feature/ScheduleTest.php
```

---

## 🔐 Keamanan

### Best Practices yang Diterapkan
- ✅ Input validation & sanitization
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ CSRF protection (Laravel CSRF token)
- ✅ Password hashing (bcrypt)
- ✅ Rate limiting untuk API & login
- ✅ Environment variable untuk secrets

### Melaporkan Vulnerability
Jika menemukan kerentanan keamanan, hubungi secara private. **Jangan** share di issue publik.

---

## 📦 Deployment

### Build Production
```bash
npm run build
php artisan optimize
php artisan config:cache
php artisan route:cache
```

### Deploy ke Server
1. Upload ke server menggunakan FTP, SSH, atau CI/CD
2. Konfigurasi `.env` production
3. Jalankan: `composer install --no-dev && php artisan migrate --force`

Untuk panduan deploy detail, lihat [DEPLOYMENT.md](./index/docs/DEPLOYMENT.md).

---

## 📖 Dokumentasi

- [📚 Setup Lokal & Development](./index/docs/SETUP.md)
- [🏗️ Arsitektur Sistem](./index/docs/ARCHITECTURE.md)
- [🔄 Sinkronisasi Livewire & Alpine](./index/docs/LIVEWIRE_ALPINE_STATE_SYNC_GUIDELINES.md)

---

## 🤝 Kontribusi

Kami menerima kontribusi dari komunitas! Berikut caranya:

1. **Fork** repository ini
2. **Buat branch** feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** perubahan (`git commit -m 'Add: AmazingFeature'`)
4. **Push** ke branch (`git push origin feature/AmazingFeature`)
5. **Buat Pull Request**

Pastikan:
- ✅ Code mengikuti coding standards
- ✅ Test sudah dibuat/diupdate
- ✅ Dokumentasi sudah diupdate
- ✅ Commit messages jelas dan deskriptif

---

## 📄 Lisensi

Hak Cipta © 2026 Masjid Salahuddin. Semua hak dilindungi.

Proyek ini adalah **proprietary software** khusus untuk Masjid Salahuddin. Penggunaan, distribusi, atau modifikasi tanpa izin resmi dilarang.

---

**Dibuat dengan ❤️ untuk kemakmuran Masjid Salahuddin**
