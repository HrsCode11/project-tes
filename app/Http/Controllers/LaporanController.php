<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Iuran;
use App\Models\Pembayaran;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
        $daftarIuran   = Iuran::orderBy('created_at', 'desc')->get();
        $riwayatEkspor = session('riwayat_ekspor', []);

        return view('laporan.index', compact('daftarIuran', 'riwayatEkspor'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'jenis'    => ['required', 'in:bulanan,tahunan,iuran'],
            'format'   => ['required', 'in:pdf,excel'],
            'bulan'    => ['required_if:jenis,bulanan', 'nullable', 'integer', 'min:1', 'max:12'],
            'tahun'    => ['required_if:jenis,bulanan', 'required_if:jenis,tahunan', 'nullable', 'integer', 'min:2020'],
            'iuran_id' => ['required_if:jenis,iuran', 'nullable', 'exists:iuran,id'],
        ]);

        $jenis   = $request->jenis;
        $format  = $request->format;
        $tahun   = $request->filled('tahun') ? (int) $request->tahun : null;
        $bulan   = $request->filled('bulan') ? (int) $request->bulan : null;
        $iuranId = $request->filled('iuran_id') ? (int) $request->iuran_id : null;

        $data     = $this->getData($jenis, $tahun, $bulan, $iuranId);
        $namaFile = $this->getNamaFile($jenis, $tahun, $bulan, $iuranId);

        // Simpan 5 riwayat ekspor terakhir di session
        $riwayat = session('riwayat_ekspor', []);
        array_unshift($riwayat, [
            'nama'   => $namaFile,
            'jenis'  => $jenis,
            'format' => $format,
            'tgl'    => now()->format('d M Y · H:i'),
            'oleh'   => auth()->user()->nama_lengkap,
        ]);
        session(['riwayat_ekspor' => array_slice($riwayat, 0, 5)]);

        if ($format === 'pdf') {
            return $this->exportPdf($jenis, $data, $namaFile, $tahun, $bulan);
        }

        return $this->exportExcel($jenis, $data, $namaFile);
    }

    private function getData(string $jenis, ?int $tahun, ?int $bulan, ?int $iuranId): array
    {
        if ($jenis === 'bulanan') {
            $pembayaran = Pembayaran::with(['warga', 'tagihan.iuran'])
                ->where('status', 'lunas')
                ->whereMonth('tgl_bayar', $bulan)
                ->whereYear('tgl_bayar', $tahun)
                ->get();

            $pemasukan = Pemasukan::whereMonth('tgl_masuk', $bulan)
                ->whereYear('tgl_masuk', $tahun)
                ->get();

            $pengeluaran = Pengeluaran::whereMonth('tgl_keluar', $bulan)
                ->whereYear('tgl_keluar', $tahun)
                ->get();

            $totalPemasukanIuran    = $pembayaran->sum('nominal');
            $totalPemasukanNonIuran = $pemasukan->sum('jumlah');
            $totalPengeluaran       = $pengeluaran->sum('jumlah');
            $totalPemasukan         = $totalPemasukanIuran + $totalPemasukanNonIuran;
            $saldoAkhir             = $totalPemasukan - $totalPengeluaran;

            return compact(
                'pembayaran', 'pemasukan', 'pengeluaran',
                'totalPemasukanIuran', 'totalPemasukanNonIuran',
                'totalPemasukan', 'totalPengeluaran', 'saldoAkhir'
            );
        }

        if ($jenis === 'tahunan') {
            $dataBulanan = [];

            for ($bln = 1; $bln <= 12; $bln++) {
                $pemasukanIuran = Pembayaran::where('status', 'lunas')
                    ->whereMonth('tgl_bayar', $bln)
                    ->whereYear('tgl_bayar', $tahun)
                    ->sum('nominal');

                $pemasukanNonIuran = Pemasukan::whereMonth('tgl_masuk', $bln)
                    ->whereYear('tgl_masuk', $tahun)
                    ->sum('jumlah');

                $pengeluaran = Pengeluaran::whereMonth('tgl_keluar', $bln)
                    ->whereYear('tgl_keluar', $tahun)
                    ->sum('jumlah');

                $dataBulanan[] = [
                    'bulan'              => $bln,
                    'nama_bulan'         => \Carbon\Carbon::create()->month($bln)->translatedFormat('F'),
                    'pemasukan_iuran'    => $pemasukanIuran,
                    'pemasukan_nonIuran' => $pemasukanNonIuran,
                    'total_pemasukan'    => $pemasukanIuran + $pemasukanNonIuran,
                    'pengeluaran'        => $pengeluaran,
                    'saldo'              => ($pemasukanIuran + $pemasukanNonIuran) - $pengeluaran,
                ];
            }

            $totalPemasukan   = collect($dataBulanan)->sum('total_pemasukan');
            $totalPengeluaran = collect($dataBulanan)->sum('pengeluaran');
            $saldoAkhir       = $totalPemasukan - $totalPengeluaran;

            return compact('dataBulanan', 'totalPemasukan', 'totalPengeluaran', 'saldoAkhir');
        }

        // Laporan per iuran
        $iuran   = Iuran::findOrFail($iuranId);
        $tagihan = Tagihan::with(['warga', 'pembayaran'])
            ->where('iuran_id', $iuranId)
            ->orderBy('status')
            ->get();

        $totalLunas     = $tagihan->where('status', 'lunas')->count();
        $totalBelum     = $tagihan->whereIn('status', ['belum_bayar', 'menunggu'])->count();
        $totalTerkumpul = $tagihan->where('status', 'lunas')->sum('nominal');
        $totalTarget    = $tagihan->sum('nominal');

        return compact(
            'iuran', 'tagihan',
            'totalLunas', 'totalBelum',
            'totalTerkumpul', 'totalTarget'
        );
    }

    private function getNamaFile(string $jenis, ?int $tahun, ?int $bulan, ?int $iuranId): string
    {
        if ($jenis === 'bulanan') {
            $namaBulan = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
            return "Laporan-Bulanan-{$namaBulan}-{$tahun}";
        }

        if ($jenis === 'tahunan') {
            return "Laporan-Tahunan-{$tahun}";
        }

        $iuran = Iuran::find($iuranId);
        $nama  = str_replace(' ', '-', $iuran->nama_iuran);

        return "Laporan-Iuran-{$nama}";
    }

    private function exportPdf(string $jenis, array $data, string $namaFile, ?int $tahun, ?int $bulan)
    {
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;

        $pdf = Pdf::loadView("laporan.pdf.{$jenis}", $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download("{$namaFile}.pdf");
    }

    private function exportExcel(string $jenis, array $data, string $namaFile)
    {
        return Excel::download(new LaporanExport($jenis, $data), "{$namaFile}.xlsx");
    }
}