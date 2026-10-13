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
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-gov-border text-xs shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 w-full sm:w-auto">
            <!-- Filter Status Kepegawaian -->
            <div class="w-full sm:w-auto">
                <select wire:model.live="employeeFilterStatus"
                    class="w-full sm:w-auto px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium text-gov-textMain cursor-pointer focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy">
                    <option value="all">Semua Status Kantor</option>
                    <option value="AKTIF">Aktif di KPP Madya</option>
                    <option value="MUTASI">Mutasi / Pindah</option>
                    <option value="NONAKTIF">Nonaktif</option>
                </select>
            </div>

            <!-- Filter Status Akun Aplikasi -->
            <div class="w-full sm:w-auto">
                <select wire:model.live="employeeFilterLink"
                    class="w-full sm:w-auto px-2.5 py-1.5 rounded-lg border border-gov-border bg-white text-xs font-medium text-gov-textMain cursor-pointer focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy">
                    <option value="all">Semua Status Akun</option>
                    <option value="linked">Sudah Punya Akun</option>
                    <option value="unlinked">Belum Terdaftar Akun</option>
                </select>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-64" x-data="{
                clearEmpSearch() {
                    $wire.set('employeeSearch', '');
                    $wire.set('search', '');
                }
            }">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input wire:model.live.debounce.300ms="employeeSearch" type="text"
                    placeholder="Cari nama, NIP 9/18 digit, jabatan..."
                    class="h-8 w-full pl-8 pr-9 py-1 rounded-lg border border-gov-border bg-slate-50 text-xs font-medium focus:bg-white focus:outline-none focus:ring-1 focus:ring-gov-navy focus:border-gov-navy transition">
                <button type="button" @click="clearEmpSearch()"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-md text-slate-400 hover:text-slate-600 transition cursor-pointer flex items-center justify-center"
                    title="Reset pencarian">
                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke="currentColor">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Teks Indikator Total (Aturan GEMINI.md: Dilarang keras menggunakan badge counter angka) -->
        <div class="text-slate-500 font-medium text-xs">
            Total: <span class="font-bold text-gov-navy">{{ method_exists($employeesList, 'total') ? $employeesList->total() : $employeesList->count() }}</span> Pegawai
            @if($activeEmployeesCount > 0)
                <span class="text-slate-400 ml-1">({{ $activeEmployeesCount }} aktif)</span>
            @endif
        </div>
    </div>

    <!-- Desktop Table Card -->
    <div class="hidden md:block bg-white rounded-xl border border-gov-border shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
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
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="employeeFilterStatus, employeeFilterLink, employeeSearch, gotoPage, nextPage, previousPage" class="divide-y divide-slate-300 transition-opacity duration-150">
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
                                            default => 'bg-emerald-50 text-emerald-700 border-emerald-200',
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
                                                wire:click="createUserFromEmployee({{ $emp->id }})"
                                                wire:confirm="Buatkan akun pengguna (Role Jamaah) untuk {{ $emp->name }}?"
                                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-gov-navy/10 hover:bg-gov-navy hover:text-white text-gov-navy border border-gov-navy/20 transition cursor-pointer">
                                                + Buat Akun
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Status Kantor (Aktif / Mutasi) -->
                            <td class="py-3 px-4 text-center">
                                @if(Auth::user()->canManage())
                                    <button type="button" wire:click="toggleEmployeeStatus({{ $emp->id }})"
                                        class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none transition cursor-pointer {{ $emp->status === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}"
                                        title="Klik untuk mengubah status aktif/mutasi">
                                        {{ $emp->status === 'AKTIF' ? 'Aktif' : 'Mutasi' }}
                                    </button>
                                @else
                                    <span class="inline-flex items-center h-5 px-2.5 rounded-full text-[11px] font-semibold border leading-none {{ $emp->status === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                        {{ $emp->status === 'AKTIF' ? 'Aktif' : 'Mutasi' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center space-x-1">
                                    @if(Auth::user()->canManage())
                                        <button type="button" @click="showEmployeeModal = true" wire:click="openEditEmployeeModal({{ $emp->id }})"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-gov-border bg-white hover:bg-slate-50 text-slate-600 shadow-2xs transition cursor-pointer"
                                            title="Ubah Data Pegawai">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button type="button" wire:click="deleteEmployee({{ $emp->id }})"
                                            wire:confirm="Yakin ingin menghapus data pegawai {{ $emp->name }} dari database?"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg border border-gov-border bg-white hover:bg-rose-50 text-rose-600 shadow-2xs transition cursor-pointer"
                                            title="Hapus Pegawai">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Lihat saja</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-300 mb-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="4" width="18" height="16" rx="3"/>
                                        <circle cx="9" cy="10" r="2"/>
                                        <line x1="15" y1="8" x2="17" y2="8"/>
                                        <line x1="15" y1="12" x2="17" y2="12"/>
                                        <line x1="7" y1="16" x2="17" y2="16"/>
                                    </svg>
                                    <h4 class="font-bold text-slate-700 text-sm">Belum Ada Data Pegawai Muslim</h4>
                                    <p class="text-xs text-slate-400 mt-1 max-w-sm">
                                        Gunakan tombol "Impor Berkas Pegawai" untuk mengunggah spreadsheet rekap pegawai atau klik "Tambah Pegawai" untuk mencatat manual.
                                    </p>
                                    @if(Auth::user()->canManage())
                                        <div class="mt-4 flex items-center gap-2">
                                            <button type="button" @click="showImportEmployeeModal = true" wire:click="openImportEmployeeModal"
                                                class="px-3 py-1.5 rounded-lg bg-gov-navy text-white text-xs font-bold shadow-2xs hover:bg-gov-navyHover transition cursor-pointer">
                                                Impor Berkas Excel
                                            </button>
                                            <button type="button" @click="showEmployeeModal = true" wire:click="openCreateEmployeeModal"
                                                class="px-3 py-1.5 rounded-lg bg-white border border-gov-border text-slate-700 text-xs font-semibold hover:bg-slate-50 transition cursor-pointer">
                                                + Tambah Manual
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $emp->status === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
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
                                    default => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeClasses }}">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ $role }}</span>
                            </span>
                        @else
                            <button type="button" wire:click="createUserFromEmployee({{ $emp->id }})"
                                class="text-[10px] font-bold text-gov-navy hover:underline">
                                + Buatkan Akun
                            </button>
                        @endif
                    </div>
                    @if(Auth::user()->canManage())
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="showEmployeeModal = true" wire:click="openEditEmployeeModal({{ $emp->id }})"
                                class="p-1 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 cursor-pointer">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            </button>
                            <button type="button" wire:click="deleteEmployee({{ $emp->id }})"
                                wire:confirm="Hapus {{ $emp->name }}?"
                                class="p-1 rounded bg-rose-50 text-rose-600 hover:bg-rose-100">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gov-border p-6 text-center text-slate-400 text-xs">
                Belum ada data pegawai yang sesuai pencarian.
            </div>
        @endforelse

        @if(method_exists($employeesList, 'hasPages') && $employeesList->hasPages())
            <div class="p-3 bg-white rounded-xl border border-gov-border">
                {{ $employeesList->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>

</div>
