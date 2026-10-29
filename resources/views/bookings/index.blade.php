@extends('layouts.app')

@php
    $header = 'Daftar Peminjaman & Booking Zoom';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Filter & Action Header (Flat Minimalist) -->
    <div class="bg-white p-4 rounded border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <form method="GET" action="{{ route('bookings.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari topik atau agenda..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
            </div>

            <select name="status" onchange="this.form.submit()" class="py-1.5 px-3 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                <option value="">Semua Status</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Disetujui (ACC)</option>
                <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Link Aktif (Executed)</option>
                <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold rounded border border-gray-300 transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('bookings.index') }}" class="px-2 py-1.5 text-xs text-gray-500 hover:text-gray-800">Reset</a>
            @endif
        </form>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('bookings.print-all', request()->query()) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium px-3 py-1.5 rounded border border-gray-300 transition" title="Cetak Rekap PDF">
                <i class="fa-solid fa-print text-gray-500 text-[11px]"></i> Cetak Rekap PDF
            </a>
            <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-1.5 bg-gray-900 hover:bg-black text-white text-xs font-medium px-3.5 py-1.5 rounded transition">
                <i class="fa-solid fa-plus text-[10px]"></i> Ajukan Booking Baru
            </a>
        </div>
    </div>

    <!-- Booking Table Card (Clean & Bordered) -->
    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-3 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Kegiatan / Topik</th>
                        <th class="px-4 py-3">Pemohon</th>
                        <th class="px-4 py-3">Jadwal & Waktu</th>
                        <th class="px-4 py-3">Akun Zoom</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($bookings as $idx => $item)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-3 py-3 text-center font-mono text-gray-500 text-[11px]">
                                {{ $bookings->firstItem() + $idx }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <a href="{{ route('bookings.show', $item) }}" class="font-semibold text-gray-900 hover:text-blue-600 text-xs">
                                        {{ $item->topic }}
                                    </a>
                                    @if($item->project_name)
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 border border-slate-200 font-medium">
                                            {{ $item->project_name }}
                                        </span>
                                    @endif
                                </div>
                                @if($item->description)
                                    <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5">{{ $item->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-medium text-gray-900 block">{{ $item->user->name ?? '-' }}</span>
                                <span class="text-[10px] text-gray-500">{{ $item->user->departemen ?? 'Umum' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $item->date_range_formatted }}</div>
                                <div class="text-[11px] text-gray-500">{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($item->account)
                                    <span class="inline-flex items-center gap-1.5 font-medium text-gray-800">
                                        <i class="fa-solid fa-server text-[10px] text-gray-400"></i>
                                        {{ $item->account->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">Belum dialokasikan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded border
                                    @if($item->status === 0) bg-amber-50 text-amber-700 border-amber-200
                                    @elseif($item->status === 1) bg-blue-50 text-blue-700 border-blue-200
                                    @elseif($item->status === 3) bg-emerald-50 text-emerald-700 border-emerald-200
                                    @else bg-red-50 text-red-700 border-red-200 @endif">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="relative inline-block text-left">
                                    <button type="button" 
                                        onclick="toggleActionDropdown('dropdown-{{ $item->id }}', this, event)"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 rounded text-xs font-medium transition shadow-2xs">
                                        <span>Aksi</span>
                                        <i class="fa-solid fa-chevron-down text-[9px] text-gray-400"></i>
                                    </button>

                                    <!-- Dropdown Menu (Fixed Overlay on top of everything) -->
                                    <div id="dropdown-{{ $item->id }}" 
                                        class="action-dropdown hidden fixed bg-white border border-gray-200 rounded shadow-xl z-50 py-1 text-xs divide-y divide-gray-100 text-left w-44">
                                        <div class="py-1">
                                            <a href="{{ route('bookings.show', $item) }}" class="flex items-center gap-2 px-3 py-1.5 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition">
                                                <i class="fa-regular fa-eye w-4 text-center text-gray-400"></i>
                                                <span>Detail Booking</span>
                                            </a>
                                            <a href="{{ route('bookings.print', $item) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition">
                                                <i class="fa-solid fa-print w-4 text-center text-gray-400"></i>
                                                <span>Cetak Bukti (PDF)</span>
                                            </a>
                                        </div>

                                        @if(auth()->user()->isAdmin() || ($item->status === 0 && $item->user_id === auth()->id()))
                                            <div class="py-1">
                                                <a href="{{ route('bookings.edit', $item) }}" class="flex items-center gap-2 px-3 py-1.5 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition">
                                                    <i class="fa-regular fa-pen-to-square w-4 text-center text-gray-400"></i>
                                                    <span>Edit Data</span>
                                                </a>
                                            </div>
                                        @endif

                                        @if(auth()->user()->isAdmin() || $item->user_id === auth()->id())
                                            <div class="py-1">
                                                <form method="POST" action="{{ route('bookings.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan booking ini?')" class="block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-red-600 hover:bg-red-50 hover:text-red-700 transition text-left">
                                                        <i class="fa-regular fa-trash-can w-4 text-center text-red-500"></i>
                                                        <span>Hapus Booking</span>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-regular fa-calendar-xmark text-2xl mb-2 text-gray-300 block"></i>
                                Tidak ada data pengajuan booking ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleActionDropdown(id, button, event) {
        event.stopPropagation();
        const target = document.getElementById(id);
        const allDropdowns = document.querySelectorAll('.action-dropdown');

        const isCurrentlyHidden = target.classList.contains('hidden');

        // Sembunyikan semua dropdown lain
        allDropdowns.forEach(d => d.classList.add('hidden'));

        if (isCurrentlyHidden) {
            // Hitung posisi tombol di viewport agar overlay melayang bebas tanpa terpotong overflow tabel
            const rect = button.getBoundingClientRect();
            target.classList.remove('hidden');

            const dropdownHeight = target.offsetHeight || 150;
            const dropdownWidth = target.offsetWidth || 176;

            // Cek apakah ada ruang di bawah, jika tidak balik ke atas
            let top = rect.bottom + 4;
            if (top + dropdownHeight > window.innerHeight) {
                top = rect.top - dropdownHeight - 4;
            }

            let left = rect.right - dropdownWidth;
            if (left < 10) left = 10;

            target.style.top = `${top}px`;
            target.style.left = `${left}px`;
        }
    }

    // Tutup dropdown jika klik di luar atau saat scrolling halaman
    document.addEventListener('click', function() {
        document.querySelectorAll('.action-dropdown').forEach(d => d.classList.add('hidden'));
    });

    window.addEventListener('scroll', function() {
        document.querySelectorAll('.action-dropdown').forEach(d => d.classList.add('hidden'));
    }, true);
</script>
@endpush
@endsection
