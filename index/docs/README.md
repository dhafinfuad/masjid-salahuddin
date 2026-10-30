# 📚 Pusat Dokumentasi Teknis — Masjid Salahuddin

Dokumentasi lengkap untuk pengembang, administrator, dan agen kecerdasan buatan (AI) sistem informasi terpadu Masjid Salahuddin.

---

## 📖 Daftar Dokumen & Panduan

### 🛠️ 1. Pengembangan & Arsitektur Sistem
1. **[SETUP.md](./SETUP.md)** — Panduan Instalasi & Konfigurasi Lokal
   - Persyaratan lingkungan (PHP 8.2+, Composer, NPM, SQLite/MySQL).
   - Pengaturan berkas `.env` dan migrasi awal database.
2. **[ARCHITECTURE.md](./ARCHITECTURE.md)** — Ikhtisar Arsitektur Aplikasi
   - Struktur direktori Laravel 11 + Livewire 3 + Alpine.js.
   - Pola modularisasi sub-tab dan alur komunikasi antar komponen.
3. **[PANDUAN_ARSITEKTUR_DAN_PERFORMA.md](./PANDUAN_ARSITEKTUR_DAN_PERFORMA.md)** — Standar Performa & Optimasi
   - Praktik terbaik penanganan caching, optimasi query Eloquent, dan *lazy loading*.
4. **[LIVEWIRE_ALPINE_STATE_SYNC_GUIDELINES.md](./LIVEWIRE_ALPINE_STATE_SYNC_GUIDELINES.md)** — Sinkronisasi State Livewire & Alpine
   - Pencegahan *DOM diffing glitch*, integrasi *morphdom*, serta penanganan *action token (nonce)*.

---

### 🎨 2. Standar Desain Antarmuka (UI/UX)
1. **[UI_GUIDELINES.md](./UI_GUIDELINES.md)** — Buku Panduan Desain & Komponen UI
   - Standar palet warna resmi (*Gov-Navy*, *Gov-Gold*, *Slate*).
   - Tipografi, tata letak kartu, tombol aksi cepat, dan pencegahan *badge count number*.
   - Aturan baku modal dialog dan drawer mobile PWA.

---

### 🚀 3. Deployment & Operasional Server
1. **[SOP_DEPLOY_1PANEL_CLOUDFLARE_NGROK.md](./SOP_DEPLOY_1PANEL_CLOUDFLARE_NGROK.md)** — Prosedur Operasi Standar Deployment
   - Konfigurasi server produksi 1Panel OpenResty (Nginx).
   - Alur integrasi reverse proxy Cloudflare dan tunnel cadangan Ngrok.
   - Langkah *zero-downtime cache clearing* dan reload Nginx.

---

### 🤖 4. Panduan Otomasi & AI Assistant
1. **[AGENTS.md](./AGENTS.md)** — Panduan & Konvensi AI Assistant
   - Aturan kepatuhan arsitektur, konvensi *commit*, dan protokol *zero-regression*.

---

## 🔗 Tautan Cepat
- **[Halaman Utama Repositori (README)](../../README.md)**
- **[Aturan Utama Proyek (GEMINI.md)](../../GEMINI.md)**
- **[Repositori GitHub](https://github.com/dhafinfuad/masjid-salahuddin)**
