@extends('layouts.app')

@php
    $header = 'Log Aktivitas & Audit Trail';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header Halaman & Aksi Pembersihan -->
    <x-page-header title="Audit Trail & Riwayat Aktivitas Sistem" description="Catatan otomatis seluruh aktivitas pengajuan permohonan booking, perubahan status, dan penghapusan jadwal.">
        <x-slot:actions>
            <button type="button" onclick="openClearModal()" class="px-3 py-1.5 bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 rounded text-xs font-medium transition inline-flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-trash-can text-xs text-rose-600"></i>
                <span>Bersihkan Riwayat Log</span>
            </button>
        </x-slot:actions>
    </x-page-header>

    <!-- Bar Pencarian & Filter Cepat -->
    <x-card>
        <form method="GET" action="{{ route('activity-logs.index') }}" class="p-4 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search input -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">Pencarian Kata Kunci</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari topik, nama pemohon, atau ID..."
                            class="w-full pl-8 pr-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500">
                    </div>
                </div>

                <!-- Filter Aksi -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">Tipe Aksi / Event</label>
                    <select name="action" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500">
                        <option value="">-- Semua Jenis Aksi --</option>
                        @foreach($availableActions as $key => $label)
                            <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>
                                {{ $label }} ({{ $key }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tanggal Mulai -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500">
                </div>

                <!-- Filter Tanggal Selesai -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-700 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100 flex-wrap gap-2">
                <div class="text-[11px] text-gray-500">
                    Menampilkan <strong class="text-gray-800">{{ $logs->total() }}</strong> aktivitas 
                    &bull; Total Keseluruhan: <span class="font-mono font-semibold text-gray-700">{{ $totalLogsCount }}</span> rekaman
                    &bull; Hari ini: <span class="font-mono text-emerald-700 font-semibold">{{ $todayLogsCount }}</span>
                </div>
                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['search', 'action', 'start_date', 'end_date']))
                        <a href="{{ route('activity-logs.index') }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-medium transition inline-flex items-center gap-1">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white rounded text-xs font-medium transition inline-flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-filter text-[10px]"></i>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Tabel Data Log Aktivitas -->
    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3" style="width: 140px;">Waktu Kejadian</th>
                        <th class="px-4 py-3" style="width: 170px;">Eksekutor / User</th>
                        <th class="px-4 py-3" style="width: 150px;">Aksi</th>
                        <th class="px-4 py-3">Keterangan Aktivitas</th>
                        <th class="px-4 py-3 text-right" style="width: 120px;">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-2.5 font-mono text-gray-500 text-[11px] whitespace-nowrap">
                                <span class="text-gray-900 font-medium block">{{ $log->created_at->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-gray-400">{{ $log->created_at->format('H:i:s') }} WIB ({{ $log->created_at->diffForHumans() }})</span>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="font-semibold text-gray-900 block">{{ $log->user->name ?? 'Sistem / Otomatis' }}</span>
                                <span class="text-[10px] text-gray-400 font-mono">{{ $log->user->email ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @if($log->action === 'create_booking')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-plus-circle text-[9px]"></i> Pengajuan Baru
                                    </span>
                                @elseif($log->action === 'update_status')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-pen-to-square text-[9px]"></i> Update Status
                                    </span>
                                @elseif($log->action === 'delete_booking')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-trash text-[9px]"></i> Hapus Booking
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono border bg-gray-50 text-gray-700 border-gray-200">
                                        {{ $log->action }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-gray-800 leading-relaxed">
                                {{ $log->description }}
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono text-gray-400 text-[11px] whitespace-nowrap">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-clock-rotate-left text-3xl mb-2 text-gray-300 block"></i>
                                <span class="font-medium text-gray-600 block">Tidak ada catatan aktivitas ditemukan.</span>
                                <span class="text-[11px] text-gray-400">Coba ubah kata kunci pencarian atau bersihkan filter tanggal.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <x-slot:footer>
                {{ $logs->links() }}
            </x-slot:footer>
        @endif
    </x-card>
</div>

<!-- Modal Bersihkan Riwayat Log -->
<div id="clearLogsModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded border border-gray-300 max-w-md w-full shadow-lg overflow-hidden animate-fadeIn">
        <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded bg-rose-100 text-rose-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Bersihkan Log Aktivitas</h4>
                    <p class="text-[11px] text-gray-500">Pangkas rekaman riwayat audit trail lama</p>
                </div>
            </div>
            <button type="button" onclick="closeClearModal()" class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('activity-logs.clear') }}" class="p-5 space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-medium text-gray-700 mb-1.5">Pilih Batas Pembersihan Log:</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 p-2.5 rounded border border-gray-200 hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="retention" value="30_days" checked class="text-gray-900 focus:ring-0">
                        <div>
                            <span class="font-semibold text-gray-900 block">Hapus log lebih dari 30 hari yang lalu</span>
                            <span class="text-[10px] text-gray-500">Mempertahankan riwayat 1 bulan terakhir.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2 p-2.5 rounded border border-gray-200 hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="retention" value="60_days" class="text-gray-900 focus:ring-0">
                        <div>
                            <span class="font-semibold text-gray-900 block">Hapus log lebih dari 60 hari yang lalu</span>
                            <span class="text-[10px] text-gray-500">Mempertahankan riwayat 2 bulan terakhir.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2 p-2.5 rounded border border-gray-200 hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="retention" value="90_days" class="text-gray-900 focus:ring-0">
                        <div>
                            <span class="font-semibold text-gray-900 block">Hapus log lebih dari 90 hari yang lalu</span>
                            <span class="text-[10px] text-gray-500">Mempertahankan riwayat 3 bulan terakhir.</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2 p-2.5 rounded border border-rose-200 bg-rose-50/40 hover:bg-rose-50 cursor-pointer">
                        <input type="radio" name="retention" value="all" class="text-rose-600 focus:ring-0">
                        <div>
                            <span class="font-semibold text-rose-900 block">Hapus SEMUA riwayat log tanpa kecuali</span>
                            <span class="text-[10px] text-rose-600">Mengosongkan seluruh tabel riwayat audit trail.</span>
                        </div>
                    </label>
                </div>
            </div>

            <p class="text-[11px] text-gray-500 leading-relaxed bg-amber-50 p-2.5 rounded border border-amber-200 text-amber-900">
                <i class="fa-solid fa-circle-info mr-1"></i> Tindakan pembersihan bersifat permanen dan data log yang terhapus tidak dapat dikembalikan.
            </p>

            <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeClearModal()" class="px-3.5 py-1.5 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50 font-medium transition">
                    Batal
                </button>
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menjalankan pembersihan riwayat log ini?')"
                    class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded font-medium transition inline-flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Eksekusi Pembersihan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const clearLogsModal = document.getElementById('clearLogsModal');

    function openClearModal() {
        if (!clearLogsModal) return;
        clearLogsModal.classList.remove('hidden');
    }

    function closeClearModal() {
        if (!clearLogsModal) return;
        clearLogsModal.classList.add('hidden');
    }

    if (clearLogsModal) {
        clearLogsModal.addEventListener('click', function(e) {
            if (e.target === clearLogsModal) {
                closeClearModal();
            }
        });
    }
</script>
@endpush
@endsection
