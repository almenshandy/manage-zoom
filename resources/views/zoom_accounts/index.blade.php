@extends('layouts.app')

@php
    $header = 'Master Akun Zoom';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header & Action Buttons (Flat Minimalist) -->
    <div class="bg-white p-4 rounded border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Inventaris Akun Zoom</h3>
            <p class="text-xs text-gray-500 mt-0.5">Kelola akun Zoom, kredensial Server-to-Server OAuth, dan masa aktif lisensi.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openSyncModal()" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium px-3 py-1.5 rounded border border-gray-300 transition">
                <i class="fa-solid fa-arrows-rotate text-gray-500 text-[11px]"></i> Sinkron dari DB `zoom`
            </button>
            <a href="{{ route('zoom-accounts.create') }}" class="inline-flex items-center gap-1.5 bg-gray-900 hover:bg-black text-white text-xs font-medium px-3.5 py-1.5 rounded transition">
                <i class="fa-solid fa-plus text-[10px]"></i> Tambah Akun Baru
            </a>
        </div>
    </div>

    <!-- Table Container (Flat) -->
    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">Nama & Identitas Akun</th>
                        <th class="px-4 py-3">Kapasitas</th>
                        <th class="px-4 py-3">Status API S2S</th>
                        <th class="px-4 py-3">Masa Berlaku</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($accounts as $acc)
                        @php
                            $isValid = $acc->expired_date && !$acc->expired_date->isPast();
                        @endphp
                        <tr class="{{ $isValid ? 'bg-emerald-50/40 hover:bg-emerald-50/70 border-l-4 border-l-emerald-500' : 'bg-red-50/40 hover:bg-red-50/70 border-l-4 border-l-red-500' }} transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-900 block">{{ $acc->name }}</span>
                                <span class="text-gray-500 text-[11px] font-mono">{{ $acc->email }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-medium text-gray-900">{{ $acc->capacity }}</span>
                                <span class="text-gray-400 text-[11px]">Peserta</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($acc->account_id && $acc->client_id)
                                    <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-white px-2 py-0.5 rounded border border-emerald-300 text-[10px] font-medium">
                                        <i class="fa-solid fa-check text-[9px]"></i> S2S Siap
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-gray-500 bg-white px-2 py-0.5 rounded border border-gray-300 text-[10px]">
                                        Default Env
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($acc->expired_date)
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold {{ $isValid ? 'text-emerald-800' : 'text-red-700' }}">
                                            {{ $acc->expired_date->format('d M Y') }}
                                        </span>
                                        @if($isValid)
                                            <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-semibold bg-red-100 text-red-800 border border-red-300">
                                                Kedaluwarsa
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Tidak diset</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="testConnection({{ $acc->id }}, '{{ addslashes($acc->name) }}', this)"
                                        class="p-1.5 text-gray-500 hover:text-emerald-700 hover:bg-white rounded border border-transparent hover:border-gray-200 transition"
                                        title="Uji Koneksi API Zoom">
                                        <i class="fa-solid fa-plug text-[11px]"></i>
                                    </button>
                                    <a href="{{ route('zoom-accounts.edit', $acc->id) }}" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-white rounded border border-transparent hover:border-gray-200 transition" title="Edit">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('zoom-accounts.destroy', $acc->id) }}" class="inline" onsubmit="return confirm('Hapus akun Zoom ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-white rounded border border-transparent hover:border-red-200 transition" title="Hapus">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-server text-2xl mb-2 text-gray-300 block"></i>
                                Belum ada akun Zoom yang ditambahkan. Anda dapat sinkronkan dari database lama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($accounts->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Pop-Up Sinkronisasi (Flat Minimalist) -->
<div id="syncModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded border border-gray-300 max-w-4xl w-full shadow-lg overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-800">
                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 leading-none">Preview Sinkronisasi Akun Zoom</h4>
                    <span class="text-[11px] text-gray-500" id="syncSummaryText">Memuat data dari database lokal `zoom`...</span>
                </div>
            </div>
            <button type="button" onclick="closeSyncModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Form & Content -->
        <form method="POST" action="{{ route('zoom-accounts.sync-execute') }}" class="flex flex-col flex-1 overflow-hidden" id="modalSyncForm">
            @csrf

            <!-- Filter & Setting Options inside Modal -->
            <div class="p-3 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-1.5 font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" id="modalCheckAll" class="rounded border-gray-300 text-gray-900 focus:ring-0" checked>
                        <span>Pilih Semua</span>
                    </label>
                    <div class="h-3.5 w-px bg-gray-300"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Jika akun sudah ada:</span>
                        <label class="flex items-center gap-1 text-gray-700 cursor-pointer">
                            <input type="radio" name="mode" value="skip" checked class="text-gray-900 focus:ring-0">
                            <span>Skip</span>
                        </label>
                        <label class="flex items-center gap-1 text-gray-700 cursor-pointer ml-1.5">
                            <input type="radio" name="mode" value="update" class="text-gray-900 focus:ring-0">
                            <span>Update</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-[11px]" id="badgeSummary">
                    <!-- Badges populated by JS -->
                </div>
            </div>

            <!-- Scrollable Table View -->
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 sticky top-0 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-2.5 w-8 text-center bg-gray-50"></th>
                            <th class="px-3 py-2.5 bg-gray-50">ID</th>
                            <th class="px-3 py-2.5 bg-gray-50">Email Akun</th>
                            <th class="px-3 py-2.5 bg-gray-50">Kapasitas</th>
                            <th class="px-3 py-2.5 bg-gray-50">Masa Berlaku</th>
                            <th class="px-3 py-2.5 bg-gray-50">Kredensial S2S</th>
                            <th class="px-3 py-2.5 text-center bg-gray-50">Status Sistem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700" id="syncTableBody">
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-400">
                                <i class="fa-solid fa-spinner fa-spin text-xl text-gray-400 block mb-2"></i>
                                Mengambil data akun dari database zoom...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-500" id="selectedCountText">0 akun dipilih</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeSyncModal()" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium text-xs rounded hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="submitSyncBtn" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium text-xs rounded transition flex items-center gap-1.5">
                        <i class="fa-solid fa-download text-[10px]"></i> Impor Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const syncModal = document.getElementById('syncModal');
    const syncTableBody = document.getElementById('syncTableBody');
    const syncSummaryText = document.getElementById('syncSummaryText');
    const badgeSummary = document.getElementById('badgeSummary');
    const modalCheckAll = document.getElementById('modalCheckAll');
    const selectedCountText = document.getElementById('selectedCountText');
    const submitSyncBtn = document.getElementById('submitSyncBtn');

    let loadedAccounts = [];

    function openSyncModal() {
        syncModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        fetchPreviewData();
    }

    function closeSyncModal() {
        syncModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function fetchPreviewData() {
        syncTableBody.innerHTML = `
            <tr>
                <td colspan="8" class="p-8 text-center text-gray-400">
                    <i class="fa-solid fa-spinner fa-spin text-xl text-gray-400 block mb-2"></i>
                    Mengambil data akun dari database zoom...
                </td>
            </tr>
        `;

        fetch('{{ route("zoom-accounts.sync-preview") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                syncTableBody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-red-500 font-medium text-xs">${res.error || 'Gagal memuat data.'}</td></tr>`;
                return;
            }

            loadedAccounts = res.data;
            syncSummaryText.textContent = `Ditemukan ${res.summary.total} akun di database zoom`;

            badgeSummary.innerHTML = `
                <span class="px-2 py-0.5 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 font-medium">Baru: ${res.summary.new}</span>
                <span class="px-2 py-0.5 rounded border border-amber-200 bg-amber-50 text-amber-800 font-medium">Sudah Ada: ${res.summary.existing}</span>
            `;

            renderTableRows(loadedAccounts);
        })
        .catch(err => {
            syncTableBody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-red-500 font-medium text-xs">Gagal menghubungi server: ${err.message}</td></tr>`;
        });
    }

    function renderTableRows(data) {
        if (!data.length) {
            syncTableBody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-gray-400 text-xs">Tidak ada data ditemukan di tabel zoom.zoom_accounts.</td></tr>`;
            updateSelectedCount();
            return;
        }

        let html = '';
        data.forEach(item => {
            const isItemValid = item.expired_date && (new Date(item.expired_date) >= new Date().setHours(0,0,0,0));
            const rowClass = isItemValid 
                ? 'bg-emerald-50/40 hover:bg-emerald-50/70 border-l-4 border-l-emerald-500' 
                : 'bg-red-50/40 hover:bg-red-50/70 border-l-4 border-l-red-500';

            html += `
                <tr class="${rowClass} transition-colors">
                    <td class="p-2.5 text-center">
                        <input type="checkbox" name="selected_ids[]" value="${item.source_id}"
                            class="modal-item-checkbox rounded border-gray-300 text-gray-900 focus:ring-0" checked>
                    </td>
                    <td class="px-3 py-2 font-mono text-gray-400 text-[11px]">#${item.source_id}</td>
                    <td class="px-3 py-2 font-mono font-medium text-gray-900">${item.email}</td>
                    <td class="px-3 py-2 whitespace-nowrap">${item.capacity} Pax</td>
                    <td class="px-3 py-2 whitespace-nowrap font-mono font-semibold ${isItemValid ? 'text-emerald-800' : 'text-red-700'}">
                        ${item.expired_date || '-'}
                        ${isItemValid 
                            ? '<span class="ml-1.5 inline-block px-1 py-0.2 rounded text-[9px] bg-emerald-100 text-emerald-800 border border-emerald-300">Aktif</span>' 
                            : '<span class="ml-1.5 inline-block px-1 py-0.2 rounded text-[9px] bg-red-100 text-red-800 border border-red-300">Kedaluwarsa</span>'}
                    </td>
                    <td class="px-3 py-2">
                        ${item.has_credentials 
                            ? `<span class="inline-flex items-center gap-1 text-emerald-700 bg-white px-1.5 py-0.5 rounded border border-emerald-300 text-[10px] font-mono">Ada</span>`
                            : `<span class="text-gray-400 italic text-[10px]">Kosong</span>`}
                    </td>
                    <td class="px-3 py-2 text-center whitespace-nowrap">
                        ${item.is_existing 
                            ? `<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white text-amber-700 border border-amber-300">Sudah Ada</span>`
                            : `<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white text-emerald-700 border border-emerald-300">Baru</span>`}
                    </td>
                </tr>
            `;
        });

        syncTableBody.innerHTML = html;
        bindCheckboxEvents();
        updateSelectedCount();
    }

    function bindCheckboxEvents() {
        const checkboxes = document.querySelectorAll('.modal-item-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                modalCheckAll.checked = Array.from(checkboxes).every(c => c.checked);
                updateSelectedCount();
            });
        });
    }

    modalCheckAll.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.modal-item-checkbox');
        checkboxes.forEach(cb => cb.checked = modalCheckAll.checked);
        updateSelectedCount();
    });

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.modal-item-checkbox:checked').length;
        selectedCountText.textContent = `${checked} akun dipilih`;
        submitSyncBtn.disabled = (checked === 0);
        submitSyncBtn.classList.toggle('opacity-50', checked === 0);
        submitSyncBtn.classList.toggle('cursor-not-allowed', checked === 0);
    }

    // Fungsi Test Koneksi API
    function testConnection(accountId, accountName, buttonEl) {
        const originalContent = buttonEl.innerHTML;
        buttonEl.disabled = true;
        buttonEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-[11px] text-gray-700"></i>';

        fetch(`/zoom-accounts/${accountId}/test-connection`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(res => res.json())
        .then(res => {
            buttonEl.disabled = false;
            buttonEl.innerHTML = originalContent;

            if (res.success) {
                let detail = res.message;
                if (res.data && res.data.type) {
                    detail += ` (${res.data.type} - ${res.data.email})`;
                }
                showCustomToast('success', 'Koneksi Berhasil', `${accountName}: ${detail}`);
            } else {
                showCustomToast('error', 'Koneksi Gagal', `${accountName}: ${res.message}`);
            }
        })
        .catch(err => {
            buttonEl.disabled = false;
            buttonEl.innerHTML = originalContent;
            showCustomToast('error', 'Error', `Gagal menghubungkan: ${err.message}`);
        });
    }

    function showCustomToast(type, title, message) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const isSuccess = (type === 'success');
        const toast = document.createElement('div');
        toast.className = `toast-item pointer-events-auto flex items-start gap-3 p-3.5 bg-white border ${isSuccess ? 'border-emerald-300' : 'border-red-300'} rounded shadow-sm text-xs transition-all duration-200 transform translate-y-0 opacity-100`;

        toast.innerHTML = `
            <i class="fa-solid ${isSuccess ? 'fa-check text-emerald-600' : 'fa-exclamation text-red-600'} mt-0.5 text-sm"></i>
            <div class="flex-1">
                <p class="font-semibold ${isSuccess ? 'text-gray-900' : 'text-red-900'}">${title}</p>
                <p class="text-gray-600 mt-0.5">${message}</p>
            </div>
            <button onclick="dismissToast(this.closest('.toast-item'))" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        container.appendChild(toast);
        setTimeout(() => dismissToast(toast), 5000);
    }
</script>
@endpush
@endsection
