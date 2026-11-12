{{-- Nama --}}
<div class="mb-4">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $warga?->nama_lengkap) }}"
        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Nama sesuai KTP" required>
    @error('nama_lengkap')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

{{-- NIK & RT --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">NIK (16 digit)</label>
        <input type="text" name="nik" value="{{ old('nik', $warga?->nik) }}" maxlength="16" inputmode="numeric"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="16 digit NIK" required>
        @error('nik')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">No. RT</label>
        <input type="text" name="no_rt" value="{{ old('no_rt', $warga?->no_rt ?? '04') }}"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            required>
        @error('no_rt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

{{-- HP --}}
<div class="mb-4">
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        No. HP (WhatsApp) <span class="text-gray-400 font-normal">(opsional)</span>
    </label>
    <input type="tel" name="no_hp" value="{{ old('no_hp', $warga?->no_hp ? '0' . substr($warga->no_hp, 2) : '') }}"
        inputmode="numeric"
        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="081234567890">
    <p class="text-gray-400 text-xs mt-1">Boleh sama dengan anggota keluarga lain.</p>
    @error('no_hp')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

{{-- Alamat --}}
<div class="mb-4">
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Alamat <span class="text-gray-400 font-normal">(opsional)</span>
    </label>
    <input type="text" name="alamat" value="{{ old('alamat', $warga?->alamat) }}"
        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        placeholder="cth: Jl. Mawar No. 7">
    @error('alamat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

{{-- Status --}}
<div class="mb-5">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
    <select name="status"
        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        @foreach(['aktif' => 'Aktif', 'pindah' => 'Pindah', 'meninggal' => 'Meninggal'] as $nilai => $label)
        <option value="{{ $nilai }}" {{ old('status', $warga?->status ?? 'aktif') === $nilai ? 'selected' : '' }}>
            {{ $label }}
        </option>
        @endforeach
    </select>
    <p class="text-gray-400 text-xs mt-1">
        Warga yang tidak aktif tidak ditagih pada iuran baru, dan akun login miliknya (jika ada) ikut dinonaktifkan.
    </p>
    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>