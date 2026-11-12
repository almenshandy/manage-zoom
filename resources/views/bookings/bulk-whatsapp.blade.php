<section class="bg-white border border-gray-200" aria-labelledby="bulkHistoryHeading">
    <div class="p-4 border-b border-gray-200">
        <h2 id="bulkHistoryHeading" class="text-sm font-semibold text-gray-900">Antrean Bulk WhatsApp</h2>
        <p class="text-xs text-gray-500 mt-1">Buka masing-masing antrean untuk melihat progres pengiriman. Pengiriman tetap berjalan saat halaman ditutup.</p>
    </div>
    <div id="bulkHistoryList" class="divide-y divide-gray-200">
        @forelse($bulkBatches as $batch)
            <details data-bulk-batch-id="{{ $batch->id }}" data-bulk-status-url="{{ route('bookings.whatsapp-bulk.show', $batch) }}" class="group">
                <summary class="block p-4 list-none cursor-pointer select-none hover:bg-gray-50 focus-visible:outline-2 focus-visible:outline-brand-600 [&::-webkit-details-marker]:hidden">
                    <span class="flex items-center justify-between gap-3 text-xs">
                        <span data-bulk-title class="font-medium text-gray-900">Antrean #{{ $batch->id }} &bull; {{ $batch->total_messages }} pesan &bull; {{ $batch->include_credentials ? 'Undangan + akun Zoom' : 'Undangan meeting' }}</span>
                        <i class="fa-solid fa-chevron-down text-gray-500 shrink-0 transition-transform group-open:rotate-180" aria-hidden="true"></i>
                    </span>
                    <span class="block text-xs text-gray-500 mt-1">{{ $batch->created_at->format('d/m/Y H:i') }}</span>
                </summary>
                <div data-bulk-content></div>
            </details>
        @empty
            <p id="bulkHistoryEmpty" class="p-4 text-xs text-gray-500">Belum ada antrean bulk.</p>
        @endforelse
    </div>
</section>

<template id="bulkProgressTemplate">
    <div class="p-4 border-t border-gray-200 space-y-3">
        <p data-bulk-summary class="text-xs text-gray-600" aria-live="polite">Memuat progres antrean...</p>
        <progress data-bulk-bar class="w-full h-2 accent-emerald-700" max="1" value="0" aria-label="Progres antrean bulk"></progress>
        <p data-bulk-error class="hidden text-xs text-red-700" role="alert"></p>
        <div data-bulk-messages class="divide-y divide-gray-200 border border-gray-200 max-h-80 overflow-y-auto"></div>
        <div class="flex items-center justify-between gap-3 text-xs">
            <button type="button" data-bulk-previous class="px-3 py-2 border border-gray-300 disabled:opacity-40" disabled>Sebelumnya</button>
            <span data-bulk-page></span>
            <button type="button" data-bulk-next class="px-3 py-2 border border-gray-300 disabled:opacity-40" disabled>Berikutnya</button>
        </div>
        <p class="text-[11px] text-gray-500">“Diterima gateway” adalah respons provider, bukan konfirmasi bahwa pesan sudah dibaca. Pengiriman tetap berjalan saat antrean diminimalkan.</p>
    </div>
</template>

@push('scripts')
<script>
(() => {
    const history = document.getElementById('bulkHistoryList');
    const states = new Map();
    function stopPolling(state) {
        clearTimeout(state.pollTimer);
        state.request?.abort();
    }
    function initializeBatch(details) {
        const content = details.querySelector('[data-bulk-content]');
        content.append(document.getElementById('bulkProgressTemplate').content.cloneNode(true));
        const state = { page: 1, lastPage: 1, pollTimer: null, request: null };
        states.set(details, state);
        details.addEventListener('toggle', () => {
            if (details.open) loadProgress(details, state); else stopPolling(state);
        });
        details.querySelector('[data-bulk-previous]').addEventListener('click', () => {
            if (state.page > 1) { state.page--; loadProgress(details, state); }
        });
        details.querySelector('[data-bulk-next]').addEventListener('click', () => {
            if (state.page < state.lastPage) { state.page++; loadProgress(details, state); }
        });
    }
    history.querySelectorAll('details[data-bulk-status-url]').forEach(initializeBatch);
    const focusedBatchId = {{ \Illuminate\Support\Js::from($focusedBatchId) }};
    const focusedBatch = focusedBatchId ? history.querySelector(`[data-bulk-batch-id="${focusedBatchId}"]`) : null;
    if (focusedBatch) {
        focusedBatch.open = true;
        focusedBatch.querySelector('summary').focus();
    }
    async function loadProgress(details, state) {
        stopPolling(state);
        if (!details.open || !details.isConnected) return;
        const controller = new AbortController();
        state.request = controller;
        const timeout = setTimeout(() => controller.abort('timeout'), 20000);
        const find = selector => details.querySelector(selector);
        const statusError = find('[data-bulk-error]');
        try {
            const url = new URL(details.dataset.bulkStatusUrl, window.location.href);
            url.searchParams.set('page', state.page);
            const response = await fetch(url, { signal: controller.signal, headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Progres belum dapat dimuat. Akan dicoba kembali.');
            const data = await response.json();
            if (controller.signal.aborted) return;
            statusError.classList.add('hidden');
            find('[data-bulk-title]').textContent = `Antrean #${data.id} • ${data.total} pesan • ${data.include_credentials ? 'Undangan + akun Zoom' : 'Undangan meeting'} • Jeda ${data.interval_seconds} detik`;
            find('[data-bulk-summary]').textContent = `${data.accepted} diterima gateway · ${data.failed} gagal/perlu diperiksa · ${data.pending} menunggu/diproses · Total ${data.total}`;
            const bar = find('[data-bulk-bar]');
            bar.max = Math.max(1, data.total);
            bar.value = data.total - data.pending;
            const container = find('[data-bulk-messages]');
            container.replaceChildren();
            data.messages.forEach(item => {
                const row = document.createElement('div');
                row.className = 'p-3 text-xs space-y-1';
                const title = document.createElement('p');
                title.className = 'font-medium text-gray-900';
                title.textContent = item.topic;
                const status = document.createElement('p');
                status.className = ['failed', 'unknown'].includes(item.status) ? 'text-red-700' : 'text-gray-600';
                status.textContent = `${item.recipient} · ${item.status_label}`;
                row.append(title, status);
                if (item.error) { const reason = document.createElement('p'); reason.className = 'text-gray-500'; reason.textContent = item.error; row.append(reason); }
                container.append(row);
            });
            state.lastPage = data.last_page;
            find('[data-bulk-page]').textContent = `Halaman ${data.page} / ${data.last_page}`;
            find('[data-bulk-previous]').disabled = state.page <= 1;
            find('[data-bulk-next]').disabled = state.page >= state.lastPage;
            if (data.pending > 0 && details.open) state.pollTimer = setTimeout(() => loadProgress(details, state), 4000);
        } catch (exception) {
            if (controller.signal.aborted && controller.signal.reason !== 'timeout') return;
            statusError.textContent = 'Progres belum dapat dimuat. Akan dicoba kembali.';
            statusError.classList.remove('hidden');
            if (details.open) state.pollTimer = setTimeout(() => loadProgress(details, state), 8000);
        } finally {
            clearTimeout(timeout);
        }
    }
    window.addEventListener('pagehide', () => states.forEach(stopPolling));
})();
</script>
@endpush
