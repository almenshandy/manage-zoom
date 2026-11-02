<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Base URL resmi Fonnte API
     */
    protected string $baseUrl = 'https://api.fonnte.com';

    /**
     * Dapatkan token Fonnte dari Database (SystemSetting) dengan fallback ke .env
     */
    public function getToken(): ?string
    {
        $dbToken = SystemSetting::get('fonnte_token');
        if (!empty($dbToken)) {
            return trim($dbToken);
        }

        $envToken = env('FONNTE_TOKEN');
        return !empty($envToken) ? trim($envToken) : null;
    }

    /**
     * Periksa apakah integrasi Fonnte diaktifkan
     */
    public function isEnabled(): bool
    {
        $enabled = SystemSetting::get('fonnte_enabled', '1');
        return (bool) $enabled && !empty($this->getToken());
    }

    /**
     * Format nomor HP tujuan ke format standar internasional (misal 628xxx)
     */
    public function formatTargetNumber(string $phone): string
    {
        // Bersihkan seluruh karakter selain angka
        $clean = preg_replace('/[^0-9]/', '', $phone);

        $defaultCountry = SystemSetting::get('fonnte_country_code', '62') ?: '62';

        if (str_starts_with($clean, '0')) {
            $clean = $defaultCountry . substr($clean, 1);
        }

        return $clean;
    }

    /**
     * Kirim pesan teks WhatsApp melalui endpoint Fonnte /send
     *
     * @param string|array $targets Nomor tujuan tunggal atau multiple (array / koma)
     * @param string $message Isi pesan yang akan dikirim
     * @return array [success => bool, message => string, data => mixed]
     */
    public function sendMessage(string|array $targets, string $message): array
    {
        $token = $this->getToken();

        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token Fonnte belum dikonfigurasi. Harap isi token Fonnte di Pengaturan Sistem atau file .env.',
            ];
        }

        // Siapkan target
        if (is_array($targets)) {
            $formattedList = array_map(fn($t) => $this->formatTargetNumber($t), $targets);
            $targetString = implode(',', array_filter($formattedList));
        } else {
            // Bisa berupa string koma jika multiple
            $parts = explode(',', $targets);
            $formattedList = array_map(fn($t) => $this->formatTargetNumber($t), $parts);
            $targetString = implode(',', array_filter($formattedList));
        }

        if (empty($targetString)) {
            return [
                'success' => false,
                'message' => 'Nomor tujuan WhatsApp tidak valid atau kosong.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(20)->post("{$this->baseUrl}/send", [
                'target'  => $targetString,
                'message' => $message,
                'countryCode' => SystemSetting::get('fonnte_country_code', '62') ?: '62',
            ]);

            $result = $response->json();

            if ($response->successful()) {
                // Fonnte umumnya merespons { status: true, ... } atau jika gagal { status: false, reason: "..." }
                $isStatusTrue = isset($result['status']) && ($result['status'] === true || $result['status'] === 'true');

                if ($isStatusTrue) {
                    return [
                        'success' => true,
                        'message' => 'Pesan WhatsApp berhasil dikirim via Fonnte.',
                        'data'    => $result,
                    ];
                }

                $reason = $result['reason'] ?? ($result['message'] ?? 'Gagal mengirim pesan dari gateway Fonnte.');
                return [
                    'success' => false,
                    'message' => 'Fonnte error: ' . $reason,
                    'data'    => $result,
                ];
            }

            Log::error('Fonnte Send API Error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'HTTP Error ' . $response->status() . ': ' . ($result['reason'] ?? $response->body()),
                'data'    => $result,
            ];

        } catch (\Throwable $e) {
            Log::error('Fonnte Request Exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan koneksi ke server Fonnte: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cek status koneksi perangkat / token ke server Fonnte
     *
     * @param string|null $customToken Jika diisi, cek status token ini
     * @return array [success => bool, message => string, device => mixed]
     */
    public function checkDeviceStatus(?string $customToken = null): array
    {
        $token = $customToken ?: $this->getToken();

        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token Fonnte belum diatur.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(15)->post("{$this->baseUrl}/device");

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                return [
                    'success' => true,
                    'message' => 'Device terhubung dengan Fonnte.',
                    'device'  => $result,
                ];
            }

            return [
                'success' => false,
                'message' => $result['reason'] ?? 'Token tidak valid atau device belum terhubung.',
                'device'  => $result,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Koneksi gagal: ' . $e->getMessage(),
            ];
        }
    }
}
