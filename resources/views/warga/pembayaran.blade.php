<x-app-layout>
    <x-slot name="title">Pembayaran</x-slot>
    <x-slot name="subtitle">Kelola tagihan dan riwayat pembayaran iuran kamu</x-slot>

    {{-- Alert --}}
    <div id="alert-box" class="hidden mb-5"></div>

    {{-- Tagihan Aktif --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-5">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="font-bold text-gray-800">Tagihan Belum Lunas</div>
            <div class="text-gray-400 text-xs mt-0.5">
                {{ $tagihanBelumLunas->count() }} tagihan aktif
            </div>
        </div>

        @forelse($tagihanBelumLunas as $t)
        <div class="px-5 py-4 border-b border-gray-50 hover:bg-gray-50">
            <div class="flex items-start justify-between">
                <div>
                    <div class="font-bold text-gray-800">{{ $t->iuran->nama_iuran }}</div>
                    <div class="text-gray-400 text-xs mt-1">
                        Deadline: {{ $t->iuran->deadline->format('d M Y') }}
                    </div>
                    @if($t->iuran->keterangan)
                    <div class="text-gray-400 text-xs mt-0.5">{{ $t->iuran->keterangan }}</div>
                    @endif
                </div>
                <div class="text-right">
                    <div class="font-bold font-mono text-blue-500 text-lg">
                        Rp {{ number_format($t->nominal, 0, ',', '.') }}
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full
                        {{ $t->status === 'menunggu' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700' }}">
                        {{ $t->status === 'menunggu' ? 'Menunggu Konfirmasi' : 'Belum Bayar' }}
                    </span>
                </div>
            </div>

            {{-- Pilihan Metode Bayar + Rincian Biaya Admin --}}
            @php
                $nominalIuran = (int) $t->nominal;
                $adminQris    = \App\Services\MidtransService::hitungBiayaAdmin($nominalIuran, 'qris');
                $adminVa      = \App\Services\MidtransService::hitungBiayaAdmin($nominalIuran, 'va');
            @endphp
            @if(in_array($t->status, ['belum_bayar', 'menunggu']))
            <div class="mt-3 max-w-xs space-y-2">
                <button type="button"
                    onclick="bayar({{ $t->id }}, 'qris', this)"
                    class="w-full flex items-center justify-between border border-purple-200 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg px-3.5 py-2 text-xs transition">
                    <span class="font-semibold inline-flex items-center gap-1.5">
                        <x-icon name="qr-code" class="w-4 h-4 flex-shrink-0" />
                        <span>Bayar via QRIS <span class="text-purple-500 font-normal">(+Rp {{ number_format($adminQris, 0, ',', '.') }})</span></span>
                    </span>
                    <span class="font-mono font-bold">Rp {{ number_format($nominalIuran + $adminQris, 0, ',', '.') }}</span>
                </button>

                <button type="button"
                    onclick="bayar({{ $t->id }}, 'va', this)"
                    class="w-full flex items-center justify-between border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg px-3.5 py-2 text-xs transition">
                    <span class="font-semibold inline-flex items-center gap-1.5">
                        <x-icon name="credit-card" class="w-4 h-4 flex-shrink-0" />
                        <span>Virtual Account <span class="text-blue-500 font-normal">(+Rp {{ number_format($adminVa, 0, ',', '.') }})</span></span>
                    </span>
                    <span class="font-mono font-bold">Rp {{ number_format($nominalIuran + $adminVa, 0, ',', '.') }}</span>
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="px-5 py-10 text-center">
            <div class="flex justify-center mb-3">
                <x-icon name="check-circle" class="w-10 h-10 text-green-400" />
            </div>
            <div class="font-bold text-gray-700">Semua tagihan sudah lunas!</div>
            <div class="text-gray-400 text-sm mt-1">Tidak ada tagihan yang perlu dibayar saat ini</div>
        </div>
        @endforelse
    </div>

    {{-- Riwayat Pembayaran --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="font-bold text-gray-800">Riwayat Pembayaran</div>
            <div class="text-gray-400 text-xs mt-0.5">Semua transaksi pembayaran iuran kamu</div>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full min-w-[600px]">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Iuran</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Metode</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tgl Bayar</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($riwayatBayar as $p)
                @php
                    $metodeLabel = match($p->metode) {
                        'qris'  => 'QRIS',
                        'va'    => 'Virtual Account',
                        default => 'Tunai',
                    };
                    $metodeColor = match($p->metode) {
                        'qris'  => 'bg-purple-100 text-purple-700',
                        'va'    => 'bg-blue-100 text-blue-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                    $statusColor = match($p->status) {
                        'lunas'   => 'bg-green-100 text-green-700',
                        'pending' => 'bg-orange-100 text-orange-700',
                        default   => 'bg-red-100 text-red-700',
                    };
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-4 font-semibold text-sm">
                        {{ $p->tagihan->iuran->nama_iuran }}
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $metodeColor }}">
                            {{ $metodeLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-4 font-mono text-sm font-bold text-green-600">
                        Rp {{ number_format($p->nominal, 0, ',', '.') }}
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
                    <td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm">
                        Belum ada riwayat pembayaran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($riwayatBayar->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $riwayatBayar->links() }}
        </div>
        @endif
    </div>

    {{-- JavaScript untuk Midtrans Snap --}}
    <script>
        function bayar(tagihanId, metode, btn) {
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<div class="text-xs font-semibold text-gray-600 py-0.5">Memproses transaksi...</div>';

            fetch(`/warga/pembayaran/${tagihanId}/checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ metode: metode }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    showModalAlert('error', data.error);
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    return;
                }

                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        showModalAlert('success', 'Pembayaran berhasil!', () => location.reload());
                    },
                    onPending: function(result) {
                        showModalAlert('warning', 'Menunggu pembayaran kamu.', () => location.reload());
                    },
                    onError: function(result) {
                        showModalAlert('error', 'Pembayaran gagal.');
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    },
                    onClose: function() {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                });
            })
            .catch(() => {
                showModalAlert('error', 'Gagal terhubung ke server. Silakan coba lagi.');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        }
    </script>

</x-app-layout>