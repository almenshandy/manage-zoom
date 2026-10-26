<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Memo Persetujuan Zoom - {{ $memo->memo_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            margin: 25px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 15pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 10pt;
            color: #555;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 10.5pt;
        }
        .meta-table td.label {
            width: 160px;
            font-weight: bold;
            color: #444;
        }
        .box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        .signature {
            margin-top: 40px;
            width: 100%;
        }
        .signature table {
            width: 100%;
        }
        .signature td {
            text-align: center;
            width: 45%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LEMBAR MEMO PERSETUJUAN PEMINJAMAN ZOOM</h2>
        <p>Nomor Dokumen: {{ $memo->memo_number }}</p>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Tanggal Diterbitkan</td>
            <td>: {{ $memo->memo_date ? $memo->memo_date->format('d F Y') : '-' }}</td>
        </tr>
        @if($memo->booking)
            <tr>
                <td class="label">Nama Pemohon</td>
                <td>: {{ $memo->booking->user->name ?? '-' }} ({{ $memo->booking->user->departemen ?? 'Umum' }})</td>
            </tr>
            <tr>
                <td class="label">Topik Kegiatan</td>
                <td>: {{ $memo->booking->topic }}</td>
            </tr>
            <tr>
                <td class="label">Jadwal Pelaksanaan</td>
                <td>: {{ $memo->booking->start_date->format('d F Y') }} (Pukul {{ substr($memo->booking->start_time, 0, 5) }} - {{ substr($memo->booking->end_time, 0, 5) }} WIB)</td>
            </tr>
            @if($memo->booking->account)
                <tr>
                    <td class="label">Akun Zoom Ditugaskan</td>
                    <td>: {{ $memo->booking->account->name }} (Kapasitas: {{ $memo->booking->account->capacity }} Peserta)</td>
                </tr>
            @endif
        @endif
    </table>

    @if($memo->booking && $memo->booking->join_url)
        <div class="box">
            <strong>Detail Akses Meeting:</strong><br>
            Link Rapat: <span style="color: #0b5cff;">{{ $memo->booking->join_url }}</span><br>
            Meeting ID: <strong>{{ $memo->booking->zoom_meeting_id }}</strong> &nbsp;|&nbsp; Passcode: <strong>{{ $memo->booking->passcode ?? '-' }}</strong>
        </div>
    @endif

    <div class="content">
        {!! nl2br(e($memo->content)) !!}
    </div>

    @if($memo->has_signature)
        <div class="signature">
            <table>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <p>Mengetahui & Menyetujui,</p>
                        <p style="font-weight: bold; margin-bottom: 60px;">Pengelola Layanan Zoom Meeting</p>
                        <p style="font-weight: bold; text-decoration: underline;">Administrator Sistem</p>
                        <p style="font-size: 9pt; color: #666;">Unit Pelaksana Teknis Sistem Informasi</p>
                    </td>
                </tr>
            </table>
        </div>
    @endif
</body>
</html>
