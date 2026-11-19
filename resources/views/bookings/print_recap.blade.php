<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Agenda Zoom Meeting</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #111827;
            margin: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h2 { margin: 0; font-size: 13pt; text-transform: uppercase; }
        .header p { margin: 2px 0 0 0; font-size: 8pt; color: #4b5563; }
        .recap-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
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
        <p>Laporan Rekapitulasi Pemakaian & Jadwal Pertemuan Daring</p>
    </div>

    <div class="recap-title">
        @if(!empty($isCustomSelection))
            REKAP JADWAL KEGIATAN ZOOM (DATA PILIHAN: {{ $bookings->count() }} KEGIATAN)
        @else
            REKAP JADWAL KEGIATAN ZOOM (PERIODE: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} S/D {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})
        @endif
    </div>

    <table class="grid">
        <thead>
            <tr>
                <th style="width: 25px;" class="center">No</th>
                <th style="width: 155px;">Topik Kegiatan</th>
                <th style="width: 100px;">Pemohon & Unit</th>
                <th style="width: 85px;">Tanggal</th>
                <th style="width: 75px;">Waktu</th>
                <th style="width: 120px;">Akun</th>
                <th style="width: 110px;">Meeting ID & Passcode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $idx => $b)
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $b->topic }}</strong>
                        @if($b->description)
                            <div style="font-size: 7.5pt; color: #4b5563;">{{ \Illuminate\Support\Str::limit($b->description, 50) }}</div>
                        @endif
                    </td>
                    <td>
                        {{ $b->user->name ?? '-' }}
                        <div style="font-size: 7.5pt; color: #6b7280;">{{ $b->user->department_name ?? 'Umum' }}</div>
                    </td>
                    <td>{{ $b->date_range_formatted }}</td>
                    <td>{{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }} WIB</td>
                    <td>
                        @if($b->account)
                            <div><strong>{{ $b->account->email ?? $b->account->name }}</strong></div>
                            @if($b->account->email && $b->account->name && $b->account->name !== $b->account->email)
                                <div style="font-size: 7pt; color: #4b5563;">({{ $b->account->name }})</div>
                            @endif
                            <div style="font-size: 7.5pt; font-family: monospace; color: #1f2937; margin-top: 2px;">
                                Pass: <strong>{{ $b->account->password ?? '-' }}</strong>
                            </div>
                            <div style="font-size: 7pt; color: #1e40af; margin-top: 2px;">
                                Kapasitas: <strong>{{ $b->account->capacity ? $b->account->capacity . ' Pax' : '-' }}</strong>
                            </div>
                        @else
                            <span style="color: #9ca3af; font-style: italic;">Belum Ditugaskan</span>
                        @endif
                    </td>
                    <td style="font-family: monospace;">
                        {{ $b->zoom_meeting_id ?: '-' }}
                        @if($b->passcode)
                            <div style="font-size: 7.5pt; color: #6b7280;">Passcode: {{ $b->passcode }}</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center" style="padding: 20px;">Tidak ada data kegiatan pada rentang tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} WIB &bull; Dicetak dari Sistem Manajemen Zoom
    </div>
</body>
</html>
