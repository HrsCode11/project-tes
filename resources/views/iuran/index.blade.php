<x-app-layout>
    @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
    <x-slot name="title">Kelola Iuran</x-slot>
    <x-slot name="subtitle">Buat dan kelola iuran insidental RT/RW</x-slot>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <p class="text-gray-500 text-sm">Klik kartu iuran untuk melihat detail tagihan per warga</p>
        </div>
        <a href="{{ route($prefix . '.iuran.create') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
            ＋ Buat Iuran Baru
        </a>
    </div>

    {{-- Grid Kartu Iuran --}}
    <div class="grid grid-cols-3 gap-5 mb-5">
        @foreach($iuran as $item)
        @php
            // Tentukan warna & label berdasarkan status
            $statusColor = match($item->status) {
                'aktif'   => 'bg-orange-100 text-orange-700',
                'selesai' => 'bg-green-100 text-green-700',
                'ditutup' => 'bg-gray-100 text-gray-600',
            };
            $statusLabel = match($item->status) {
                'aktif'   => 'Aktif',
                'selesai' => 'Selesai',
                'ditutup' => 'Ditutup',
            };
            // Hitung progress pembayaran
            $total  = $item->totalWarga();
            $lunas  = $item->jumlahLunas();
            $persen = $item->persentaseLunas();
        @endphp

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition
            {{ $item->status === 'aktif' ? 'border-blue-300' : '' }}">

            {{-- Top --}}
            <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                <x-icon name="receipt" class="w-5 h-5 text-yellow-600" />
            </div>
                <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>

            {{-- Info --}}
            <div class="font-bold text-gray-800 mb-1">{{ $item->nama_iuran }}</div>
            <div class="text-blue-500 font-bold font-mono text-lg mb-3">
                Rp {{ number_format($item->nominal, 0, ',', '.') }}
            </div>
            <div class="text-gray-400 text-xs mb-3 leading-relaxed">
                Dibuat: {{ $item->created_at->format('d M Y') }}<br>
                Deadline: {{ $item->deadline->format('d M Y') }}<br>
                {{ $total }} warga · Target Rp {{ number_format($item->nominal * $total, 0, ',', '.') }}
            </div>

            {{-- Progress Bar --}}
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden mb-1">
                <div class="h-full rounded-full bg-blue-500 transition-all"
                    style="width: {{ $persen }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400 font-mono mb-4">
                <span>{{ $lunas }}/{{ $total }} lunas</span>
                <span>{{ $persen }}%</span>
            </div>

            {{-- Aksi --}}
            <div class="flex gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route($prefix . '.iuran.show', $item) }}"
                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold py-2 rounded-lg text-center transition">
                    <x-icon name="eye" class="w-3.5 h-3.5 inline" /> Lihat Tagihan
                </a>
                <a href="{{ route($prefix . '.iuran.edit', $item) }}"
                    class="px-3 py-2 border border-gray-200 hover:bg-gray-50 rounded-lg text-xs font-semibold text-gray-600 transition">
                    <x-icon name="pencil" class="w-3.5 h-3.5" />
                </a>
                @if($item->pembayaran_count === 0)
                <form action="{{ route($prefix . '.iuran.destroy', $item) }}" method="POST"
                    data-konfirmasi="Hapus iuran {{ $item->nama_iuran }}? Seluruh tagihannya, yang belum pernah dibayar, ikut terhapus.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" title="Hapus iuran"
                        class="px-3 py-2 border border-gray-200 hover:bg-red-50 hover:text-red-500 rounded-lg text-xs font-semibold text-gray-600 transition">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </form>
                @else
                <span title="Sudah ada transaksi pembayaran, tidak dapat dihapus. Ubah statusnya menjadi ditutup."
                    class="px-3 py-2 border border-gray-100 rounded-lg text-gray-300 cursor-not-allowed">
                    <x-icon name="trash" class="w-3.5 h-3.5" />
                </span>
                @endif
            </div>

        </div>
        @endforeach

        {{-- Kartu Tambah Baru --}}
        <a href="{{ route($prefix . '.iuran.create') }}"
            class="border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center gap-2 py-10 min-h-[220px] hover:border-blue-300 hover:bg-blue-50 transition cursor-pointer">
            <x-icon name="plus" class="w-4 h-4" /> Buat Iuran Baru
        </a>
    </div>

    {{-- Pagination --}}
    @if($iuran->hasPages())
    <div class="mt-4">{{ $iuran->links() }}</div>
    @endif

</x-app-layout>
