<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Agenda Zoom Meeting</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.35;
            color: #111827;
            margin: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h2 { margin: 0; font-size: 13pt; text-transform: uppercase; }
        .header p { margin: 2px 0 0 0; font-size: 8pt; color: #4b5563; }
        .recap-title {
            text-align: center;
            font-size: 10.5pt;
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
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        table.grid th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }
        table.grid td.center { text-align: center; }
        .invitation-box {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            line-height: 1.4;
            color: #1f2937;
        }
        .invitation-box .item-row {
            margin-bottom: 1px;
        }
        .invitation-box .join-section {
            margin-top: 5px;
            padding-top: 4px;
            border-top: 1px dashed #d1d5db;
        }
        .invitation-box .join-url {
            color: #1d4ed8;
            word-break: break-all;
            font-size: 7.5pt;
            font-family: monospace;
            text-decoration: underline;
        }
        .account-box {
            font-size: 7.5pt;
            line-height: 1.35;
        }
        .account-box .email {
            font-weight: bold;
            color: #111827;
        }
        .account-box .pass {
            font-family: monospace;
            color: #1f2937;
            margin-top: 2px;
        }
        .account-box .capacity {
            color: #1e40af;
            margin-top: 2px;
            font-weight: bold;
        }
        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 7.5pt;
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
        @elseif(!empty($startDate) || !empty($endDate))
            REKAP JADWAL KEGIATAN ZOOM (PERIODE: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} S/D {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Sekarang' }})
        @else
            REKAP SEMUA JADWAL KEGIATAN ZOOM
        @endif
    </div>

    <table class="grid">
        <thead>
            <tr>
                <th style="width: 25px;" class="center">No</th>
                <th style="width: 140px;">Pemohon & Unit</th>
                <th>Undangan Zoom Meeting (Detail Jadwal & Link)</th>
                <th style="width: 175px;">Akun Host Zoom</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $idx => $b)
                <tr>
                    <td class="center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $b->user->name ?? '-' }}</strong>
                        <div style="font-size: 7.5pt; color: #4b5563; margin-top: 2px;">
                            {{ $b->user->department_name ?? 'Umum' }}
                        </div>
                        @if($b->project_display_name)
                            <div style="font-size: 7pt; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 2px 4px; margin-top: 4px; border-radius: 2px;">
                                Proyek: {{ $b->project_display_name }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="invitation-box">
                            <div class="item-row">Agenda: <strong>{{ $b->topic }}</strong></div>
                            @if($b->description)
                                <div style="font-size: 7.5pt; color: #4b5563; margin-bottom: 2px;">
                                    Catatan: {{ \Illuminate\Support\Str::limit($b->description, 70) }}
                                </div>
                            @endif
                            <div class="item-row">Hari/Tanggal: <strong>{{ $b->date_range_formatted }}</strong></div>
                            <div class="item-row">Waktu: <strong>{{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }} WIB</strong></div>

                            <div class="join-section">
                                <div style="font-weight: bold; margin-bottom: 2px;">Join Zoom Meeting:</div>
                                @if($b->join_url)
                                    <div class="join-url"><a href="{{ $b->join_url }}" style="color: #1d4ed8; text-decoration: underline;">{{ $b->join_url }}</a></div>
                                @else
                                    <div style="color: #9ca3af; font-style: italic;">Link Zoom belum dibuat / belum tersedia</div>
                                @endif

                                <div style="margin-top: 4px; font-family: monospace; font-size: 7.5pt;">
                                    Meeting ID: <strong>{{ $b->zoom_meeting_id ?: '-' }}</strong> &nbsp;|&nbsp; Passcode: <strong>{{ $b->passcode ?: '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="account-box">
                            @if($b->account)
                                <div class="email">{{ $b->account->email ?? $b->account->name }}</div>
                                @if($b->account->name && $b->account->name !== $b->account->email)
                                    <div style="font-size: 7pt; color: #4b5563;">Nama: {{ $b->account->name }}</div>
                                @endif
                                <div class="pass">Pass: <strong>{{ $b->account->password ?? '-' }}</strong></div>
                                <div class="capacity">Kapasitas: {{ $b->account->capacity ? $b->account->capacity . ' Pax' : '-' }}</div>
                            @else
                                <span style="color: #9ca3af; font-style: italic;">Belum Ditugaskan</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center" style="padding: 20px;">Tidak ada data kegiatan ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis melalui Portal Sistem Zoom pada {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB
    </div>
</body>
</html>
