<x-app-layout>
    @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
    <x-slot name="title">Kelola Pemasukan</x-slot>
    <x-slot name="subtitle">Rekap pemasukan kas RT/RW bulan ini</x-slot>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-5 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-3.5 sm:p-5">
        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-50 rounded-xl flex items-center justify-center mb-2 sm:mb-3">
            <x-icon name="arrow-down-tray" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" />
        </div>
            <div class="text-base sm:text-2xl font-bold font-mono text-green-600">
                Rp {{ number_format($totalBulanIni, 0, ',', '.') }}
            </div>
            <div class="text-gray-500 text-xs sm:text-sm mt-1">Total Pemasukan Bulan Ini</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3.5 sm:p-5">
        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-50 rounded-xl flex items-center justify-center mb-2 sm:mb-3">
            <x-icon name="receipt" class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" />
        </div>            
        <div class="text-base sm:text-2xl font-bold font-mono text-blue-600">
                Rp {{ number_format($totalIuranBulanIni, 0, ',', '.') }}
            </div>
            <div class="text-gray-500 text-xs sm:text-sm mt-1">Dari Iuran Warga</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3.5 sm:p-5 col-span-2 sm:col-span-1">
        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-50 rounded-xl flex items-center justify-center mb-2 sm:mb-3">
            <x-icon name="gift" class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" />
        </div>            
            <div class="text-base sm:text-2xl font-bold font-mono text-purple-600">
                Rp {{ number_format($totalNonIuranBulanIni, 0, ',', '.') }}
            </div>
            <div class="text-gray-500 text-xs sm:text-sm mt-1">Donasi, Sewa, Subsidi, dll.</div>
        </div>
    </div>

    {{-- Tabel Pemasukan Non-Iuran --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 sm:px-5 py-4 border-b border-gray-100">
            <div>
                <div class="font-bold text-gray-800">Pemasukan Non-Iuran</div>
                <div class="text-gray-400 text-xs mt-0.5">
                    Donasi, sewa fasilitas, subsidi, dll. · Total {{ $pemasukan->total() }} data
                </div>
            </div>
            <a href="{{ route($prefix . '.pemasukan.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition self-start sm:self-auto">
                ＋ Tambah Pemasukan
            </a>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full min-w-[600px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pemasukan as $item)
                @php
                    $katColor = match($item->kategori) {
                        'donasi'  => 'bg-purple-100 text-purple-700',
                        'sewa'    => 'bg-blue-100 text-blue-700',
                        'subsidi' => 'bg-green-100 text-green-700',
                        default   => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 text-sm text-gray-500">
                        {{ $item->tgl_masuk->format('d M Y') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-semibold text-sm">{{ $item->keterangan }}</div>
                        @if($item->sumber)
                        <div class="text-gray-400 text-xs">dari: {{ $item->sumber }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $katColor }}">
                            {{ ucfirst($item->kategori) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 font-mono text-sm font-bold text-green-600">
                        +Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                        <a href="{{ route($prefix . '.pemasukan.edit', $item) }}"
                                class="text-blue-500 hover:underline text-sm font-semibold">Edit</a>
                            <form action="{{ route($prefix . '.pemasukan.destroy', $item) }}" method="POST"
                                data-konfirmasi="Yakin hapus pemasukan '{{ $item->keterangan }}' ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:underline text-sm font-semibold">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-gray-400">
                        Belum ada pemasukan non-iuran tercatat
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($pemasukan->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $pemasukan->links() }}</div>
        @endif
    </div>

</x-app-layout>