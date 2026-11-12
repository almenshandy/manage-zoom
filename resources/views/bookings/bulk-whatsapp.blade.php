<section class="bg-white border border-gray-200" aria-labelledby="bulkHistoryHeading">
    <div class="p-4 border-b border-gray-200">
        <h2 id="bulkHistoryHeading" class="text-sm font-semibold text-gray-900">Antrean Bulk WhatsApp</h2>
        <p class="text-xs text-gray-500 mt-1">Lima antrean terakhir Anda. Pengiriman tetap berjalan saat halaman ditutup.</p>
    </div>
    <div id="bulkHistoryList" class="divide-y divide-gray-200">
        @forelse($bulkBatches as $batch)
            <button type="button" data-bulk-status-url="{{ route('bookings.whatsapp-bulk.show', $batch) }}" class="w-full text-left p-4 text-xs flex flex-wrap items-center justify-between gap-2 hover:bg-gray-50">
                <span>Antrean #{{ $batch->id }} &bull; {{ $batch->total_messages }} pesan &bull; {{ $batch->include_credentials ? 'Undangan + akun Zoom' : 'Undangan meeting' }}</span>
                <span class="text-gray-500">{{ $batch->created_at->format('d/m/Y H:i') }} &rarr;</span>
            </button>
        @empty
            <p id="bulkHistoryEmpty" class="p-4 text-xs text-gray-500">Belum ada antrean bulk.</p>
        @endforelse
    </div>
    <div id="bulkProgress" class="hidden p-4 border-t border-gray-200 space-y-3" tabindex="-1">
        <h3 id="bulkProgressTitle" class="text-sm font-semibold text-gray-900"></h3>
        <p id="bulkProgressSummary" class="text-xs text-gray-600" aria-live="polite"></p>
        <progress id="bulkProgressBar" class="w-full h-2 accent-emerald-700" max="1" value="0" aria-label="Progres antrean bulk"></progress>
        <p id="bulkProgressError" class="hidden text-xs text-red-700" role="alert"></p>
        <div id="bulkProgressMessages" class="divide-y divide-gray-200 border border-gray-200 max-h-80 overflow-y-auto"></div>
        <div class="flex items-center justify-between gap-3 text-xs">
            <button type="button" id="bulkPreviousPage" class="px-3 py-2 border border-gray-300 disabled:opacity-40">Sebelumnya</button>
            <span id="bulkProgressPage"></span>
            <button type="button" id="bulkNextPage" class="px-3 py-2 border border-gray-300 disabled:opacity-40">Berikutnya</button>
        </div>
        <p class="text-[11px] text-gray-500">“Diterima gateway” adalah respons provider, bukan konfirmasi bahwa pesan sudah dibaca. Progres diperbarui otomatis selama pengiriman berjalan.</p>
    </div>
</section>

@push('scripts')
<script>
(() => {
    let activeStatusUrl = '', activePage = 1, lastPage = 1, pollTimer, progressRequest;
    const history = document.getElementById('bulkHistoryList');
    function openProgress(url) {
        activeStatusUrl = url;
        activePage = 1;
        document.getElementById('bulkProgress').classList.remove('hidden');
        document.getElementById('bulkProgress').focus();
        loadProgress();
    }
    history.addEventListener('click', event => {
        const button = event.target.closest('[data-bulk-status-url]');
        if (button) openProgress(button.dataset.bulkStatusUrl);
    });
    document.addEventListener('bulk-whatsapp-queued', event => {
        const data = event.detail;
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.bulkStatusUrl = data.status_url;
        button.className = 'w-full text-left p-4 text-xs hover:bg-gray-50';
        button.textContent = `Antrean baru • ${data.total_messages} pesan → Lihat progres`;
        document.getElementById('bulkHistoryEmpty')?.remove();
        history.prepend(button);
        while (history.children.length > 5) history.lastElementChild.remove();
        openProgress(data.status_url);
    });
    async function loadProgress() {
        clearTimeout(pollTimer);
        progressRequest?.abort();
        const controller = new AbortController();
        progressRequest = controller;
        const timeout = setTimeout(() => controller.abort('timeout'), 20000);
        const statusError = document.getElementById('bulkProgressError');
        try {
            const url = new URL(activeStatusUrl, window.location.href);
            url.searchParams.set('page', activePage);
            const response = await fetch(url, { signal: controller.signal, headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Progres belum dapat dimuat. Akan dicoba kembali.');
            const data = await response.json();
            if (controller.signal.aborted) return;
            statusError.classList.add('hidden');
            document.getElementById('bulkProgressTitle').textContent = `Antrean #${data.id} • ${data.include_credentials ? 'Undangan + akun Zoom' : 'Undangan meeting'} • Jeda ${data.interval_seconds} detik`;
            document.getElementById('bulkProgressSummary').textContent = `${data.accepted} diterima gateway · ${data.failed} gagal/perlu diperiksa · ${data.pending} menunggu/diproses · Total ${data.total}`;
            const bar = document.getElementById('bulkProgressBar');
            bar.max = Math.max(1, data.total);
            bar.value = data.total - data.pending;
            const container = document.getElementById('bulkProgressMessages');
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
            lastPage = data.last_page;
            document.getElementById('bulkProgressPage').textContent = `Halaman ${data.page} / ${data.last_page}`;
            document.getElementById('bulkPreviousPage').disabled = activePage <= 1;
            document.getElementById('bulkNextPage').disabled = activePage >= lastPage;
            if (data.pending > 0) pollTimer = setTimeout(loadProgress, 4000);
        } catch (exception) {
            if (controller.signal.aborted && controller.signal.reason !== 'timeout') return;
            statusError.textContent = 'Progres belum dapat dimuat. Akan dicoba kembali.';
            statusError.classList.remove('hidden');
            pollTimer = setTimeout(loadProgress, 8000);
        } finally {
            clearTimeout(timeout);
        }
    }
    document.getElementById('bulkPreviousPage').addEventListener('click', () => { if (activePage > 1) { activePage--; loadProgress(); } });
    document.getElementById('bulkNextPage').addEventListener('click', () => { if (activePage < lastPage) { activePage++; loadProgress(); } });
    window.addEventListener('pagehide', () => { clearTimeout(pollTimer); progressRequest?.abort(); });
})();
</script>
@endpush
