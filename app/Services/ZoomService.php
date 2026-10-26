<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class ZoomService
{
    protected string $baseUrl;
    protected string $timezone;

    public function __construct()
    {
        $this->baseUrl  = config('zoom.base_url', 'https://api.zoom.us/v2/');
        $this->timezone = config('zoom.timezone', 'Asia/Jakarta');
    }

    /**
     * Mendapatkan OAuth Access Token via Server-to-Server Credentials
     */
    public function getAccessToken(?string $accountId = null, ?string $clientId = null, ?string $clientSecret = null): ?string
    {
        $accountId    = $accountId ?: config('zoom.account_id');
        $clientId     = $clientId ?: config('zoom.client_id');
        $clientSecret = $clientSecret ?: config('zoom.client_secret');

        if (!$accountId || !$clientId || !$clientSecret) {
            throw new Exception('Kredensial Zoom API (Account ID, Client ID, Client Secret) belum dikonfigurasi.');
        }

        $cacheKey = 'zoom_token_' . md5($clientId);

        return Cache::remember($cacheKey, now()->addMinutes(55), function () use ($accountId, $clientId, $clientSecret) {
            $response = Http::asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ]);

            if ($response->successful()) {
                return $response->json()['access_token'] ?? null;
            }

            Log::error('Zoom Auth Failed', ['response' => $response->body()]);
            throw new Exception('Gagal mendapatkan token autentikasi Zoom: ' . $response->body());
        });
    }

    /**
     * Buat Meeting Baru di Akun Zoom Terkait
     */
    public function createMeeting(array $payload, ?string $token = null): array
    {
        $token = $token ?? $this->getAccessToken();

        // Ambil default setting dari Database (SystemSetting)
        $defaultSettings = \App\Models\SystemSetting::getZoomMeetingSettings();

        // Timpa jika ada custom setting yang dikirim dari form/payload
        $finalSettings = array_merge($defaultSettings, $payload['settings'] ?? []);

        $body = [
            'topic'      => $payload['topic'],
            'type'       => 2, // Scheduled Meeting
            'start_time' => $payload['start_time'], // Format ISO-8601: 2026-10-25T10:00:00
            'duration'   => (int) ($payload['duration'] ?? 60), // Dalam menit
            'timezone'   => $this->timezone,
            'agenda'     => $payload['agenda'] ?? '',
            'settings'   => $finalSettings,
        ];

        if (!empty($payload['password'])) {
            $body['password'] = $payload['password'];
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}users/me/meetings", $body);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Zoom Create Meeting Error', ['body' => $response->body()]);
        throw new Exception('Gagal membuat jadwal meeting Zoom: ' . $response->body());
    }

    /**
     * Hapus Meeting di Zoom API
     */
    public function deleteMeeting(string $meetingId, ?string $token = null): bool
    {
        $token = $token ?? $this->getAccessToken();
        $response = Http::withToken($token)->delete("{$this->baseUrl}meetings/{$meetingId}");
        return $response->successful();
    }

    /**
     * Cek Koneksi / Validasi Kredensial Zoom API
     */
    public function testConnection(?string $accountId = null, ?string $clientId = null, ?string $clientSecret = null): array
    {
        $accountId    = $accountId ?: config('zoom.account_id');
        $clientId     = $clientId ?: config('zoom.client_id');
        $clientSecret = $clientSecret ?: config('zoom.client_secret');

        if (!$accountId || !$clientId || !$clientSecret) {
            return [
                'success' => false,
                'message' => 'Kredensial API tidak lengkap (Account ID, Client ID, atau Client Secret kosong).',
            ];
        }

        try {
            // Coba request token langsung tanpa cache untuk memastikan kredensial valid
            $authResponse = Http::timeout(8)
                ->asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ]);

            if (!$authResponse->successful()) {
                $err = $authResponse->json();
                $reason = $err['reason'] ?? $err['error'] ?? $authResponse->body();
                return [
                    'success' => false,
                    'message' => 'Otentikasi OAuth Gagal: ' . $reason,
                ];
            }

            $token = $authResponse->json()['access_token'] ?? null;
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Token OAuth kosong dari server Zoom.',
                ];
            }

            // Panggil endpoint /users/me untuk memverifikasi profil akun
            $profileResponse = Http::timeout(8)
                ->withToken($token)
                ->get("{$this->baseUrl}users/me");

            if ($profileResponse->successful()) {
                $profile = $profileResponse->json();
                return [
                    'success' => true,
                    'message' => 'Koneksi Berhasil!',
                    'data'    => [
                        'email'     => $profile['email'] ?? '-',
                        'type'      => match($profile['type'] ?? 1) { 2 => 'Licensed / Pro', 3 => 'On-Prem', default => 'Basic' },
                        'pmi'       => $profile['pmi'] ?? '-',
                        'dept'      => $profile['dept'] ?? '-',
                    ],
                ];
            }

            return [
                'success' => true,
                'message' => 'OAuth Berhasil, namun info profil terbatas.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Koneksi error: ' . $e->getMessage(),
            ];
        }
    }
}
