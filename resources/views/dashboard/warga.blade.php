<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="subtitle">Selamat datang, {{ auth()->user()->nama_lengkap }}</x-slot>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-5 mb-6">

        <div class="bg-white rounded-xl border border-gray-200 p-3.5 sm:p-5">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-50 rounded-xl flex items-center justify-center mb-2 sm:mb-3">
                <x-icon name="clock" class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" />
            </div>
            <div class="text-base sm:text-2xl font-bold font-mono text-red-500">
                Rp {{ number_format($totalHarusBayar, 0, ',', '.') }}
            </div>
            <div class="text-gray-500 text-xs sm:text-sm mt-1">Total yang Harus Dibayar</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-3.5 sm:p-5">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-50 rounded-xl flex items-center justify-center mb-2 sm:mb-3">
                <x-icon name="check-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" />
            </div>
            <div class="text-base sm:text-2xl font-bold font-mono text-green-600">
                Rp {{ number_format($totalSudahBayar, 0, ',', '.') }}
            </div>
            <div class="text-gray-500 text-xs sm:text-sm mt-1">Total Sudah Dibayar</div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">

        {{-- Tagihan Belum Lunas --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Tagihan Belum Lunas</div>
                <div class="text-gray-400 text-xs mt-0.5">{{ $tagihanBelumLunas->count() }} tagihan aktif</div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($tagihanBelumLunas as $t)
                <div class="px-5 py-4">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <div class="font-semibold text-sm">{{ $t->iuran->nama_iuran }}</div>
                            <div class="text-gray-400 text-xs">
                                Deadline: {{ $t->iuran->deadline->format('d M Y') }}
                            </div>
                        </div>
                        <span class="text-xs font-bold bg-red-100 text-red-700 px-2 py-1 rounded-full">
                            Belum Bayar
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="font-bold font-mono text-blue-500">
                            Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400 text-sm flex flex-col items-center gap-2">
                    <x-icon name="check-circle" class="w-8 h-8 text-green-400" />
                    Semua tagihan sudah lunas!
                </div>
                @endforelse
            </div>
        </div>

        {{-- Riwayat Pembayaran --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Riwayat Pembayaran</div>
                <div class="text-gray-400 text-xs mt-0.5">5 transaksi terakhir</div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($riwayatBayar as $p)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50">
                    <div>
                        <div class="font-semibold text-sm">{{ $p->tagihan->iuran->nama_iuran }}</div>
                        <div class="text-gray-400 text-xs">
                            {{ $p->tgl_bayar->format('d M Y') }} ·
                            {{ match($p->metode) {
                                'qris' => 'QRIS',
                                'va'   => 'Virtual Account',
                                default => 'Tunai'
                            } }}
                        </div>
                    </div>
                    <div class="font-bold font-mono text-sm text-green-600">
                        Rp {{ number_format($p->nominal, 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400 text-sm">
                    Belum ada riwayat pembayaran
                </div>
                @endforelse
            </div>
        </div>

    </div>

</x-app-layout>