<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1E293B; }
        .header { background: #1B2A4A; color: white; padding: 20px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 11px; opacity: .7; }
        .summary { display: flex; gap: 12px; margin-bottom: 20px; }
        .summary-box { flex: 1; border: 1px solid #E2E8F0; border-radius: 6px; padding: 12px; }
        .summary-box .label { font-size: 10px; color: #64748B; margin-bottom: 4px; }
        .summary-box .value { font-size: 14px; font-weight: bold; }
        .green { color: #10B981; } .red { color: #EF4444; } .blue { color: #3B82F6; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1B2A4A; color: white; border: 1px solid #334155; padding: 8px 10px; text-align: left; font-size: 10px; }
        td { border: 1px solid #E2E8F0; padding: 8px 10px; }
        tr:nth-child(even) { background: #F8FAFF; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: bold; }
        .badge-green { background: #D1FAE5; color: #059669; }
        .badge-red { background: #FEE2E2; color: #DC2626; }
        .badge-orange { background: #FEF3C7; color: #D97706; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #94A3B8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN IURAN — {{ strtoupper($iuran->nama_iuran) }}</h1>
        <p>RT 04 · Kelurahan Dukuh · Kecamatan Lembeyan · Kabupaten Magetan</p>
        <p>Nominal: Rp {{ number_format($iuran->nominal, 0, ',', '.') }} · Deadline: {{ $iuran->deadline->format('d M Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Warga</div>
            <div class="value blue">{{ $tagihan->count() }} warga</div>
        </div>
        <div class="summary-box">
            <div class="label">Sudah Lunas</div>
            <div class="value green">{{ $totalLunas }} warga</div>
        </div>
        <div class="summary-box">
            <div class="label">Belum Lunas</div>
            <div class="value red">{{ $totalBelum }} warga</div>
        </div>
        <div class="summary-box">
            <div class="label">Dana Terkumpul</div>
            <div class="value green">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Target</div>
            <div class="value blue">Rp {{ number_format($totalTarget, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Warga</th>
                <th>No. RT</th>
                <th>Nominal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tagihan as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $t->warga->nama_lengkap }}</td>
                <td>{{ $t->warga->no_rt }}</td>
                <td>Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                <td>
                    @if($t->status === 'lunas')
                        <span class="badge badge-green">Lunas</span>
                    @elseif($t->status === 'menunggu')
                        <span class="badge badge-orange">Menunggu</span>
                    @else
                        <span class="badge badge-red">Belum Bayar</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Digenerate otomatis pada {{ now()->translatedFormat('d F Y, H:i') }} oleh {{ auth()->user()->nama_lengkap }}
    </div>
</body>
</html>