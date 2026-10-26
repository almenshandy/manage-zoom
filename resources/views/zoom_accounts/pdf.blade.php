<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Inventaris Akun Zoom Meeting</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #111827;
            margin: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h2 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 8pt;
            color: #4b5563;
        }
        .recap-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        .summary-bar {
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        table.grid {
            width: 100%;
            border-collapse: collapse;
        }
        table.grid th, table.grid td {
            border: 1px solid #9ca3af;
            padding: 5px 6px;
            text-align: left;
        }
        table.grid th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }
        table.grid td.center { text-align: center; }
        .badge-active {
            color: #065f46;
            background-color: #d1fae5;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7.5pt;
        }
        .badge-expired {
            color: #991b1b;
            background-color: #fee2e2;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7.5pt;
        }
        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8pt;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>PORTAL SISTEM MANAJEMEN ZOOM</h2>
        <p>Laporan Inventaris & Status Lisensi Akun Zoom Meeting</p>
    </div>

    <div class="recap-title">
        REKAPITULASI STATUS LISENSI & AKUN ZOOM (DIURUTKAN SESUAI MASA BERLAKU)
    </div>

    @php
        $total = $accounts->count();
        $activeCount = $accounts->filter(fn($a) => $a->expired_date && !$a->expired_date->isPast())->count();
        $expiredCount = $total - $activeCount;
    @endphp

    <div class="summary-bar">
        Total Akun: <strong>{{ $total }}</strong> &bull; 
        Aktif / Berlaku: <strong style="color: #059669;">{{ $activeCount }}</strong> &bull; 
        Kedaluwarsa: <strong style="color: #dc2626;">{{ $expiredCount }}</strong>
    </div>

    <table class="grid">
        <thead>
            <tr>
                <th style="width: 25px;" class="center">No</th>
                <th style="width: 160px;">Nama Akun / Ruang</th>
                <th style="width: 170px;">Email Akun</th>
                <th style="width: 60px;" class="center">Kapasitas</th>
                <th style="width: 85px;" class="center">Masa Berlaku</th>
                <th style="width: 80px;" class="center">Status Lisensi</th>
                <th style="width: 80px;" class="center">Konfigurasi S2S</th>
                <th style="width: 55px;" class="center">Total Booking</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $idx => $acc)
                @php
                    $isValid = $acc->expired_date && !$acc->expired_date->isPast();
                @endphp
                <tr style="{{ $isValid ? 'background-color: #f0fdf4;' : 'background-color: #fef2f2;' }}">
                    <td class="center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $acc->name }}</strong></td>
                    <td style="font-family: monospace;">{{ $acc->email }}</td>
                    <td class="center">{{ $acc->capacity }} Pax</td>
                    <td class="center font-mono">
                        {{ $acc->expired_date ? $acc->expired_date->format('d/m/Y') : '-' }}
                    </td>
                    <td class="center">
                        @if($isValid)
                            <span class="badge-active">AKTIF</span>
                        @else
                            <span class="badge-expired">KEDALUWARSA</span>
                        @endif
                    </td>
                    <td class="center">
                        @if($acc->account_id && $acc->client_id)
                            <span style="color: #047857; font-weight: bold; font-size: 8pt;">Terkonfigurasi</span>
                        @else
                            <span style="color: #6b7280; font-size: 8pt;">Default (.env)</span>
                        @endif
                    </td>
                    <td class="center">{{ $acc->bookings_count ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center" style="padding: 15px;">Tidak ada data akun Zoom.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} WIB &bull; Dicetak oleh: {{ auth()->user()->name ?? 'Administrator' }}
    </div>
</body>
</html>
