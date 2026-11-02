<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Dapatkan nama provider aktif ('flowkirim' atau 'fonnte')
     */
    public function getActiveProvider(): string
    {
        $provider = SystemSetting::get('wa_default_provider', 'flowkirim');
        return in_array($provider, ['flowkirim', 'fonnte']) ? $provider : 'flowkirim';
    }

    /**
     * Cek apakah gateway WhatsApp otomatis diaktifkan dan memiliki token
     */
    public function isEnabled(?string $provider = null): bool
    {
        $gatewayEnabled = (bool) SystemSetting::get('wa_gateway_enabled', '1');
        if (!$gatewayEnabled) {
            return false;
        }

        $active = $provider ?: $this->getActiveProvider();
        return !empty($this->getToken($active));
    }

    /**
     * Dapatkan token berdasarkan provider
     */
    public function getToken(string $provider): ?string
    {
        if ($provider === 'flowkirim') {
            $dbToken = SystemSetting::get('flowkirim_token');
            if (!empty($dbToken)) return trim($dbToken);
            $envToken = env('FLOWKIRIM_TOKEN');
            return !empty($envToken) ? trim($envToken) : null;
        }

        if ($provider === 'fonnte') {
            $dbToken = SystemSetting::get('fonnte_token');
            if (!empty($dbToken)) return trim($dbToken);
            $envToken = env('FONNTE_TOKEN');
            return !empty($envToken) ? trim($envToken) : null;
        }

        return null;
    }

    /**
     * Format nomor HP tujuan ke format standar internasional (misal: 628xxx)
     */
    public function formatTargetNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        $defaultCountry = SystemSetting::get('wa_country_code', '62') ?: '62';

        if (str_starts_with($clean, '0')) {
            $clean = $defaultCountry . substr($clean, 1);
        }

        return $clean;
    }

    /**
     * Kirim pesan teks WhatsApp menggunakan provider aktif (atau provider spesifik)
     */
    public function sendMessage(string|array $targets, string $message, ?string $provider = null): array
    {
        $selectedProvider = $provider ?: $this->getActiveProvider();

        if ($selectedProvider === 'flowkirim') {
            return $this->sendViaFlowKirim($targets, $message);
        }

        if ($selectedProvider === 'fonnte') {
            return $this->sendViaFonnte($targets, $message);
        }

        return [
            'success'  => false,
            'provider' => $selectedProvider,
            'message'  => "Provider WhatsApp '{$selectedProvider}' tidak didukung.",
        ];
    }

    /**
     * Dapatkan Session ID aktif FlowKirim secara otomatis jika yang diinput adalah Device ID
     */
    public function resolveFlowKirimSessionId(string $token, string $inputSessionOrDevice): string
    {
        $cleanInput = trim($inputSessionOrDevice);
        if (empty($cleanInput)) {
            return '';
        }

        // Coba minta session aktif dari endpoint /api/whatsapp/sessions/{deviceId}
        try {
            $res = Http::withToken($token)
                ->acceptJson()
                ->timeout(10)
                ->get("https://scan.flowkirim.com/api/whatsapp/sessions/{$cleanInput}");

            $data = $res->json();
            if ($res->successful() && isset($data['data']['session_id'])) {
                $actualSessionId = $data['data']['session_id'];
                // Update ke database cache agar pemanggilan selanjutnya semakin cepat
                SystemSetting::set('flowkirim_session_id', $actualSessionId);
                return $actualSessionId;
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal auto-resolve session FlowKirim: " . $e->getMessage());
        }

        return $cleanInput;
    }

    /**
     * Pengiriman via FlowKirim API (https://scan.flowkirim.com/api/whatsapp/messages/text)
     */
    public function sendViaFlowKirim(string|array $targets, string $message, ?string $customToken = null): array
    {
        $token = $customToken ?: $this->getToken('flowkirim');
        $endpoint = SystemSetting::get('flowkirim_endpoint', 'https://scan.flowkirim.com/api/whatsapp/messages/text') ?: 'https://scan.flowkirim.com/api/whatsapp/messages/text';
        $rawSession = trim((string) SystemSetting::get('flowkirim_session_id', ''));

        if (empty($token)) {
            return [
                'success'  => false,
                'provider' => 'flowkirim',
                'message'  => 'API Token FlowKirim belum dikonfigurasi. Silakan isi di Pengaturan Sistem.',
            ];
        }

        // Auto-resolve session ID jika user memasukkan device ID
        $sessionId = $this->resolveFlowKirimSessionId($token, $rawSession);

        // Siapkan target
        $targetList = is_array($targets) ? $targets : explode(',', $targets);
        $cleanTargets = array_values(array_filter(array_map(fn($t) => $this->formatTargetNumber($t), $targetList)));

        if (empty($cleanTargets)) {
            return [
                'success'  => false,
                'provider' => 'flowkirim',
                'message'  => 'Nomor tujuan WhatsApp tidak valid atau kosong.',
            ];
        }

        $lastResponse = null;
        $successCount = 0;
        $errors = [];

        foreach ($cleanTargets as $recipient) {
            // Standar JID FlowKirim: <nomor>@s.whatsapp.net
            $toJid = str_contains($recipient, '@') ? $recipient : "{$recipient}@s.whatsapp.net";

            $payload = [
                'to'      => $toJid,
                'message' => $message,
            ];

            if (!empty($sessionId)) {
                $payload['session_id'] = $sessionId;
            }

            try {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->timeout(20)
                    ->post($endpoint, $payload);

                $result = $response->json() ?? [];
                $lastResponse = $result;

                $isOk = $response->successful() && (!isset($result['success']) || $result['success'] === true);
                if ($isOk) {
                    $successCount++;
                } else {
                    $msg = $result['message'] ?? '';
                    if (!empty($result['errors'])) {
                        $errDetails = [];
                        foreach ($result['errors'] as $fld => $errs) {
                            $errDetails[] = implode(', ', (array) $errs);
                        }
                        $msg .= ' (' . implode('; ', $errDetails) . ')';
                    }
                    $errors[] = $msg ?: ('Gagal mengirim ke ' . $recipient);
                }
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
                Log::error("FlowKirim Send Exception: " . $e->getMessage());
            }
        }

        if ($successCount > 0) {
            return [
                'success'  => true,
                'provider' => 'flowkirim',
                'message'  => "Berhasil mengirim {$successCount} pesan WhatsApp via FlowKirim.",
                'data'     => $lastResponse,
            ];
        }

        return [
            'success'  => false,
            'provider' => 'flowkirim',
            'message'  => 'FlowKirim: ' . implode('; ', array_unique($errors)),
            'data'     => $lastResponse,
        ];
    }

    /**
     * Pengiriman via Fonnte API (https://api.fonnte.com/send)
     */
    public function sendViaFonnte(string|array $targets, string $message, ?string $customToken = null): array
    {
        $token = $customToken ?: $this->getToken('fonnte');
        $endpoint = SystemSetting::get('fonnte_endpoint', 'https://api.fonnte.com/send') ?: 'https://api.fonnte.com/send';

        if (empty($token)) {
            return [
                'success'  => false,
                'provider' => 'fonnte',
                'message'  => 'Device Token Fonnte belum dikonfigurasi. Silakan isi di Pengaturan Sistem.',
            ];
        }

        $targetList = is_array($targets) ? $targets : explode(',', $targets);
        $cleanTargets = array_values(array_filter(array_map(fn($t) => $this->formatTargetNumber($t), $targetList)));
        $targetString = implode(',', $cleanTargets);

        if (empty($targetString)) {
            return [
                'success'  => false,
                'provider' => 'fonnte',
                'message'  => 'Nomor tujuan WhatsApp tidak valid atau kosong.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(20)->post($endpoint, [
                'target'      => $targetString,
                'message'     => $message,
                'countryCode' => SystemSetting::get('wa_country_code', '62') ?: '62',
            ]);

            $result = $response->json() ?? [];
            $isStatusTrue = isset($result['status']) && ($result['status'] === true || $result['status'] === 'true');

            if ($response->successful() && $isStatusTrue) {
                return [
                    'success'  => true,
                    'provider' => 'fonnte',
                    'message'  => 'Pesan WhatsApp berhasil dikirim via Fonnte.',
                    'data'     => $result,
                ];
            }

            $reason = $result['reason'] ?? ($result['message'] ?? 'Gagal mengirim pesan dari gateway Fonnte.');
            return [
                'success'  => false,
                'provider' => 'fonnte',
                'message'  => 'Fonnte error: ' . $reason,
                'data'     => $result,
            ];
        } catch (\Throwable $e) {
            Log::error('Fonnte Send Exception: ' . $e->getMessage());
            return [
                'success'  => false,
                'provider' => 'fonnte',
                'message'  => 'Terjadi kesalahan koneksi ke server Fonnte: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cek status koneksi token untuk provider
     */
    public function checkStatus(string $provider, ?string $customToken = null): array
    {
        if ($provider === 'flowkirim') {
            return $this->checkFlowKirimStatus($customToken);
        }

        if ($provider === 'fonnte') {
            return $this->checkFonnteStatus($customToken);
        }

        return [
            'success' => false,
            'message' => 'Provider tidak valid.',
        ];
    }

    /**
     * Cek status FlowKirim via /api/me dan cek session device jika ada
     */
    protected function checkFlowKirimStatus(?string $customToken = null): array
    {
        $token = $customToken ?: $this->getToken('flowkirim');
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token FlowKirim belum diisi.'];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(12)
                ->get('https://scan.flowkirim.com/api/me');

            $data = $response->json();

            if ($response->successful() && isset($data['success']) && $data['success'] === true) {
                $user = $data['data'] ?? [];
                $name = $user['name'] ?? 'User';
                $email = $user['email'] ?? '';

                $rawDevice = trim((string) SystemSetting::get('flowkirim_session_id', ''));
                $deviceInfo = '';
                if (!empty($rawDevice)) {
                    $resolvedSession = $this->resolveFlowKirimSessionId($token, $rawDevice);
                    $deviceInfo = " &bull; Session: <code class='font-mono text-emerald-800 font-semibold'>{$resolvedSession}</code>";
                }

                return [
                    'success' => true,
                    'message' => "Token FlowKirim Aktif! Akun: {$name} ({$email}){$deviceInfo}",
                    'data'    => $user,
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Token FlowKirim tidak valid atau kedaluwarsa.',
                'data'    => $data,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Koneksi ke FlowKirim gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Cek status Fonnte
     */
    protected function checkFonnteStatus(?string $customToken = null): array
    {
        $token = $customToken ?: $this->getToken('fonnte');
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token Fonnte belum diisi.'];
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])->timeout(12)->post('https://api.fonnte.com/device');
            $data = $response->json();

            if ($response->successful() && isset($data['status']) && $data['status'] === true) {
                return [
                    'success' => true,
                    'message' => 'Device Fonnte terhubung.',
                    'data'    => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $data['reason'] ?? 'Token tidak valid atau device Fonnte terputus.',
                'data'    => $data,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Koneksi ke Fonnte gagal: ' . $e->getMessage()];
        }
    }
}
