<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Peminjaman Zoom #ZM-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1f2937;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h2 { margin: 0 0 4px 0; font-size: 14pt; text-transform: uppercase; }
        .header p { margin: 0; font-size: 9pt; color: #4b5563; }
        .title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data td {
            padding: 6px 4px;
            vertical-align: top;
        }
        table.data td.label {
            width: 28%;
            font-weight: bold;
            color: #374151;
        }
        table.data td.colon { width: 3%; }
        .box-zoom {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .box-zoom h4 { margin: 0 0 6px 0; font-size: 10pt; text-transform: uppercase; }
        .signatures {
            width: 100%;
            margin-top: 40px;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .stamp-space { height: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PORTAL SISTEM MANAJEMEN ZOOM</h2>
        <p>Layanan Fasilitas Pertemuan & Konferensi Daring Institusi</p>
    </div>

    <div class="title">LEMBAR BUKTI PEMESANAN ZOOM MEETING</div>

    <table class="data">
        <tr>
            <td class="label">Nomor Booking</td>
            <td class="colon">:</td>
            <td><strong>#ZM-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Pemohon</td>
            <td class="colon">:</td>
            <td>{{ $booking->user->name ?? '-' }} ({{ $booking->user->username ?? '-' }})</td>
        </tr>
        <tr>
            <td class="label">Unit / Departemen</td>
            <td class="colon">:</td>
            <td>{{ $booking->user->departemen ?? 'Umum' }}</td>
        </tr>
        <tr>
            <td class="label">Topik Kegiatan</td>
            <td class="colon">:</td>
            <td><strong>{{ $booking->topic }}</strong></td>
        </tr>
        <tr>
            <td class="label">Tanggal Pelaksanaan</td>
            <td class="colon">:</td>
            <td>{{ $booking->date_range_formatted }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Pelaksanaan</td>
            <td class="colon">:</td>
            <td>Pukul {{ substr($booking->start_time, 0, 5) }} s/d {{ substr($booking->end_time, 0, 5) }} WIB ({{ $booking->duration_minutes }} Menit)</td>
        </tr>
        <tr>
            <td class="label">Akun Zoom Ditugaskan</td>
            <td class="colon">:</td>
            <td><strong>{{ $booking->account->name ?? 'Belum Ditugaskan' }}</strong> (Kapasitas: {{ $booking->account->capacity ?? '-' }} Peserta)</td>
        </tr>
        <tr>
            <td class="label">Status Permohonan</td>
            <td class="colon">:</td>
            <td><strong>{{ $booking->status_label }}</strong></td>
        </tr>
        @if($booking->description)
        <tr>
            <td class="label">Deskripsi / Susunan Acara</td>
            <td class="colon">:</td>
            <td>{{ $booking->description }}</td>
        </tr>
        @endif
    </table>

    @if($booking->zoom_meeting_id && $booking->join_url)
        <div class="box-zoom">
            <h4>Akses Pertemuan Virtual (Zoom Meeting Details)</h4>
            <table style="width: 100%; font-size: 10pt;">
                <tr>
                    <td style="width: 25%; font-weight: bold;">Meeting ID</td>
                    <td style="width: 5%;">:</td>
                    <td style="font-family: monospace; font-size: 11pt; font-weight: bold;">{{ $booking->zoom_meeting_id }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Passcode</td>
                    <td>:</td>
                    <td style="font-family: monospace; font-size: 11pt;">{{ $booking->passcode ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Join Link</td>
                    <td>:</td>
                    <td style="word-break: break-all; color: #1d4ed8;">{{ $booking->join_url }}</td>
                </tr>
            </table>
        </div>
    @endif

    <table class="signatures">
        <tr>
            <td>
                Pemohon Kegiatan,<br>
                <div class="stamp-space"></div>
                <strong>{{ $booking->user->name ?? '-' }}</strong><br>
                <span>{{ $booking->user->jabatan ?? 'Staf Pegawai' }}</span>
            </td>
            <td>
                Administrator Layanan Zoom,<br>
                <div class="stamp-space"></div>
                <strong>Administrator TI</strong><br>
                <span>Pusat Komputer & Jaringan</span>
            </td>
        </tr>
    </table>
</body>
</html>
