<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

## Livewire Load Speed & Architecture Mandate

Always maintain Livewire as the bedrock for application load speed and interactivity across all components:
1. **0ms Optimistic & Instant UI**: Use Alpine.js as Livewire's client-side reactive companion for instantaneous modal triggers, tab switching, and live inputs so the user never experiences click-to-render delays.
2. **Minimal Livewire Public State**: Keep public component properties strictly limited to what is interactive. Never store large static arrays (e.g. 518 cities) in public properties; pass static or read-only data via `render()` or in-memory Alpine data.
3. **Instant Visual Feedback & Proper Display Modifiers**: Always bind `wire:loading` and `wire:target` to interactive buttons and forms with micro-spinners and `wire:loading.attr="disabled"` to eliminate perceived lag. Always use display-specific modifiers (`wire:loading.inline-flex` or `wire:loading.flex`) with `style="display: none;"` and `shrink-0 whitespace-nowrap` to prevent Livewire's default `display: inline-block` from overriding flex layouts and stacking spinner icons vertically above text.
4. **Lean Snapshot Payloads**: Use `wire:ignore` on static modals/dialogs and ensure Livewire server actions complete in under 50ms.
5. **State Synchronization & Morph Resilience**: Always adhere strictly to [docs/LIVEWIRE_ALPINE_STATE_SYNC_GUIDELINES.md](docs/LIVEWIRE_ALPINE_STATE_SYNC_GUIDELINES.md). Never hardcode Alpine sub-tab state or modal visibility in root `x-data`. Always synchronize sub-tabs via public PHP properties and `$wire.set('...SubTab', val, false)`, always dispatch window events (`open-*-modal` / `close-*-modal`) from backend PHP methods, and never use `#[Url]` when routing relies on path parameters (`/admin/{tab}`).
6. **Server-Driven Dynamic State & Morph Isolation (Blade Murni + `wire:key` vs Alpine Anti-Pattern)**:
   - **DILARANG KERAS** menggunakan direktif Alpine (`:class="$wire.prop ? ..."`, `x-show`, `x-text`) untuk merender elemen atau tombol yang statusnya dikendalikan dari backend Livewire.
   - Pola campuran tersebut memicu bentrok reaktivitas saat Livewire DOM morphing: kelas CSS bertabrakan (*CSS race condition*), warna teks/background terbalik (misal teks merah di atas tombol navy, atau teks putih di atas latar pink), dan icon `<i data-lucide="...">` lenyap menjadi kotak kosong saat proses render ulang.
   - **WAJIB** gunakan percabangan Blade murni (`@if($prop) ... @else ... @endif`) dengan `wire:key` yang berbeda pada masing-masing state (misal `wire:key="btn-action-active"` dan `wire:key="btn-action-inactive"`).
   - **HINDARI** menyematkan kelas CSS `transition` / `transition-colors` pada tombol atau elemen yang mengalami pergantian tema warna secara drastis untuk mencegah efek interpolasi warna yang berkedip (*split-second color transition artifacts*).
   - **WAJIB** sematkan ikon dinamis sebagai native inline `<svg>` langsung di dalam Blade (bukan tag `<i>` Lucide runtime) agar ikon selalu dirender secara instan pada frame pertama (*zero-frame delay*) tanpa jeda eksekusi JavaScript.
7. **Js::from Mandate for Blade to Alpine Data Transfer**: Never pass unescaped or manual string literals with `addslashes()` into Alpine `@click` / `x-data` / `$dispatch` expressions. Literal newlines (`\n`) and quotes in user content (such as finance descriptions, event titles, notes) will break JavaScript parsing (`SyntaxError: Invalid or unexpected token`). Always wrap payload objects or strings with `{{ Js::from(...) }}`.

## UI & Component Guidelines Mandate

Always adhere strictly to [UI_GUIDELINES.md](UI_GUIDELINES.md) for all styling and component standards:
1. **Select Option / Dropdown (`<select>`)**: Always use `font-medium` (`font-weight: 500`). Never use `font-bold` or `font-semibold` on `<select>`.
2. **Status Badges**: Exact specification `inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none` with Capitalized title-case text (never uppercase).
3. **Action Buttons**: Icon-only styling (`p-1.5 rounded-lg border shadow-2xs`) with descriptive `title` attributes.
4. **Table Header Alignment**: `text-center` for action columns, `text-left` for text data, and `text-right` / `text-center` for numeric values.
5. **Primary Action / Submit Buttons (Tombol "Simpan" & Sejenisnya)**: Wajib selalu gunakan class:
   ```html
   px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center
   ```
   Jangan gunakan warna lain seperti `bg-emerald-600` atau ukuran padding non-standar untuk tombol aksi submit / simpan formulir.
6. **Table Row Borders (Garis Baris Tabel)**: Wajib selalu gunakan `divide-y divide-slate-300` (1px solid `#cbd5e1`) pada setiap `<tbody>` dan `border-b border-slate-300` pada `<thead>`. Jangan gunakan `divide-slate-100` yang tidak kontras terhadap latar putih.
7. **Livewire Loading Indicators & Alignment (Penyelarasan Icon Loading Sebaris)**: Livewire secara bawaan menyuntikkan inline style `style="display: inline-block;"` saat direktif `wire:loading` aktif tanpa modifier khusus. Nilai inline style ini menimpa utilitas Tailwind `inline-flex` sehingga icon `<svg>` spinner dan teks `<span>` menjadi bertumpuk vertikal (icon di atas teks). WAJIB selalu gunakan:
   ```html
   <div wire:loading.inline-flex style="display: none;" class="inline-flex flex-row items-center gap-2 shrink-0 whitespace-nowrap ...">
       <svg class="... animate-spin shrink-0 inline-block" ...></svg>
       <span class="whitespace-nowrap leading-none">...</span>
   </div>
   ```
   Jangan pernah menggunakan `wire:loading` polos pada elemen inline-flex/flex.
8. **Pagination UI Component (Navigasi Nomor Halaman)**: Wajib selalu gunakan bilah kotak numerik terpadu (*unified pagination bar*) berlatar putih dengan pembatas halus (`rounded-lg border border-slate-200 bg-white shadow-2xs`), tombol aktif bertema korporat Navy (`bg-gov-navy text-white font-bold`), dan icon panah chevron `<` / `>`. DILARANG menggunakan tombol teks blok terpisah atau teks mentah seperti `pagination.previous` / `pagination.next` pada mobile. Seluruh string bahasa pagination dijamin melalui `lang/id/pagination.php`.
9. **Form Input Validation Standards (Password, NIP Pegawai, & WhatsApp)**: Wajib terapkan standar ketat: Kata sandi minimal 8 karakter dengan kombinasi huruf kapital, angka, dan simbol (`Password::min(8)->letters()->mixedCase()->numbers()->symbols()`); NIP Pegawai wajib tepat berjumlah 18 digit angka (`digits:18`); Nomor WhatsApp wajib diawali karakter `"08"` dengan panjang 10-15 digit (`regex:/^08[0-9]{8,13}$/`) serta menormalisasi awalan `+628`/`628`. Kamus terjemahan validasi bahasa Indonesia wajib lengkap di `lang/id/validation.php`.
10. **Larangan Keras Badge Penanda ("Hari Ini", dll) & Badge Count Number**:
    - **DILARANG KERAS** menambahkan badge-badge penanda seperti `"Hari Ini"`, badge angka hitungan (*badge count number*, seperti counter jumlah data/item pada tab, label, atau header), maupun badge dekoratif sejenisnya.
    - Pengguna (USER) secara tegas tidak menyukai badge seperti "Hari Ini" atau badge counter. Untuk menandai baris aktif, hari ini, atau entitas penting, cukup gunakan penataan posisi (row teratas) dan diferensiasi warna latar belakang/border halus yang sudah ada tanpa menyematkan elemen badge teks atau angka hitungan tambahan.

## Direct Execution & Autonomous Workflow Mandate (Tanpa Konfirmasi Rencana Perantara)

1. **Eksekusi Langsung (Direct Execution)**: Setiap kali menerima permintaan perbaikan bug, penyesuaian UI, atau penambahan fitur, **LANGSUNG eksekusi** modifikasi kode pada berkas terkait dan jalankan verifikasi pengujian secara mandiri tanpa jeda.
2. **Dilarang Memblokir untuk Review Rencana**: JANGAN membuat dokumen rencana pelaksanaan (seperti `implementation_plan.md` dengan `RequestFeedback: true`) yang memblokir proses kerja hanya untuk meminta konfirmasi/approval tombol perantara dari user, kecuali jika user secara eksplisit meminta ("buatkan rencana terlebih dahulu").
3. **Hasil Akhir yang Siap Pakai**: Selesaikan seluruh pengubahan berkas, pastikan sintaks dan pengujian lulus 100%, lalu langsung sajikan laporan ringkas hasil akhir kepada pengguna.
