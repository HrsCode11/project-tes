<x-app-layout>
    <x-slot name="title">Edit Iuran</x-slot>
    <x-slot name="subtitle">Ubah data iuran {{ $iuran->nama_iuran }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Form Edit Iuran</div>
            </div>

            @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
            <form action="{{ route($prefix . '.iuran.update', $iuran) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Iuran</label>
                    <input type="text" name="nama_iuran" value="{{ old('nama_iuran', $iuran->nama_iuran) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                        required>
                    @error('nama_iuran')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nominal (Rp)</label>
                        <input type="number" name="nominal" value="{{ old('nominal', $iuran->nominal) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                            required>
                        @error('nominal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deadline</label>
                        <input type="date" name="deadline" value="{{ old('deadline', $iuran->deadline->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
                            required>
                        @error('deadline')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $iuran->keterangan) }}</textarea>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="aktif"   {{ old('status', $iuran->status) === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ old('status', $iuran->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="ditutup" {{ old('status', $iuran->status) === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                        Update Iuran
                    </button>
                    <a href="{{ route('bendahara.iuran.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

</x-app-layout>