<x-app-layout>
    <x-slot name="title">Input Pembayaran Tunai</x-slot>
    <x-slot name="subtitle">Konfirmasi pembayaran tunai dari warga</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Form Pembayaran Tunai</div>
                <div class="text-gray-400 text-xs mt-0.5">
                    Cari nama warga lalu pilih tagihan yang ingin dikonfirmasi
                </div>
            </div>

            <div class="p-6">

                @if ($errors->any())
                    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- STEP 1: Cari Warga --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-500 text-white rounded-full text-xs font-bold mr-1">1</span>
                        Cari Warga
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="magnifying-glass" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input type="text" id="cari-warga"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm bg-gray-50 focus:bg-white transition"
                            placeholder="Ketik nama warga..."
                            autocomplete="off">
                    </div>

                    {{-- Hasil pencarian --}}
                    <div id="hasil-cari" class="hidden mt-2 border border-gray-200 rounded-xl overflow-hidden shadow-lg bg-white z-10 max-h-60 overflow-y-auto">
                        {{-- Diisi via JavaScript --}}
                    </div>
                </div>

                {{-- STEP 2: Pilih Tagihan --}}
                <div id="step-tagihan" class="hidden mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-500 text-white rounded-full text-xs font-bold mr-1">2</span>
                        Pilih Tagihan
                    </label>
                    <div id="daftar-tagihan" class="space-y-2">
                        {{-- Diisi via JavaScript --}}
                    </div>
                </div>

                {{-- STEP 3: Konfirmasi --}}
                <div id="step-konfirmasi" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-blue-500 text-white rounded-full text-xs font-bold mr-1">3</span>
                        Konfirmasi Pembayaran
                    </label>

                    {{-- Preview --}}
                    <div id="preview-bayar" class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                        {{-- Diisi via JavaScript --}}
                    </div>

                    <form id="form-bayar" action="{{ route((auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara') . '.pembayaran.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tagihan_id" id="tagihan_id_input">

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Catatan <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <input type="text" name="catatan"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-gray-50 focus:bg-white transition"
                                placeholder="cth: Titip ke tetangga, bayar langsung, dll.">
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700 mb-4 flex items-center gap-2">
                            <x-icon name="check-circle" class="w-4 h-4 flex-shrink-0" />
                            Status tagihan akan otomatis berubah menjadi <strong>Lunas</strong> setelah disimpan.
                        </div>

                        <div class="flex gap-3">
                            <button type="button" onclick="resetForm()"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition text-sm">
                                Ganti Warga
                            </button>
                            <button type="submit"
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                                <x-icon name="check-circle" class="w-4 h-4" />
                                Simpan Pembayaran
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Empty state --}}
                <div id="step-empty" class="py-8 text-center text-gray-400 text-sm">
                    <x-icon name="magnifying-glass" class="w-10 h-10 mx-auto mb-3 text-gray-200" />
                    Cari nama warga untuk memulai
                </div>

            </div>
        </div>

        {{-- Link kembali --}}
        <div class="mt-4">
            @php $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara'; @endphp
            <a href="{{ route($prefix . '.pembayaran.index') }}"
                class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
                <x-icon name="arrow-left" class="w-4 h-4" />
                Kembali ke Riwayat Pembayaran
            </a>
        </div>
    </div>

    @php
        $dataTagihan = $tagihan->map(fn($t) => [
            'id'          => $t->id,
            'warga_id'     => $t->warga_id,
            'nama'        => $t->warga->nama_lengkap,
            'no_rt'       => $t->warga->no_rt,
            'nik'         => $t->warga->nik,
            'iuran'       => $t->iuran->nama_iuran,
            'nominal'     => $t->nominal,
            'nominal_fmt' => 'Rp ' . number_format($t->nominal, 0, ',', '.'),
            'status'      => $t->status,
            'deadline'    => $t->iuran->deadline->format('d M Y'),
        ]);
    @endphp
    
    <script>
        const semuaTagihan = {{ Illuminate\Support\Js::from($dataTagihan) }};
        let selectedWarga = null;
        let tagihanWarga  = [];

        // Cari warga
        document.getElementById('cari-warga').addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const hasil = document.getElementById('hasil-cari');

            if (query.length < 2) {
                hasil.classList.add('hidden');
                return;
            }

            // Cari unique warga dari tagihan
            const wargaMap = {};
            semuaTagihan.forEach(t => {
                if (t.nama.toLowerCase().includes(query) || t.nik.includes(query)) {
                    wargaMap[t.warga_id] = { id: t.warga_id, nama: t.nama, no_rt: t.no_rt, nik: t.nik };
                }
            });

            const wargaList = Object.values(wargaMap);

            if (wargaList.length === 0) {
                hasil.innerHTML = `
                    <div class="px-4 py-6 text-center text-gray-400 text-sm">
                        Warga tidak ditemukan
                    </div>`;
                hasil.classList.remove('hidden');
                return;
            }

            hasil.innerHTML = wargaList.map(w => `
                <div onclick="pilihWarga(${w.id})"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-0 transition">
                    <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        ${w.nama.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div class="font-semibold text-sm text-gray-800">${w.nama}</div>
                        <div class="text-gray-400 text-xs">No. RT ${w.no_rt} · NIK ${w.nik}</div>
                    </div>
                </div>
            `).join('');

            hasil.classList.remove('hidden');
        });

        // Pilih warga
        function pilihWarga(userId) {
            selectedWarga = semuaTagihan.find(t => t.warga_id === userId);
            tagihanWarga  = semuaTagihan.filter(t => t.warga_id === userId);

            // Update input
            document.getElementById('cari-warga').value = selectedWarga.nama;
            document.getElementById('hasil-cari').classList.add('hidden');

            // Tampilkan tagihan
            const daftarTagihan = document.getElementById('daftar-tagihan');

            if (tagihanWarga.length === 0) {
                daftarTagihan.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-4 text-sm text-green-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Semua tagihan warga ini sudah lunas!
                    </div>`;
            } else {
                daftarTagihan.innerHTML = tagihanWarga.map(t => `
                    <div onclick="pilihTagihan(${t.id}, '${t.iuran}', '${t.nominal_fmt}', '${t.deadline}', '${selectedWarga.nama}')"
                        class="border-2 border-gray-200 hover:border-blue-400 hover:bg-blue-50 rounded-xl p-4 cursor-pointer transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="font-semibold text-sm text-gray-800">${t.iuran}</div>
                                <div class="text-gray-400 text-xs mt-1">Deadline: ${t.deadline}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold font-mono text-blue-500">${t.nominal_fmt}</div>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full mt-1 inline-block
                                    ${t.status === 'menunggu' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700'}">
                                    ${t.status === 'menunggu' ? 'Menunggu' : 'Belum Bayar'}
                                </span>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            document.getElementById('step-empty').classList.add('hidden');
            document.getElementById('step-tagihan').classList.remove('hidden');
            document.getElementById('step-konfirmasi').classList.add('hidden');
        }

        // Pilih tagihan
        function pilihTagihan(id, namaIuran, nominal, deadline, namaWarga) {
            document.getElementById('tagihan_id_input').value = id;

            // Preview
            document.getElementById('preview-bayar').innerHTML = `
                <div class="flex items-center gap-3 mb-3 pb-3 border-b border-blue-200">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                        ${namaWarga.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div class="font-bold text-gray-800">${namaWarga}</div>
                        <div class="text-gray-500 text-xs">${namaIuran}</div>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">Nominal Pembayaran</div>
                    <div class="font-bold font-mono text-xl text-green-600">${nominal}</div>
                </div>
                <div class="flex justify-between items-center mt-1">
                    <div class="text-sm text-gray-500">Deadline</div>
                    <div class="text-sm font-semibold text-gray-700">${deadline}</div>
                </div>
            `;

            document.getElementById('step-konfirmasi').classList.remove('hidden');

            // Scroll ke konfirmasi
            document.getElementById('step-konfirmasi').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Reset form
        function resetForm() {
            document.getElementById('cari-warga').value = '';
            document.getElementById('hasil-cari').classList.add('hidden');
            document.getElementById('step-tagihan').classList.add('hidden');
            document.getElementById('step-konfirmasi').classList.add('hidden');
            document.getElementById('step-empty').classList.remove('hidden');
            selectedWarga = null;
            tagihanWarga  = [];
        }

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#cari-warga') && !e.target.closest('#hasil-cari')) {
                document.getElementById('hasil-cari').classList.add('hidden');
            }
        });
    </script>

</x-app-layout>