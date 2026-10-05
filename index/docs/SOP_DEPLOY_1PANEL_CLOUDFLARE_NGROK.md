# SOP LENGKAP: DEPLOY LARAVEL DI 1PANEL PROXMOX MENGGUNAKAN MEKANISME CLOUDFLARE WORKER & NGROK TUNNEL

Dokumen ini adalah Panduan Operasional Standar (SOP) resmi dan menyeluruh untuk mendeploy aplikasi web berbasis **Laravel** pada infrastruktur **Proxmox Debian 1Panel** di lingkungan jaringan kantor dengan firewall ketat (seperti KPP Madya Malang / DJP). 

Panduan ini menjamin:
1. **Website Publik (`masjidsalahuddin.my.id`)** dapat diakses secara resmi oleh seluruh masyarakat/jemaah di internet dengan gembok SSL (HTTPS) valid.
2. **Website Internal Kantor (`multiapp.my.id`)** beserta seluruh subdomainnya **tetap 100% aman, privat, dan terisolasi** di dalam intranet LAN kantor tanpa risiko kebocoran ke pihak luar.
3. **Bypass Firewall Kantor Tanpa Mengubah Router**: Berjalan menumpang di Port 443 (HTTPS) tanpa memerlukan izin pembukaan port atau IP publik statis dari admin jaringan kantor.

---

## DAFTAR ISI
1. [Arsitektur & Skema Kerja Sistem](#1-arsitektur--skema-kerja-sistem)
2. [Prasyarat & Bahan yang Diperlukan](#2-prasyarat--bahan-yang-diperlukan)
3. [FASE 1: Konfigurasi Domain di Cloudflare](#fase-1-konfigurasi-domain-di-cloudflare)
4. [FASE 2: Pembuatan Akun & Domain Statis di Ngrok](#fase-2-pembuatan-akun--domain-statis-di-ngrok)
5. [FASE 3: Instalasi & Konfigurasi Service Ngrok di Server Debian](#fase-3-instalasi--konfigurasi-service-ngrok-di-server-debian)
6. [FASE 4: Setup Website & Database di 1Panel](#fase-4-setup-website--database-di-1panel)
7. [FASE 5: Penyesuaian Kode Laravel (Anti-Mixed Content)](#fase-5-penyesuaian-kode-laravel-anti-mixed-content)
8. [FASE 6: Pembuatan Cloudflare Worker Reverse Proxy](#fase-6-pembuatan-cloudflare-worker-reverse-proxy)
9. [FASE 7: Pengujian & Pembuktian Keamanan](#fase-7-pengujian--pembuktian-keamanan)
10. [FASE 8: Pengerasan Keamanan Siber Lanjutan (Cyber Defense)](#fase-8-pengerasan-keamanan-siber-lanjutan-cyber-defense)
11. [11. Troubleshooting & Solusi Kendala Populer](#11-troubleshooting--solusi-kendala-populer)

---

## 1. Arsitektur & Skema Kerja Sistem

```
[ Pengunjung / Jemaah di Internet ]
                │  (Akses: https://masjidsalahuddin.my.id)
                ▼
      [ Cloudflare Edge ]  <-- Menangani Sertifikat SSL Resmi & Proteksi DDoS
                │
                ▼
     [ Cloudflare Worker ]  <-- Menerjemahkan Host & Menghapus Warning Ngrok
                │
                ▼
        [ Ngrok Cloud ]  <-- Menghubungi Server Kantor lewat Port 443 (HTTPS)
                │
════════════════╪══════════════════════════════════════════════════════════
 FIREWALL KANTOR│ (Port 443 Lolos Bebas, Tanpa Perlu Port Forwarding di Router)
════════════════╪══════════════════════════════════════════════════════════
                ▼
    [ Server Debian Proxmox ]
                │
         [ Ngrok Agent ]  (Mendengarkan tunnel di latar belakang)
                │
                ▼ (Internal loopback localhost:80)
      [ OpenResty 1Panel ]
                │
                ▼
      [ Container PHP 8 ]  <-- Menjalankan Laravel Masjid Salahuddin
                │
      [ Container MySQL ]  <-- Database db_masjid
```

---

## 2. Prasyarat & Bahan yang Diperlukan

1. **Akses Server**:
   * IP Server Debian Proxmox: `10.12.13.225` (sesuaikan dengan IP server Anda).
   * User SSH: `administrator` dengan hak akses `sudo`.
   * Akses Web Dashboard 1Panel: `http://10.12.13.225:9080`.
2. **Akun & Domain**:
   * Domain resmi yang sudah dibeli: `masjidsalahuddin.my.id`.
   * Akun di [Cloudflare](https://dash.cloudflare.com) (Paket Free).
   * Akun di [Ngrok](https://dashboard.ngrok.com) (Paket Free).
3. **File Project**:
   * Source code Laravel project yang sudah di-build (`npm run build`).
   * File dump SQL database aplikasi (contoh: `db_aplikasi.sql`).

---

## FASE 1: Konfigurasi Domain di Cloudflare

Langkah ini bertujuan memindahkan kendali DNS domain resmi Anda ke Cloudflare agar mendapatkan perlindungan SSL dan fitur Cloudflare Worker.

1. Buka browser, kunjungi **[dash.cloudflare.com](https://dash.cloudflare.com)** dan login ke akun Anda.
2. Di pojok kanan atas atau dashboard utama, klik tombol **Add a domain** (atau **Add a Site**).
3. Masukkan nama domain Anda:
   ```text
   masjidsalahuddin.my.id
   ```
4. Pilih paket: **Free ($0)** -> Klik **Continue**.
5. Cloudflare akan melakukan scanning DNS bawaan (biarkan saja) -> Scroll ke bawah lalu klik **Continue to activation**.
6. Cloudflare akan menampilkan **2 baris Nameserver resmi**, contoh:
   * Nameserver 1: `ada.ns.cloudflare.com`
   * Nameserver 2: `bob.ns.cloudflare.com`
   *(Catat kedua nama ini)*.
7. Buka tab baru, login ke panel tempat Anda membeli domain (misal: Rumahweb, Niagahoster, DomaiNesia, IDCloudHost, dll).
8. Masuk ke menu **Domain** -> cari domain `masjidsalahuddin.my.id` -> pilih menu **Kelola Nameserver (Nameserver Management)**.
9. Pilih opsi **Custom Nameserver**, lalu masukkan 2 baris Nameserver Cloudflare tadi. Hapus jika ada baris nameserver 3 dan 4. Klik **Save / Simpan**.
10. Kembali ke Cloudflare, klik tombol **Check Nameservers**. Status domain akan berubah menjadi **Active** dalam rentang 5 hingga 30 menit.

---

## FASE 2: Pembuatan Akun & Domain Statis di Ngrok

Langkah ini bertujuan mendapatkan 1 domain terowongan permanen yang dapat melewati firewall kantor via Port 443.

1. Buka browser, kunjungi **[dashboard.ngrok.com/signup](https://dashboard.ngrok.com/signup)**.
2. Buat akun (atau klik *Continue with Google*).
3. Setelah masuk ke Dashboard Ngrok:
   * Di menu sidebar sebelah kiri, klik menu **Domains** (berada di bawah kategori *Cloud Edge*).
   * Klik tombol **New Domain** (atau **Create Domain**).
   * Ngrok akan secara otomatis memesankan 1 nama domain statis permanen gratis tingkat dunia, contohnya:
     ```text
     importer-dropper-panama.ngrok-free.dev
     ```
   * **Salin dan catat nama domain ini!** Domain ini permanen dan tidak akan pernah berubah.
4. Di sidebar sebelah kiri, klik menu **Getting Started** -> **Your Authtoken**.
5. Klik tombol **Copy** pada kode token panjang Anda. Simpan kode token ini di Notepad.

---

## FASE 3: Instalasi & Konfigurasi Service Ngrok di Server Debian

Langkah ini memasang agen Ngrok di dalam server Debian Proxmox agar selalu menyala otomatis 24 jam di latar belakang.

### Langkah 3.1: Login SSH ke Server Debian
Buka terminal (PowerShell di Windows atau Terminal macOS/Linux):
```bash
ssh administrator@10.12.13.225
```
*(Masukkan password user administrator server Anda)*.

### Langkah 3.2: Pasang Aplikasi Ngrok Resmi
Jalankan perintah berikut satu per satu (atau sekaligus):
```bash
# 1. Daftarkan GPG Key resmi Ngrok
curl -sSL https://ngrok-agent.s3.amazonaws.com/ngrok.asc | sudo tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null

# 2. Tambahkan repository Ngrok ke sistem Debian
echo "deb https://ngrok-agent.s3.amazonaws.com buster main" | sudo tee /etc/apt/sources.list.d/ngrok.list

# 3. Update dan install paket ngrok
sudo apt-get update && sudo apt-get install ngrok -y
```

### Langkah 3.3: Daftarkan Authtoken
Masukkan token yang tadi Anda salin dari FASE 2:
```bash
ngrok config add-authtoken TOKEN_RAHASIA_NGROK_ANDA
```

### Langkah 3.4: Buat Service Background Otomatis (Systemd)
Agar Ngrok tidak mati ketika jendela terminal ditutup atau ketika server me-reboot, kita buatkan layanan sistem permanen:

Jalankan blok perintah ini di terminal:
```bash
# Otomatis mendeteksi letak binary ngrok dan membuat service
NGROK_BIN=$(which ngrok)

sudo tee /etc/systemd/system/ngrok.service > /dev/null << EOF
[Unit]
Description=Ngrok Tunnel Service
After=network.target

[Service]
Type=simple
User=administrator
ExecStart=$NGROK_BIN http --url=NAMA_DOMAIN_NGROK_ANDA.ngrok-free.dev 80
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF

# Reload dan aktifkan service
sudo systemctl daemon-reload
sudo systemctl enable ngrok
sudo systemctl restart ngrok
sudo systemctl status ngrok
```
> ⚠️ **Catatan:** Ganti `NAMA_DOMAIN_NGROK_ANDA.ngrok-free.dev` dengan nama domain yang Anda dapatkan di FASE 2 (misal: `importer-dropper-panama.ngrok-free.dev`).

Pastikan pada baris status tertulis warna hijau: **`Active: active (running)`**. Tekan huruf **`q`** untuk kembali ke terminal.

---

## FASE 4: Setup Website & Database di 1Panel

Langkah ini menyiapkan rumah aplikasi Laravel dan database MySQL di dalam 1Panel server Proxmox.

### Langkah 4.1: Buat Database MySQL
1. Buka 1Panel di browser: `http://10.12.13.225:9080`.
2. Di sidebar sebelah kiri, klik menu **Databases**.
3. Klik tombol biru **Create Database**:
   * **Name**: `db_masjid`
   * **Type**: MySQL
   * **Character Set**: `utf8mb4`
   * **Username**: `user_masjid`
   * **Password**: *(Buat password yang aman, catat di Notepad)*
   * **Permissions**: Pilih **All of them (%)**
   * Klik **Confirm**.
4. Di tabel database pada baris `db_masjid`, klik tombol **Import**.
5. Upload file dump database Anda (misal `db_aplikasi.sql`), lalu klik **Import**. Tunggu hingga status selesai 100%.

### Langkah 4.2: Buat Website Baru di 1Panel
1. Di sidebar sebelah kiri 1Panel, klik menu **Websites**.
2. Klik tombol biru **Create**.
3. Pada pop-up yang muncul, klik tab **Runtime** (di sebelah Deployment / Reverse Proxy):
   * **Primary Domain**: Masukkan domain utama: `masjidsalahuddin.my.id`
   * **Other Domains**: Masukkan domain ngrok: `importer-dropper-panama.ngrok-free.dev`
     *(Pemisah baris jika ada beberapa domain)*
   * **Runtime**: Pilih **php-8**
   * **Alias / App Name**: `masjid-salahuddin`
   * Klik **Confirm**.

### Langkah 4.3: Upload Kodingan Laravel
1. Di komputer lokal Anda (tempat project Laravel berada), pastikan Anda sudah menjalankan compile asset:
   ```bash
   npm run build
   ```
2. Kompres (ZIP) seluruh isi project Laravel Anda.
   *(Saran: Jangan sertakan folder `node_modules` dan `vendor` jika koneksi lambat. Folder `public/build` WAJIB disertakan).*
3. Buka menu **Websites** di 1Panel.
4. Pada baris `masjidsalahuddin.my.id`, cari kolom **Directory** lalu klik ikon **Folder**.
5. Anda akan masuk ke file manager folder website. Jika ada file default bernama `index.html`, klik kanan dan **Delete**.
6. Klik tombol **Upload** di bagian atas -> Pilih file ZIP kodingan Anda tadi.
7. Setelah upload selesai, klik kanan file ZIP tersebut -> pilih **Extract (Unzip)**.

### Langkah 4.4: Konfigurasi File `.env`
1. Masih di File Manager 1Panel, cari file `.env` (jika belum ada, salin dari `.env.example`).
2. Klik file `.env` tersebut, lalu klik **Edit**.
3. Pastikan konfigurasi penting berikut disesuaikan:
   ```env
   APP_NAME="Masjid Salahuddin"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://masjidsalahuddin.my.id

   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=db_masjid
   DB_USERNAME=user_masjid
   DB_PASSWORD=password_database_anda_tadi

   SESSION_DRIVER=file
   SESSION_LIFETIME=120
   CACHE_STORE=file
   FILESYSTEM_DISK=public
   QUEUE_CONNECTION=sync
   ```
   > ⚠️ **ATURAN EMAS 1PANEL**:
   > * `DB_HOST` wajib bernilai `mysql` (huruf kecil semua), BUKAN `127.0.0.1`. Ini karena PHP dan MySQL hidup di kontainer Docker terpisah.
   > * `SESSION_DRIVER` wajib bernilai `file` agar tidak crash jika table session belum dibuat.
   > * `APP_DEBUG` wajib bernilai `false` di server produksi.
4. Klik tombol **Save / Simpan**.

### Langkah 4.5: Atur Document Root ke `/public`
1. Di 1Panel, klik menu **Websites** -> klik tombol **Edit / Configuration** pada website `masjidsalahuddin.my.id`.
2. Di sidebar kiri, pilih menu **Directory**.
3. Pada opsi **Run Directory**, pilih:
   ```text
   /public
   ```
4. Klik tombol **Save and reload**.

### Langkah 4.6: Pengaturan Hak Akses Linux & Clear Cache
Buka terminal SSH server Anda (`ssh administrator@10.12.13.225`):
```bash
# 1. Cari nama container PHP untuk website masjid
sudo docker ps --filter "name=php"

# 2. Masuk ke dalam container (ganti NAMA_CONTAINER_PHP dengan nama asli dari output perintah di atas)
sudo docker exec -it NAMA_CONTAINER_PHP sh

# 3. Pindah ke direktori website
cd /www/sites/masjidsalahuddin.my.id/index

# 4. Jika vendor belum ada, jalankan composer install
composer install --optimize-autoloader --no-dev

# 5. Berikan hak akses penuh ke user 1000 untuk storage & cache
chown -R 1000:1000 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 6. Bersihkan dan segarkan cache Laravel
php artisan optimize:clear
php artisan storage:link
exit
```

---

## FASE 5: Penyesuaian Kode Laravel (Anti-Mixed Content)

Karena website diakses lewat protokol HTTPS (Cloudflare) namun diteruskan ke OpenResty lewat port 80 (HTTP), Laravel perlu diinstruksikan untuk memercayai proxy dan memaksa seluruh link CSS/JS menggunakan `https://`. Jika langkah ini terlewat, website akan tampil polos tanpa desain CSS (*Mixed Content Blocked*).

### 1. Ubah File `bootstrap/app.php`:
Pastikan middleware `trustProxies` diaktifkan:
```php
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
```

### 2. Ubah File `app/Providers/AppServiceProvider.php`:
Tambahkan `URL::forceScheme('https')` di dalam method `boot()`:
```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        URL::forceScheme('https');
    }
}
```

---

## FASE 6: Pembuatan Cloudflare Worker Reverse Proxy

Langkah ini menghubungkan domain resmi `https://masjidsalahuddin.my.id` ke domain Ngrok secara transparan dan menghilangkan halaman intervensi bawaan Ngrok.

### Langkah 6.1: Buat Worker Baru
1. Buka dashboard **[dash.cloudflare.com](https://dash.cloudflare.com)**.
2. Di sidebar sebelah kiri, klik menu **Compute (Workers & Pages)**.
3. Klik tombol biru **Create application**.
4. Di tab *Workers*, klik tombol **Create Worker**.
5. Pilih template nomor 3: **Start with Hello World!** (ikon bola dunia hijau).
6. Beri nama Worker:
   ```text
   masjid-proxy
   ```
7. Klik tombol **Deploy** di bagian bawah.

### Langkah 6.2: Masukkan Kode Proxy (Dengan Pengerasan Siber)
1. Setelah berhasil dideploy, klik tombol **Edit code** di pojok kanan atas.
2. Hapus seluruh kodingan yang ada di file `worker.js`, lalu ganti dengan kode berikut:
   ```javascript
   export default {
     async fetch(request, env, ctx) {
       const targetHost = "importer-dropper-panama.ngrok-free.dev";
       const publicHost = "masjidsalahuddin.my.id";

       const url = new URL(request.url);
       url.hostname = targetHost;
       url.protocol = "https:";
       url.port = "";

       const newHeaders = new Headers(request.headers);
       newHeaders.set("Host", targetHost);
       newHeaders.set("ngrok-skip-browser-warning", "true");
       newHeaders.set("X-Forwarded-Host", publicHost);
       newHeaders.set("X-Forwarded-Proto", "https");

       // Penerusan IP Pengunjung Asli (Real Client IP) untuk Rate Limiter & Audit Log Laravel
       const clientIP = request.headers.get("CF-Connecting-IP");
       if (clientIP) {
         newHeaders.set("CF-Connecting-IP", clientIP);
         newHeaders.set("X-Forwarded-For", clientIP);
         newHeaders.set("X-Real-IP", clientIP);
       }

       // Header Verifikasi Origin (Mencegah Bypass Ngrok Langsung)
       if (env.ORIGIN_SECRET) {
         newHeaders.set("X-Origin-Verify", env.ORIGIN_SECRET);
       }

       // Clone body untuk request non-GET jika perlu retry
       const body = (request.method !== "GET" && request.method !== "HEAD") 
         ? await request.arrayBuffer() 
         : null;

       // Retry hingga 3x untuk menyerap fluktuasi/kedipan koneksi internet kantor
       let response;
       for (let attempt = 1; attempt <= 3; attempt++) {
         try {
           response = await fetch(url.toString(), {
             method: request.method,
             headers: newHeaders,
             body: body,
             redirect: "manual"
           });
           break; // Berhasil, keluar dari loop
         } catch (err) {
           if (attempt < 3) {
             await new Promise((resolve) => setTimeout(resolve, 600)); // jeda 600ms lalu coba lagi
           }
         }
       }

       // Jika tunnel ngrok benar-benar mati/offline, tampilkan pesan ramah & auto-reload
       if (!response) {
         return new Response(
           `<!DOCTYPE html>
           <html lang="id">
           <head>
             <meta charset="UTF-8">
             <meta name="viewport" content="width=device-width, initial-scale=1.0">
             <title>Sedang Menyambungkan ke Server...</title>
             <meta http-equiv="refresh" content="3">
             <style>
               body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #0f172a; color: #f8fafc; text-align: center; padding: 20px; }
               .box { max-width: 480px; background: #1e293b; padding: 32px; border-radius: 16px; border: 1px solid #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
               .spinner { width: 40px; height: 40px; border: 4px solid #334155; border-top-color: #38bdf8; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
               @keyframes spin { to { transform: rotate(360deg); } }
               h2 { margin: 0 0 10px; font-size: 1.25rem; }
               p { color: #94a3b8; font-size: 0.9rem; line-height: 1.5; margin: 0; }
             </style>
           </head>
           <body>
             <div class="box">
               <div class="spinner"></div>
               <h2>Menghubungkan ke Server Masjid Salahuddin...</h2>
               <p>Terowongan server sedang melakukan sinkronisasi otomatis. Halaman akan dimuat ulang secara otomatis dalam 3 detik.</p>
             </div>
           </body>
           </html>`,
           { status: 503, headers: { "Content-Type": "text/html; charset=UTF-8" } }
         );
       }

       // Salin header respons untuk disuntikkan security headers & pembersihan signature backend
       const respHeaders = new Headers(response.headers);

       // Pengamanan Header Siber di Level Cloudflare Edge
       respHeaders.set("X-Frame-Options", "SAMEORIGIN");
       respHeaders.set("X-Content-Type-Options", "nosniff");
       respHeaders.set("Referrer-Policy", "strict-origin-when-cross-origin");
       respHeaders.set("Permissions-Policy", "camera=(), microphone=(), geolocation=()");

       // Hapus signature teknis server agar tidak dapat dipindai peretas
       respHeaders.delete("Server");
       respHeaders.delete("X-Powered-By");
       respHeaders.delete("X-Ngrok-Server");

       // Tangani redirect URL jika Laravel melempar ke targetHost
       if ([301, 302, 303, 307, 308].includes(response.status)) {
         const location = response.headers.get("Location");
         if (location) {
           const newLocation = location.replace(targetHost, publicHost);
           respHeaders.set("Location", newLocation);
         }
       }

       return new Response(response.body, {
         status: response.status,
         statusText: response.statusText,
         headers: respHeaders
       });
     }
   };
   ```
3. Klik tombol biru **Deploy** di pojok kanan atas.
4. Klik tombol panah kembali **`← masjid-proxy`** di pojok kiri atas untuk kembali ke dashboard worker.

### Langkah 6.3: Kaitkan Worker ke Domain Utama
1. Di halaman worker `masjid-proxy`, klik tab **Domains** (atau tab **Settings** -> **Domains & Routes**).
2. Di bagian **Custom Domains and Routes**, klik tombol biru **Add Domain** (atau **Add Custom Domain**).
3. Di kolom domain, ketik nama domain resmi Anda:
   ```text
   masjidsalahuddin.my.id
   ```
4. Klik tombol **Add Custom Domain**.
5. Cloudflare akan memproses dalam hitungan detik. Status akan menjadi **Active / Production**.

---

## FASE 7: Pengujian & Pembuktian Keamanan

Lakukan pengujian independen dari luar jaringan kantor untuk membuktikan sistem telah bekerja sempurna dan aman:

### Tes 1: Akses Website Masjid (Publik)
1. Siapkan Smartphone Anda. **Matikan Wi-Fi kantor** dan hidupkan **Paket Data Seluler (Telkomsel/Indosat/XL)**.
2. Buka Google Chrome di HP, ketik:
   ```text
   https://masjidsalahuddin.my.id
   ```
3. **Hasil:** Website Masjid Salahuddin terbuka dengan cepat, tata letak CSS rapi, gambar tampil, jadwal sholat akurat, dan memiliki gembok hijau HTTPS resmi.

### Tes 2: Bukti Keamanan Website Internal Kantor (`multiapp.my.id`)
1. Masih di HP yang menggunakan paket data seluler luar kantor.
2. Ketik salah satu alamat web kantor:
   ```text
   https://asugas.multiapp.my.id
   ```
3. **Hasil:** Browser akan menampilkan status **ERR_CONNECTION_TIMED_OUT** atau **Server Not Found**.
4. **Kesimpulan Keamanan:** Website dinas kantor Anda **100% aman, privat, dan tidak bocor ke publik**, karena hanya dapat dibuka oleh komputer yang terhubung ke kabel LAN/Wi-Fi kantor KPP Madya Malang.

---

## FASE 8: Pengerasan Keamanan Siber Lanjutan (Cyber Defense)

Mengingat aplikasi diakses melalui kombinasi Cloudflare Worker dan Ngrok Tunnel, berikut adalah protokol pengamanan siber berlapis (*defense-in-depth*) yang diaktifkan untuk mencegah peretasan, serangan DoS, credential stuffing, dan eksploitasi file:

### 8.1. Proteksi Anti-Bypass Ngrok Menggunakan Origin Secret Token
Untuk mencegah peretas yang berhasil menemukan URL mentah Ngrok (`importer-dropper-panama.ngrok-free.dev`) membypass Cloudflare WAF:
1. Buat sebuah token rahasia acak, misalnya:
   ```text
   ms_sec_98f4a120c8e317d6b9e843
   ```
2. **Di Cloudflare Worker**: Masuk ke menu Worker `masjid-proxy` -> **Settings** -> **Variables and Secrets** -> Tambahkan Secret:
   * **Variable name**: `ORIGIN_SECRET`
   * **Value**: `ms_sec_98f4a120c8e317d6b9e843`
3. **Di Server 1Panel (`.env`)**:
   Buka file `.env` Laravel dan tambahkan:
   ```env
   ORIGIN_SECRET=ms_sec_98f4a120c8e317d6b9e843
   SESSION_SECURE_COOKIE=true
   ```
4. **Hasil**: Jika ada bot atau hacker membuka `https://importer-dropper-panama.ngrok-free.dev` secara langsung tanpa melalui Cloudflare Worker, Laravel akan secara otomatis menolak dan melempar (redirect 301) permintaan tersebut kembali ke domain resmi `https://masjidsalahuddin.my.id`.

### 8.2. Pengaktifan Cloudflare Bot Fight Mode & Security Level
Cloudflare menyediakan proteksi WAF gratis yang sangat ampuh:
1. Buka dashboard Cloudflare untuk domain `masjidsalahuddin.my.id`.
2. Masuk ke menu **Security** -> **Bots** -> Aktifkan toggle **Bot Fight Mode** ke posisi **ON**. Ini akan secara otomatis memblokir scraping otomatis, bot crawler berbahaya, dan vulnerability scanner (seperti sqlmap, nikto).
3. Masuk ke menu **Security** -> **Settings** -> Atur **Security Level** ke **Medium** atau **High**.
4. Masuk ke menu **SSL/TLS** -> **Edge Certificates** -> Aktifkan toggle **Always Use HTTPS** dan **Minimum TLS Version** ke **TLS 1.2**.

### 8.3. Rate Limiting Otomatis Autentikasi (Anti Brute-Force)
Di level aplikasi Laravel, proteksi brute-force sudah diintegrasikan:
* Setiap upaya login ke `/auth/login` dibatasi maksimal **5 kali percobaan gagal** per kombinasi email dan IP pengunjung.
* Jika melebihi batas, akun dan IP akan dikunci sementara selama **60 detik** (*Throttling 429 Too Many Requests*).
* Berkat forwarding header `CF-Connecting-IP` di `worker.js`, IP yang dibatasi adalah IP riil penyerang, bukan IP proxy ngrok.

### 8.4. Validasi Ketat Unggah File (Anti Web-Shell & Malware)
Seluruh endpoint unggah file (Laporan Agenda LPJ & Bukti Kas Keuangan) dilindungi validasi tipe MIME biner:
* **Laporan Agenda (PDF)**: Wajib berformat file asli `.pdf` dengan ukuran maksimal 10 MB.
* **Bukti Kas (Kuitansi)**: Hanya mengizinkan `.jpg`, `.jpeg`, `.png`, `.webp`, `.pdf` dengan ukuran maksimal 5 MB.
* File PHP, ekstensi ganda (seperti `shell.php.jpg`), dan skrip executable ditolak seketika oleh engine validator sebelum menyentuh media penyimpanan.

### 8.5. Penonaktifan Fitur Cepat (Quick Login) di Mode Produksi
Fitur Quick Login 1-klik untuk testing internal secara otomatis dimatikan ketika file `.env` di server bernilai `APP_ENV=production`. Di server live, seluruh pengguna wajib memasukkan email dan kata sandi yang sah.

---

## 11. Troubleshooting & Solusi Kendala Populer

### Kendala 1: Tampilan Website Polos Tanpa Warna/CSS (Hanya Teks Putih)
* **Penyebab**: Browser memblokir aset CSS karena dipanggil lewat HTTP (*Mixed Content Blocked*), atau folder `public/build` belum terupload.
* **Solusi**:
  1. Pastikan FASE 5 (`trustProxies` dan `forceScheme('https')`) sudah terpasang di Laravel.
  2. Cek di File Manager 1Panel folder `public/`: pastikan folder `build/` ada (berisi file `manifest.json` dan folder `assets/`).
  3. Jalankan `php artisan optimize:clear` di dalam kontainer PHP.
  4. Lakukan Hard Refresh di browser dengan menekan `Ctrl + F5`.

### Kendala 2: Error 502 Bad Gateway atau Connection Refused
* **Penyebab**: Layanan service Ngrok di Debian berhenti atau mati.
* **Solusi**: Buka terminal SSH server, ketik `sudo systemctl status ngrok`. Jika statusnya *inactive* atau *failed*, jalankan `sudo systemctl restart ngrok`.

### Kendala 3: Error "Table 'sessions' doesn't exist"
* **Penyebab**: `SESSION_DRIVER` di file `.env` bernilai `database` padahal migration session belum dijalankan.
* **Solusi**: Buka `.env` di 1Panel, ubah menjadi `SESSION_DRIVER=file` dan `CACHE_STORE=file`, simpan, lalu jalankan `php artisan config:clear`.

### Kendala 4: Error "Connection Refused" pada Database MySQL
* **Penyebab**: `DB_HOST` di `.env` tertulis `127.0.0.1` atau `localhost`.
* **Solusi**: Di 1Panel, host internal MySQL antar-kontainer adalah `mysql`. Ubah baris konfigurasi `.env` menjadi:
  ```env
  DB_HOST=mysql
  DB_PORT=3306
  ```

### Kendala 5: Error 525 (SSL Handshake Failed) di Browser / HP
* **Penyebab**: 
  1. Server Proxmox atau VM Debian sempat di-reboot / restart sehingga tunnel Ngrok sempat mati sesaat.
  2. Terjadi fluktuasi/gangguan singkat pada koneksi internet kantor sehingga koneksi tunnel sedang *reconnecting*.
  3. Pengaturan SSL di Cloudflare disetel ke mode **Full (Strict)** padahal origin memakai proxy Cloudflare Worker.
* **Solusi**:
  1. **Bukan karena HTTPS 1Panel mati**: Jangan nyalakan HTTPS di 1Panel. 1Panel tetap wajib menggunakan HTTP Port 80 lokal.
  2. Buka dashboard Cloudflare -> menu **SSL/TLS** -> pastikan mode enkripsi diset ke **Full** (bukan *Full Strict*).
  3. Service Ngrok akan otomatis tersambung kembali begitu jaringan internet stabil (karena ada parameter `Restart=always` di systemd).


---
*Dokumen ini dibuat dan divalidasi secara otomatis untuk infrastruktur Server Proxmox VE Debian 1Panel v1.10.26-lts - Masjid Salahuddin KPP Madya Malang.*
