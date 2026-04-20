<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nik'          => ['required', 'string', 'size:16', 'unique:users'],
            'email'        => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'no_rt'        => ['required', 'string', 'max:10'],
            'password'     => ['required', 'min:8'],
            'role'         => ['required', Rule::in(['ketua_rt', 'bendahara', 'warga'])],
            'status'       => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('ketua.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nik'          => ['required', 'string', 'size:16', Rule::unique('users')->ignore($user->id)],
            'email'        => ['required', 'string', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'no_rt'        => ['required', 'string', 'max:10'],
            'password'     => ['nullable', 'min:8'],
            'role'         => ['required', Rule::in(['ketua_rt', 'bendahara', 'warga'])],
            'status'       => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('ketua.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        // Cegah hapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('ketua.users.index')
                ->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('ketua.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}