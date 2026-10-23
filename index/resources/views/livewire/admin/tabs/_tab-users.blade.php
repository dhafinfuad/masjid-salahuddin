<div class="space-y-6">

    <!-- ======================================================== -->
    <!-- TOP HEADER CARD (SESUAI SUBTAB AKTIF) -->
    <!-- ======================================================== -->
    <!-- Top Header Card: Pengguna -->
    <div x-show="userSubTab === 'pengguna'">
        <!-- Top Header & Filter Card -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-3.5 sm:p-5 rounded-xl border border-gov-border shadow-2xs">
            <div class="hidden sm:flex items-center space-x-2.5">
                <div
                    class="w-8 h-8 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center border border-gov-border shrink-0">
                    <i data-lucide="user-cog" class="w-4 h-4 text-gov-navy"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base text-gov-textMain">Manajemen Pengguna & Hak Akses</h3>
                    <p class="text-xs text-gov-textMuted">Kelola akun pengurus DKM (Master, Ketua, Sekretaris,
                        Bendahara) dan Jamaah.</p>
                </div>
            </div>
            @if(Auth::user()->canManage())
                <div class="flex items-center gap-2">
                    <button type="button" @click="openCreateUser()"
                        class="h-9 px-3.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition inline-flex items-center space-x-2 cursor-pointer">
                        <i data-lucide="user-plus" class="w-4 h-4 text-amber-400"></i>
                        <span>Tambah Pengguna</span>
                    </button>
                </div>
            @else
                <div>
                    <span
                        class="inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-slate-100 text-slate-500 text-xs font-medium border border-gov-border">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Mode Lihat Saja</span>
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Top Header Card: Pegawai -->
    <div x-show="userSubTab === 'pegawai'" x-cloak>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-3.5 sm:p-5 rounded-xl border border-gov-border shadow-2xs">
            <div class="hidden sm:flex items-center space-x-2.5 min-w-0 flex-1">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-gov-navy flex items-center justify-center border border-gov-border shrink-0">
                    <svg class="w-4 h-4 text-gov-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="16" rx="3"/>
                        <circle cx="9" cy="10" r="2"/>
                        <line x1="15" y1="8" x2="17" y2="8"/>
                        <line x1="15" y1="12" x2="17" y2="12"/>
                        <line x1="7" y1="16" x2="17" y2="16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-base text-gov-textMain">Data Pegawai Muslim KPP Madya Malang</h3>
                    <p class="text-xs text-gov-textMuted">Database induk seluruh pegawai muslim kantor untuk penugasan ibadah, rekap infaq, dan inventarisasi jamaah.</p>
                </div>
            </div>
            @if(Auth::user()->canManage())
                <div class="flex items-center gap-2.5 shrink-0 flex-nowrap">
                    <button type="button" @click="showImportEmployeeModal = true" wire:click="openImportEmployeeModal"
                        class="h-9 px-3.5 rounded-lg bg-white hover:bg-slate-50 text-gov-navy font-bold text-xs border border-gov-border shadow-2xs transition inline-flex items-center space-x-2 cursor-pointer shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="12" y1="18" x2="12" y2="12"/>
                            <line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                        <span>Impor Berkas Pegawai</span>
                    </button>
                    <button type="button" @click="showEmployeeModal = true" wire:click="openCreateEmployeeModal"
                        class="h-9 px-3.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white font-bold text-xs shadow-2xs transition inline-flex items-center space-x-2 cursor-pointer shrink-0 whitespace-nowrap">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        <span>Tambah Pegawai</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Top Header Card: Kepengurusan -->
    <div x-show="userSubTab === 'kepengurusan'" x-cloak>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-3.5 sm:p-5 rounded-xl border border-gov-border shadow-2xs">
            <div class="hidden sm:flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-gov-navy flex items-center justify-center border border-blue-200 shrink-0">
                    <i data-lucide="shield-check" class="w-5 h-5 text-gov-navy"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-base text-gov-textMain">Manajemen Kepengurusan Takmir & Dokumen SK</h3>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola berkas PDF resmi SK Kepala KPP Madya Malang, susunan personalia takmir, dan uraian Tupoksi.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if(Auth::user()->canManage())
                    <button type="button" 
                        wire:click="openTemplateKepModal"
                        class="h-9 px-3.5 rounded-lg bg-gov-navy hover:bg-gov-navyHover text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer"
                        title="Buka Template & Perbarui Surat KEP Kepala KPP Madya Malang">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span>Template Surat KEP Takmir</span>
                    </button>
                @endif

                <a href="{{ url('/profil') }}" target="_blank"
                    class="h-9 px-3.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Lihat di Portal</span>
                </a>

                @if(Auth::user()->canManage())
                    <button type="button" 
                        wire:click="resetTakmirToDefault"
                        wire:confirm="Kembalikan seluruh data dokumen SK, susunan pengurus, dan tupoksi ke standar SK KEP-48 awal?"
                        class="h-9 px-3.5 rounded-lg border border-rose-200 bg-rose-50/50 hover:bg-rose-50 text-rose-700 text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-rose-500"></i>
                        <span>Reset ke Default SK</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- SUBTAB 1: MANAJEMEN PENGGUNA & ROLE (MILESTONE 4) -->
    <!-- ======================================================== -->
    <div x-show="userSubTab === 'pengguna'" class="space-y-6">

        <!-- Role Filter Card & Summary -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-gov-border text-xs shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 w-full sm:w-auto">
                <div class="w-full sm:w-auto">
                    <x-custom-select 
                        wire:model.live="userFilterRole"
                        :options="[
                            'all' => 'Semua Role',
                            'Master' => 'Master Admin',
                            'Ketua' => 'Ketua DKM',
                            'Sekretaris' => 'Sekretaris',
                            'Bendahara' => 'Bendahara',
                            'Jamaah' => 'Jamaah (Viewer)',
                        ]"
                        placeholder="Semua Role"
                        icon="shield"
                        class="w-full sm:w-44"
                    />
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-64" x-data="{
                    clearSearch() {
                        $wire.clearUserSearch();
                    }
                }">
                    <i data-lucide="search"
                        class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari nama, email..."
                        class="h-9 w-full pl-8 pr-9 text-xs rounded-lg border border-gov-border bg-slate-50 font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                    <button type="button" @click="clearSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 transition cursor-pointer flex items-center justify-center" title="Reset pencarian ke data seharusnya">
                        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="text-slate-500 font-medium text-xs">
                Total: <span class="font-bold text-gov-navy">{{ method_exists($usersList, 'total') ? $usersList->total() : $usersList->count() }}</span> pengguna
            </div>
        </div>

        <!-- Desktop Table Card -->
        <div class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-visible">
            <table class="w-full text-left text-xs border-collapse">
                    <thead
                        class="bg-slate-50/80 text-gov-textMuted uppercase font-bold border-b border-slate-300 tracking-wider text-[11px]">
                        <tr>
                            <th wire:click="sortBy('name', 'users')" class="py-3 px-4 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                <div class="flex items-center gap-1">
                                    <span>Nama Pengguna</span>
                                    <x-sort-icon field="name" table="users" />
                                </div>
                            </th>
                            <th wire:click="sortBy('email', 'users')" class="py-3 px-4 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                <div class="flex items-center gap-1">
                                    <span>Alamat Email</span>
                                    <x-sort-icon field="email" table="users" />
                                </div>
                            </th>
                            <th wire:click="sortBy('role', 'users')" class="py-3 px-4 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                <div class="flex items-center gap-1">
                                    <span>Role & Level Akses</span>
                                    <x-sort-icon field="role" table="users" />
                                </div>
                            </th>
                            <th wire:click="sortBy('status', 'users')" class="py-3 px-4 text-center cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                                <div class="flex items-center justify-center gap-1">
                                    <span>Status</span>
                                    <x-sort-icon field="status" table="users" />
                                </div>
                            </th>
                            <th class="py-3 px-4 text-center w-20 rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 transition-opacity duration-150">
                        @forelse($usersList as $u)
                            @php
                                $badgeColor = match ($u->role) {
                                    'Master', 'admin' => 'bg-purple-100 text-purple-800 border-purple-300',
                                    'Ketua' => 'bg-emerald-50 text-emerald-700 border-emerald-400',
                                    'Sekretaris', 'operator' => 'bg-blue-100 text-blue-800 border-blue-300',
                                    'Bendahara' => 'bg-amber-100 text-amber-800 border-amber-300',
                                    default => 'bg-slate-100 text-slate-700 border-slate-300',
                                };
                                $accessLevel = match ($u->role) {
                                    'Master', 'admin', 'Ketua', 'Sekretaris', 'Bendahara', 'operator' => 'Akses Penuh (CRUD)',
                                    default => 'Read-Only',
                                };
                            @endphp
                            <tr wire:key="user-row-{{ $u->id }}" class="hover:bg-slate-50/75 transition-colors">
                                <!-- Nama -->
                                <td class="py-3 px-4 font-semibold text-gov-textMain">
                                    <div class="flex items-center space-x-2.5">
                                        <div
                                            class="w-8 h-8 rounded-full bg-slate-100 text-gov-navy font-bold flex items-center justify-center text-xs shrink-0 border border-slate-200">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="block font-bold text-slate-900">{{ $u->name }}</span>
                                            @if($u->id === Auth::id())
                                                <span
                                                    class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] bg-emerald-50 text-emerald-700 font-semibold border border-emerald-400">
                                                    Anda
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Alamat Email -->
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-800">{{ $u->email }}</div>
                                </td>

                                <!-- Role & Access Level -->
                                <td class="py-3 px-4">
                                    <div class="space-y-1">
                                        <span
                                            class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none {{ $badgeColor }}">
                                            {{ $u->role }}
                                        </span>
                                        <span class="block text-[10px] text-slate-400 font-medium">
                                            {{ $accessLevel }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="py-3 px-4 text-center">
                                    @if(Auth::user()->canManage() && $u->id !== Auth::id())
                                        <button type="button" wire:click="toggleUserStatus({{ $u->id }})"
                                            class="w-[72px] justify-center inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold border leading-none transition cursor-pointer shrink-0 {{ ($u->status ?? 'AKTIF') === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-400 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}"
                                            style="width: 72px; min-width: 72px; max-width: 72px;"
                                            title="Klik untuk mengubah status">
                                            {{ ($u->status ?? 'AKTIF') === 'AKTIF' ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    @else
                                        <span
                                            class="w-[72px] justify-center inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold border leading-none shrink-0 {{ ($u->status ?? 'AKTIF') === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-400' : 'bg-rose-50 text-rose-700 border-rose-200' }}"
                                            style="width: 72px; min-width: 72px; max-width: 72px;">
                                            {{ ($u->status ?? 'AKTIF') === 'AKTIF' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-center whitespace-nowrap relative" x-data="{ openMenu: false }">
                                    @if(Auth::user()->canManage())
                                        <div class="inline-flex items-center justify-center">
                                            <!-- Tombol Titik Tiga Vertikal -->
                                            <button type="button" 
                                                @click="openMenu = !openMenu" 
                                                class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                                :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''"
                                                title="Menu Aksi">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu Aksi -->
                                            <div x-show="openMenu" 
                                                x-cloak
                                                x-transition:enter="transition ease-out duration-50"
                                                x-transition:enter-start="transform opacity-0 scale-98"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-50"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-98"
                                                @click.outside="openMenu = false"
                                                @keydown.escape.window="openMenu = false"
                                                class="absolute right-3 top-full mt-1 w-48 bg-white rounded-xl shadow-xl border border-gov-border py-1 z-40 text-xs text-left divide-y divide-slate-100">
                                                
                                                <div class="py-1">
                                                    <!-- Edit Pengguna -->
                                                    <button type="button" 
                                                        @click="openMenu = false; openEditUser({{ Js::from($u) }})"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                                        <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Edit Pengguna</div>
                                                    </button>

                                                    @if($u->id !== Auth::id())
                                                        <!-- Hapus Pengguna -->
                                                        <button type="button"
                                                            @click="openMenu = false; openDeleteModal({{ Js::from([
                                                                'id' => $u->id,
                                                                'action' => 'deleteUser',
                                                                'title' => 'Hapus Pengguna',
                                                                'itemName' => $u->name . ' (' . $u->email . ')',
                                                                'message' => 'Apakah Anda yakin ingin menghapus pengguna ini dari sistem?',
                                                            ]) }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                            <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Pengguna</div>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Lihat saja</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                    <i data-lucide="users" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                    Tidak ada data pengguna yang sesuai dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            @if(method_exists($usersList, 'hasPages') && $usersList->hasPages())
                <div class="p-3 bg-white border-t border-gov-border">
                    {{ $usersList->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>

        <!-- Mobile Standalone Responsive Cards -->
        <div class="md:hidden space-y-3" wire:loading.class="opacity-50 pointer-events-none" wire:target="gotoPage, nextPage, previousPage">
            @forelse($usersList as $u)
                @php
                    $badgeColor = match ($u->role) {
                        'Master', 'admin' => 'bg-purple-100 text-purple-800 border-purple-300',
                        'Ketua' => 'bg-emerald-50 text-emerald-700 border-emerald-400',
                        'Sekretaris', 'operator' => 'bg-blue-100 text-blue-800 border-blue-300',
                        'Bendahara' => 'bg-amber-100 text-amber-800 border-amber-300',
                        default => 'bg-slate-100 text-slate-700 border-slate-300',
                    };
                    $accessLevel = match ($u->role) {
                        'Master', 'admin', 'Ketua', 'Sekretaris', 'Bendahara', 'operator' => 'Akses Penuh (CRUD)',
                        default => 'Read-Only',
                    };
                    $isAktif = ($u->status ?? 'AKTIF') === 'AKTIF';
                @endphp
                <div wire:key="user-card-{{ $u->id }}" class="bg-white rounded-xl border border-gov-border p-4 shadow-2xs space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-9 h-9 rounded-full bg-slate-100 text-gov-navy font-bold flex items-center justify-center text-xs shrink-0 border border-slate-200">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-slate-900 text-sm">{{ $u->name }}</span>
                                    @if($u->id === Auth::id())
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] bg-emerald-50 text-emerald-700 font-semibold border border-emerald-400">
                                            Anda
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 font-medium">{{ $u->email }}</p>
                            </div>
                        </div>
                        <div>
                            @if(Auth::user()->canManage() && $u->id !== Auth::id())
                                <button type="button" wire:click="toggleUserStatus({{ $u->id }})"
                                    class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none transition cursor-pointer {{ $isAktif ? 'bg-emerald-50 text-emerald-700 border-emerald-400 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}"
                                    title="Klik untuk mengubah status">
                                    {{ $isAktif ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            @else
                                <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none {{ $isAktif ? 'bg-emerald-50 text-emerald-700 border-emerald-400' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                    {{ $isAktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none {{ $badgeColor }}">
                                {{ $u->role }}
                            </span>
                            <span class="text-[11px] text-slate-400">({{ $accessLevel }})</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-2 border-t border-slate-100 relative" x-data="{ openMenu: false }">
                        @if(Auth::user()->canManage())
                            <div class="inline-flex items-center justify-center">
                                <!-- Tombol Titik Tiga Vertikal Mobile -->
                                <button type="button" 
                                    @click="openMenu = !openMenu" 
                                    class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                    :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''"
                                    title="Menu Aksi">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu Aksi Mobile -->
                                <div x-show="openMenu" 
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-50"
                                    x-transition:enter-start="transform opacity-0 scale-98"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-50"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-98"
                                    @click.outside="openMenu = false"
                                    @keydown.escape.window="openMenu = false"
                                    class="absolute right-0 top-full mt-1 w-48 bg-white rounded-xl shadow-xl border border-gov-border py-1 z-40 text-xs text-left divide-y divide-slate-100">
                                    
                                    <div class="py-1">
                                        <button type="button" 
                                            @click="openMenu = false; openEditUser({{ Js::from($u) }})"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                            <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                            <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Edit Pengguna</div>
                                        </button>

                                        @if($u->id !== Auth::id())
                                            <button type="button"
                                                @click="openMenu = false; openDeleteModal({{ Js::from([
                                                    'id' => $u->id,
                                                    'action' => 'deleteUser',
                                                    'title' => 'Hapus Pengguna',
                                                    'itemName' => $u->name . ' (' . $u->email . ')',
                                                    'message' => 'Apakah Anda yakin ingin menghapus pengguna ini dari sistem?',
                                                ]) }})"
                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Pengguna</div>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">Lihat saja</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-gov-border p-8 text-center text-slate-400 shadow-2xs">
                    <div class="flex flex-col items-center justify-center py-4">
                        <i data-lucide="users" class="w-8 h-8 text-slate-300 mb-2"></i>
                        <p class="font-medium text-slate-500">Tidak ada data pengguna yang sesuai dengan kriteria pencarian.</p>
                    </div>
                </div>
            @endforelse

            @if(method_exists($usersList, 'hasPages') && $usersList->hasPages())
                <div class="p-3 bg-white rounded-xl border border-gov-border shadow-2xs">
                    {{ $usersList->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>
    <!-- AKHIR SUBTAB 1: PENGGUNA -->

    <!-- ======================================================== -->
    <!-- SUBTAB 2: DATA PEGAWAI MUSLIM KPP MADYA MALANG -->
    <!-- ======================================================== -->
    <div x-show="userSubTab === 'pegawai'" x-cloak class="space-y-6">
        @include('livewire.admin.tabs._tab-pegawai')
    </div>

    <!-- ======================================================== -->
    <!-- SUBTAB 3: KEPENGURUSAN & DOKUMEN SK -->
    <!-- ======================================================== -->
    <div x-show="userSubTab === 'kepengurusan'" x-cloak class="space-y-6">
        @include('livewire.admin.tabs._tab-kepengurusan')
    </div>

</div>
