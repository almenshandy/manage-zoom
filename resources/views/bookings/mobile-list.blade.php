<div class="mobile-record-list md:hidden">
    @forelse($bookings as $item)
        <x-mobile-record :title="$item->topic" :href="route('bookings.show', $item)" :badge="$item->status_label" :badge-class="$item->status_badge">
            @if($item->project_name)
                <p class="mobile-record-caption"><i class="fa-solid fa-briefcase" aria-hidden="true"></i> {{ $item->project_name }}</p>
            @endif
            <div class="mobile-schedule-row">
                <span class="mobile-schedule-icon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                <div>
                    <p class="mobile-schedule-date">{{ $item->date_range_formatted }}</p>
                    <p class="mobile-record-caption">{{ substr($item->start_time ?? '', 0, 5) ?: '-' }} – {{ substr($item->end_time ?? '', 0, 5) ?: '-' }} WIB</p>
                </div>
            </div>
            <p class="mobile-record-caption"><i class="fa-regular fa-user" aria-hidden="true"></i> {{ $item->user?->name ?? '-' }}</p>
            <details class="mobile-record-disclosure">
                <summary>Informasi & tindakan lainnya <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary>
                <div class="mobile-disclosure-content">
                    <dl class="mobile-record-facts">
                        <div><dt>Akun Zoom</dt><dd>{{ $item->account?->name ?? 'Belum dialokasikan' }}</dd></div>
                        @if($item->description)
                            <div class="mobile-fact-wide"><dt>Agenda</dt><dd>{{ $item->description }}</dd></div>
                        @endif
                    </dl>
                    <div class="mobile-secondary-actions">
                        <a href="{{ route('bookings.print', $item) }}" target="_blank" rel="noopener"><i class="fa-solid fa-print" aria-hidden="true"></i> Cetak bukti</a>
                        @if(auth()->user()->isAdmin() || ($item->status === 0 && $item->user_id === auth()->id()))
                            <a href="{{ route('bookings.edit', $item) }}"><i class="fa-regular fa-pen-to-square" aria-hidden="true"></i> Edit booking</a>
                        @endif
                        @if(auth()->user()->isAdmin() || $item->user_id === auth()->id())
                            <form method="POST" action="{{ route('bookings.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan booking ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mobile-danger-action"><i class="fa-regular fa-trash-can" aria-hidden="true"></i> Hapus booking</button>
                            </form>
                        @endif
                    </div>
                </div>
            </details>
            <x-slot:footer>
                <a class="mobile-record-link" href="{{ route('bookings.show', $item) }}">Lihat detail <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </x-slot:footer>
        </x-mobile-record>
    @empty
        <div class="mobile-empty-state"><i class="fa-regular fa-calendar-xmark" aria-hidden="true"></i><h2>Belum ada booking</h2><p>Coba ubah filter atau ajukan booking baru.</p></div>
    @endforelse
</div>
