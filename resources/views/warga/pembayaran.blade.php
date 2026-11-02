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

            {{-- Tombol Bayar --}}
            @if($t->status === 'belum_bayar')
            <div class="mt-3">
                <button
                    onclick="bayar({{ $t->id }})"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    💳 Bayar via QRIS / Virtual Account
                </button>
            </div>
            @elseif($t->status === 'menunggu')
            <div class="mt-3">
                <button
                    onclick="bayar({{ $t->id }})"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg text-sm transition flex items-center gap-2">
                    🔄 Lanjutkan Pembayaran
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="px-5 py-10 text-center">
            <div class="text-4xl mb-3">🎉</div>
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
        function bayar(tagihanId) {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '⏳ Memproses...';

    fetch(`/warga/pembayaran/${tagihanId}/checkout`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            btn.disabled = false;
            btn.innerHTML = '💳 Bayar via QRIS / Virtual Account';
            return;
        }

        snap.pay(data.snap_token, {
            onSuccess: function(result) {
                alert('Pembayaran berhasil!');
                location.reload();
            },
            onPending: function(result) {
                alert('Menunggu pembayaran kamu.');
                location.reload();
            },
            onError: function(result) {
                alert('Pembayaran gagal.');
                btn.disabled = false;
                btn.innerHTML = '💳 Bayar via QRIS / Virtual Account';
            },
            onClose: function() {
                btn.disabled = false;
                btn.innerHTML = '💳 Bayar via QRIS / Virtual Account';
            }
        });
    });
}

        function showAlert(type, message) {
            const colors = {
                success: 'bg-green-100 border-green-400 text-green-700',
                error:   'bg-red-100 border-red-400 text-red-700',
                warning: 'bg-orange-100 border-orange-400 text-orange-700',
            };
            const box = document.getElementById('alert-box');
            box.className = `border px-4 py-3 rounded-xl mb-5 ${colors[type]}`;
            box.innerHTML = message;
            box.classList.remove('hidden');
            box.scrollIntoView({ behavior: 'smooth' });
        }
    </script>

</x-app-layout>