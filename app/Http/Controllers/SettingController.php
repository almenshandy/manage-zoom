<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Halaman Pengaturan Zoom & Multi WhatsApp Gateway
     */
    public function index(WhatsAppService $waService)
    {
        $zoomSettings = SystemSetting::where('group', 'zoom_meeting')->get();
        $waSettings = SystemSetting::where('group', 'whatsapp_gateway')->get()->keyBy('key');

        $activeProvider = $waService->getActiveProvider();
        $statusInfo = null;
        if (!empty($waService->getToken($activeProvider))) {
            $statusInfo = $waService->checkStatus($activeProvider);
        }

        return view('settings.index', compact('zoomSettings', 'waSettings', 'activeProvider', 'statusInfo'));
    }

    /**
     * Update pengaturan Zoom
     */
    public function update(Request $request)
    {
        $allSettings = SystemSetting::where('group', 'zoom_meeting')->get();

        foreach ($allSettings as $setting) {
            if ($setting->type === 'boolean') {
                $value = $request->has($setting->key) ? '1' : '0';
            } else {
                $value = $request->input($setting->key, $setting->value);
            }

            SystemSetting::set($setting->key, $value);
        }

        return back()->with('success', 'Konfigurasi default Zoom API berhasil diperbarui.');
    }

    /**
     * Update pengaturan WhatsApp Gateway (Multi-provider)
     */
    public function updateWhatsApp(Request $request)
    {
        $waSettings = SystemSetting::where('group', 'whatsapp_gateway')->get();

        foreach ($waSettings as $setting) {
            if ($setting->type === 'boolean') {
                $value = $request->has($setting->key) ? '1' : '0';
            } else {
                $value = $request->input($setting->key, $setting->value);
            }

            SystemSetting::set($setting->key, $value);
        }

        return back()->with('success', 'Pengaturan WhatsApp Gateway berhasil diperbarui.');
    }

    /**
     * Cek status koneksi token WhatsApp Gateway via AJAX
     */
    public function checkWhatsAppStatus(Request $request, WhatsAppService $waService)
    {
        $provider = $request->input('provider') ?: $waService->getActiveProvider();
        $token = $request->input('token');

        $result = $waService->checkStatus($provider, $token);
        return response()->json($result);
    }

    /**
     * Uji coba kirim pesan WhatsApp
     */
    public function testWhatsApp(Request $request, WhatsAppService $waService)
    {
        $request->validate([
            'target'   => 'required|string',
            'provider' => 'nullable|string',
            'message'  => 'nullable|string',
        ]);

        $target = $request->input('target');
        $provider = $request->input('provider') ?: $waService->getActiveProvider();
        $message = $request->input('message') ?: "Halo! Ini adalah pesan tes pengiriman WhatsApp dari Sistem Manajemen Zoom menggunakan gateway: " . strtoupper($provider);

        $result = $waService->sendMessage($target, $message, $provider);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
