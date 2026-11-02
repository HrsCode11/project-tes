<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AkunController extends Controller
{
    private const ROLE = ['ketua_rt', 'bendahara', 'warga'];

    public function index(Request $request)
    {
        $akun = User::with('warga')
            ->withCount(['iuranDibuat', 'pemasukanDicatat', 'pengeluaranDicatat'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $cari = $request->q;
                $query->where(function ($w) use ($cari) {
                    $w->where('email', 'like', "%{$cari}%")
                      ->orWhereHas('warga', fn($x) => $x->where('nama_lengkap', 'like', "%{$cari}%"));
                });
            })
            ->when($request->filled('role'), fn($query) => $query->where('role', $request->role))
            ->orderBy('role') // urutan enum: ketua_rt, bendahara, warga
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('akun.index', compact('akun'));
    }

    public function create()
    {
        // Hanya warga aktif yang belum punya akun
        $wargaTersedia = Warga::aktif()
            ->doesntHave('user')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nik', 'no_hp', 'no_rt']);

        return view('akun.create', compact('wargaTersedia'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warga_id' => [
                'required',
                Rule::exists('warga', 'id')->where('status', 'aktif'),
                'unique:users,warga_id',
            ],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'role'     => ['required', Rule::in(self::ROLE)],
        ], [
            'warga_id.required' => 'Pilih warga terlebih dahulu.',
            'warga_id.exists'   => 'Warga tidak ditemukan atau tidak aktif.',
            'warga_id.unique'   => 'Warga ini sudah punya akun.',
            'email.unique'      => 'Email ini sudah dipakai akun lain.',
        ]);

        $warga = Warga::findOrFail($validated['warga_id']);

        User::create([
            'warga_id' => $warga->id,
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'status'   => 'aktif',
        ]);

        return redirect()->route('ketua.akun.index')
            ->with('success', "Akun untuk {$warga->nama_lengkap} berhasil dibuat.");
    }

    public function edit(User $akun)
    {
        $akun->load('warga');

        return view('akun.edit', compact('akun'));
    }

    public function update(Request $request, User $akun)
    {
        $validated = $request->validate([
            'email'    => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($akun->id)],
            'password' => ['nullable', 'min:8'],
            'role'     => ['required', Rule::in(self::ROLE)],
            'status'   => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'email.unique' => 'Email ini sudah dipakai akun lain.',
        ]);

        // Pengaman: jangan sampai Ketua RT mengunci akunnya sendiri
        if ($akun->id === auth()->id()
            && ($validated['role'] !== $akun->role || $validated['status'] !== 'aktif')) {
            return back()->withInput()
                ->with('error', 'Kamu tidak bisa mengubah role atau menonaktifkan akunmu sendiri.');
        }

        // Akun tidak boleh aktif jika warganya sudah pindah/meninggal
        if ($validated['status'] === 'aktif' && $akun->warga && !$akun->warga->isAktif()) {
            return back()->withInput()
                ->with('error', "Warga ini berstatus {$akun->warga->status}. Ubah status warga di Kelola Warga terlebih dahulu.");
        }

        $data = [
            'email'  => $validated['email'],
            'role'   => $validated['role'],
            'status' => $validated['status'],
        ];

        // Password hanya diganti kalau diisi
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $akun->update($data);

        return redirect()->route('ketua.akun.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $akun)
    {
        if ($akun->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        if ($alasan = $akun->alasanTidakBisaDihapus()) {
            return back()->with('error', $alasan);
        }

        $nama = $akun->nama_lengkap;

        try {
            DB::transaction(function () use ($akun) {
                // Sesi yang masih aktif ikut dihapus agar langsung ter-logout
                DB::table('sessions')->where('user_id', $akun->id)->delete();
                $akun->delete();
            });
        } catch (QueryException $e) {
            return back()->with('error', 'Akun tidak dapat dihapus karena masih terkait data lain.');
        }

        return redirect()->route('ketua.akun.index')
            ->with('success', "Akun {$nama} berhasil dihapus. Data warganya tetap tersimpan di Kelola Warga.");
    }
}