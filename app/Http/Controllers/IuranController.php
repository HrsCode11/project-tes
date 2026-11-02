<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Tagihan;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IuranController extends Controller
{
    private function redirectByRole(): string
    {
        return auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara';
    }

    public function index()
    {
        // Iuran aktif yang deadline-nya sudah lewat otomatis ditutup
        Iuran::where('status', 'aktif')
            ->where('deadline', '<', now()->toDateString())
            ->update(['status' => 'ditutup']);

        $iuran = Iuran::with('tagihan')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('iuran.index', compact('iuran'));
    }

    public function create()
    {
        $jumlahWarga = Warga::aktif()->count();

        return view('iuran.create', compact('jumlahWarga'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_iuran' => ['required', 'string', 'max:100'],
            'nominal'    => ['required', 'numeric', 'min:1000'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'deadline'   => ['required', 'date', 'after:today'],
        ]);

        $validated['dibuat_oleh'] = auth()->id();
        $validated['status']      = 'aktif';

        // Iuran dan seluruh tagihannya dibuat dalam satu transaksi,
        // supaya tidak ada iuran yang tersimpan tanpa tagihan jika terjadi error
        [$iuran, $jumlahTagihan] = DB::transaction(function () use ($validated) {
            $iuran      = Iuran::create($validated);
            $wargaAktif = Warga::aktif()->get();

            foreach ($wargaAktif as $warga) {
                Tagihan::create([
                    'iuran_id' => $iuran->id,
                    'warga_id' => $warga->id,
                    'nominal'  => $iuran->nominal,
                    'status'   => 'belum_bayar',
                ]);
            }

            return [$iuran, $wargaAktif->count()];
        });

        $prefix = $this->redirectByRole();

        return redirect()->route("{$prefix}.iuran.index")
            ->with('success', "Iuran '{$iuran->nama_iuran}' berhasil dibuat. {$jumlahTagihan} tagihan telah digenerate.");
    }

    public function show(Iuran $iuran)
    {
        $tagihan = $iuran->tagihan()
            ->with('warga')
            ->orderBy('status')
            ->paginate(10);

        return view('iuran.show', compact('iuran', 'tagihan'));
    }

    public function edit(Iuran $iuran)
    {
        return view('iuran.edit', compact('iuran'));
    }

    public function update(Request $request, Iuran $iuran)
    {
        $validated = $request->validate([
            'nama_iuran' => ['required', 'string', 'max:100'],
            'nominal'    => ['required', 'numeric', 'min:1000'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'deadline'   => ['required', 'date'],
            'status'     => ['required', Rule::in(['aktif', 'selesai', 'ditutup'])],
        ]);

        $iuran->update($validated);

        $prefix = $this->redirectByRole();

        return redirect()->route("{$prefix}.iuran.index")
            ->with('success', "Iuran '{$iuran->nama_iuran}' berhasil diupdate.");
    }

    public function destroy(Iuran $iuran)
    {
        $nama = $iuran->nama_iuran;
        $iuran->delete();

        $prefix = $this->redirectByRole();

        return redirect()->route("{$prefix}.iuran.index")
            ->with('success', "Iuran '{$nama}' berhasil dihapus beserta semua tagihannya.");
    }
}