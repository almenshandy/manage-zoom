<div class="mobile-record-list md:hidden">
    @forelse($projects as $p)
        <x-mobile-record :title="$p->name" :badge="match ($p->status) { 'active' => 'Aktif', 'completed' => 'Selesai', default => 'Nonaktif' }"
            :badge-class="match ($p->status) { 'active' => 'bg-emerald-50 text-emerald-700', 'completed' => 'bg-blue-50 text-blue-700', default => 'bg-gray-100 text-gray-600' }">
            <p class="mobile-record-caption">{{ $p->department?->nama_departemen ?? 'Lintas / Umum' }}@if($p->code) &bull; {{ $p->code }}@endif</p>
            <div class="mobile-schedule-row">
                <span class="mobile-schedule-icon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
                <div>
                    <p class="mobile-field-label">Periode pelaksanaan</p>
                    <p class="mobile-schedule-date">
                        @if($p->start_date && $p->end_date)
                            {{ $p->start_date->format('d M Y') }} – {{ $p->end_date->format('d M Y') }}
                        @elseif($p->start_date)
                            Mulai {{ $p->start_date->format('d M Y') }}
                        @else
                            Fleksibel / tanpa batas
                        @endif
                    </p>
                </div>
            </div>
            <button type="button" onclick="openProjectMeetings(this)" data-url="{{ route('projects.meetings', $p) }}" data-project-name="{{ $p->name }}"
                aria-haspopup="dialog" aria-controls="projectMeetingsModal" class="mobile-meetings-link">
                <span><i class="fa-solid fa-video" aria-hidden="true"></i> <strong>{{ $p->bookings_count }}</strong> meeting terkait</span><i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </button>
            <details class="mobile-record-disclosure">
                <summary>Informasi & tindakan lainnya <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary>
                <div class="mobile-disclosure-content">
                    @if($p->description)<p class="mobile-record-caption">{{ $p->description }}</p>@endif
                    <form method="POST" action="{{ route('projects.destroy', $p) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data proyek ini?')" class="mobile-secondary-actions">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="mobile-danger-action"><i class="fa-regular fa-trash-can" aria-hidden="true"></i> Hapus proyek</button>
                    </form>
                </div>
            </details>
            <x-slot:footer>
                <button type="button" onclick="openEditModal({{ json_encode($p) }})" class="mobile-record-link">Edit proyek <i class="fa-regular fa-pen-to-square" aria-hidden="true"></i></button>
            </x-slot:footer>
        </x-mobile-record>
    @empty
        <div class="mobile-empty-state"><i class="fa-solid fa-briefcase" aria-hidden="true"></i><h2>Belum ada proyek</h2><p>Tambahkan proyek untuk mengelompokkan meeting kegiatan.</p></div>
    @endforelse
</div>
