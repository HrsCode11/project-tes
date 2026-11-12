<x-app-layout>
@php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp

    <x-slot name="title">Pembayaran</x-slot>
    <x-slot name="subtitle">Riwayat semua transaksi pembayaran iuran</x-slot>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div></div>
        <a href="{{ route($prefix . '.pembayaran.create') }}"
            class="bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
            ＋ Input Bayar Tunai
        </a>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="font-bold text-gray-800">Riwayat Pembayaran</div>
            <div class="text-gray-400 text-xs mt-0.5">Total {{ $pembayaran->total() }} transaksi</div>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full min-w-[600px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Warga</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Iuran</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Metode</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tgl Bayar</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pembayaran as $p)
                @php
                    $metodeColor = match($p->metode) {
                        'qris'  => 'bg-purple-100 text-purple-700',
                        'va'    => 'bg-blue-100 text-blue-700',
                        'tunai' => 'bg-gray-100 text-gray-700',
                    };
                    $metodeLabel = match($p->metode) {
                        'qris'  => 'QRIS',
                        'va'    => 'Virtual Account',
                        'tunai' => 'Tunai',
                    };
                    $statusColor = match($p->status) {
                        'lunas'   => 'bg-green-100 text-green-700',
                        'pending' => 'bg-orange-100 text-orange-700',
                        'gagal'   => 'bg-red-100 text-red-700',
                    };
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr($p->warga->nama_lengkap, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-sm">{{ $p->warga->nama_lengkap }}</div>
                                <div class="text-gray-400 text-xs">RT {{ $p->warga->no_rt }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm">{{ $p->tagihan->iuran->nama_iuran }}</td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $metodeColor }}">
                            {{ $metodeLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-4 font-mono text-sm font-semibold text-green-600">
                        +Rp {{ number_format($p->nominal, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs">
                        {{ $p->tgl_bayar ? $p->tgl_bayar->format('d M Y · H:i') : '—' }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                        Belum ada transaksi pembayaran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($pembayaran->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $pembayaran->links() }}
        </div>
        @endif
    </div>

</x-app-layout>