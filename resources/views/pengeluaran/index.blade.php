<x-app-layout>
@php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
    <x-slot name="title">Kelola Pengeluaran</x-slot>
    <x-slot name="subtitle">Catat pengeluaran operasional dan kegiatan RT/RW</x-slot>

    {{-- Stat Card --}}
    <div class="grid grid-cols-3 gap-5 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mb-3">
            <x-icon name="arrow-up-tray" class="w-5 h-5 text-red-600" />
        </div>
        <div class="text-2xl font-bold font-mono text-red-500">
            Rp {{ number_format($totalBulanIni, 0, ',', '.') }}
        </div>
            <div class="text-gray-500 text-sm mt-1">Total Pengeluaran Bulan Ini</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <div class="font-bold text-gray-800">Riwayat Pengeluaran</div>
                <div class="text-gray-400 text-xs mt-0.5">Total {{ $pengeluaran->total() }} pengeluaran</div>
            </div>
            <a href="{{ route($prefix . '.pengeluaran.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
                ＋ Tambah Pengeluaran
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
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bukti</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pengeluaran as $item)
                @php
                    $katColor = match($item->kategori) {
                        'kebersihan'   => 'bg-green-100 text-green-700',
                        'keamanan'     => 'bg-blue-100 text-blue-700',
                        'sosial'       => 'bg-purple-100 text-purple-700',
                        'administrasi' => 'bg-orange-100 text-orange-700',
                        default        => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 text-sm text-gray-500">
                        {{ $item->tgl_keluar->format('d M Y') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-semibold text-sm">{{ $item->keterangan }}</div>
                        @if($item->penerima)
                        <div class="text-gray-400 text-xs">ke: {{ $item->penerima }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $katColor }}">
                            {{ ucfirst($item->kategori) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 font-mono text-sm font-bold text-red-500">
                        −Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4">
                        @if($item->bukti_path)
                        <a href="{{ Storage::url($item->bukti_path) }}" target="_blank"
                            class="text-blue-500 hover:underline text-xs font-semibold">
                            🖼️ Lihat
                        </a>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                        <a href="{{ route($prefix . '.pengeluaran.edit', $item) }}"
                                class="text-blue-500 hover:underline text-sm font-semibold">Edit</a>
                                <form action="{{ route($prefix . '.pengeluaran.destroy', $item) }}" method="POST"
                                data-konfirmasi="Yakin hapus pengeluaran '{{ $item->keterangan }}' ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-sm font-semibold">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                        Belum ada pengeluaran tercatat
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($pengeluaran->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $pengeluaran->links() }}</div>
        @endif
    </div>
</x-app-layout>