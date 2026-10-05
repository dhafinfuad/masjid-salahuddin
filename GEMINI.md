# Aturan & Panduan Pengembangan Proyek — Masjid Salahuddin

Dokumen ini merupakan panduan kerja utama bagi AI Assistant (Antigravity/Gemini) dalam mengelola, mengembangkan, dan memelihara proyek **Masjid Salahuddin** (`1panel-masjidsalahuddin`). Seluruh aturan di bawah ini bersifat **wajib (mandatory)** dan harus ditaati secara otomatis tanpa perlu diingatkan berulang kali oleh pengguna di chat.

---

## ⚡ 1. Aturan Wajib: Otomatis Git Commit (Automatic Commit Mandate)

> [!IMPORTANT]
> **Setiap kali selesai mengubah, membuat, atau menghapus file kode dalam proyek ini, AI WAJIB langsung melakukan Git Commit secara otomatis tanpa menunggu instruksi eksplisit dari pengguna.**

### Alur Kerja Git Otomatis:
1. **Selesaikan Pekerjaan Kode**: Buat atau edit berkas sesuai kebutuhan pengguna.
2. **Periksa Status Git**: Pastikan file kerja bersih dan tidak ada file sensitif yang bocor.
3. **Stage Perubahan**:
   ```bash
   git add .
   ```
4. **Commit dengan Pesan Jelas & Terstruktur**:
   Gunakan format *conventional commit* dalam Bahasa Indonesia atau Inggris yang deskriptif:
   - `feat: ...` untuk fitur baru
   - `fix: ...` untuk perbaikan bug / error
   - `style: ...` untuk perubahan tampilan / styling UI
   - `docs: ...` untuk pembaruan panduan / dokumentasi
   - `refactor: ...` untuk restrukturisasi atau perapihan kode
   - `chore: ...` untuk update konfigurasi / dependensi
   *Contoh:* `git commit -m "docs: tambahkan panduan aturan proyek GEMINI.md"`
5. **Kirim ke GitHub (Push)**:
   Kirim hasil commit ke cabang utama (`main`) di GitHub agar repositori remote selalu sinkron:
   ```bash
   git push origin main
   ```
6. **Laporkan Hasil**:
   Beritahukan kepada pengguna ringkasan file yang diubah dan bukti bahwa perubahan sudah tersimpan rapi di Git & GitHub.

---

## 🔒 2. Aturan Keamanan & Perlindungan Data Sensitif

Repositori ini berstatus **Publik** di GitHub ([https://github.com/dhafinfuad/masjid-salahuddin](https://github.com/dhafinfuad/masjid-salahuddin)). Oleh karena itu:

1. **DILARANG KERAS Men-commit File Rahasia**:
   - File `.env`, `.env.local`, atau variasi file environment lainnya (hanya `.env.example` yang boleh di-commit).
   - Private key atau sertifikat SSL di folder `ssl/` (`*.key`, `*.pem`, `*.crt`).
   - File log web server di folder `log/` (`access.log`, `error.log`) dan `storage/logs/`.
   - Berkas keuangan internal atau spreadsheet infaq (`*.xlsx`, `*.xls`, `*.csv`) serta backup database di `storage/app/backups/`.
   - Kredensial, API key, auth token, atau password yang di-hardcode dalam kode.
2. **Patuhi File `.gitignore`**:
   Pastikan [.gitignore](.gitignore) selalu memfilter folder `vendor/`, `node_modules/`, `storage/framework/sessions/`, `storage/framework/views/`, dan file cache lainnya.

---

## 🎨 3. Aturan Desain & Komponen UI (User Mandate)

1. **DILARANG KERAS Menggunakan Badge "Hari Ini" atau Penanda Waktu Serupa**:
   - Jangan pernah menambahkan badge teks penanda waktu seperti `Hari Ini` pada jadwal sholat, kartu kegiatan, atau daftar kalender.
   - Pembedaan baris hari ini dilakukan melalui penempatan baris di urutan paling atas (Row #1) dan pewarnaan baris halus (misal border kiri warna amber).
2. **DILARANG KERAS Menggunakan Badge Count Number**:
   - Jangan pernah menambahkan badge angka hitungan (misal badge counter jumlah item pada tab navigasi, label counter di samping judul, badge notifikasi jumlah baris, dsb.).
3. **Gaya Penataan & Diferensiasi Visual**:
   - Prioritaskan penataan urutan/posisi data (misal data hari ini ditaruh di paling atas).
   - Tampilan harus tetap bersih, rapi, elegan, dan profesional tanpa elemen dekoratif berlebihan.

---

## 💬 4. Gaya Komunikasi dengan Pengguna

1. **Ramah, Sabar, dan Bertahap**:
   - Pengguna masih awam tentang konsep teknis Git dan GitHub.
   - Jelaskan langkah-langkah secara pelan, runtut, dan mudah dipahami tanpa membebani dengan istilah teknis rumit.
   - Berikan analogi sederhana bila menjelaskan alur teknis yang kompleks.
2. **Gunakan Bahasa Indonesia yang Baik dan Santun**.
