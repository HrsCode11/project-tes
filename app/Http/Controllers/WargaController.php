<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class WargaController extends Controller
{
    private function prefix(): string
    {
        return auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara';
    }

    private function rules(?Warga $warga = null): array
    {
        return [
            'nik'          => ['required', 'digits:16', Rule::unique('warga', 'nik')->ignore($warga?->id)],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'no_hp'        => ['nullable', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'no_rt'        => ['required', 'string', 'max:10'],
            'alamat'       => ['nullable', 'string', 'max:255'],
            'status'       => ['required', Rule::in(['aktif', 'pindah', 'meninggal'])],
        ];
    }

    private function messages(): array
    {
        return [
            'nik.digits'  => 'NIK harus 16 digit angka.',
            'nik.unique'  => 'NIK ini sudah terdaftar.',
            'no_hp.regex' => 'Format nomor HP tidak valid. Contoh: 081234567890',
        ];
    }

    public function index(Request $request)
    {
        $warga = Warga::with('user')
            ->withCount(['tagihan', 'pembayaran'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $cari = $request->q;
                $query->where(function ($w) use ($cari) {
                    $w->where('nama_lengkap', 'like', "%{$cari}%")
                      ->orWhere('nik', 'like', "%{$cari}%");
                });
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->status))
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        $ringkasan = [
            'total'      => Warga::count(),
            'aktif'      => Warga::aktif()->count(),
            'tanpa_akun' => Warga::doesntHave('user')->count(),
        ];

        return view('warga.index', compact('warga', 'ringkasan'));
    }

    public function create()
    {
        return view('warga.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        Warga::create($validated);

        return redirect()->route($this->prefix() . '.warga.index')
            ->with('success', 'Data warga berhasil ditambahkan.');
    }

    public function edit(Warga $warga)
    {
        return view('warga.edit', compact('warga'));
    }

    public function update(Request $request, Warga $warga)
    {
        $validated = $request->validate($this->rules($warga), $this->messages());

        // Cegah pengurus mengunci dirinya sendiri
        if ($validated['status'] !== 'aktif' && $warga->user?->id === auth()->id()) {
            return back()->withInput()
                ->with('error', 'Kamu tidak bisa menonaktifkan data warga milik akunmu sendiri.');
        }

        $warga->update($validated);

        // Warga yang tidak aktif tidak boleh tetap bisa login
        if ($validated['status'] !== 'aktif' && $warga->user) {
            $warga->user->update(['status' => 'nonaktif']);
        }

        return redirect()->route($this->prefix() . '.warga.index')
            ->with('success', 'Data warga berhasil diperbarui.');
    }

    public function destroy(Warga $warga)
    {
        if ($alasan = $warga->alasanTidakBisaDihapus()) {
            return back()->with('error', $alasan);
        }

        $nama = $warga->nama_lengkap;

        try {
            $warga->delete();
        } catch (QueryException $e) {
            return back()->with('error', 'Data warga tidak dapat dihapus karena masih terkait data lain.');
        }

        return redirect()->route($this->prefix() . '.warga.index')
            ->with('success', "Data {$nama} berhasil dihapus.");
    }
}