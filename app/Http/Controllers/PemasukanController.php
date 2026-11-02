<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PemasukanController extends Controller
{

    private function redirectByRole(): string
{
    return auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara';
}

    public function index()
{
    $pemasukan = Pemasukan::orderBy('tgl_masuk', 'desc')->paginate(10);

    $totalNonIuranBulanIni = Pemasukan::whereMonth('tgl_masuk', now()->month)
        ->whereYear('tgl_masuk', now()->year)
        ->sum('jumlah');

    $totalIuranBulanIni = \App\Models\Pembayaran::where('status', 'lunas')
        ->whereMonth('tgl_bayar', now()->month)
        ->whereYear('tgl_bayar', now()->year)
        ->sum('nominal');

    $totalBulanIni = $totalNonIuranBulanIni + $totalIuranBulanIni;

    return view('pemasukan.index', compact(
        'pemasukan',
        'totalNonIuranBulanIni',
        'totalIuranBulanIni',
        'totalBulanIni'
    ));
}

    public function create()
    {
        return view('pemasukan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => ['required', 'string', 'max:255'],
            'kategori'   => ['required', Rule::in(['donasi', 'sewa', 'subsidi', 'lainnya'])],
            'sumber'     => ['nullable', 'string', 'max:100'],
            'jumlah'     => ['required', 'numeric', 'min:1000'],
            'tgl_masuk'  => ['required', 'date'],
        ]);

        Pemasukan::create([
            'dicatat_oleh' => auth()->id(),
            'keterangan'   => $request->keterangan,
            'kategori'     => $request->kategori,
            'sumber'       => $request->sumber,
            'jumlah'       => $request->jumlah,
            'tgl_masuk'    => $request->tgl_masuk,
        ]);

        $prefix = $this->redirectByRole();
        return redirect()->route("{$prefix}.pemasukan.index")
            ->with('success', 'Pemasukan berhasil dicatat.');
    }

    public function edit(Pemasukan $pemasukan)
    {
        return view('pemasukan.edit', compact('pemasukan'));
    }

    public function update(Request $request, Pemasukan $pemasukan)
    {
        $request->validate([
            'keterangan' => ['required', 'string', 'max:255'],
            'kategori'   => ['required', Rule::in(['donasi', 'sewa', 'subsidi', 'lainnya'])],
            'sumber'     => ['nullable', 'string', 'max:100'],
            'jumlah'     => ['required', 'numeric', 'min:1000'],
            'tgl_masuk'  => ['required', 'date'],
        ]);

        $pemasukan->update($request->all());

        $prefix = $this->redirectByRole();
        return redirect()->route("{$prefix}.pemasukan.index")
            ->with('success', 'Pemasukan berhasil diupdate.');
    }

    public function destroy(Pemasukan $pemasukan)
    {
        $pemasukan->delete();

        $prefix = $this->redirectByRole();
        return redirect()->route("{$prefix}.pemasukan.index")
            ->with('success', 'Pemasukan berhasil dihapus.');

    }
}