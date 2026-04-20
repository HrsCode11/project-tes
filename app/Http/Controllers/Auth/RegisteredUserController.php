<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nik'          => ['required', 'string', 'size:16', 'unique:users'],
            'email'        => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'no_rt'        => ['required', 'string', 'max:10'],
            'password'     => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'nik'          => $request->nik,
            'email'        => $request->email,
            'no_rt'        => $request->no_rt,
            'password'     => Hash::make($request->password),
            'role'         => 'warga',
            'status'       => 'aktif',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('warga.dashboard');
    }
}