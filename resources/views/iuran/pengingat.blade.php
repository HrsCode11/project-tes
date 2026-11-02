<x-app-layout>
    <x-slot name="title">Kirim Pengingat</x-slot>
    <x-slot name="subtitle">{{ $iuran->nama_iuran }} · Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</x-slot>

    @php
        $prefix = auth()->user()->isKetuaRT() ? 'ketua' : 'bendahara';
        $sudah  = $daftar->filter(fn($t) => $t->pengingat_dikirim_at)->count();
    @endphp

    <div class="max-w-3xl">

        <a href="{{ route($prefix . '.iuran.show', $iuran) }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <x-icon name="arrow-left" class="w-4 h-4" /> Kembali ke detail iuran
        </a>

        <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded-xl px-4 py-3 text-sm mb-5">
            Tekan <strong>Kirim via WA</strong> pada tiap warga. WhatsApp akan terbuka dengan pesan yang sudah terisi
            beserta link bayar pribadi. Kamu tinggal menekan tombol kirim di WhatsApp.
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100">
                <div class="font-bold text-gray-800">Warga yang belum bayar</div>
                <div class="text-gray-400 text-xs mt-0.5">
                    {{ $daftar->count() }} warga · {{ $sudah }} sudah diingatkan ·
                    link berlaku sampai {{ $iuran->deadline->translatedFormat('d F Y') }}
                </div>
            </div>

            @if($daftar->isNotEmpty())

            {{-- Pencarian --}}
            <div class="px-5 py-3 border-b border-gray-100">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="magnifying-glass" class="w-4 h-4 text-gray-400" />
                    </div>
                    <input type="search" id="cari-pengingat" autocomplete="off"
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 focus:bg-white"
                        placeholder="Cari nama atau No. HP...">
                </div>
                <div id="info-cari" class="text-gray-400 text-xs mt-2 hidden"></div>
            </div>

            <div id="daftar-pengingat" class="divide-y divide-gray-50">
                @foreach($daftar as $t)
                <div data-baris
                    data-cari="{{ mb_strtolower($t->warga->nama_lengkap . ' ' . ($t->warga->no_hp ?? '')) }}"
                    class="flex items-center gap-3 px-5 py-4 hover:bg-gray-50">

                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ strtoupper(substr($t->warga->nama_lengkap, 0, 1)) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-gray-800 truncate">{{ $t->warga->nama_lengkap }}</div>
                        <div class="text-gray-400 text-xs">
                            {{ $t->warga->no_hp ? '+' . $t->warga->no_hp : 'No. HP belum diisi' }}
                        </div>
                        @if($t->pengingat_dikirim_at)
                        <div data-status class="text-green-600 text-xs">
                            Diingatkan {{ $t->pengingat_dikirim_at->translatedFormat('d M, H:i') }}
                        </div>
                        @else
                        <div data-status class="text-gray-400 text-xs">Belum diingatkan</div>
                        @endif
                    </div>

                    <a href="{{ $t->wa_url }}" target="_blank" rel="noopener"
                        data-url="{{ route($prefix . '.tagihan.pengingat', $t) }}"
                        onclick="tandai(this)"
                        class="flex-shrink-0 text-white text-sm font-semibold px-4 py-2 rounded-lg transition
                        {{ $t->warga->no_hp ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600' }}">
                        <span data-label>
                            @if($t->pengingat_dikirim_at) Kirim lagi
                            @elseif($t->warga->no_hp) Kirim via WA
                            @else Pilih kontak
                            @endif
                        </span>
                    </a>
                </div>
                @endforeach
            </div>

            {{-- Tampil saat pencarian tidak menemukan apa pun --}}
            <div id="tidak-ada-hasil" class="hidden px-5 py-12 text-center text-gray-400 text-sm flex-col items-center gap-2">
                <x-icon name="magnifying-glass" class="w-10 h-10 text-gray-200" />
                <span>Tidak ada warga yang cocok dengan pencarian.</span>
            </div>

            @else
            <div class="px-5 py-12 text-center text-gray-400 text-sm flex flex-col items-center gap-2">
                <x-icon name="check-circle" class="w-10 h-10 text-green-300" />
                Tidak ada warga yang perlu diingatkan.
            </div>
            @endif

        </div>
    </div>

    <script>
        // ── Mencatat waktu pengingat. Link WhatsApp tetap terbuka di tab baru. ──
        function tandai(el) {
            fetch(el.dataset.url, {
                method: 'POST',
                keepalive: true,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
            .then(res => res.json())
            .then(data => {
                const baris  = el.closest('[data-baris]');
                const status = baris.querySelector('[data-status]');
                status.textContent = 'Diingatkan ' + data.waktu;
                status.className = 'text-green-600 text-xs';
                el.querySelector('[data-label]').textContent = 'Kirim lagi';
            })
            .catch(() => {});
        }

        // ── Pencarian ──
        const inputCari   = document.getElementById('cari-pengingat');
        const semuaBaris  = document.querySelectorAll('[data-baris]');
        const infoCari    = document.getElementById('info-cari');
        const kosong      = document.getElementById('tidak-ada-hasil');

        if (inputCari) {
            inputCari.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();

                // Kalau yang diketik hanya angka, anggap nomor HP: 0812... dicocokkan dengan 62812...
                let qHp = null;
                if (q && /^[\d\s+\-]+$/.test(q)) {
                    qHp = q.replace(/\D/g, '');
                    if (qHp.startsWith('0')) qHp = '62' + qHp.slice(1);
                }

                let tampil = 0;
                semuaBaris.forEach(baris => {
                    const teks  = baris.dataset.cari;
                    const cocok = !q || teks.includes(q) || (qHp && teks.includes(qHp));

                    baris.classList.toggle('hidden', !cocok);
                    if (cocok) tampil++;
                });

                const adaFilter = q.length > 0;
                infoCari.textContent = 'Menampilkan ' + tampil + ' dari ' + semuaBaris.length + ' warga';
                infoCari.classList.toggle('hidden', !adaFilter);

                kosong.classList.toggle('hidden', tampil > 0);
                kosong.classList.toggle('flex', tampil === 0);
            });
        }
    </script>
</x-app-layout>