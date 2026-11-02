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
        .green { color: #10B981; }
        .red { color: #EF4444; }
        .blue { color: #3B82F6; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #F8FAFF; border: 1px solid #E2E8F0; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; color: #64748B; }
        td { border: 1px solid #E2E8F0; padding: 8px 10px; }
        tr:nth-child(even) { background: #F8FAFF; }
        .section-title { font-size: 13px; font-weight: bold; margin: 16px 0 8px; border-bottom: 2px solid #1B2A4A; padding-bottom: 4px; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #94A3B8; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: bold; }
        .badge-green { background: #D1FAE5; color: #059669; }
        .badge-red { background: #FEE2E2; color: #DC2626; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN BULANAN</h1>
        <p>RT 04 · Kelurahan Dukuh · Kecamatan Lembeyan · Kabupaten Magetan</p>
        <p>Periode: {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</p>
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Pemasukan</div>
            <div class="value green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Pengeluaran</div>
            <div class="value red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Saldo Akhir</div>
            <div class="value blue">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Pemasukan dari Iuran -->
    @if($pembayaran->count() > 0)
    <div class="section-title">Pemasukan dari Iuran</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Warga</th>
                <th>Nama Iuran</th>
                <th>Metode</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembayaran as $p)
            <tr>
                <td>{{ $p->tgl_bayar->format('d/m/Y') }}</td>
                <td>{{ $p->warga->nama_lengkap }}</td>
                <td>{{ $p->tagihan->iuran->nama_iuran }}</td>
                <td>{{ strtoupper($p->metode) }}</td>
                <td class="text-right">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="font-weight:bold;text-align:right">Total</td>
                <td class="text-right" style="font-weight:bold;color:#10B981">
                    Rp {{ number_format($totalPemasukanIuran, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Pemasukan Non-Iuran -->
    @if($pemasukan->count() > 0)
    <div class="section-title">Pemasukan Non-Iuran</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Kategori</th>
                <th>Sumber</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pemasukan as $p)
            <tr>
                <td>{{ $p->tgl_masuk->format('d/m/Y') }}</td>
                <td>{{ $p->keterangan }}</td>
                <td>{{ ucfirst($p->kategori) }}</td>
                <td>{{ $p->sumber ?? '—' }}</td>
                <td class="text-right">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="font-weight:bold;text-align:right">Total</td>
                <td class="text-right" style="font-weight:bold;color:#10B981">
                    Rp {{ number_format($totalPemasukanNonIuran, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Pengeluaran -->
    @if($pengeluaran->count() > 0)
    <div class="section-title">Pengeluaran</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Kategori</th>
                <th>Penerima</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengeluaran as $p)
            <tr>
                <td>{{ $p->tgl_keluar->format('d/m/Y') }}</td>
                <td>{{ $p->keterangan }}</td>
                <td>{{ ucfirst($p->kategori) }}</td>
                <td>{{ $p->penerima ?? '—' }}</td>
                <td class="text-right">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" style="font-weight:bold;text-align:right">Total</td>
                <td class="text-right" style="font-weight:bold;color:#EF4444">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    <div class="footer">
        Digenerate otomatis pada {{ now()->translatedFormat('d F Y, H:i') }} oleh {{ auth()->user()->nama_lengkap }}
    </div>
</body>
</html>