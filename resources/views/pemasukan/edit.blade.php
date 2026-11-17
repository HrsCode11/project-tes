<x-app-layout>
    @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
    <x-slot name="title">Edit Pemasukan</x-slot>
    <x-slot name="subtitle">Ubah data pemasukan</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Form Edit Pemasukan</div>
            </div>
            <form action="{{ route($prefix . '.pemasukan.update', $pemasukan) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan', $pemasukan->keterangan) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                        required>
                    @error('keterangan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                        <select name="kategori"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="donasi"  {{ old('kategori', $pemasukan->kategori) === 'donasi'  ? 'selected' : '' }}>Donasi</option>
                            <option value="sewa"    {{ old('kategori', $pemasukan->kategori) === 'sewa'    ? 'selected' : '' }}>Sewa Fasilitas</option>
                            <option value="subsidi" {{ old('kategori', $pemasukan->kategori) === 'subsidi' ? 'selected' : '' }}>Subsidi</option>
                            <option value="lainnya" {{ old('kategori', $pemasukan->kategori) === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" value="{{ old('jumlah', $pemasukan->jumlah) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sumber</label>
                        <input type="text" name="sumber" value="{{ old('sumber', $pemasukan->sumber) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Masuk</label>
                        <input type="date" name="tgl_masuk"
                            value="{{ old('tgl_masuk', $pemasukan->tgl_masuk->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                        Update Pemasukan
                    </button>
                    <a href="{{ route($prefix . '.pemasukan.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>