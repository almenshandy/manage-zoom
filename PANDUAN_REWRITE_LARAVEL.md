# PANDUAN LENGKAP REWRITE SISTEM: CODEIGNITER 3 KE LARAVEL

**Project:** Sistem Manajemen Booking & Akun Zoom Meeting
**Target Stack:** Laravel 11 / 12 (PHP 8.2+)
**Tanggal Penyusunan Dokumen:** 25 Oktober 2026

---

## DAFTAR ISI

1. [Ringkasan Masalah &amp; Kebutuhan](#1-ringkasan-masalah--kebutuhan)
2. [Pemetaan Modul CI3 ke Laravel](#2-pemetaan-modul-ci3-ke-laravel)
3. [Arsitektur &amp; Spesifikasi Backend](#3-arsitektur--spesifikasi-backend)
   - [A. Struktur Direktori Rekomendasi](#a-struktur-direktori-rekomendasi)
   - [B. Konfigurasi Environment &amp; Zoom](#b-konfigurasi-environment--zoom)
   - [C. Service Layer: ZoomService](#c-service-layer-zoomservice)
   - [D. Pemecahan Controller](#d-pemecahan-controller)
   - [E. Model Eloquent &amp; Relasi Data](#e-model-eloquent--relasi-data)
   - [F. Middleware &amp; Keamanan](#f-middleware--keamanan)
   - [G. Logging &amp; Audit Otomatis (Observer)](#g-logging--audit-otomatis-observer)
4. [Arsitektur &amp; Spesifikasi Frontend](#4-arsitektur--spesifikasi-frontend)
   - [A. Rekomendasi Pendekatan Frontend](#a-rekomendasi-pendekatan-frontend)
   - [B. Komponen UI &amp; Layouting](#b-komponen-ui--layouting)
   - [C. Integrasi Kalender Interaktif](#c-integrasi-kalender-interaktif)
   - [D. Modul Cetak PDF &amp; Export Data](#d-modul-cetak-pdf--export-data)
5. [Roadmap &amp; Langkah Eksekusi Migrasi](#5-roadmap--langkah-eksekusi-migrasi)
6. [Catatan Periksa &amp; Validasi (Checklist Review)](#6-catatan-periksa--validasi-checklist-review)

---

## 1. RINGKASAN MASALAH & KEBUTUHAN

Sistem saat ini berbasis **CodeIgniter 3**:

- File controller utama `Zoom.php` berukuran besar (>34 KB), mencakup banyak tanggung jawab: otentikasi role, CRUD booking, request HTTP ke Zoom API, pembuatan memo, ekspor/cetak, kalender, hingga truncating tabel.
- Menggunakan library `Zoom_SDK.php` dengan client Guzzle manual tanpa caching token OAuth yang terstandarisasi.
- Tampilan views CI3 tersebar dengan potongan HTML parsial (`header.php`, `footer.php`, `topbar.php`, `sidebar.php`).

**Tujuan Rewrite ke Laravel:**

- Meningkatkan keamanan (_CSRF protection_, _password hashing standard Argon2id/Bcrypt_, _Form Request validation_).
- Memisahkan logika bisnis (business logic) dari controller ke **Service Layer**.
- Otomasi pencatatan log aktivitas dan status meeting.
- Memberikan UI yang lebih responsif dan mudah dirawat.

---

## 2. PEMETAAN MODUL CI3 KE LARAVEL

| Modul CI3 Lama                        | File Lama (CI3)                                      | Target di Laravel                                                |
| :------------------------------------ | :--------------------------------------------------- | :--------------------------------------------------------------- |
| **Autentikasi & Session**             | `Login.php`, `ModelLogin.php`                        | Laravel Breeze / Fortify (`AuthController`) + Session Middleware |
| **Manajemen Booking**                 | `Zoom.php` (`save_kegiatan`, `submit_booking`, dll.) | `ZoomBookingController.php` + `ZoomBookingService.php`           |
| **API Zoom (OAuth & Create Meeting)** | `libraries/Zoom_SDK.php`                             | `app/Services/ZoomService.php`                                   |
| **Manajemen Akun Zoom**               | `ZoomAccount.php`, `ModelZoom.php`                   | `ZoomAccountController.php` + `ZoomAccount` Model                |
| **Kalender Jadwal**                   | `views/zoom/calendar_meetings.php`, `Zoom.php`       | `CalendarController.php` (Blade + FullCalendar.js JSON endpoint) |
| **Log Aktivitas**                     | `ModelLog.php`, `activity_logs.php`                  | `ActivityLog` Model + Laravel Model Observer                     |
| **Memo & Lembar Persetujuan**         | `create_memo.php`, `memo_history.php`                | `MemoController.php`                                             |
| **Cetak PDF**                         | `views/zoom/print_*.php`, TCPDF/HTML2PDF             | `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf`              |
| **Ekspor / Backup Database**          | `download_table`, `truncate_tables`                  | Artisan Command / `DatabaseBackupController.php`                 |

---

## 3. ARSITEKTUR & SPESIFIKASI BACKEND

### A. Struktur Direktori Rekomendasi

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── ZoomBookingController.php
│   │   ├── ZoomAccountController.php
│   │   ├── CalendarController.php
│   │   ├── MemoController.php
│   │   ├── ActivityLogController.php
│   │   └── UserController.php
│   ├── Middleware/
│   │   ├── EnsureAdmin.php
│   │   └── EnsureBookingOwnerOrAdmin.php
│   └── Requests/
│       ├── StoreBookingRequest.php
│       └── UpdateBookingRequest.php
├── Models/
│   ├── User.php
│   ├── ZoomAccount.php
│   ├── ZoomBooking.php
│   ├── Memo.php
│   └── ActivityLog.php
├── Observers/
│   └── ZoomBookingObserver.php
└── Services/
    └── ZoomService.php
```

---

### B. Konfigurasi Environment & Zoom

Buat file konfigurasi terpusat di `config/zoom.php`:

```php
<?php

return [
    'account_id'    => env('ZOOM_ACCOUNT_ID'),
    'client_id'     => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'base_url'      => env('ZOOM_BASE_URL', 'https://api.zoom.us/v2/'),
    'timezone'      => env('ZOOM_TIMEZONE', 'Asia/Jakarta'),
];
```

Dan daftarkan variabel environment di `.env`:

```dotenv
ZOOM_ACCOUNT_ID=your_zoom_account_id
ZOOM_CLIENT_ID=your_zoom_client_id
ZOOM_CLIENT_SECRET=your_zoom_client_secret
ZOOM_TIMEZONE=Asia/Jakarta
```

---

### C. Service Layer: ZoomService

Service ini menggantikan fungsionalitas `Zoom_SDK.php`. Dilengkapi dengan token caching otomatis (55 menit) agar tidak membuang kuota request autentikasi Zoom.

```php
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
        $accountId    = $accountId ?? config('zoom.account_id');
        $clientId     = $clientId ?? config('zoom.client_id');
        $clientSecret = $clientSecret ?? config('zoom.client_secret');

        $cacheKey = "zoom_token_" . md5($clientId);

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

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}users/me/meetings", [
                'topic'      => $payload['topic'],
                'type'       => 2, // Scheduled Meeting
                'start_time' => $payload['start_time'], // Format ISO-8601: 2026-10-25T10:00:00
                'duration'   => (int) $payload['duration'], // Dalam menit
                'timezone'   => $this->timezone,
                'agenda'     => $payload['agenda'] ?? '',
                'settings'   => [
                    'host_video'        => true,
                    'participant_video' => true,
                    'join_before_host'  => false,
                    'mute_upon_entry'   => true,
                    'waiting_room'      => true,
                    'auto_recording'    => $payload['auto_recording'] ?? 'none',
                ],
            ]);

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
}
```

---

### D. Pemecahan Controller

Contoh implementasi controller `ZoomBookingController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\ZoomBooking;
use App\Models\ZoomAccount;
use App\Services\ZoomService;
use App\Http\Requests\StoreBookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZoomBookingController extends Controller
{
    protected ZoomService $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    public function index()
    {
        $bookings = ZoomBooking::with(['user', 'account'])
            ->latest('start_time')
            ->paginate(15);

        return view('zoom.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $accounts = ZoomAccount::where('status', 'active')->get();
        return view('zoom.bookings.create', compact('accounts'));
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['status']  = 'pending';

        $booking = ZoomBooking::create($validated);

        return redirect()->route('zoom.bookings.show', $booking)
            ->with('success', 'Booking meeting berhasil diajukan.');
    }

    /**
     * Generate Link Meeting via Zoom API (Admin / Pemilik Booking)
     */
    public function generateMeetingLink(ZoomBooking $booking)
    {
        $this->authorize('update', $booking);

        $account = $booking->account;

        // Panggil Zoom API dengan kredensial akun terkait
        $token = $this->zoomService->getAccessToken(
            $account->account_id,
            $account->client_id,
            $account->client_secret
        );

        $zoomResponse = $this->zoomService->createMeeting([
            'topic'      => $booking->topic,
            'start_time' => $booking->start_time->toIso8601String(),
            'duration'   => $booking->duration,
            'agenda'     => $booking->description,
        ], $token);

        $booking->update([
            'zoom_meeting_id' => $zoomResponse['id'],
            'join_url'        => $zoomResponse['join_url'],
            'start_url'       => $zoomResponse['start_url'],
            'passcode'        => $zoomResponse['password'] ?? null,
            'status'          => 'confirmed',
        ]);

        return back()->with('success', 'Link Zoom Meeting berhasil dibuat dan aktif.');
    }
}
```

---

### E. Model Eloquent & Relasi Data

#### 1. Model `ZoomBooking`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZoomBooking extends Model
{
    protected $fillable = [
        'user_id',
        'zoom_account_id',
        'topic',
        'description',
        'start_time',
        'duration',
        'zoom_meeting_id',
        'join_url',
        'start_url',
        'passcode',
        'status', // pending, confirmed, completed, cancelled
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'duration'   => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ZoomAccount::class, 'zoom_account_id');
    }
}
```

#### 2. Model `ZoomAccount`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZoomAccount extends Model
{
    protected $fillable = [
        'name',
        'email',
        'account_id',
        'client_id',
        'client_secret',
        'status', // active, inactive
        'capacity',
    ];

    protected $hidden = [
        'client_secret',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(ZoomBooking::class);
    }
}
```

---

### F. Middleware & Keamanan

Gantikan pengecekan role manual CI3:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 1) {
            abort(403, 'Akses ditolak! Anda tidak memiliki akses ke fitur ini.');
        }

        return $next($request);
    }
}
```

---

### G. Logging & Audit Otomatis (Observer)

Gantikan pemanggilan manual `ModelLog` di berbagai baris CI3 dengan **Observer**:

```php
<?php

namespace App\Observers;

use App\Models\ZoomBooking;
use App\Models\ActivityLog;

class ZoomBookingObserver
{
    public function created(ZoomBooking $booking): void
    {
        ActivityLog::create([
            'user_id'     => auth()->id() ?? $booking->user_id,
            'action'      => 'create_booking',
            'description' => "Pengajuan booking baru: '{$booking->topic}' (ID: {$booking->id})",
            'ip_address'  => request()->ip(),
        ]);
    }

    public function updated(ZoomBooking $booking): void
    {
        if ($booking->wasChanged('status')) {
            ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'update_status',
                'description' => "Status booking '{$booking->topic}' diubah menjadi {$booking->status}",
                'ip_address'  => request()->ip(),
            ]);
        }
    }

    public function deleted(ZoomBooking $booking): void
    {
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'delete_booking',
            'description' => "Menghapus booking '{$booking->topic}' (ID: {$booking->id})",
            'ip_address'  => request()->ip(),
        ]);
    }
}
```

---

## 4. ARSITEKTUR & SPESIFIKASI FRONTEND

### A. Rekomendasi Pendekatan Frontend

- **Blade Components + Tailwind CSS / Bootstrap 5 + Alpine.js**:
  - Pilihan paling ideal dan cepat untuk migrasi dari CI3 tanpa mengubah alur kerja tim.
  - Kompatibel penuh dengan Vite bundler bawaan Laravel.

### B. Komponen UI & Layouting

Gantikan pemotongan file CI3 (`views/layouts/*`) menjadi satu arsitektur master layout Blade:

```html
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Sistem Manajemen Zoom' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) @stack('styles')
  </head>
  <body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex">
      <!-- Sidebar Navigation -->
      <x-sidebar />

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col">
        <x-navbar />

        <main class="p-6">
          @if (session('success'))
          <div
            class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4"
          >
            {{ session('success') }}
          </div>
          @endif {{ $slot }}
        </main>

        <x-footer />
      </div>
    </div>
    @stack('scripts')
  </body>
</html>
```

---

### C. Integrasi Kalender Interaktif

Gantikan logika rumit `calendar_prev` dan `calendar_next` CI3 dengan FullCalendar.js:

1. **Backend Controller Endpoint (JSON Feed):**

```php
public function events(Request $request)
{
    $start = $request->query('start');
    $end   = $request->query('end');

    $meetings = ZoomBooking::with('account')
        ->whereBetween('start_time', [$start, $end])
        ->where('status', '!=', 'cancelled')
        ->get();

    return response()->json($meetings->map(function ($item) {
        return [
            'id'    => $item->id,
            'title' => "({$item->account->name}) {$item->topic}",
            'start' => $item->start_time->toIso8601String(),
            'end'   => $item->start_time->copy()->addMinutes($item->duration)->toIso8601String(),
            'url'   => route('zoom.bookings.show', $item->id),
            'color' => $item->status === 'confirmed' ? '#10B981' : '#F59E0B',
        ];
    }));
}
```

2. **Frontend FullCalendar Initialization (Blade):**

```html
<div id="calendar" class="bg-white p-4 rounded-lg shadow"></div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      locale: "id",
      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay",
      },
      events: '{{ route("zoom.calendar.events") }}',
      eventClick: function (info) {
        if (info.event.url) {
          window.location.href = info.event.url;
          info.jsEvent.preventDefault();
        }
      },
    });
    calendar.render();
  });
</script>
@endpush
```

---

### D. Modul Cetak PDF & Export Data

Gantikan library TCPDF CI3 lama dengan `barryvdh/laravel-dompdf`:

```php
use Barryvdh\DomPDF\Facade\Pdf;

public function printMemo(Memo $memo)
{
    $pdf = Pdf::loadView('zoom.pdf.memo', compact('memo'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream("memo-kegiatan-{$memo->id}.pdf");
}
```

---

## 5. ROADMAP & LANGKAH EKSEKUSI MIGRASI

```text
[Minggu 1]
 ├─ Install Laravel 11/12
 ├─ Setup .env dan Database Connection
 ├─ Konversi Database CI3 ke File Migration Laravel
 └─ Setup Autentikasi (Breeze/Fortify) & Seeder Data User

[Minggu 2]
 ├─ Implementasi ZoomService (OAuth S2S & Meeting API)
 ├─ Controller & View Manajemen Akun Zoom
 ├─ Controller & View Pengajuan Booking Meeting
 └─ Pengecekan Bentrok Jadwal Antar Akun

[Minggu 3]
 ├─ Migrasi Fitur Kalender (FullCalendar Feed)
 ├─ Migrasi Fitur Memo & Cetak PDF (DomPDF)
 ├─ Pasang Model Observer untuk Audit Log Otomatis
 └─ Halaman Activity Logs & Filter Data

[Minggu 4]
 ├─ Data Migration (Transfer data lama dari DB CI3 ke Laravel)
 ├─ Pengujian UAT (User Acceptance Testing)
 └─ Cutover Server (Arahkan Virtual Host Apache/Nginx ke folder public Laravel)
```

---

## 6. CATATAN PERIKSA & VALIDASI (CHECKLIST REVIEW)

Silakan gunakan checklist berikut saat melakukan peninjauan/koreksi:

- [ ] **Kredensial Zoom API:** Pastikan akun Zoom menggunakan tipe aplikasi _Server-to-Server OAuth_ terbaru (karena JWT App Zoom sudah resmi dihentikan oleh Zoom).
- [ ] **Format Password:** Jika ingin memakai data akun user yang lama tanpa reset password, periksa algoritma enkripsi password lama di CI3 (apakah bcrypt atau md5).
- [ ] **Role & Permission:** Pastikan pembagian role (Admin vs User Unit) sudah terpetakan dengan tepat.
- [ ] **Penyimpanan File Lampiran:** Pastikan folder upload memo atau bukti kegiatan menggunakan disk `storage/app/public` Laravel.
- [ ] **Waktu & Timezone:** Pastikan `config/app.php` diset ke `timezone => 'Asia/Jakarta'`.
