<x-app-layout>
    @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
    <x-slot name="title">Detail Iuran</x-slot>
    <x-slot name="subtitle">{{ $iuran->nama_iuran }} · Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</x-slot>

    {{-- Info Iuran --}}
    <div class="grid grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold font-mono text-blue-500">{{ $iuran->totalWarga() }}</div>
            <div class="text-gray-500 text-xs mt-1">Total Warga</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold font-mono text-green-500">{{ $iuran->jumlahLunas() }}</div>
            <div class="text-gray-500 text-xs mt-1">Sudah Lunas</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold font-mono text-red-500">
                {{ $iuran->tagihan()->where('status', 'belum_bayar')->count() }}
            </div>
            <div class="text-gray-500 text-xs mt-1">Belum Bayar</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold font-mono text-gray-700">{{ $iuran->persentaseLunas() }}%</div>
            <div class="text-gray-500 text-xs mt-1">Tingkat Lunas</div>
        </div>
    </div>

    {{-- Tabel Tagihan --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <div class="font-bold text-gray-800">Daftar Tagihan per Warga</div>
                <div class="text-gray-400 text-xs mt-0.5">
                    Deadline: {{ $iuran->deadline->format('d M Y') }}
                </div>
            </div>
            @if($iuran->status === 'aktif')
            <a href="{{ route($prefix . '.iuran.pengingat', $iuran) }}"
            class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition inline-flex items-center gap-2">

                <img src="{{ asset('icons/whatsapp.svg') }}"
                    alt="WhatsApp"
                    class="w-4 h-4">

                <span>Kirim Pengingat WA</span>
            </a>
            @endif
            <a href="{{ route($prefix . '.iuran.index') }}"
                class="text-blue-500 text-sm font-semibold hover:underline">
                ← Kembali
            </a>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full min-w-[600px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Warga</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. RT</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($tagihan as $t)
                @php
                    $statusColor = match($t->status) {
                        'lunas'       => 'bg-green-100 text-green-700',
                        'menunggu'    => 'bg-orange-100 text-orange-700',
                        'belum_bayar' => 'bg-red-100 text-red-700',
                    };
                    $statusLabel = match($t->status) {
                        'lunas'       => 'Lunas',
                        'menunggu'    => 'Menunggu',
                        'belum_bayar' => 'Belum Bayar',
                    };
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs">
                                {{ strtoupper(substr($t->warga->nama_lengkap, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-sm text-gray-800">{{ $t->warga->nama_lengkap }}</div>
                                <div class="text-gray-400 text-xs">
                                {{ $t->warga->no_hp ? '+' . $t->warga->no_hp : 'No. HP belum diisi' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm font-semibold">{{ $t->warga->no_rt }}</td>
                    <td class="px-5 py-4 font-mono text-sm font-semibold">
                        Rp {{ number_format($t->nominal, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center text-gray-400">
                        Belum ada tagihan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($tagihan->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $tagihan->links() }}
        </div>
        @endif
    </div>

</x-app-layout>