@extends('layouts.app')

@php
    $header = 'Detail Peminjaman Zoom';
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Top Action Bar -->
    <x-page-header title="Detail Peminjaman #ZM-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}" description="Informasi lengkap permohonan peminjaman slot rapat Zoom" :backUrl="route('bookings.index')" backText="Kembali ke Daftar">
        <x-slot:actions>
            @if(auth()->user() && auth()->user()->isAdmin() && $booking->status === 0)
                <x-btn variant="white" onclick="document.getElementById('accModal').classList.remove('hidden')">
                    <i class="fa-solid fa-check text-emerald-600"></i> Setujui (ACC)
                </x-btn>
                <x-btn variant="danger" onclick="document.getElementById('rejectModal').classList.remove('hidden')">
                    <i class="fa-solid fa-xmark"></i> Tolak
                </x-btn>
            @endif

            @if($booking->status === 1)
                <x-btn variant="primary" onclick="document.getElementById('genModal').classList.remove('hidden')">
                    <i class="fa-solid fa-bolt text-yellow-400"></i> Buat Link Zoom
                </x-btn>
            @endif

            <x-btn variant="white" :href="route('memos.create', ['booking_id' => $booking->id])">
                <i class="fa-solid fa-file-lines text-gray-500"></i> Buat Memo
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    <!-- Main Detail Card -->
    <x-card>
        <div class="p-6 space-y-6">
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-5 border-b border-gray-200">
                <div>
                    <div class="mb-2">
                        <x-status-badge :status="$booking->status" :label="$booking->status_label" />
                    </div>
                    <h2 class="text-base font-bold text-gray-900">{{ $booking->topic }}</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pemohon: <span class="font-medium text-gray-800">{{ $booking->user->name ?? '-' }}</span> &bull; 
                        Unit/Departemen: <span class="text-gray-700">{{ $booking->user->departemen ?? 'Umum' }}</span>
                    </p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-[11px] text-gray-400 block uppercase tracking-wider">Tanggal Pengajuan</span>
                    <span class="font-mono text-xs text-gray-700 font-medium">{{ $booking->created_at->format('d/m/Y H:i') }} WIB</span>
                </div>
            </div>

            <!-- Zoom Link Active Box (Flat) -->
            @if($booking->status === 3 && $booking->join_url)
                <div class="p-4 bg-gray-50 border border-gray-200 rounded">
                    <div class="flex items-center gap-2 text-gray-900 font-semibold text-xs mb-3">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Link Pertemuan Zoom Telah Dibuat
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <span class="text-gray-400 block text-[11px]">Meeting ID:</span>
                            <span class="font-mono font-semibold text-gray-900 select-all">{{ $booking->zoom_meeting_id }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[11px]">Passcode:</span>
                            <span class="font-mono font-semibold text-gray-900 select-all">{{ $booking->passcode ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[11px]">Link Join:</span>
                            <a href="{{ $booking->join_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1 mt-0.5">
                                Buka Ruang Zoom <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Grid Details -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs border-b border-gray-200 pb-5">
                <div>
                    <span class="text-gray-400 block mb-1 text-[11px] uppercase tracking-wider">Jadwal & Waktu Pelaksanaan</span>
                    <p class="font-medium text-gray-900">{{ $booking->date_range_formatted }}</p>
                    <p class="text-gray-500 mt-0.5">
                        Pukul {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} WIB ({{ $booking->duration_minutes }} Menit)
                    </p>
                </div>

                <div>
                    <span class="text-gray-400 block mb-1 text-[11px] uppercase tracking-wider">Akun Zoom Ditugaskan</span>
                    @if($booking->account)
                        <p class="font-medium text-gray-900">{{ $booking->account->name }}</p>
                        <p class="text-gray-500 mt-0.5">Kapasitas: {{ $booking->account->capacity }} Peserta &bull; {{ $booking->account->email }}</p>
                    @else
                        <span class="text-amber-700 italic">Belum dialokasikan oleh Admin</span>
                    @endif
                </div>

                <div class="sm:col-span-2">
                    <span class="text-gray-400 block mb-1 text-[11px] uppercase tracking-wider">Deskripsi / Agenda Kegiatan</span>
                    <div class="bg-gray-50 p-3 rounded border border-gray-200 text-gray-700 leading-relaxed">
                        {{ $booking->description ?: 'Tidak ada deskripsi agenda.' }}
                    </div>
                </div>

                @if($booking->user_note)
                    <div class="sm:col-span-2">
                        <span class="text-gray-400 block mb-1 text-[11px] uppercase tracking-wider">Catatan Tambahan Pemohon</span>
                        <div class="bg-gray-50 p-3 rounded border border-gray-200 text-gray-600">
                            {{ $booking->user_note }}
                        </div>
                    </div>
                @endif

                @if($booking->admin_note)
                    <div class="sm:col-span-2">
                        <span class="text-gray-400 block mb-1 text-[11px] uppercase tracking-wider">Catatan Administrator</span>
                        <div class="bg-blue-50/50 p-3 rounded border border-blue-200 text-blue-950 font-medium">
                            {{ $booking->admin_note }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer Action links -->
            <div class="flex items-center justify-between text-xs pt-2">
                <span class="text-gray-400">Terakhir diperbarui: {{ $booking->updated_at->format('d/m/Y H:i') }}</span>
                <div class="flex items-center gap-2">
                    @if(auth()->user()->isAdmin() || ($booking->status === 0 && $booking->user_id === auth()->id()))
                        <x-btn variant="white" :href="route('bookings.edit', $booking->id)">
                            <i class="fa-regular fa-pen-to-square"></i> Edit Booking
                        </x-btn>
                    @endif
                </div>
            </div>
        </div>
    </x-card>
</div>

<!-- Modal ACC (Flat) -->
@if(auth()->user() && auth()->user()->isAdmin())
    <div id="accModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded border border-gray-300 max-w-md w-full shadow-lg p-5">
            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-2">Setujui Permohonan Booking</h4>
            <p class="text-xs text-gray-500 mb-4">Pilih akun Zoom yang akan ditugaskan untuk kegiatan ini.</p>

            <form method="POST" action="{{ route('bookings.approve', $booking->id) }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tugaskan Akun Zoom *</label>
                    <select name="zoom_account_id" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono text-xs">
                        <option value="">-- Pilih Akun Zoom --</option>
                        @foreach(\App\Models\ZoomAccount::whereDate('expired_date', '>=', now()->toDateString())->orderBy('expired_date', 'asc')->get() as $acc)
                            <option value="{{ $acc->id }}" {{ $booking->zoom_account_id == $acc->id ? 'selected' : '' }}>
                                {{ $acc->email }} ({{ $acc->capacity }} Pax) &bull; Masa Berlaku s/d {{ $acc->expired_date ? $acc->expired_date->format('d/m/Y') : '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Catatan Admin (Opsional)</label>
                    <input type="text" name="admin_note" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500" placeholder="Contoh: Disetujui, gunakan ruang A...">
                </div>

                <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('accModal').classList.add('hidden')" class="px-3 py-1.5 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white rounded">Simpan & ACC</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reject (Flat) -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded border border-gray-300 max-w-md w-full shadow-lg p-5">
            <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-2">Tolak Permohonan Booking</h4>
            <p class="text-xs text-gray-500 mb-4">Berikan alasan penolakan agar pemohon dapat mengetahuinya.</p>

            <form method="POST" action="{{ route('bookings.reject', $booking->id) }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Alasan Penolakan *</label>
                    <textarea name="admin_note" required rows="3" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500" placeholder="Tulis alasan penolakan..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-3 py-1.5 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded">Tolak Permohonan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Generate Meeting (Flat) -->
    @if($booking->status === 1)
        <div id="genModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded border border-gray-300 max-w-md w-full shadow-lg p-5">
                <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-2">Generate Link Zoom Meeting</h4>
                <p class="text-xs text-gray-500 mb-4">Sistem akan memanggil Zoom API untuk membuat ruang pertemuan secara otomatis.</p>

                <form method="POST" action="{{ route('bookings.generate-meeting', $booking->id) }}" class="space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Topik Meeting</label>
                        <input type="text" name="topic" value="{{ $booking->topic }}" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Waktu Mulai</label>
                            <input type="datetime-local" name="start_time" value="{{ $booking->start_date->format('Y-m-d') }}T{{ substr($booking->start_time, 0, 5) }}" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Durasi (Menit)</label>
                            <input type="number" name="duration" value="{{ $booking->duration_minutes }}" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Passcode (Opsional)</label>
                        <input type="text" name="passcode" value="123456" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded font-mono">
                    </div>

                    <!-- Custom Meeting Overrides (From SystemSettings) -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded space-y-2">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Pengaturan Tambahan Room</span>

                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-1.5 text-gray-700 cursor-pointer">
                                <input type="checkbox" name="waiting_room" value="1" {{ \App\Models\SystemSetting::get('zoom_waiting_room', 1) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-0">
                                <span>Waiting Room</span>
                            </label>

                            <label class="flex items-center gap-1.5 text-gray-700 cursor-pointer">
                                <input type="checkbox" name="mute_upon_entry" value="1" {{ \App\Models\SystemSetting::get('zoom_mute_upon_entry', 1) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-0">
                                <span>Mute Saat Masuk</span>
                            </label>

                            <label class="flex items-center gap-1.5 text-gray-700 cursor-pointer">
                                <input type="checkbox" name="join_before_host" value="1" {{ \App\Models\SystemSetting::get('zoom_join_before_host', 0) ? 'checked' : '' }} class="rounded border-gray-300 text-gray-900 focus:ring-0">
                                <span>Join Sebelum Host</span>
                            </label>
                        </div>

                        <div class="pt-1.5">
                            <label class="block font-medium text-gray-600 mb-0.5 text-[11px]">Auto Recording</label>
                            <select name="auto_recording" class="w-full px-2 py-1 bg-white border border-gray-300 rounded text-[11px]">
                                <option value="none" {{ \App\Models\SystemSetting::get('zoom_auto_recording', 'none') === 'none' ? 'selected' : '' }}>None (Tanpa Rekam)</option>
                                <option value="cloud" {{ \App\Models\SystemSetting::get('zoom_auto_recording', 'none') === 'cloud' ? 'selected' : '' }}>Cloud Recording</option>
                                <option value="local" {{ \App\Models\SystemSetting::get('zoom_auto_recording', 'none') === 'local' ? 'selected' : '' }}>Local Recording</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                        <button type="button" onclick="document.getElementById('genModal').classList.add('hidden')" class="px-3 py-1.5 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white rounded">Eksekusi API</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endif
@endsection
