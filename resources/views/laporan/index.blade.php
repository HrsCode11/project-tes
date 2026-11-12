<x-app-layout>
    <x-slot name="title">Ekspor Laporan</x-slot>
    <x-slot name="subtitle">Generate dan unduh laporan keuangan RT/RW</x-slot>

    <div class="grid grid-cols-2 gap-6">

        {{-- Form Ekspor --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Generate Laporan</div>
                <div class="text-gray-400 text-xs mt-0.5">Pilih jenis laporan dan format file</div>
            </div>
            <form action="{{ route(auth()->user()->isKetuaRT() ? 'ketua.laporan.export' : 'bendahara.laporan.export') }}"
                method="POST" class="p-6">
                @csrf

                {{-- Jenis Laporan --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Jenis Laporan</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="bulanan" class="sr-only peer" checked>
                            <div class="border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-xl p-3 text-center transition">
                                <x-icon name="document-chart" class="w-6 h-6 mx-auto mb-1 text-gray-400" />
                                <div class="text-xs font-semibold">Bulanan</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="tahunan" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-xl p-3 text-center transition">
                                <x-icon name="document-chart" class="w-6 h-6 mx-auto mb-1 text-gray-400" />
                                <div class="text-xs font-semibold">Tahunan</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="iuran" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-xl p-3 text-center transition">
                                <x-icon name="receipt" class="w-6 h-6 mx-auto mb-1 text-gray-400" />
                                <div class="text-xs font-semibold">Per Iuran</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Filter Bulanan --}}
                <div id="filter-bulanan" class="mb-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Bulan</label>
                            <select name="bulan" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ now()->month == $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                            <select name="tahun" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                                @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Filter Tahunan --}}
                <div id="filter-tahunan" class="mb-4 hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun</label>
                    <select name="tahun" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Filter Per Iuran --}}
                <div id="filter-iuran" class="mb-4 hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Iuran</label>
                    <select name="iuran_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih iuran --</option>
                        @foreach($daftarIuran as $iuran)
                        <option value="{{ $iuran->id }}">
                            {{ $iuran->nama_iuran }} ({{ ucfirst($iuran->status) }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Format --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Format File</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="format" value="pdf" class="sr-only peer" checked>
                            <div class="border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 rounded-xl p-3 text-center transition flex items-center justify-center gap-2">
                                <x-icon name="document-text" class="w-5 h-5 text-red-500" />
                                <span class="text-sm font-semibold text-red-600">PDF</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="format" value="excel" class="sr-only peer">
                            <div class="border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 rounded-xl p-3 text-center transition flex items-center justify-center gap-2">
                                <x-icon name="document-chart" class="w-5 h-5 text-green-600" />
                                <span class="text-sm font-semibold text-green-600">Excel</span>
                            </div>
                        </label>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2">
                    <x-icon name="arrow-down-tray" class="w-5 h-5" />
                    Download Laporan
                </button>

            </form>
        </div>

        {{-- Riwayat Ekspor --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Riwayat Ekspor</div>
                <div class="text-gray-400 text-xs mt-0.5">5 laporan terakhir yang pernah digenerate</div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($riwayatEkspor as $r)
                <div class="flex items-center gap-3 px-5 py-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                        {{ $r['format'] === 'pdf' ? 'bg-red-50' : 'bg-green-50' }}">
                        @if($r['format'] === 'pdf')
                            <x-icon name="document-text" class="w-5 h-5 text-red-500" />
                        @else
                            <x-icon name="document-chart" class="w-5 h-5 text-green-500" />
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm truncate">{{ $r['nama'] }}</div>
                        <div class="text-gray-400 text-xs mt-0.5">
                            {{ $r['tgl'] }} · oleh {{ $r['oleh'] }}
                        </div>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full uppercase
                        {{ $r['format'] === 'pdf' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                        {{ $r['format'] }}
                    </span>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-gray-400 text-sm flex flex-col items-center gap-2">
                    <x-icon name="document-chart" class="w-10 h-10 text-gray-200" />
                    Belum ada riwayat ekspor
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <script>
        const radios        = document.querySelectorAll('input[name="jenis"]');
        const filterBulanan = document.getElementById('filter-bulanan');
        const filterTahunan = document.getElementById('filter-tahunan');
        const filterIuran   = document.getElementById('filter-iuran');

        function updateFilters() {
            // Sembunyikan semua & disable semua input
            [filterBulanan, filterTahunan, filterIuran].forEach(f => {
                f.classList.add('hidden');
                f.querySelectorAll('select, input').forEach(el => el.disabled = true);
            });

            // Tampilkan & enable filter yang aktif
            const aktif = document.querySelector('input[name="jenis"]:checked').value;
            let activeFilter;
            if (aktif === 'bulanan') activeFilter = filterBulanan;
            if (aktif === 'tahunan') activeFilter = filterTahunan;
            if (aktif === 'iuran')   activeFilter = filterIuran;

            if (activeFilter) {
                activeFilter.classList.remove('hidden');
                activeFilter.querySelectorAll('select, input').forEach(el => el.disabled = false);
            }
        }

        radios.forEach(r => r.addEventListener('change', updateFilters));

        // Jalankan saat halaman load
        updateFilters();
    </script>

</x-app-layout>