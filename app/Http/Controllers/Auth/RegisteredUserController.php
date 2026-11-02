<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Aktivasi akun: warga yang sudah didata pengurus membuat akun login
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nik'      => ['required', 'digits:16'],
            'no_hp'    => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'email'    => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'nik.digits'   => 'NIK harus 16 digit angka.',
            'no_hp.regex'  => 'Format nomor HP tidak valid. Contoh: 081234567890',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
        ]);

        // Cari warga: NIK dan No. HP harus cocok, status aktif, dan belum punya akun
        $warga = Warga::aktif()
            ->where('nik', $request->nik)
            ->where('no_hp', Warga::normalisasiNoHp($request->no_hp))
            ->doesntHave('user')
            ->first();

        // Pesan sengaja dibuat sama untuk semua penyebab kegagalan,
        // agar tidak bisa dipakai untuk menebak NIK yang terdaftar
        if (!$warga) {
            return back()
                ->withInput($request->only('nik', 'no_hp', 'email'))
                ->withErrors([
                    'nik' => 'Data tidak ditemukan atau tidak dapat diaktivasi. '
                           . 'Pastikan NIK dan No. HP sama dengan data yang didaftarkan ke pengurus RT.',
                ]);
        }

        $user = User::create([
            'warga_id' => $warga->id,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'warga',
            'status'   => 'aktif',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('warga.dashboard');
    }
}