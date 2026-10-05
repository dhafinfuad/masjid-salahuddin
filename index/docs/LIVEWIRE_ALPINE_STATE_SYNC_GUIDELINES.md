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

---

## 3. Checklist Sebelum Deploy Fitur Baru
- [ ] Apakah sub-tab baru sudah dideklarasikan sebagai `public string $...SubTab` di PHP?
- [ ] Apakah tombol switch sub-tab memanggil `$wire.set('...SubTab', '...', false)`?
- [ ] Apakah tombol buka modal baru menggunakan pola pure client-side Alpine (`openCreate*` / `openEdit*`) tanpa AJAX roundtrip?
- [ ] Apakah form modal menggunakan `@submit.prevent="$wire.save*(formObject)"`?
- [ ] Apakah method modal (tutup/simpan) memancarkan event `$this->dispatch('close-*-modal')`?
- [ ] Apakah modal HTML sudah memiliki atribut `wire:ignore.self`?
- [ ] Apakah indikator proses/loading menggunakan modifier spesifik (`wire:loading.inline-flex` / `wire:loading.flex`) dengan `style="display: none;"` agar icon dan teks tidak bertumpuk vertikal akibat `display: inline-block` bawaan Livewire?
- [ ] Apakah tidak ada konflik antara parameter path URL dan anotasi `#[Url]`?
- [ ] Untuk elemen dinamis berbasis state Livewire: Apakah sudah menggunakan Blade murni (`@if`) dengan `wire:key` unik berbeda, tanpa manipulasi Alpine `:class`/`x-text`, tanpa kelas `transition`, dan dengan native inline SVG?
- [ ] Apakah seluruh operan data Blade ke ekspresi Alpine (`openDeleteModal`, `openEdit*`, `$dispatch`) sudah dibungkus aman menggunakan `Js::from(...)` tanpa string literal `{{ addslashes(...) }}` manual?
