**MASJID SALAHUDDIN**  
Sistem Informasi Manajemen Masjid & Portal Jamaah Terpadu: Penjadwalan Shalat & Petugas, Kalender Kajian & Notula Eksekutif, Akuntansi Kas & Komitmen Program Sosial, TV Digital Signage, serta PWA Offline-Capable.

PHP 8.3  
Laravel 11  
Livewire 3  
Alpine.js  
Tailwind CSS  
MySQL  
PWA (Service Worker)  
Chart.js  
DomPDF  
1Panel (Docker & Nginx)  

### STUDI KASUS ARSITEKTUR
**Masjid Salahuddin — Ekosistem Digital Kemakmuran Masjid, Transparansi Finansial, & Layanan Terpadu Jamaah**  
✕   

#### Latar Belakang
Pengelolaan operasional peribadatan dan administrasi Masjid Salahuddin di lingkungan KPP Madya Malang sebelumnya masih menghadapi tantangan fragmentasi data: jadwal sholat dan penugasan imam/muadzin dicatat pada lembar terpisah, pencatatan infaq dan sedekah rentan terlambat dilaporkan kepada jamaah, publikasi agenda kajian belum terpusat, serta ketiadaan media informasi digital dinamis yang dapat menyinkronkan hitung mundur adzan dan iqomah secara tepat waktu di ruang utama sholat.

Sistem **Masjid Salahuddin** dirancang sebagai ekosistem digital komprehensif berbasis web dan Progressive Web App (PWA) untuk menyatukan seluruh tata kelola peribadatan, transparansi akuntansi keuangan kas, manajemen komitmen program sosial, publikasi risalah notula dakwah, hingga integrasi layar TV Digital Signage interaktif yang terhubung langsung dengan mesin hisab astronomis akurat Kemenag RI.

---

#### Cakupan 5 Modul Utama Aplikasi

**1. Portal Publik Jamaah & Layanan PWA Offline**  
**PORTAL JAMAAH**  
Pusat informasi publik responsif yang dapat diakses melalui peramban desktop maupun smartphone (PWA). Menyajikan waktu sholat hisab harian untuk 500+ kota di Indonesia, widget petugas sholat aktif, kalender kajian pekanan/tematik, pembaca notula dakwah interaktif dengan fitur pengaturan ukuran huruf, verifikasi legalitas SK Takmir resmi, hingga kanal aspirasi dua arah Kotak Saran dengan transparansi tindak lanjut DKM.

**2. Manajemen Peribadatan & Penugasan Petugas Sholat**  
**JADWAL & PETUGAS SHALAT**  
Automasi penjadwalan waktu shalat sepanjang tahun yang diperkuat integrasi hisab astronomis Kemenag dan perhitungan hisab astronomi lokal. Dilengkapi matriks penugasan imam, muadzin harian (Dzuhur & Ashar), serta petugas Shalat Jumat (Khatib, MC, Bilal). Memiliki fitur cetak roster bulanan resmi ber-QR Code dan broadcast jadwal otomatis via WhatsApp.

**3. Akuntansi Keuangan Kas & Komitmen Program Sosial**  
**KEUANGAN & PROGRAM SOSIAL**  
Tata kelola pembukuan kas masjid berbasis multi-kategori anggaran dengan rekonsiliasi saldo otomatis, bukti transaksi terverifikasi, dan generator laporan keuangan siap cetak berstandar akuntansi DKM. Mengelola program sosial dan santunan anak yatim lengkap dengan buku donatur, modul komitmen infaq rutin, serta integrasi rekonsiliasi setoran/kliring payroll internal pegawai kantor.

**4. TV Digital Signage (Layar Informasi Masjid)**  
**TV DISPLAY SIGNAGE**  
Antarmuka Full HD (1080p landscape) khusus monitor TV ruang utama masjid yang beroperasi 24/7 tanpa jeda muat ulang (*zero-refresh*). Menampilkan jam digital hisab akurat, waktu imsak, hitung mundur adzan & jeda iqomah dengan transisi layar sholat tertib (*screen blackout*), agenda kegiatan berjalan, serta running text pengumuman dan himbauan adab masjid.

**5. Dashboard Eksekutif Takmir & Poster Studio Otomatis**  
**ADMIN DASHBOARD & TOOLS**  
Panel kendali terpusat bagi jajaran pengurus DKM dengan dasbor metrik KPI keuangan, manajemen penceramah/ustadz, administrasi komunitas One Day One Juz (ODOJ), analitik lalu lintas jamaah, serta generator publikasi pamflet dakwah digital (Poster Studio) yang mengonversi jadwal kajian menjadi grafis siap sebar dalam hitungan detik.

---

#### Spesifikasi Rekayasa & Tata Kelola Keamanan

* **Modern Full-Stack Reactive Architecture (Livewire 3 + Alpine.js)**  
  Dibangun dengan fondasi **PHP 8.3** dan framework **Laravel 11**, memanfaatkan **Livewire 3** untuk rendering server-side yang cepat dipadukan dengan **Alpine.js** untuk interaktivitas 0ms bebas flicker (*optimistic UI*). Seluruh antarmuka dirancang elegan dengan sistem desain korporat **Tailwind CSS**.

* **Audit Trail, RBAC, & Single Source of Truth**  
  Menerapkan kontrol akses berbasis peran (RBAC) ketat yang membedakan hak akses Super Administrator, Pengurus DKM, dan Jamaah Pegawai. Mendukung verifikasi identitas resmi dengan validasi email kedinasan, proteksi sesi ganda, sanitasi payload formulir keuangan, serta enkripsi kata sandi standar industri.

* **Progressive Web App (PWA) & Infrastruktur 1Panel**  
  Dilengkapi modul Service Worker cerdas untuk *offline caching*, memungkinkan jamaah tetap dapat membuka panduan ibadah dan jadwal shalat meskipun koneksi internet terputus. Sistem dideploy secara andal menggunakan **Docker Nginx & MySQL** di lingkungan server **1Panel** dengan dukungan backup terjadwal dan efisiensi konsumsi memori tinggi.
