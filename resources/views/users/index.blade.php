@extends('layouts.app')

@php
    $header = 'Manajemen Pengguna';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <x-page-header title="Daftar Pengguna Sistem" description="Kelola akun pengguna, peran hak akses (Admin / Pegawai), dan unit departemen.">
        <x-slot:actions>
            <button type="button" onclick="openUserSyncModal()" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium px-3 py-1.5 rounded border border-gray-300 transition">
                <i class="fa-solid fa-arrows-rotate text-gray-500 text-[11px]"></i> Tarik Karyawan (LSP)
            </button>
            <x-btn variant="primary" :href="route('users.create')">
                <i class="fa-solid fa-user-plus text-[10px]"></i> Tambah Pengguna
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    <!-- Filter & Search (Flat) -->
    <div class="bg-white p-3 rounded border border-gray-200">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau departemen..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
            </div>

            <select name="role" onchange="this.form.submit()" class="py-1.5 px-3 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                <option value="">Semua Peran</option>
                <option value="1" {{ request('role') === '1' ? 'selected' : '' }}>Administrator</option>
                <option value="2" {{ request('role') === '2' ? 'selected' : '' }}>Pegawai / Staf</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold rounded border border-gray-300 transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('users.index') }}" class="px-2 py-1.5 text-xs text-gray-500 hover:text-gray-800">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table Users (Flat) -->
    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">Nama & Username</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Jabatan & Departemen</th>
                        <th class="px-4 py-3">No. HP</th>
                        <th class="px-4 py-3 text-center">Peran (Role)</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-900 block">{{ $user->name }}</span>
                                <span class="text-gray-400 text-[11px] font-mono">@<!-- -->{{ $user->username }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-mono text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-800 font-medium block">{{ $user->jabatan ?: '-' }}</span>
                                <span class="text-gray-500 text-[11px]">{{ $user->departemen ?: 'Umum' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600 font-mono">
                                {{ $user->no_hp ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <form method="POST" action="{{ route('users.update-role', $user->id) }}" class="inline">
                                    @csrf
                                    <select name="role" onchange="this.form.submit()" class="text-[10px] font-semibold px-2 py-0.5 rounded border focus:ring-0 cursor-pointer
                                        {{ $user->role == 1 ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-gray-50 text-gray-700 border-gray-300' }}">
                                        <option value="1" {{ $user->role == 1 ? 'selected' : '' }}>Administrator</option>
                                        <option value="2" {{ $user->role == 2 ? 'selected' : '' }}>Pegawai / Staf</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded border border-transparent hover:border-gray-200 transition" title="Edit Pengguna">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="inline" onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded border border-transparent hover:border-red-200 transition" title="Hapus">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-users text-2xl mb-2 text-gray-300 block"></i>
                                Tidak ada data pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <x-slot:footer>
                {{ $users->links() }}
            </x-slot:footer>
        @endif
    </x-card>
</div>

<!-- Modal Pop-Up Sinkronisasi Karyawan LSP (Flat Minimalist) -->
<div id="userSyncModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded border border-gray-300 max-w-4xl w-full shadow-lg overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-800">
                    <i class="fa-solid fa-users-gear text-xs"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 leading-none">Tarik Data Karyawan dari Server LSP</h4>
                    <span class="text-[11px] text-gray-500" id="userSyncSummaryText">Menghubungkan ke database LSP (145.14.154.207)...</span>
                </div>
            </div>
            <button type="button" onclick="closeUserSyncModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Form & Content -->
        <form method="POST" action="{{ route('users.sync-execute') }}" class="flex flex-col flex-1 overflow-hidden" id="modalUserSyncForm">
            @csrf

            <!-- Filter & Setting Options inside Modal -->
            <div class="p-3 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-1.5 font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" id="userModalCheckAll" class="rounded border-gray-300 text-gray-900 focus:ring-0" checked>
                        <span>Pilih Semua Karyawan</span>
                    </label>
                    <div class="h-3.5 w-px bg-gray-300"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Jika user sudah ada:</span>
                        <label class="flex items-center gap-1 text-gray-700 cursor-pointer">
                            <input type="radio" name="mode" value="skip" checked class="text-gray-900 focus:ring-0">
                            <span>Skip</span>
                        </label>
                        <label class="flex items-center gap-1 text-gray-700 cursor-pointer ml-1.5">
                            <input type="radio" name="mode" value="update" class="text-gray-900 focus:ring-0">
                            <span>Update Profil</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-[11px]" id="userBadgeSummary">
                    <!-- Badges populated by JS -->
                </div>
            </div>

            <!-- Scrollable Table View -->
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 sticky top-0 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-2.5 w-8 text-center bg-gray-50"></th>
                            <th class="px-3 py-2.5 bg-gray-50">Nama Lengkap</th>
                            <th class="px-3 py-2.5 bg-gray-50">Username</th>
                            <th class="px-3 py-2.5 bg-gray-50">Email</th>
                            <th class="px-3 py-2.5 bg-gray-50">Jabatan & Unit</th>
                            <th class="px-3 py-2.5 text-center bg-gray-50">Status Karyawan</th>
                            <th class="px-3 py-2.5 text-center bg-gray-50">Status Portal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700" id="userSyncTableBody">
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">
                                <i class="fa-solid fa-spinner fa-spin text-xl text-gray-400 block mb-2"></i>
                                Mengambil 319 data karyawan dari server LSP...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-500" id="userSelectedCountText">0 karyawan dipilih</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeUserSyncModal()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium text-xs rounded hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="userSubmitSyncBtn" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium text-xs rounded transition flex items-center gap-1.5">
                        <i class="fa-solid fa-download text-[10px]"></i> Tarik Data Terpilih
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const userSyncModal = document.getElementById('userSyncModal');
    const userSyncTableBody = document.getElementById('userSyncTableBody');
    const userSyncSummaryText = document.getElementById('userSyncSummaryText');
    const userBadgeSummary = document.getElementById('userBadgeSummary');
    const userModalCheckAll = document.getElementById('userModalCheckAll');
    const userSelectedCountText = document.getElementById('userSelectedCountText');
    const userSubmitSyncBtn = document.getElementById('userSubmitSyncBtn');

    let loadedKaryawan = [];

    function openUserSyncModal() {
        userSyncModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        fetchLspUsers();
    }

    function closeUserSyncModal() {
        userSyncModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function fetchLspUsers() {
        userSyncTableBody.innerHTML = `
            <tr>
                <td colspan="7" class="p-8 text-center text-gray-400">
                    <i class="fa-solid fa-spinner fa-spin text-xl text-gray-400 block mb-2"></i>
                    Mengambil data karyawan dari server LSP...
                </td>
            </tr>
        `;

        fetch('{{ route("users.sync-preview") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                userSyncTableBody.innerHTML = `<tr><td colspan="7" class="p-6 text-center text-red-500 font-medium text-xs">${res.error || 'Gagal terhubung ke database LSP.'}</td></tr>`;
                return;
            }

            loadedKaryawan = res.data;
            userSyncSummaryText.textContent = `Ditemukan ${res.summary.total} karyawan di server LSP`;

            userBadgeSummary.innerHTML = `
                <span class="px-2 py-0.5 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 font-medium">Baru: ${res.summary.new}</span>
                <span class="px-2 py-0.5 rounded border border-amber-200 bg-amber-50 text-amber-800 font-medium">Sudah Ada: ${res.summary.existing}</span>
            `;

            renderUserTableRows(loadedKaryawan);
        })
        .catch(err => {
            userSyncTableBody.innerHTML = `<tr><td colspan="7" class="p-6 text-center text-red-500 font-medium text-xs">Error: ${err.message}</td></tr>`;
        });
    }

    function renderUserTableRows(data) {
        if (!data.length) {
            userSyncTableBody.innerHTML = `<tr><td colspan="7" class="p-6 text-center text-gray-400 text-xs">Tidak ada data karyawan ditemukan.</td></tr>`;
            updateUserSelectedCount();
            return;
        }

        let html = '';
        data.forEach(item => {
            html += `
                <tr class="hover:bg-gray-50/75 transition-colors ${item.is_existing ? 'bg-amber-50/20' : ''}">
                    <td class="p-2.5 text-center">
                        <input type="checkbox" name="selected_ids[]" value="${item.id}"
                            class="user-item-checkbox rounded border-gray-300 text-gray-900 focus:ring-0" checked>
                    </td>
                    <td class="px-3 py-2 font-medium text-gray-900">${item.name}</td>
                    <td class="px-3 py-2 font-mono text-gray-600 text-[11px]">@${item.username}</td>
                    <td class="px-3 py-2 font-mono text-gray-600 text-[11px]">${item.email}</td>
                    <td class="px-3 py-2">
                        <span class="text-gray-800 block">${item.jabatan}</span>
                        <span class="text-gray-400 text-[10px]">${item.departemen}</span>
                    </td>
                    <td class="px-3 py-2 text-center whitespace-nowrap">
                        ${item.active 
                            ? '<span class="px-1.5 py-0.2 rounded text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium">Aktif</span>' 
                            : '<span class="px-1.5 py-0.2 rounded text-[10px] bg-gray-100 text-gray-500 border border-gray-200">Non-Aktif</span>'}
                    </td>
                    <td class="px-3 py-2 text-center whitespace-nowrap">
                        ${item.is_existing 
                            ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white text-amber-700 border border-amber-300">Sudah Ada</span>`
                            : `<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white text-emerald-700 border border-emerald-300">Baru</span>`}
                    </td>
                </tr>
            `;
        });

        userSyncTableBody.innerHTML = html;
        bindUserCheckboxEvents();
        updateUserSelectedCount();
    }

    function bindUserCheckboxEvents() {
        const checkboxes = document.querySelectorAll('.user-item-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                userModalCheckAll.checked = Array.from(checkboxes).every(c => c.checked);
                updateUserSelectedCount();
            });
        });
    }

    userModalCheckAll.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.user-item-checkbox');
        checkboxes.forEach(cb => cb.checked = userModalCheckAll.checked);
        updateUserSelectedCount();
    });

    function updateUserSelectedCount() {
        const checked = document.querySelectorAll('.user-item-checkbox:checked').length;
        userSelectedCountText.textContent = `${checked} karyawan dipilih`;
        userSubmitSyncBtn.disabled = (checked === 0);
        userSubmitSyncBtn.classList.toggle('opacity-50', checked === 0);
        userSubmitSyncBtn.classList.toggle('cursor-not-allowed', checked === 0);
    }
</script>
@endpush
@endsection
