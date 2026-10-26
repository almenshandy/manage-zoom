@extends('layouts.app')

@php
    $header = 'Pengaturan Default Zoom';
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <x-page-header title="Konfigurasi Default Zoom Meeting API" description="Atur kebijakan default pembuatan ruang meeting (dapat ditimpa per sesi saat generate link)." />

    <x-card>
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf

            <div class="divide-y divide-gray-200">
                @foreach($settings as $setting)
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
                        <i class="fa-solid fa-floppy-disk text-[10px]"></i> Simpan Konfigurasi
                    </x-btn>
                </div>
            </x-slot:footer>
        </form>
    </x-card>
</div>
@endsection
