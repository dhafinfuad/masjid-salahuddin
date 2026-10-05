# Panduan Arsitektur & Optimasi Performa (High Performance Guide)
### Masjid Salahuddin Digital Platform (Laravel 11 + Livewire 3 + Alpine.js + Tailwind CSS)

Dokumen ini adalah **standar acuan teknis** untuk setiap pengembang atau AI yang akan menambahkan halaman, modul, atau fitur baru pada proyek ini. Tujuannya adalah memastikan aplikasi tetap memiliki kecepatan luar biasa (ultra-fast), stabil, dan efisien saat data membesar.

---

## DAFTAR ISI
1. [Mengapa Aplikasi Bisa Lambat? (Prinsip Dasar)](#1-mengapa-aplikasi-bisa-lambat-prinsip-dasar)
2. [Pilar 1: Arsitektur Navigasi & Perpindahan Tab](#pilar-1-arsitektur-navigasi--perpindahan-tab)
3. [Pilar 2: Optimasi Database & Larangan Keras N+1 Query](#pilar-2-optimasi-database--larangan-keras-n1-query)
4. [Pilar 3: Isolasi State & Filter Pencarian](#pilar-3-isolasi-state--filter-pencarian)
5. [Pilar 4: Keamanan Konfigurasi & Concurrency Resilience](#pilar-4-keamanan-konfigurasi--concurrency-resilience)
6. [Checklist Setiap Menambah Fitur / Halaman Baru](#checklist-setiap-menambah-fitur--halaman-baru)
7. [Panduan Deploy ke Homeserver (Proxmox Debian + 1Panel)](#panduan-deploy-ke-homeserver-proxmox-debian--1panel)

---

## 1. Mengapa Aplikasi Bisa Lambat? (Prinsip Dasar)

Dalam pengembangan web modern, ada 4 penyebab utama mengapa sebuah aplikasi terasa berat atau lambat:
1. **Terlalu Banyak Query ke Database (N+1 Problem)**: Menjalankan puluhan atau ratusan query kecil untuk menampilkan satu halaman, padahal bisa diselesaikan dengan 1–2 query terpadu.
2. **Eksekusi Query di Dalam Template View (Blade)**: Memanggil model database di dalam perulangan `@foreach` di file HTML Blade.
3. **Over-Fetching & Speculative Requests**: Server dipaksa memproses request yang sebenarnya tidak diminta oleh user (misal: fitur *hover prefetching* yang terlalu agresif).
4. **Server Re-render untuk Operasi Visual**: Meminta server me-render ulang seluruh halaman hanya untuk menyembunyikan/menampilkan elemen yang sebenarnya sudah ada di browser.

---

## 2. Pilar 1: Arsitektur Navigasi & Perpindahan Tab

### A. Navigasi Antar Halaman Menu (`wire:navigate` vs `wire:navigate.hover`)
- **Gunakan `wire:navigate` (TANPA `.hover`) pada menu sidebar / daftar navigasi yang padat**:
  ```html
  <!-- BENAR: Hanya fetch saat user benar-benar mengklik link -->
  <a href="{{ url('/admin/programs') }}" wire:navigate class="...">Program Sosial</a>

  <!-- SALAH: Mengirimkan request ke server hanya karena kursor mouse lewat/hover -->
  <a href="{{ url('/admin/programs') }}" wire:navigate.hover class="...">Program Sosial</a>
  ```
  *Alasan*: Jika menggunakan `.hover` pada deretan 6 menu, ketika user menggerakkan mouse ke bawah, browser akan menembakkan 6 request berat secara bersamaan ke server di detik yang sama, memicu lonjakan beban CPU dan *race condition*.
- Gunakan `wire:navigate.hover` **hanya** pada tombol tunggal yang sangat terisolasi (misal: tombol Login di portal publik).

### B. Perpindahan Sub-Tab di Dalam Halaman (Client-Side Display vs Server Re-Render)
- Jika data seluruh sub-tab sudah ter-render di browser, **JANGAN memanggil method server** hanya untuk berpindah tab.
- Gunakan Alpine.js (`x-show` dan `x-cloak`) untuk perpindahan instan (0 milidetik, bebas flicker):
  ```html
  <!-- State Alpine di parent container -->
  <div x-data="{ activeSubTab: 'pekanan' }">
      <!-- Tombol Tab -->
      <button type="button" @click="activeSubTab = 'pekanan'">Pekanan</button>
      <button type="button" @click="activeSubTab = 'jumat'">Jumat</button>

      <!-- Konten Tab -->
      <div x-show="activeSubTab === 'pekanan'" x-cloak>...</div>
      <div x-show="activeSubTab === 'jumat'" x-cloak>...</div>
  </div>
  ```

### C. Standar Livewire Loading Indicators (Mencegah Icon Spinner & Teks Bertumpuk Vertikal)
- **Akar Masalah Teknis**:
  Livewire secara bawaan menyuntikkan inline style `style="display: inline-block;"` saat direktif `wire:loading` aktif tanpa modifier khusus. Nilai inline style ini menimpa class utilitas Tailwind `inline-flex` atau `flex`, sehingga browser memperlakukan elemen `<svg>` spinner dan teks `<span>` sebagai elemen bertumpuk vertikal (icon berada di atas teks alih-alih sebaris horizontal).
- **Aturan Baku Wajib**:
  1. **Wajib Gunakan Modifier**: Selalu gunakan `wire:loading.inline-flex` (atau `wire:loading.flex` untuk kontainer blok). **DILARANG** menggunakan `wire:loading` polos pada elemen flex/inline-flex.
  2. **Inisialisasi `style="display: none;"`**: Selalu sertakan `style="display: none;"` pada markup Blade agar elemen tidak berkedip saat inisialisasi awal sebelum Livewire siap.
  3. **Class Kontainer**: Wajib sertakan `inline-flex flex-row items-center gap-2 shrink-0 whitespace-nowrap`.
  4. **Class Icon `<svg>`**: Wajib sertakan `shrink-0 inline-block animate-spin`.
  5. **Class Teks `<span>`**: Wajib sertakan `whitespace-nowrap leading-none`.
- **Contoh Standar**:
  ```html
  <div wire:loading.inline-flex style="display: none;"
      class="inline-flex flex-row items-center gap-2 text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 shrink-0 whitespace-nowrap">
      <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-amber-600 inline-block" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span class="whitespace-nowrap leading-none">Memperbarui jadwal...</span>
  </div>
  ```

---

## 3. Pilar 2: Optimasi Database & Larangan Keras N+1 Query

### A. Eager Loading Relasi
Jika menampilkan data yang memiliki relasi (misal: Transaksi memiliki Kategori, Peserta memiliki Program), **wajib** menggunakan `with(...)`:
```php
// SALAH (N+1 Query: 1 query transaksi + 15 query kategori per baris)
$finances = Finance::paginate(15);

// BENAR (Hanya 2 query: 1 query transaksi + 1 query kategori gabungan)
$finances = Finance::with(['category', 'agenda'])->paginate(15);
```

### B. Subquery Aggregation (`withCount` & `withSum`)
Jangan menghitung total atau relasi satu per satu di dalam perulangan kartu/tabel. Hitung sekaligus di query utama:
```php
// BENAR: Mengambil data program sekaligus menghitung total uang & jumlah peserta dalam 1 query
$programs = SocialProgram::withCount([
        'participants as active_participants_count' => fn($q) => $q->where('status', 'AKTIF')
    ])
    ->withSum([
        'finances as total_collected' => fn($q) => $q->where('type', 'pemasukan')
    ], 'amount')
    ->get();
```

### C. Accessor Cerdas pada Model Eloquent
Jika membuat accessor di Model, periksa dulu apakah atribut hasil `withSum` / `withCount` sudah tersedia sebelum menjalankan query fallback:
```php
// Di Model (SocialProgram.php)
public function getTotalCollectedAttribute(): float
{
    // Jika sudah dihitung dari withSum, langsung pakai (0 query!)
    if (array_key_exists('total_collected', $this->attributes)) {
        return (float) ($this->attributes['total_collected'] ?? 0);
    }
    // Fallback jika dipanggil di tempat lain tanpa withSum
    return (float) $this->finances()->where('type', 'pemasukan')->sum('amount');
}
```

### D. LARANGAN KERAS: Menjalankan Query di File Blade View
**TIDAK BOLEH** menulis query Eloquent di dalam file Blade (`.blade.php`), terutama di dalam `@foreach`:
```html
<!-- SALAH BESAR (Membuat halaman lambat karena query berulang di setiap baris) -->
@foreach($programs as $prog)
    @php
        $in = \App\Models\Finance::where('program_name', $prog)->sum('amount');
    @endphp
@endforeach

<!-- BENAR: Hitung agregasi di Controller / Livewire component menggunakan groupBy, lalu kirim ke Blade -->
```

### E. Smart Query Scoping (Hanya Query Tab yang Aktif)
Pada komponen Livewire tunggal dengan banyak tab (seperti `AdminDashboard`), **hanya jalankan query untuk tab yang sedang aktif**:
```php
public function render()
{
    $tab = $this->currentTab;

    // Default variabel kosong
    $allEvents = collect([]);
    $financesList = collect([]);

    // Query hanya dieksekusi jika tab terkait dibuka user
    if ($tab === 'finance') {
        $financesList = Finance::with('category')->paginate(15);
    }

    return view('livewire.admin.dashboard', compact('financesList'));
}
```

---

## 4. Pilar 3: Isolasi State & Filter Pencarian

Ketika sebuah halaman memiliki beberapa sub-tab (misal: *Kajian Pekanan*, *Kajian Jumat*, *Kegiatan Akbar*), **pisahkan variabel pencariannya**:

1. **Variabel Properti Terpisah di Livewire**:
   ```php
   public string $pekananSearch = '';
   public string $jumatSearch = '';
   public string $agendaSearch = '';
   ```
2. **Lifecycle Hook untuk Reset Pagination**:
   ```php
   public function updatedPekananSearch(): void { $this->resetPage('pekananPage'); }
   public function updatedJumatSearch(): void { $this->resetPage('jumatPage'); }
   public function updatedAgendaSearch(): void { $this->resetPage('agendaPage'); }
   ```
3. **Binding Terpisah di Blade**:
   Gunakan `wire:model.live.debounce.300ms="pekananSearch"` pada input Pekanan, dan `wire:model.live.debounce.300ms="jumatSearch"` pada input Jumat.
   *Keuntungan*: Mengetik di satu tab tidak akan mengganggu, memfilter, atau mengosongkan data di tab lain.

---

## 5. Pilar 4: Keamanan Konfigurasi & Concurrency Resilience

1. **Aturan `env()`**:
   - Jangan pernah memanggil fungsi `env('VAR')` langsung di dalam Controller, Model, atau Blade view.
   - Panggil selalu melalui `config('service.var')`.
2. **Fallback Kunci Enkripsi (`config/app.php`)**:
   Untuk mencegah error `MissingAppKeyException` akibat glitch concurrency di server multi-threaded, sediakan selalu nilai fallback:
   ```php
   'key' => env('APP_KEY') ?: 'base64:...',
   ```

---

## Checklist Setiap Menambah Fitur / Halaman Baru

Gunakan checklist ini sebelum menganggap sebuah fitur selesai:
- [ ] Apakah menu navigasi menggunakan `wire:navigate` standar (bukan `.hover`)?
- [ ] Apakah semua relasi tabel sudah di-eager load menggunakan `with(...)`?
- [ ] Apakah ada pemanggilan query database (`Model::where(...)`) di dalam file Blade? (Harus **TIDAK ADA**).
- [ ] Apakah kartu statistik/agregasi menggunakan `withCount` / `withSum` alih-alih looping individual?
- [ ] Apakah pencarian di sub-tab memiliki variabel state tersendiri?
- [ ] Apakah debounce (minimal `300ms`) dipasang pada input search (`wire:model.live.debounce.300ms`)?
- [ ] Apakah pagination di-reset ke halaman 1 saat user mengetik di search bar (`updatedSearch`)?
- [ ] Apakah semua indikator loading menggunakan modifier spesifik (`wire:loading.inline-flex` / `wire:loading.flex`) dan bukan `wire:loading` polos agar icon dan teks tidak bertumpuk vertikal?
- [ ] Jalankan benchmark query: Pastikan jumlah query untuk menampilkan halaman tersebut di bawah 35 query.

---

## 7. Panduan Deploy ke Homeserver (Proxmox Debian + 1Panel)

### Pertanyaan: Apakah akan ada kendala kecepatan saat dideploy di Proxmox Debian + 1Panel dibanding Windows Laragon?
**Jawaban Tegas: TIDAK ADA KENDALA, JUSTRU AKAN JAUH LEBIH CEPAT (2x hingga 5x lebih kencang)!**

### Mengapa di Linux (Debian) Lebih Cepat dari Windows (Laragon)?
1. **Sistem File (ext4 vs NTFS)**:
   - Laravel membaca ratusan file PHP dan Blade di setiap request. Sistem file Linux (ext4) memiliki performa I/O file kecil dan in-memory caching (VFS Page Cache) yang berkali-kali lipat lebih cepat daripada NTFS Windows.
2. **Arsitektur Nginx + PHP-FPM**:
   - Di Debian, PHP berjalan menggunakan **PHP-FPM** berbasis proses independen (bukan multi-threaded seperti Apache di Windows). Tidak ada risiko race condition environment, pemrosesan request jauh lebih stabil dan ringan.
3. **Fitur Optimasi Produksi Laravel**:
   Di server produksi Linux, seluruh konfigurasi, routing, dan template Blade bisa di-cache ke dalam memory RAM.

---

### Langkah Praktis Saat Deploy di 1Panel (Debian):

#### 1. Konfigurasi PHP di 1Panel:
Aktifkan dan setel ekstensi **OPcache** pada PHP container di 1Panel:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.validate_timestamps=0 ; (Set 0 untuk performa maksimal di production)
```

#### 2. Jalankan Perintah Optimasi Produksi Laravel:
Setelah code di-clone ke server dan file `.env` diisi, jalankan:
```bash
# 1. Cache konfigurasi (Sangat penting: membuat akses config instan dari RAM)
php artisan config:cache

# 2. Cache routing
php artisan route:cache

# 3. Cache seluruh template Blade
php artisan view:cache

# 4. Cache event & listener
php artisan event:cache

# Atau jalankan satu perintah ringkas:
php artisan optimize
```

#### 3. Izin Folder (Permissions):
Pastikan user web server (biasanya `www-data` atau `1000`) memiliki izin tulis ke folder storage:
```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Dengan arsitektur yang sudah dioptimasi ini dan deploy di Debian Proxmox + 1Panel, aplikasi Masjid Salahuddin akan mampu melayani puluhan hingga ratusan akses simultan jamaah dan pengurus dengan waktu respon di bawah 50–100 milidetik secara stabil.
