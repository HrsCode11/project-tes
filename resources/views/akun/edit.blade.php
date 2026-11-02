<x-app-layout>
    <x-slot name="title">Edit Akun</x-slot>
    <x-slot name="subtitle">Ubah email, role, atau status akses</x-slot>

    @php
        $isSelf = $akun->id === auth()->id();
        $nama   = $akun->warga?->nama_lengkap ?? $akun->nama_lengkap;
    @endphp

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Form Edit Akun</div>
            </div>

            <form action="{{ route('ketua.akun.update', $akun) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm flex items-center gap-2">
                    <x-icon name="exclamation-triangle" class="w-4 h-4 flex-shrink-0" />
                    {{ session('error') }}
                </div>
                @endif

                {{-- Warga (hanya tampil, tidak bisa diganti) --}}
                <div class="mb-5 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ strtoupper(substr($nama, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-sm text-gray-800">{{ $nama }}</div>
                        <div class="text-gray-500 text-xs">
                            {{ $akun->warga ? 'NIK ' . $akun->warga->nik . ' · status warga: ' . $akun->warga->status : 'Tidak terhubung ke data warga' }}
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $akun->email) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password baru</label>
                    <input type="password" name="password" autocomplete="new-password"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Kosongkan jika tidak ingin mengubah">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Role & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                        <select name="role" {{ $isSelf ? 'disabled' : '' }}
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                            @foreach(['warga' => 'Warga', 'bendahara' => 'Bendahara', 'ketua_rt' => 'Ketua RT'] as $nilai => $label)
                            <option value="{{ $nilai }}" {{ old('role', $akun->role) === $nilai ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($isSelf)<input type="hidden" name="role" value="{{ $akun->role }}">@endif
                        @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status akun</label>
                        <select name="status" {{ $isSelf ? 'disabled' : '' }}
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500">
                            <option value="aktif"    {{ old('status', $akun->status) === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status', $akun->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @if($isSelf)<input type="hidden" name="status" value="{{ $akun->status }}">@endif
                        @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                @if($isSelf)
                <p class="text-gray-400 text-xs mb-4">Role dan status akunmu sendiri tidak dapat diubah.</p>
                @else
                <div class="mb-4"></div>
                @endif

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                        Update Akun
                    </button>
                    <a href="{{ route('ketua.akun.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>