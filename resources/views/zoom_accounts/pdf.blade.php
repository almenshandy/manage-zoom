<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Inventaris Akun Zoom Meeting</title>
    <style>
        @page {
            margin: 25px 25px 40px 25px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #111827;
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
            font-size: 8.5pt;
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
            font-size: 9pt;
        }

        table.grid {
            width: 100%;
            border-collapse: collapse;
        }

        table.grid th,
        table.grid td {
            border: 1px solid #9ca3af;
            padding: 5px 7px;
            text-align: center;
        }

        table.grid th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
        }

        table.grid td.center {
            text-align: center;
        }

        .badge-active {
            color: #065f46;
            background-color: #d1fae5;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8pt;
        }

        .badge-expired {
            color: #991b1b;
            background-color: #fee2e2;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8pt;
        }

        footer {
            position: fixed;
            bottom: -25px;
            left: 0px;
            right: 0px;
            height: 25px;
            /* border-top: 1px solid #e5e7eb; */
            padding-top: 5px;
            font-size: 8pt;
            color: #6b7280;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>PORTAL SISTEM MANAJEMEN ZOOM</h2>
        <p>Laporan Inventaris & Status Lisensi Akun Zoom Meeting</p>
    </div>

    {{-- <div class="recap-title">
        REKAPITULASI STATUS LISENSI AKUN ZOOM
    </div> --}}

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
                <th>No</th>
                <th>Email Akun Zoom</th>
                <th>Kapasitas</th>
                <th>Masa Berlaku</th>
                <th>Status Lisensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $idx => $acc)
                @php
                    $isValid = $acc->expired_date && !$acc->expired_date->isPast();
                @endphp
                <tr style="{{ $isValid ? 'background-color: #f0fdf4;' : 'background-color: #fef2f2;' }}">
                    <td class="center">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace; font-size: 9pt;">
                        <strong>{{ $acc->email }}</strong>
                    </td>
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
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center" style="padding: 15px;">Tidak ada data akun Zoom.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer>
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} WIB &bull; Dicetak oleh:
        {{ auth()->user()->name ?? 'Administrator' }}
    </footer>
</body>

</html>