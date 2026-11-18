<x-app-layout>
    <x-slot name="title">Buat Iuran Baru</x-slot>
    <x-slot name="subtitle">Iuran akan otomatis ditagihkan ke semua warga aktif</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Form Buat Iuran</div>
            </div>

            {{-- Info Warga --}}
            <div class="mx-4 sm:mx-6 mt-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
            <x-icon name="exclamation-triangle" class="w-4 h-4 inline text-blue-500" /> Setelah disimpan...
                <strong>{{ $jumlahWarga }} warga aktif</strong>.
            </div>

            @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
            <form action="{{ route($prefix . '.iuran.store') }}" method="POST" class="p-4 sm:p-6">
                @csrf

                {{-- Nama Iuran --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Iuran</label>
                    <input type="text" name="nama_iuran" value="{{ old('nama_iuran') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="cth: Dana 17 Agustus, Iuran Ronda Malam" required>
                    @error('nama_iuran')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nominal & Deadline --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal per Warga (Rp)</label>
                        <input type="number" name="nominal" value="{{ old('nominal') }}" min="1000"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="cth: 50000" required>
                        @error('nominal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deadline Pembayaran</label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('deadline')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Keterangan <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="keterangan" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Deskripsi singkat tujuan iuran...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info Total --}}
                <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-700 mb-5">
                <x-icon name="check-circle" class="w-4 h-4 inline text-green-500" /> Tagihan akan dibuat... <strong>{{ $jumlahWarga }} warga aktif</strong> secara otomatis.
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                        Simpan & Buat Tagihan
                    </button>
                    <a href="{{ route($prefix. '.iuran.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

</x-app-layout>