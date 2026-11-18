<x-app-layout>
    @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
    <x-slot name="title">Edit Pengeluaran</x-slot>
    <x-slot name="subtitle">Ubah data pengeluaran</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Form Edit Pengeluaran</div>
            </div>
            <form action="{{ route($prefix . '.pengeluaran.update', $pengeluaran) }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <input type="text" name="keterangan"
                        value="{{ old('keterangan', $pengeluaran->keterangan) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                        <select name="kategori"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="kebersihan" {{ old('kategori', $pengeluaran->kategori) === 'kebersihan'   ? 'selected' : '' }}>Kebersihan</option>
                            <option value="keamanan" {{ old('kategori', $pengeluaran->kategori) === 'keamanan'     ? 'selected' : '' }}>Keamanan</option>
                            <option value="sosial" {{ old('kategori', $pengeluaran->kategori) === 'sosial'       ? 'selected' : '' }}>Sosial</option>
                            <option value="administrasi" {{ old('kategori', $pengeluaran->kategori) === 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                            <option value="lainnya" {{ old('kategori', $pengeluaran->kategori) === 'lainnya'      ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah (Rp)</label>
                        <input type="number" name="jumlah"
                            value="{{ old('jumlah', $pengeluaran->jumlah) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Penerima</label>
                        <input type="text" name="penerima"
                            value="{{ old('penerima', $pengeluaran->penerima) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Keluar</label>
                        <input type="date" name="tgl_keluar"
                            value="{{ old('tgl_keluar', $pengeluaran->tgl_keluar->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                </div>

                {{-- Bukti lama --}}
                @if($pengeluaran->bukti_path)
                <div class="mb-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bukti Saat Ini</label>
                    <a href="{{ Storage::url($pengeluaran->bukti_path) }}" target="_blank"
                        class="text-blue-500 hover:underline text-sm inline-flex items-center gap-1.5">
                        <x-icon name="photo" class="w-4 h-4" /> Lihat bukti sekarang
                    </a>
                </div>
                @endif

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Ganti Bukti <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm text-gray-500">
                    <p class="text-gray-400 text-xs mt-1">Kosongkan jika tidak ingin mengganti bukti</p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                        Update Pengeluaran
                    </button>
                    <a href="{{ route($prefix . '.pengeluaran.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>