@extends('layouts.app')

@php
    $header = 'Daftar Peminjaman & Booking Zoom';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Filter & Action Header -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('bookings.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari topik atau agenda rapat..."
                    class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <select name="status" onchange="this.form.submit()" class="py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Disetujui (ACC)</option>
                <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Link Aktif (Executed)</option>
                <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl hover:bg-slate-900 transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('bookings.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800">Reset</a>
            @endif
        </form>

        <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-blue-500/20 transition shrink-0">
            <i class="fa-solid fa-plus"></i> Ajukan Booking Baru
        </a>
    </div>

    <!-- Booking Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">Kegiatan / Topik</th>
                        <th class="px-5 py-3.5">Pemohon</th>
                        <th class="px-5 py-3.5">Jadwal & Waktu</th>
                        <th class="px-5 py-3.5">Akun Zoom</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($bookings as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4">
                                <a href="{{ route('bookings.show', $item->id) }}" class="font-bold text-slate-800 hover:text-blue-600 text-sm block">
                                    {{ $item->topic }}
                                </a>
                                @if($item->description)
                                    <p class="text-slate-400 text-[11px] mt-0.5 line-clamp-1">{{ $item->description }}</p>
                                @endif
                                @if($item->join_url)
                                    <div class="mt-1 flex items-center gap-2 text-[11px]">
                                        <span class="text-emerald-600 font-semibold"><i class="fa-solid fa-link"></i> Link Aktif</span>
                                        <span class="text-slate-400">ID: {{ $item->zoom_meeting_id }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-semibold text-slate-800 block">{{ $item->user->name ?? '-' }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $item->user->departemen ?? 'Umum' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-medium text-slate-800 block">
                                    {{ $item->start_date->format('d M Y') }}
                                    @if($item->day_count > 1)
                                        <span class="text-[10px] text-blue-600 font-semibold bg-blue-50 px-1.5 py-0.5 rounded">({{ $item->day_count }} Hari)</span>
                                    @endif
                                </span>
                                <span class="text-slate-500 text-[11px]">
                                    {{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB ({{ $item->duration_minutes }}m)
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($item->account)
                                    <span class="inline-flex items-center gap-1.5 font-medium text-slate-800">
                                        <i class="fa-solid fa-video text-blue-500 text-[10px]"></i> {{ $item->account->name }}
                                    </span>
                                    <span class="block text-slate-400 text-[10px]">{{ $item->account->capacity }} Peserta</span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum di-assign</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-full border text-[10px] font-bold tracking-wide {{ $item->status_badge }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right space-x-1">
                                <!-- Tombol Detail -->
                                <a href="{{ route('bookings.show', $item->id) }}" class="p-2 text-slate-500 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition inline-block" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <!-- Tombol ACC (Jika Admin dan Status 0) -->
                                @if(auth()->user() && auth()->user()->isAdmin() && $item->status === 0)
                                    <button onclick="openAccModal('{{ $item->id }}', '{{ addslashes($item->topic) }}')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-semibold transition" title="Setujui (ACC)">
                                        ACC
                                    </button>
                                @endif

                                <!-- Tombol Buat Link Zoom (Status 1: Approved) -->
                                @if($item->status === 1)
                                    <button onclick="openGenerateModal('{{ $item->id }}', '{{ addslashes($item->topic) }}', '{{ $item->start_date->format('Y-m-d') }}T{{ $item->start_time }}', '{{ $item->duration_minutes }}')"
                                        class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-semibold transition" title="Buat Link Zoom">
                                        <i class="fa-solid fa-bolt mr-1"></i> Buat Link
                                    </button>
                                @endif

                                <!-- Tombol Buka Link Langsung jika status 3 -->
                                @if($item->join_url)
                                    <a href="{{ $item->join_url }}" target="_blank" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-[11px] font-semibold transition inline-block">
                                        Join
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-2"></i>
                                <p>Tidak ada data peminjaman yang cocok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal ACC -->
<div id="accModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 mb-1">Setujui Permohonan (ACC)</h3>
        <p class="text-xs text-slate-500 mb-4" id="accTopicText"></p>

        <form id="accForm" method="POST" action="">
            @csrf
            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Akun Zoom Yang Ditugaskan *</label>
                    <select name="zoom_account_id" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Akun Zoom --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->capacity }} Peserta)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Catatan Admin (Opsional)</label>
                    <textarea name="admin_note" rows="3" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Harap segera disiapkan materinya..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeAccModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 text-xs">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 text-xs shadow-md shadow-emerald-600/30">Setujui (ACC)</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Generate Meeting Link via Zoom API -->
<div id="generateModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">
                <i class="fa-solid fa-video"></i>
            </span>
            <h3 class="text-base font-bold text-slate-800">Generate Link Zoom Meeting</h3>
        </div>
        <p class="text-xs text-slate-500 mb-4">Sistem akan memanggil Zoom API dan menerbitkan Link, Meeting ID, dan Passcode otomatis.</p>

        <form id="generateForm" method="POST" action="">
            @csrf
            <div class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Topik Meeting</label>
                    <input type="text" name="topic" id="genTopic" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Waktu Mulai (Tanggal & Jam)</label>
                    <input type="datetime-local" name="start_time" id="genStartTime" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Durasi (Menit)</label>
                        <input type="number" name="duration" id="genDuration" min="15" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Passcode Meeting</label>
                        <div class="flex gap-1.5">
                            <input type="text" name="passcode" id="genPasscode" placeholder="123456" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="randomizePassword()" class="px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl" title="Randomize">
                                <i class="fa-solid fa-dice"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeGenerateModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 text-xs">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 text-xs shadow-md shadow-blue-600/30">Proses Buat Link</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAccModal(id, topic) {
        document.getElementById('accTopicText').innerText = topic;
        document.getElementById('accForm').action = '/bookings/' + id + '/approve';
        document.getElementById('accModal').classList.remove('hidden');
    }

    function closeAccModal() {
        document.getElementById('accModal').classList.add('hidden');
    }

    function openGenerateModal(id, topic, startTime, duration) {
        document.getElementById('genTopic').value = topic;
        document.getElementById('genStartTime').value = startTime;
        document.getElementById('genDuration').value = duration;
        randomizePassword();
        document.getElementById('generateForm').action = '/bookings/' + id + '/generate-meeting';
        document.getElementById('generateModal').classList.remove('hidden');
    }

    function closeGenerateModal() {
        document.getElementById('generateModal').classList.add('hidden');
    }

    function randomizePassword() {
        const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        let res = '';
        for (let i = 0; i < 6; i++) {
            res += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('genPasscode').value = res;
    }
</script>
@endpush
@endsection
