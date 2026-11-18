<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="subtitle">Selamat datang, {{ auth()->user()->nama_lengkap }}</x-slot>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-6">

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <x-icon name="wallet" class="w-5 h-5 text-blue-500" />
                </div>
            </div>
            <div class="text-2xl font-bold font-mono {{ $saldoKas >= 0 ? 'text-gray-800' : 'text-red-500' }}">
                Rp {{ number_format($saldoKas, 0, ',', '.') }}
            </div>
            <div class="text-gray-500 text-sm mt-1">Total Saldo Kas</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                    <x-icon name="trending-up" class="w-5 h-5 text-green-600" />
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-green-600">
                Rp {{ number_format($totalPemasukanBulanIni, 0, ',', '.') }}
            </div>
            <div class="text-gray-500 text-sm mt-1">Pemasukan Bulan Ini</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                    <x-icon name="trending-down" class="w-5 h-5 text-orange-500" />
                </div>
            </div>
            <div class="text-2xl font-bold font-mono text-red-500">
                Rp {{ number_format($totalPengeluaranBulanIni, 0, ',', '.') }}
            </div>
            <div class="text-gray-500 text-sm mt-1">Pengeluaran Bulan Ini</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                    <x-icon name="exclamation-triangle" class="w-5 h-5 text-red-500" />
                </div>
                <span class="text-xs font-semibold bg-red-100 text-red-700 px-2 py-1 rounded-full">
                    {{ $jumlahWargaTunggak }} warga
                </span>
            </div>
            <div class="text-2xl font-bold font-mono text-red-500">
                Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
            </div>
            <div class="text-gray-500 text-sm mt-1">Total Tunggakan</div>
        </div>

    </div>

    {{-- ROW 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5 mb-6">

        {{-- Transaksi Terbaru --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Transaksi Terbaru</div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($transaksiTerbaru as $t)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50">
                    <div>
                        <div class="font-semibold text-sm text-gray-800">{{ $t['nama'] }}</div>
                        <div class="text-gray-400 text-xs">{{ $t['ket'] }}</div>
                    </div>
                    <div class="font-bold font-mono text-sm {{ $t['jenis'] === 'masuk' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $t['jenis'] === 'masuk' ? '+' : '−' }}Rp {{ number_format($t['nominal'], 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400 text-sm">
                    Belum ada transaksi
                </div>
                @endforelse
            </div>
        </div>

        {{-- Warga Menunggak --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <div class="font-bold text-gray-800">Warga Menunggak</div>
                    <div class="text-gray-400 text-xs mt-0.5">
                        {{ $jumlahWargaTunggak }} warga · Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($wargaTunggak as $t)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                        {{ strtoupper(substr($t->warga->nama_lengkap, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-gray-800">{{ $t->warga->nama_lengkap }}</div>
                        <div class="text-gray-400 text-xs">{{ $t->iuran->nama_iuran }}</div>
                    </div>
                    <div class="font-bold font-mono text-sm text-red-500">
                        Rp {{ number_format($t->nominal, 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-gray-400 text-sm flex flex-col items-center gap-2">
                    <x-icon name="check-circle" class="w-8 h-8 text-green-400" />
                    Semua warga sudah lunas!
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Status Pembayaran --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="font-bold text-gray-800">Status Pembayaran Iuran</div>
            <div class="text-gray-400 text-xs mt-0.5">Rekap semua iuran aktif</div>
        </div>
        <div class="p-4 sm:p-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-xl">
                    <div class="text-3xl font-bold text-blue-600 font-mono">{{ $lunasDigital }}</div>
                    <div class="text-sm text-gray-500 mt-1">Lunas Digital</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="text-3xl font-bold text-green-600 font-mono">{{ $lunasTunai }}</div>
                    <div class="text-sm text-gray-500 mt-1">Lunas Tunai</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-xl">
                    <div class="text-3xl font-bold text-orange-500 font-mono">{{ $menunggu }}</div>
                    <div class="text-sm text-gray-500 mt-1">Menunggu</div>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-xl">
                    <div class="text-3xl font-bold text-red-500 font-mono">{{ $belumBayar }}</div>
                    <div class="text-sm text-gray-500 mt-1">Belum Bayar</div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>