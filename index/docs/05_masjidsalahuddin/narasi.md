**PANEL PEGAWAI**  
Sistem Informasi Manajemen Kepegawaian Terpadu: Pendataan Demografi Aparatur, Dasbor Infografis Komposisi Organisasi, Tata Kelola Penunjukan PLH/PLT, & Sinkronisasi Berkas Kepegawaian.

Python 3.11  
Flask  
SQLAlchemy  
Tailwind CSS  
Jinja2  
HTMX  
Chart.js  
Leaflet.js  
PyMySQL  
Docker  

### STUDI KASUS ARSITEKTUR
**Panel Pegawai — Ekosistem Pendataan Kepegawaian, Visualisasi Demografi, & Manajemen Penunjukan PLH/PLT**  
✕   

#### Latar Belakang
Pengelolaan data aparatur di lingkungan kantor sebelumnya masih mengandalkan lembar kerja spreadsheet manual yang tersebar, memicu kendala inkonsistensi demografi antar-seksi, sulitnya monitoring komposisi organisasi secara *real-time*, serta minimnya sistem pelacakan masa berlaku surat penunjukan PLH/PLT.

Sistem **Panel Pegawai** hadir sebagai *single source of truth* berbasis web untuk mendigitalisasi dan mengonsolidasi seluruh data aparatur, infografis analitik demografi, tata kelola siklus hidup mandat PLH/PLT, serta sinkronisasi berkas data berkala secara terpusat.

---

#### Cakupan 4 Modul Utama Aplikasi

**1. Pendataan Master Pegawai & Demografi**  
**DATA PEGAWAI**  
Manajemen data aparatur terpadu (NIP 18 & 9 digit, seksi, jabatan, dan riwayat penempatan). Dilengkapi *live search* instan berbasis HTMX, filter multi-status, tombol salin NIP, serta form modal CRUD interaktif tanpa muat ulang halaman.

**2. Dasbor Infografis & Analitik Eksekutif**  
**DASHBOARD**  
Pusat visualisasi data komposisi kepegawaian dengan 4 metrik KPI utama serta 6 grafik analitik interaktif Chart.js (seksi, jabatan, usia, pendidikan, agama, dan masa kerja). Dilengkapi peta interaktif Leaflet.js untuk pemetaan demografis asal kelahiran pegawai.

**3. Tata Kelola Penunjukan PLH/PLT**  
**PLH & PLT**  
Administrasi penugasan Pelaksana Harian dan Pelaksana Tugas dengan pelacakan siklus hidup mandat otomatis (*Aktif, Terjadwal, Selesai, Non-Aktif*). Memudahkan pemantauan masa berlaku surat tugas dan kepastian delegasi kewenangan jabatan.

**4. Sinkronisasi Berkas Kepegawaian**  
**IMPORT & EXPORT**  
Fasilitas impor cerdas spreadsheet (Excel/CSV) yang mengenali pegawai via NIP Pendek dan melakukan *selective upsert* tanpa menimpa data personal sensitif. Menyediakan ekspor laporan rekapitulasi data resmi kantor secara instan.

---

#### Spesifikasi Rekayasa & Tata Kelola Keamanan

* **Modern Python Stack & HTMX Reactive UI**  
  Dibangun dengan arsitektur modular Python 3.11 dan **Flask** (WSGI), **SQLAlchemy** ORM, template **Jinja2**, serta utilitas **Tailwind CSS**. Menggunakan **HTMX** untuk interaktivitas reaktif yang cepat tanpa beban framework frontend yang berat.

* **Single Source of Truth, RBAC, & Database Terpadu**  
  Terintegrasi dengan database MySQL bersama (`db_aplikasi`) dan otentikasi NIP Pendek terenkripsi via **Flask-Login** & **Bcrypt**. Menerapkan kontrol akses berbasis peran (RBAC) ketat untuk menjamin integritas data kepegawaian.

* **Smart Sync Engine & Docker Deployment**  
  Didukung mesin impor sinkronisasi berbasis **Pandas & openpyxl** untuk validasi data otomatis dan pencegahan duplikasi. Seluruh aplikasi diorkestrasikan dengan **Docker Compose & Gunicorn** untuk kemudahan pemeliharaan di server lokal.
