<div class="px-5 py-3 border-b border-gray-200 text-xs text-gray-500">
    {{ $bookings->total() }} meeting / booking terkait proyek {{ $project->name }} (semua status).
</div>
<div class="mobile-meetings-list mobile-record-list md:hidden">
    @forelse($bookings as $booking)
        <x-mobile-record :title="$booking->topic" :href="route('bookings.show', $booking)" :badge="$booking->status_label" :badge-class="$booking->status_badge">
            <div class="mobile-schedule-row">
                <span class="mobile-schedule-icon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                <div><p class="mobile-schedule-date">{{ $booking->date_range_formatted }}</p><p class="mobile-record-caption">{{ substr($booking->start_time ?? '', 0, 5) ?: '-' }} - {{ substr($booking->end_time ?? '', 0, 5) ?: '-' }} WIB</p></div>
            </div>
            <dl class="mobile-record-facts">
                <div><dt>Pemohon</dt><dd>{{ $booking->user?->name ?? '-' }}</dd></div>
                <div><dt>Akun Zoom</dt><dd>{{ $booking->account?->name ?? 'Belum dialokasikan' }}</dd></div>
            </dl>
            <x-slot:footer><a class="mobile-record-link" href="{{ route('bookings.show', $booking) }}">Lihat Detail <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></x-slot:footer>
        </x-mobile-record>
    @empty
        <div class="mobile-empty-state"><i class="fa-regular fa-calendar" aria-hidden="true"></i><h2>Belum ada meeting</h2><p>Belum ada meeting terkait proyek ini.</p></div>
    @endforelse
</div>
<div class="hidden md:block overflow-x-auto">
    <table class="mobile-card-table w-full text-left text-xs">
        <thead class="bg-gray-50 text-gray-600 border-b border-gray-200 text-[11px] uppercase tracking-wider">
            <tr>
                <th class="px-4 py-3">Kegiatan / Topik</th>
                <th class="px-4 py-3">Jadwal & Waktu</th>
                <th class="px-4 py-3">Pemohon</th>
                <th class="px-4 py-3">Akun Zoom</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Detail</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-gray-700">
            @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50/75">
                    <td data-label="Kegiatan / Topik" class="px-4 py-3 font-semibold text-gray-900">{{ $booking->topic }}</td>
                    <td data-label="Jadwal & Waktu" class="px-4 py-3 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $booking->date_range_formatted }}</div>
                        <div class="text-[11px] text-gray-500">{{ substr($booking->start_time ?? '', 0, 5) ?: '-' }} - {{ substr($booking->end_time ?? '', 0, 5) ?: '-' }} WIB</div>
                    </td>
                    <td data-label="Pemohon" class="px-4 py-3">{{ $booking->user?->name ?? '-' }}</td>
                    <td data-label="Akun Zoom" class="px-4 py-3">
                        @if($booking->account)
                            <div class="font-medium text-gray-900">{{ $booking->account->name }}</div>
                            <div class="text-[11px] text-gray-500">{{ $booking->account->email }}</div>
                        @else
                            <span class="text-gray-400 italic">Belum dialokasikan</span>
                        @endif
                    </td>
                    <td data-label="Status" class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded-sm border {{ $booking->status_badge }}">{{ $booking->status_label }}</span>
                    </td>
                    <td data-label="Detail" class="px-4 py-3 whitespace-nowrap">
                        <a href="{{ route('bookings.show', $booking) }}" class="text-blue-700 hover:text-blue-900 font-medium">Lihat Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada meeting terkait proyek ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($bookings->hasPages())
    <div data-meetings-pagination class="px-4 py-3 border-t border-gray-200">
        {{ $bookings->links() }}
    </div>
@endif
