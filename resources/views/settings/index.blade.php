@extends('layouts.app')

@php
    $header = 'Pengaturan Sistem';
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <x-page-header title="Pengaturan Sistem & Integrasi" description="Konfigurasi default Zoom Meeting API dan Multi-Gateway WhatsApp (FlowKirim & Fonnte) untuk notifikasi otomatis." />

    <!-- Tab Navigasi Pengaturan -->
    <div class="flex items-center gap-2 border-b border-gray-200">
        <button type="button" onclick="switchSettingTab('whatsapp')" id="tabBtnWhatsApp"
            class="px-4 py-2.5 text-xs font-semibold border-b-2 border-emerald-600 text-emerald-700 flex items-center gap-2 transition">
            <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
            <span>WhatsApp Gateway (Multi-Provider)</span>
        </button>
        <button type="button" onclick="switchSettingTab('zoom')" id="tabBtnZoom"
            class="px-4 py-2.5 text-xs font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 transition">
            <i class="fa-solid fa-video text-blue-500 text-sm"></i>
            <span>Zoom Meeting API</span>
        </button>
    </div>

    <!-- TAB 1: WhatsApp Gateway Multi-Provider -->
    <div id="tabContentWhatsApp" class="space-y-4">
        <!-- Status Panel Gateway Aktif -->
        <x-card>
            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded border border-emerald-200 bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-lg">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Gateway WhatsApp Aktif:</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-gray-900 text-white font-mono uppercase">
                                {{ $activeProvider }}
                            </span>
                            <span id="gatewayStatusBadge" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold
                                {{ ($statusInfo['success'] ?? false) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                <i class="fa-solid fa-circle text-[6px] mr-1 {{ ($statusInfo['success'] ?? false) ? 'text-emerald-500' : 'text-amber-500' }}"></i>
                                {{ ($statusInfo['success'] ?? false) ? 'Terhubung (Connected)' : 'Periksa Token / Belum Terhubung' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1" id="gatewayStatusDesc">
                            {{ $statusInfo['message'] ?? 'Pilih provider default dan masukkan token API untuk mengaktifkan pengiriman otomatis.' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="checkCurrentGatewayConnection()" id="btnCheckStatus"
                        class="px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded text-xs font-medium transition inline-flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-rotate text-[11px]" id="iconCheckStatus"></i>
                        <span>Cek Status</span>
                    </button>
                    <button type="button" onclick="openTestModal()"
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-medium transition inline-flex items-center gap-1.5 shadow-xs">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                        <span>Uji Kirim Pesan</span>
                    </button>
                </div>
            </div>
        </x-card>

        <!-- Form Konfigurasi WhatsApp Gateway -->
        <form method="POST" action="{{ route('settings.update-whatsapp') }}">
            @csrf
            <x-card>
                <div class="p-4 bg-gray-50/70 border-b border-gray-200">
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Pilihan Provider & Pengaturan Umum</h4>
                    <p class="text-[11px] text-gray-500">Tentukan provider default yang digunakan sistem saat staf atau admin menekan tombol kirim pesan.</p>
                </div>

                <div class="divide-y divide-gray-200">
                    <!-- Default Provider Switcher -->
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-blue-50/40">
                        <div class="max-w-md">
                            <label class="text-xs font-bold text-gray-900 block">Default Provider Gateway WhatsApp</label>
                            <span class="text-[11px] text-gray-500 mt-0.5 block">
                                Pilih provider utama untuk mengirim tautan Zoom. FlowKirim menggunakan standar Bearer Token REST API, Fonnte menggunakan Device Token.
                            </span>
                        </div>
                        <div class="shrink-0 w-full sm:w-64">
                            <select name="wa_default_provider" id="wa_default_provider" onchange="highlightSelectedProvider(this.value)"
                                class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs font-semibold text-gray-800 focus:outline-none focus:border-gray-500">
                                <option value="flowkirim" {{ ($waSettings['wa_default_provider']->value ?? 'flowkirim') === 'flowkirim' ? 'selected' : '' }}>
                                    FlowKirim (flowkirim.com) - Rekomendasi
                                </option>
                                <option value="fonnte" {{ ($waSettings['wa_default_provider']->value ?? '') === 'fonnte' ? 'selected' : '' }}>
                                    Fonnte (fonnte.com)
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Toggle Aktifkan Gateway -->
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/50 transition">
                        <div class="max-w-md">
                            <label class="text-xs font-semibold text-gray-900 block">Aktifkan Pengiriman WhatsApp Otomatis</label>
                            <span class="text-[11px] text-gray-500 mt-0.5 block">Jika nonaktif, tombol kirim akan membuka WhatsApp Web secara manual.</span>
                        </div>
                        <div class="shrink-0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="wa_gateway_enabled" value="1" {{ ($waSettings['wa_gateway_enabled']->value ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Kode Negara Default -->
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/50 transition">
                        <div class="max-w-md">
                            <label class="text-xs font-semibold text-gray-900 block">Kode Negara Standar</label>
                            <span class="text-[11px] text-gray-500 mt-0.5 block">Prefix pengganti angka 0 di awal nomor (default 62 untuk Indonesia).</span>
                        </div>
                        <div class="shrink-0 w-24">
                            <input type="text" name="wa_country_code" value="{{ $waSettings['wa_country_code']->value ?? '62' }}"
                                class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs text-center font-mono focus:outline-none focus:border-gray-500">
                        </div>
                    </div>
                </div>

                <!-- Bagian 1: Kredensial FlowKirim -->
                <div id="section_flowkirim" class="border-t border-gray-200 p-4 space-y-3 bg-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">FLOWKIRIM</span>
                            <span class="text-xs font-bold text-gray-900 uppercase">Konfigurasi FlowKirim API</span>
                        </div>
                        <a href="https://flowkirim.com" target="_blank" class="text-[11px] text-blue-600 hover:underline font-medium inline-flex items-center gap-1">
                            Dashboard FlowKirim <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">FlowKirim API / Bearer Token <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="flowkirim_token" id="flowkirim_token"
                                    value="{{ $waSettings['flowkirim_token']->value ?? '' }}"
                                    placeholder="Tempel token FlowKirim..."
                                    class="w-full pl-3 pr-9 py-1.5 bg-white border border-gray-300 rounded text-xs font-mono focus:outline-none focus:border-gray-500">
                                <button type="button" onclick="toggleTokenVisibility('flowkirim_token')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fa-regular fa-eye text-xs" id="eyeIcon_flowkirim_token"></i>
                                </button>
                            </div>
                            @if(env('FLOWKIRIM_TOKEN'))
                                <span class="text-[10px] text-gray-400 mt-1 block">Fallback .env terdeteksi.</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Device / Session ID <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="flowkirim_session_id" id="flowkirim_session_id"
                                value="{{ $waSettings['flowkirim_session_id']->value ?? '' }}"
                                placeholder="Contoh: flowkirim-xxx atau ID Perangkat"
                                class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs font-mono focus:outline-none focus:border-gray-500">
                            <span class="text-[10px] text-gray-400 mt-1 block">Lihat ID perangkat di menu Perangkat FlowKirim.</span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Send Endpoint URL</label>
                            <input type="text" name="flowkirim_endpoint" value="{{ $waSettings['flowkirim_endpoint']->value ?? 'https://scan.flowkirim.com/api/whatsapp/messages/text' }}"
                                class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs font-mono focus:outline-none focus:border-gray-500">
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Kredensial Fonnte -->
                <div id="section_fonnte" class="border-t border-gray-200 p-4 space-y-3 bg-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">FONNTE</span>
                            <span class="text-xs font-bold text-gray-900 uppercase">Konfigurasi Fonnte API</span>
                        </div>
                        <a href="https://md.fonnte.com" target="_blank" class="text-[11px] text-emerald-600 hover:underline font-medium inline-flex items-center gap-1">
                            Dashboard Fonnte <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Fonnte Device Token <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="fonnte_token" id="fonnte_token"
                                    value="{{ $waSettings['fonnte_token']->value ?? '' }}"
                                    placeholder="Tempel token Fonnte..."
                                    class="w-full pl-3 pr-9 py-1.5 bg-white border border-gray-300 rounded text-xs font-mono focus:outline-none focus:border-gray-500">
                                <button type="button" onclick="toggleTokenVisibility('fonnte_token')" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fa-regular fa-eye text-xs" id="eyeIcon_fonnte_token"></i>
                                </button>
                            </div>
                            @if(env('FONNTE_TOKEN'))
                                <span class="text-[10px] text-gray-400 mt-1 block">Fallback .env terdeteksi.</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Send Endpoint URL</label>
                            <input type="text" name="fonnte_endpoint" value="{{ $waSettings['fonnte_endpoint']->value ?? 'https://api.fonnte.com/send' }}"
                                class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded text-xs font-mono focus:outline-none focus:border-gray-500">
                        </div>
                    </div>
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-gray-500">Pilihan gateway yang aktif langsung digunakan saat staf mengirim undangan meeting.</span>
                        <x-btn variant="primary" type="submit">
                            <i class="fa-solid fa-floppy-disk text-[10px]"></i> Simpan Pengaturan Gateway
                        </x-btn>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    </div>

    <!-- TAB 2: Zoom Meeting API (Default Config) -->
    <div id="tabContentZoom" class="space-y-4 hidden">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf

            <x-card>
                <div class="p-4 bg-gray-50/70 border-b border-gray-200">
                    <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Konfigurasi Default Zoom Meeting API</h4>
                    <p class="text-[11px] text-gray-500">Atur parameter bawaan ruang meeting Zoom (dapat ditimpa per sesi saat generate link).</p>
                </div>

                <div class="divide-y divide-gray-200">
                    @foreach($zoomSettings as $setting)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50/50 transition">
                            <div class="max-w-lg">
                                <label for="{{ $setting->key }}" class="text-xs font-semibold text-gray-900 block cursor-pointer">
                                    {{ $setting->label }}
                                </label>
                                <span class="text-[11px] text-gray-500 mt-0.5 block leading-relaxed">
                                    {{ $setting->description }}
                                </span>
                            </div>

                            <div class="shrink-0">
                                @if($setting->type === 'boolean')
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="{{ $setting->key }}" id="{{ $setting->key }}" value="1" {{ $setting->value == '1' ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-gray-900"></div>
                                    </label>
                                @elseif($setting->key === 'zoom_auto_recording')
                                    <select name="{{ $setting->key }}" class="px-2.5 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500 font-medium text-gray-800">
                                        <option value="none" {{ $setting->value === 'none' ? 'selected' : '' }}>None (Tidak Merekam)</option>
                                        <option value="cloud" {{ $setting->value === 'cloud' ? 'selected' : '' }}>Cloud Recording (Otomatis ke Cloud Zoom)</option>
                                        <option value="local" {{ $setting->value === 'local' ? 'selected' : '' }}>Local Recording (Komputer Host)</option>
                                    </select>
                                @elseif($setting->key === 'zoom_jbh_time')
                                    <select name="{{ $setting->key }}" class="px-2.5 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500 font-medium text-gray-800">
                                        <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Kapan Saja (Anytime)</option>
                                        <option value="5" {{ $setting->value == '5' ? 'selected' : '' }}>5 Menit Sebelum Mulai</option>
                                        <option value="10" {{ $setting->value == '10' ? 'selected' : '' }}>10 Menit Sebelum Mulai</option>
                                        <option value="15" {{ $setting->value == '15' ? 'selected' : '' }}>15 Menit Sebelum Mulai</option>
                                    </select>
                                @else
                                    <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="px-2.5 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] text-gray-500">Nilai tersimpan langsung di database dan dicache untuk performa optimal.</span>
                        <x-btn variant="primary" type="submit">
                            <i class="fa-solid fa-floppy-disk text-[10px]"></i> Simpan Konfigurasi Zoom
                        </x-btn>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    </div>
</div>

<!-- Modal Uji Coba Kirim WhatsApp -->
<div id="waTestModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded border border-gray-300 max-w-md w-full shadow-lg overflow-hidden animate-fadeIn">
        <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Uji Kirim WhatsApp Gateway</h4>
                    <p class="text-[11px] text-gray-500">Test kirim pesan notifikasi ke nomor WhatsApp Anda</p>
                </div>
            </div>
            <button type="button" onclick="closeTestModal()" class="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="formTestWhatsApp" onsubmit="submitTestWhatsApp(event)" class="p-5 space-y-3.5 text-xs">
            <div>
                <label class="block font-medium text-gray-700 mb-1">Gunakan Gateway Provider</label>
                <select id="testProviderSelect" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded font-medium text-xs focus:outline-none focus:border-gray-500">
                    <option value="flowkirim">FlowKirim (flowkirim.com)</option>
                    <option value="fonnte">Fonnte (fonnte.com)</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Nomor WhatsApp Penerima <span class="text-red-500">*</span></label>
                <input type="text" id="testTargetNumber" required
                    placeholder="Contoh: 08123456789 atau 628123456789"
                    value="{{ auth()->user()->no_hp ?? '' }}"
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono text-xs">
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Isi Pesan Uji Coba</label>
                <textarea id="testMessageText" rows="3"
                    class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded text-xs text-gray-700 focus:outline-none focus:border-gray-500">Halo! Ini adalah pesan pengujian gateway WhatsApp dari Sistem Manajemen Zoom. Koneksi berhasil!</textarea>
            </div>

            <div id="testResultBox" class="hidden p-3 rounded text-xs border"></div>

            <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeTestModal()" class="px-3.5 py-1.5 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50 font-medium transition">
                    Tutup
                </button>
                <button type="submit" id="btnSubmitTest" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-medium transition inline-flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function switchSettingTab(tab) {
        const tabBtnWA = document.getElementById('tabBtnWhatsApp');
        const tabBtnZoom = document.getElementById('tabBtnZoom');
        const tabContentWA = document.getElementById('tabContentWhatsApp');
        const tabContentZoom = document.getElementById('tabContentZoom');

        if (tab === 'whatsapp') {
            tabBtnWA.className = 'px-4 py-2.5 text-xs font-semibold border-b-2 border-emerald-600 text-emerald-700 flex items-center gap-2 transition';
            tabBtnZoom.className = 'px-4 py-2.5 text-xs font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 transition';
            tabContentWA.classList.remove('hidden');
            tabContentZoom.classList.add('hidden');
        } else {
            tabBtnZoom.className = 'px-4 py-2.5 text-xs font-semibold border-b-2 border-gray-900 text-gray-900 flex items-center gap-2 transition';
            tabBtnWA.className = 'px-4 py-2.5 text-xs font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-900 flex items-center gap-2 transition';
            tabContentZoom.classList.remove('hidden');
            tabContentWA.classList.add('hidden');
        }
    }

    function toggleTokenVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById('eyeIcon_' + inputId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.className = 'fa-regular fa-eye-slash text-xs text-gray-700';
        } else {
            input.type = 'password';
            if (icon) icon.className = 'fa-regular fa-eye text-xs text-gray-400';
        }
    }

    function checkCurrentGatewayConnection() {
        const providerSelect = document.getElementById('wa_default_provider');
        const provider = providerSelect ? providerSelect.value : 'flowkirim';
        const tokenInput = document.getElementById(provider + '_token');
        const btn = document.getElementById('btnCheckStatus');
        const icon = document.getElementById('iconCheckStatus');
        const badge = document.getElementById('gatewayStatusBadge');
        const desc = document.getElementById('gatewayStatusDesc');

        if (btn) btn.disabled = true;
        if (icon) icon.classList.add('fa-spin');

        fetch("{{ route('settings.whatsapp-status') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                provider: provider,
                token: tokenInput ? tokenInput.value : ''
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200';
                badge.innerHTML = '<i class="fa-solid fa-circle text-[6px] mr-1 text-emerald-500"></i> Terhubung (Connected)';
                desc.innerHTML = `<span class="text-emerald-700 font-medium">${data.message}</span>`;
            } else {
                badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200';
                badge.innerHTML = '<i class="fa-solid fa-circle text-[6px] mr-1 text-rose-500"></i> Terputus / Tidak Valid';
                desc.innerHTML = `<span class="text-rose-600">${data.message || 'Gagal terhubung ke provider.'}</span>`;
            }
        })
        .catch(err => {
            badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200';
            badge.innerHTML = '<i class="fa-solid fa-circle text-[6px] mr-1 text-rose-500"></i> Error Jaringan';
            desc.innerHTML = `<span class="text-rose-600">Kesalahan koneksi: ${err.message}</span>`;
        })
        .finally(() => {
            if (btn) btn.disabled = false;
            if (icon) icon.classList.remove('fa-spin');
        });
    }

    const testModal = document.getElementById('waTestModal');
    function openTestModal() {
        if (!testModal) return;
        const currentProvider = document.getElementById('wa_default_provider').value;
        document.getElementById('testProviderSelect').value = currentProvider;
        document.getElementById('testResultBox').classList.add('hidden');
        testModal.classList.remove('hidden');
    }

    function closeTestModal() {
        if (!testModal) return;
        testModal.classList.add('hidden');
    }

    function submitTestWhatsApp(e) {
        e.preventDefault();
        const provider = document.getElementById('testProviderSelect').value;
        const target = document.getElementById('testTargetNumber').value;
        const message = document.getElementById('testMessageText').value;
        const btn = document.getElementById('btnSubmitTest');
        const resBox = document.getElementById('testResultBox');

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> Mengirim...';
        resBox.classList.add('hidden');

        fetch("{{ route('settings.whatsapp-test') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ provider, target, message })
        })
        .then(res => res.json())
        .then(data => {
            resBox.classList.remove('hidden');
            if (data.success) {
                resBox.className = 'p-3 rounded text-xs border bg-emerald-50 border-emerald-200 text-emerald-800';
                resBox.innerHTML = `<strong>Berhasil [${provider.toUpperCase()}]:</strong> ${data.message}`;
            } else {
                resBox.className = 'p-3 rounded text-xs border bg-rose-50 border-rose-200 text-rose-800';
                resBox.innerHTML = `<strong>Gagal [${provider.toUpperCase()}]:</strong> ${data.message}`;
            }
        })
        .catch(err => {
            resBox.classList.remove('hidden');
            resBox.className = 'p-3 rounded text-xs border bg-rose-50 border-rose-200 text-rose-800';
            resBox.innerHTML = `<strong>Error Jaringan:</strong> ${err.message}`;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i> <span>Kirim Sekarang</span>';
        });
    }
</script>
@endpush
@endsection
