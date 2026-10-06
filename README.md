# 🕌 Masjid Salahuddin - Sistem Informasi Terpadu

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![License](https://img.shields.io/badge/License-Proprietary-blue.svg)](#lisensi)
[![Status](https://img.shields.io/badge/Status-Active%20Development-brightgreen)](#)

Sistem informasi terpadu yang dirancang khusus untuk mendukung pengelolaan operasional, administrasi, dan layanan digital di Masjid Salahuddin. Aplikasi ini mengintegrasikan berbagai aspek manajemen masjid dalam satu platform yang mudah digunakan dan responsif.

---

## 📋 Daftar Isi

- [✨ Fitur Utama](#-fitur-utama)
- [🛠️ Teknologi & Stack](#️-teknologi--stack)
- [📁 Struktur Proyek](#-struktur-proyek)
- [🚀 Panduan Setup](#-panduan-setup)
- [📖 Dokumentasi](#-dokumentasi)
- [🤝 Kontribusi](#-kontribusi)
- [📄 Lisensi](#-lisensi)

---

## ✨ Fitur Utama

### 👥 Portal Jamaah
- Informasi profil dan identitas masjid
- Jadwal dan agenda kegiatan terbaru
- Katalog kajian dan penceramah
- Berita dan artikel informatif

### ⏰ Manajemen Jadwal Sholat
- Perhitungan waktu sholat otomatis dan akurat
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

## 🛠️ Teknologi & Stack

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| **Backend Framework** | [Laravel](https://laravel.com/) | 11.x |
| **Language** | PHP | 8.2+ |
| **Frontend Reactivity** | [Livewire](https://livewire.laravel.com/) & [Alpine.js](https://alpinejs.dev/) | Latest |
| **Build Tool** | [Vite](https://vitejs.dev/) | 8.x |
| **Styling** | Blade + Tailwind CSS | - |
| **Database** | MySQL / MariaDB | 8.0+ / 10.5+ |
| **Web Server** | Nginx + OpenResty | Latest |
| **Control Panel** | 1Panel | - |

---

## 📁 Struktur Proyek

```
masjid-salahuddin/
├── index/                          # 📂 Aplikasi Laravel Utama
│   ├── app/
│   │   ├── Http/Controllers/      # Controller untuk setiap fitur
│   │   ├── Livewire/              # Komponen Livewire reaktif
│   │   ├── Models/                # Model database (Eloquent ORM)
│   │   └── Services/              # Business logic & services
│   ├── config/                     # Konfigurasi aplikasi
│   ├── database/
│   │   ├── migrations/            # Schema database
│   │   ├── seeders/               # Data awal
│   │   └── factories/             # Test data factory
│   ├── public/                     # Public web root
│   │   ├── manifest.json          # PWA manifest
│   │   ├── sw.js                  # Service worker
│   │   └── assets/                # CSS, JS, images
│   ├── resources/
│   │   ├── views/                 # Blade template views
│   │   ├── css/                   # Stylesheet
│   │   └── js/                    # JavaScript
│   ├── routes/
│   │   ├── web.php                # Web routes
│   │   └── api.php                # API routes (jika ada)
│   ├── tests/                     # Feature & Unit tests
│   ├── .env.example               # Environment template
│   ├── composer.json              # PHP dependencies
│   └── package.json               # Node dependencies
├── rewrite/                        # ⚙️ Konfigurasi URL Rewrite
│   └── nginx.conf                 # Rule Nginx/OpenResty
├── ssl/                           # 🔒 Sertifikat SSL (gitignored)
├── log/                           # 📝 Log aplikasi (gitignored)
└── README.md                      # Dokumentasi ini
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
- [Laragon](https://laragon.org/) (Windows - All-in-one)
- [Herd](https://herd.laravel.com/) (macOS - Laravel optimized)
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
# Salin environment file
cp .env.example .env

# Instalasi PHP dependencies
composer install
```

#### 3️⃣ Konfigurasi Database
Edit file `.env` dan atur koneksi database:
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
# Generate application key
php artisan key:generate

# Jalankan database migration & seeding
php artisan migrate --seed
```

#### 5️⃣ Instalasi Frontend Dependencies
```bash
# Instalasi Node packages
npm install

# Build assets (development)
npm run dev

# Atau build production
npm run build
```

#### 6️⃣ Jalankan Development Server
```bash
php artisan serve
```

Aplikasi akan berjalan di **`http://localhost:8000`**

---

### 🔑 Credentials Default (Setelah Seeding)

Untuk login pertama kali ke dashboard admin:
- **Email**: `admin@masjid-salahuddin.local`
- **Password**: `password`

⚠️ **Penting**: Ganti password setelah login pertama kali!

---

## 📖 Dokumentasi

### Panduan Pengembang
- [Setup Lokal & Development](./docs/SETUP.md) - Panduan detail setup
- [Struktur Database](./docs/DATABASE.md) - Schema dan relasi database
- [API Reference](./docs/API.md) - Dokumentasi API (jika ada)
- [Deployment Guide](./docs/DEPLOYMENT.md) - Panduan deploy ke production

### User Guide
- [Panduan Admin](./docs/ADMIN_GUIDE.md) - Cara menggunakan dashboard admin
- [Panduan Jamaah](./docs/USER_GUIDE.md) - Fitur portal jamaah

---

## 🧪 Testing

Jalankan test suite untuk memastikan kualitas kode:

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
- ✅ Rate limiting untuk API
- ✅ Environment variable untuk secrets

### Melaporkan Vulnerability
Jika menemukan kerentanan keamanan, mohon hubungi secara private kepada tim development. **Jangan** share di issue publik.

---

## 📦 Deployment

### Deploy ke Server Production

1. **Persiapan**:
   ```bash
   # Build assets production
   npm run build
   
   # Optimize untuk production
   php artisan optimize
   php artisan config:cache
   php artisan route:cache
   ```

2. **Upload ke Server**:
   - Gunakan FTP, SSH, atau CI/CD pipeline
   - Pastikan `.env` production sudah dikonfigurasi dengan benar

3. **Setup di Server**:
   ```bash
   composer install --no-dev
   php artisan migrate --force
   ```

Untuk panduan deploy detail, lihat [Deployment Guide](./docs/DEPLOYMENT.md).

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

Lihat [CONTRIBUTING.md](./CONTRIBUTING.md) untuk detail lebih lanjut.

---

## 📋 Roadmap

- [ ] Mobile app native (React Native / Flutter)
- [ ] Multi-language support (Inggris, Arab)
- [ ] Integrasi dengan sistem pembayaran infaq
- [ ] Analytics dashboard untuk takmir
- [ ] Video streaming untuk khutbah Jumat
- [ ] WhatsApp Bot integration
- [ ] Calendar sync (Google Calendar, iCal)

---

## 📞 Kontak & Support

- **Issue & Bug Report**: Buat [GitHub Issue](https://github.com/dhafinfuad/masjid-salahuddin/issues)
- **Diskusi & Tanya Jawab**: Gunakan [GitHub Discussions](https://github.com/dhafinfuad/masjid-salahuddin/discussions)
- **Email**: [hubungi via repository]

---

## 📄 Lisensi

Hak Cipta © 2026 Masjid Salahuddin. Semua hak dilindungi.

Proyek ini adalah **proprietary software** yang khusus dikembangkan untuk Masjid Salahuddin. Penggunaan, distribusi, atau modifikasi tanpa izin resmi dilarang.

---

## 🙏 Ucapan Terima Kasih

Terima kasih kepada:
- Tim takmir Masjid Salahuddin atas visi dan dukungannya
- [Laravel](https://laravel.com/) community
- [Livewire](https://livewire.laravel.com/) & [Alpine.js](https://alpinejs.dev/) developers
- Semua kontributor yang telah membantu project ini

---

**Dibuat dengan ❤️ untuk kemakmuran Masjid Salahuddin**

> "Sebaik-baik kalian adalah yang terbaik terhadap keluarganya, dan aku adalah yang terbaik terhadap keluargaku." - HR. At-Tirmidzi
