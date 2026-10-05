# UI Component Guidelines — Masjid Salahuddin

This document defines the standardized patterns for UI components used across the admin dashboard and portal pages. All contributors must follow these guidelines to maintain visual consistency.

---

## 1. Status Badge (Lencana Status)

Status badges are compact, pill-shaped indicators used to display the state of an entity (event status, attendance, active/inactive, week pattern, etc.).

### Standard Specifications
- **Height**: `20px` (`h-[20px]`)
- **Font Size**: `11px` (`text-[11px]`)
- **Text Casing**: `Capitalized` / Title Case (e.g. `Hadir`, `Batal Hadir`, `Belum Hadir`, `Pekan 1`, `Aktif`). **NEVER UPPERCASE**.

### Anatomy

Every badge MUST use the following base classes:

```
inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none
```

| Property | Class | Required |
|:---|:---|:---|
| Layout | `inline-flex items-center` | ✅ Always |
| Height | `h-[20px]` | ✅ Always (exact 20px) |
| Padding | `px-2.5` | ✅ Always |
| Border Radius | `rounded-full` | ✅ Always (pill shape) |
| Font Size | `text-[11px]` | ✅ Always (exact 11px) |
| Font Weight | `font-semibold` | ✅ Always |
| Border | `border` (+ semantic border-color) | ✅ Always |
| Line Height | `leading-none` | ✅ Always |
| Text Transform | Capitalized (Title Case) | ✅ Always (**NO UPPERCASE**) |

Optional additions:
- `justify-center` — when badge needs centered text
- `transition-colors` — for interactive/dynamic badges
- `shrink-0` — when badge should not shrink in flex layouts
- `gap-1` — when badge contains an icon

### ❌ DO NOT USE

| Deprecated Pattern | Replacement |
|:---|:---|
| `UPPERCASE` text or `uppercase` class | Capitalized text (Title Case) |
| `h-5` or unconstrained height | `h-[20px]` |
| `rounded` (non-full) | `rounded-full` |
| `text-xs` (12px) | `text-[11px]` |
| `text-[10px]` | `text-[11px]` |
| `font-bold` | `font-semibold` |
| `px-2` | `px-2.5` |
| Badge penanda "Hari Ini" | DILARANG. Gunakan baris paling atas & warna latar border |
| Badge count number / counter | DILARANG. Jangan gunakan badge angka hitungan item/tab |

### ❌ STRICT PROHIBITION: No "Hari Ini" Badges & No Badge Count Numbers (User Mandate)
- **DILARANG KERAS** menggunakan badge penanda waktu/kondisi seperti `"Hari Ini"`.
- **DILARANG KERAS** menggunakan badge hitungan angka (*badge count number*, misal badge counter jumlah item, notifikasi count, badge numerik di judul/tab).
- **Kebijakan Desain**: User secara tegas tidak menyukai elemen badge penanda seperti "Hari Ini" atau badge count number. Cukup gunakan penataan baris (ditaruh di row paling atas) dan warna baris halus (background & border-l) tanpa menyematkan badge teks/angka.

---

## 2. Semantic Color Palette

Use the following color combinations based on semantic meaning:

### Status Colors

| Status | Background | Text | Border | Usage |
|:---|:---|:---|:---|:---|
| **Success / Hadir / Aktif** | `bg-emerald-100` | `text-emerald-800` | `border-emerald-300` | Hadir, Sukses, Selesai |
| **Success (light)** | `bg-emerald-50` | `text-emerald-700` | `border-emerald-200` | Aktif, Pendaftaran Dibuka |
| **Danger / Batal / Libur** | `bg-rose-100` | `text-rose-800` | `border-rose-300` | Batal Hadir, Diliburkan |
| **Danger (light)** | `bg-rose-50` | `text-rose-700` | `border-rose-200` | Kuota Penuh, Nonaktif |
| **Warning / Pending / Belum Hadir** | `bg-amber-100` | `text-amber-800` | `border-amber-300` | Belum Hadir, Pending |
| **Warning (light)** | `bg-amber-50` | `text-amber-700` | `border-amber-200` | Viewer, Warning light |
| **Primary Info / Pekan / Role** | `bg-blue-50` | `text-blue-700` | `border-blue-300` | Pekan X, Operator, Info |
| **Info / Dzuhur (light)** | `bg-sky-50` | `text-sky-700` | `border-sky-200` | Waktu Shalat |
| **Neutral / Default** | `bg-slate-100` | `text-slate-700` | `border-slate-200` | Default, Waktu |
| **Category / Tag** | `bg-amber-100/70` | `text-gov-navy` | `border-amber-300` | Kategori Kegiatan |

### Dynamic Badges (Alpine.js)

For badges that change state dynamically via Alpine.js, use `:class` bindings with Capitalized values:

```html
<span class="inline-flex items-center justify-center rounded-full h-[20px] px-2.5 text-[11px] leading-none font-semibold border transition-colors"
      :class="{
          'bg-emerald-100 text-emerald-800 border-emerald-300': statusBadge === 'Hadir',
          'bg-rose-100 text-rose-800 border-rose-300': statusBadge === 'Batal Hadir',
          'bg-amber-100 text-amber-800 border-amber-300': statusBadge === 'Belum Hadir'
      }"
      x-text="statusBadge">
    {{ $fallbackValue }}
</span>
```

---

## 3. Code Examples

### Static Status Badge
```html
<span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 leading-none">
    Hadir
</span>
```

### Pekan / Info Badge
```html
<span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold border leading-none bg-blue-50 text-blue-700 border-blue-300">
    Pekan 1
</span>
```

### Category Tag Badge
```html
<span class="inline-flex items-center rounded-full h-[20px] px-2.5 text-[11px] leading-none font-semibold text-gov-navy bg-amber-100/70 border border-amber-300">
    {{ $category->name }}
</span>
```

### Interactive Toggle Badge (Button)
```html
<button type="button"
        wire:click="toggleStatus({{ $id }})"
        class="inline-flex items-center gap-1 h-[20px] px-2.5 rounded-full text-[11px] font-semibold transition cursor-pointer border leading-none {{ $isActive ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-100 text-rose-800 border-rose-300 hover:bg-rose-200' }}">
    <i data-lucide="{{ $isActive ? 'check-circle-2' : 'alert-circle' }}" class="w-3 h-3"></i>
    <span>{{ $isActive ? 'Aktif' : 'Nonaktif' }}</span>
</button>
```

### Count Badge (Header)
```html
<span class="inline-flex items-center h-[20px] px-2.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-300 leading-none">
    {{ $count }} Jadwal
</span>
```

---

## 4. Action Buttons (Icon-Only)

All table action buttons use **icon-only** styling without text labels. Each button must retain a `title` attribute for accessibility.

### Base Pattern
```
inline-flex items-center justify-center p-1.5 rounded-lg border shadow-2xs transition cursor-pointer
```

### Color Variants

| Action | Background | Hover | Text | Border | Icon Color |
|:---|:---|:---|:---|:---|:---|
| **Edit** | `bg-amber-50` | `hover:bg-amber-100` | `text-amber-700` | `border-amber-200` | `text-amber-600` |
| **Delete** | `bg-rose-50` | `hover:bg-rose-100` | `text-rose-700` | `border-rose-200` | `text-rose-600` |
| **WhatsApp** | `bg-emerald-50` | `hover:bg-emerald-100` | `text-emerald-700` | `border-emerald-200` | `text-emerald-600` |
| **View/Link** | `bg-sky-50` | `hover:bg-sky-100` | `text-sky-700` | `border-sky-200` | `text-sky-600` |

### WhatsApp URL Guarantee
All WhatsApp action links MUST use the global helper `wa_link($phoneNumber)` (or `format_phone_for_wa($phoneNumber)`). This automatically strips non-digits and converts domestic formats (`08...`, `8...`, `+62...`) to the standard international format (`628...`), preventing the WhatsApp error *"The username 08xxxx isn't on WhatsApp"*.

```html
<a href="{{ wa_link($k->speaker_phone) }}" target="_blank"
    class="inline-flex items-center justify-center p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-2xs transition"
    title="Hubungi via WhatsApp">
    <i data-lucide="phone" class="w-3.5 h-3.5 text-emerald-600"></i>
</a>
### Dynamic State Toggle Buttons (Tombol Aksi Pengesahan / Batal Antar-Status)

Ketika tombol memiliki dua kondisi visual kontras (misal: "Tinjau & Sahkan" vs "Batalkan Pengesahan", "Aktifkan" vs "Nonaktifkan"):
- **WAJIB** gunakan percabangan Blade `@if($prop) ... @else ... @endif` di sisi server dengan `wire:key` yang berbeda pada masing-masing state (`wire:key="btn-...-active"` vs `wire:key="btn-...-inactive"`).
- **DILARANG** menggunakan manipulasi Alpine `:class="$wire.prop ? ..."` atau `x-text` karena akan bertabrakan dengan DOM morphing Livewire, menyebabkan *color glitching* (misal teks merah di atas tombol navy, atau teks putih di atas tombol pink).
- **HINDARI KELAS `transition`**: Jangan sematkan `transition` / `transition-colors` pada tombol dwistatus ini agar peramban tidak menganimasikan/menginterpolasi warna yang kontras selama 150ms.
- **GUNAKAN INLINE SVG**: Sematkan icon sebagai native inline `<svg>` langsung di dalam Blade agar icon tampil seketika (*zero-frame delay*) tanpa risiko hilang saat proses render ulang.

```html
@if($tteSigned)
    <button wire:key="btn-tte-signed" type="button" wire:click="signTteReport" wire:loading.attr="disabled"
        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold shadow-2xs shrink-0 cursor-pointer disabled:opacity-50 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300">
        <!-- Spinner & Inline SVG -->
        <span>Batalkan Pengesahan</span>
    </button>
@else
    <button wire:key="btn-tte-unsigned" type="button" wire:click="signTteReport" wire:loading.attr="disabled"
        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold shadow-2xs shrink-0 cursor-pointer disabled:opacity-50 bg-gov-navy hover:bg-gov-navyHover text-white border border-transparent">
        <!-- Spinner & Inline SVG -->
        <span>Tinjau &amp; Sahkan TTE</span>
    </button>
@endif
```

---

## 5. Table Header Alignment

- **AKSI** (Action) columns: Always use `text-center` alignment
- Data columns: Use `text-left` by default
- Numeric columns: Use `text-right` or `text-center`

---

## 6. Form Select / Dropdown Inputs (Select Option)

All dropdown controls (`<select>` / select option) across all pages in the project MUST use `font-medium` (`font-weight: 500`) and adhere to the standardized visual hierarchy identical to the **Kota / Kabupaten (Kemenag)** dropdown. This eliminates native iOS WebKit double-arrow picker rendering (`↕`), ensures consistent `38px` touch targets, and provides unified styling across all devices.

### Standard Specifications
- **Padding**: `py-1.5` for balanced vertical alignment and touch targets without rigid height constraints.
- **Font Weight**: `font-medium` (`font-weight: 500`) — **MANDATORY**.
- **Font Size**: `text-xs` (12px) with `text-gov-textMain`.
- **iOS WebKit Reset**: Guaranteed via `appearance: none` in CSS to remove the native iOS double-triangle picker (`↕`).
- **Chevron Icon**: Injected automatically via CSS background SVG using Lucide `chevron-down` (`14px` by `14px`, stroke `#94a3b8` / `slate-400`, positioned at `right 0.625rem center`).
- **Option Inheritance**: Child `<option>` elements automatically inherit `font-medium` and white background.
- **Global CSS Standard**: Enforced automatically via `resources/css/app.css`.

### Base Patterns

#### 1. Portal Filter Select with Left Icon (Matches Kota / Kabupaten Dropdown)
```html
<div class="relative">
    <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none flex items-center" wire:ignore>
        <i data-lucide="calendar" class="w-3.5 h-3.5 text-blue-600 shrink-0 inline-block"></i>
    </div>
    <select id="filter-month" wire:model.live="selectedMonth"
        class="w-full pl-8 pr-8 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs">
        <option value="1">Januari</option>
    </select>
</div>
```

#### 2. Standard Form Select (Modals & Full-Width Forms)
```html
<select wire:model="fieldName"
    class="w-full px-3.5 py-1.5 rounded-lg border border-gov-border bg-slate-50/50 hover:bg-slate-100/70 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition cursor-pointer font-medium text-xs text-gov-textMain shadow-2xs">
    <option value="">Pilih Opsi...</option>
    <option value="1">Opsi 1</option>
</select>
```

#### 3. Compact Filter Select (Admin Toolbar / Search Bar)
```html
<select wire:model.live="filterName"
    class="p-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium text-gov-textMain cursor-pointer focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy">
    <option value="all">Semua Status</option>
    <option value="active">Aktif</option>
</select>
```

#### 4. Quick Status Select (Inline Table Row)
```html
<select wire:change="updateStatus({{ $id }}, $event.target.value)"
    class="table-select text-xs font-medium rounded-lg border border-gov-border py-1 px-2 focus:ring-1 focus:ring-gov-navy transition cursor-pointer">
    <option value="AKTIF">Aktif</option>
    <option value="NONAKTIF">Nonaktif</option>
</select>
```

### ❌ DO NOT USE

| Deprecated Pattern | Replacement | Reason |
|:---|:---|:---|
| `<select class="... font-bold ...">` | `<select class="... font-medium ...">` | Prevents overly aggressive heavy text |
| `<select class="... font-semibold ...">` | `<select class="... font-medium ...">` | Standardized to font-medium (500) |
| `<select>` with native iOS arrows (`↕`) | `select:not(.sr-only)` in app.css | Removes ugly iOS picker double arrows and matches Kota dropdown |
| Rigid `h-[38px] py-2` on selects/filters | `py-1.5` padding | Prevents rigid height clipping and provides balanced spacing |

---

## 7. Form Date & Time Inputs (`<input type="date">`, `<input type="time">`)

All date and time inputs across modals, drawers, and form dialogs MUST render identically to text inputs with `font-medium`, `text-xs`, and `text-gov-textMain`. On iOS WebKit and Safari, native double-bubble/oval date pickers are reset via `app.css` to prevent squished internal buttons and ensure consistent left-aligned text values.

### Standard Specifications
- **Touch Target**: `p-2.5` or `py-1.5` touch target.
- **Font Weight**: `font-medium` (`font-weight: 500`) — **MANDATORY**.
- **Font Size**: `text-xs` (12px) with `text-gov-textMain`.
- **Cursor**: `cursor-pointer`.
- **iOS WebKit Reset**: Guaranteed via `app.css` (`input[type="date"]::-webkit-date-and-time-value { text-align: left; min-height: 1.25rem; display: block; width: 100%; }`).

### Standard Component Pattern
```html
<input x-model="form.date" type="date"
    class="w-full p-2.5 rounded-lg border border-gov-border bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition font-medium text-xs text-gov-textMain cursor-pointer">
```

---

## 8. Primary Action & Submit Buttons (Tombol "Simpan" & Sejenisnya)

All primary submit and save buttons across the application (e.g. "Simpan", "Tambah", "Perbarui", "Proses", "Daftarkan") in modals, drawers, and form dialogs MUST strictly use the standardized primary action button styling.

### Standard Specifications
- **Classes**:
  ```
  px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center
  ```
- **Loading State**: Always pair with `wire:loading.attr="disabled"` on the `<button>` and include dual `wire:loading.remove` and `wire:loading.inline-flex` spans with micro-spinner SVG to provide instant feedback.

### Standard Component Pattern
```html
<button type="submit" wire:loading.attr="disabled"
    class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
    <span wire:loading.remove wire:target="saveAction">
        Simpan Perubahan
    </span>
    <span wire:loading.inline-flex wire:target="saveAction"
        class="inline-flex items-center justify-center gap-2">
        <svg class="w-4 h-4 animate-spin text-white inline-block shrink-0" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="whitespace-nowrap">Menyimpan...</span>
    </span>
</button>
```

### ❌ DO NOT USE

| Deprecated Pattern | Replacement | Reason |
|:---|:---|:---|
| `bg-emerald-600 hover:bg-emerald-700` on form submit | `bg-gov-navy hover:bg-gov-navyHover` | Primary actions must strictly adhere to the brand corporate Navy theme (`bg-gov-navy`) |
| `px-3.5 py-1.5` or non-standard padding | `px-4 py-2` | Unified touch target and visual consistency across all forms and modals |
| Missing `disabled:opacity-50` or `cursor-pointer` | Include all standard utility classes | Ensures proper disabled styling during Livewire async network requests |

---

## 8. Table Styling & Row Borders (Garis Baris Tabel)

All data tables across all dashboard modules MUST use high-contrast row dividers with `divide-y divide-slate-300` (`#cbd5e1`). This guarantees that each row `<tr>` boundary is clearly distinguishable from the white/off-white background.

### Standard Specifications
- **Row Divider Color**: `divide-slate-300` (`#cbd5e1`) — **MANDATORY**.
- **Row Divider Thickness**: `1px` (`divide-y`).
- **Thead Bottom Border**: `border-b border-slate-300` (or `border-b border-gov-border`).
- **Global CSS Guarantee**: Explicit row border targeting in `resources/css/app.css`: `table > tbody.divide-y > tr, table > tbody.divide-slate-300 > tr, table > tbody.divide-y > :not(:last-child), table > tbody.divide-slate-300 > :not(:last-child) { border-color: #cbd5e1 !important; }`.


### Standard Table Architecture Pattern
```html
<div class="bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-300 text-gov-textMuted uppercase font-bold text-[11px] tracking-wider">
                    <th class="p-3.5 pl-4">Kolom 1</th>
                    <th class="p-3.5">Kolom 2</th>
                    <th class="p-3.5 text-center pr-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-300 font-medium text-slate-700">
                <tr class="hover:bg-slate-50/80 transition">
                    <td class="p-3.5 pl-4">Data 1</td>
                    <td class="p-3.5">Data 2</td>
                    <td class="p-3.5 text-center pr-4">...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

### ❌ DO NOT USE

| Deprecated Pattern | Replacement | Reason |
|:---|:---|:---|
| `<tbody class="divide-y divide-slate-100">` | `<tbody class="divide-y divide-slate-300">` | `slate-100` (`#f1f5f9`) is virtually invisible against white backgrounds |
| `<tr class="border-b border-slate-100">` | `<tr class="border-b border-slate-300">` | Provides high contrast separation between thead and tbody |
| Tables without row dividers | Always include `divide-y divide-slate-300` on `<tbody>` | Clear visual structure for financial and operational records |

---

## 9. Livewire Loading Indicators & Horizontal Alignment (Penyelarasan Icon & Teks Loading)

Saat menggunakan direktif `wire:loading` pada elemen interaktif (seperti tombol "Memperbarui Jadwal", "Menyimpan...", indikator filter, atau pill proses), seluruh komponen harus menjaga agar icon spinner dan teks pendampingnya selalu berada pada **satu baris horizontal (`flex-row items-center`)** dan tidak pernah bertumpuk vertikal.

### ⚠️ Akar Masalah / Perilaku Bawaan Livewire
Livewire secara bawaan menyuntikkan inline style `style="display: inline-block;"` saat direktif `wire:loading` aktif tanpa modifier khusus. Nilai inline style ini **menimpa utilitas Tailwind `inline-flex` atau `flex`**, sehingga browser memperlakukan elemen `<svg>` spinner dan teks `<span>` sebagai elemen bertumpuk vertikal (icon berada di atas teks alih-alih sebaris).

### Standar Spesifikasi Wajib (Mandatory Specifications)
1. **Gunakan Modifier Spesifik**: Wajib selalu gunakan `wire:loading.inline-flex` (atau `wire:loading.flex` untuk kontainer blok). **DILARANG** menggunakan `wire:loading` polos pada elemen flex/inline-flex.
2. **Inline Style Default `style="display: none;"`**: Wajib selalu sertakan `style="display: none;"` pada markup awal Blade agar elemen tidak berkedip saat render awal sebelum Livewire siap.
3. **Kontainer Loading**: Wajib menggunakan class:
   ```
   inline-flex flex-row items-center gap-2 shrink-0 whitespace-nowrap
   ```
4. **Elemen Icon Spinner (`<svg>`)**: Wajib menggunakan class:
   ```
   w-3.5 h-3.5 animate-spin shrink-0 inline-block
   ```
5. **Elemen Teks Pendamping (`<span>`)**: Wajib menggunakan class:
   ```
   whitespace-nowrap leading-none
   ```

### Standard Component Pattern
```html
<!-- Contoh 1: Indikator Loading Pil Status / Filter Toolbar -->
<div wire:loading.inline-flex style="display: none;"
    class="inline-flex flex-row items-center gap-2 text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 shrink-0 whitespace-nowrap">
    <svg class="w-3.5 h-3.5 animate-spin shrink-0 text-amber-600 inline-block" viewBox="0 0 24 24" fill="none">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <span class="whitespace-nowrap leading-none">Memperbarui jadwal...</span>
</div>

<!-- Contoh 2: Di dalam Tombol Form Submit -->
<button type="submit" wire:loading.attr="disabled"
    class="px-4 py-2 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold shadow-2xs transition cursor-pointer disabled:opacity-50 inline-flex items-center justify-center">
    <span wire:loading.remove wire:target="submitForm">Simpan</span>
    <span wire:loading.inline-flex wire:target="submitForm" style="display: none;"
        class="inline-flex flex-row items-center justify-center gap-2 shrink-0 whitespace-nowrap">
        <svg class="w-4 h-4 animate-spin shrink-0 text-white inline-block" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="whitespace-nowrap leading-none">Menyimpan...</span>
    </span>
</button>
```

### ❌ DO NOT USE

| Pola Salah (Deprecated / Bug-Prone) | Solusi Benar (Replacement) | Alasan Teknis |
|:---|:---|:---|
| `<div wire:loading class="inline-flex ...">` | `<div wire:loading.inline-flex style="display: none;" class="inline-flex flex-row items-center gap-2 whitespace-nowrap ...">` | `wire:loading` polos menyuntikkan `display: inline-block` yang menimpa `inline-flex` dan menyebabkan icon & teks terbungkus menjadi 2 baris bertumpuk vertikal |
| `<svg class="animate-spin">` tanpa `shrink-0` | `<svg class="... shrink-0 inline-block">` | Mencegah penyusutan/gepeng icon saat teks panjang |
| `<span>Loading...</span>` tanpa `whitespace-nowrap` | `<span class="whitespace-nowrap leading-none">Loading...</span>` | Menjamin teks tidak terpotong atau turun ke baris baru |

---

## 10. Pagination UI Component (Navigasi Halaman)

Seluruh komponen pagination (navigasi nomor halaman) baik di halaman Admin Backoffice maupun Portal Publik (seperti Kegiatan Masjid) **WAJIB** menggunakan gaya seragam berupa bilah kotak numerik terpadu (*unified pagination bar*) berlatar putih dengan pembatas halus (*divide border*) dan tombol aktif bertema korporat Navy (`bg-gov-navy`).

### Standard Specifications
- **Tinggi Elemen (Height)**: `h-9` (36px).
- **Lebar Minimal Tombol Numerik**: `min-w-[36px]`.
- **Wadah Luar (Bar Container)**:
  ```html
  inline-flex items-center rounded-lg border border-slate-200 bg-white shadow-2xs overflow-hidden shrink-0 text-xs select-none
  ```
- **Halaman Aktif (Active Page)**:
  Wajib menggunakan latar `bg-gov-navy` dengan teks putih tebal:
  ```html
  inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-xs font-bold text-white bg-gov-navy border-r border-gov-navy cursor-default z-10 shadow-2xs
  ```
- **Halaman Tidak Aktif (Inactive Pages)**:
  ```html
  inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-xs font-medium text-slate-700 hover:text-gov-navy hover:bg-slate-50 border-r border-slate-200 transition cursor-pointer shrink-0
  ```
- **Pemisah Titik Tiga (Ellipsis `...`)**:
  ```html
  inline-flex items-center justify-center min-w-[34px] h-9 px-2 text-xs font-semibold text-slate-400 bg-slate-50/40 border-r border-slate-200 cursor-default shrink-0
  ```
- **Tombol Panah Navigasi (`<` & `>`)**:
  - Aktif: `inline-flex items-center justify-center w-9 h-9 text-slate-600 hover:text-gov-navy hover:bg-slate-50 border-r border-slate-200 transition cursor-pointer shrink-0`
  - Nonaktif (*Disabled*): `inline-flex items-center justify-center w-9 h-9 text-slate-300 bg-slate-50/50 cursor-not-allowed border-r border-slate-200 shrink-0`
- **Teks Informasi (Info Text)**:
  Wajib berbahasa Indonesia baku:
  ```html
  Menampilkan <span class="font-bold text-slate-800">1</span> sampai <span class="font-bold text-slate-800">10</span> dari <span class="font-bold text-slate-800">45</span> data
  ```
- **Responsif Mobile**:
  Bilah navigasi diletakkan di dalam kontainer `max-w-full overflow-x-auto py-0.5 flex items-center justify-center sm:justify-end w-full sm:w-auto` agar di layar ponsel tetap menampilkan nomor halaman numerik yang rapi dan dapat digeser (*scrollable*) jika nomor banyak, serta **DILARANG** menampilkan tombol teks mentah terpisah seperti `pagination.previous` / `pagination.next`.
- **Lokalisasi Bahasa**:
  Dukungan teks multi-bahasa dijamin melalui berkas `lang/id/pagination.php` dan `lang/en/pagination.php`.

### Standard Component Pattern
```html
<nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
    <div class="text-center sm:text-left text-slate-500 font-medium order-2 sm:order-1">
        <p class="leading-5">
            <span>Menampilkan</span>
            <span class="font-bold text-slate-800">{{ $paginator->firstItem() }}</span>
            <span>sampai</span>
            <span class="font-bold text-slate-800">{{ $paginator->lastItem() }}</span>
            <span>dari</span>
            <span class="font-bold text-slate-800">{{ $paginator->total() }}</span>
            <span>data</span>
        </p>
    </div>

    <div class="max-w-full overflow-x-auto py-0.5 flex items-center justify-center sm:justify-end order-1 sm:order-2 w-full sm:w-auto">
        <div class="inline-flex items-center rounded-lg border border-slate-200 bg-white shadow-2xs overflow-hidden shrink-0 text-xs select-none">
            {{-- Tombol Previous (<) --}}
            {{-- Tombol Nomor Halaman (1, 2, 3...) --}}
            {{-- Tombol Next (>) --}}
        </div>
    </div>
</nav>
```

### ❌ DO NOT USE

| Pola Salah (Deprecated / Bug-Prone) | Solusi Benar (Replacement) | Alasan Teknis |
|:---|:---|:---|
| Teks mentah `pagination.previous` / `pagination.next` | Gunakan icon chevron `<` dan `>` di dalam bilah terpadu | Teks terjemahan mentah akibat ketiadaan file `lang/id` merusak tampilan UI |
| Tampilan mobile dengan dua tombol blok lebar terpisah (`sm:hidden`) | Gunakan bilah numerik responsif yang sama (`inline-flex rounded-lg overflow-x-auto`) | Pengalaman pengguna (UX) terfragmentasi dan inkonsisten antara mobile dan desktop |
| Warna tombol aktif non-navy (misal: `bg-blue-600`, `bg-indigo-600`) | Wajib selalu `bg-gov-navy text-white font-bold` | Selaras dengan identitas brand visual portal dan dashboard Masjid Salahuddin |

---

## 11. Form Input Validation Specifications (Password, NIP Pegawai, & WhatsApp)

Standar baku pengisian formulir akun, pengguna, dan pendaftaran komitmen yang wajib dipatuhi:

### A. Kata Sandi (Password Complexity)
- **Aturan Validasi**:
  - Minimal 8 karakter (`min:8`).
  - Wajib mengandung huruf kapital (`mixedCase()` / `[A-Z]`).
  - Wajib mengandung huruf kecil (`mixedCase()` / `[a-z]`).
  - Wajib mengandung angka (`numbers()` / `[0-9]`).
  - Wajib mengandung simbol atau karakter khusus (`symbols()` / `[\W_]`).
- **Penerapan Teknis**:
  Gunakan aturan `Illuminate\Validation\Rules\Password::min(8)->letters()->mixedCase()->numbers()->symbols()`.
- **Teks Bantuan UI**:
  `Minimal 8 karakter, wajib kombinasi huruf kapital, angka, dan simbol.`

### B. NIP Pegawai (Nomor Induk Pegawai)
- **Aturan Validasi**:
  - Wajib tepat berjumlah 18 digit angka (`digits:18`).
  - Karakter spasi, strip, atau pemisah lainnya dibersihkan secara otomatis di sisi backend sebelum validasi (`preg_replace('/\D/', '', $nip)`).
- **Teks Bantuan UI**:
  `Wajib tepat 18 digit angka (contoh: 198501012010121001).`

### C. Nomor WhatsApp
- **Aturan Validasi**:
  - Wajib diawali dengan karakter `"08"` dan terdiri dari 10–15 digit angka (`regex:/^08[0-9]{8,13}$/`).
  - Awalan `+628` atau `628` serta tanda hubung otomatis dinormalisasi menjadi `08` di sisi backend.
- **Teks Bantuan UI**:
  `Wajib diawali dengan "08" (10-15 digit, contoh: 081234567890).`

### ❌ DO NOT USE

| Pola Salah (Deprecated / Bug-Prone) | Solusi Benar (Replacement) | Alasan Teknis |
|:---|:---|:---|
| Password lemah tanpa kompleksitas (`min:6` polos) | Wajib kombinasi minimal 8 karakter dengan huruf kapital, angka, dan simbol | Menghindari celah keamanan brute force pada akun |
| Validasi NIP longgar (`min:9`, `max:30` string) | Wajib tepat 18 digit angka (`digits:18`) | NIP ASN/Kemenkeu memiliki format baku tepat 18 digit angka |
| Nomor HP bebas tanpa awalan 08 | Wajib diawali karakter `08` (`regex:/^08[0-9]{8,13}$/`) | Menjamin nomor adalah nomor seluler WhatsApp Indonesia yang valid |

---

## 12. Sub-tab Navigation Bars (Menu Tab Sekunder)

Seluruh navigasi sub-tab sekunder di modul admin (Kajian/Kegiatan, Keuangan, Program Sosial, Pengguna/Kepengurusan) dan portal wajib menggunakan standar dimensi berikut:

### Standard Specifications
- **Padding**: `px-3.5 py-2` — **MANDATORY** (memberikan ruang sentuh vertikal yang proporsional dan nyaman).
- **Border Radius**: `rounded-lg` (`.5rem`).
- **Typography**: `text-xs` font-medium untuk tab pasif, font-bold untuk tab aktif.
- **Wadah (Container)**: `<div class="text-xs flex flex-col sm:flex-row sm:items-center justify-start gap-3 bg-white p-3 rounded-xl border border-gov-border shadow-2xs">`.
- **Status Aktif**: `bg-gov-navy text-white font-bold shadow-2xs`.
- **Status Inaktif**: `bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium`.

### Component Pattern
```html
<button type="button" @click="activeSubTab = 'foo'"
    class="px-3.5 py-2 rounded-lg transition whitespace-nowrap cursor-pointer flex items-center gap-2"
    :class="activeSubTab === 'foo' ? 'bg-gov-navy text-white font-bold shadow-2xs' : 'bg-white text-slate-700 border border-gov-border hover:bg-slate-50 font-medium'">
    <i data-lucide="calendar" class="w-4 h-4"></i>
    <span>Nama Sub-tab</span>
</button>
```


