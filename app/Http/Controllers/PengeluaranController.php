<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class PengeluaranController extends Controller
{

    private function redirectByRole(): string
    {
        return auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara';
    }

    public function index()
    {
        $pengeluaran = Pengeluaran::orderBy('tgl_keluar', 'desc')->paginate(10);

        $totalBulanIni = Pengeluaran::whereMonth('tgl_keluar', now()->month)
            ->whereYear('tgl_keluar', now()->year)
            ->sum('jumlah');

        return view('pengeluaran.index', compact('pengeluaran', 'totalBulanIni'));
    }

    public function create()
    {
        return view('pengeluaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => ['required', 'string', 'max:255'],
            'kategori'   => ['required', Rule::in(['kebersihan', 'keamanan', 'sosial', 'administrasi', 'lainnya'])],
            'penerima'   => ['nullable', 'string', 'max:100'],
            'jumlah'     => ['required', 'numeric', 'min:1000'],
            'tgl_keluar' => ['required', 'date'],
            // Bukti opsional, max 2MB, hanya gambar/pdf
            'bukti'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $buktiPath = null;

        // Jika ada file bukti yang diupload, simpan ke storage
        if ($request->hasFile('bukti')) {
            // Simpan ke storage/app/public/bukti-pengeluaran/
            // Storage::url() bisa dipakai untuk akses dari browser
            $buktiPath = $request->file('bukti')->store('bukti-pengeluaran', 'public');
        }

        Pengeluaran::create([
            'dicatat_oleh' => auth()->id(),
            'keterangan'   => $request->keterangan,
            'kategori'     => $request->kategori,
            'penerima'     => $request->penerima,
            'jumlah'       => $request->jumlah,
            'tgl_keluar'   => $request->tgl_keluar,
            'bukti_path'   => $buktiPath,
        ]);

        $prefix = $this->redirectByRole();
        return redirect()->route("{$prefix}.pengeluaran.index")
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function edit(Pengeluaran $pengeluaran)
    {
        return view('pengeluaran.edit', compact('pengeluaran'));
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $request->validate([
            'keterangan' => ['required', 'string', 'max:255'],
            'kategori'   => ['required', Rule::in(['kebersihan', 'keamanan', 'sosial', 'administrasi', 'lainnya'])],
            'penerima'   => ['nullable', 'string', 'max:100'],
            'jumlah'     => ['required', 'numeric', 'min:1000'],
            'tgl_keluar' => ['required', 'date'],
            'bukti'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $buktiPath = $pengeluaran->bukti_path;

        if ($request->hasFile('bukti')) {
            // Hapus file lama jika ada
            if ($buktiPath) {
                Storage::disk('public')->delete($buktiPath);
            }
            $buktiPath = $request->file('bukti')->store('bukti-pengeluaran', 'public');
        }

        $pengeluaran->update([
            'keterangan' => $request->keterangan,
            'kategori'   => $request->kategori,
            'penerima'   => $request->penerima,
            'jumlah'     => $request->jumlah,
            'tgl_keluar' => $request->tgl_keluar,
            'bukti_path' => $buktiPath,
        ]);

        $prefix = $this->redirectByRole();
        return redirect()->route("{$prefix}.pengeluaran.index")
            ->with('success', 'Pengeluaran berhasil diupdate.');

    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        // Hapus file bukti jika ada
        if ($pengeluaran->bukti_path) {
            Storage::disk('public')->delete($pengeluaran->bukti_path);
        }

        $pengeluaran->delete();

        $prefix = $this->redirectByRole();
        return redirect()->route("{$prefix}.pengeluaran.index")
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}