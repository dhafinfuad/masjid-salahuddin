<!-- ======================================================== -->
<!-- SUBTAB 2: MASTER DATA PEGAWAI MUSLIM KPP MADYA MALANG -->
<!-- ======================================================== -->
<div class="space-y-6">

    <!-- Ringkasan Hasil Impor Spreadsheet (jika baru selesai diimpor) -->
    @if($importEmployeeResult)
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs flex items-center justify-between gap-3 shadow-2xs animate-in fade-in">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100/80 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <span class="font-bold text-emerald-900 block">Hasil Impor Data Pegawai:</span>
                    <span class="text-[11px] text-emerald-700">
                        Total baris: <strong>{{ $importEmployeeResult['total_rows'] }}</strong> | 
                        Baru: <strong>{{ $importEmployeeResult['imported'] }}</strong> | 
                        Diperbarui: <strong>{{ $importEmployeeResult['updated'] }}</strong> | 
                        Tertaut akun: <strong>{{ $importEmployeeResult['linked'] }}</strong>
                        @if(($importEmployeeResult['skipped_non_muslim'] ?? 0) > 0)
                            | Non-muslim dilewati: <strong>{{ $importEmployeeResult['skipped_non_muslim'] }}</strong>
                        @endif
                    </span>
                </div>
            </div>
            <button type="button" wire:click="$set('importEmployeeResult', null)" class="text-emerald-500 hover:text-emerald-700 p-1.5 rounded-lg hover:bg-emerald-100 transition cursor-pointer" title="Tutup ringkasan">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- Filter Card & Pencarian Pegawai -->
    <div class="flex flex-col md:flex-row md:items-center gap-2">
        <!-- Search Input -->
        <div class="relative flex-1 min-w-0" x-data="{
            clearEmpSearch() {
                $wire.set('employeeSearch', '');
                $wire.set('search', '');
            }
        }">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            <input wire:model.live.debounce.300ms="employeeSearch" type="text"
                placeholder="Cari nama, NIP 9/18 digit, jabatan..."
                class="h-9 w-full pl-8 pr-9 text-xs rounded-lg border border-gov-border bg-white font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition shadow-2xs">
            <button type="button" @click="clearEmpSearch()"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 hover:text-slate-600 transition cursor-pointer flex items-center justify-center"
                title="Reset pencarian">
                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Filters Row -->
        <div class="flex flex-row items-center gap-2 w-full md:w-auto shrink-0">
            <!-- Filter Status Akun Aplikasi -->
            <div class="flex-1 md:w-44">
                <x-custom-select 
                    wire:model.live="employeeFilterLink"
                    :options="[
                        'all' => 'Akun (Semua)',
                        'linked' => 'Punya Akun',
                        'unlinked' => 'Belum Akun',
                    ]"
                    placeholder="Akun (Semua)"
                    class="w-full"
                />
            </div>

            <button type="button" wire:click="resetEmployeeFilters"
                class="h-9 px-3 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-700 hover:text-gov-navy text-xs font-semibold shadow-2xs transition cursor-pointer flex items-center gap-1.5 shrink-0"
                title="Reset filter dan pencarian ke kondisi awal">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-slate-500"></i>
                <span class="whitespace-nowrap">Reset</span>
            </button>
        </div>
    </div>

    <!-- Desktop Table Card -->
    <div class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-visible">
        <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50/80 text-gov-textMuted uppercase font-bold border-b border-slate-300 tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-3.5 w-12 text-center">No</th>
                        <th wire:click="sortBy('name', 'employees')" class="py-3 px-4 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                            <div class="flex items-center gap-1">
                                <span>Nama Pegawai &amp; Gelar</span>
                                <x-sort-icon field="name" table="employees" />
                            </div>
                        </th>
                        <th wire:click="sortBy('jabatan', 'employees')" class="py-3 px-4 cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                            <div class="flex items-center gap-1">
                                <span>Jabatan / Unit</span>
                                <x-sort-icon field="jabatan" table="employees" />
                            </div>
                        </th>
                        <th class="py-3 px-4 text-center">Status Akun Aplikasi</th>
                        <th wire:click="sortBy('status', 'employees')" class="py-3 px-4 text-center cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                            <div class="flex items-center justify-center gap-1">
                                <span>Status Kantor</span>
                                <x-sort-icon field="status" table="employees" />
                            </div>
                        </th>
                        <th class="py-3 px-4 text-center w-20 rounded-tr-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="employeeFilterLink, employeeSearch, gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 transition-opacity duration-150">
                    @forelse($employeesList as $index => $emp)
                        @php
                            $rowNum = $loop->iteration + ($employeesList->firstItem() ? $employeesList->firstItem() - 1 : 0);
                        @endphp
                        <tr wire:key="employee-row-{{ $emp->id }}" class="hover:bg-slate-50/75 transition-colors">
                            <!-- No -->
                            <td class="py-3 px-3.5 text-center font-mono text-slate-500">
                                {{ $rowNum }}
                            </td>

                            <!-- Nama Pegawai -->
                            <td class="py-3 px-4 font-semibold text-gov-textMain">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-gov-navy font-bold flex items-center justify-center text-xs shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($emp->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="block font-bold text-slate-900 truncate max-w-xs sm:max-w-md">{{ $emp->name }}</span>
                                        @if($emp->unit_kerja)
                                            <span class="text-[10px] text-slate-400 block truncate">{{ $emp->unit_kerja }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Jabatan / Seksi -->
                            <td class="py-3 px-4">
                                <div class="text-slate-700 font-medium">
                                    {{ $emp->jabatan ?: 'Pegawai KPP Madya Malang' }}
                                </div>
                            </td>

                            <!-- Status Akun Aplikasi (Badge langsung merujuk ke Role) -->
                            <td class="py-3 px-4 text-center">
                                @if($emp->user_id && $emp->user)
                                    @php
                                        $role = $emp->user->role;
                                        $badgeClasses = match($role) {
                                            'Master', 'admin', 'Administrator' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'Ketua', 'Sekretaris', 'Bendahara' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-emerald-50 text-emerald-700 border-emerald-400',
                                        };
                                    @endphp
                                    <div class="inline-flex flex-col items-center gap-0.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $badgeClasses }}">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>{{ $role }}</span>
                                        </span>
                                    </div>
                                @else
                                    <div class="inline-flex flex-col items-center gap-1">
                                        <span class="text-[11px] text-slate-400 font-medium">Belum Terdaftar</span>
                                        @if(Auth::user()->canManage())
                                            <button type="button"
                                                @click="openConfirmModal({{ Js::from([
                                                    'id' => $emp->id,
                                                    'action' => 'createUserFromEmployee',
                                                    'title' => 'Tautkan / Buat Akun Pengguna',
                                                    'itemName' => $emp->name . ($emp->nip_pendek ? ' (NIP: ' . $emp->nip_pendek . ')' : ''),
                                                    'message' => 'Tautkan ke akun pengguna yang cocok atau buatkan akun baru untuk ' . $emp->name . '?',
                                                    'type' => 'primary',
                                                    'confirmButtonText' => 'Ya, Proses Akun',
                                                    'noticeText' => 'Sistem akan memeriksa apakah akun pengguna sudah ada untuk ditautkan, atau membuatkan akun baru dengan peran Jamaah.',
                                                ]) }})"
                                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-gov-navy/10 hover:bg-gov-navy hover:text-white text-gov-navy border border-gov-navy/20 transition cursor-pointer"
                                                title="Tautkan otomatis jika akun sudah ada, atau buatkan akun baru">
                                                Buat Akun
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Status Kantor (Aktif / Mutasi) -->
                            <td class="py-3 px-4 text-center">
                                @if(Auth::user()->canManage())
                                    <button type="button" wire:click="toggleEmployeeStatus({{ $emp->id }})"
                                        class="w-[70px] justify-center inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold border leading-none transition cursor-pointer shrink-0 {{ $emp->status === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-400 hover:bg-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}"
                                        style="width: 70px; min-width: 70px; max-width: 70px;"
                                        title="Klik untuk mengubah status aktif/mutasi">
                                        {{ $emp->status === 'AKTIF' ? 'Aktif' : 'Mutasi' }}
                                    </button>
                                @else
                                    <span class="w-[70px] justify-center inline-flex items-center h-[20px] px-2 rounded-full text-[11px] font-semibold border leading-none shrink-0 {{ $emp->status === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-400' : 'bg-amber-50 text-amber-700 border-amber-200' }}"
                                          style="width: 70px; min-width: 70px; max-width: 70px;">
                                        {{ $emp->status === 'AKTIF' ? 'Aktif' : 'Mutasi' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
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
                                                <!-- Ubah Data Pegawai -->
                                                <button type="button" 
                                                    @click="openMenu = false; showEmployeeModal = true"
                                                    wire:click="openEditEmployeeModal({{ $emp->id }})"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                                    <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                                    <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Ubah Data Pegawai</div>
                                                </button>

                                                <!-- Hapus Pegawai -->
                                                <button type="button" 
                                                    @click="openMenu = false; openDeleteModal({{ Js::from([
                                                        'id' => $emp->id,
                                                        'action' => 'deleteEmployee',
                                                        'title' => 'Hapus Data Pegawai',
                                                        'itemName' => $emp->name . ($emp->nip_pendek ? ' (NIP: ' . $emp->nip_pendek . ')' : ''),
                                                        'message' => 'Apakah Anda yakin ingin menghapus data pegawai ' . $emp->name . ' dari database?',
                                                    ]) }})"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                                    <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                    <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Pegawai</div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Lihat saja</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-empty-state 
                            :isTable="true" 
                            colspan="6" 
                            icon="id-card" 
                            title="Tidak ada data pegawai" 
                            subtitle="Belum ada data pegawai yang sesuai dengan kriteria atau filter pencarian ini." 
                        />
                    @endforelse
                </tbody>
            </table>

        @if(method_exists($employeesList, 'hasPages') && $employeesList->hasPages())
            <div class="p-3 border-t border-slate-200">
                {{ $employeesList->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

    <!-- Mobile Cards View -->
    <div class="md:hidden space-y-3">
        @forelse($employeesList as $emp)
            <div wire:key="emp-card-{{ $emp->id }}" class="bg-white rounded-xl border border-gov-border p-4 shadow-2xs space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-gov-navy font-bold flex items-center justify-center text-xs shrink-0 border border-slate-200">
                            {{ strtoupper(substr($emp->name, 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-xs leading-snug">{{ $emp->name }}</h4>
                            <span class="text-[10px] text-slate-400 block">{{ $emp->jabatan ?: 'Pegawai KPP Madya' }}</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $emp->status === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-400' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                        {{ $emp->status }}
                    </span>
                </div>

                <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-xs">
                    <div>
                        @if($emp->user_id && $emp->user)
                            @php
                                $role = $emp->user->role;
                                $badgeClasses = match($role) {
                                    'Master', 'admin', 'Administrator' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'Ketua', 'Sekretaris', 'Bendahara' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-emerald-50 text-emerald-700 border-emerald-400',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeClasses }}">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ $role }}</span>
                            </span>
                        @else
                            <button type="button" 
                                @click="openConfirmModal({{ Js::from([
                                    'id' => $emp->id,
                                    'action' => 'createUserFromEmployee',
                                    'title' => 'Tautkan / Buat Akun Pengguna',
                                    'itemName' => $emp->name . ($emp->nip_pendek ? ' (NIP: ' . $emp->nip_pendek . ')' : ''),
                                    'message' => 'Tautkan ke akun pengguna yang cocok atau buatkan akun baru untuk ' . $emp->name . '?',
                                    'type' => 'primary',
                                    'confirmButtonText' => 'Ya, Proses Akun',
                                    'noticeText' => 'Sistem akan memeriksa apakah akun pengguna sudah ada untuk ditautkan, atau membuatkan akun baru dengan peran Jamaah.',
                                ]) }})"
                                class="text-[10px] font-bold text-gov-navy hover:underline">
                                Buat Akun
                            </button>
                        @endif
                    </div>
                    @if(Auth::user()->canManage())
                        <div class="relative inline-flex items-center" x-data="{ openMenu: false }">
                            <button type="button" 
                                @click="openMenu = !openMenu" 
                                class="p-1.5 rounded-lg border border-slate-200 hover:border-gov-navy/40 hover:bg-slate-100/80 text-slate-500 hover:text-gov-navy transition shadow-2xs cursor-pointer inline-flex items-center justify-center"
                                :class="openMenu ? 'bg-slate-100 border-gov-navy/50 text-gov-navy ring-1 ring-gov-navy/20' : ''"
                                title="Menu Aksi">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                </svg>
                            </button>
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
                                        @click="openMenu = false; showEmployeeModal = true"
                                        wire:click="openEditEmployeeModal({{ $emp->id }})"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition font-medium text-xs cursor-pointer">
                                        <svg class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Ubah Data Pegawai</div>
                                    </button>
                                    <button type="button" 
                                        @click="openMenu = false; openDeleteModal({{ Js::from([
                                            'id' => $emp->id,
                                            'action' => 'deleteEmployee',
                                            'title' => 'Hapus Data Pegawai',
                                            'itemName' => $emp->name . ($emp->nip_pendek ? ' (NIP: ' . $emp->nip_pendek . ')' : ''),
                                            'message' => 'Apakah Anda yakin ingin menghapus data pegawai ' . $emp->name . ' dari database?',
                                        ]) }})"
                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition font-medium text-xs cursor-pointer">
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        <div class="text-left flex-1 min-w-0 font-semibold leading-tight">Hapus Pegawai</div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <x-empty-state 
                icon="id-card" 
                title="Tidak ada data pegawai" 
                subtitle="Belum ada data pegawai yang sesuai dengan kriteria atau filter pencarian ini." 
            />
        @endforelse

        @if(method_exists($employeesList, 'hasPages') && $employeesList->hasPages())
            <div class="p-3 bg-white rounded-xl border border-gov-border">
                {{ $employeesList->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

</div>
