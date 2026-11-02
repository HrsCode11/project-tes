<x-app-layout>
    <x-slot name="title">Edit Warga</x-slot>
    <x-slot name="subtitle">Ubah data {{ $warga->nama_lengkap }}</x-slot>

    @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Form Edit Warga</div>
            </div>
            <form action="{{ route($prefix . '.warga.update', $warga) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                @include('warga._form', ['warga' => $warga])

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                        Update Warga
                    </button>
                    <a href="{{ route($prefix . '.warga.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-lg transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>