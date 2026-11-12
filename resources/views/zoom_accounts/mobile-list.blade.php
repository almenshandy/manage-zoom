<div class="mobile-record-list md:hidden">
    @forelse($accounts as $acc)
        @php($isValid = $acc->expired_date && !$acc->expired_date->isPast())
        <x-mobile-record :title="$acc->name" :badge="$isValid ? 'Aktif' : 'Kedaluwarsa'" :badge-class="$isValid ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'"
            data-zoom-account data-account-name="{{ $acc->name }}" data-account-email="{{ $acc->email }}" data-account-password="{{ $acc->password ?? '' }}">
            <p class="mobile-record-email">{{ $acc->email }}</p>
            <dl class="mobile-record-facts">
                <div><dt>Kapasitas</dt><dd>{{ $acc->capacity }} peserta</dd></div>
                <div><dt>Berlaku sampai</dt><dd>{{ $acc->expired_date?->format('d M Y') ?? 'Belum diatur' }}</dd></div>
            </dl>
            <div class="mobile-password-row">
                <div><span class="mobile-field-label">Password</span><span id="account-password-mobile-{{ $acc->id }}" class="account-password">{{ $acc->password !== null && $acc->password !== '' ? '********' : 'Belum diisi' }}</span></div>
                @if($acc->password !== null && $acc->password !== '')
                    <button type="button" onclick="toggleAccountPassword(this)" aria-controls="account-password-mobile-{{ $acc->id }}" aria-pressed="false" aria-label="Tampilkan password" title="Tampilkan password"><i class="fa-regular fa-eye" aria-hidden="true"></i></button>
                @endif
            </div>
            <details class="mobile-record-disclosure">
                <summary>Informasi & tindakan lainnya <i class="fa-solid fa-chevron-down" aria-hidden="true"></i></summary>
                <div class="mobile-disclosure-content">
                    <p class="mobile-record-caption">API Zoom: {{ $acc->account_id && $acc->client_id ? 'S2S siap digunakan' : 'Menggunakan pengaturan global' }}</p>
                    <div class="mobile-secondary-actions">
                        <button type="button" onclick="testConnection({{ $acc->id }}, this.closest('[data-zoom-account]').dataset.accountName, this)"><i class="fa-solid fa-plug" aria-hidden="true"></i> Uji koneksi</button>
                        <form method="POST" action="{{ route('zoom-accounts.destroy', $acc) }}" onsubmit="return confirm('Hapus akun Zoom ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mobile-danger-action"><i class="fa-regular fa-trash-can" aria-hidden="true"></i> Hapus akun</button>
                        </form>
                    </div>
                </div>
            </details>
            <x-slot:footer>
                <button type="button" onclick="copyZoomAccount(this)" class="mobile-record-button"><i class="fa-regular fa-copy" aria-hidden="true"></i> Salin akun</button>
                <a class="mobile-record-edit" href="{{ route('zoom-accounts.edit', $acc) }}"><i class="fa-regular fa-pen-to-square" aria-hidden="true"></i> Edit</a>
            </x-slot:footer>
        </x-mobile-record>
    @empty
        <div class="mobile-empty-state"><i class="fa-solid fa-video" aria-hidden="true"></i><h2>Belum ada akun Zoom</h2><p>Tambahkan akun atau sinkronkan data yang tersedia.</p></div>
    @endforelse
</div>
