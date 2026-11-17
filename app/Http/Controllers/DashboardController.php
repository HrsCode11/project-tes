<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Pembayaran;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Tagihan;

class DashboardController extends Controller
{
    public function ketua()
    {
        return view('dashboard.ketua', $this->dataKeuangan());
    }

    public function bendahara()
    {
        return view('dashboard.bendahara', $this->dataKeuangan());
    }

    public function warga()
    {
        $warga = auth()->user()->warga;

        abort_if(!$warga, 403, 'Akun kamu belum terhubung ke data warga. Hubungi pengurus RT.');

        \App\Services\MidtransService::syncAllPending();

        $tagihanBelumLunas = Tagihan::with('iuran')
            ->where('warga_id', $warga->id)
            ->where('status', 'belum_bayar')
            ->whereHas('iuran', fn($q) => $q->where('status', 'aktif'))
            ->get();

        $totalHarusBayar = $tagihanBelumLunas->sum('nominal');

        $riwayatBayar = Pembayaran::with('tagihan.iuran')
            ->where('warga_id', $warga->id)
            ->where('status', 'lunas')
            ->orderBy('tgl_bayar', 'desc')
            ->limit(5)
            ->get();

        $totalSudahBayar = Pembayaran::where('warga_id', $warga->id)
            ->where('status', 'lunas')
            ->sum('nominal');

        return view('dashboard.warga', compact(
            'tagihanBelumLunas',
            'totalHarusBayar',
            'riwayatBayar',
            'totalSudahBayar'
        ));
    }

    // Tagihan belum bayar dari iuran yang masih aktif
    private function tunggakanAktif()
    {
        return Tagihan::where('status', 'belum_bayar')
            ->whereHas('iuran', fn($q) => $q->where('status', 'aktif'));
    }

    // Data dashboard untuk Ketua RT dan Bendahara
    private function dataKeuangan(): array
    {
        \App\Services\MidtransService::syncAllPending();

        // ── Saldo kas: seluruh pemasukan dikurangi seluruh pengeluaran ──
        $totalMasukIuran    = Pembayaran::where('status', 'lunas')->sum('nominal');
        $totalMasukNonIuran = Pemasukan::sum('jumlah');
        $totalKeluar        = Pengeluaran::sum('jumlah');
        $saldoKas = ($totalMasukIuran + $totalMasukNonIuran) - $totalKeluar;

        // ── Pemasukan bulan ini (iuran + non-iuran) ──
        $totalPemasukanBulanIni =
            Pembayaran::where('status', 'lunas')
                ->whereMonth('tgl_bayar', now()->month)
                ->whereYear('tgl_bayar', now()->year)
                ->sum('nominal')
            + Pemasukan::whereMonth('tgl_masuk', now()->month)
                ->whereYear('tgl_masuk', now()->year)
                ->sum('jumlah');

        // ── Pengeluaran bulan ini ──
        $totalPengeluaranBulanIni = Pengeluaran::whereMonth('tgl_keluar', now()->month)
            ->whereYear('tgl_keluar', now()->year)
            ->sum('jumlah');

        // ── Tunggakan ──
        $totalTunggakan     = $this->tunggakanAktif()->sum('nominal');
        $jumlahWargaTunggak = $this->tunggakanAktif()->distinct()->count('warga_id');

        // ── Transaksi terbaru (gabungan pemasukan iuran & pengeluaran) ──
        $pembayaranTerbaru = Pembayaran::with(['warga', 'tagihan.iuran'])
            ->where('status', 'lunas')
            ->orderBy('tgl_bayar', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'nama'    => $p->warga->nama_lengkap,
                'ket'     => $p->tagihan->iuran->nama_iuran,
                'jenis'   => 'masuk',
                'nominal' => $p->nominal,
                'tgl'     => $p->tgl_bayar,
            ]);

        $pengeluaranTerbaru = Pengeluaran::orderBy('tgl_keluar', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'nama'    => 'Operasional RT',
                'ket'     => $p->keterangan,
                'jenis'   => 'keluar',
                'nominal' => $p->jumlah,
                'tgl'     => $p->tgl_keluar,
            ]);

        $transaksiTerbaru = $pembayaranTerbaru
            ->concat($pengeluaranTerbaru)
            ->sortByDesc('tgl')
            ->take(5)
            ->values();

        // ── Warga menunggak ──
        $wargaTunggak = Tagihan::with(['warga', 'iuran'])
            ->where('status', 'belum_bayar')
            ->whereHas('iuran', fn($q) => $q->where('status', 'aktif'))
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // ── Status pembayaran ──
        $lunasDigital = Pembayaran::where('status', 'lunas')
            ->whereIn('metode', ['qris', 'va'])
            ->count();

        $lunasTunai = Pembayaran::where('status', 'lunas')
            ->where('metode', 'tunai')
            ->count();

        $menunggu   = Tagihan::where('status', 'menunggu')->count();
        $belumBayar = $this->tunggakanAktif()->count();

        // ── Data grafik per bulan ──
        $tahunGrafik = (int) request('tahun', now()->year);
        $tahunOptions = range(now()->year, 2024);

        $dataGrafik = [];
        for ($bln = 1; $bln <= 12; $bln++) {
            $pemasukanIuran = Pembayaran::where('status', 'lunas')
                ->whereMonth('tgl_bayar', $bln)
                ->whereYear('tgl_bayar', $tahunGrafik)
                ->sum('nominal');

            $pemasukanNonIuran = Pemasukan::whereMonth('tgl_masuk', $bln)
                ->whereYear('tgl_masuk', $tahunGrafik)
                ->sum('jumlah');

            $pengeluaran = Pengeluaran::whereMonth('tgl_keluar', $bln)
                ->whereYear('tgl_keluar', $tahunGrafik)
                ->sum('jumlah');

            $dataGrafik[] = [
                'bulan'       => \Carbon\Carbon::create()->month($bln)->translatedFormat('M'),
                'pemasukan'   => (int) ($pemasukanIuran + $pemasukanNonIuran),
                'pengeluaran' => (int) $pengeluaran,
            ];
        }

        return compact(
            'saldoKas',
            'totalPemasukanBulanIni',
            'totalPengeluaranBulanIni',
            'totalTunggakan',
            'jumlahWargaTunggak',
            'transaksiTerbaru',
            'wargaTunggak',
            'lunasDigital',
            'lunasTunai',
            'menunggu',
            'belumBayar',
            'dataGrafik',
            'tahunGrafik',
            'tahunOptions'
        );
    }
}