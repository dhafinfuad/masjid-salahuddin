# 🔧 Setup & Installation Guide

Panduan lengkap untuk setup dan menjalankan Masjid Salahuddin secara lokal.

---

## 📋 Daftar Isi

- [Requirements](#requirements)
- [Instalasi (Windows)](#instalasi-windows)
- [Instalasi (macOS)](#instalasi-macos)
- [Instalasi (Linux)](#instalasi-linux)
- [Konfigurasi Database](#konfigurasi-database)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Database Seeding](#database-seeding)
- [Running Dev Server](#running-dev-server)
- [Troubleshooting](#troubleshooting)

---

## 📋 Requirements

### Minimum System Requirements
- **PHP**: >= 8.2 (dengan extension: pdo_mysql, mbstring, openssl, gd, fileinfo)
- **Node.js**: >= 18 & npm >= 9
- **MySQL**: >= 8.0 atau **MariaDB** >= 10.5
- **Composer**: >= 2.5
- **Git**: untuk version control

### Recommended Setup Tools
Gunakan tools berikut untuk setup yang lebih mudah:

| Platform | Tool | Download |
|----------|------|----------|
| **Windows** | Laragon | [laragon.org](https://laragon.org/) |
| **Windows** | Docker Desktop | [docker.com](https://www.docker.com/) |
| **macOS** | Herd | [herd.laravel.com](https://herd.laravel.com/) |
| **macOS** | Valet | [laravel.com/docs/valet](https://laravel.com/docs/valet) |
| **Linux** | Docker | [docker.com](https://www.docker.com/) |
| **All OS** | XAMPP / WAMP | [apachefriends.org](https://www.apachefriends.org/) |

---

## 💻 Instalasi (Windows)

### Option 1: Menggunakan Laragon (Recommended)

1. **Download & Install Laragon**
   - Download dari [laragon.org](https://laragon.org/)
   - Install di `C:\laragon`

2. **Clone Repository**
   ```bash
   cd C:\laragon\www
   git clone https://github.com/dhafinfuad/masjid-salahuddin.git
   cd masjid-salahuddin\index
   ```

3. **Setup PHP Dependencies**
   ```bash
   composer install
   ```

4. **Setup Node Dependencies**
   ```bash
   npm install
   ```

5. **Generate .env & App Key**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

6. **Setup Database**
   ```bash
   php artisan migrate --seed
   ```

7. **Build Assets**
   ```bash
   npm run build
   ```

8. **Run Dev Server**
   ```bash
   php artisan serve
   ```

---

### Option 2: Menggunakan XAMPP

1. **Download & Install XAMPP**
   - Download dari [apachefriends.org](https://www.apachefriends.org/)
   - Install XAMPP

2. **Clone Repository**
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/dhafinfuad/masjid-salahuddin.git
   cd masjid-salahuddin\index
   ```

3. **Setup**
   - Ikuti langkah 3-8 dari Option 1

4. **Start Server**
   - Buka XAMPP Control Panel
   - Klik "Start" untuk Apache & MySQL
   - Buka browser: `http://localhost/masjid-salahuddin/index/public`

---

## 🍎 Instalasi (macOS)

### Option 1: Menggunakan Herd (Recommended)

1. **Download & Install Herd**
   - Download dari [herd.laravel.com](https://herd.laravel.com/)
   - Install di Applications folder

2. **Clone Repository**
   ```bash
   cd ~/Herd
   git clone https://github.com/dhafinfuad/masjid-salahuddin.git
   cd masjid-salahuddin/index
   ```

3. **Setup**
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   ```

4. **Run Server**
   ```bash
   php artisan serve
   ```

---

### Option 2: Menggunakan Homebrew + Valet

1. **Install Requirements** (jika belum)
   ```bash
   brew install php@8.2 mysql node composer
   ```

2. **Setup Valet**
   ```bash
   composer global require laravel/valet
   valet install
   ```

3. **Clone & Setup Repository**
   ```bash
   mkdir ~/Sites
   cd ~/Sites
   git clone https://github.com/dhafinfuad/masjid-salahuddin.git
   cd masjid-salahuddin/index
   valet link
   ```

4. **Setup Laravel**
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   ```

5. **Access Application**
   - Buka browser: `http://masjid-salahuddin.test`

---

## 🐧 Instalasi (Linux)

### Menggunakan Docker (Recommended)

1. **Install Docker & Docker Compose**
   ```bash
   # Ubuntu/Debian
   sudo apt-get update
   sudo apt-get install docker.io docker-compose
   ```

2. **Clone Repository**
   ```bash
   git clone https://github.com/dhafinfuad/masjid-salahuddin.git
   cd masjid-salahuddin
   ```

3. **Setup Docker**
   ```bash
   docker-compose up -d
   ```

4. **Setup Laravel**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app cp .env.example .env
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --seed
   docker-compose exec app npm install
   docker-compose exec app npm run build
   ```

5. **Access Application**
   - Buka browser: `http://localhost:8000`

---

### Manual Setup (Ubuntu/Debian)

1. **Install PHP & Dependencies**
   ```bash
   sudo apt-get update
   sudo apt-get install php8.2 php8.2-cli php8.2-fpm php8.2-mysql \
     php8.2-mbstring php8.2-openssl php8.2-gd php8.2-fileinfo \
     mysql-server nodejs npm composer git nginx
   ```

2. **Clone Repository**
   ```bash
   git clone https://github.com/dhafinfuad/masjid-salahuddin.git
   cd masjid-salahuddin/index
   ```

3. **Setup**
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   ```

4. **Configure Nginx** (optional)
   - Copy file konfigurasi dari `/rewrite/nginx.conf`

5. **Run Server**
   ```bash
   php artisan serve
   ```

---

## 🗄️ Konfigurasi Database

### Opsi 1: MySQL (Recommended)

1. **Buat Database**
   ```sql
   CREATE DATABASE masjid_salahuddin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'masjid'@'localhost' IDENTIFIED BY 'password_anda';
   GRANT ALL PRIVILEGES ON masjid_salahuddin.* TO 'masjid'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. **Update `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=masjid_salahuddin
   DB_USERNAME=masjid
   DB_PASSWORD=password_anda
   ```

---

### Opsi 2: SQLite (Development Only)

1. **Update `.env`**
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database.sqlite
   ```

2. **Buat Database File**
   ```bash
   touch database/database.sqlite
   ```

---

## ⚙️ Konfigurasi Environment

Edit file `.env` dan sesuaikan dengan setup kamu:

```env
# APP CONFIGURATION
APP_NAME="Masjid Salahuddin"
APP_ENV=local                    # local, staging, production
APP_DEBUG=true                   # false di production
APP_KEY=base64:xxx               # Generated via php artisan key:generate
APP_URL=http://localhost:8000

# DATABASE
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=masjid_salahuddin
DB_USERNAME=root
DB_PASSWORD=

# MAIL (Optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=admin@masjid-salahuddin.local

# CACHE & SESSION
CACHE_DRIVER=file               # file, database, redis
SESSION_DRIVER=database         # file, database, cookie

# PWA & NOTIFICATION (Optional)
VAPID_PUBLIC_KEY=your_key
VAPID_PRIVATE_KEY=your_key
VAPID_SUBJECT=mailto:admin@masjid-salahuddin.local
```

---

## 🌱 Database Seeding

Seed data itu optional untuk development. Seeder akan membuat:
- Admin user
- Sample prayer schedules
- Sample events & news
- Sample staff roster

### Jalankan Seeding

```bash
# Fresh migration + seeding
php artisan migrate:fresh --seed

# Seed tanpa fresh
php artisan db:seed
```

### Credentials Setelah Seeding

- **Email**: `admin@masjid-salahuddin.local`
- **Password**: `password`

⚠️ **Ganti password setelah login pertama kali!**

---

## 🚀 Running Dev Server

### Start Development Server

```bash
# Jalankan PHP development server
php artisan serve

# Atau dengan port custom
php artisan serve --port=3000
```

Aplikasi akan berjalan di `http://localhost:8000`

### Compile Frontend Assets (Watch Mode)

Di terminal/tab lain:

```bash
# Development mode dengan watch
npm run dev

# Build untuk production
npm run build

# Build + minified
npm run build --production
```

### Running Both Simultaneously

Gunakan terminal multiplexer seperti `tmux` atau buat 2 terminal tab:

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2:**
```bash
npm run dev
```

---

## 🐛 Troubleshooting

### Problem 1: "No application encryption key has been specified"
**Solution:**
```bash
php artisan key:generate
```

### Problem 2: Database connection error
**Check:**
- MySQL/MariaDB running?
- Database credentials di `.env` benar?
- Database & user sudah dibuat?

### Problem 3: Permission denied errors (Linux/Mac)
**Solution:**
```bash
sudo chmod -R 755 storage
sudo chmod -R 755 bootstrap/cache
sudo chown -R $USER:$USER .
```

### Problem 4: Node modules atau Composer issues
**Solution:**
```bash
# Clean install PHP dependencies
rm -rf vendor
composer install

# Clean install Node dependencies
rm -rf node_modules package-lock.json
npm install
```

### Problem 5: Port 8000 sudah dipakai
**Solution:**
```bash
php artisan serve --port=8001
```

### Problem 6: Assets tidak ter-load (CSS/JS kosong)
**Solution:**
```bash
# Rebuild assets
npm run build

# Clear cache
php artisan optimize:clear
```

---

## 📞 Butuh Bantuan?

- 📖 Baca dokumentasi lain di folder `/docs`
- 💬 Tanya di [GitHub Discussions](https://github.com/dhafinfuad/masjid-salahuddin/discussions)
- 🐛 Laporkan bug di [GitHub Issues](https://github.com/dhafinfuad/masjid-salahuddin/issues)
- 📖 Lihat [Laravel Documentation](https://laravel.com/docs)

---

**Happy Coding! 🚀**
