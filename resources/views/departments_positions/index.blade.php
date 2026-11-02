@extends('layouts.app')

@php
    $header = 'Master Departemen & Jabatan LSP';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header Indicator & Actions -->
    <div class="bg-white p-4 rounded border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Master Data Departemen & Jabatan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Tarik nama resmi Departemen dan Jabatan dari server LSP agar kode (DP-xxx & JBT-xx) tampil jelas.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" onclick="openLspSyncModal()" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3.5 py-1.5 rounded transition shadow-2xs">
                <i class="fa-solid fa-arrows-rotate text-[10px]"></i> Tarik dari Server LSP
            </button>

            @if($tab === 'departments')
                <button type="button" onclick="openCreateDeptModal()" class="inline-flex items-center gap-1.5 bg-gray-900 hover:bg-black text-white text-xs font-medium px-3.5 py-1.5 rounded transition">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah Departemen
                </button>
            @else
                <button type="button" onclick="openCreatePosModal()" class="inline-flex items-center gap-1.5 bg-gray-900 hover:bg-black text-white text-xs font-medium px-3.5 py-1.5 rounded transition">
                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah Jabatan
                </button>
            @endif
        </div>
    </div>

    <!-- Tab Navigation (Flat Minimalist) -->
    <div class="flex items-center gap-2 border-b border-gray-200">
        <a href="{{ route('departments-positions.index', ['tab' => 'departments']) }}" 
            class="px-4 py-2 text-xs font-medium border-b-2 transition -mb-px flex items-center gap-2
            {{ $tab === 'departments' ? 'border-gray-900 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fa-solid fa-building text-xs"></i>
            <span>Departemen</span>
            <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $tab === 'departments' ? 'bg-gray-100 text-gray-900 font-bold' : 'bg-gray-100 text-gray-600' }}">
                {{ $departments->count() }}
            </span>
        </a>

        <a href="{{ route('departments-positions.index', ['tab' => 'positions']) }}" 
            class="px-4 py-2 text-xs font-medium border-b-2 transition -mb-px flex items-center gap-2
            {{ $tab === 'positions' ? 'border-gray-900 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fa-solid fa-id-badge text-xs"></i>
            <span>Jabatan</span>
            <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $tab === 'positions' ? 'bg-gray-100 text-gray-900 font-bold' : 'bg-gray-100 text-gray-600' }}">
                {{ $positions->count() }}
            </span>
        </a>
    </div>

    <!-- Tab Content -->
    @if($tab === 'departments')
        <!-- Tabel Departemen -->
        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="px-3 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3 w-36">Kode Departemen</th>
                            <th class="px-4 py-3">Nama Departemen</th>
                            <th class="px-4 py-3">Rumpun Direksi</th>
                            <th class="px-4 py-3">Pejabat</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700">
                        @forelse($departments as $idx => $d)
                            <tr class="hover:bg-gray-50/75 transition-colors">
                                <td class="px-3 py-3 text-center font-mono text-gray-500 text-[11px]">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-4 py-3 font-mono font-semibold text-gray-900">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200 text-[11px]">
                                        {{ $d->kode_departemen }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900 text-xs">
                                    {{ $d->nama_departemen }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $d->rumpun_direksi ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 font-mono text-[11px]">
                                    {{ $d->pejabat ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" 
                                            onclick="openEditDeptModal({{ json_encode($d) }})"
                                            class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded border border-transparent hover:border-gray-200" title="Edit Departemen">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        <form method="POST" action="{{ route('departments.destroy', $d) }}" onsubmit="return confirm('Hapus departemen {{ $d->nama_departemen }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded border border-transparent hover:border-red-200" title="Hapus Departemen">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-building text-2xl mb-2 text-gray-300 block"></i>
                                    Belum ada data Departemen. Klik tombol <strong>"Tarik dari Server LSP"</strong> di atas untuk mengambil data resmi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Tabel Jabatan -->
        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="px-3 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3 w-36">Kode Jabatan</th>
                            <th class="px-4 py-3">Nama Jabatan</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700">
                        @forelse($positions as $idx => $p)
                            <tr class="hover:bg-gray-50/75 transition-colors">
                                <td class="px-3 py-3 text-center font-mono text-gray-500 text-[11px]">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="px-4 py-3 font-mono font-semibold text-gray-900">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200 text-[11px]">
                                        {{ $p->kode_jabatan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900 text-xs">
                                    {{ $p->nama_jabatan }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" 
                                            onclick="openEditPosModal({{ json_encode($p) }})"
                                            class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded border border-transparent hover:border-gray-200" title="Edit Jabatan">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        <form method="POST" action="{{ route('positions.destroy', $p) }}" onsubmit="return confirm('Hapus jabatan {{ $p->nama_jabatan }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded border border-transparent hover:border-red-200" title="Hapus Jabatan">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-id-badge text-2xl mb-2 text-gray-300 block"></i>
                                    Belum ada data Jabatan. Klik tombol <strong>"Tarik dari Server LSP"</strong> di atas untuk mengambil data resmi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Modal Pop-Up Sinkronisasi dari LSP (Flat Minimalist) -->
<div id="lspSyncModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded border border-gray-300 max-w-3xl w-full shadow-lg overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 leading-none">Sinkronisasi Departemen & Jabatan dari Server LSP</h4>
                    <span class="text-[11px] text-gray-500" id="syncStatusSubtitle">Menghubungkan ke server LSP (145.14.154.207)...</span>
                </div>
            </div>
            <button type="button" onclick="closeLspSyncModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('departments-positions.sync-execute') }}" class="flex flex-col flex-1 overflow-hidden">
            @csrf

            <!-- Filter & Setting Options -->
            <div class="p-3 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="font-medium text-gray-700">Target Sinkronisasi:</span>
                    <label class="flex items-center gap-1.5 text-gray-700 cursor-pointer">
                        <input type="radio" name="target" value="all" checked class="text-gray-900 focus:ring-0">
                        <span>Semua (Departemen & Jabatan)</span>
                    </label>
                    <label class="flex items-center gap-1.5 text-gray-700 cursor-pointer">
                        <input type="radio" name="target" value="departments" class="text-gray-900 focus:ring-0">
                        <span>Hanya Departemen</span>
                    </label>
                    <label class="flex items-center gap-1.5 text-gray-700 cursor-pointer">
                        <input type="radio" name="target" value="positions" class="text-gray-900 focus:ring-0">
                        <span>Hanya Jabatan</span>
                    </label>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-gray-500">Jika kode sudah ada:</span>
                    <label class="flex items-center gap-1 text-gray-700 cursor-pointer">
                        <input type="radio" name="mode" value="update" checked class="text-gray-900 focus:ring-0">
                        <span>Perbarui Nama</span>
                    </label>
                    <label class="flex items-center gap-1 text-gray-700 cursor-pointer">
                        <input type="radio" name="mode" value="skip" class="text-gray-900 focus:ring-0">
                        <span>Lewati</span>
                    </label>
                </div>
            </div>

            <!-- Preview Data Container -->
            <div class="p-4 flex-1 overflow-y-auto space-y-4 custom-scrollbar" id="syncPreviewContainer">
                <div class="p-8 text-center text-gray-400">
                    <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-600 block mb-2"></i>
                    Sedang memuat data dari database remote LSP...
                </div>
            </div>

            <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-500" id="syncSummaryCount">-</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeLspSyncModal()" class="px-3.5 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium text-xs rounded hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium text-xs rounded transition flex items-center gap-1.5">
                        <i class="fa-solid fa-cloud-arrow-down text-[10px]"></i> Eksekusi Tarik Data Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah/Edit Departemen -->
<div id="deptModal" class="hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center p-4 backdrop-blur-xs">
    <div class="bg-white rounded-lg border border-gray-200 w-full max-w-md overflow-hidden shadow-xl">
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between bg-white">
            <h4 id="deptModalTitle" class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Tambah Departemen</h4>
            <button type="button" onclick="closeDeptModal()" class="w-7 h-7 rounded flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="deptForm" method="POST" action="{{ route('departments.store') }}" class="p-5 space-y-3 text-xs">
            @csrf
            <div id="deptMethodSpoof"></div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Kode Departemen <span class="text-red-500">*</span></label>
                <input type="text" name="kode_departemen" id="input_kode_dept" required placeholder="Contoh: DP-001"
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded font-mono text-xs focus:outline-none focus:border-gray-500">
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Nama Departemen <span class="text-red-500">*</span></label>
                <input type="text" name="nama_departemen" id="input_nama_dept" required placeholder="Contoh: DIREKTUR UTAMA"
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Rumpun Direksi</label>
                    <input type="text" name="rumpun_direksi" id="input_rumpun_direksi" placeholder="Contoh: Direksi"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Pejabat (NIP/ID)</label>
                    <input type="text" name="pejabat" id="input_pejabat" placeholder="Misal: 12067122"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded font-mono text-xs focus:outline-none focus:border-gray-500">
                </div>
            </div>

            <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeDeptModal()" class="px-4 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium rounded hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium rounded transition">
                    Simpan Departemen
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah/Edit Jabatan -->
<div id="posModal" class="hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center p-4 backdrop-blur-xs">
    <div class="bg-white rounded-lg border border-gray-200 w-full max-w-md overflow-hidden shadow-xl">
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between bg-white">
            <h4 id="posModalTitle" class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Tambah Jabatan</h4>
            <button type="button" onclick="closePosModal()" class="w-7 h-7 rounded flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="posForm" method="POST" action="{{ route('positions.store') }}" class="p-5 space-y-3 text-xs">
            @csrf
            <div id="posMethodSpoof"></div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Kode Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="kode_jabatan" id="input_kode_pos" required placeholder="Contoh: JBT-01"
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded font-mono text-xs focus:outline-none focus:border-gray-500">
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Nama Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_jabatan" id="input_nama_pos" required placeholder="Contoh: DIREKTUR UTAMA"
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500">
            </div>

            <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closePosModal()" class="px-4 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium rounded hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium rounded transition">
                    Simpan Jabatan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const lspSyncModal = document.getElementById('lspSyncModal');
    const syncStatusSubtitle = document.getElementById('syncStatusSubtitle');
    const syncPreviewContainer = document.getElementById('syncPreviewContainer');
    const syncSummaryCount = document.getElementById('syncSummaryCount');

    function openLspSyncModal() {
        lspSyncModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        fetchLspData();
    }

    function closeLspSyncModal() {
        lspSyncModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function fetchLspData() {
        fetch("{{ route('departments-positions.sync-preview') }}")
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    syncPreviewContainer.innerHTML = `<div class="p-6 text-center text-red-600 bg-red-50 border border-red-200 rounded">${res.error || 'Gagal memuat data'}</div>`;
                    return;
                }

                syncStatusSubtitle.textContent = `Terhubung! Ditemukan ${res.summary.dept_total} Departemen & ${res.summary.pos_total} Jabatan.`;
                syncSummaryCount.textContent = `Total: ${res.summary.dept_total} Departemen (${res.summary.dept_new} baru) | ${res.summary.pos_total} Jabatan (${res.summary.pos_new} baru)`;

                let html = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Preview Departemen -->
                        <div class="border border-gray-200 rounded bg-white overflow-hidden">
                            <div class="bg-gray-50 px-3 py-2 border-b border-gray-200 font-semibold text-xs text-gray-800 flex items-center justify-between">
                                <span>Departemen LSP (${res.departments.length})</span>
                                <span class="text-[10px] text-gray-500">${res.summary.dept_new} baru</span>
                            </div>
                            <div class="max-h-60 overflow-y-auto divide-y divide-gray-100 text-[11px] custom-scrollbar">
                `;

                res.departments.forEach(d => {
                    html += `
                        <div class="p-2 hover:bg-gray-50 flex items-center justify-between gap-2">
                            <div>
                                <span class="font-mono font-semibold text-gray-800">${d.kode_departemen}</span> - <span class="font-medium text-gray-900">${d.nama_departemen}</span>
                                <div class="text-[10px] text-gray-400">${d.rumpun_direksi || '-'}</div>
                            </div>
                            ${d.is_existing ? '<span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">Ada</span>' : '<span class="text-[9px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-bold">Baru</span>'}
                        </div>
                    `;
                });

                html += `
                            </div>
                        </div>

                        <!-- Preview Jabatan -->
                        <div class="border border-gray-200 rounded bg-white overflow-hidden">
                            <div class="bg-gray-50 px-3 py-2 border-b border-gray-200 font-semibold text-xs text-gray-800 flex items-center justify-between">
                                <span>Jabatan LSP (${res.positions.length})</span>
                                <span class="text-[10px] text-gray-500">${res.summary.pos_new} baru</span>
                            </div>
                            <div class="max-h-60 overflow-y-auto divide-y divide-gray-100 text-[11px] custom-scrollbar">
                `;

                res.positions.forEach(j => {
                    html += `
                        <div class="p-2 hover:bg-gray-50 flex items-center justify-between gap-2">
                            <div>
                                <span class="font-mono font-semibold text-gray-800">${j.kode_jabatan}</span> - <span class="font-medium text-gray-900">${j.nama_jabatan}</span>
                            </div>
                            ${j.is_existing ? '<span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">Ada</span>' : '<span class="text-[9px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-bold">Baru</span>'}
                        </div>
                    `;
                });

                html += `
                            </div>
                        </div>
                    </div>
                `;

                syncPreviewContainer.innerHTML = html;
            })
            .catch(err => {
                syncPreviewContainer.innerHTML = `<div class="p-6 text-center text-red-600 bg-red-50 border border-red-200 rounded">Koneksi Error: ${err.message}</div>`;
            });
    }

    // Modal CRUD Departemen
    const deptModal = document.getElementById('deptModal');
    const deptForm = document.getElementById('deptForm');
    const deptModalTitle = document.getElementById('deptModalTitle');
    const deptMethodSpoof = document.getElementById('deptMethodSpoof');
    const inputKodeDept = document.getElementById('input_kode_dept');
    const inputNamaDept = document.getElementById('input_nama_dept');
    const inputRumpun = document.getElementById('input_rumpun_direksi');
    const inputPejabat = document.getElementById('input_pejabat');

    function openCreateDeptModal() {
        deptModalTitle.textContent = 'Tambah Departemen Baru';
        deptForm.action = "{{ route('departments.store') }}";
        deptMethodSpoof.innerHTML = '';
        inputKodeDept.value = '';
        inputNamaDept.value = '';
        inputRumpun.value = '';
        inputPejabat.value = '';
        deptModal.classList.remove('hidden');
    }

    function openEditDeptModal(d) {
        deptModalTitle.textContent = 'Edit Data Departemen';
        deptForm.action = `/departments/${d.id}`;
        deptMethodSpoof.innerHTML = '@method("PUT")';
        inputKodeDept.value = d.kode_departemen;
        inputNamaDept.value = d.nama_departemen;
        inputRumpun.value = d.rumpun_direksi || '';
        inputPejabat.value = d.pejabat || '';
        deptModal.classList.remove('hidden');
    }

    function closeDeptModal() {
        deptModal.classList.add('hidden');
    }

    // Modal CRUD Jabatan
    const posModal = document.getElementById('posModal');
    const posForm = document.getElementById('posForm');
    const posModalTitle = document.getElementById('posModalTitle');
    const posMethodSpoof = document.getElementById('posMethodSpoof');
    const inputKodePos = document.getElementById('input_kode_pos');
    const inputNamaPos = document.getElementById('input_nama_pos');

    function openCreatePosModal() {
        posModalTitle.textContent = 'Tambah Jabatan Baru';
        posForm.action = "{{ route('positions.store') }}";
        posMethodSpoof.innerHTML = '';
        inputKodePos.value = '';
        inputNamaPos.value = '';
        posModal.classList.remove('hidden');
    }

    function openEditPosModal(p) {
        posModalTitle.textContent = 'Edit Data Jabatan';
        posForm.action = `/positions/${p.id}`;
        posMethodSpoof.innerHTML = '@method("PUT")';
        inputKodePos.value = p.kode_jabatan;
        inputNamaPos.value = p.nama_jabatan;
        posModal.classList.remove('hidden');
    }

    function closePosModal() {
        posModal.classList.add('hidden');
    }
</script>
@endpush
@endsection
