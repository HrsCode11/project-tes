<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="subtitle">Selamat datang, {{ auth()->user()->nama_lengkap }}</x-slot>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-4 gap-5 mb-6">

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-xl">💰</div>
            </div>
            <div class="text-2xl font-bold font-mono">Rp 0</div>
            <div class="text-gray-500 text-sm mt-1">Total Saldo Kas</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-xl">📈</div>
            </div>
            <div class="text-2xl font-bold font-mono">Rp 0</div>
            <div class="text-gray-500 text-sm mt-1">Pemasukan Bulan Ini</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-xl">📉</div>
            </div>
            <div class="text-2xl font-bold font-mono">Rp 0</div>
            <div class="text-gray-500 text-sm mt-1">Pengeluaran Bulan Ini</div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-xl">⚠️</div>
            </div>
            <div class="text-2xl font-bold font-mono">Rp 0</div>
            <div class="text-gray-500 text-sm mt-1">Total Tunggakan</div>
        </div>

    </div>

    {{-- ROW 2: Transaksi + Tunggakan --}}
    <div class="grid grid-cols-2 gap-5 mb-6">

        {{-- Transaksi Terbaru --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Transaksi Terbaru</div>
                <a href="#" class="text-blue-500 text-sm font-semibold hover:underline">Lihat semua →</a>
            </div>
            <div class="px-5 py-8 text-center text-gray-400 text-sm">
                Belum ada transaksi
            </div>
        </div>

        {{-- Warga Menunggak --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <div class="font-bold text-gray-800">Warga Menunggak</div>
                    <div class="text-gray-400 text-xs mt-0.5">0 warga · Rp 0</div>
                </div>
                <a href="#" class="text-blue-500 text-sm font-semibold hover:underline">Lihat semua →</a>
            </div>
            <div class="px-5 py-8 text-center text-gray-400 text-sm">
                Belum ada tunggakan
            </div>
        </div>

    </div>

    {{-- ROW 3: Status Pembayaran --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <div class="font-bold text-gray-800">Status Pembayaran Iuran</div>
                <div class="text-gray-400 text-xs mt-0.5">Rekap semua iuran aktif</div>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-xl">
                    <div class="text-3xl font-bold text-blue-600 font-mono">0</div>
                    <div class="text-sm text-gray-500 mt-1">Lunas Digital</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="text-3xl font-bold text-green-600 font-mono">0</div>
                    <div class="text-sm text-gray-500 mt-1">Lunas Tunai</div>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-xl">
                    <div class="text-3xl font-bold text-orange-500 font-mono">0</div>
                    <div class="text-sm text-gray-500 mt-1">Menunggu</div>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-xl">
                    <div class="text-3xl font-bold text-red-500 font-mono">0</div>
                    <div class="text-sm text-gray-500 mt-1">Belum Bayar</div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>