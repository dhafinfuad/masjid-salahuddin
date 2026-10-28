# Panduan Arsitektur: Sinkronisasi State Livewire & Alpine.js

Dokumen ini memuat standar arsitektur dan pedoman wajib dalam pengembangan antarmuka reaktif yang menggabungkan **Livewire 3/4** dan **Alpine.js** di aplikasi Masjid Salahuddin, guna mencegah masalah refresh berkedip (blink), modal yang menutup sendiri, atau sub-tab yang melompat kembali ke kondisi awal secara tidak diinginkan.

---

## 1. Anatomi Akar Masalah (Root Cause Analysis)

### A. Livewire DOM Morphing vs Root `x-data` Re-evaluation
Pada komponen Livewire berbasis satu halaman besar (SPA-like dashboard) seperti `AdminDashboard`, seluruh state antarmuka sering kali didefinisikan pada tag root `<div>` via Alpine.js:
```html
<div x-data="{
    activeTab: '{{ $currentTab }}',
    financeSubTab: 'utama',             <!-- BAHAYA: Nilai statis/hardcoded -->
    showFinanceCategoryModal: false,     <!-- BAHAYA: Nilai statis/hardcoded -->
    ...
}">
```
Ketika aksi Livewire dieksekusi (misal klik "Tambah Pos", "Edit", atau input debounced):
1. Livewire mengirim permintaan AJAX ke server dan merender ulang template Blade.
2. Respons HTML dikembalikan ke browser, dan engine diffing (morphdom) mencocokkan atribut elemen root `<div>`.
3. Karena string `x-data` di-evaluasi ulang oleh Alpine saat atribut root dimorph, properti yang di-hardcode (seperti `financeSubTab: 'utama'` dan `showFinanceCategoryModal: false`) akan **di-reset paksa ke nilai default awalnya**.
4. **Akibat**:
   - Pengguna yang sedang berada di sub-tab `'kategori'` terlempar kembali ke sub-tab `'utama'`.
   - Modal yang baru saja dibuka pengguna langsung tertutup sendiri begitu balasan server tiba.

### B. Ketiadaan Event Dispatching pada Modal Server-Side
Ketika method backend Livewire (seperti `openFinanceCategoryModal()` atau `editFinanceCategory()`) hanya mengubah properti PHP tanpa memancarkan event (`$this->dispatch(...)`):
- Alpine di sisi client tidak memiliki jaminan sinyal event untuk mempertahankan kondisi terbuka (`true`).
- Begitu siklus commit Livewire selesai, Alpine terpengaruh oleh DOM morphing root yang bernilai `false`.

### C. Konflik Atribut `#[Url]` vs Path Parameter Routing
Ketika routing aplikasi menggunakan URL path:
```php
Route::get('/admin/{tab}', AdminDashboard::class);
```
Maka menambahkan atribut `#[Url(as: 'tab')]` pada properti `$currentTab` adalah **anti-pattern**:
- `#[Url]` mengawasi **query string** (misal `?tab=finance`), bukan path URL (`/admin/finance`).
- Ketika navigasi client mengubah URL ke `/admin/finance` tanpa query string, watcher URL Livewire mendeteksi parameter `tab` kosong dan mencoba mengembalikan nilai properti ke default (`'dashboard'`).
- Hal ini memicu loop permintaan jaringan dan re-render terus-menerus di latar belakang yang tampak sebagai **blink refresh berkala**.

---

## 2. Aturan Baku Pengembangan (Mandatory Architectural Rules)

### Aturan 1: State Sub-Tab Wajib Dual-Synchronized
Setiap sub-tab yang memicu atau berdampingan dengan aksi server Livewire **wajib** memiliki representasi di komponen PHP Livewire:

```php
// Backend: AdminDashboard.php
public string $financeSubTab = 'utama'; // 'utama' | 'program' | 'kategori'
public string $kajianSubTab = 'pekanan';
public string $agendaSubTab = 'agenda';
```

Dan di template Blade:
```blade
<!-- Inisialisasi dari nilai PHP yang aktif -->
financeSubTab: '{{ $financeSubTab }}',

<!-- Navigasi Sub-tab: Update Alpine untuk 0ms instant render, DAN update snapshot Livewire -->
<button type="button" 
    @click="financeSubTab = 'kategori'; $wire.set('financeSubTab', 'kategori', false); ...">
    Kelola Kategori Kas
</button>
```
> **Catatan**: Parameter `false` pada `$wire.set(prop, val, false)` memastikan pembaruan state snapshot bersifat *deferred / silent* tanpa memicu round-trip HTTP yang tidak perlu.

### Aturan 2: Siklus Hidup Modal Wajib Event-Driven
Setiap modal yang memiliki interaksi backend (CRUD, load data edit, reset form) **wajib** menggunakan pola event dispatching window:

1. **Backend PHP**:
```php
public function openFinanceCategoryModal(): void
{
    // ... reset state ...
    $this->showFinanceCategoryModal = true;
    $this->dispatch('open-finance-category-modal'); // WAJIB
}

public function editFinanceCategory(int $id): void
{
    // ... load data ...
    $this->showFinanceCategoryModal = true;
    $this->dispatch('open-finance-category-modal'); // WAJIB
}

public function saveFinanceCategory(): void
{
    // ... save data ...
    $this->showFinanceCategoryModal = false;
    $this->dispatch('close-finance-category-modal'); // WAJIB
}
```

2. **Frontend Blade Listener & Modal Wrapper**:
```blade
<!-- Root Window Listener -->
<div ...
    @open-finance-category-modal.window="showFinanceCategoryModal = true"
    @close-finance-category-modal.window="showFinanceCategoryModal = false">

    <!-- Modal Element Wajib wire:ignore.self -->
    <div x-show="showFinanceCategoryModal" x-cloak wire:ignore.self ...>
```

3. **Inisialisasi Root State yang Sinkron**:
```blade
showFinanceCategoryModal: {{ $showFinanceCategoryModal ? 'true' : 'false' }},
```

### Aturan 3: Jangan Gunakan `#[Url]` Jika Routing Menggunakan Path Parameter
- Jika route menggunakan `/admin/{tab}`, bind nilai `$tab` melalui method `mount(?string $tab = null)`:
```php
public function mount(?string $tab = null): void
{
    if ($tab && in_array($tab, self::VALID_TABS)) {
        $this->currentTab = $tab;
    }
}
```
- **Haram** menyematkan `#[Url(as: 'tab')]` pada `$currentTab` karena akan bentrok dengan sinkronisasi query string bawaan Livewire.

### Aturan 4: Optimasi Ikon Lucide pada Livewire Hook
Untuk menghindari kedipan ikon di seluruh layar:
- `createLucideIcons()` harus melakukan pengecekan cepat (fast bail-out) hanya jika terdapat elemen `[data-lucide]` yang belum terkonversi:
```javascript
const unrendered = document.querySelectorAll('i[data-lucide], span[data-lucide]');
if (unrendered.length === 0) return;
```

### Aturan 5: Pemicu Buka Modal Wajib Pure Client-Side (0ms Instant & Zero Blink)
Untuk mencegah efek **blink / kedip visual** saat membuka modal (seperti pada Edit Kegiatan, Edit Kategori, Edit Jadwal Kajian, Edit Penugasan Petugas, Edit Perencanaan Kegiatan, dan Edit Transaksi Kas):
1. **DILARANG** melakukan HTTP roundtrip atau memanggil `$wire.set(...)` berkali-kali saat tombol "Tambah" atau "Edit" diklik:
   - Pada Livewire 3, pemanggilan `$wire.set()` tanpa `$` diteruskan ke `wireFallback` dan memicu aksi jaringan AJAX `fireAction()`.
   - Mengirim beberapa panggilan `$wire.set` sekaligus akan memicu re-render template Blade penuh (500KB+), proses DOM diffing (morphdom), dan re-evaluasi ikon Lucide di latar belakang layar yang terlihat sebagai **blink kedip**.
2. **Pola Emas (Standard Architecture)**:
   - Definisikan state form di Alpine: `...Form: { id: null, name: '', ... }`
   - Buka modal secara instan murni di Alpine (0ms):
   ```javascript
   openCreateFinanceCategory() {
       this.isEditingFinanceCategory = false;
       this.financeCategoryForm = { id: null, name: '', group: 'pengeluaran_rutin', color: 'emerald' };
       this.showFinanceCategoryModal = true;
   },
   openEditFinanceCategory(data) {
       this.isEditingFinanceCategory = true;
       this.financeCategoryForm = {
           id: data.id,
           name: data.name || '',
           group: data.group || 'pengeluaran_rutin',
           color: data.color || 'emerald'
       };
       this.showFinanceCategoryModal = true;
   }
   ```
   - Input di dalam modal menggunakan `x-model="financeCategoryForm.name"` dan form submit menggunakan:
   ```blade
   <form @submit.prevent="$wire.saveFinanceCategory(financeCategoryForm)">
   ```
   - Di method PHP Livewire:
   ```php
   public function saveFinanceCategory(array $formData = []): void
   {
       if (! empty($formData)) {
           // Petakan ke properti komponen untuk validasi & penyimpanan
           $this->newCategoryName = $formData['name'] ?? '';
           ...
       }
       ...
       $this->dispatch('close-finance-category-modal');
   }
   ```
    - Dengan pola ini, saat membuka modal **tidak ada komunikasi server sama sekali**, sehingga rendering 100% instan (0ms) tanpa efek berkedip sedikit pun.

### Aturan 6: Isolasi State Backend vs Alpine Anti-Pattern (Blade Murni + `wire:key` + Inline SVG)
Ketika suatu elemen (seperti tombol status pengesahan, tombol switch mode, atau banner status) berganti tampilan berdasarkan status backend Livewire (misal `$tteSigned`, `$isPublished`, `$isActive`):
1. **DILARANG KERAS** menggunakan manipulasi kelas dan teks client-side via Alpine:
   - ❌ Anti-pattern: `:class="$wire.prop ? 'bg-rose-50 text-rose-700' : 'bg-gov-navy text-white'"`
   - ❌ Anti-pattern: `x-text="$wire.prop ? 'Batal' : 'Aktifkan'"`
   - ❌ Anti-pattern: `x-show="!$wire.prop"` bersamaan dengan tag `<i data-lucide="...">`
2. **Bahaya Menggabungkan Alpine `:class` dengan DOM Morphing Livewire**:
   - Terjadi bentrok *race-condition* saat server merender ulang: kelas CSS dari Alpine dan Livewire saling menimpa secara parsial.
   - Hasilnya berupa *glitch visual*: teks berubah menjadi merah di atas tombol navy, teks putih di atas tombol pink, atau warna tombol mendadak hilang menjadi kotak putih kosong.
   - Ikon `<i data-lucide="...">` terhapus oleh morphdom dan meninggalkan kotak kosong sebelum script Lucide sempat berjalan ulang.
   - Kelas CSS `transition` / `transition-colors` menganimasikan pergantian warna antarkelas yang berbeda tersebut selama 150–300 milidetik, menghasilkan kedipan warna yang sangat mengganggu mata pengguna.
3. **Pola Emas (Standard Architecture)**:
   - Gunakan percabangan Blade murni di sisi server:
   ```blade
   @if($prop)
       <button wire:key="btn-prop-true" type="button" wire:click="toggleAction" wire:loading.attr="disabled"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold shadow-2xs shrink-0 cursor-pointer disabled:opacity-50 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300">
           ...
       </button>
   @else
       <button wire:key="btn-prop-false" type="button" wire:click="toggleAction" wire:loading.attr="disabled"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold shadow-2xs shrink-0 cursor-pointer disabled:opacity-50 bg-gov-navy hover:bg-gov-navyHover text-white">
           ...
       </button>
   @endif
   ```
   - **Wajib `wire:key` Berbeda**: Memastikan morphdom memperlakukan kedua state sebagai elemen terpisah (unmount yang lama, mount yang baru) tanpa meracuni kelas CSS yang ada.
   - **Hapus `transition`**: Jangan gunakan `transition` pada tombol yang berganti warna latar/teks drastis untuk mencegah interpolasi warna yang berkedip.
   - **Sematkan Inline SVG Langsung**: Seluruh ikon dinamis wajib ditulis sebagai native `<svg>` langsung di dalam Blade tanpa bergantung pada `window.createLucideIcons()` peramban, sehingga dijamin tampil sejak *frame* pertama (*zero-frame delay*).

### Aturan 7: Operan Data Blade ke Alpine Expression Wajib Menggunakan `Js::from(...)`
Ketika mengirim data dinamis (teks deskripsi, nama entitas, judul kegiatan, objek JSON, dll.) dari Blade ke dalam atribut direktif Alpine.js (misalnya `@click="openDeleteModal(...)"`, `@click="openEdit*(...)"`, `@click="$dispatch(...)"`):
1. **DILARANG KERAS Menggunakan String Interpolasi Manual / `addslashes()`**:
   - ❌ Anti-pattern: `itemName: '{{ addslashes($data->name) }}'`
   - ❌ Anti-pattern: `itemName: '{{ $data->description }}'`
   - ❌ Anti-pattern: `matches('{{ $group }}', '{{ strtolower(addslashes($name)) }}')`
2. **Bahaya Fatal `addslashes()` pada JavaScript Expression**:
   - `addslashes()` hanya menambahkan backslash pada petik tunggal (`'`), petik ganda (`"`), backslash (`\`), dan byte NUL.
   - `addslashes()` **TIDAK** mengubah karakter baris baru / newline (`\r`, `\n`) menjadi string escape `\n`.
   - Di JavaScript, string literal berpetik `'...'` atau `"..."` yang memuat karakter baris baru nyata (*literal raw newline*) merupakan pelanggaran fatal sintaks:
     `Uncaught SyntaxError: Invalid or unexpected token` / `Alpine Expression Error: Invalid or unexpected token`.
   - Akibatnya: Alpine gagal mengevaluasi ekspresi `@click`, modal konfirmasi (seperti `confirmDeleteModal`) tidak terbuka sama sekali, dan seluruh interaksi client-side terblokir.
3. **Pola Emas (Standard Architecture)**:
   - **WAJIB** gunakan `{{ Js::from(...) }}` untuk membungkus seluruh objek argumen atau nilai string:
     ```blade
     @click="openDeleteModal({{ Js::from([
         'title' => 'Hapus Transaksi Kas',
         'message' => 'Yakin ingin menghapus catatan transaksi kas ini?',
         'itemName' => $fin->description . ' (Rp ' . number_format($fin->amount, 0, ',', '.') . ')',
         'action' => 'deleteFinance',
         'id' => $fin->id,
     ]) }})"
     ```
   - `Js::from()` memanggil `json_encode()` dengan bendera pengamanan ketat (`JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT`), sehingga newline dan tanda petik dikodekan aman tanpa merusak pembungkus atribut HTML `@click="..."`.

### Aturan 8: Larangan Menyimpan File Besar / Base64 pada Properti Publik Livewire (Mencegah `PayloadTooLargeException`)
1. **Bahaya Fatal Serialization State Livewire**:
   - Seluruh properti publik (`public $property`) pada komponen Livewire secara otomatis dikonversi menjadi snapshot JSON yang dikirim ke browser dan **wajib dikirim kembali ke server pada setiap permintaan interaksi pengguna** (`wire:click`, `$wire.set`, trigger event, dll.).
   - Menyimpan berkas biner, string Base64 dokumen PDF (misalnya dokumen scan 1 MB -> 1.335 KB Base64), atau array blob berukuran besar langsung di properti publik PHP (seperti `public string $scanPdfBase64 = ''`) akan membuat ukuran payload request melonjak drastis.
   - Ketika payload melebihi batas default `payload.max_size` (1024 KB), Livewire langsung menghentikan request dengan eksepsi fatal:
     ```
     Livewire\Exceptions\PayloadTooLargeException: Livewire request payload is too large (1335KB). Maximum allowed size is 1024KB.
     ```
2. **Pola Emas (Storage + HTTP Streaming Architecture)**:
   - **JANGAN PERNAH** menyimpan isi biner atau Base64 berkas di properti publik Livewire.
   - Simpan berkas sementara di disk server lokal (`storage/app/private/temp_scans/latest_scan.pdf`) dan simpan path-nya di sesi pengguna (`session(['temp_scan_pdf_path' => $destPath])`).
   - Sediakan rute controller / closure streaming HTTP khusus untuk membukanya secara aman:
     ```php
     Route::get('/admin/programs/preview-scan-pernyataan', function () {
         $path = session('temp_scan_pdf_path') ?: storage_path('app/private/temp_scans/latest_scan.pdf');
         return response()->file($path, [
             'Content-Type' => 'application/pdf',
             'Cache-Control' => 'private, max-age=86400',
         ]);
     });
     ```
   - Properti publik Livewire hanya boleh menyimpan metadata ringkas (seperti NIP, nama pegawai, dan angka komitmen). Ukuran payload Livewire akan terjaga sangat ramping (< 20 KB).
   - Naikkan juga batas pengaman pada `config/livewire.php`:
     ```php
     'payload' => [
         'max_size' => 10 * 1024 * 1024, // 10MB - safety buffer
     ],
     ```

### Aturan 9: Penanganan Pratinjau Dokumen Scanner Mesin (Format MRC & JBIG2 vs PDF.js Canvas)
1. **Anatomi Berkas Scanner Multi-Function (Canon, Ricoh, Xerox, dsb.)**:
   - Berkas PDF hasil scanner dokumen fisik modern umumnya menggunakan format **MRC (*Mixed Raster Content*)**.
   - Berkas ini memisahkan isi halaman menjadi beberapa lapisan (*layer*):
     - **Lapisan Latar Belakang** (`/Filter [/FlateDecode/DCTDecode]`): Gambar JPEG beresolusi rendah yang memuat tekstur kertas, di mana teks hitam sengaja dihilangkan atau diputihkan oleh algoritma scanner.
     - **Lapisan Masker Teks** (`/Filter /JBIG2Decode` dengan `/ImageMask true`): Masker 1-bit monokrom beresolusi tinggi yang menyimpan teks hitam pekat, stempel, dan tanda tangan secara tajam.
2. **Kegagalan Ekstraksi Stream JPEG Murni di PHP**:
   - Mengekstrak stream JPEG mentah secara manual via PHP tanpa komposit JBIG2 hanya akan menghasilkan gambar latar belakang yang **pudar, putih, dan teksnya hilang/tidak terbaca**.
3. **Pola Emas (Full PDF.js Canvas Rendering)**:
   - Gunakan pustaka **PDF.js** pada sisi client untuk merender pratinjau dokumen ke dalam elemen `<canvas>`.
   - PDF.js mengeksekusi pipeline rendering PDF secara lengkap, menggabungkan lapisan latar belakang dan masker JBIG2 secara presisi sehingga seluruh tulisan, tanda tangan, dan tabel tampil **hitam pekat, sangat tajam, dan kontras sempurna**.
   - **Mencegah Indikator Loading Menggantung**:
     Gunakan state Alpine pelindung ganda `isPdfLoading && !hasRenderedAnyPage` dan pastikan blok `finally` selalu mematikan status loading:
     ```javascript
     try {
         // Render tiap halaman ke canvas
         this.hasRenderedAnyPage = true;
     } catch (err) {
         console.error(err);
     } finally {
         this.isPdfLoading = false;
     }
     ```

### Aturan 10: Sinkronisasi Form Nilai Nominal Antara Objek OCR & Input Alpine (Two-Way Ribuan Input Binding)
1. **Konsistensi Penamaan Kunci Array**:
   - Pastikan template Blade membaca fallback dari kedua penamaan kunci data (misalnya `$page['commitments']` dan `$page['programs']`):
     ```blade
     infaq: {{ (int)($page['commitments']['infaq'] ?? $page['programs']['infaq'] ?? 0) }}
     ```
2. **Sinkronisasi Dua Arah Non-Blocking**:
   - Saat pengguna mengetik atau mengubah nilai nominal pada input berformat ribuan, lakukan sanitasi angka murni di client-side lalu kirimkan nilainya ke backend secara silent tanpa trigger render ulang yang berat:
     ```blade
     @input="window.handleRibuanInput($event.target, (clean) => { 
         infaq = clean ? parseInt(clean) : 0; 
         $wire.set('scanPages.{{ $index }}.commitments.infaq', infaq, false); 
         $wire.set('scanPages.{{ $index }}.programs.infaq', infaq, false); 
     })"
     ```

### Aturan 11: Pencegahan Race Condition Antarmuka Modal Antar-Tab via Action Token (Nonce)
Ketika pengguna membuka modal di Tab A, menutupnya dengan cepat, lalu berpindah ke Tab B dan membuka modal di Tab B, respon HTTP asynchronous tertunda dari Tab A yang baru tiba 1-2 detik kemudian dapat membajak (*hijack*) antarmuka modal Tab B jika tidak dilindungi token transaksi.

1. **Token Nonce di State Parent Alpine (`currentActionToken`)**:
   ```javascript
   currentActionToken: null,
   startAction(scope) {
       this.currentActionToken = scope + '-' + Date.now() + '-' + Math.random().toString(36).substring(2, 7);
       return this.currentActionToken;
   },
   cancelAction() {
       this.currentActionToken = null;
   }
   ```
2. **Pemicu Tombol Aksi**:
   ```html
   @click="let token = startAction('kajian-' + tab); openCreateKajian(tab, token)"
   ```
3. **Penyaringan Respon Asynchronous (Stale Response Invalidation)**:
   Pada seluruh pemanggilan async server (misal `getDefaultKajianTime`), simpan token awal dan verifikasi sebelum menyentuh state:
   ```javascript
   async updateKajianDefaultTime(token = null) {
       const activeToken = token || this.currentActionToken;
       const res = await $wire.getDefaultKajianTime(this.kajianForm.type, this.kajianForm.date, activeToken);
       if (activeToken && this.currentActionToken !== activeToken) return; // Abaikan respon basi
       if (res) this.kajianForm.time_display = res;
   }
   ```
4. **Invalidasi Otomatis saat Ganti Tab & Tutup Modal**:
   - Saat modal ditutup: Panggil `this.cancelAction(); window._activeKajianType = null;`.
   - Saat beralih sub-tab (`switchSubTab`): Panggil `this.cancelAction(); window._activeKajianType = null;`.
5. **Proteksi Re-inisialisasi Root `x-data`**:
   Simpan pointer tipe/state modal aktif di level window (`window._activeKajianType = type;`) dan pulihkan saat `init()` Alpine berjalan agar state lokal tidak di-reset ke nilai default jika Livewire melakukan morphing root.

### Aturan 12: Standar Form Submission Modal & Larangan Alpine State Loading pada Tombol Simpan (Anti-Ghosting / Text Collision Prevention)
1. **Anatomi Masalah Text Clashing / Overlapping pada Tombol Simpan**:
   - Jika modal mendefinisikan flag loading manual di Alpine (seperti `isSaving: false` atau `async submitFoo()`) lalu menggunakan `x-show="!isSaving"` dan `x-show="isSaving"` pada tombol submit:
     - Ketika Livewire melakukan diffing DOM atau network request, Tailwind utility class `inline-flex` menimpa evaluasi `x-show` inline style `display: none` pada engine browser mobile (WebKit iOS / Chromium Android).
     - Akibatnya, teks idle (misal "Simpan & Unduh PDF") dan teks loading ("Menyiapkan PDF...") ter-render bersamaan di dalam tombol sehingga bertumpuk, berbayang, dan tidak terbaca (*text ghosting / visual collision*).
2. **Pola Baku Form Submission Livewire**:
   - Bungkus seluruh isian dan footer modal dalam satu form:
     ```blade
     <form wire:submit.prevent="saveAction" novalidate class="flex flex-col flex-1 min-h-0 bg-white rounded-b-2xl"
         style="background-color: #ffffff; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
         <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4 text-xs notula-scrollable bg-white">
             <!-- Input Form Fields -->
         </div>
         <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2.5 shrink-0 rounded-b-2xl" ...>
             <button type="button" @click="showModal = false" class="...">Batal</button>
             <button type="submit" wire:loading.attr="disabled" class="...">
                 <span wire:loading.remove wire:target="saveAction" style="color: #ffffff;">Simpan Data</span>
                 <span wire:loading.inline-flex wire:target="saveAction" style="display: none; color: #ffffff;" class="inline-flex items-center justify-center gap-2">
                     <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">...</svg>
                     <span class="whitespace-nowrap" style="color: #ffffff;">Menyimpan...</span>
                 </span>
             </button>
         </div>
     </form>
     ```
3. **Karakteristik Wajib**:
   - Hapus properti `isSaving` atau fungsi async tombol dari `x-data`.
   - Gunakan `wire:loading.remove` dan `wire:loading.inline-flex` bawaan Livewire dengan target method yang tepat.
   - Jangan menyisipkan ikon ekstra atau label tombol ganda yang rumit. Gunakan teks bersih dan jelas.

---

## 3. Checklist Sebelum Deploy Fitur Baru
- [ ] Apakah sub-tab baru sudah dideklarasikan sebagai `public string $...SubTab` di PHP?
- [ ] Apakah tombol switch sub-tab memanggil `$wire.set('...SubTab', '...', false)`?
- [ ] Apakah tombol buka modal baru menggunakan pola pure client-side Alpine (`openCreate*` / `openEdit*`) tanpa AJAX roundtrip?
- [ ] Apakah form modal menggunakan `@submit.prevent="$wire.save*(formObject)"`?
- [ ] Apakah method modal (tutup/simpan) memancarkan event `$this->dispatch('close-*-modal')`?
- [ ] Apakah modal HTML sudah memiliki atribut `wire:ignore.self`?
- [ ] Apakah modal dilindungi dari race condition dengan pola Action Token / Nonce (`currentActionToken`)?
- [ ] Apakah kontainer modal terluar bebas border putih di atas backdrop gelap (`border-0` & `border: none;`)?
- [ ] Apakah kontainer modal atas menggunakan warna latar yang sama dengan header (`bg-[#0b2b54]`) untuk mencegah corner bleeding antialiasing di desktop?
- [ ] Apakah header modal bersih tanpa kotak pembungkus ikon (`div.w-8.h-8`)?
- [ ] Apakah modal dibungkus dalam `<form wire:submit.prevent="saveAction" novalidate>` dengan tombol submit `<button type="submit" wire:loading.attr="disabled">` tanpa flag loading kustom Alpine `isSaving` / `x-show`?
- [ ] Apakah indikator proses/loading menggunakan modifier spesifik (`wire:loading.inline-flex` / `wire:loading.flex`) dengan `style="display: none;"` agar icon dan teks tidak bertumpuk vertikal akibat `display: inline-block` bawaan Livewire?
- [ ] Apakah tidak ada konflik antara parameter path URL dan anotasi `#[Url]`?
- [ ] Untuk elemen dinamis berbasis state Livewire: Apakah sudah menggunakan Blade murni (`@if`) dengan `wire:key` unik berbeda, tanpa manipulasi Alpine `:class`/`x-text`, tanpa kelas `transition`, dan dengan native inline SVG?
- [ ] Apakah seluruh operan data Blade ke ekspresi Alpine (`openDeleteModal`, `openEdit*`, `$dispatch`) sudah dibungkus aman menggunakan `Js::from(...)` tanpa string literal `{{ addslashes(...) }}` manual?
- [ ] Apakah ada properti publik Livewire yang menampung file biner besar, Base64, atau data berukuran > 500KB? (DILARANG: Wajib dialihkan ke storage lokal + rute HTTP streaming untuk mencegah `PayloadTooLargeException`).
- [ ] Untuk pratinjau dokumen scan/PDF: Apakah menggunakan rendering PDF.js canvas lengkap agar layer komposit JBIG2 tampil jernih, tajam, dan tidak pudar?
- [ ] Apakah input nominal komitmen/angka terikat dua arah via Alpine `$wire.set('...', val, false)` dengan membaca fallback kedua struktur data (`commitments` & `programs`)?

