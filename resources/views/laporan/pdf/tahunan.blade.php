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
        .text-right { text-align: right; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #94A3B8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN TAHUNAN {{ $tahun }}</h1>
        <p>RT 04 · Kelurahan Dukuh · Kecamatan Lembeyan · Kabupaten Magetan</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Pemasukan {{ $tahun }}</div>
            <div class="value green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Pengeluaran {{ $tahun }}</div>
            <div class="value red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Saldo Akhir {{ $tahun }}</div>
            <div class="value blue">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-right">Pemasukan Iuran</th>
                <th class="text-right">Pemasukan Non-Iuran</th>
                <th class="text-right">Total Pemasukan</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataBulanan as $b)
            <tr>
                <td>{{ $b['nama_bulan'] }}</td>
                <td class="text-right">Rp {{ number_format($b['pemasukan_iuran'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($b['pemasukan_nonIuran'], 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($b['total_pemasukan'], 0, ',', '.') }}</td>
                <td class="text-right" style="color:#EF4444">Rp {{ number_format($b['pengeluaran'], 0, ',', '.') }}</td>
                <td class="text-right" style="font-weight:bold;color:{{ $b['saldo'] >= 0 ? '#10B981' : '#EF4444' }}">
                    Rp {{ number_format($b['saldo'], 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
            <tr style="background:#1B2A4A;color:white;font-weight:bold">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format(collect($dataBulanan)->sum('pemasukan_iuran'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format(collect($dataBulanan)->sum('pemasukan_nonIuran'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Digenerate otomatis pada {{ now()->translatedFormat('d F Y, H:i') }} oleh {{ auth()->user()->nama_lengkap }}
    </div>
</body>
</html>