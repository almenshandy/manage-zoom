@extends('layouts.app')

@php($header = 'Kirim Bulk WhatsApp')

@section('content')
<div class="max-w-6xl mx-auto space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-600">Periksa meeting, isi pesan, dan nomor tujuan sebelum membuat antrean.</p>
        <a href="{{ route('bookings.index') }}" class="text-sm font-medium text-gray-700 underline underline-offset-4">Kembali ke booking</a>
    </div>
    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-sm text-red-700" role="alert">{{ $errors->first() }}</div>
    @endif
    <form id="bulkWhatsAppForm" method="POST" action="{{ route('bookings.whatsapp-bulk.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
        @csrf
        <input type="hidden" id="bulkRequestId" value="{{ \Illuminate\Support\Str::uuid() }}">
        <div class="lg:col-span-2 space-y-5 min-w-0">
            <section class="bg-white border border-gray-200" aria-labelledby="bulkMeetingsHeading">
                <div class="p-4 border-b border-gray-200">
                    <h2 id="bulkMeetingsHeading" class="text-sm font-semibold text-gray-900">1. Meeting yang dipilih <span class="text-gray-500">({{ $bookings->count() }})</span></h2>
                    <p class="mt-1 text-xs text-gray-500">Nama pemohon dicantumkan pada setiap pesan sebagai “Booking atas nama”.</p>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                        <article class="p-4 space-y-2 min-w-0">
                            <p class="text-xs text-gray-500">Booking atas nama: <strong class="text-gray-900">{{ $booking->user?->name ?? '-' }}</strong></p>
                            <h3 class="text-sm font-semibold text-gray-900 break-words">{{ $booking->topic }}</h3>
                            <p class="text-xs text-gray-600">{{ $booking->date_range_formatted }} &bull; {{ substr($booking->start_time ?? '', 0, 5) }}–{{ substr($booking->end_time ?? '', 0, 5) }} WIB</p>
                            <p class="text-xs text-gray-600">Akun Zoom: {{ $booking->account?->email ?? 'Belum ditentukan' }}</p>
                            <details class="border border-gray-200 text-xs">
                                <summary class="p-3 cursor-pointer font-medium text-gray-700">Pratinjau pesan</summary>
                                <pre data-bulk-preview="{{ $booking->id }}" class="p-3 pt-0 whitespace-pre-wrap break-words font-mono text-xs text-gray-600"></pre>
                            </details>
                        </article>
                    @empty
                        <div class="p-5 text-sm text-gray-600">Belum ada meeting dipilih. <a class="font-semibold underline" href="{{ route('bookings.index') }}">Pilih meeting dari daftar booking</a>.</div>
                    @endforelse
                </div>
            </section>
            <section class="p-4 bg-white border border-gray-200 space-y-3" aria-labelledby="bulkContentHeading">
                <h2 id="bulkContentHeading" class="text-sm font-semibold text-gray-900">2. Isi pesan</h2>
                <p class="text-xs text-gray-600">Undangan memuat nama pemohon, topik, jadwal, link meeting, Meeting ID, dan passcode peserta.</p>
                <label class="flex items-start gap-3 p-3 border border-gray-200 cursor-pointer">
                    <input type="checkbox" id="bulkIncludeCredentials" class="mt-0.5 shrink-0" @disabled(!$canIncludeAccounts)>
                    <span><span class="block text-sm font-medium text-gray-900">Sertakan akun Zoom</span><span class="block mt-1 text-xs text-gray-500">Tambahkan email dan password akun host ke setiap pesan. Password asli ikut dikirim; pratinjau menampilkan asterisk.</span></span>
                </label>
                @unless($canIncludeAccounts)
                    <p class="text-xs text-amber-800">Pilihan akun tersedia jika setiap meeting yang dipilih memiliki akun Zoom aktif dengan email dan password lengkap.</p>
                @endunless
            </section>
            <section class="p-4 bg-white border border-gray-200 space-y-3" aria-labelledby="bulkRecipientsHeading">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 id="bulkRecipientsHeading" class="text-sm font-semibold text-gray-900">3. Nomor tujuan</h2>
                    <button type="button" id="openBulkRecipients" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold border border-gray-300 text-gray-800"><i class="fa-solid fa-address-book" aria-hidden="true"></i> Pilih nomor tujuan</button>
                </div>
                <p class="text-xs text-gray-500">Pilih nomor dari profil pengguna atau tambahkan nomor lain melalui pemilih nomor. Maksimal 50 nomor berbeda.</p>
                <div id="bulkRecipientList" class="flex flex-wrap gap-2" aria-live="polite"></div>
            </section>
        </div>
        <aside class="p-4 bg-white border border-gray-200 space-y-4 lg:sticky lg:top-5">
            <h2 class="text-sm font-semibold text-gray-900">Ringkasan pengiriman</h2>
            <div><label for="bulkInterval" class="block text-xs font-medium text-gray-700 mb-2">Jeda antar pesan</label><select id="bulkInterval" class="w-full p-2 border border-gray-300 text-sm">@for($seconds = 7; $seconds <= 15; $seconds++)<option value="{{ $seconds }}" @selected($seconds === 10)>{{ $seconds }} detik</option>@endfor</select></div>
            <p id="bulkEstimate" class="p-3 bg-gray-50 border border-gray-200 text-xs text-gray-600 leading-relaxed" aria-live="polite"></p>
            <p id="bulkMessageType" class="text-xs font-medium text-gray-700"></p>
            <p class="text-xs text-gray-500">Satu pesan per meeting untuk setiap nomor tujuan. Jeda dihitung setelah respons gateway; antrean lain dapat menambah waktu tunggu.</p>
            <div id="bulkFormError" class="hidden p-3 bg-red-50 text-red-700 border border-red-200 text-xs" role="alert"></div>
            <p id="bulkFormSuccess" class="hidden p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs" role="status"></p>
            <button type="submit" id="submitBulkWhatsApp" class="w-full px-4 py-3 bg-emerald-700 text-white text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed" disabled>Masukkan ke antrean</button>
        </aside>
    </form>
    @include('bookings.bulk-whatsapp')
</div>

<div id="bulkRecipientsModal" class="hidden fixed inset-0 z-50 bg-gray-900/60 flex items-center justify-center p-3 sm:p-5" role="dialog" aria-modal="true" aria-labelledby="bulkRecipientsTitle">
    <div class="bg-white border border-gray-300 w-full max-w-2xl max-h-[90dvh] flex flex-col overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between gap-3 shrink-0">
            <div><h2 id="bulkRecipientsTitle" class="text-sm font-semibold text-gray-900">Pilih nomor WhatsApp</h2><p class="mt-1 text-xs text-gray-500">Nomor yang tersimpan di profil pengguna aktif.</p></div>
            <button type="button" id="closeBulkRecipients" class="min-w-11 min-h-11 text-gray-600" aria-label="Tutup pemilih nomor"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
        <div class="overflow-y-auto min-h-0 p-4 space-y-3">
            <label for="bulkContactSearch" class="block text-xs font-medium text-gray-700">Cari nama atau nomor</label>
            <input id="bulkContactSearch" type="search" maxlength="100" placeholder="Cari pengguna..." class="w-full p-3 border border-gray-300 text-sm">
            <label class="inline-flex items-center gap-2 min-h-11 text-xs text-gray-700"><input type="checkbox" id="bulkContactsSelectAll"> Pilih semua nomor di halaman ini</label>
            <p id="bulkContactsStatus" class="text-xs text-gray-500" aria-live="polite"></p>
            <div id="bulkContactsList" class="border border-gray-200 divide-y divide-gray-200" aria-busy="false"></div>
            <div class="flex justify-between items-center gap-2 text-xs">
                <button type="button" id="bulkContactsPrevious" class="px-3 py-2 border border-gray-300 disabled:opacity-40">Sebelumnya</button>
                <span id="bulkContactsPage"></span>
                <button type="button" id="bulkContactsNext" class="px-3 py-2 border border-gray-300 disabled:opacity-40">Berikutnya</button>
            </div>
            <div class="pt-3 border-t border-gray-200">
                <label for="bulkManualRecipients" class="block text-xs font-medium text-gray-700 mb-2">Nomor tambahan</label>
                <textarea id="bulkManualRecipients" rows="2" maxlength="5000" placeholder="081234567890&#10;6281234567891" class="w-full p-3 border border-gray-300 text-sm font-mono"></textarea>
                <p class="mt-1 text-xs text-gray-500">Pisahkan nomor tambahan dengan koma, titik koma, atau baris baru. Nomor duplikat digabung otomatis.</p>
            </div>
            <p id="bulkContactsError" class="hidden text-xs text-red-700" role="alert"></p>
        </div>
        <div class="p-4 border-t border-gray-200 flex flex-wrap justify-between items-center gap-3 shrink-0">
            <span id="bulkContactsSelected" class="text-xs text-gray-600" aria-live="polite"></span>
            <div class="flex gap-2"><button type="button" id="cancelBulkRecipients" class="px-3 py-2 border border-gray-300 text-sm">Batal</button><button type="button" id="applyBulkRecipients" class="px-3 py-2 bg-emerald-700 text-white text-sm font-semibold">Gunakan nomor</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const previews = {{ \Illuminate\Support\Js::from($bulkPreviews) }};
    const countryCode = {{ \Illuminate\Support\Js::from($bulkCountryCode) }};
    const contactsUrl = {{ \Illuminate\Support\Js::from(route('bookings.whatsapp-bulk.contacts')) }};
    const form = document.getElementById('bulkWhatsAppForm');
    const includeCredentials = document.getElementById('bulkIncludeCredentials');
    const interval = document.getElementById('bulkInterval');
    const submit = document.getElementById('submitBulkWhatsApp');
    const formError = document.getElementById('bulkFormError');
    const success = document.getElementById('bulkFormSuccess');
    const modal = document.getElementById('bulkRecipientsModal');
    const open = document.getElementById('openBulkRecipients');
    const search = document.getElementById('bulkContactSearch');
    const manual = document.getElementById('bulkManualRecipients');
    const contactsError = document.getElementById('bulkContactsError');
    const all = document.getElementById('bulkContactsSelectAll');
    let recipients = new Map(), draft = new Map(), contacts = [];
    let page = 1, lastPage = 1, contactRequest, searchTimer, previousOverflow = '', submitting = false;

    function normalize(raw) {
        const number = raw.replace(/\D/g, '');
        return number.startsWith('0') ? countryCode + number.substring(1) : number;
    }
    function updateSummary() {
        const total = previews.length * recipients.size;
        document.getElementById('bulkEstimate').textContent = `${previews.length} meeting × ${recipients.size} nomor = ${total} pesan. Total jeda minimal sekitar ${Math.max(0, total - 1) * Number(interval.value)} detik, di luar waktu respons gateway.${total > 1000 ? ' Maksimal 1.000 pesan per antrean; kurangi nomor tujuan.' : ''}`;
        document.getElementById('bulkMessageType').textContent = includeCredentials.checked ? 'Isi: Undangan + email & password akun Zoom' : 'Isi: Undangan meeting';
        submit.disabled = submitting || !previews.length || !recipients.size || total > 1000;
        previews.forEach(item => {
            document.querySelector(`[data-bulk-preview="${item.id}"]`).textContent = item.message + (includeCredentials.checked ? item.account_preview || '' : '');
        });
    }
    function renderRecipients() {
        const list = document.getElementById('bulkRecipientList');
        list.replaceChildren();
        if (!recipients.size) {
            const empty = document.createElement('p');
            empty.className = 'text-xs text-gray-500 py-2';
            empty.textContent = 'Belum ada nomor tujuan dipilih.';
            list.append(empty);
        }
        recipients.forEach((name, number) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'px-3 py-2 border border-gray-300 text-xs text-left break-all';
            button.textContent = `${name ? name + ' · ' : ''}${number} ×`;
            button.setAttribute('aria-label', `Hapus nomor ${number}`);
            button.addEventListener('click', () => { if (!submitting) { recipients.delete(number); renderRecipients(); } });
            list.append(button);
        });
        updateSummary();
    }
    function syncDraft() {
        document.querySelectorAll('[data-contact-number]').forEach(input => { input.checked = draft.has(input.dataset.contactNumber); });
        const valid = contacts.filter(contact => contact.valid);
        const count = valid.filter(contact => draft.has(contact.phone)).length;
        all.checked = valid.length > 0 && count === valid.length;
        all.indeterminate = count > 0 && count < valid.length;
        all.disabled = valid.length === 0;
        document.getElementById('bulkContactsSelected').textContent = `${draft.size} nomor dipilih`;
    }
    function showContactsError(message) {
        contactsError.textContent = message;
        contactsError.classList.remove('hidden');
    }
    async function loadContacts() {
        contactRequest?.abort();
        const controller = new AbortController();
        contactRequest = controller;
        const timeout = setTimeout(() => controller.abort('timeout'), 20000);
        const list = document.getElementById('bulkContactsList');
        list.setAttribute('aria-busy', 'true');
        list.replaceChildren();
        contacts = [];
        syncDraft();
        contactsError.classList.add('hidden');
        document.getElementById('bulkContactsStatus').textContent = 'Memuat nomor...';
        document.getElementById('bulkContactsPrevious').disabled = true;
        document.getElementById('bulkContactsNext').disabled = true;
        try {
            const url = new URL(contactsUrl);
            url.searchParams.set('search', search.value);
            url.searchParams.set('page', page);
            const response = await fetch(url, { signal: controller.signal, headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Daftar nomor belum dapat dimuat. Cari ulang atau tambahkan nomor secara manual.');
            const data = await response.json();
            if (controller.signal.aborted) return;
            contacts = data.contacts;
            lastPage = data.last_page;
            document.getElementById('bulkContactsStatus').textContent = contacts.length ? `${data.total} pengguna dengan nomor tersimpan.` : 'Tidak ada nomor tersimpan yang cocok.';
            contacts.forEach(contact => {
                const label = document.createElement('label');
                label.className = 'flex items-center gap-3 p-3 cursor-pointer text-xs';
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.dataset.contactNumber = contact.phone;
                input.disabled = !contact.valid;
                input.addEventListener('change', () => {
                    if (input.checked && draft.size >= 50 && !draft.has(contact.phone)) { input.checked = false; showContactsError('Maksimal 50 nomor berbeda.'); return; }
                    if (input.checked) draft.set(contact.phone, contact.name); else draft.delete(contact.phone);
                    syncDraft();
                });
                const text = document.createElement('span');
                text.className = 'min-w-0 break-words';
                text.textContent = `${contact.name} · ${contact.phone || '-'}${contact.valid ? '' : ' (nomor perlu diperbaiki di profil)'}`;
                label.append(input, text);
                list.append(label);
            });
            document.getElementById('bulkContactsPage').textContent = `Halaman ${data.page} / ${data.last_page}`;
            document.getElementById('bulkContactsPrevious').disabled = page <= 1;
            document.getElementById('bulkContactsNext').disabled = page >= lastPage;
            syncDraft();
        } catch (exception) {
            if (controller.signal.aborted && controller.signal.reason !== 'timeout') return;
            document.getElementById('bulkContactsStatus').textContent = '';
            showContactsError('Daftar nomor belum dapat dimuat. Cari ulang atau tambahkan nomor secara manual.');
        } finally {
            clearTimeout(timeout);
            if (contactRequest === controller) list.setAttribute('aria-busy', 'false');
        }
    }
    function closeModal() {
        clearTimeout(searchTimer);
        contactRequest?.abort();
        modal.classList.add('hidden');
        document.body.style.overflow = previousOverflow;
        open.focus();
    }
    open.addEventListener('click', () => {
        if (submitting) return;
        draft = new Map(recipients);
        manual.value = '';
        previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        modal.classList.remove('hidden');
        search.focus();
        page = 1;
        loadContacts();
    });
    document.getElementById('closeBulkRecipients').addEventListener('click', closeModal);
    document.getElementById('cancelBulkRecipients').addEventListener('click', closeModal);
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    modal.addEventListener('keydown', event => {
        if (event.key === 'Escape') { event.preventDefault(); closeModal(); }
        if (event.key === 'Tab') {
            const controls = Array.from(modal.querySelectorAll('button:not([disabled]), input:not([disabled]), textarea')).filter(element => element.getClientRects().length);
            const first = controls[0], last = controls[controls.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        }
    });
    search.addEventListener('input', () => { clearTimeout(searchTimer); contactRequest?.abort(); page = 1; searchTimer = setTimeout(loadContacts, 250); });
    document.getElementById('bulkContactsPrevious').addEventListener('click', () => { if (page > 1) { page--; loadContacts(); } });
    document.getElementById('bulkContactsNext').addEventListener('click', () => { if (page < lastPage) { page++; loadContacts(); } });
    all.addEventListener('change', () => {
        for (const contact of contacts.filter(contact => contact.valid)) {
            if (all.checked) {
                if (draft.size >= 50 && !draft.has(contact.phone)) { showContactsError('Maksimal 50 nomor berbeda.'); break; }
                draft.set(contact.phone, contact.name);
            } else draft.delete(contact.phone);
        }
        syncDraft();
    });
    document.getElementById('applyBulkRecipients').addEventListener('click', () => {
        const next = new Map(draft);
        for (const raw of manual.value.split(/[,;\r\n]+/).map(value => value.trim()).filter(Boolean)) {
            const number = normalize(raw);
            if (!/^\+?[0-9\s().-]+$/.test(raw) || !/^[1-9][0-9]{7,14}$/.test(number)) { showContactsError('Ada nomor tambahan yang tidak valid. Gunakan 8–15 digit termasuk kode negara.'); return; }
            if (!next.has(number)) next.set(number, '');
        }
        if (next.size > 50) { showContactsError('Maksimal 50 nomor berbeda.'); return; }
        recipients = next;
        renderRecipients();
        closeModal();
    });
    includeCredentials.addEventListener('change', updateSummary);
    interval.addEventListener('change', updateSummary);
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (submit.disabled) return;
        submitting = true;
        updateSummary();
        submit.textContent = 'Membuat antrean...';
        [open, includeCredentials, interval].forEach(input => { input.disabled = true; });
        formError.classList.add('hidden');
        success.classList.add('hidden');
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 30000);
        try {
            const response = await fetch(form.action, {
                method: 'POST', signal: controller.signal,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ request_id: document.getElementById('bulkRequestId').value, booking_ids: previews.map(item => item.id), recipients: [...recipients.keys()].join('\n'), interval_seconds: Number(interval.value), include_credentials: includeCredentials.checked }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.errors ? Object.values(data.errors).flat().join(' ') : data.message || 'Antrean belum dapat dibuat.');
            document.getElementById('bulkRequestId').value = data.next_request_id;
            success.textContent = `Antrean berhasil dibuat untuk ${data.total_messages} pesan. Lihat progres di bagian antrean.`;
            success.classList.remove('hidden');
            recipients.clear();
            renderRecipients();
            document.dispatchEvent(new CustomEvent('bulk-whatsapp-queued', { detail: data }));
        } catch (exception) {
            formError.textContent = controller.signal.aborted ? 'Respons terlalu lama. Coba lagi; permintaan yang sama tidak akan membuat antrean ganda.' : exception.message;
            formError.classList.remove('hidden');
        } finally {
            clearTimeout(timeout);
            submitting = false;
            open.disabled = false;
            interval.disabled = false;
            includeCredentials.disabled = {{ \Illuminate\Support\Js::from(!$canIncludeAccounts) }};
            submit.textContent = 'Masukkan ke antrean';
            updateSummary();
        }
    });
    window.addEventListener('pagehide', () => { clearTimeout(searchTimer); contactRequest?.abort(); });
    renderRecipients();
})();
</script>
@endpush
