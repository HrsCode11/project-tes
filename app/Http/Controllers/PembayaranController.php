<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    private function redirectByRole(): string
    {
        return auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara';
    }

    public function index()
    {
        $pembayaran = Pembayaran::with(['tagihan.iuran', 'warga'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pembayaran.index', compact('pembayaran'));
    }

    public function create()
    {
        // Iuran aktif yang deadline-nya sudah lewat otomatis ditutup
        Iuran::where('status', 'aktif')
            ->where('deadline', '<', now()->toDateString())
            ->update(['status' => 'ditutup']);

        // Tagihan yang belum lunas dari iuran yang masih aktif
        $tagihan = Tagihan::with(['warga', 'iuran'])
            ->whereIn('status', ['belum_bayar', 'menunggu'])
            ->whereHas('iuran', function ($q) {
                $q->where('status', 'aktif');
            })
            ->whereDoesntHave('pembayaran', function ($q) {
                $q->where('status', 'lunas');
            })
            ->get();

        return view('pembayaran.create', compact('tagihan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tagihan_id' => ['required', 'exists:tagihan,id'],
            'catatan'    => ['nullable', 'string', 'max:255'],
        ]);

        $tagihan = Tagihan::with('iuran')->findOrFail($request->tagihan_id);

        if ($tagihan->status === 'lunas') {
            return back()->withErrors(['tagihan_id' => 'Tagihan ini sudah lunas.']);
        }

        if ($tagihan->iuran->status !== 'aktif') {
            return back()->withErrors(['tagihan_id' => 'Iuran untuk tagihan ini sudah ditutup.']);
        }

        DB::transaction(function () use ($tagihan, $request) {

            Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'warga_id'   => $tagihan->warga_id,
                'metode'     => 'tunai',
                'nominal'    => $tagihan->nominal,
                'status'     => 'lunas',
                'tgl_bayar'  => now(),
                'catatan'    => $request->catatan,
            ]);

            $tagihan->update(['status' => 'lunas']);

            // Jika semua tagihan iuran ini sudah lunas, iuran otomatis selesai
            $iuran = $tagihan->iuran;
            $adaYangBelumLunas = $iuran->tagihan()
                ->whereIn('status', ['belum_bayar', 'menunggu'])
                ->exists();

            if (!$adaYangBelumLunas) {
                $iuran->update(['status' => 'selesai']);
            }
        });

        $prefix = $this->redirectByRole();

        return redirect()->route("{$prefix}.pembayaran.index")
            ->with('success', 'Pembayaran tunai berhasil dikonfirmasi.');
    }
}