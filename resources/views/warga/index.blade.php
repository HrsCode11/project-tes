<x-app-layout>
    <x-slot name="title">Kelola Warga</x-slot>
    <x-slot name="subtitle">Data penduduk RT yang menjadi dasar penagihan iuran</x-slot>

    @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp

    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-3.5 sm:p-5">
            <div class="text-lg sm:text-2xl font-bold font-mono text-gray-800">{{ $ringkasan['total'] }}</div>
            <div class="text-gray-500 text-xs sm:text-sm mt-1">Total Warga Terdata</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3.5 sm:p-5">
            <div class="text-lg sm:text-2xl font-bold font-mono text-green-600">{{ $ringkasan['aktif'] }}</div>
            <div class="text-gray-500 text-xs sm:text-sm mt-1">Warga Aktif (ditagih iuran)</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-3.5 sm:p-5 col-span-2 sm:col-span-1">
            <div class="text-lg sm:text-2xl font-bold font-mono text-orange-500">{{ $ringkasan['tanpa_akun'] }}</div>
            <div class="text-gray-500 text-xs sm:text-sm mt-1">Belum Punya Akun Login</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-wrap gap-3">
            <div>
                <div class="font-bold text-gray-800">Daftar Warga</div>
                <div class="text-gray-400 text-xs mt-0.5">Menampilkan {{ $warga->total() }} data</div>
            </div>
            <a href="{{ route($prefix . '.warga.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                <x-icon name="plus" class="w-4 h-4" /> Tambah Warga
            </a>
        </div>

        {{-- Pencarian & filter --}}
        <form method="GET" class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="magnifying-glass" class="w-4 h-4 text-gray-400" />
                </div>
                <input type="text" name="q" value="{{ request('q') }}"
                    class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 focus:bg-white"
                    placeholder="Cari nama atau NIK...">
            </div>
            <select name="status"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50">
                <option value="">Semua status</option>
                @foreach(['aktif' => 'Aktif', 'pindah' => 'Pindah', 'meninggal' => 'Meninggal'] as $nilai => $label)
                <option value="{{ $nilai }}" {{ request('status') === $nilai ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white font-semibold px-5 py-2 rounded-lg text-sm transition">
                Cari
            </button>
            @if(request()->hasAny(['q', 'status']))
            <a href="{{ route($prefix . '.warga.index') }}"
                class="text-sm text-gray-500 hover:text-gray-700 px-2 py-2 text-center">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Warga</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">NIK</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. HP</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">RT</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($warga as $w)
                    @php
                        $statusColor = match($w->status) {
                            'aktif'     => 'bg-green-100 text-green-700',
                            'pindah'    => 'bg-orange-100 text-orange-700',
                            'meninggal' => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($w->nama_lengkap, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-sm text-gray-800">{{ $w->nama_lengkap }}</div>
                                    <div class="text-gray-400 text-xs">{{ $w->alamat ?? 'Alamat belum diisi' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-mono text-xs text-gray-600">{{ $w->nik }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $w->no_hp ? '+' . $w->no_hp : '—' }}</td>
                        <td class="px-5 py-4 text-sm font-semibold">{{ $w->no_rt }}</td>
                        <td class="px-5 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                                {{ ucfirst($w->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($w->user)
                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Punya akun</span>
                            @else
                            <span class="text-gray-300 text-xs">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @php
                                // Tombol hapus hanya muncul jika tidak punya akun dan belum punya riwayat tagihan/pembayaran
                                $bisaHapus = !$w->user && $w->tagihan_count === 0 && $w->pembayaran_count === 0;
                            @endphp
                            <div class="flex items-center gap-4">
                                <a href="{{ route($prefix . '.warga.edit', $w) }}"
                                    class="text-blue-500 hover:underline text-sm font-semibold inline-flex items-center gap-1">
                                    <x-icon name="pencil" class="w-3.5 h-3.5" /> Edit
                                </a>

                                @if($bisaHapus)
                                <form action="{{ route($prefix . '.warga.destroy', $w) }}" method="POST"
                                    data-konfirmasi="Hapus data {{ $w->nama_lengkap }}? Tindakan ini tidak bisa dibatalkan.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:underline text-sm font-semibold inline-flex items-center gap-1">
                                        <x-icon name="trash" class="w-3.5 h-3.5" /> Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">
                            Tidak ada data warga yang cocok
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($warga->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $warga->links() }}</div>
        @endif
    </div>
</x-app-layout>